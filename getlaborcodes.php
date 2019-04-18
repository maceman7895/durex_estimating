<?php 
	//Connect to the GP database.  If error, display the error message and exit.
	include 'dbconn.php';
	
	$workcenter=""; 
	$codetype="";
	$whereclause=0;
	
	if (isset($_REQUEST["wc"])) { 
		$workcenter=$_REQUEST["wc"];
	}
	
	if (isset($_REQUEST["type"])) { 
		$codetype=$_REQUEST["type"];
	} 
	
	$sql_laborcode="select LABORCODE_I, LABCODEDESC_I, COST_I,Variable_Overhead_Amount, Fixed_Overhead_Amount from LC010014";
	
	if ($workcenter!="") {
		if ($whereclause==0) {
			$sql_laborcode=$sql_laborcode." where";
			$whereclause=1;
		}
		$sql_laborcode=$sql_laborcode."
?>
