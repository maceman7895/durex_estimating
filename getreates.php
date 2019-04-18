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
		$laborcode=$_REQUEST["setupcode"];
	}
	
	$sql_laborrate="select LABORCODE_I, LABCODEDESC_I, COST_I,Variable_Overhead_Amount, Fixed_Overhead_Amount from LC010014 where LABORCODE_I='$laborcode'";
	$sql_setuprate="select LABORCODE_I, LABCODEDESC_I, COST_I,Variable_Overhead_Amount, Fixed_Overhead_Amount from LC010014 where LABORCODE_I='$setupcode'";
	
;
	
	foreach($db->query($sql_laborrate) as $rate) {
		$laborrate=$rate["COST_I"];
		$mfgohrate=$rate["Fixed_Overhead_Amount"];
		$sgaohrate=$rate["Variable_Overhead_Amount"];
	}
	foreach($db->query($sql_setuprate) as $rate) {
		$setuprate=$labor["COST_I"];
		$smfgohrate=$labor["Fixed_Overhead_Amount"];
		$ssgaohrate=$labor["Variable_Overhead_Amount"];
	}	
	$json=Array("labor"=>$laborrate, "setup"=>$setuprate, "mfgoh"=>$mfgohrate, "sgaoh"=$sgaohrate, "smfgoh"=>$smfgohrate, "ssgaoh"=>$ssgaohrate];
	
	echo json_encode($json);
?>