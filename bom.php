<style>
	// Class
	.visible {
	  visibility: visible;
	}
	.invisible {
	  visibility: hidden;
	}
</style>

<?php
	//Default SQl querys 
	$estimateid=$_REQUEST["estimate"];
	$prodgroups=array("Cast Heaters", "Cartridge Heaters", "Straight Tubular Heaters", "Formed Tubular Heaters");
	$sql_prodgroups="select * from IV40600 where USCATNUM=1 and USCATVAL not in( select ProductTypeCode from [DUREX_Estimate ProductType])";  //Query for the product groups from GP
	$sql_estimators="select USERID, USERNAME from DYNAMICS..SY01400 where UserStatus=1 order by USERNAME";  //Query for getting the mask for a product group
	$sql_quotedata="select SOPNUMBE as QuoteNumber, CUSTNAME, (select CUSTCLAS from RM00101 where CUSTNMBR=q.CUSTNMBR) as CustomerType,CNTCPRSN AS Contact, PHNUMBR1 as PhoneNumber, * from SOP10100 q where SOPNUMBE='QT000019'";
	$sql_estimate="select * from DUREX_Estimates e where EstimateId=".$_REQUEST["estimate"]; 
	$sql_quantity="select EstimateQtyId, EstimateId, QuoteLineId, cast(QTY as int) as QTY  from DUREX_EstimateQuantity where EstimateId=".$_REQUEST["estimate"];
	$sql_BOM="select EstimateBOMId, EstimateId, ITEMNMBR, CAST(ITEMDESC as varchar(30)) as ITEMDESC, CAST(ROUND(QTY,2) as Decimal(18,2)) as QTY, Noninventory, UofM, Note from DUREX_EstimateBOM where EstimateId=".$_REQUEST["estimate"];
	
	$sql_BOMCount="select count(*) as reccount from DUREX_EstimateBOM where EstimateId=".$_REQUEST["estimate"];
	$sql_cost="select b.EstimateId, b.ITEMNMBR, QuoteLineId, b.QTY, UnitCost from DUREX_EstimateBOM b 
		inner join DUREX_EstimateQuantity q on q.EstimateId=b.EstimateId	
		inner join DUREX_EstimateBOMCosts  c on c.EstimateQuantityId=q.EstimateQtyId  and c.EstimateBOMId=b.EstimateBOMId
			where b.EstimateId=:id and ITEMNMBR=:item";
	$quantity=Array();
	
	$qtenumber='';
	$qteCustomer='';
	$qteCustomerType='';
	$qteContact='';
	$qtePhone='';
	
	$estNumber='';
	$estQuote='';
	$estItem='';
	$estItemDesc='';
	
	$bomcount=0;
	
	//Connect to the GP database.  If error, display the error message and exit.
	include 'dbconn.php';
	
	foreach($db->query($sql_quotedata) as $quote) {
			$qtenumber=$quote["QuoteNumber"];
			$qteCustomer=$quote["CUSTNAME"];
			$qteCustomerType=$quote["CustomerType"];
			$qteContact=$quote["Contact"];
			$qtePhone=$quote["PhoneNumber"];
	}
	
	foreach($db->query($sql_estimate) as $estimate) {
			$estNumber=$estimate["EstimateId"];
			$estQuote=$estimate["SOPNUMBE"];
			$estItem=$estimate["ITEMNMBR"];
			$estItemDesc=$estimate["ITEMDESC"];
	}
	
	foreach($db->query($sql_BOMCount) as $row) {
			$bomcount=$row["reccount"];
	}	

	$num=0;
	$lines="";
	foreach($db->query($sql_quantity) as $qty) {
		$quantity[$num]=$qty["QTY"];
		$num=$num+1;
		$linenum=$qty["QuoteLineId"]/16384;
		if ($lines!="") {
			$lines=$lines.",".$linenum;
		}
		else {
			$lines=strval($linenum);
		}
	}
?>
	<table class="table table-striped" style="margin-top: 5px;background-color:#ffffff;">
		<tr>
			<th style="border-right: 1px ridge #636161"><br><span data-toggle="modal" data-target=".modal" data-modtitle="Add A Component" data-modsize="1024px" data-modsav="component" data-paragraphs="?estimate=<?php print $_REQUEST["estimate"]; ?>" data-url="addcomponent.php"><img src="/images/add.png" alt="delete" width="25" height="25" data-toggle="tooltip" data-placement="top" title="Add component"></span></th>
			<th style="column-width:27px; border-right: 1px ridge #636161"><br>Line #</th>
			<th style="column-width:175px;border-right: 1px ridge #636161"><br>Part Number</th>
			<th style="column-width:200px;border-right: 1px ridge #636161"><br>Description</th>			
			<th style="column-width:75px;border-right: 1px ridge #636161;text-align: center"><br>Quantity</th>
			<th style="column-width:75px;border-right: 1px ridge #636161;text-align: center"><br>U of M</th>
			<?php
				foreach($quantity as $key=>$val) {
					//if ($val!=0) {
			?>
			<th style="column-width:100px;border-right: 1px ridge #636161;text-align: center">Cost<br>(<?php print $val; ?>)</th>
			<?php
					//}
				}
			?>
			<th style="column-width:350px;border-right: 1px ridge #636161"><br>Notes</th>
			<th style="column-width:25px;"><div class="<?php if($bomcount!=0) { print 'invisible'; } else { print "visible"; } ?>"><br><span data-toggle="modal" data-target=".modal" data-modfooter="hide" data-modtitle="Copy BOM" data-modsize="512px" data-modsav="copybom" data-paragraphs="?estimate=<?php print $_REQUEST["estimate"]; ?>" data-url="copybom.php"><img src="/images/bomcopy.png" alt="delete" width="25" height="25" data-toggle="tooltip" data-placement="top" title="Copy BOM"><span></div></th>
		</tr>
	<?php
		$bomline=0;
		foreach($db->query($sql_BOM) as $bom) {
			$bomline=$bomline+1;
			$itemnum=$bom["ITEMNMBR"];
			$note=$bom["Note"];
			$bomid=$bom["EstimateBOMId"];
	?>
		<tr>
			<td><span data-toggle="modal" data-target=".modal" data-modtitle="Edit Component" data-modtitle="Edit Component" data-modsize="1024px" data-modsav="component" data-paragraphs="?estimate=<?php print $_REQUEST["estimate"]; ?>&itemnumber=<?php print  urlencode($itemnum); ?>" data-url="addcomponent.php"><img src="/images/edit.png" alt="edit" width="25" height="25" data-toggle="tooltip" data-placement="top" title="Edit"></span></td>				
			<td style="column-width:27px;text-align: right"><?php print $bomline; ?></td>
			<td style="column-width:175px;"><?php print $bom["ITEMNMBR"]; ?></td>
			<td style="column-width:200px;"><?php print $bom["ITEMDESC"]; ?></td>
			<td style="column-width:75px;text-align: right"><?php print $bom["QTY"]; ?></td>
			<td style="column-width:75px;text-align: right"><?php print $bom["UofM"]; ?></td>		
			<?php
				//$sql_costs=str_replace("&&itemnumber", $bom["ITEMNMBR"], $sql_cost);
				//print $sql_cost."<br>";
				//$stmt = $db->query($sql_costs);
				//$stmt = $db->prepare($sql_cost);
				try {
					$db1=new PDO("odbc:DRIVER={SQL Server};Server=DRX-VM-GPSQL01;Database=DTEST;UID=sa;PWD=Access123");
				}
				catch ( PDOException $e) {
					print $e->getMessage();
					exit;
				}
				//print "Item Number: ".$itemnum."<br>";
				$stmt = $db1->prepare("select b.EstimateId, b.ITEMNMBR, QuoteLineId, b.QTY, CAST(ROUND((UnitCost+.004),2) as DECIMAL(18,2)) as UnitCost 
										from DUREX_EstimateBOM b 
											inner join DUREX_EstimateQuantity q on q.EstimateId=b.EstimateId
											inner join DUREX_EstimateBOMCosts c on c.EstimateQuantityId=q.EstimateQtyId and c.EstimateBOMId=b.EstimateBOMId 
										where b.EstimateId=:id and ITEMNMBR=:item");
				$stmt->bindValue(':id', $estimateid, PDO::PARAM_INT);
				$stmt->bindValue(':item', $itemnum, PDO::PARAM_STR);
				$stmt->execute(); 
				//print "Results:<br>";
				//print_r($stmt->fetch());
				

				//$user = $stmt->fetch();				
				//while ($row = $stmt->fetch()) {
				//	echo $row['UnitCost']."<br />\n";
				//}
				//foreach($db->query($sql_costs) as $costs) {
				foreach($stmt as $costs) {
			?>
			<td style="column-width:100px;text-align: right"><?php print $costs["UnitCost"]; ?></td>
			<?php
				}
			?>
			<td style="column-width:350px;"><?php print $bom["Note"]; ?></td>
			<td><span data-toggle="modal" data-target=".modal" data-modtitle="Delete Component" data-modfooter="hide" data-modsize="512px" data-modsav="component" data-paragraphs="?bomid=<?php print $bomid; ?>" data-url="deletecomponent.php"><img src="/images/delete.png" alt="delete" width="25" height="25" data-toggle="tooltip" data-placement="top" title="Delete"></span></td>		
		</tr>
	<?php
		}
	?>
	</table>