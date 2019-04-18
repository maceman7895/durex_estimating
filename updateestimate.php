<?php
	// security check
	include 'security.php';
	//Default SQl querys 
	//print_r($_POST);
	//Connect to the GP database.  If error, display the error message and exit.
	include 'dbconn.php';
	
	$estimateid="";
	$contact="";
	$phone="";
	$commission="";
	$designlead="";
	$prodlead="";	
	$estpart="";
	$estdesc="";
	$approvalby="";
	$drawingreq="";
	$pqa="";
	$readyapproval="";
	$estimator="";
	$mocharge=0;
	$nrecharge=0; 
	$nrechargetype='';
	
	if (isset($_POST["estimateid"])) {
		$estimateid=$_POST["estimateid"];
		$contact=$_POST["contact"];
		$phone=$_POST["phone"];
		$commission=$_POST["commission"];
		$designlead=$_POST["designlead"];
		$prodlead=$_POST["prodlead"];	
		//$estpart=$_POST["estpart"];
		//$estdesc=$_POST["estdesc"];
		$approvalby=$_POST["approvalby"];
		$drawingreq=$_POST["drawingreq"];
		$pqa=$_POST["pqa"];
		$readyapproval=$_POST["readyapproval"];
		$estimator=$_POST["estimator"];
		$mocharge=$_POST["mocharge"];
		$nrecharge=$_POST["nrecharge"];
		$mocharge=$_POST["mocharge"];	
		$nrecharge=$_POST["nrecharge"];
		$nrechargetype=$_POST["nrechargetype"];
	}
	
	$sql_update="Update e SET Commission='$commission', Contact='$contact', ContactPhone='$phone', LeadTimeDesign='$designlead', LeadtimeProduction='$prodlead', ApprovalBy='$approvalby', ApprovedDrawings=$drawingreq, PQA=$pqa, ReadyForApproval=$readyapproval, Estimator='$estimator', MOCharge=$mocharge, NRECharge=$nrecharge, NREChargeType='$nrechargetype' from DUREX_Estimates e where estimateid=$estimateid";
				  
	echo $sql_update;
	$stmt=$db->prepare($sql_update);

	try {
		$stmt->execute();
	}
	catch (Exception $e){
		throw $e;
	}
?>