<?php
	
	include 'dbconn.php';
	
	if (isset($_REQUEST["qtyid"])) { 
		$qtyid=$_REQUEST["qtyid"];
	}
	else {
		$qtyid=0;
	}
	
	if (isset($_REQUEST["commission"])) { 
		$commission=$_REQUEST["commission"];
	}
	else {
		$commission=0;
	}
	
	if ($qtyid==0) {
		print "No Quantity Id was passed!";
	}
	else {
		$sql="UPDATE q SET Commission=$commission from DUREX_EstimateQuantity q where EstimateQtyId=$qtyid";

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
