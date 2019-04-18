<?php
	//print "BOMID: ";
	$bomid=$_REQUEST["bomid"];
	$sql_bomdeletecost="Delete from DUREX_EstimateBOMCosts where EstimateBOMId=$bomid";
	$sql_bomdelete="Delete from DUREX_estimateBOM where EstimateBOMId=$bomid";
	
	include 'dbconn.php';
	
	$stmt= $db->prepare($sql_bomdeletecost);
	try {
		$stmt->execute();
			
	}
	catch (Exception $e){
		throw $e;
	}

	$stmt= $db->prepare($sql_bomdelete);
	try {
		$stmt->execute();
			
	}
	catch (Exception $e){
		throw $e;
	}	
	
?>
