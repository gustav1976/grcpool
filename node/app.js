var express = require('express');
//var https = require('https');
var http = require('http');
var app = express();
var fs = require('fs');
var bodyParser = require('body-parser');

var server = http.createServer(app).listen(3001);

var io = require('socket.io').listen(server);

app.use(bodyParser.json({
    verify: function(req, res, buf, encoding) {
        req.rawBody = buf.toString();
    }
}));

app.use("/", express.static(__dirname));

app.post("/updateTicker",function(req,res) {
	console.log("ticker received ");
	res.contentType('text/html');
	res.send('');
	io.sockets.emit('updateTicker',req.rawBody);	
});

io.sockets.on('connection', function (socket) {
	socket.on('disconnect', function(){

	});	
});


