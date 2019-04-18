<?php
	// security check
	include 'security.php';
	include 'dbconn.php';
	
	$notetext='';
	
	if (isset($_REQUEST["itemnumber"])) {
		$itemnum=$_REQUEST["itemnumber"];
	}
	else {
		$itemnum='';
	}
	
	if (isset($_REQUEST["quote"])) {
		$quote=$_REQUEST["quote"];
	}
	else {
		$quote='';
	}
	
	$sql_gpquotenotes="select *, CAST(CAST(NOTEINDX as INT) as BINARY(4)) as BinaryForm from SY03900 where NOTEINDX in (select NOTEINDX from SOP10100 where SOPNUMBE='$quote')";
	$sql_salesinfo="select l.[Customer Name],l.[SOP Type], l.[SOP Number], l.[Customer PO Number], l.[Item Number], a.CUSTITEMNMBR as [Customer Item Number], l.[Customer Number], l.QTY,l.[Unit Price], l.[Requested Ship Date],l.[User Category Value 1] as [Product Groups]  from SalesLineItems l
			left outer join SOP60300 a on a.ITEMNMBR=l.[Item Number] and a.CUSTNMBR=l.[Customer Number] where l.[Customer Number] in (select CUSTNMBR from SOP10100 where SOPNUMBE='$quote')";
	
	foreach($db->query($sql_gpquotenotes) as $gpnote) { 
		$notetext=$gpnote["TXTFIELD"];
	}
	
	$date=date("m-d-Y");
	
?>

<nav id="historytabs" class="nav nav-pills nav-justified" role="tablist">
	<a class="nav-item nav-link show" id="history0-tab" role="tab" data-toggle="tab" href="#history0" data-url="" data-parms="" aria-controls="history0" aria-expanded="true" aria-selected="true">Quotation Notes</a>
	<a class="nav-item nav-link" id="history1-tab" role="tab" data-toggle="tab" href="#history1" data-url="" data-parms="" aria-controls="history1" aria-selected="false">Planned VS. MO Routing</a>
	<a class="nav-item nav-link" id="history2-tab" role="tab" href="#history2" data-toggle="tab" data-url="" data-parms="" aria-controls="history2" aria-selected="false">Traveler</a>
	<a class="nav-item nav-link" id="history4-tab" role="tab" href="#history4" data-toggle="tab" href="#history4" role="tab" data-url="saleshistory.php" data-parms="quote=<?php print $quote; ?>&itemnumber=<?php print $itemnum; ?>" aria-selected="false">Sales History</a>
	<a class="nav-item nav-link" id="history3-tab" role="tab" href="#history3" data-toggle="tab" data-url="" data-parms="" aria-controls="history3" aria-selected="false">MO Cost History</a>
</nav>

<div class="tab-content" id="myHistoryContent">
	<div class="tab-pane fade" id="history0" role="tabpanel" aria-labelledby="history0-tab">
		<div style="height:100px; border: 1px solid black; padding: 10px; margin: 10px;";>
		<?php
			print "$notetext";
		?>
		</div>
	</div>
	<div class="tab-pane fade" id="history1" role="tabpanel" aria-labelledby="history1-tab"><iframe src="http://drx-vm-gpsql01/ReportServer/Pages/ReportViewer.aspx?%2fDynamics+GP+2013%2fDUREX%2fManufacturing%2fItem+Estimator+Planned+Vs+MO+Routing&rs:Command=Render&ITEM=<?php print $itemnum; ?>&rc:Parameters=Collapsed" width="100%" height="1000px"></iframe></div>
	<div class="tab-pane fade" id="history2" role="tabpanel" aria-labelledby="history2-tab"><iframe src="http://drx-vm-gpsql01/ReportServer/Pages/ReportViewer.aspx?%2fDynamics+GP+2013%2fDUREX%2fManufacturing%2fItem+Driven+Traveler%2fDurex+ITEM+BASED+Production+Traveler+with+Picklist+and+Costs&rs:Command=Render&ITEMNMBR=<?php print $itemnum; ?>&rs:Format=HTML4.0" width="100%" height="1000px"></iframe></div>
	<div class="tab-pane fade" id="history3" role="tabpanel" aria-labelledby="history3-tab"><iframe src="http://drx-vm-gpsql01/ReportServer/Pages/ReportViewer.aspx?%2fDynamics+GP+2013%2fDUREX%2fManufacturing%2fDevelopment%2fDavid%2fJCA+Lite%2fDEV%2fAccounting%2fMO+LIFE+CYCLE+-+TOTAL+DEV_COST_ACCOUNTING_BY_ITEM&rs:Command=Render&paramItemNo=<?php print $itemnum; ?>&paramWC=ALL&paramEndDateRange=<?php print $date; ?>" width="100%" height="1000px"></iframe></div>
	<div class="tab-pane fade" id="history4" role="tabpanel" aria-labelledby="history4-tab">
		<div style="text-align:center">
			<img src="images/loading.gif">
			<p>Loading...</p>
		</div>
	</div>
</div>

<script src="js/excel-bootstrap-table-filter-bundle.js"></script>
<script>
	$('#salesTable').excelTableFilter();

	$('#historytabs a').click(function (e) {
		e.preventDefault();
		timeoutcheck();
		var url = $(this).attr("data-url");
		var parms = $(this).attr("data-parms");	
		var href = this.hash;
		var pane = $(this);
		
		if (parms!='') {
			url=url+'?'+parms;
		}
		//alert('URL: '+url);
		// ajax load from data-url
		if (url!="") { 
			$(href).load(url,function(result){      
				pane.tab('show');
			});
		}
		//alert("history tab clicked!");
	});
	
	
	$("#history0-tab").click();
</script>