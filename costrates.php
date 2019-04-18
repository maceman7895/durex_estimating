<!doctype html>
<html lang="en">
<?php
	include 'security.php';
	include 'dbconn.php';
	
	$sql_subgroups="select *, 
		(select ProductTypeCode from DUREX_EstimateProductType where EstimateProductTypeId=s.EstimateProductTypeId) as ProductGroup, 
		(select UserCatLongDescr from IV40600 where USCATVAL=s.ProductSubType and USCATNUM=2) as ProductSubgroupDescription
	from DUREX_EstimateProductSubtype s
	Order by s.ProductSubType";	
	
?>

	<head>
		<?php include 'header.php'; ?>
		<title>Durex Quote & Estimating</title>	
	</head>
	<body>
		<?php
			include "navbar.php";
		?>
		<div style="padding-left: 10px;"><h2>Product Subgroup Table</h2></div>
		<table id="subgroupTable" class="table">
			<thead>
				<tr class="table-info">
					<th style="border-right: 1px ridge #636161">Subgroup Code</th>
					<th style="border-right: 1px ridge #636161">Description</th>
					<th style="border-right: 1px ridge #636161">Product Group</th>
					<th style="border-right: 1px ridge #636161">Default Order<br>Charge Rate</th>
				</tr>
			</thead>
			<tbody>
		<?php
			foreach($db->query($sql_subgroups) as $subgroup) {
		?>
				<tr>
					<td><?php print $subgroup["ProductSubType"] ?></td>
					<td><?php print $subgroup["ProductSubgroupDescription"] ?></td>
					<td><?php print $subgroup["ProductGroup"] ?></td>
					<td><input type="number" id="defaultOrderProcessing" value="<?php print number_format($subgroup["DefaultOrderProcessing"],2); ?>">
				</tr>
			</tbody>
		</table>
	</body>
	<?php include 'footer.php'; ?>
	<script src="js/excel-bootstrap-table-filter-bundle.js"></script>
	<script> 
		$('#subgroupTable').excelTableFilter();
	</script>