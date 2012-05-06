var app = require('express').createServer();

app.get('/', function(req, res){
  res.send('hello my sweetest banana');
});

app.listen(80);