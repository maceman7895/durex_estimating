<?php
	$ini_array = parse_ini_file("estimating.ini", true);
	//print_r($ini_array);
	
	$server=$ini_array["gpdatabase"]["dbserver"];
	$company=$ini_array["gpdatabase"]["companydb"];
	
	
	//print "<br>".$ini_array["gpdatabase"]["companydb"];
	$estimating_conn="odbc:DRIVER={SQL Server};Server=".$ini_array["estimatingdb"]["dbserver"].";Database=".$ini_array["estimatingdb"]["database"].";UID=".$ini_array["estimatingdb"]["user"].";PWD=".$ini_array["estimatingdb"]["pwd"];
	$gpcompany_conn="odbc:DRIVER={SQL Server};Server=".$ini_array["gpdatabase"]["dbserver"].";Database=".$ini_array["gpdatabase"]["companydb"].";UID=".$ini_array["gpdatabase"]["user"].";PWD=".$ini_array["gpdatabase"]["pwd"];
	$gpdynamics_conn="odbc:DRIVER={SQL Server};Server=".$ini_array["gpdatabase"]["dbserver"].";Database=".$ini_array["gpdatabase"]["dynamicsdb"].";UID=".$ini_array["gpdatabase"]["user"].";PWD=".$ini_array["gpdatabase"]["pwd"];
	
	//print "<br> $estimating_conn";
	//print "<br> $gpcompany_conn";
	//print "<br> $gpdynamics_conn";
	
	try {
		$db=new PDO("odbc:DRIVER={SQL Server};Server=DRX-VM-GPSQL01;Database=DTEST;UID=sa;PWD=Access123");
	}
	catch ( PDOException $e) {
		print $e->getMessage();
		exit;
	}
	
	try {
		$estimatingdb=new PDO($estimating_conn);
	}
	catch ( PDOException $e) {
		print $e->getMessage();
		exit;
	}

	try {
		$gpcompanydb=new PDO($gpcompany_conn);
	}
	catch ( PDOException $e) {
		print $e->getMessage();
		exit;
	}	

	
	
?>	