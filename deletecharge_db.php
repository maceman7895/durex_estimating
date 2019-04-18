<?php
	//print "BOMID: ";
	$chargeid=$_REQUEST["chargeid"];
	
	$sql_othercharge="Delete from DUREX_EstimateOtherCharges where OtherChargeId=$chargeid";
	
	include 'dbconn.php';
	
	print "SQL: $sql_othercharge";
	
	$stmt= $db->prepare($sql_othercharge);
	try {
		$stmt->execute();
			
	}
	catch (Exception $e){
		throw $e;
	}

	
	
?>