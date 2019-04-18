<?php

	//Default SQl querys 
	//print_r($_REQUEST);
	//Connect to the GP database.  If error, display the error message and exit.
	include 'dbconn.php';
	
	$bomid=$_REQUEST["bomid"];
	$sql_bom="select * from DUREX_EstimateBOM where EstimateBOMid=$bomid";
	foreach($db->query($sql_bom) as $bom) { 
		$itemnumber=$bom["ITEMNMBR"];
?>

<p style="text-align:center">Are you sure you want to delete <?php print $itemnumber; ?>?</p>
<p style="text-align:center">This process can't be undone.</p>
<div style="text-align:center">
	<button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
	<button type="button" class="btn btn-danger" onclick="delcomponent();" data-dismiss="modal">Yes</button>
</div>
<?php
	}
?>

<script>
	function delcomponent() {
		$.post("delfrombom.php", {
			bomid: '<?php print $bomid; ?>' 
		}, function (data, status) {
			// close the popup
			//alert("Status: "+status);
			//alert("Data: "+data);
			$('#bom-tab').click();
	 
			// read records again
			//readRecords();
	 
			// clear fields from the popup
		});

		
	}
</script>

