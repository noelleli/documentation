<?php
ini_set('memory_limit', '16128M');
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('America/Los_Angeles');

include dirname(__FILE__)."/config.php";


$dbName = "marketing";
//$profile = getFirstProfileId($analytics);

$gaprofile = new gaprofiles();
$profile = $gaprofile::makezine;

$today = date("Y-m-d", time());

$dbconnection = new mysqlconnect();
$dbconnection->connect($dbName);
$mysqli = $dbconnection->connection;


$insertquery = "insert into mz_reach(statdate, pageviews, sessions, users, newusers, bounces, timeonsite) values (?, ?,?,?,?,?,?)";

if ($stmt = $mysqli->prepare($insertquery)){
  $stmt->bind_param("siiiiid", $statdate, $pageviews, $sessions, $users, $newusers, $bounces, $timeonsite);
  $mysqli->query("start");
}
else {
  printf("error: %s\n", $mysqli->error);
}

$analytics = getService();
//$profile = getFirstProfileId($analytics);
$profile = '8515';
$mzmetrics = getResults($analytics, $profile);


if (count($mzmetrics->getRows()) > 0){
  $rowCount = count($mzmetrics->getRows());
  $rows = $mzmetrics->getRows();
  //print_r($rows);
  for ($i=0; $i<$rowCount; $i++){
    $statdate = date('Y-m-d', strtotime($rows[$i][0]));
    $pageviews = $rows[$i][1];
    $sessions = $rows[$i][2];
    $users = $rows[$i][3];
    $newusers = $rows[$i][4];
    $bounces = $rows[$i][5];
    $timeonsite = floatval($rows[$i][6]);
    $stmt->execute();
    printf("date: %s, pageviews: %d.\n", $statdate, $pageviews);
  }
}

$stmt->close();
$mysqli->query("done");
$dbconnection->dbclose();

function getService(){
  // Creates and returns the Analytics service object.

  // Load the Google API PHP Client Library.
  require_once 'google-api-php-client/src/Google/autoload.php';

  // Use the developers console and replace the values with your
  // service account email, and relative location of your key file.
  $service_account_email = '186879846102-oa1so3s7gaaq0gcb735s02if9trafe5m@developer.gserviceaccount.com';
  $key_file_location = 'client_secrets.p12';

  // Create and configure a new client object.
  $client = new Google_Client();
  $client->setApplicationName("GAnalytics");
  $analytics = new Google_Service_Analytics($client);

  // Read the generated client_secrets.p12 key.
  $key = file_get_contents($key_file_location);
  $cred = new Google_Auth_AssertionCredentials(
      $service_account_email,
      array(Google_Service_Analytics::ANALYTICS_READONLY),
      $key
  );
  $client->setAssertionCredentials($cred);
  if($client->getAuth()->isAccessTokenExpired()) {
    $client->getAuth()->refreshTokenWithAssertion($cred);
  }
  return $analytics;
}

function getResults($analytics, $profileId) {
  $optParams = array (
    'dimensions' => 'ga:date',
    'max-results' => '1000');

  return $analytics->data_ga->get(
       'ga:'.$profileId,
       '2014-01-01',
       '2014-12-31',
       'ga:pageviews,ga:sessions,ga:users,ga:newusers,ga:bounces,ga:avgSessionDuration',
       $optParams);
}
