<?php
   /*********************************************************************************************
	*                                       breakdown.php                                       *
	*-------------------------------------------------------------------------------------------*
	* 	Written By: William Mace 																*
	*	Date: 03/28/2019																		*
	*-------------------------------------------------------------------------------------------*
	*	Descriotion:																			*
	*		This generates the time break down data.											*
	*-------------------------------------------------------------------------------------------*
	*  Paremeters: 													                      		*
	*		estimateid = The estimate id to do the breakdown foreach						  	*
	*-------------------------------------------------------------------------------------------*
	* 	Modifications:																		  	*
	*	Date:		By:					Description:										  	*
	*	12/31/2000	William Mace															  	*
	*********************************************************************************************/
	include 'security.php';
	include 'dbconn.php';
	//print "Time & Cost Breakdown";
	
	//Get the estimate id passed 
	if (isset($_REQUEST["estimateid"])) {
		$estimateid=$_REQUEST["estimateid"];
	}
	else { 
		$estimateid=0;
	}

	//Define the arrays 
	$qty=array();
	$coltotal=array();

	//SQL queries to get data
	$sql_timecost="select *, CAST(ROUND((SetupLaborRate+MFGOHRate)*(SetupTime/60), 2) as DECIMAL(18,2)) as SetupLaborCost, CAST(ROUND((RunLaborRate+MFGOHRate)*(RunLaborTime/60), 2) as DECIMAL(18,2)) as RunLaborCost, CAST(ROUND(MachineRate*(MachineTime/60), 2) as DECIMAL(18,2)) as MachineCost, CAST(ROUND(((SetupLaborRate+MFGOHRate)*(SetupTime/60))+((RunLaborRate+MFGOHRate)*(RunLaborTime/60))+(MachineRate*(MachineTime/60)), 2) as DECIMAL(18,2)) as BaseCost ,SetupTime+RunLaborTime+MachineTime as BaseTime from DUREX_EstimateRouting where EstimateId=$estimateid and Subparentid=0 order by RoutingSequence";
	$sql_quantities="select * from DUREX_EstimateQuantity where EstimateId=$estimateid and QuoteLineId<>0";

	
	$total_based=0.00;
	//print "SQL: $sql_timecost<br>";
	
	foreach($db->query($sql_quantities) as $qtys) {
		$qty[]=number_format($qtys["QTY"],0);
		$coltotal[]=0;
	}
	
	//Calculates what the table size should be
	$tablesize=955+(100*sizeof($qty));
?>
	<style> 
	.timenumber { 
		text-align: right;
	}
	
	.costnumber {
		border-top: 1px solid #9b9b9b;
		text-align: right;
	}
	</style>
	<table class="table" style="margin-top: 5px;background-color:#ffffff;width:<?php print $tablesize; ?>px;" id="breakdownTable">
		<tr style="color:#000000; background-color:#9bd1f7">
			<th style="width:60px;border-right: 1px ridge #636161;width:75px;">Seq #</th>
			<th style="width:125px;border-right: 1px ridge #636161;width:125px;">Work Center</th>
			<th style="width:60px;"></th>
			<th style="width:120px;border-right: 1px ridge #636161;width ">Labor Setup</th>			
			<th style="width:110px;border-right: 1px ridge #636161">Labor Run</th>
			<th style="width:150px;border-right: 1px ridge #636161">Machine Setup</th>			
			<th style="width:130px;border-right: 1px ridge #636161">Machine Run</th>
			<th style="width:100px;border-right: 1px ridge #636161">Base (1)</th>	
		<?php 
			//Read through the quantities to create headings for quantities 
			foreach($qty as $key=>$val) {
		?>
			<th style="idth:100px;border-right: 1px ridge #636161; text-align:center">QTY <?php print $val; ?></th>	
		<?php
			}
		?>
		</tr>
<?php
	$reccount=0;
	//Read the routing records to get costs
	foreach($db->query($sql_timecost) as $cost) {  //SQL for labor costs
		//Update the record count
		$reccount=$reccount+1; 
		//Deterimine if odd or even
		$level=($reccount % 2);
		
		//Sets the row color for odd or even
		if ($level==0) { 
			$color="#cde7f7";
		}
		else {
			$color="#f7f7f7";
		}
		
		//Get the work center id and sequence number
		$wcid=$cost["WorkCenterId"];
		$seq=$cost["RoutingSequence"];
		
		//Display the time row
?>
		<tr style="background-color:<?php print $color; ?>">
			<td rowspan=2 style="vertical-align: middle;"><?php print $cost["RoutingSequence"]; ?></th>
			<td rowspan=2 style="vertical-align: middle;"><?php print $cost["WorkCenterId"]; ?></td>
			<td>Time</td>
			<td class="timenumber"><?php print $cost["SetupTime"]; ?></td>			
			<td class="timenumber"><?php print $cost["RunLaborTime"]; ?></td>
			<td class="timenumber">0.00</td>		
			<td class="timenumber"><?php print $cost["MachineTime"]; ?></td>
			<td class="timenumber"><?php print $cost["BaseTime"]; ?></td>
		<?php 
			//Read through the quantities to get times for quantities
			foreach($qty as $key=>$val) { //Quantity time loop
				//SQL query to get times for quantity
				$sql_qtytime="select SetupTime+(RunLaborTime*QTY)+(MachineTime*QTY) as QtyTime from DUREX_EstimateQuantity q inner join DUREX_EstimateRouting r on r.EstimateId=q.EstimateId where r.EstimateId=$estimateid and QTY=$val and RoutingSequence=$seq and WorkCenterId='$wcid' and q.QuoteLineId<>0  order by RoutingSequence";
				//print "SQL: $sql_qtytime";
				
				//Read through the results
				foreach($gpcompanydb->query($sql_qtytime) as $time) { //SQL quantity time loop
		?>	
			<td class="timenumber"><?php print number_format($time["QtyTime"],2); ?></td>
		<?php 
					//Update total time for quantity
					$coltotal[$key]=$coltotal[$key]+$time["QtyTime"];
				} //Ends SQL quantity time loop
			} //Ends quantity time loop
		?>
		</tr>
		<?php
		//Displat the cost row
		?>
		<tr style="background-color:<?php print $color; ?>">
			<td style="border-top: 1px solid #9b9b9b;">Cost</td>
			<td class="costnumber" style="border-top: 1px solid #9b9b9b;"><?php print "$".number_format($cost["SetupLaborCost"],2); ?></td>			
			<td class="costnumber" style="border-top: 1px solid #9b9b9b;"><?php print "$".number_format($cost["RunLaborCost"],2); ?></td>
			<td class="costnumber" style="border-top: 1px solid #9b9b9b;">$0.00</td>	
			<td class="costnumber" style="border-top: 1px solid #9b9b9b;"><?php print "$".number_format($cost["MachineCost"],2); ?></td>
			<td class="costnumber" style="border-top: 1px solid #9b9b9b;"><?php print "$".number_format($cost["BaseCost"],2); ?></td>
		<?php 
			//Read through the quantities to quantity costs
			foreach($qty as $key=>$val) {  //Quantity cost loop
				//The SQL query to calculte the costs for quantity
				$sql_qtycost="select ((SetupLaborRate+MFGOHRate)*(SetupTime/60))+((RunLaborRate+MFGOHRate)*((RunLaborTime*QTY)/60))+(MachineRate*((MachineTime*QTY)/60)) QtyTimeCost from DUREX_EstimateQuantity q inner join DUREX_EstimateRouting r on r.EstimateId=q.EstimateId where r.EstimateId=$estimateid and QTY=$val and RoutingSequence=$seq and WorkCenterId='$wcid' and q.QuoteLineId<>0";
				//Read through the results
				foreach($gpcompanydb->query($sql_qtycost) as $qtycost) {  // SQL quantity cost loop
				//Display the results in a column
		?>	
			<td class="costnumber" style="border-top: 1px solid #9b9b9b;"><?php print "$".number_format($qtycost["QtyTimeCost"],2); ?></td>
		<?php 
				}  //Ends SQL quantity time loop
			}  //Ends Quantity cost loop
		?>	
		</tr>
<?php
		//Increment total time for base
		$total_based=$total_based+$cost["BaseTime"];
	}  //End for SQL for labor costs
	
	//Increment record count 
	$reccount=$reccount+1; 
	
	//Determine if odd or even record
	$level=($reccount % 2);
	
	//Set the row color for odd or even row
	if ($level==0) { //If level is zero then even
		$color="#cde7f7";
	}  
	else {  //Anything else is an odd row
		$color="#f7f7f7";
	}
	
	//Display total lines
?>
		<tr style="background-color:<?php print $color; ?>">
			<td colspan="7" style="border-top: 1px double #9b9b9b;font-weight:700; text-align:right; ">Total Time (minutes):</td>
			<td style="border-top: 1px double #9b9b9b;font-weight:700; text-align:right;"><?php print number_format($total_based,2); ?></td>
		<?php 
			//Loop through the quantities for total row
			foreach($qty as $key=>$val) {  //Loop through quantities
		?>
			<td style="border-top: 1px double #9b9b9b;font-weight:700; text-align:right;"><?php print number_format($coltotal[$key],2); ?></td>
		<?php
			}  //End loop for quantities 
		?>
		</tr>
		<tr style="background-color:<?php print $color; ?>">
			<td colspan="7" style="border-top: none;font-weight:700; text-align:right; ">Total Time (Hours):</td>
			<td style="border-top: none;font-weight:700; text-align:right;"><?php print number_format(($total_based/60),2); ?></td>
		<?php 
			//Loop through the quantities for total row		
			foreach($qty as $key=>$val) {  //Loop through quantities
		?>
			<td style="border-top: none;font-weight:700; text-align:right;"><?php print number_format(($coltotal[$key]/60),2); ?></td>
		<?php
			}  //End loop for quantities
		?>
		</tr>

	</table>