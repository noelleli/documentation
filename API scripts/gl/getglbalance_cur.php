<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__)."/error.log");
date_default_timezone_set('America/Los_Angeles');

fclose(STDIN);
fclose(STDOUT);
fclose(STDERR);
$STDIN = fopen('/dev/null', 'r');
$STDOUT = fopen(dirname(__FILE__)."/application.log", 'a+');
$STDERR = fopen(dirname(__FILE__)."/../error.log", 'a+');

$mysenderid = 'MAKERMEDIA';
$mysenderpwd = 'H88FbZCGFI';
$mycompany = 'MAKERMEDIA';
$myuser = 'nli';
$mypwd = 'Maker2015!!';
$controlId  = time();
$sessonId = 'J1m-DXB9dVdLwhp3DeuzZX0nV0rCGg..';
$month = date("F", time());
//echo $month;

$reportingPeriod = "Month Ended ".$month." 2015";
//$accountNames = array('Revenu', 'Cost Of Sales', 'Personal Expense', 'Marketinf');
$apccount = 'Accounts Payable';
$arccount = 'Account Receivable, Net of Allowance';
$glaccount = 'Net Income - Det';

$xmlPostStr = '<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE request SYSTEM "intacct_request.v2.1.dtd"><request><control><senderid>';
$xmlPostStr.= $mysenderid;
$xmlPostStr.= '</senderid><password>';
$xmlPostStr.= $mysenderpwd;
$xmlPostStr.= '</password><controlid>';
$xmlPostStr.= $controlId;
$xmlPostStr.= '</controlid><uniqueid>true</uniqueid><dtdversion>2.1</dtdversion></control><operation><authentication><login><userid>';
$xmlPostStr.= $myuser;
$xmlPostStr.= '</userid><companyid>';
$xmlPostStr.= $mycompany;
$xmlPostStr.= '</companyid><password>';
$xmlPostStr.= $mypwd;
$xmlPostStr.= '</password></login></authentication>';
$xmlPostStr.= '<content><function controlid="';
$xmlPostStr.= $controlId.'-1';
$xmlPostStr.= '"><get_accountbalances><reportingperiodname>';
$xmlPostStr.= $reportingPeriod;
$xmlPostStr.= '</reportingperiodname><accountgroupname>';
$xmlPostStr.= $apccount;
$xmlPostStr.= '</accountgroupname></get_accountbalances></function></content>';
$xmlPostStr.= '<content><function controlid="';
$xmlPostStr.= $controlId.'-2';
$xmlPostStr.= '"><get_accountbalances><reportingperiodname>';
$xmlPostStr.= $reportingPeriod;
$xmlPostStr.= '</reportingperiodname><accountgroupname>';
$xmlPostStr.= $arccount;
$xmlPostStr.= '</accountgroupname></get_accountbalances></function></content>';
$xmlPostStr.= '<content><function controlid="';
$xmlPostStr.= $controlId.'-2';
$xmlPostStr.= '"><get_accountbalances><reportingperiodname>';
$xmlPostStr.= $reportingPeriod;
$xmlPostStr.= '</reportingperiodname><accountgroupname>';
$xmlPostStr.= $glaccount;
$xmlPostStr.= '</accountgroupname></get_accountbalances></function></content></operation></request>';

//echo $xmlPostStr;

// INITIATE CURL
$ch = curl_init();
curl_setopt( $ch, CURLOPT_URL, 'HTTPS://api.intacct.com/ia/xml/xmlgw.phtml');
curl_setopt( $ch, CURLOPT_HEADER, 0 );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: x-intacct-xml-request'));
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt( $ch, CURLOPT_TIMEOUT, 3000); //Seconds until timeout
curl_setopt( $ch, CURLOPT_POST, 1 );
	  
  // BUILD THE POST BODY
/*$body = "encodehere '".urlencode( $xmlPostStr )."' end encode";
echo $body;*/

curl_setopt( $ch, CURLOPT_POSTFIELDS, $xmlPostStr );
  // POST AND GET RESPONSE

$outputfile = fopen(dirname(__FILE__)."/glbalance".$month.".xml", 'w+');

curl_setopt( $ch, CURLOPT_FILE, $outputfile);  	

curl_exec( $ch );

//echo $response;

curl_close( $ch );

$current_time = date("Y-m-d H:i:s", time());
printf("\nexporting %s file: %s. \n", $month, $current_time);
