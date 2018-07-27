var express = require('express');
//var https = require('https');
var http = require('http');
var app = express();
var fs = require('fs');
var bodyParser = require('body-parser');

var server = http.createServer(app).listen(3001);
var io = require('socket.io').listen(server);
var poolBlockWaiting = false;
var poolBlockRequest = 1;

app.use(bodyParser.json({
    verify: function(req, res, buf, encoding) {
        req.rawBody = buf.toString();
    }
}));

app.use("/", express.static(__dirname));

app.post("/updateTicker",function(req,res) {
	res.contentType('text/html');
	res.send('');
	io.sockets.emit('updateTicker',req.rawBody);	
});

app.post("/updateBlock",function(req,res) {
	res.contentType('text/html');
	res.send('');
	io.sockets.emit('updateBlock',req.rawBody);	
});

app.post("/updatePoolBlocks",function(req,res) {
	res.contentType('text/html');
	res.send('');
	//poolBlockRequest = req.rawBody;
	if (!poolBlockWaiting) {
		poolBlockWaiting = true;
		setTimeout(function() {
			io.sockets.emit('updatePoolBlocks',poolBlockRequest);
			poolBlockWaiting = false;
		},5000);
	}
});

io.sockets.on('connection', function (socket) {
	socket.on('disconnect', function(){

	});	
});