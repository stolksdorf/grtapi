var application_root = __dirname,
    express = require("express");
    path = require("path"),
    mongoose = require('mongoose');

var app = express.createServer();

// Database

mongoose.connect('mongodb://localhost/grtapiDB');

// Config

app.configure(function () {
  app.use(express.bodyParser());
  app.use(express.methodOverride());
  app.use(app.router);
  app.use(express.static(path.join(application_root, "public")));
  app.use(express.errorHandler({ dumpExceptions: true, showStack: true }));
});


//REST Commands
app.get('/api', function (req, res) {
  res.send('GRTAPI is running');
});

app.get('/api/widget' , function(req, res){
	console.log("Received a GEt Request", req.body);
	return WidgetModel.find(function (err, widgets){

		console.log("I got this!");

		if(!err){
			return res.send(widgets);
		} else {
			return console.log("GET ERR: ",err);
		}
	});
});

app.post('/api/widget', function(req, res){
	var widget;
	console.log("Received POST request: ", req.body);

	widget = new WidgetModel({
		key  : req.body.key,
		value: req.body.value
	});

	widget.save(function (err) {
		if (!err) {
			return console.log("created");
		} else {
			return console.log("POST ERR: ", err);
		}
	});

	return res.send(widget);
})


//Schema Design

var Schema = mongoose.Schema;
var Widget = new Schema({
	key      : {type : String},
	value    : {type : String},
	modified : {type : Date, default : Date.now}
});


var WidgetModel = mongoose.model('Widget', Widget);







// Launch server

app.listen(80);
