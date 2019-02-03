<?php 
  /* Functions for redis operations starts here   */ 

  $redisObj = new Redis(); 

  function openRedisConnection( $hostName, $port){ 
  	global $redisObj; 
	// Opening a redis connection
  	$redisObj->connect( $hostName, $port );
  	return $redisObj; 
  } 

  function setValueWithTtl( $key, $value, $ttl ){ 

  	try{ 
  		global $redisObj; 
		// setting the value in redis
  		$redisObj->setex( $key, $ttl, $value );
  	}catch( Exception $e ){ 
  		echo $e->getMessage(); 
  	} 
  } 

  function getValueFromKey( $key ){ 
  	try{ 
  		global $redisObj; 
		// getting the value from redis
  		return $redisObj->get( $key);
  	}catch( Exception $e ){ 
  		echo $e->getMessage(); 
  	} 
  } 

  function deleteValueFromKey( $key ){ 
  	try{ 
  		global $redisObj; 
		// deleting the value from redis
  		$redisObj->del( $key);
  	}catch( Exception $e ){ 
  		echo $e->getMessage(); 
  	} 
  } 

   /* Functions for converting sql result  object to array goes below  */ 

  function convertToArray( $result ){ 
  	$resultArray = array(); 

  	for( $count=0; $row = $result->fetch_assoc(); $count++ ) { 
  		$resultArray[$count] = $row; 
  	} 

  	return $resultArray; 
  } 

   /* Functions for executing the Psql query goes below   */ 
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
          

  // Calling function to execute sql query
  $arrValues = executeQuery( $sql );

  // Making json string
  $jsonValue = json_encode($arrValues);

  // Opening a redis connection
  openRedisConnection( 'redis-service', 6379 );

  // Inserting the value with ttl =  1 hours
  setValueWithTtl( 'somekey1', $jsonValue, 3600);

  // Fetching value from redis using the key. 
  $val = getValueFromKey( 'somekey1' ); 

  //  Output:  the json encoded array from redis 
  echo $val;

  // Unsetting value from redis
  deleteValueFromKey( $key );

        $queryRecords = pg_query($this->conn, $sql) or die ;
        $data = pg_fetch_all($queryRecords);
        return $data; 
        }
  }
} 
?>