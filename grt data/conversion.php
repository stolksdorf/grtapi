<?php 



function extractCSV($path){
	$tokens = explode("\n", file_get_contents(dirname(__FILE__) . $path));
	$index = explode(",", $tokens[0]);
	$result = array();
	for($t=1; $t < count($tokens); $t++){
		if($tokens[$t] != ""){
			array_push($result, array_combine($index, explode(",", $tokens[$t])));
		}
	} 
	return $result;
}


//echo print_r(extractCSV('/GRT_GTFS/agency.txt'), true);

//extractCSV('/GRT_GTFS/stop_times.txt');
//extractCSV('/GRT_GTFS/stops.txt');
//extractCSV('/GRT_GTFS/trips.txt');
//extractCSV('/SampleGTFS/stop_times.txt');
echo "Finished";
