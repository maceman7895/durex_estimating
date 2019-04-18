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
	$estiamteid=0; 
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
	
	if (isset($_REQUEST["estimateid"])) { 
		$estimateid=$_REQUEST["estimateid"];
	}
	
	if ($routingid==0) { 
		$seqno=$seqno+10; 
	}
	

?>
<style>

	
	.number {
		text-align:right;
		width: 95px;
	}
</style>
<form id="routeForm" name="routeForm">	
	<input type="hidden" id=estimateid" value="<?php print $estimateid; ?>">
	<input type="hidden" id="RoutingId" value="<?php print $routingid; ?>">
	<div class="form-row">
		<div class="form-group col-md-1">
			<label for="seqno">Seq #:</label>
			<input type="text" tabindex=1 class="form-control" id="routeseq" name="routeseq" value="<?php if ($seqno<100) { print "0".$seqno; } else { print $seqno; } ?>">
		</div>
		<div class="form-group col-md-6">
			<label for="routedesc">Route Description:</label>
			<input type="text" tabindex=2 class="form-control" id="routedesc" name="routedesc"><?php print $routedesc; ?>
		</div>
	</div>
	<div class="form-row">
		<div class="form-group col-mid-4">
			<label for="wcid">WC Id:</label>
			<select class="form-control" id="wcid" name="wcid" tabindex=3>
				<?php	foreach($db->query($sql_workcenters) as $wc) { ?>
				  <option value="<?php print $wc["WCID_I"];?>"<?php if ($wcid==$wc["WCID_I"]) { print " selected='selected'"; } ?>><?php print  $wc["WCID_I"]." : ".$wc["WCDESC_I"]; ?></option>
				<?php } ?>
			</select>	
		</div>
		<div class="form-group col-mid-4">	
			<label for="wcid">Machine Id:</label>
			<select class="form-control" id="wcid" name="machineid" tabindex=4>
				 <option value=""> </option>
				<?php	foreach($db->query($sql_machine) as $machine) { ?>
				  <option value="<?php print $machine["MACHINEID_I"];?>"><?php if ($machineid==$machine["MACHINEID_I"]) { print " selected='selected'"; } ?><?php print  $machine["MACHINEID_I"]." : ".$machine["MACHINEDESC_I"]; ?></option>
				<?php } ?>
			</select>		
		</div>
	</div>
	<table class="table table-striped" style="margin-top: 5px;background-color:#ffffff; width: 600px; margin:auto">
		<tr>
			<th>Type</th>
			<th>Labor Code</th>
			<th style="text-align:center; width:125px">Time<br>(minutes)</th>
			<th style="text-align:center; width:125px">Rate</th>
		</tr>
		<tr>
			<td>Setup</td>
			<td>
				<select tabindex=5 class="form-control" id="setupcode" name="setupcode">
					<?php 
						//Get the labor codes
						foreach($db->query($sql_laborcode) as $laborcode) {
					?>
					<option value="<?php print $laborcode["code"]; ?>"<?php if ($laborcode["code"]==$setupcode) { print " selected"; } ?>><?php print $laborcode["code"]." : ".$laborcode["codedesc"]; ?> </option> 
					<?php
						}
					?>
				</select>
			</td>
			<td><input tabindex=6 class="form-control number" type="number" id="setuptime" name="setuptime" value="<?php print $setuptime; ?>"></td>
			<td><input class="form-control number" type="number" id="setuprate" name="setuprate" value="<?php print $setuprate; ?>" readonly></td>			
		</tr>
		<tr>
			<td>Labor</td>
			<td>
				<select tabindex=5 class="form-control" id="laborcode" name="laborcode">
					<?php 
						//Get the labor codes
						foreach($db->query($sql_laborcode) as $laborcode) {
					?>
					<option value="<?php print $laborcode["code"]; ?>"<?php if ($laborcode["code"]==$laborcode) { print " selected"; } ?>><?php print $laborcode["code"]." : ".$laborcode["codedesc"]; ?> </option> 
					<?php
						}
					?>
				</select>
			</td>
			<td><input tabindex=8 class="form-control number" type="number" id="labortime" name="labortime" value="<?php print $labortime; ?>"></td>
			<td><input class="form-control number" type="number" id="laborrate" name="laborrate" value="<?php print $laborrate; ?>" readonly></td>			
		</tr>
		<tr>
			<td>Machine</td>
			<td></td>
			<td><input tabindex=10 class="form-control number" type="number" id="machinetime" name="labortime" value="<?php print $machinetime; ?>"></td>
			<td><input class="form-control number" type="number" id="machinerate" name="laborrate" value="<?php print $machinerate; ?>" readonly></td>			
		</tr>
		<tr>
			<td>Move</td>
			<td></td>
			<td><input tabindex=12 class="form-control number" type="number" id="movetime" name="movetime" value="<?php print $movetime; ?>"></td>
			<td></td>			
		</tr>
		<tr>
			<td>Queue</td>
			<td></td>
			<td><input tabindex=14 class="form-control number" type="number" id="queuetime" name="queuetime" value="<?php print $queuetime; ?>"></td>
			<td></td>			
		</tr>
		<tr>
			<td>Cycle</td>
			<td></td>
			<td><input tabindex=16 class="form-control number" type="number" id="cycletime" name="cycletime" value="<?php print $cycletime; ?>"></td>
			<td></td>			
		</tr>
	</table>
	<div class="form-group">
		<label for="notes">Notes:</label>
		<textarea class="form-control" id="note" name="note" rows="3"><?php print $note; ?></textarea>
	</div>
	<button type="button" class="btn btn-primary" id="modalSave" data-dismiss="modal" onclick="saveroute()'">Save changes</button>
	
</form>

<script> 
// Show loader & then get content when modal is shown
	function saveroute() {
		$('#route-tab').click();
	}
</script>
