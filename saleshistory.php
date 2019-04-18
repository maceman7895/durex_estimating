<?php
	include 'dbconn.php';
	
	if (isset($_REQUEST["quote"])) {
		$quote=$_REQUEST["quote"];
	}
	else {
		$quote='';
	}

	if (isset($_REQUEST["itemnumber"])) {
		$itemnumber=$_REQUEST["itemnumber"];
	}
	else {
		$itemnumber='';
	}	
	
	
	$sql_salesinfo="select l.[Customer Name],l.[SOP Type], l.[SOP Number], l.[Customer PO Number], l.[Item Number], a.CUSTITEMNMBR as [Customer Item Number], l.[Customer Number], l.QTY,l.[Unit Price], format(l.[Requested Ship Date],'MM/dd/yyyy') AS [Requested Ship Date],l.[User Category Value 1] as [Product Groups]  from SalesLineItems l
		left outer join SOP60300 a on a.ITEMNMBR=l.[Item Number] and a.CUSTNMBR=l.[Customer Number] where l.[Customer Number] in (select CUSTNMBR from SOP10100 where SOPNUMBE='$quote') and l.[SOP Type] in ('Quote','Order') and l.[SOP Number]<>'$quote' and (l.[Item Number]='$itemnumber' or a.CUSTITEMNMBR='$itemnumber') ORDER BY l.[Order Date] DESC";
	
?>
	<head>
	<?php
		include "header.php";
	?>
		<link rel="stylesheet" href="css/excel-bootstrap-table-filter-style.css">
	</head>
	<body>
		<table id="salesTable" class="table">
			<thead>
				<tr class="table-info">
					<th>Customer Name</th>
					<th>SOP Type</th>
					<th>SOP Number</th>
					<th>Customer PO Number</th>
					<th>Item Number</th>
					<th>Customer Item Number</th>
					<th>Customer Number</th>
					<th>QTY</th>
					<th>Unit Price</th>
					<th>Requested Ship Date</th>
					<th>Product Groups</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($db->query($sql_salesinfo) as $sales) { ?>
				<tr>
					<td><?php print $sales["Customer Name"]; ?></td>
					<td><?php print $sales["SOP Type"]; ?></td>
					<td><?php print $sales["SOP Number"]; ?></td>
					<td><?php print $sales["Customer PO Number"]; ?></td>
					<td><?php print $sales["Item Number"]; ?></td>
					<td><?php print $sales["Customer Item Number"]; ?></td>
					<td><?php print $sales["Customer Number"]; ?></td>
					<td><?php print number_format($sales["QTY"]); ?></td>
					<td><?php print number_format($sales["Unit Price"],2); ?></td>
					<td><?php print $sales["Requested Ship Date"]; ?></td>
					<td><?php print $sales["Product Groups"]; ?></td>				
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</body>
	<?php
		include "footer.php";
	?>
	<script src="js/excel-bootstrap-table-filter-bundle.js"></script>
	<script> 
		$('#salesTable').excelTableFilter();
	</script>
