<?php 
	include 'dbconn.php';
	
	if (isset($_REQUEST["otherchargeid"])) {
		$chargeid=$_REQUEST["otherchargeid"];
	}
	else { 
		$chargeid=0;
	}
	
	if (isset($_REQUEST["costtype"])) {
		$costtype=$_REQUEST["costtype"];
	}
	else { 
		$costtype='';
	}

	$sql_othercharge="select * from DUREX_EstimateOtherCharges c where OtherChargeId=$chargeid";

	
	foreach($db->query($sql_othercharge) as $charge) { 
		$chargedesc=$charge["OtherDescription"];
?>

<p style="text-align:center">Are you sure you want to delete <?php print $chargedesc; ?> cost?</p>
<p style="text-align:center">This process can't be undone.</p>
<div style="text-align:center">
	<button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
	<button type="button" class="btn btn-danger" onclick="delcharge();" data-dismiss="modal">Yes</button>
</div>
<?php
	}
?>

<script>
	//var costtype=<?php print $costtype; ?>;
	function delcharge() {
		$.post("deletecharge_db.php", {
			chargeid: '<?php print $chargeid; ?>' 
		}, function (data, status) {
			// close the popup
			var costtype="<?php print $costtype; ?>";
			//alert("Status: "+status);
			//alert("Data: "+data);
			//alert("Cost Type: " +costtype);
			if (costtype=='MISC') {
				$('#miscoss-tab').click();
			}
			
			if (costtype=='TOOLING') {
				$('#tooling-tab').click();
			}
			
			// read records again
			//readRecords();
	 
			// clear fields from the popup
		});
		
	}
</script>
