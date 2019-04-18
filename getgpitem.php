<?php

	//Default SQl querys 
	//print_r($_POST);
	//Connect to the GP database.  If error, display the error message and exit.
	include 'dbconn.php';
	$itemnum=$_POST["itemnumber"];
	$sql="select ITEMNMBR,ITEMDESC, STNDCOST as Cost, UOMSCHDL as UofM from IV00101 where ITEMNMBR='$itemnum'";
	$json = array();
	
	foreach($db->query($sql) as $item) {
		$json=$item;
	}
		
	print json_encode($json);
	
?>
