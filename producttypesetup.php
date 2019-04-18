<!doctype html>
<html lang="en">
<?php
	//Default SQl querys 
	$sql_prodtypes="select * from DUREX_EstimateProductType";
	
	//Connect to the GP database.  If error, display the error message and exit.
	include 'dbconn.php';
?>

	<head>
<?php include "header.php"; ?>
		<title>Durex Quote & Estimating</title>	
	</head>
	<body>

		<?php
			include "navbar.php";
		?>
		<div style="padding-left: 10px;"><h2>Product Type Setup</h2></div>

		<table class="table table-striped" style="margin-top: 5px;width:1440px;background-color:#ffffff;">
			<tr>
				<th style="border-right: 1px ridge #636161"> <img src="/images/add.png" alt="delete" width="25" height="25" data-toggle="tooltip" data-placement="top" title="Add a product type"></th>
				<th style="column-width:350px; border-right: 1px ridge #636161">Product Type</th>
				<th style="column-width:300px;border-right: 1px ridge #636161">Estimator</th>
				<th style="column-width:350px;border-right: 1px ridge #636161">Estimator Email</th>
				<th style="column-width:300px;border-right: 1px ridge #636161">Approver Id</th>
				<th style="column-width:324px;border-right: 1px ridge #636161">Approver Email</th>
				<th style="column-width:200px;border-right: 1px ridge #636161">Material Adder (%)</th>
				<th style="column-width:200px;border-right: 1px ridge #636161">Markup (%)</th> 
				<th style="column-width:25px;"></th>
			</tr>
		<?php
			foreach($db->query($sql_prodtypes) as $prodtype) {
		?>
			<tr>
				<td><img src="/images/edit.png" alt="edit" width="25" height="25" data-toggle="tooltip" data-placement="top" title="Edit"></td>				
				<td style="column-width:350px;"><?php print $prodtype["ProductTypeCode"]; ?></td>
				<td style="column-width:300px;" ><?php print $prodtype["EstimatorId"]; ?></td>
				<td style="column-width:350px;"><?php print $prodtype["EstimatorEmail"]; ?></td>
				<td style="column-width:300px;"><?php print $prodtype["ApproverId"]; ?></td>
				<td style="column-width:324px;"><?php print $prodtype["ApproverEmail"]; ?></td>
				<td style="column-width:200px;"><?php print number_format($prodtype["AdderPercentage"],2); ?></td>
				<td style="column-width:200px;"><?php print number_format($prodtype["Markup"],2); ?></td>
				<td><img src="/images/delete.png" alt="delete" width="25" height="25" data-toggle="tooltip" data-placement="top" title="Delete"></td>		
			</tr>
		<?php
			}
		?>
		</table>
	</body>
<?php include "footr.php"; ?>