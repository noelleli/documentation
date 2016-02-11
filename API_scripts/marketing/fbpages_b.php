<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__)."/../error.log");
date_default_timezone_set('UTC');


include dirname(__DIR__)."/config.php";

fclose(STDIN);
fclose(STDOUT);
fclose(STDERR);
$STDIN = fopen('/dev/null', 'r');
$STDOUT = fopen(dirname(__FILE__)."/application.log", 'a+');
$STDERR = fopen(dirname(__FILE__)."/../error.log", 'a+');

$keys = new keys();

$api_key = $keys::fbkey;
$fbaccountid = $keys::fbaccount;
$dbName = "marketing";

$currenttime = date("Y-m-d H:i:s", time());
printf("fbpages_b started at: %s.\n", $currenttime);

$sincedate = date("m/d/Y", time());
$untildate = date("m/d/Y", strtotime("+1 day", time()));


$dbconnection = new mysqlconnect();
$dbconnection->connect($dbName);
$mysqli = $dbconnection->connection;


$selectquery = "select fblookup.endpoint from fblookup where fblookup.schedule = \"b\"";
$endpoints = array();

if ($result = $mysqli->query($selectquery)){
	while ($row = $result->fetch_assoc()){
		$endpoints[]= $row;
	}
}
else {
	printf("error: %s\n", $mysqli->error);
}

$insertquery = "insert into fbpagestats_b(statname, endtime, stats, title, dimensions, displaydate) values (?, ?, ?, ?, ?,?)";

if ($stmt = $mysqli->prepare($insertquery)){
	$stmt->bind_param("ssisss", $statname, $endtime, $stats, $title, $dimensions, $displaydate);
	$mysqli->query("start");
}
else {
	printf("error: %s\n", $mysqli->error);
}

foreach ($endpoints as $endpointarray) {
	$endpoint = $endpointarray["endpoint"];
	$stats = get_stats($endpoint,$api_key,$sincedate,$untildate);
	$statarray = json_decode($stats, true);
	//print_r($statarray);
	$statname = $statarray["data"][0]["name"];
	$title = $statarray["data"][0]["title"];
	foreach($statarray["data"][0]["values"] as $statvalue) {
		$endtime = date("Y-m-d H:i:s", strtotime($statvalue["end_time"]));
		$displaydate = date("Y-m-d", strtotime("-1 day", strtotime($statvalue["end_time"])));
		foreach($statvalue["value"] as $key =>$val){
			$dimensions = $key;
			$stats = $val;
			$stmt->execute();
			printf("date %s; stat %s: %d. display as %s.\n", $endtime, $title, $stats, $displaydate);
		}
	}	
}

$stmt->close();
$mysqli->query("done");
$dbconnection->dbclose();

function get_stats($endpoint,$api_key,$sincedate,$untildate){
	$getstats = "https://graph.facebook.com/v2.3/".$endpoint."?access_token=".$api_key."&since=".$sincedate."&until=".$untildate;
	//echo $getstats;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $getstats);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec ($ch);
	//echo $response;
	curl_close ($ch);
	return $response;
}