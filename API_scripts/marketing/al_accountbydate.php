<?php
ini_set('memory_limit', '16128M');
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

$keys = new keys();


$yesterday = date("Y-m-d", strtotime("yesterday"));
$current_time = date("Y-m-d H:i:s", time());
printf("\n%s\n", $yesterday);

$dbconnection = new mysqlconnect();
$dbconnection->connect($dbName);
$mysqli = $dbconnection->connection;

$merchantid = $keys::almerchantid;
$authkey = $keys::alauthkey;
$module = 'MerchantReport';
$reportid = '12';


$report = get_report($authkey, $merchantid, $module, $reportid, $yesterday);

$xml = new simpleXMLElement($report);

$rowcount = count($xml->children());
//print_r($xml->Table1[2]->Sales);


$query = "insert into al_accountstats(accountdate, impressions, clicks, alsales, convertedsales, mobilesales, mobileconversions, commissions, incentives,
	networkcommissions, adj, newcustomers, newcustomersales, cpcfees, networkcpcfees, totalcommissions) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) on duplicate key update 
	accountdate=values(accountdate), impressions=values(impressions), clicks=values(clicks), alsales=values(alsales), convertedsales=values(convertedsales), mobilesales=values(mobilesales),
	mobileconversions=values(mobileconversions), commissions=values(commissions), incentives=values(incentives), networkcommissions=values(networkcommissions), adj=values(adj),
	newcustomers=values(newcustomers), newcustomersales=values(newcustomersales), cpcfees=values(cpcfees), networkcpcfees=values(networkcpcfees), totalcommissions=values(totalcommissions)";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("siidididddiidddd", $accountdate, $impressions, $clicks, $alsales, $convertedsales, $mobilesales, $mobileconversions, $commissions, $incentives,
	$networkcommissions, $adj, $newcustomers, $newscustomersales, $cpcfees, $networkcpcfees, $totalcommissions);

$mysqli->query("start");

for ($i = 0; $i < $rowcount; ++$i){
	$row = $xml->Table1[$i];
	$date = strtotime($row->Date);
	$accountdate = date('Y-m-d', $date); 
	$impressions = (int)str_replace(',', '', $row->Ad_Impressions); 
	$clicks = (int)str_replace(',', '', $row->Click_Throughs);
	$alsales = floatval(str_replace(array(',', '$'), '', $row->Sales)); 
	$convertedsales = $row->Number_of_Sales;
	$mobilesales = floatval(str_replace(array(',', '$'), '', $row->Mobile_Sales)); 
	$mobileconversions = $row->Number_of_Mobile_Sales; 
	$commissions = floatval(str_replace(array(',', '$'), '', $row->Commissions));
	$incentives = floatval(str_replace(array(',', '$'), '', $row->Incentives));
	$networkcommissions = floatval(str_replace(array(',', '$'), '', $row->Network_Commissions)); 
	$adj = $row->Number_of_Adjustments; 
	$newcustomers = $row->New_Customers; 
	$newscustomersales = floatval(str_replace(array(',', '$'), '', $row->New_Customer_Sales)); 
	$cpcfees = floatval(str_replace(array(',', '$'), '', $row->CPC_Fees)); 
	$networkcpcfees = floatval(str_replace(array(',', '$'), '', $row->Newwork_CPC_Fees)); 
	$totalcommissions = floatval(str_replace(array(',', '$'), '', $row->Total_Commissions_Fees));
	$stmt->execute();
	printf("\nran at %s for %s: inserted %d, commission %s.\n", $current_time, $yesterday, $rowcount, $totalcommissions);
}

$stmt->close();
$mysqli->query("done");

$dbconnection->dbclose();

function get_report($authkey, $merchantid, $module, $reportid, $yesterday){
	$request = 'https://classic.avantlink.com/api.php?auth_key='.$authkey.'&merchant_id='.$merchantid.'&module='.$module.'&report_id='.$reportid.'&date_begin='.$yesterday.'&date_end='.$yesterday;
	//echo $request;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $request);
	//curl_setopt($ch, CURLOPT_VERBOSE, true);
	curl_setopt($ch, CURLOPT_HEADER, 0 );
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: avantlink-request'));
	//curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt($ch, CURLOPT_TIMEOUT, 3000); //Seconds until timeout
	//$outputfile = fopen(dirname(__FILE__)."al_response.xml", 'w+');
	//curl_setopt( $ch, CURLOPT_POST, 1 );

	//curl_setopt( $ch, CURLOPT_FILE, $outputfile);
	$response = curl_exec($ch);
	//echo $response;
	curl_close( $ch );
	return $response;
}