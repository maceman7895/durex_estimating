<?php
	//Connect to the GP database.  If error, display the error message and exit.
	include 'dbconn.php';
	
	$estimateid='';
	$bomid=0;
	
	
	if (isset($_REQUEST["estimate"])) {
		$estimateid=$_REQUEST["estimate"];
	}
	
	if (isset($_REQUEST["bomid"])) {
		$bomid=$_REQUEST["bomid"];
	}
	
	$sql_workcenters="select WCID_I, WCDESC_I from WC010931 where WCDESC_I not like '%DO NOT USE%'";
	$sql_machine="select MACHINEID_I, MACHINEDESC_I, COSTPERPIECE_I from MM010032 where MACHINEDESC_I not like '%DO NOT USE%'";
	$sql_laborcode="select * from LC010014";
	$sql_routing="select *,(select WCDESC_I from WC010931 where WCID_I=r.WorkCenterId) as WorkCenterDesc from DUREX_EstimateRouting r where EstimateId=$estimateid and Subparentid=$bomid order by RoutingSequence";
	//print "SQL: $sql_routing";
	$total=array("SetupTime" => 0, "RunLaborTime" => 0, "MachineTime" =>0, "CycleTime" => 0)
	
?>


<table class="table table-striped "style="margin-top: 5px;background-color:#ffffff;">
	<tr  style="color:#000000; background-color:#90f785">
		<th style="border-right: 1px ridge #636161"><span data-toggle="modal" data-target="#modal-1" data-modtitle="Add A Route" data-modsize="825px" data-modfooter="hide" data-modsav="route" data-paragraphs="?estimate=<?php print $_REQUEST["estimate"]; ?>&parentid=<?php print $bomid; ?>" data-url="addroute.php"><img src="/images/add.png" alt="add" width="25" height="25" data-toggle="tooltip" data-placement="top" title="Add route"></span></th>
		<th style="column-width:50px; border-right: 1px ridge #636161">Seq#</th> 
		<th style="column-width:125x;border-right: 1px ridge #636161;text-align:left">WC Id</th>
		<th style="column-width:300px; border-right: 1px ridge #636161">Description</th> 
		<th style="column-width:100px;border-right: 1px ridge #636161;text-align:center">Setup Time</th>
		<th style="column-width:100px;border-right: 1px ridge #636161;text-align:center">Labor Time</th>
		<th style="column-width:100px;border-right: 1px ridge #636161;text-align:center">Machine Time</th>
		<th style="column-width:100px;border-right: 1px ridge #636161;text-align:center">Cycle Time</th>
		<th style="column-width:35px;border-right: 1px ridge #636161">Notes</th>
		<th style="column-width:25px;"></th>
	</tr>
	<?php foreach($db->query($sql_routing) as $route) { 
		$total["SetupTime"]+=$route["SetupTime"]; 
		$total["RunLaborTime"]+=$route["RunLaborTime"]; 
		$total["MachineTime"]+=$route["MachineTime"]; 
		$total["CycleTime"]+=$route["CycleTime"]; 		
	?>
		
	<tr>
		
		<td><span data-toggle="modal" data-target="#modal-1" data-modtitle="Edit Route" data-modsize="825px" data-modsav="route" data-modfooter="hide" data-paragraphs="?estimate=<?php print $estimateid; ?>&routingid=<?php print $route["RoutingId"]; ?>" data-url="addroute.php" <?php if ($bomid!=0) { ?> onclick="$('#modal-2').modal({ show: true });" <?php } ?>><img src="/images/edit.png" alt="edit" width="25" height="25" data-toggle="tooltip" data-placement="top" title="Edit"></span></td>				
		<td style="column-width:50px;"><?php print $route["RoutingSequence"]; ?></td>
		<td style="column-width:75px;"><?php print $route["WorkCenterId"]; ?></td>
		<td style="column-width:350px;"><?php print $route["WorkCenterDesc"]; ?></td>
		<td style="column-width:100px;text-align:right"><?php print number_format($route["SetupTime"],2); ?></td>
		<td style="column-width:100px;text-align:right"><?php print number_format($route["RunLaborTime"],2); ?></td>
		<td style="column-width:100px;text-align:right"><?php print number_format($route["MachineTime"],2); ?></td>
		<td style="column-width:100px;text-align:right"><?php print number_format($route["CycleTime"],2); ?></td>
		<td style="column-width:350px;"><?php print $route["Notes"]; ?></td>
		<td><span data-toggle="modal" data-target="#modal-1" data-modtitle="Delete Step" data-modfooter="hide" data-modsize="512px" data-modsav="route" data-paragraphs="?routingid=<?php print $route["RoutingId"]; ?>&subroute=<?php print $route["Subparentid"]; ?>" data-url="deletestep.php"><img src="/images/delete.png" alt="delete" width="25" height="25" data-toggle="tooltip" data-placement="top" title="Delete"></span></td>		
		
	<?php } ?>
	</tr>
	<tr>
		<td colspan="4" style="text-align:right; font-weight:700">Totals:</td>
		<td style="column-width:100px;text-align:right;font-weight:700"><?php print number_format($total["SetupTime"],2); ?></td>
		<td style="column-width:100px;text-align:right;font-weight:700"><?php print number_format($total["RunLaborTime"],2); ?></td>
		<td style="column-width:100px;text-align:right;font-weight:700"><?php print number_format($total["MachineTime"],2); ?></td>
		<td style="column-width:100px;text-align:right;font-weight:700"><?php print number_format($total["CycleTime"],2); ?></td>
		<td style="column-width:350px;"><?php print $route["Notes"]; ?></td>
		<td></td>		
	</tr>
</table>
