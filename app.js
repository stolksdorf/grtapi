var application_root = __dirname,
    express = require("express"),
    path = require("path"),
    mongoose = require('mongoose'),


var app = express.createServer();

// Config
app.configure(function () {
  app.use(express.bodyParser());
  app.use(express.methodOverride());
  app.use(app.router);
  app.use(express.static(path.join(application_root, "public")));
  app.use(express.errorHandler({ dumpExceptions: true, showStack: true }));
});

// Database
mongoose.connect('mongodb://localhost/grtapiDB');

//Schema Design
var Schema = mongoose.Schema;

var StopSchema = new Schema({
	stopnum  : {type : String},
	gpslon   : {type : Number},
	gpslat   : {type : Number},
	stopname : {type : String}
});

var BusSchema = new Schema({
	stopnum  : {type : String},
	busnum   : {type : String},
	busdesc  : {type : String}
});

var TimeSchema = new Schema({
	stopnum  : {type : String},
	busnum   : {type : String},
	time     : {type : String}
});

var StopModel = mongoose.model('Stop', StopSchema);
var BusModel  = mongoose.model('Bus' , BusSchema);
var TimeModel = mongoose.model('Time', TimeSchema);

/*
var Widget = new Schema({
	key      : {type : String},
	value    : {type : String},
	modified : {type : Date, default : Date.now}
});
var WidgetModel = mongoose.model('Widget', Widget);
*/








//Launch Site
app.get('/api', function (req, res) {
  res.send('GRTAPI is running, fuck yeah!');
});



///////////REST GET Commands
app.get('/api/stop', function(req, res){
	StopModel.find({}, function(error, stops){
		if(!error){
			return res.send(stops);
		}else{
			return console.log("GET Stop ERR: ", error);
		}
	});
});

app.get('/api/bus', function(req, res){
	BusModel.find({}, function(error, buses){
		if(!error){
			return res.send(buses);
		}else{
			return console.log("GET Bus ERR: ", error);
		}
	});
});

app.get('/api/time', function(req, res){
	TimeModel.find({}, function(error, times){
		if(!error){
			return res.send(times);
		}else{
			return console.log("GET Time ERR: ", error);
		}
	});
});

app.get('/api/search', function(req, res){

	return res.send('Implementating this later bro');

});








////////////REST POST Commands
app.post('/api/stop', function(req, res){
	var stop = new StopModel({
		stopnum  : req.body.stopnum,
		gpslon   : req.body.gpslon,
		gpslat   : req.body.gpslat,
		stopname : req.body.stopname
	});

	stop.save(function (err) {
		if (!err) {
			return console.log("Added New Stop: " + req.body.stopnum);
		} else {
			return console.log("POST Stop ERR: ", err);
		}
	});
	return res.send(stop);
});

app.post('/api/bus', function(req, res){
	var bus = new BusModel({
		stopnum  : req.body.stopnum,
		busnum   : req.body.busnum,
		busdesc  : req.body.busdesc
	});

	bus.save(function (err) {
		if (!err) {
			return console.log("Added New Bus: " + req.body.busnum);
		} else {
			return console.log("POST Bus ERR: ", err);
		}
	});
	return res.send(bus);
});

app.post('/api/time', function(req, res){
	var time = new TimeModel({
		stopnum  : req.body.stopnum,
		busnum   : req.body.busnum,
		time     : req.body.busdesc
	});

	time.save(function (err) {
		if (!err) {
			return console.log("Added New Time: " + req.body.stopnum + 
			                   " for " + req.body.busnum + 
			                   " at " + req.body.time);
		} else {
			return console.log("POST Time ERR: ", err);
		}
	});
	return res.send(time);
});





/*

app.get('/api/widget' , function(req, res){
	console.log("Received a GET Request", req.body);
	WidgetModel.find({}, function (err, widgets){
		console.log("widgets:", widgets);
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
});

*/








//////////// Launch server
app.listen(80);
