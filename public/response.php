<?php
error_reporting(0);
Class dbObj{
	var $servername = "postgres-service";
	var $username = "test";
	var $password = "test";
	var $dbname = "minukube-php";
	var $port = "5432";
	var $conn;
	function getConnstring() {
		$con = pg_connect("host=".$this->servername." port=".$this->port." dbname=".$this->dbname." user=".$this->username." password=".$this->password."") or die("Connection failed: ".pg_last_error());

		
		if (pg_last_error()) {
			printf("Connect failed: %s\n", pg_last_error());
			exit();
		} else {
			$this->conn = $con;
		}
		return $this->conn;
	}
}

class Employee {
	protected $conn;
	protected $data = array();
	function __construct() {

		$db = new dbObj();
		$connString =  $db->getConnstring();
		$this->conn = $connString;
	}
	
	public function getEmployees() {
		$query = "SELECT * FROM employees";
		$result = pg_query($query);
			if (pg_last_error()) {
			include('import.php');
			exit();
			}
			else
				{
    			$sql = "SELECT * FROM employees";
				$queryRecords = pg_query($this->conn, $sql) or die ;
				$data = pg_fetch_all($queryRecords);
				return $data; 
				}
	}
} 
?>