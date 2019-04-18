<!doctype html>
<html lang="en">
<?php
	include 'security.php';
	include 'dbconn.php';
	
	$sql_productgroups="select *, (select UserCatLongDescr from IV40600 where USCATVAL=s.ProductTypeCode and USCATNUM=1) as ProductSubgroupDescription 	from DUREX_EstimateProductType s Order by s.ProductTypeCode";	
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
		<div style="padding-left: 10px;"><h2>Product Group Table</h2></div>
		<div class="alert alert-success alert-dismissible fade collapse">
		  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		  <strong><span id="status">Success!</span></strong> <span id="msg">Success</p>
		</div>
		<table id="subgroupTable" class="table">
			<thead>
				<tr class="table-info">
					<th style="border-right: 1px ridge #636161">Group Code</th>
					<th style="border-right: 1px ridge #636161">Estimate ID</th>
					<th style="border-right: 1px ridge #636161">Estimator Email</th>
					<th style="border-right: 1px ridge #636161">Approver ID</th>
					<th style="border-right: 1px ridge #636161">Approver Email</th>
					<th style="border-right: 1px ridge #636161">Material Adder (%)</th>
					<th style="border-right: 1px ridge #636161">Markup (%)</th>					
				</tr>
			</thead>
			<tbody>
		<?php
			foreach($db->query($sql_productgroups) as $prodgroup) {
		?>
				<tr>
					<td><?php print $prodgroup["ProductTypeCode"]; ?></td>
					<td><input type="text" id="estimatorid<?php print $subgroup["EstimateProductTypeId"]; ?>" value="<?php print $prodgroup["EstimatorId"]; ?>"></td>
					<td><input type="text" id="estimatoremail<?php print $subgroup["EstimateProductTypeId"]; ?>" value="<?php print $prodgroup["EstimatorEmail"]; ?>"></td>
					<td><input type="text" id="approverid<?php print $subgroup["EstimateProductTypeId"]; ?>" value="<?php print $prodgroup["ApproverId"]; ?>"></td>
					<td><input type="text" id="approveemail<?php print $subgroup["EstimateProductTypeId"]; ?>" value="<?php print $prodgroup["ApproverEmail"]; ?>"></td>
					<td><input style="text-align: right" type="number" id="Adder<?php print $subgroup["EstimateProductTypeId"]; ?>" value="<?php print number_format($prodgroup["AdderPercentage"],2); ?>" onchange="alert('Changed!');">
					<td><input style="text-align: right" type="number" id="Markup<?php print $subgroup["EstimateProductTypeId"]; ?>" value="<?php print number_format($prodgroup["Markup"],2); ?>" onchange="alert('Changed2!');">
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