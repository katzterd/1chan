import log from '#inc/logger.js'
import fastify from "fastify"
import socketioServer from "fastify-socket.io"
import { Server } from "socket.io"
import { kvsConnection } from '#inc/kvs.js'

;["SIO_SRV_IP",
	"SIO_TOKEN",
	"SIO_HOST",
	"SIO_PORT"
].forEach(param => {
	if (typeof process.env[param] === 'undefined') {
		log.fatal(`Параметр «${param}» не определен в .env-файле!`)
	}
})

const app = fastify()
app.register(socketioServer)

app.post("/broadcast/", async (req, reply) => {
	if (req.ip !== process.env.SIO_SRV_IP || req.body.token !== process.env.SIO_TOKEN) {
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

async function emit(channel, event, data, expire=false) {
	const kvs = await kvsConnection()

	app.io.to(channel).emit(event, data)
	if (expire) {
		const expiryDate = new Date().getTime() + expire *1000 *60 *60
		kvs.sAdd(`Events:${channel}`, JSON.stringify([ event, data, expiryDate ]) )
	}
}

app.ready(async (err) => {
	const kvs = await kvsConnection()

	if (err) throw err;
	app.io.on("connection", socket => {
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

function unserializeSafe(str) {
	try {
		return JSON.parse(str)
	} catch(e) {
		return null
	}
}

export function listen() {
	app.listen({ host: process.env.SIO_HOST, port: process.env.SIO_PORT })
	log.succ(`Сервис Socket.IO по адресу ${process.env.SIO_HOST}:${process.env.SIO_PORT} принимает сообщения с локального адреса ${process.env.SIO_SRV_IP}`)
}