<?php

	//Default SQl querys 
	//print_r($_POST);
	//Connect to the GP database.  If error, display the error message and exit.

	include 'dbconn.php';
	
	$estimateid='';
	$itemnumber='';
	
	if (isset($_REQUEST["estimateid"])) { $estimateid=$_REQUEST['estimateid']; }
	if (isset($_REQUEST["itemnumber"])) { $itemnumber=$_REQUEST['itemnumber']; }
	
	$sql_copybom="exec sp_DUREX_CopyBom $estimateid, '$itemnumber'";
	
	//print "SQL: $sql_copybom";
	$stmt=$db->prepare($sql_copybom);

	try {
		$stmt->execute();
	}
	catch (Exception $e){
		throw $e;
	}
	
?>