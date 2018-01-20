<?php


class Connection {
	public $conn;
	
	// constructor
    function __construct() {
			
		$servername = "localhost";
		$username = "root"; //admin_admin
		$password = ""; //circleof1312

		try 
		{
		    $conn = new PDO("mysql:host=$servername;dbname=rst", $username, $password);
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