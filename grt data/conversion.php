<?php 
include_once(dirname(__FILE__).  '\pest\PestJSON.php');

$baseUrl = 'http://107.21.214.193/api';

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





function extractRawFiles(){
	global $inTimes, $inStops, $inTrips;
	echo "Extracting Stops\n";
	$inStops = extractCSV('\stops.txt');

	echo "Extracting Trips\n";
	$inTrips = extractCSV('\trips.txt');

	echo "Extracting Stop Times\n";
	$inTimes = extractCSV('\stop_times.txt');

	echo "Finished Extraction.\n";
}

function printDatabaseVars(){
	global $outStops, $outBuses, $outTimes;
	echo print_r($outStops, true);
	echo print_r($outTimes, true);
	echo print_r($outBuses, true);
}





function buildStopTable(){
	global $inStops, $outStops;
	foreach($inStops as $stop){
		array_push($outStops, array(
				'stopnum' => $stop['stop_id'],
				'address' => $stop['stop_name'],
				'gpslat'  => $stop['stop_lat'],
				'gpslon'  => $stop['stop_lon']
		));
	}
}



function buildBusAndTimeTable(){
	global $inTimes, $inTrips, $outBuses, $outTimes;

	$processedBuses = array();
	foreach($inTimes as $time){
		$trip = findInTrip($time['trip_id']);

		//process the bus num
		$busnum = $trip['route_id'];
		$busdesc = $trip['trip_headsign'];		
		if($trip['trip_headsign'][1] == ' '){
			$busnum = $busnum . $trip['trip_headsign'][0];
			$busdesc = substr($busdesc,2);
		}
		array_push($outTimes, array(
		           'stopnum' => $time['stop_id'],
		           'busnum'  => $busnum,
		           'time'    => $time['departure_time']
		));

		if(!in_array($busnum, $processedBuses)){
			array_push($outBuses, array(
			        'stopnum' => $time['stop_id'],
			        'busnum'  => $busnum,
			        'busdesc' => $busdesc
			));
			array_push($processedBuses, $busnum);
		}
	}
}



//Returns a trip obj based on a trip ID
function findInTrip($tripId){
	global $inTrips;
	foreach($inTrips as $trip){	
		if($tripId == $trip['trip_id']){
			return $trip;
		}	
	}
	return array();
}





extractRawFiles();
buildStopTable();
buildBusAndTimeTable();
printDatabaseVars();



//echo print_r($outStops,true);

//echo print_r(findInTrip("549305"));


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
		'stopnum' => timeRow.stop_id,
		'busnum'  => tripRow.route_id, //requires some post-processing
		'time'    => timeRow.depart_time
	);
	if(listOfProcessedBuses does not contain tripRow.route_id){
		outBuses.push(
			'stopnum' => timeRow.stop_id,
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