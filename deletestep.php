<?php 
	include 'dbconn.php';
	
	if (isset($_REQUEST["routingid"])) {
		$routingid=$_REQUEST["routingid"];
	}
	else { 
		$routingid=0;
	}

	//Checks to see if a sub is set
	if (isset($_REQUEST["subroute"])) {
		$subroute=$_REQUEST["subroute"];
	}
	else { 
		$subroute=0;
	}

	$sql_routingstep="select * from DUREX_EstimateRouting r where RoutingId=$routingid";
	foreach($db->query($sql_routingstep) as $bom) { 
		$routingsetup=$bom["RoutingSequence"];
?>

<p style="text-align:center">Are you sure you want to delete <?php print $routingsetup; ?> routing sequence?</p>
<p style="text-align:center">This process can't be undone.</p>
<div style="text-align:center">
	<button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
	<button type="button" class="btn btn-danger" onclick="delstep();" data-dismiss="modal">Yes</button>
</div>
<?php
	}
?>

<script>
	function delstep() {
		var subroute=<?php print $subroute; ?>;
		$.post("deletestep_db.php", {
			routingid: '<?php print $routingid; ?>' 
		}, function (data, status) {
			// close the popup
			//alert("Status: "+status);
			//alert("Data: "+data);
			if (subroute==0) { 
				$('#routing-tab').click();
			}
			else {
				var target = $("#modal-2").attr("href");

				// load the url and show modal on success
				$("#modal-2 .modal-body").load(target, function() { 
					 $("#modal-2").modal("show"); 
				});			
			}
			// read records again
			//readRecords();
	 
			// clear fields from the popup
		});
		
	}
</script>
