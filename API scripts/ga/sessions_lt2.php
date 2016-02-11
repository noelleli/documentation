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



$dbName = "marketing";
//$profile = getFirstProfileId($analytics);
$profile = '8515';
$yesterday = date("Y-m-d", strtotime("yesterday"));
$current_time = date("Y-m-d H:i:s", time());
printf("\ncurrent time: %s. script for: %s\n", $current_time, $yesterday);

$dbconnection = new mysqlconnect();
$dbconnection->connect($dbName);
$mysqli = $dbconnection->connection;


$insertquery = "insert into mz_sessioncount(record_date, metric) values (?, ?) on duplicate key update 
    record_date=values(record_date), metric=values(metric)";

if ($stmt = $mysqli->prepare($insertquery)){
  $stmt->bind_param("si", $datetime, $metric);
  $mysqli->query("start");
}
else {
  printf("error: %s\n", $mysqli->error);
}

$analytics = getService();
//$profile = getFirstProfileId($analytics);
$profile = '8515';
$sessioncount = getResults($analytics, $profile);

if (count($sessioncount->getRows()) > 0){
  $rowCount = count($sessioncount->getRows());
  $rows = $sessioncount->getRows();
  //print_r($rows);
  for ($i=0; $i<$rowCount; $i++){
    $datetime = date('Y-m-d', strtotime($rows[$i][0]));
    $metric = $rows[$i][1];
    $stmt->execute();
    printf("date: %s, counts: %d.\n", $datetime, $metric);
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
    'segment' => 'users::condition::ga:sessionCount>2',
    'samplingLevel' => 'HIGHER_PRECISION');

   return $analytics->data_ga->get(
       'ga:'.$profileId,
       'yesterday',
       'yesterday',
       'ga:users',
       $optParams);
}
?>