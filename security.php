<?php
	//Read the setting from the ini file
	include 'readini.php'; 
	
	//Begins a session
	session_start();
	//print_r($_SESSION);
	//Default SQl querys 
	$check=array();
	
	
	if (!isset($_SESSION["username"]) || $_SESSION["username"]=='' || !isset($_SESSION["lastactivity"]) || (time() - $_SESSION["lastactivity"] > $setup["timeout"])) { 
	    session_unset();     // unset $_SESSION variable for the run-time 
		session_destroy();   // destroy session data in storage
		if (isset($_REQUEST["tabcheck"])) {
			$check["security"]=1;
		}
		else {
			header( 'Location: index.php' ); //Redirct to login pages
		}
	}
	else {
		$username=$_SESSION["username"]; //Gets the user's name 
		$_SESSION["lastactivity"]=time();//Sets the last activity time for time feature
		$check["security"]=0;		
	}
	
	if (isset($_REQUEST["tabcheck"])) {
		print json_encode($check);
	}

	//var_dump($_SESSION);
	
?>
	