<?php
	// security chec
	include 'security.php';
	//Default SQl querys 
	$estimateid=$_REQUEST["estimate"];
	$prodgroups=array("Cast Heaters", "Cartridge Heaters", "Straight Tubular Heaters", "Formed Tubular Heaters");
	$sql_prodgroups="select * from IV40600 where USCATNUM=1 and USCATVAL not in( select ProductTypeCode from [DUREX_Estimate ProductType])";  //Query for the product groups from GP
	$sql_estimators="select USERID, USERNAME from DYNAMICS..SY01400 where UserStatus=1 order by USERNAME";  //Query for getting the mask for a product group
	$sql_quotedata="select SOPNUMBE as QuoteNumber, CUSTNAME, (select CUSTCLAS from RM00101 where CUSTNMBR=q.CUSTNMBR) as CustomerType,CNTCPRSN AS Contact, PHNUMBR1 as PhoneNumber, * from SOP10100 q where SOPNUMBE='QT000019'";
	$sql_estimate="select * from DUREX_Estimates e where EstimateId=".$_REQUEST["estimate"]; 
	$sql_quantity="select EstimateQtyId, EstimateId, QuoteLineId, cast(QTY as int) as QTY  from DUREX_EstimateQuantity where EstimateId=".$_REQUEST["estimate"]." order by QuoteLineId";
	$sql_BOM="select EstimateBOMId, EstimateId, RTRIM(ITEMNMBR) as ITEMNMBR, StandardCost, CAST(ITEMDESC as varchar(30)) as ITEMDESC,  (select USCATVLS_3 from IV00101 where ITEMNMBR=b.ITEMNMBR) as StockingType, CAST(ROUND(QTY,2) as Decimal(18,2)) as QTY, Noninventory, UofM, Note, (select count(*) from  DUREX_EstimateBOM where EstimateId=b.EstimateId and ParentEstimateBOMId=b.EstimateBOMId) as Children, (select count(*) from DUREX_EstimateRouting where EstimateId=b.EstimateId and Subparentid=b.EstimateBOMId) as estimateroutes, (select count(*) from RT010001 where ITEMNMBR=b.ITEMNMBR) as gproutecount, (select count(*) from WO010032 where ITEMNMBR=b.ITEMNMBR) as mocount from DUREX_EstimateBOM b where EstimateId='$estimateid' and ParentEstimateBOMId=0 order by EstimateBOMId";
	//print "SQL: $sql_BOM";
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
		$qtyline[$num]=$qty["QuoteLineId"];
		$qtytotal[$num]=0;
		$num=$num+1;
		$linenum=$qty["QuoteLineId"]/16384;
		if ($lines!="") {
			$lines=$lines.",".$linenum;
		}
		else {
			$lines=strval($linenum);
		}
	}
	
	$editcol=26;
	$othersymcol=110;
	$linenumcol=75;
	$partnumcol=150;
	$desccol=300;
	$stotypcol=70;
	$qtycol=85;
	$stndcostcol=100;
	$uofmcol=90;
	$costcol=100;
	$notecol=300;
	$delcol=20;

	$tablesize=$editcol+$othersymcol+$linenumcol+$partnumcol+$desccol+$stotypcol+$qtycol+$uofmcol+$costcol+$notecol+$delcol+((sizeof($quantity)*$costcol));
	//print "Table Size: $tablesize";
?>
	<table class="table" style="margin-top: 5px;background-color:#ffffff; width:<?php print $tablesize; ?>px;" id="bomtable">
		<tr style="color:#000000; background-color:#9bd1f7">
			<th style="width:<?php print $editcol; ?>px;border-right: 1px ridge #636161"><br><span data-toggle="modal" data-target=".modal" data-modtitle="Add A Component" data-modsize="1024px" data-modsav="component" data-paragraphs="?estimate=<?php print $_REQUEST["estimate"]; ?>" data-url="addcomponent.php"><img src="/images/add.png" alt="delete" width="25" height="25" data-toggle="tooltip" data-placement="top" title="Add component"></span></th>
			<th style="width:<?php print $othersymcol; ?>px;"><br></th>
			<th style="width:<?php print $linenumcol; ?>px; border-right: 1px ridge #636161"><br>Line #</th>
			<th style="width:<?php print $partnumcol; ?>px;border-right: 1px ridge #636161"><br>Part Number</th>
			<th style="width:<?php print $desccolcol; ?>px;border-right: 1px ridge #636161"><br>Description</th>
			<th style="width:<?php print $stotypcol; ?>px;border-right: 1px ridge #636161">Stocking<br>Type</th>			
			<th style="width:<?php print $qtycol; ?>px;border-right: 1px ridge #636161;text-align: center"><br>QTY</th>
			<th style="width:<?php print $stndcostcol; ?>px;border-right: 1px ridge #636161;text-align: center">Item STD. Cost</th>
			<th style="width:<?php print $uofmcol; ?>px;border-right: 1px ridge #636161;text-align: center; padding:0px"><br>U of M</th>
			<?php
				foreach($quantity as $key=>$val) {
					//if ($val!=0) {
					if ($qtyline[$key]==0) { 
			?>
			<th style="width:<?php print $costcol; ?>px;border-right: 1px ridge #636161;text-align: center">Base<br>(<?php print $val; ?>)</th>
			<?php	
					}
					else {
			?>
			<th style="width:<?php print $costcol; ?>px;border-right: 1px ridge #636161;text-align: center">Cost<br>(<?php print $val; ?>)</th>
			<?php
					}
				}
			?>
			<th style="width:<?php print $notecol; ?>px;;border-right: 1px ridge #636161"><br>Notes</th>
			<th style="column-width:<?php print $delcol; ?>px;"><div class="<?php if($bomcount!=0) { print 'invisible'; } else { print "visible"; } ?>"><br><span data-toggle="modal" data-target=".modal" data-modfooter="hide" data-modtitle="Copy BOM" data-modsize="512px" data-modsav="copybom" data-paragraphs="?estimate=<?php print $_REQUEST["estimate"]; ?>" data-url="copybom.php"><img src="/images/bomcopy.png" alt="delete" width="25" height="25" data-toggle="tooltip" data-placement="top" title="Copy BOM"><span></div></th>
		</tr>
	<?php
		$bomline=0;
		//print "SQL: $sql_BOM";
		foreach($db->query($sql_BOM) as $bom) {
			$routeflag=0;
			$bomline=$bomline+1;
			$itemnum=$bom["ITEMNMBR"];
			$note=$bom["Note"];
			$bomid=$bom["EstimateBOMId"];
			$bomStockingType=$bom["StockingType"];
			$bomMOcount=$bom["mocount"];
			if ($bom["Children"]!=0) {
					$children=1;
			}
			else {
				$children=0;
			}
			$bomNonInv=$bom["Noninventory"];
			//Set flag whether a route exist or not
			if ($bom["estimateroutes"]==0 && $bom["gproutecount"]==0) {
				$routeflag=0;
			}
			
			if ($bom["estimateroutes"]>0) {
				$routeflag=1;
			}
			
			if (($bom["gproutecount"]>0 && $bom["estimateroutes"]==0) || ($bomNonInv==1 && $bom["estimateroutes"]==0)) {
				$routeflag=2;
			}
			
	?>
	<tbody id="component<?php print $bomid; ?>">
		<tr style="background-color: #fafafa;">
			<td style="width:<?php print $editcol; ?>px"><span data-toggle="modal" data-target="#modal-1" data-modtitle="Edit Component" data-modfooter="hide" data-modtitle="Edit Component" data-modsize="1024px" data-modsav="component" data-paragraphs="?estimate=<?php print $_REQUEST["estimate"]; ?>&itemnumber=<?php print  urlencode($itemnum); ?>" data-url="addcomponent.php"><img src="/images/edit.png" alt="edit" width="20" height="20" data-toggle="tooltip" data-placement="top" title="Edit"></span></td>				
			<td style="width:<?php print $othersymcol; ?>px; padding:0px;margin: 0px; vertical-align: middle;">
				<?php
				if ($children==1) {
				?>
				<a onclick="getchildbom(<?php print "$estimateid, $bomid, $bomline"; ?>); "><img id="bom_childIndicator<?php print $bomid; ?>" src="images/plus.png" width="30" height="30"   data-toggle="tooltip" data-placement="top" title="Toggle BOM" ></a>
				<span data-toggle="modal" data-target="#modal-2" data-modtitle="Route for <?php print $itemnum; ?>" data-modsize="1280px" data-modsav="route" data-paragraphs="?estimate=<?php print $_REQUEST["estimate"]; ?>&bomid=<?php print $bomid; ?>" data-url="routing.php"><a onclick="getrouterbom(<?php print "$estimateid, $bomid, '$bomline.$childline'"; ?>);"><img src="images/<?php switch ($routeflag) { case 0: print "route_icon"; break; case 1: print "greenroute"; break; case 2: print "blueroute"; break; }?>.png" width="15" height="15"   data-toggle="tooltip" data-placement="top" title="<?php switch ($routeflag) { case 0: print "Route Missing"; break; case 1: print "Review Route"; break; case 2: print "Add route"; break; }?>"></a></span>
				<?php if ($bomMOcount!=0) { ?>
				<span data-toggle="modal" data-target="#modal-2" data-modtitle="MO Report for <?php print $itemnum; ?>" data-modsize="1400px" data-modsav="route" data-paragraphs="?estimate=<?php print $_REQUEST["estimate"]; ?>&itemnumber=<?php print $itemnum; ?>" data-url="moreport.php"><a><img src="images/moreport.png" width="20" height="20"   data-toggle="tooltip" data-placement="top" title="MO Report"></a></span>
				
				<?php 
						}
					}
				
				?>
				<?php 
					if (($bomNonInv==0 && $children==1) || $bomNonInv==1) {
				?>
				
				<span data-toggle="modal" data-target="#modal-1" data-modtitle="<?php if ($children==0) { print "Make Sub Bom"; } else { print "Add A Component"; } ?>" data-modsize="1024px" data-modsav="component" data-paragraphs="?estimate=<?php print $_REQUEST["estimate"]; ?>&parentid=<?php print $bomid; ?>" data-url="addcomponent.php"><img src="/images/add.png" alt="delete" width="20" height="20" data-toggle="tooltip" data-placement="top" title="<?php if ($children==0) { print "Make Sub Bom"; } else { print "Add A Component"; } ?>"></span>
				<?php
					}
				?>
				</td>			
			<td style="width:<?php print $linenumcol; ?>px;text-align: right"><?php print $bomline; ?></td>
			<td style="width:<?php print $partnumcol; ?>px">
				<?php if ($bomNonInv==0) { ?>
					<a href="dgpp://DGPB/?Db=&Srv=<?php print $server; ?>&Cmp=<?php print $company; ?>&Prod=3830&Act=OPEN&Func=ITEM&ITEMNMBR=<?php print $itemnum; ?>">
				<?php } ?>
					<?php print $bom["ITEMNMBR"]; ?>
				<?php if ($bomNonInv==0) { ?>
					</a>
				<?php } ?>
			</td>
			<td style="width:<?php print $desccol; ?>px;"><?php print $bom["ITEMDESC"]; ?></td>
			<td style="width:<?php print $stotypcol; ?>px;"><?php print $bomStockingType; ?></td>
			<td style="width:<?php print $qtycol; ?>px;text-align: right"><?php print $bom["QTY"]; ?></td>
			<td style="width:<?php print $stndcostcol; ?>px;text-align: right"><?php print $bom["StandardCost"]; ?></td>
			<td style="width:<?php print $uofmcol; ?>px;text-align: right"><?php print $bom["UofM"]; ?></td>		
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
				$stmt = $db1->prepare("select b.EstimateId, b.ITEMNMBR, QuoteLineId, ROUND(ISNULL(UnitCost,0)+.004,2) as UnitCost, CAST(ROUND((((b.QTY*ROUND(ISNULL(UnitCost,0)+.004,2)))),2) as DECIMAL(18,2)) as ExtCost  
										from DUREX_EstimateBOM b 
											inner join DUREX_EstimateQuantity q on q.EstimateId=b.EstimateId
											left outer join DUREX_EstimateBOMCosts c on c.EstimateQuantityId=q.EstimateQtyId and c.EstimateBOMId=b.EstimateBOMId 
										where b.EstimateBOMId=:id order by QuoteLineId");
				$stmt->bindValue(':id', $bomid, PDO::PARAM_INT);
				//$stmt->bindValue(':item', $itemnum, PDO::PARAM_STR);
				$stmt->execute(); 
				//print "Results:<br>";
				//print_r($stmt->fetch());
				

				//$user = $stmt->fetch();				
				//while ($row = $stmt->fetch()) {
				//	echo $row['UnitCost']."<br />\n";
				//}
				//foreach($db->query($sql_costs) as $costs) {
				$num1=0;
				foreach($stmt as $costs) {
					$qtytotal[$num1]=$qtytotal[$num1]+$costs["ExtCost"];
			?>
			<td style="width:<?php print $costcol; ?>px;text-align: right" data-toggle="tooltip" data-html="true" title="Unit Cost: <?php print "$".number_format($costs["UnitCost"],2); ?>"><?php print $costs["ExtCost"]; ?></td>
			<?php
					$num1=$num1+1;
				}
			?>
			<td style="width:<?php print $notecol; ?>px;"><?php print $bom["Note"]; ?></td>
			<td style="width:<?php print $delcol; ?>px;"><span data-toggle="modal" data-target=".modal" data-modtitle="Delete Component" data-modfooter="hide" data-modsize="512px" data-modsav="component" data-paragraphs="?bomid=<?php print $bomid; ?>" data-url="deletecomponent.php"><img src="/images/delete.png" alt="delete" width="20" height="20" data-toggle="tooltip" data-placement="top" title="Delete"></span></td>		
		</tr>
	</tbody>

	<?php
		}
	?>
		<tr>
			<td colspan="9" style="text-align:right"><b>Totals:</b></td>
			<?php
				foreach($qtytotal as $key => $val) {
			?>
			<td style="width:<?php print $costcol; ?>px;text-align: right"><b><?php print number_format($val,2); ?></b></td>
			<?php
				}
			?>
			<td colspan="2"></td>
		</tr>
	</table>
</html>
