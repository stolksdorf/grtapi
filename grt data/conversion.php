<?php 
include_once(dirname(__FILE__).  '\pest\PestJSON.php');

$baseUrl = 'http://107.21.214.193/api';
$pest = new pestJSON($baseUrl);

//Database vars
$inTimes = array();
$inStops = array();
$inTrips = array();

$outStops = array();
$outBuses = array();
$outTimes = array();


function extractCSV($path){	
	//$basePath = '\GRT_GTFS';
	$basePath = '\testGTFS';

	$tokens = explode("\n", file_get_contents(dirname(__FILE__) . $basePath . $path));
	$index = explode(",", str_replace(array("\r", "\n"), '', $tokens[0]));
	$result = array();
	for($t=1; $t < count($tokens); $t++){
		if($tokens[$t] != ""){
			$tokens[$t] = str_replace(array("\r", "\n"), '', $tokens[$t]);
			array_push($result, array_combine($index, explode(",", $tokens[$t])));
		}
	} 
	return $result;
}

function buildStopTable($inStops){
	foreach($inStops as $stop){
		sendToEC2('/stop', array(
				'stopid' => $stop['stop_id'],
				'stopname' => $stop['stop_name'],
				'lat'  => $stop['stop_lat'],
				'lon'  => $stop['stop_lon']
		));
	}
}

function buildBusAndTimeTable($inTrips, $inTimes){
	$processedBuses = array();
	foreach($inTimes as $time){
		$trip = findInTrip($time['trip_id'], $inTrips);

		//process the bus num
		$busnum = $trip['route_id'];
		$busdesc = $trip['trip_headsign'];		
		if($trip['trip_headsign'][1] == ' '){
			$busnum = $busnum . $trip['trip_headsign'][0];
			$busdesc = substr($busdesc,2);
		}

		sendToEC2('/time', array(
		           'stopid' => $time['stop_id'],
		           'busnum'  => $busnum,
		           'time'    => $time['departure_time']
		));

		//if(!in_array($busnum, $processedBuses)){
		sendToEC2('/bus', array(
		        'busnum'  => $busnum,
		        'busdesc' => $busdesc
		));
			//array_push($processedBuses, $busnum);
		//}

		sendToEC2('/stopbus', array(
		        'stopid' => $time['stop_id'],
		        'busnum'  => $busnum
		));
	}

}


function sendToEC2($resource, $data){
	global $pest;
	$demo = false;
	if($demo){
		echo print_r($data,true);
	}else{
		$pest->post($resource, $data);
	}
}

//Returns a trip obj based on a trip ID
function findInTrip($tripId, $inTrips){

	foreach($inTrips as $trip){	
		if($tripId == $trip['trip_id']){
			return $trip;
		}	
	}
	return array();
}



buildStopTable(extractCSV('\stops.txt'));

buildBusAndTimeTable(
        extractCSV('\trips.txt'),
        extractCSV('\stop_times.txt')
);
echo "Finished";






/*Steps

1) extract out the 3 needed csv
2) build the stops files super easy
3) Loop through the inStopTimes
*/

/*
listOfProcessedBuses = array();

foreach(timeRow in stopTimes){
	tripRow = getTrip(timeRow.trip_id);
	outTimes.push(
		'stopid' => timeRow.stop_id,
		'busnum'  => tripRow.route_id, //requires some post-processing
		'time'    => timeRow.depart_time
	);
	if(listOfProcessedBuses does not contain tripRow.route_id){
		outBuses.push(
			'stopid' => timeRow.stop_id,
			'busnum'  => tripRow.route_id,
			'busdesc' => tripRow.trip_headsign		
		);
		add to list of processed buses
	}
}





//echo print_r(extractCSV('/GRT_GTFS/agency.txt'), true);

//extractCSV('/GRT_GTFS/stop_times.txt');
//extractCSV('/GRT_GTFS/stops.txt');
//extractCSV('/GRT_GTFS/trips.txt');
//extractCSV('/SampleGTFS/stop_times.txt');



$pest = new pestJSON($baseUrl);

/*
try{
	$widget = $pest->get('/widget');
	echo print_r($widget,true);
} catch(Pest_UnknownResponse $e){
	echo print_r($e,true);
}


*/

/*
echo print_r($pest->post('/widget', 
	array(
		'key' => 'foo3',
		'value' => 'Yeah yeah!'
	)
));


/*



   
        $baseUrl = $this->getApiBaseUrl();
        $pest = new PestJSON($baseUrl);
    
        $pest->setupAuth($this->apiKey, $this->apiSecret);
        $this->_restClient = $pest;
    
        return $this->_restClient;
    }
    
    
*/