<?php
ini_set('memory_limit', '16128M');
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('America/Los_Angeles');

include dirname(__FILE__)."/config.php";


$gaprofile = new gaprofiles();
$profile = $gaprofile::makezine;

$today = date("Y-m-d", time());

$file = file_get_contents(dirname(__FILE__).'/boards/boardspages.csv');
$filecontent = array_map("str_getcsv", explode("\n", $file));

$links =  array();
for ($i = 1; $i < count($filecontent); $i++) {
  $analytics = getService();
  $galink = $filecontent[$i][2];
  $click_array = getResults($analytics, $galink);
  $link_clicks = $click_array->getRows();
  array_push($links, array('board_name' => $filecontent[$i][0], 
                            'link_name' => $filecontent[$i][1],
                            'link' => $filecontent[$i][2],
                            'click_total' => $click_array['totalsForAllResults']['ga:totalEvents']));
}

$fp = fopen('link_clicks.csv', 'w');
foreach ($links as $line) {
  fputcsv($fp, $line);
}
fclose($fp);

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

function getResults($analytics,$filter) {
  $optParams = array (
    'dimensions' => 'ga:date',
    'filters' => 'ga:eventLabel=@'.$filter,
    'max-results' => '1000');

  return $analytics->data_ga->get(
       'ga:8515',
       '2016-01-25',
       '2016-02-03',
       'ga:totalEvents',
       $optParams);
}
