<?php 
	//print "cost type: $costtype <br>";
	//Connect to the GP database.  If error, display the error message and exit.
	include 'dbconn.php';

	//Default SQl querys 
	if (isset($_REQUEST["estimateid"])) {
		$estimateid=$_REQUEST["estimateid"];
	}
	else {
		$estimateid=0;
	}
	
	if (isset($_REQUEST["costtype"])) {
		$costtype=$_REQUEST["costtype"];
	}
	else {
		$costtype='';
	}

	if (isset($_REQUEST["otherchargeid"])) {
		$otherchargeid=$_REQUEST["otherchargeid"];
	}
	else {
		$otherchargeid=0;
	} 
	
	if (isset($_REQUEST["otherdescription"])) {
		$otherdesc=$_REQUEST["otherdescription"];
	}
	else {
		$otherdesc='';
	} 

	if (isset($_REQUEST["amount"])) {
		$othercost=$_REQUEST["amount"];
	}
	else {
		$othercost=0;
	} 

	if (isset($_REQUEST["otherchargetype"])) {
		$otherchargetype=$_REQUEST["otherchargetype"];
	}
	else {
		$otherchargetype='';
	} 

	if (isset($_REQUEST["note"])) {
		$othernote=$_REQUEST["note"];
	}
	else {
		$othernote='';
	} 
	
	print_r($_REQUEST);
	
	if ($estimateid!=0 || $costtype!='') {
		if ($otherchargeid!=0) {
			$sql="UPDATE oc SET OtherDescription='$otherdesc', Cost=$othercost, OtherChargeType='$otherchargetype', Note='$othernote', CostType='$costtype' FROM DUREX_EstimateOtherCharges oc where OtherChargeId=$otherchargeid";
		}
		else { 
			$sql="INSERT INTO DUREX_EstimateOtherCharges (EstimateId, OtherDescription, Cost, OtherChargeType, Note, CostType) VALUES ( $estimateid, '$otherdesc', $othercost, '$otherchargetype', '$othernote', '$costtype')";
		}
		print "SQL: $sql";
		$stmt= $db->prepare($sql);

		try {
			$stmt->execute();
		}
		catch (Exception $e){
			throw $e;
		}
	}
	else { 
		print "Error: record can't be found or estimate is missing.";
	}



?>