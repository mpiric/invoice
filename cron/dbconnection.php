<?php

class Connection {
	public $conn;
	
	// constructor
    function __construct() {
			
		
		$servername = "localhost";
		$username = "janak321";
		$password = "uteredy9e";
		$database = "zadmin_rst";

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