<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__)."/../error.log");
date_default_timezone_set('America/Los_Angeles');

include dirname(__DIR__)."/config.php";

fclose(STDIN);
fclose(STDOUT);
fclose(STDERR);
$STDIN = fopen('/dev/null', 'r');
$STDOUT = fopen(dirname(__FILE__)."/application_project.log", 'a+');
$STDERR = fopen(dirname(__FILE__)."/../error.log", 'a+');

$time = time();
$current_time = date("Y-m-d H:i:s", time());
printf("\nparser ran: %s.\n", $current_time);

$lastmonth = date("F", strtotime("first day of last month"));
$currentmonth = date("F", time());
$currentyear = date("Y", time());
$dbName = "financial";

$dbconnection = new mysqlconnect();
$dbconnection->connect($dbName);
$mysqli = $dbconnection->connection;

$filemonths = array($lastmonth,$currentmonth);

$deletecredits = "delete from shedcredits where shedcredits.endingdate = ?";
if($deletecredits = $mysqli->prepare($deletecredits)){
	$deletecredits->bind_param("s", $enddate);
}
else {
	printf("error: %s\n", $mysqli->error);
}

$deletedebits = "delete from sheddebits where sheddebits.endingdate = ?";
if($deletedebits = $mysqli->prepare($deletedebits)){
	$deletedebits->bind_param("s", $enddate);
}
else {
	printf("error: %s\n", $mysqli->error);
}

$querycredits = "insert into shedcredits(transactionid, glaccountno, beginningdate, endingdate, creditamount, adjcredits, endbalance, departmentid, projectid) values 
(?, ?, ?, ?, ?, ?, ?, ?, ?)";

if($stmtcredits = $mysqli->prepare($querycredits)){
	$stmtcredits->bind_param("sissdddis", $transaction_id, $glaccountno, $begindate, $endingdate, $credits, $adjcredits, $endbalance, $departmentid, $projectid);
}
else {
	printf("error: %s\n", $mysqli->error);
}

$querydebits= "insert into sheddebits(transactionid, glaccountno, beginningdate, endingdate, debitamount, adjdebits, departmentid, projectid) values 
(?, ?, ?, ?, ?, ?, ?, ?)";

if($stmtdebits = $mysqli->prepare($querydebits)){
	$stmtdebits->bind_param("sissddis", $transaction_id, $glaccountno, $begindate, $endingdate, $debits, $adjdebits, $departmentid, $projectid);
}
else {
	printf("error: %s\n", $mysqli->error);
}

$mysqli->multi_query("start query");

foreach ($filemonths as $filemonth){
	$monthnum = date("m", strtotime($filemonth." ".$currentyear));
	$enddate = "2015-".$monthnum."-30";
	while(file_exists($file = dirname(__FILE__)."/projectbalance".$filemonth.".xml")) {
		printf("\ndeleting data for %s.\n", $enddate);
		$deletecredits->execute();
		$deletedebits->execute();
		$string = file_get_contents($file);
		$xml = simplexml_load_string($string);
		foreach ($results = $xml->operation->result as $result){
			$count = (string)$result->data['count'];
		    for ($i = 0; $i < $count; $i++){
				$row = $result->data->glaccountbalance[$i];
				$glaccountno = $row->ACCOUNTNO;
				$debits = floatval($row->TOTDEBIT);
				$beginning = strtotime("2015-".$monthnum."-01");
				$begindate = date('Y-m-d', $beginning);
				$ending = strtotime("2015-".$monthnum."-30");
				$endingdate = date('Y-m-d', $ending);
				$transaction_id = $monthnum."0115-".$monthnum."3015-".$glaccountno;
				$credits = floatval($row->TOTCREDIT);
				$adjdebits = floatval($row->TOTADJDEBIT);
				$adjcredits = floatval($row->TOTADJCREDIT);
				$endbalance = $row->ENDBAL;
				$departmentid = $row->DEPARTMENTID;
				$projectid = $row->PROJECTID;
				$stmtdebits->execute();
				$stmtcredits->execute();
			    printf("row %d ending date %s ending balanace %s.\n", $i, $endingdate, $endbalance);
			}
		}
		printf("done processed: %s.\n", $file);
		rename($file, dirname(__FILE__)."/project_archived/projectbalance".$filemonth."-".$time.".xml");
		continue;
	}
}

$stmtcredits->close();
$stmtdebits->close();
$mysqli->multi_query("done");
$dbconnection->dbclose();
