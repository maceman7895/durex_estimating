<?php
	//Connect to the GP database.  If error, display the error message and exit.
	
	try {
		$db=new PDO("odbc:DRIVER={SQL Server};Server=DRX-VM-GPSQL01;Database=DTEST;UID=sa;PWD=Access123;Client_CSet=UTF-8");
	}
	catch ( PDOException $e) {
		print $e->getMessage();
		exit;
	}
	
	
	$search='';
	$page=0;
	$row=1000;
	
	if (isset($_REQUEST["search"])) { 
		$search=$_REQUEST["search"];
	}
	if (isset($_REQUEST["page"])) { 
		$page=$_REQUEST["page"];
	}
	
	$offset=$row*$page;
	
	if ($search!='') { 
		$sql_items="select RTRIM(ITEMNMBR) as id,  RTRIM(ITEMNMBR)+' : '+RTRIM(CONVERT(nvarchar(max), ITEMDESC)) as text, RTRIM(ITEMNMBR) as itemnumber,RTRIM(CONVERT(nvarchar(max), ITEMDESC)) as itemdesc  from IV00101 where ITEMNMBR like '$search%' order by ITEMNMBR OFFSET $offset ROWS FETCH NEXT $row ROWS ONLY ";	
	}
	else {
		$sql_items="select RTRIM(ITEMNMBR) as id,  RTRIM(ITEMNMBR)+' : '+RTRIM(CONVERT(nvarchar(max), ITEMDESC)) as text, RTRIM(ITEMNMBR) as itemnumber,RTRIM(CONVERT(nvarchar(max), ITEMDESC)) as itemdesc from IV00101 order by ITEMNMBR OFFSET $offset ROWS FETCH NEXT $row ROWS ONLY";	
	}


	//print "SQL:".$sql_items;
	$json=array();
	
	foreach ($db->query($sql_items) as $row) {
		 $json["results"][] = array("id"=>$row["id"], "text"=>utf8_encode($row["text"]), "itemnumber"=>$row["itemnumber"], "itemdesc"=>utf8_encode($row["itemdesc"]));	
	}
	
	$json["pagination"]["more"]=true;
	
	echo str_replace("null",'" "',json_encode($json, JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_APOS | JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR ));
	
?>