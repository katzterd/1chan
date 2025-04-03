import log from '#inc/logger.js'
import socketioServer from "fastify-socket.io"
import { Server } from "socket.io"
import { kvsConnection } from '#inc/kvs.js'
import checkEnv from '#inc/check-env.js'

checkEnv(["SIO_TOKEN", "SRV_LOCAL_HOST"])

let io = null

export default async function socketIOplugin(fastify) {
	fastify.register(socketioServer)

	fastify.post("/broadcast/", async (req, reply) => {
		if (req.hostname !== `${process.env.SRV_LOCAL_HOST}:${process.env.SRV_PORT}` || req.body.token !== process.env.SIO_TOKEN) {
			reply.code(403).send()
			return
		}
		const {channel, event, data, ids, expire} = req.body
		if (ids) {
			ids.forEach(id => emit(`${channel}:${id}`, event, data, expire))
		}
		else {
			emit(channel, event, data, expire)
		}
		reply.code(200).send()
	})

	fastify.ready(async (err) => {
		if (err) throw err;
		fastify.io.setMaxListeners(100)
		io = fastify.io
		const kvs = await kvsConnection()
		fastify.io.on("connection", socket => {
			socket.on('subscribe', function(channels) {
				if (!(channels instanceof Array))
					channels = [channels]
				channels.forEach(async (channel) => {
					socket.join(channel)

					const events = await kvs.sMembers(`Events:${channel}`)
					, now = new Date().getTime()
					, live = [], dead = [];
					events.forEach(e => {
						const eu = unserializeSafe(e)
						if (eu) {
							const [event, data, expiryDate] = eu
							if (expiryDate < now) {
								dead.push(e)
							}
							else {
								live.push({event, data})
							}
						}
					})
					if (live.length)
						socket.emit('_multi_', live)
					if (dead.length)
						await kvs.sRem(`Events:${channel}`, dead)
				})
			})
			socket.on('unsubscribe', function(channels) {
				if (!(channels instanceof Array))
					channels = [channels]
				channels.forEach(channel => socket.leave(channel))
			})
		})
	})
}

export async function emit(channel, event, data, expire=false) {
	if (!io) {
		log.err('Fastify+Socket.IO is not initialized')
		return
	}
	const kvs = await kvsConnection()
	io.to(channel).emit(event, data)
	if (expire) {
		const expiryDate = new Date().getTime() + expire *1000 *60 *60
		kvs.sAdd(`Events:${channel}`, JSON.stringify([ event, data, expiryDate ]) )
	}
}

function unserializeSafe(str) {
	try {
		return JSON.parse(str)
	} catch(e) {
		return null
	}
}
