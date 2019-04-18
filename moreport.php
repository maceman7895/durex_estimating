<?php 
	//Connect to the GP database.  If error, display the error message and exit.
	include 'dbconn.php';
	
	//Set passed parameters
	if (isset($_REQUEST["itemnumber"])) {
		$itemnum=$_REQUEST["itemnumber"];
	}
	else {
		$itemnum=0;
	}
	
	$wclist="";
	//Query for work centers
	$sql_workcenters="select WCID_I, WCDESC_I from WC010931 where WCDESC_I not like '%DO NOT USE%'";
	
	foreach($db->query($sql_workcenters) as $wc) { 
		if ($wclist=="") { 
			$wclist=$wc["WCID_I"];
		}
		else { 
			$wclist=$wclist.",".$wc["WCID_I"];
		}
	}
	
	$date=date("m-d-Y");
?>
<iframe src="http://drx-vm-gpsql01/ReportServer/Pages/ReportViewer.aspx?%2fDynamics+GP+2013%2fDUREX%2fManufacturing%2fDevelopment%2fDavid%2fJCA+Lite%2fDEV%2fAccounting%2fMO+LIFE+CYCLE+-+TOTAL+DEV_COST_ACCOUNTING_BY_ITEM&rs:Command=Render&paramItemNo=<?php print $itemnum; ?>&paramWC=WC121,WC211&paramEndDateRange=<?php print $date; ?>" width="100%" height="1000px"></iframe>