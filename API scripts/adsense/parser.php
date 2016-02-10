<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('America/Los_Angeles');

include dirname(__FILE__)."/config.php";

$dbName = 'marketing';
$dbconnection = new mysqlconnect();
$dbconnection->connect($dbName);
$mysqli = $dbconnection->connection;

$jsondata = file_get_contents('data.json');

$datarows = (json_decode($jsondata, true));

//print_r($datarows["rows"]);

$query = "insert into adsensedata(statdate, pageviews, impressions, clicks, revenue) value (?,?,?,?,?)";

$stmt = $mysqli->prepare($query);

$stmt->bind_param("siiid", $statdate, $pageviews, $impressions, $clicks, $revenue);
$mysqli->query("start query");

for ($i = 0; $i <243; $i++){
	$datarow = $datarows["rows"][$i];
	$statdate = date('Y-m-d', strtotime($datarow[0]));
	$pageviews = (int)$datarow[1];
	$impressions = (int)$datarow[2];
	$clicks = (int)$datarow[3];
	$revenue = number_format($datarow[4], 2);
	$stmt->execute();
	printf("date: %s; revenue: %d.\n", $statdate, $revenue);
}

$stmt->close();

$mysqli->query("done");

$mysqli->close();



