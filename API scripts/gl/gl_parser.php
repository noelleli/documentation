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
$STDOUT = fopen(dirname(__FILE__)."/application.log", 'a+');
$STDERR = fopen(dirname(__FILE__)."/../error.log", 'a+');

$time = time();
$current_time = date("Y-m-d H:i:s", time());
printf("\nparser: %s.\n", $current_time);

$lastmonth = date("F", strtotime("first day of last month"));
$currentmonth = date("F", time());
$currentyear = date("Y", time());
$dbName = "financial";

$dbconnection = new mysqlconnect();
$dbconnection->connect($dbName);
$mysqli = $dbconnection->connection;

$filemonths = array($lastmonth,$currentmonth);


foreach ($filemonths as $filemonth){
	while(file_exists($file = dirname(__FILE__)."/glbalance".$filemonth.".xml")){
		$string = file_get_contents($file);
		$xml = simplexml_load_string($string);
		printf("\nprocessing file %s.\n", $filemonth);
		$monthnum = date("m", strtotime($filemonth." ".$currentyear));
		for ($r = 0; $r < 3; $r++) {
			$results = $xml->operation->result[$r];
			$rowcounts = count($results->data->children());			
			switch ($r) {
			case 0:			
				$creditdb = 'apcredits';
				$debitedb = 'apdebits';	
				break 1;
			case 1:	
				$creditdb = 'arcredits';
				$debitedb = 'ardebits';
				break 1;
			case 2:	
				$creditdb = 'glcredits';
				$debitedb = 'gldebits';
				break 1;
			}
			printf("%s.\n", $creditdb);
			printf("%d.\n", $r);

			$querycredits = "insert into ".$creditdb."(transactionid, glaccountno, beginningdate, endingdate, creditamount, adjcredits, endbalance) values 
			(?, ?, ?, ?, ?, ?, ?) on duplicate key update glaccountno=values(glaccountno), beginningdate=values(beginningdate), endingdate=values(endingdate), creditamount=values(creditamount), adjcredits=values(adjcredits), endbalance = values(endbalance)";

			if($stmtcredits = $mysqli->prepare($querycredits)){
				$stmtcredits->bind_param("sissddd", $transaction_id, $glaccountno, $begindate, $endingdate, $credits, $adjcredits, $endbalance);
			}
			else {
				printf("error: %s\n", $mysqli->error);
			}

			$querydebits= "insert into ".$debitedb."(transactionid, glaccountno, beginningdate, endingdate, debitamount, adjdebits) values 
			(?, ?, ?, ?, ?, ?) on duplicate key update glaccountno=values(glaccountno), beginningdate=values(beginningdate), endingdate=values(endingdate), debitamount=values(debitamount), adjdebits=values(adjdebits)";

			if($stmtdebits = $mysqli->prepare($querydebits)){
				$stmtdebits->bind_param("sissdd", $transaction_id, $glaccountno, $begindate, $endingdate, $debits, $adjdebits);
			}
			else {
				printf("error: %s\n", $mysqli->error);
			}

			$mysqli->multi_query("start query");

			for ($i = 0; $i < $rowcounts; $i++){
				$row = $results->data->accountbalance[$i];
				$glaccountno = $row->glaccountno;
				$startbalance = floatval($row->startbalance);
				$debits = floatval($row->debits);
				$credits = floatval($row->credits);
				$adjdebits = floatval($row->adjdebits);
				$adjcredits = floatval($row->adjcredits);
				$endbalance = floatval($row->endbalance);
				$beginning = strtotime("2015-".$monthnum."-01");
				$begindate = date('Y-m-d', $beginning);
				$ending = strtotime("2015-".$monthnum."-30");
				$endingdate = date('Y-m-d', $ending);
				$transaction_id = $monthnum."0115-".$monthnum."3015-".$glaccountno;
				$stmtdebits->execute();
				$stmtcredits->execute();
				printf("glaccount: %s inserted.\n", $transaction_id);
				}
		}
		rename($file, dirname(__FILE__)."/archived/glbalance".$filemonth."-".$time.".xml");
		continue;
	}
}

$stmtcredits->close();
$stmtdebits->close();

$mysqli->multi_query("done");

$dbconnection->dbclose();



