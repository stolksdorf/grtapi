var http = require('http');
http.createServer(function (req, res) {
  res.writeHead(200, {'Content-Type': 'text/plain'});
  res.end('Hello World\n');
}).listen(80, '107.21.214.193');
console.log('Server running at http://107.21.214.193:80/');