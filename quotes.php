<!doctype html>
<html lang="en">
<?php
	// security check
	include 'security.php';
	
   include "dbconn.php";
   $sql_quotes="select q.SOPNUMBE, e.EstimateId, q.CUSTNAME, e.ITEMNMBR, 
						case when e.ApprovedBy is null and e.ReadyForApproval=0 then 'Estimate not complete' 
							when e.ApprovedBy is null and e.ReadyForApproval=1 then 'Waiting for approval' 
							when e.ApprovedBy<>'' then 'Approved' end as EstimateStatus  
					from DUREX_Estimates e inner join SOP10100 q on q.SOPNUMBE=e.SOPNUMBE 
					order by q.SOPNUMBE, e.EstimateId";
?>

	<head>
<?php
	include "header.php";
?>
		<link rel="stylesheet" href="css/excel-bootstrap-table-filter-style.css">
		<title>Durex Quote & Estimating</title>	
	</head>
	<body>

		<?php
			include "navbar.php";
		?>
		<div style="padding-left: 10px;"><h2>Quotes</h2></div>
		<table id="quoteTable" class="table"> 
			<thead>
				<tr style="background-color: #f9d622;">
					<th>Quote #</th>
					<th>Estimate #</th>
					<th>Customer</th>
					<th>Item Number</th>
					<th>Estimate Status</th>
				</tr>
			</thead>
			
		<?php
			foreach($db->query($sql_quotes) as $quote) {
		?>
			<tr>
				<td><?php print $quote["SOPNUMBE"]; ?></td>
				<td><?php print $quote["EstimateId"]; ?></td>
				<td><?php print $quote["CUSTNAME"]; ?></td>
				<td><?php print $quote["ITEMNMBR"]; ?></td>	
				<td><?php print $quote["EstimateStatus"]; ?></td>
			</tr>
		<?php
			}
		?>
		</table>
	</body>
<?php
	include "footer.php";
?>
	<script src="js/excel-bootstrap-table-filter-bundle.js"></script>
	<script> 
		$('#quoteTable').excelTableFilter();
		//setTimeout(function () { location.reload(); }, 10000);
		
	</script>
