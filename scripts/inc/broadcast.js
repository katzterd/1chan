import log from '#inc/logger.js'
import fastify from "fastify"
import socketioServer from "fastify-socket.io"
import { Server } from "socket.io"

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
	const {channel, event, data, ids} = req.body
	if (ids) {
		ids.forEach(id => app.io.to(channel+':'+id).emit(event, data))
	}
	else {
		app.io.to(channel).emit(event, data)
	}
	reply.code(200).send()
})

app.ready((err) => {
	if (err) throw err;
	app.io.on("connection", socket => {
		socket.on('subscribe', function(rooms) {
			if (!(rooms instanceof Array))
				rooms = [rooms]
			rooms.forEach(room => socket.join(room))
		})
		socket.on('unsubscribe', function(rooms) {
			if (!(rooms instanceof Array))
				rooms = [rooms]
			rooms.forEach(room => socket.leave(room))
		})
	})
})

export function listen() {
	app.listen({ host: process.env.SIO_HOST, port: process.env.SIO_PORT })
	log.succ(`Сервис Socket.IO по адресу ${process.env.SIO_HOST}:${process.env.SIO_PORT} принимает сообщения с локального адреса ${process.env.SIO_SRV_IP}`)
}