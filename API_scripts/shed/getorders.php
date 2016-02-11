<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('America/Los_Angeles');

include dirname(__DIR__)."/config.php";

$keys = new keys();

$api_key = $keys::shedkey;
$passwd = $keys::shedpwd;
$path = "makershed.myshopify.com/admin/orders.json";
$countpath = "makershed.myshopify.com/admin/orders/count.json";
$filters = "created_at_min=2015-05-01&created_at_max=2015-06-20&financial_status=paid&fulfillment_status=shipped";

$dbconnection = new mysqlconnect();
$dbconnection->connect($dbName);
$mysqli = $dbconnection->connection;

$query = "insert ignore into shedfulfillments(createdat, fulfillmentid, orderid, updatedat) values (?,?,?,?)";

if ($stmt = $mysqli->prepare($query)){
	$stmt->bind_param("siis", $createdat, $fulfillmentid, $orderid, $updatedat);
}
else {
	printf("query error: %s\n", $mysqli->error);
}

$ordercount = get_order_counts($api_key,$passwd,$countpath,$filters);
$count = json_decode($ordercount, true);
print_r($count);
$pages = ceil($count["count"]/250);
echo $pages;


$mysqli->query("start");

for ($i=1; $i<=$pages; ++$i){
	$orders = get_orders($api_key, $passwd, $path, $filters,$i);
	$orderarray = json_decode($orders, true);
	//print_r($orderarray);
	foreach ($orderarray['orders']as $order){
	$fulfillments = $order['fulfillments'];
		foreach ($fulfillments as $fulfillment){
			$createdat = date("Y-m-d H:i:s",strtotime($fulfillment['created_at']));
			$fulfillmentid = $fulfillment['id'];
			$orderid = $fulfillment['order_id'];
			$updatedat = date("Y-m-d H:i:s", strtotime($fulfillment['updated_at']));
			printf("Inserted: %s.\n", $createdat);
			$stmt->execute();
		}
	}
	sleep(2);
	printf("at page: %s.\n", $i);
}


$stmt->close();
$mysqli->query("done");
$mysqli->close();

function get_orders($api_key,$passwd,$path,$filters, $i){
	$getorders = "https://".$api_key.":".$passwd."@".$path."?".$filters."fields=created_at,id&limit=250=&page=".$i;
	echo $getorders;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $getorders);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//curl_setopt($ch, CURLOPT_TIMEOUT, 3000);

	$response = curl_exec($ch);
	curl_close($ch);
	return $response;
}

function get_order_counts($api_key,$passwd,$countpath,$filters){
	$getordercounts = "https://".$api_key.":".$passwd."@".$countpath."?".$filters;
	echo $getordercounts;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $getordercounts);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//curl_setopt($ch, CURLOPT_TIMEOUT, 3000);
	$ordercounts = curl_exec($ch);
	curl_close($ch);
	return $ordercounts;
}

