<?php
	//Connect to the GP database.  If error, display the error message and exit.
	include 'dbconn.php';
	$laborcode="";
	$setupcode="";
	
	$laborrate=0;
	$mfgohrate=0;
	$sgaohrate=0;
	$setuprate=0;
	$smfgohrate=0;
	$ssgaohrate=0;
	
	
	if (isset($_REQUEST["laborcode"])) {
		$laborcode=$_REQUEST["laborcode"];
	}
	
	if (isset($_REQUEST["setupcode"])) {
		$setupcode=$_REQUEST["setupcode"];
	}
	
	$sql_laborrate="select LABORCODE_I, LABCODEDESC_I, cast(COST_I as decimal(18,2)) as rate,cast(Variable_Overhead_Amount as decimal(18,2)) as sga, cast(Fixed_Overhead_Amount as decimal(18,2)) as mfg from LC010014 where LABORCODE_I='$laborcode'";
	$sql_setuprate="select LABORCODE_I, LABCODEDESC_I, cast(COST_I as decimal(18,2)) as rate,cast(Variable_Overhead_Amount as decimal(18,2)) as sga, cast(Fixed_Overhead_Amount as decimal(18,2)) as mfg from LC010014 where LABORCODE_I='$setupcode'";
	
	//print "SQL: $sql_laborrate<br>";
	//print "SQL: $sql_setuprate<br>";
	
	foreach($db->query($sql_laborrate) as $rate) {
		$laborrate=$rate["rate"];
		$mfgohrate=$rate["mfg"];
		$sgaohrate=$rate["sga"];
	}
	
	foreach($db->query($sql_setuprate) as $rate) {
		$setuprate=$rate["rate"];
		$smfgohrate=$rate["mfg"];
		$ssgaohrate=$rate["sga"];
	}
	
	$json=array("test"=>0, "labor"=>$laborrate, "setup"=>$setuprate, "mfgoh"=>$mfgohrate, "sgaoh"=>$sgaohrate, "smfgoh"=>$smfgohrate, "ssgaoh"=>$ssgaohrate);
	
	echo json_encode($json);
?>