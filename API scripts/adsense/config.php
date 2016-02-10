<?php

class mysqlconnect {	
	private $mysqlHost = "make-information.csrmwv3nzzxe.us-east-1.rds.amazonaws.com";
	private $mysqlUser = "makey";
	private $mysqlPass = "'SYc2Eg&fx*V";		
	public $connection;

	function connect($dbName){
		$con = mysqli_connect($this->mysqlHost, $this->mysqlUser, $this->mysqlPass, $dbName);  
			if ($con->connect_errno > 0){
			die ('connection error: ['.$con->connect_errno.']');
		} else {
			$this->connection = $con;
			echo "db connected";
		}
		return $this->connection;
	}

	function dbclose(){
		mysqli_close($this->connection);
		echo "db closed";
	}
}	
	
class keys {
	const fbkey = "CAAMAzRerrtsBALFnrBsh7GIPypRu91HICPjELkfgSNqdFv9UbpZCNrhBoZAbfpif73GlF9NR0ns0hCZBbU7vAtRnIdSc7R7FRnampZCDNuHxkvw65vKy2xp9BHWJJ6znWUG0neTewweZCYpnU7n3SZALwosq2ysZCRohPUEpGA9UYYTWtO7ZA8OFJPJTfFRkkigHcTALNy3QqT8JdZAdrIXZCc";
	const fbaccount = "5873603189";
}