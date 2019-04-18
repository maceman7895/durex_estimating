<?php
	// security check
	//include 'security.php';
	include 'dbconn.php';
	//print "Time & Cost Breakdown";
	
	//Get the estimate id passed 
	if (isset($_REQUEST["estimateid"])) {
		$estimateid=$_REQUEST["estimateid"];
	}
	else { 
		$estimateid=0;
	}
	
	$qty=array();
	$coltotal=array();
	$estReadyForApproval=0;
	
	$sql_review="EXEC sp_DUREX_EstimateReview $estimateid";
	$sql_quantities="select * from DUREX_EstimateQuantity where EstimateId=$estimateid and QuoteLineId<>0";
	$sql_estimate="select * from DUREX_Estimates where EstimateId=$estimateid";
	
	foreach($db->query($sql_estimate) as $estimate) {
		$estReadyForApproval=$estimate["ReadyForApproval"];
	} 
	
	
	$total_based=0.00;
	//print "SQL: $sql_timecost<br>";
	
	foreach($db->query($sql_quantities) as $qtys) {
		$qty[]=number_format($qtys["QTY"],0);
		$coltotal[]=0;
		$qtyid[]=$qtys["EstimateQtyId"];
	}
	
	$label=300;
	$number=125;
	$percent=50;
	$tablesize=300+(($number+$percent)*(sizeof($qty)+1));
	$tablecol=3+((sizeof($qty)*2));
	
	//print "Table size: $tablesize<br>";
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
	<table class="table" style="margin-top: 5px;background-color:#ffffff;width:<?php print $tablesize; ?>px;" id="reviewTable">
		<tr style="color:#000000; background-color:#f2d08c">
			<th style="border-right: 1px ridge #636161;text-align: center"></th>
			<th colspan="2" style="border-right: 1px ridge #636161;text-align: center;">BASE</th>
			<th colspan="<?php print sizeof($qty)*2; ?>" style="border-right: 1px ridge #636161;text-align: center"> Requested Quantities </th>
		</tr>
		<tr style="color:#000000; background-color:#f2d08c">
			<th style="border-top: none; border-right: 1px ridge #636161; text-align: center; width:<?php print $label; ?>px"></th>
			<th style="border-top: 1px solid black;border-right: 1px ridge #636161;text-align: center; width:<?php print $number; ?>px;">1</th>
			<th style="border-top: 1px solid black;border-right: 1px ridge #636161; text-align:center; width:<?php print $percent; ?>px;">%</th>			
		<?php 
			foreach($qty as $key=>$val) {
		?>
			<th style="border-top: 1px solid black;border-right: 1px ridge #636161; text-align: center; width:<?php print $number; ?>px;"> <?php print $val; ?></th>	
			<th style="border-top: 1px solid black;border-right: 1px ridge #636161; text-align: center; width:<?php print $percent; ?>px;">%</th>	
		<?php
			}
		?>
		</tr>
		<?php
			$stmt = $db->query($sql_review);
			do {
			   $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			   if ($rows) {
				   $results=$rows;
			   }
			} while ($stmt->nextRowset());

			foreach($results as $review) { 
				$totalline=$review["TotalLine"];
				if (substr($review["Costline"],0,8)=="Approved") {
					$approveline=1;
				}
				else { 
					$approveline=0;
				}
				if (!$estReadyForApproval && $approveline) {
				}
				else {
		?>
		<tr>
			<td <?php if ($approveline) { print "colspan=3"; } ?> style="<?php if ($approveline) { print "background-color: #f7eacf;"; } ?>width:<?php print $label; ?>px;<?php if ($totalline) { print "border-top: 1px solid black;"; } ?>"><?php print $review["Costline"]; ?></td>
			<?php if (!$approveline) { ?>
			<td class="timenumber" style="background-color: #f7eacf; width:<?php print $number; ?>px;<?php if($totalline) { print "border-top: 1px solid black;"; }?> "><?php print "$".number_format($review["Base1"],2); ?></td>
			<?php
				}
				switch ($review["Costline"]) {
					 case "Mark Up %": ?>
			<td class="timenumber" style="background-color: #f7eacf;font-size:.9em;width:<?php print $percent; ?>px;<?php if($totalline) { print "border-top: 1px solid black;"; }?>"><?php print number_format($review["Base1Percent"],1); ?></td>
			<?php
						break; 
					case "Commission": 
			?>
			<td class="timenumber" style="background-color: #f7eacf;font-size:.9em;width:<?php print $percent; ?>px;<?php if($totalline) { print "border-top: 1px solid black;"; }?>"><?php print number_format($review["Base1Percent"],1); ?></td>
		<?php
						break;
					default:
						if (!$approveline) {
			?>
			<td class="timenumber" style="background-color: #f7eacf;font-size:.9em;width:<?php print $percent; ?>px;<?php if($totalline) { print "border-top: 1px solid black;"; }?>"><?php print number_format($review["Base1Percent"],1); ?></td>
		<?php 
						}
						break;
			}
				foreach($qty as $key=>$val) {
					if ($review["Costline"]=="Approved Sell Price") {
		?>
			<td class="timenumber" style="width:<?php print $number; ?>px;<?php if($totalline) { print "border-top: 1px solid black;"; }?>"><input type="number" id="priceQTY<?php print $qtyid[$key]; ?>" value="<?php print number_format($review["QTY".$val],2); ?>" style="width:100px;text-align:right" onchange="updatePrice('<?php print $qtyid[$key]; ?>');"></td>
		<?php 
					}
					else {
		?>
			<td class="timenumber" style="width:<?php print $number; ?>px;<?php if($totalline) { print "border-top: 1px solid black;"; }?>"><?php print "$".number_format($review["QTY".$val],2); ?></td>
			<?php 
					}
				switch ($review["Costline"]) {
					 case "Mark Up %": ?>
			<td class="timenumber" style="font-size:.9em;width:<?php print $percent; ?>px;<?php if($totalline) { print "border-top: 1px solid black;"; }?>"><input type="number" id="markupQTY<?php print $qtyid[$key]; ?>" value="<?php print number_format($review["QTY".$val."Percent"],1); ?>" style="width:50px;text-align:right" onchange="updateMarkup('<?php print $qtyid[$key]; ?>');"></td>
			<?php
						break; 
					case "Commission": 
			?>
			<td class="timenumber" style="font-size:.9em;width:<?php print $percent; ?>px;<?php if($totalline) { print "border-top: 1px solid black;"; }?>"><input type="number" id="commQTY<?php print $qtyid[$key]; ?>"  value="<?php print number_format($review["QTY".$val."Percent"],1); ?>" style="width:50px;text-align: right;" onchange="updateCommission('<?php print $qtyid[$key]; ?>');"></td>
			<?php
						break;
					default:
			?>			
			<td class="timenumber" style="font-size:.9em;width:<?php print $percent; ?>px;<?php if($totalline) { print "border-top: 1px solid black;"; }?>"><?php print number_format($review["QTY".$val."Percent"],1); ?></td>
		<?php
				}
			}
		?>			
		</tr>
		<?php
			}
		}
		?>
	</table>

	
<script> 
	function updateMarkup(e) {
		//alert("Markup Changed: "+e);
		var obj="#markupQTY"+e;
		var markup=$(obj).val();
		//alert("Data: " + markup);

		$.post("updatemarkup.php", {
			qtyid: e,
			markup: markup
		}, function (data, status) {
			// close the popup
			//alert("Status: "+status);
			//alert("Data: "+data);
			// read records again
			//readRecords();
	 
			// clear fields from the popup
		});
		
		$('#review-tab').click();
	}
	
	function updateCommission(e) {
		//alert("Markup Changed: "+e);
		var obj="#commQTY"+e;
		var commission=$(obj).val();
		//alert("Data: " + markup);

		$.post("updatecommission.php", {
			qtyid: e,
			commission: commission
		}, function (data, status) {
			// close the popup
			//alert("Status: "+status);
			//alert("Data: "+data);
			// read records again
			//readRecords();
	 
			// clear fields from the popup
		});
		
		$('#review-tab').click();
	}
	
	function updatePrice(e) {
		//alert("Price: "+e);
		var obj="#priceQTY"+e;
		//alert('Obj: '+ obj);
		var price=$(obj).val();
		//alert("Data: " +price);

		$.post("updatePrice.php", {
			qtyid: e,
			price: price
		}, function (data, status) {
			// close the popup
			//alert("Status: "+status);
			//alert("Data: "+data);
			// read records again
			//readRecords();
	 
			// clear fields from the popup
		});
		
		$('#review-tab').click();
	}
</script>