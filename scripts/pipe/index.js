require('dotenv').config()

const bodyParser = require('body-parser')
, express = require('express'), app = express()
, server = require('http').createServer(app)
, io = require('socket.io')(server)
app.use(bodyParser.json())

app.post('/broadcast', (req, res) => {
	if (req.socket.remoteAddress != process.env.SRV_IP || req.body.token != process.env.SRV_TOKEN) {
		res.status(403).end()
		return;
	}
	let {channel, event, data, ids} = req.body

	if (ids) {
		ids.forEach(id => io.to(channel+':'+id).emit(event, data))
	}
	else {
		io.to(channel).emit(event, data)
	}

	res.status(200).end()
})

io.on('connection', function (socket) {
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

server.listen(process.env.PORT, process.env.HOST)