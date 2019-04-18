<?php
	// security check
	include 'security.php';
	include 'dbconn.php';
	
	if (isset($_REQUEST["qtyid"])) { 
		$qtyid=$_REQUEST["qtyid"];
	}
	else {
		$qtyid=0;
	}
	
	if (isset($_REQUEST["price"])) { 
		$price=$_REQUEST["price"];
	}
	else {
		$price=0;
	}
	
	if ($qtyid==0) {
		print "No Quantity Id was passed!";
	}
	else {
		$sql="UPDATE q SET ApprovedSellPrice=$price from DUREX_EstimateQuantity q where EstimateQtyId=$qtyid";

		echo $sql;
		$stmt= $db->prepare($sql);

		try {
			$stmt->execute();
		}
		catch (Exception $e){
			throw $e;
		}	
	}
?>
