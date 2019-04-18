<?php
	//Default SQl querys 
	if (isset($_REQUEST["estimate"])) {
		$estimateid=$_REQUEST["estimate"];
	}
	else {
		$estimateid=0;
	}
	
	if (isset($_REQUEST["costtype"])) {
		$costtype=$_REQUEST["costtype"];
	}
	else {
		$costtype='';
	}
	
	//print "cost type: $costtype <br>";
	//Connect to the GP database.  If error, display the error message and exit.
	include 'dbconn.php';
	
	$sql_getmisc="select * from DUREX_EstimateOtherCharges where CostType='$costtype' and EstimateId=$estimateid";
	
	if ($costtype=="MISC") {
		$costTitle="Misc./OSS/Freight"; 
	}
	else {
		$costTitle="Tooling";
	}
	$costline=0;
	
?>
	<table class="table" style="margin-top: 5px;background-color:#ffffff;" id="mytable" style="width:1061px;">
		<tr style="color:#000000; background-color:#9bd1f7">
			<th style="width:3%;border-right: 1px ridge #636161"><span data-toggle="modal" data-target=".modal" data-modtitle="Add <?php print $costTitle; ?> Cost" data-modsize="800px" data-modsav="misc" data-paragraphs="?estimateid=<?php print $_REQUEST["estimate"]; ?>&costtype=<?php print $costtype; ?>" data-url="addmisccharge.php"><img src="/images/add.png" alt="delete" width="25" height="25" data-toggle="tooltip" data-placement="top" title="Add A Charge"></span></th>
			<th style="width:50%; border-right: 1px ridge #636161">Description</th>
			<th style="width:11%;border-right: 1px ridge #636161">Cost</th>
			<th style="width:11%;border-right: 1px ridge #636161">Type</th>			
			<th style="width:35%;border-right: 1px ridge #636161">Note</th>
			<th style="column-width:25px;"></th>
		</tr>
	<?php
		if ($estimateid!=0 && $costtype!='') {
			foreach($db->query($sql_getmisc) as $cost) {
				$costline=$costline+1;
				$costId=$cost["OtherChargeId"];
				$costDescription=$cost["OtherDescription"];
				$costCost=$cost["Cost"];
				$costType=$cost["OtherChargeType"];
				$costNote=$cost["Note"];
	?>
		<tr style="background-color: #fffff;">
			<td style="width:3%"><span data-toggle="modal" data-target="#modal-1" data-modtitle="Edit Charge" data-modtitle="Edit Charge" data-modsize="1024px" data-modsav="misccharge" data-paragraphs="?otherchargeid=<?php print $costId; ?>" data-url="addmisccharge.php"><img src="/images/edit.png" alt="edit" width="25" height="25" data-toggle="tooltip" data-placement="top" title="Edit"></span></td>				
			<td style="width:50%"><?php print $costDescription; ?></td>
			<td style="width:11%;"><?php print $costCost; ?></td>
			<td style="width:11%;"><?php print $costType; ?></td>			
			<td style="width:30.99%;"><?php print $costNote; ?></td>
			<td><span data-toggle="modal" data-target=".modal" data-modtitle="Delete Charge" data-modfooter="hide" data-modsize="512px" data-modsav="misccharge" data-paragraphs="?otherchargeid=<?php print $costId; ?>&costtype=<?php print $costtype; ?>" data-url="deletecharge.php"><img src="/images/delete.png" alt="delete" width="25" height="25" data-toggle="tooltip" data-placement="top" title="Delete"></span></td>		
		</tr>
	<?php
			}
		}
		if ($costline==0) {
	?>
		<tr style="background-color: #fffff;">
			<td colspan="6" style="text-align: center;">
			Currently no costs
			</td>		
		</tr>
	<?php
		}
	?>
	</table>
</html>
