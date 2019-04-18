<!doctype html>
<html lang="en">
<?php
	// security check
	include 'security.php';
	//Default SQl querys 
	$prodgroups=array("Cast Heaters", "Cartridge Heaters", "Straight Tubular Heaters", "Formed Tubular Heaters");
	$sql_prodgroups="select * from IV40600 where USCATNUM=1 and USCATVAL not in( select ProductTypeCode from [DUREX_Estimate ProductType])";  //Query for the product groups from GP
	$sql_estimators="select USERID, USERNAME from DYNAMICS..SY01400 where UserStatus=1 order by USERNAME";  //Query for getting the mask for a product group
	$sql_estimates="select EstimateId,(select CUSTNAME from SOP10100 where SOPNUMBE=e.SOPNUMBE) as Customer, SOPNUMBE as QuoteNumber,ITEMNMBR, CASE when e.ApprovedBy is null then 'No' else 'Yes' end as Approval, (select ApproverId from DUREX_EstimateProductType where ProductTypeCode=e.ProductType) as Approver  from DUREX_Estimates e  where ReadyForApproval=1 order by EstimateId";
	
	//Connect to the GP database.  If error, display the error message and exit.
	include 'dbconn.php';
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
			//print $sql_estimates;
		?>
		<div style="padding-left: 10px;"><h2>Approvals</h2></div>
		<table id="estimatesTable" class="table">
			<thead>
				<tr class="table-info">
					<th style="border-right: 1px ridge #636161">Estimate #</th>
					<th style="border-right: 1px ridge #636161">Customer</th>
					<th style="border-right: 1px ridge #636161">Quote</th>
					<th style="border-right: 1px ridge #636161">Item Number</th>
					<th style="border-right: 1px ridge #636161">Approver</th>
					<th style="border-right: 1px ridge #636161">Approval</th>
				</tr>
			</thead>
			<?php 
				foreach ($db->query($sql_estimates) as $estimate) {
			?>
			<tr>
				<td style="column-width:200px;"><a href="approveestimate.php?estimate=<?php print $estimate["EstimateId"]; ?>"><?php print $estimate["EstimateId"]; ?></a></td>
				<td style="column-width:300px;"><?php print $estimate["Customer"]; ?></td>
				<td style="column-width:150px;"><?php print $estimate["QuoteNumber"]; ?></td>
				<td style="column-width:300px;"><?php print $estimate["ITEMNMBR"]; ?></td>
				<td style="column-width:300px;"><?php print $estimate["Approver"]; ?></td>					
				<td style="column-width:150px;"><?php print $estimate["Approval"]; ?></td>
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
		$('#estimatesTable').excelTableFilter();
		//setTimeout(function () { location.reload(); }, 10000);
		
	</script>

