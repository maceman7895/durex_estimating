<?php
	//print "BOMID: ";
	$routingid=$_REQUEST["routingid"];
	
	$sql_routing="Delete from DUREX_estimateRouting where RoutingId=$routingid";
	
	include 'dbconn.php';
	
	$stmt= $db->prepare($sql_routing);
	try {
		$stmt->execute();
			
	}
	catch (Exception $e){
		throw $e;
	}

	$stmt= $db->prepare($sql_routing);
	try {
		$stmt->execute();
			
	}
	catch (Exception $e){
		throw $e;
	}	
	
?>