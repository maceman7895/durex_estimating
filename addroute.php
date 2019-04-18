<?php
	//Connect to the GP database.  If error, display the error message and exit.
	include 'dbconn.php';
	
	$sql_workcenters="select WCID_I, WCDESC_I from WC010931 where WCDESC_I not like '%DO NOT USE%' order by WCID_I";
	$sql_machine="select MACHINEID_I, MACHINEDESC_I, COSTPERPIECE_I from MM010032 where MACHINEDESC_I not like '%DO NOT USE%'";
	$sql_laborcode="select LABORCODE_I as code, LABCODEDESC_I as codedesc from LC010014";

	//Initialize variables
	$seqno=0;
	$note="";
	$routedesc="";	
	$estimateid=0; 
	$routingid=0;
	$wcid="";
	$machineid="";
	$laborcode="";
	$labortime="0.00";
	$laborrate="0.00";
	$setupcode="";
	$setuptime="0.00";
	$setuprate="0.00";
	$machinetime="0.00";
	$machinerate="0.00";
	$movetime="0.00";
	$queuetime="0.00";
	$cycletime="0.00";
	$mfgohrate=0;
	$sgaohrate=0;
	$parentid=0;
	$subparentid=0;
	
	if (isset($_REQUEST["estimate"])) { 
		$estimateid=$_REQUEST["estimate"];
	}

	if (isset($_REQUEST["routingid"])) { 
		$routingid=$_REQUEST["routingid"];
	}

	if (isset($_REQUEST["parentid"])) { 
		$subparentid=$_REQUEST["parentid"];
		$parentid=$_REQUEST["parentid"];
	}
	
	//print_r($_REQUEST);
	//print "hello";
	
	$sql_nextSeq="select MAX(RoutingSequence) as nextseq from DUREX_EstimateRouting where EstimateId=$estimateid and Subparentid=$subparentid";
	
	foreach($db->query($sql_nextSeq) as $row) {
		$seqno=$row["nextseq"];
	}
	
	if ($routingid==0) { 
		$seqno=$seqno+10; 
	}
	else {
		$sql_route="select * from DUREX_EstimateRouting where EstimateId=$estimateid and RoutingId=$routingid";
		
		foreach($db->query($sql_route) as $route) {
			$seqno=$route["RoutingSequence"];
			$note=$route["Notes"];
			$wcid=$route["WorkCenterId"];
			$machineid=$route["MachineId"];
			$labortime=$route["RunLaborTime"];
			$laborrate=$route["RunLaborRate"];
			$setuptime=$route["SetupTime"];
			$setuprate=$route["SetupLaborRate"];
			$machinetime=$route["MachineTime"];
			$machinerate=$route["MachineRate"];
			$movetime=$route["MoveTime"];
			$queuetime=$route["QueueTime"];
			$cycletime=$route["CycleTime"];
			$mfgohrate=$route["MFGOHRate"];
			$sgaohrate=$route["SGAOHRate"];
			$parentid=$route["Subparentid"];
		}
	}

?>
<style>

	
	.number {
		text-align:right;
		width: 95px;
	}
</style>

<form id="routeForm" name="routeForm">	
	<input type="hidden" id="estimateid" value="<?php print $estimateid; ?>">
	<input type="hidden" id="RoutingId" value="<?php print $routingid; ?>">
	<input type="hidden" id="mfgohrate" value="<?php print $mfgohrate; ?>">
	<input type="hidden" id="sgaoharate" value="<?php print $sgaohrate; ?>">
	<input type="hidden" id="parentid" value="<?php print $subparentid; ?>">
	
	<div class="form-row">
		<div class="form-group col-md-1">
			<label for="seqno">Seq #:</label>
			<input type="text" tabindex=1 class="form-control" id="routeseq" name="routeseq" value="<?php if ($seqno<100) { print "0".$seqno; } else { print $seqno; } ?>">
		</div>
		<div class="form-group col-mid-4">
			<label for="wcid">WC Id:</label>
			<select class="form-control" id="wcid" name="wcid" tabindex=3 onchange="getrates();">
				<?php	foreach($db->query($sql_workcenters) as $wc) { ?>
				  <option value="<?php print $wc["WCID_I"];?>"<?php if ($wcid==$wc["WCID_I"]) { print " selected='selected'"; } ?>><?php print  $wc["WCID_I"]." : ".$wc["WCDESC_I"]; ?></option>
				<?php } ?>
			</select>	
		</div>
	</div>

	<table class="table table-striped" style="margin-top: 5px;background-color:#ffffff; width: 600px; margin:auto">
		<tr>
			<th>Type</th>
			<th style="text-align:center; width:125px">Time<br>(minutes)</th>
			<th style="text-align:center; width:125px">Rate</th>
		</tr>
		<tr>
			<td>Setup</td>
			<td><input tabindex=6 class="form-control number" type="number" id="setuptime" name="setuptime" value="<?php print $setuptime; ?>"></td>
			<td><input class="form-control number" type="number" id="setuprate" name="setuprate" value="<?php print $setuprate; ?>" readonly></td>			
		</tr>
		<tr>
			<td>Labor</td>
			<td><input tabindex=8 class="form-control number" type="number" id="labortime" name="labortime" value="<?php print $labortime; ?>"></td>
			<td><input class="form-control number" type="number" id="laborrate" name="laborrate" value="<?php print $laborrate; ?>" readonly></td>			
		</tr>
		<tr>
			<td>Machine</td>
			<td><input tabindex=10 class="form-control number" type="number" id="machinetime" name="machinetime" value="<?php print $machinetime; ?>"></td>
			<td><input class="form-control number" type="number" id="machinerate" name="machinerate" value="<?php print $machinerate; ?>" readonly></td>			
		</tr>
		<tr>
			<td>Cycle</td>
			<td><input tabindex=16 class="form-control number" type="number" id="cycletime" name="cycletime" value="<?php print $cycletime; ?>"></td>
			<td></td>			
		</tr>
	</table>
	<div class="form-group">
		<label for="notes">Notes:</label>
		<textarea class="form-control" id="note" name="note" rows="3"><?php print $note; ?></textarea>
	</div>
	<button type="button" class="btn btn-primary" id="modalSave" data-dismiss="modal" onclick="saveroute();">Save changes</button>
	
</form>

<script> 
// Show loader & then get content when modal is shown
	function saveroute() {
		timeoutcheck();
		var estimateid=$('#estimateid').val();
		var routingid=$('#RoutingId').val();
		var note=$('#note').val();
		var routingseq=$('#routeseq').val();
		var routedesc=$('#routedesc').val();
		var wc=$('#wcid').val();
		var setuplaborrate=$('#setuprate').val();
		var setuptime=$('#setuptime').val();
		var setuplaborcode='';
		var machineid=$('#machineid').val();
		var machinetime=$('#machinetime').val();
		var machinerate=$('#machinerate').val();
		var runlaborcode=' ';
		var runlaborrate=$('#laborrate').val();
		var runlabortime=$('#labortime').val();
		var cycletime=$('#cycletime').val();

		var mfgohrate=$('#mfgohrate').val();
		var sgaohrate=$('#sgaoharate').val();
		var parentid=$('#parentid').val();
		//alert("pressed!");
		
		//Send data to save or update
		$.post("updateroute.php", {
			estimateid: estimateid,
			routingid: routingid,
			note: note,
			routingseq: routingseq,
			routedesc: routedesc,
			wc: wc,
			setuplaborrate: setuplaborrate,
			setuptime: setuptime,
			//setuplaborcode: setuplaborcode,
			machineid: machineid,
			machinetime: machinetime,
			machinerate: machinerate,
			//runlaborcode: runlaborcode,
			runlaborrate: runlaborrate,
			runlabortime: runlabortime,
			cycletime: cycletime,
			mfgohrate: mfgohrate,
			sgaohrate: sgaohrate,
			parentid: parentid
		}, function (data, status) {
			// close the popup
			alert("Status: "+status);
			alert("Data: "+data);
			// read records again
			//readRecords();
			alert("parentid: "+parentid);
			if (parentid==0) { 
				$('#routing-tab').click();
				
			}
			else {
				var target = $("#modal-2").attr("href");
				target="routing.php?estimate="+estimateid+"&bomid="+parentid;
				alert("Target:"+ target);

				// load the url and show modal on success
				$("#modal-2 .modal-body").load(target, function() { 
					 $("#modal-2").modal("show"); 
				});			
			}
		});
		alert("heelo");
	}
	
	function getrates(){
		//alert("I should get rates");
		var wc=$('#wcid').val();
		var num=wc.substring(2, 5);
		if (wc.substring(0,2)=='WC') {
			var laborcode='RUNWC'+num;
			var setupcode='SUWC'+num;
		}
		//alert("Run code: "+laborcode);
		//alert("Setup code: "+setupcode);
		$.post("getrates.php", {
			laborcode: laborcode,
			setupcode: setupcode
		}, function (data, status) {
			// close the popup
			//alert("Status: "+status);
			//alert("Data: "+data);
			//alert( "Laborrate: "+data.labor);
			$('#setuprate').val(data.setup);
			$('#laborrate').val(data.labor);
			$('#mfgohrate').val(data.mfgoh);
			$('#sgaohrate').val(data.sgaoh);
	
			// read records again
			//readRecords();
	 
			// clear fields from the popup
		},"json");

	}
</script>
