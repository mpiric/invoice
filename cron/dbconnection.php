<?php

class Connection {
	public $conn;
	
	// constructor
    function __construct() {
			
		
		$servername = "sankalpdb.cnbrifrfy4wn.ap-south-1.rds.amazonaws.com";
		$username = "masterdb";
		$password = "masterdb";
		$database = "sankalpdb";

		try 
		{
		    //$conn = new PDO("mysql:host=$servername;dbname=cpa", $username, $password);
		    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
		    // set the PDO error mode to exception
		    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    //echo "Connected successfully"; 
	    }
		catch(PDOException $e)
	    {
	    	echo "Connection failed: " . $e->getMessage();
	    }
		
		$this->conn = $conn;
    }

}


?>