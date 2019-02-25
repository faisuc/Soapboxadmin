var socket          = require('socket.io');
var express         = require('express');
var https           = require('https');
var http            = require('http');
var fs              = require('fs');

var app = express();
var http_server = http.createServer(app).listen(3001);
var port = process.env.PORT || 3001;
var io = socket.listen(http_server);

http_server.listen(port, () => {
    console.log('Server listening at port %d', port);
});

var users = {};

io.on('connection', function(socket) {

    console.log('User Connected');

    socket.on('add user', function (data) {
        if (typeof users[data.user_id] == 'undefined')
        {
            users[data.user_id] = {
                "sockets": [socket.id]
            };
        }
        else
        {
            users[data.user_id].sockets.push(socket.id);
        }
        console.log(users);
    });

    socket.on('send chat message', function (data) {

        if (typeof users[data.send_to_user_id] != 'undefined')
        {
            var user = users[data.send_to_user_id];
            for (var key in user.sockets)
            {
                io.to(user.sockets[key]).emit('send chat message triggered', data);
            }
        }
    });

});