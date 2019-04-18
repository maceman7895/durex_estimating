<?php
	//Connect to the GP database.  If error, display the error message and exit.
	include 'dbconn.php';
	
	$response=array();
	
	$subtypeid=0;
	$rate=0;
	$engrate=0;
	$update='UNKOWN';
	$fieldname='';
	
	//print_r($_REQUEST);
	if (isset($_REQUEST["subtypeid"])) {
		$subtypeid=$_REQUEST["subtypeid"];
	}
	
	if (isset($_REQUEST["oprate"])) {
		$rate=$_REQUEST["oprate"];
		$update="order process charge";
		$fieldname='DefaultOrderProcessing';
	}
	
	if (isset($_REQUEST["engrate"])) {
		$rate=$_REQUEST["engrate"];
		$update="engineering rate";
		$fieldname='EngineeringRate';		
	}
	
	
	
	//Get the correct sql statememnt
	$sql="update s SET $fieldname=$rate from DUREX_EstimateProductSubtype s where ProductSubTypeId=$subtypeid";
	
	$sql_get="select *, 
		(select ProductTypeCode from DUREX_EstimateProductType where EstimateProductTypeId=s.EstimateProductTypeId) as ProductGroup, 
		(select UserCatLongDescr from IV40600 where USCATVAL=s.ProductSubType and USCATNUM=2) as ProductSubgroupDescription
	from DUREX_EstimateProductSubtype s where ProductSubTypeId=$subtypeid";


	//echo $sql;
	$stmt= $db->prepare($sql);

	try {
		$stmt->execute();
		$response["status"]="ok";
		foreach($db->query($sql_get) as $r) {
			$response["msg"]=$r["ProductSubType"]." $update has been set to ".number_format($r["DefaultOrderProcessing"],2);
		}
	}
	catch (Exception $e){
		$response["status"]="error";
		$response["msg"]="Something went wrong updating the $update rate.";
		throw $e;
	}	
	
	echo json_encode($response);

	
?>
