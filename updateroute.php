<?php
	//Connect to the GP database.  If error, display the error message and exit.
	include 'dbconn.php';
	
	//print_r($_REQUEST);

	//Initializing variables
	$estimateid=0;
	$routingid=0;
	$routingseq=0;
	$routedesc='';
	$wc='';
	$setuplaborrate=0;
	$setuptime=0;
	$setuplaborcode='';
	$machineid='';
	$machinetime=0; 
	$machinerate=0;
	$runlaborcode='';
	$runlaborrate=0;
	$runlabortime=0;
	$cycletime=0;
	$movetime=0;
	$queuetime=0;
	$mfgohrate=0;
	$sgaohrate=0;
	$note='';
	$parentid=0;
	
	//Set the values passed
	if (isset($_REQUEST["estimateid"])) { 
		$estimateid=$_REQUEST["estimateid"];
	}
	
	if (isset($_REQUEST["routingid"])) { 
		$routingid=$_REQUEST["routingid"];
	}

	if (isset($_REQUEST["routedesc"])) { 
		$routedesc=$_REQUEST["routedesc"];
	}	
	
	if (isset($_REQUEST["routingseq"])) { 
		$routingseq=$_REQUEST["routingseq"];
	}
	
	if (isset($_REQUEST["wc"])) { 
		$wc=$_REQUEST["wc"];
	}	
	
	if (isset($_REQUEST["setuplaborcode"])) { 
		$setuplaborrate=$_REQUEST["setuplaborcode"];
	}

	if (isset($_REQUEST["setuplaborrate"])) { 
		$setuplaborrate=$_REQUEST["setuplaborrate"];
	}
	
	if (isset($_REQUEST["setuptime"])) { 
		$setuptime=$_REQUEST["setuptime"];
	}

	if (isset($_REQUEST["machineid"])) { 
		$machineid=$_REQUEST["machineid"];
	}
	
	if (isset($_REQUEST["machinetime"])) { 
		$machinetime=$_REQUEST["machinetime"];
	}		

	if (isset($_REQUEST["machinerate"])) { 
		$machinerate=$_REQUEST["machinerate"];
	}
	
	if (isset($_REQUEST["runlaborrate"])) { 
		$runlaborrate=$_REQUEST["runlaborrate"];
	}

	if (isset($_REQUEST["runlabortime"])) { 
		$runlabortime=$_REQUEST["runlabortime"];
	}
	
	if (isset($_REQUEST["laborcode"])) { 
		$runlaborcode=$_REQUEST["laborcode"];
	}	

	if (isset($_REQUEST["cycletime"])) { 
		$cycletime=$_REQUEST["cycletime"];
	}
	
	if (isset($_REQUEST["movetime"])) { 
		$movetime=$_REQUEST["movetime"];
	}

	if (isset($_REQUEST["queuetime"])) { 
		$queuetime=$_REQUEST["queuetime"];
	}
	
	if (isset($_REQUEST["mfgohrate"])) { 
		$mfgohrate=$_REQUEST["mfgohrate"];
	}

	if (isset($_REQUEST["sgaohrate"])) { 
		$sgaohrate=$_REQUEST["sgaohrate"];
	}

	if (isset($_REQUEST["note"])) { 
		$note=$_REQUEST["note"];
	}	
	
	if (isset($_REQUEST["parentid"])) { 
		$parentid=$_REQUEST["parentid"];
	}	
	
	
	//Get the correct sql statememnt
	if ($routingid==0) {
		$sql="INSERT INTO DUREX_EstimateRouting (EstimateId, RoutingSequence, RoutingDescription, WorkCenterId, SetupLaborCode, SetupLaborRate, SetupTime, MachineId, MachineTime, MachineRate, RunLaborCode, RunLaborRate, RunLaborTime,CycleTime, MoveTime, QueueTime, MFGOHRate, SGAOHRate, Notes, Subparentid) VALUES ($estimateid, $routingseq, '$routedesc', '$wc', '$setuplaborcode', $setuplaborrate, $setuptime, '$machineid', $machinetime, $machinerate, '$runlaborcode', $runlaborrate, $runlabortime, $cycletime, $movetime, $queuetime, $mfgohrate, $sgaohrate, '$note', $parentid)";
	}
	else {
		$sql="UPDATE r SET EstimateId=$estimateid, RoutingSequence=$routingseq, RoutingDescription='$routedesc', WorkCenterId='$wc', SetupLaborCode='$setuplaborcode', SetupLaborRate=$setuplaborrate, SetupTime=$setuptime, MachineId='$machineid', MachineTime=$machinetime, MachineRate=$machinerate, RunLaborCode='$runlaborcode', RunLaborRate=$runlaborrate, RunLaborTime=$runlabortime, CycleTime=$cycletime, MoveTime=$movetime , QueueTime=$queuetime, MFGOHRate=$mfgohrate, SGAOHRate=$sgaohrate, Notes='$note' from DUREX_EstimateRouting r where RoutingId=$routingid";
	}

	echo $sql;
	$stmt= $db->prepare($sql);

	try {
		$stmt->execute();
	}
	catch (Exception $e){
		throw $e;
	}	

?>
