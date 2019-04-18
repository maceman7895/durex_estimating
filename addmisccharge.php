<?php 
	//Security check routine
	include 'security.php';
	
	//Connect to the GP database.  If error, display the error message and exit.
	include 'dbconn.php';

	//Get the estimate id if passed
	if (isset($_REQUEST["estimateid"])) {
		$estimateid=$_REQUEST["estimateid"];
	}
	else { 
		$estimateid=0;
	}
	
	//Get the other charge id if passed
	if (isset($_REQUEST["otherchargeid"])) {
		$otherchargeid=$_REQUEST["otherchargeid"];
	}
	else { 
		$otherchargeid=0;
	}
	
	//Get the cost type if passed
	if (isset($_REQUEST["costtype"])) {
		$costtype=$_REQUEST["costtype"];
	}
	else { 
		$costtype='';
	}	

	//Initialize values
	$otherDescription="";
	$otherCost="";
	$otherChargeType="";
	$otherNote="";
	$otherVendor="";
	$otherLeadTime="";
	
	$sql_otherCharge="select * from DUREX_EstimateotherCharges where OtherChargeId='$otherchargeid'";
	
	if ($otherchargeid!=0) {
		foreach($db->query($sql_otherCharge) as $othercharge) {
			$estimateid=$othercharge["EstimateId"];
			$otherDescription=$othercharge["OtherDescription"];
			$otherCost=$othercharge["Cost"];
			$otherChargeType=$othercharge["OtherChargeType"];
			$otherNote=$othercharge["Note"];
			$costtype=$othercharge["CostType"];
			$otherCendor=$othercharge["Vendor"];
			$otherLeadTime=$othercharge["LeadTime"];
		}
	}
	
	//print "Estimate id: $costtype";
?>

<form id="otherchargeForm" name="otherchargeForm">	
	<input type="hidden" id="estimateid" value="<?php print $estimateid; ?>">
	<input type="hidden" id="otherchargeid" value="<?php print $otherchargeid; ?>">
	<input type="hidden" id="costtype" value="<?php print $costtype; ?>">
	
	<div class="form-row">
		<div class="form-group col-md-12">
			<label for="otherdesc">Description:</label>
			<input type="text" tabindex=1 class="form-control" id="otherdesc" name="otherdesc" value="<?php print $otherDescription;  ?>" onblur="allowsave();">
		</div>
	</div>
	
	<div class="form-row">
		<div class="form-group col-md-2">
			<label for="othercost">Cost:</label>
			<input type="number" tabindex=2 class="form-control" id="othercost" name="othercost" value="<?php print $otherCost;  ?>" onblur="allowsave();">
		</div>
		<div>Cost Type:<br></div>
		<div class="form-group form-check" style="margin: 5px";>

			<input class="form-check-input" type="radio" name="othertypeRadios" id="amortize" value="Amortize"<?php if ($otherChargeType=="Amortize") { print " checked"; } ?> onblur="allowsave();">
			<label class="form-check-label" for="amortize">
				Amortize
			</label>
		</div>
		<?php 
			if ($costtype=="TOOLING") { 
		?>
		<div class="form-group form-check" style="margin: 5px";>
			<input class="form-check-input" type="radio" name="othertypeRadios" id="Lot" value="One-time"<?php if ($otherChargeType=="One-time") { print " checked"; } ?> onblur="allowsave();">
			<label class="form-check-label" for="Lot">
				One-time
			</label>
		</div>		
		<?php 
			}
			else {
		?>
		<div class="form-group form-check" style="margin: 5px";>
			<input class="form-check-input" type="radio" name="othertypeRadios" id="Lot" value="Lot Charge"<?php if ($otherChargeType=="Lot Charge") { print " checked"; } ?> onblur="allowsave();">
			<label class="form-check-label" for="Lot">
				Lot Charge
			</label>
		</div>
		<div class="form-group form-check" style="margin: 5px";>
			<input class="form-check-input" type="radio" name="othertypeRadios" id="Each" value="Each"<?php if ($otherChargeType=="Each") { print " checked"; } ?> onblur="allowsave();">
			<label class="form-check-label" for="Lot">
				Each
			</label>
		</div>
		<?php
			}
		?>
	</div>
	<div class="form-row">
		<div class="form-group col-md-6">
			<label for="othervendor">Vendor:</label>
			<input type="text" tabindex=2 class="form-control" id="othervendor" name="othervendor" value="<?php print $otherVendor;  ?>">
		</div>
		<div class="form-group col-md-6">
			<label for="otherleadtime">Lead Time:</label>
			<input type="text" tabindex=2 class="form-control" id="otherleadtime" name="otherleadtime" value="<?php print $otherLeadTime;  ?>">
		</div>
	</div>	
	<div class="form-row">
		<div class="form-group col-md-12">
			<label for="othernote">Note:</label>
			<textarea class="form-control" id="othernote"><?php print $otherNote;  ?></textarea>
		</div>
	</div>
	<div class="form-row">
		<button type="button" class="btn btn-primary" id="miscSave" data-dismiss="modal" onclick="addmisccost('<?php print $costtype; ?>');" <?php if ($otherchargeid==0) { print "disabled"; } ?>>Save changes</button>
	</div>
</form>

<script>
	function addmisccost(costtype) {
		timeoutcheck();
		var estimateid=$('#estimateid').val();
		var otherchargeid=$('#otherchargeid').val();
		var costtype=$('#costtype').val();
		var costdesc=$('#otherdesc').val();
		var amount=$('#othercost').val();
		var note=$('#othernote').val();
		if ($("#amortize:checked").val()) { 
			otherchargetype='Amortize';
		}
		if ($("#Lot:checked").val()) { 
			otherchargetype='Lot Charge';
		}
		if ($("#Each:checked").val()) { 
			otherchargetype='Each';
		}
		//alert("pressed!");
		
		//Send data to save or update
		$.post("addothercost.php", {
			estimateid: estimateid,
			otherchargeid: otherchargeid,
			otherdescription: costdesc,
			amount: amount, 
			otherchargetype: otherchargetype,
			note: note,
			costtype: costtype
		}, function (data, status) {
			// close the popup
			//alert("Status: "+status);
			//alert("Data: "+data);
			
			if (costtype=='MISC') { 
				$("#miscoss-tab").click();
				//alert('click misc tab');
			}
			
			if (costtype=='TOOLING') { 
				$("#tooling-tab").click();
				//alert('click tooling tab');
			}	
			// read records again
			//readRecords();
	 
			// clear fields from the popup
		});
	}	
	
	function allowsave() {
		timeoutcheck();
		var otherchargetype='';
		var costdesc=$('#otherdesc').val();
		var amount=$('#othercost').val();
		if ($("#amortize:checked").val()) { 
			otherchargetype='Amortize';
		}
		if ($("#Lot:checked").val()) { 
			otherchargetype='Lot Charge';
		}
		if ($("#Each:checked").val()) { 
			otherchargetype='Each';
		}				
		
		if (otherchargetype!='' && costdesc!='' && amount!=0) {
			$("#miscSave").removeAttr('disabled');
		}
		else {
			$("#miscSave").attr('disabled', 'disabled');
		}
	}

</script>


	