<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__)."/../error.log");
date_default_timezone_set('America/Los_Angeles');

include dirname(__FILE__)."/config.php";

fclose(STDIN);
fclose(STDOUT);
fclose(STDERR);
$STDIN = fopen('/dev/null', 'r');
$STDOUT = fopen(dirname(__FILE__)."/application.log", 'a+');
$STDERR = fopen(dirname(__FILE__)."/error.log", 'a+');

$dbName = 'marketing';
$dbconnection = new mysqlconnect();
$dbconnection->connect($dbName);
$mysqli = $dbconnection->connection;

$filearray = array('dfpreportApr.xml.gz', 'dfpreportMay.xml.gz', 'dfpreportJun.xml.gz', 
	'dfpreportJul.xml.gz','dfpreportAug.xml.gz', 'dfpreportSep.xml.gz');

foreach ($filearray as $gzfile){
	$buffer = 2000000;
	$unzipped = gzopen($gzfile,'rb');
	$filename = str_replace('.gz', '', $gzfile);
	$xmlfile = fopen($filename, 'wb');
	while(!gzeof($unzipped)){
		fwrite($xmlfile, gzread($unzipped, $buffer));
	}
	gzclose($unzipped);
	fclose($xmlfile);
	$xmlstring = file_get_contents(dirname(__FILE__).'/'.$filename);
	$xml = simplexml_load_string($xmlstring);
	$xmldataset = $xml->ReportData->DataSet;
	//$advertisername = (string)$xmldataset->Column[2]->Val;
	$xmlrows = $xmldataset->Row->count();
	//print_r($xmldataset);
	printf("\ntotal datarow: %d.\n", $xmlrows);


	$query = "insert into dfpdata2(statdate, adunit, advertisername, creativename, adunitid, advertiserid, creativeid,
		totalimpressions, totalclicks, totalrevenue, adsenseimpressions, adsenseclicks, adsenserevenue) values (?,?,?,?,?,?,?,?,?,?,?,?,?)";

	$stmt = $mysqli->prepare($query);

	$stmt->bind_param("sssssssiidiid", $statdate, $adunit, $advertisername, $creativename, $adunitid, $advertiserid, $creativeid, 
		$totalimpressions, $totalclicks, $totalrevenue, $adsenseimpressions, $adsenseclicks, $adsenserevenue);
	$mysqli->query("start query");

	for ($i = 0; $i <$xmlrows; $i++){
		$datarow = $xmldataset->Row[$i];
		$date = (string)$datarow->Column[0]->Val;
		$statdate = date('Y-m-d', strtotime($date));
		$adunit = (string)$datarow->Column[1]->Val;
		$advertisername = (string)$datarow->Column[2]->Val;
		$creativename = (string)$datarow->Column[3]->Val;
		$adunitid = (string)$datarow->Column[4]->Val;
		$advertiserid = (string)$datarow->Column[5]->Val;
		$creativeid = (string)$datarow->Column[6]->Val;
		$totalimpressions = (int)str_replace(',', '', $datarow->Column[7]->Val);
		$totalclicks = (int)str_replace(',', '', $datarow->Column[8]->Val);
		$totalrevenue = floatval(str_replace(array('$', ','), '', $datarow->Column[9]->Val));
		$adsenseimpressions = (int)str_replace(',', '', $datarow->Column[10]->Val);
		$adsenseclicks = (int)str_replace(',', '', $datarow->Column[11]->Val);
		$adsenserevenue = floatval(str_replace(array('$', ','), '', $datarow->Column[12]->Val));
		$stmt->execute();
		printf("date: %s; revenue: %.2f.\n", $statdate, $totalrevenue);
	}
}

$stmt->close();

$mysqli->query("done");

$mysqli->close();

function xml_attribute($rownumber, $attributes){
		if (isset($rownumber[$attributes]))
		return (string)$rownumber[$attributes]; 
}
