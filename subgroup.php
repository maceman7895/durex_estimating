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
		<link rel="stylesheet" href="css/excel-bootstrap-table-filter-style.css">
		<title>Durex Quote & Estimating</title>	
	</head>
	<body>
		<?php
			include "navbar.php";
		?>
		<div style="padding-left: 10px;"><h2>Product Subgroup Table</h2></div>
		<div class="alert alert-success alert-dismissible fade collapse">
		  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		  <strong><span id="status">Success!</span></strong> <span id="msg">You have been signed in successfully!</p>
		</div>
		<table id="subgroupTable" class="table">
			<thead>
				<tr class="table-info">
					<th style="border-right: 1px ridge #636161">Subgroup Code</th>
					<th style="border-right: 1px ridge #636161">Description</th>
					<th style="border-right: 1px ridge #636161">Product Group</th>
					<th style="border-right: 1px ridge #636161">Default Order Processing Charge</th>
					<th style="border-right: 1px ridge #636161">Default Engineering Rate</th>
				</tr>
			</thead>
			<tbody>
		<?php
			foreach($db->query($sql_subgroups) as $subgroup) {
		?>
				<tr>
					<td><?php print $subgroup["ProductSubType"]; ?></td>
					<td><?php print $subgroup["ProductSubgroupDescription"]; ?></td>
					<td><?php print $subgroup["ProductGroup"]; ?></td>
					<td><input style="text-align: right" type="number" id="OPrate<?php print $subgroup["ProductSubTypeId"]; ?>" value="<?php print number_format($subgroup["DefaultOrderProcessing"],2); ?>" onchange="updateOPcharge(<?php print $subgroup["ProductSubTypeId"]; ?>);">
					<td><input style="text-align: right" type="number" id="ENGrate<?php print $subgroup["ProductSubTypeId"]; ?>" value="<?php print number_format($subgroup["EngineeringRate"],2); ?>" onchange="updateEngineerRate(<?php print $subgroup["ProductSubTypeId"]; ?>);">
				</tr>
			<?php 
			}
			?>
			</tbody>
		</table>
	</body>
	<?php include 'footer.php'; ?>
	<script src="js/excel-bootstrap-table-filter-bundle.js"></script>
	<script> 
		$('#subgroupTable').excelTableFilter();
		
		function updateOPcharge(subid) {
			//alert("Update Rate ("+subid+")"); 
			var oprate="OPrate"+subid;
			var newrate=$("#"+oprate).val(); 
			//alert("New Rate: "+newrate);
		   $.post("updatesubgroup.php", {
			subtypeid: subid,
			oprate: newrate
			}, function (data, status) {
				//alert("Data: "+data);
				var obj=jQuery.parseJSON(data);
				
				if (obj.status=="ok") { 
					$('.alert').addClass("alert-success");
					$('#status').html('Success!');
					$('#msg').html(obj.msg);
					$('.alert').addClass("show");
					setTimeout(function(){$('.alert').removeClass("show"); $('.alert').addClass('collapse'); $('.alert').removeClass("alert-success");},2000);			
				}
				else {
					$('.alert').addClass("alert-error");
					$('#status').html('Error!');
					$('#msg').html(obj.msg);
					$('.alert').addClass("show");
					setTimeout(function(){$('.alert').removeClass("show"); $('.alert').addClass('collapse'); $('.alert').removeClass("alert-error");},4000);				
				}

			}); 
		}
		
		function updateEngineerRate(subid) {
			//alert("Update Rate ("+subid+")"); 
			var oprate="ENGrate"+subid;
			var newrate=$("#"+oprate).val(); 
			//alert("New Rate: "+newrate);
		   $.post("updatesubgroup.php", {
			subtypeid: subid,
			engrate: newrate
			}, function (data, status) {
				//alert("Data: "+data);
				var obj=jQuery.parseJSON(data);
				
				if (obj.status=="ok") { 
					$('.alert').addClass("alert-success");
					$('#status').html('Success!');
					$('#msg').html(obj.msg);
					$('.alert').addClass("show");
					setTimeout(function(){$('.alert').removeClass("show"); $('.alert').addClass('collapse'); $('.alert').removeClass("alert-success");},2000);			
				}
				else {
					$('.alert').addClass("alert-error");
					$('#status').html('Error!');
					$('#msg').html(obj.msg);
					$('.alert').addClass("show");
					setTimeout(function(){$('.alert').removeClass("show"); $('.alert').addClass('collapse'); $('.alert').removeClass("alert-error");},4000);				
				}

			}); 
		}
	</script>