<?php

	include 'dbconn.php';
	
	$response=Array();
	$response["error"]=0;
	$response["msg"]="";
		
	if (isset($_REQUEST["estimateid"])) {
		$estimateid=$_REQUEST["estimateid"];
		$sql_BOMcheckblank="select count(*) as bomlines from DUREX_EstimateBOM where EstimateId=$estimateid";
		$sql_BOMcheckzero="select count(*) as missingcost from DUREX_EstimateBOMCosts c inner join DUREX_EstimateBOM b on b.EstimateBOMId=c.EstimateBOMId where EstimateId=$estimateid and (UnitCost=0 or UnitCost is null)";
		$sql_Routingcheck="select count(*) missingtime from DUREX_EstimateRouting e where EstimateId=$estimateid and SetupTime+RunLaborTime=0";
		$sql_Routingcheckblank="select count(*) as routingsteps from DUREX_EstimateRouting where EstimateId=$estimateid";
		$sql_MISCcheck="select count(*) as missingcost from DUREX_EstimateOtherCharges where EstimateId=$estimateid and Cost=0"; 
		
		//Check for empy BOM
		foreach($db->query($sql_BOMcheckblank) as $results) { 
			if ($results["bomlines"]<1) {
				$response["error"]=2;
				$response["msg"]="BOM is blank.";
			}
		}
		
		//Check BOM for missing costs
		if ($response["error"]==0) {
			foreach($db->query($sql_BOMcheckzero) as $results) { 
				if ($results["missingcost"]!=0) {
					$response["error"]=3;
					$response["msg"]="BOM is missing costs.";
				}
			}
		}
		
		//Check for empty route
		if ($response["error"]==0) {
			foreach($db->query($sql_Routingcheckblank) as $results) { 
				if ($results["routingsteps"]<1) {
					$response["error"]=4;
					$response["msg"]="Route is blank.";
				}
			}
		}

		//Check for route missing time
		if ($response["error"]==0) {
			foreach($db->query($sql_Routingcheck) as $results) { 
				if ($results["missingtime"]!=0) {
					$response["error"]=5;
					$response["msg"]="Route is missing time.";
				}
			}
		}	

		//Check for route missing time
		if ($response["error"]==0) {
			foreach($db->query($sql_MISCcheck) as $results) { 
				if ($results["missingcost"]!=0) {
					$response["error"]=6;
					$response["msg"]="Miscellaneous is missing cost.";
				}
			}
		}		
		
	}
	else { 
		$response["error"]=1;
		$response["msg"]="No Estimate Id passed!";
	}

	print json_encode($response);
	
	
?>