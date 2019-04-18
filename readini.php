<?php

	$ini_array = parse_ini_file("estimating.ini", true);
	//print_r($ini_array);
	
	$server=$ini_array["gpdatabase"]["dbserver"];
	$company=$ini_array["gpdatabase"]["companydb"];
	$security=$ini_array["security"];
	$setup=$ini_array["setup"];
	
	
?>
