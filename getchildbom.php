<?php 
	// security check
	include 'security.php';
	include 'dbconn.php';
	
	$estiamteid="";
	$parentitem="";
	$bomline="";
	
	//Get the estimate id passed to routine
	if (isset($_REQUEST["estimateid"])) {
		$estimateid=$_REQUEST["estimateid"];
	}
	
	//Get the parent item number passed to routine
	if (isset($_REQUEST["parentitem"])) {
		$parentitem=$_REQUEST["parentitem"];
	}

	//Get the parent item number passed to routine
	if (isset($_REQUEST["bomline"])) {
		$bomline=$_REQUEST["bomline"];
	}

	$sql_bom="select EstimateBOMId, EstimateId, RTRIM(ITEMNMBR) as ITEMNMBR, StandardCost, CAST(ITEMDESC as varchar(30)) as ITEMDESC,  (select USCATVLS_3 from IV00101 where ITEMNMBR=b.ITEMNMBR) as StockingType, CAST(ROUND(QTY,2) as Decimal(18,2)) as QTY, Noninventory, UofM, Note, (select count(*) from  DUREX_EstimateBOM where EstimateId=b.EstimateId and ParentEstimateBOMId=b.EstimateBOMId) as Children, (select count(*) from DUREX_EstimateRouting where EstimateId=b.EstimateId and Subparentid=b.EstimateBOMId) as estimateroutes, (select count(*) from RT010001 where ITEMNMBR=b.ITEMNMBR) as gproutecount, (select count(*) from WO010032 where ITEMNMBR=b.ITEMNMBR) as mocount from DUREX_EstimateBOM b where EstimateId=:estnum and ParentEstimateBOMId=:parentitem";
	$sql_bom_params=array(":estnum", ":parentitem");
	$sql_qtycount="select count(*) as qtycount from DUREX_EstimateQuantity where EstimateId=$estimateid";	
	
	foreach ($db->query($sql_qtycount) as $count) {
		$qtycount=$count["qtycount"];
	}
	
	$level=substr_count($bomline, ".")+1;

	$editcol=26;
	$othersymcol=110;
	$linenumcol=75;
	$partnumcol=150;
	$desccol=310;
	$stotypcol=70;
	$stndcostcol=100;
	$qtycol=90;
	$uofmcol=90;
	$costcol=100;
	$notecol=300-(($level-1)*10);
	$delcol=20;
	
	$tablesize=$editcol+$othersymcol+$linenumcol+$partnumcol+$desccol+$stotypcol+$qtycol+$uofmcol+$costcol+$notecol+$delcol+(($qtycount*$costcol));
	$colspan=10+$qtycount;
	
	//print $_REQUEST["bomline"]."<br>";
	
	//If estimate id and parent item is passsed run lookup
	if ($estimateid!=0 && $parentitem!=0) {
		//Set the parameter values array
		$sql_bom_param_values=array($estimateid, $parentitem);

		//Replace the parameter with values
		$sql_bom=str_replace($sql_bom_params, $sql_bom_param_values, $sql_bom);
		//print "$sql_bom<br>";
	?>
	<tbody id="component<?php print $parentitem; ?>child">
		<tr>
			<td colspan=<?php print $colspan;?>>
				<?php //print "Table size: $tablesize"; ?>
				<table class="table table-light" style="margin: 0px;width:<?php //print $tablesize; ?>100%;">
					<?php
						$childline=0;
						//print "<br>".$sql_bomchild;
						foreach($db->query($sql_bom) as $child) {
							$childline=$childline+1;
							$itemnum=$child["ITEMNMBR"];
							$note=$child["Note"];
							$bomid=$child["EstimateBOMId"];
							$bomStockingType=$child["StockingType"];
							$bomMOcount=$child["mocount"];
							if ($child["Children"]!=0) {
								$children=1;
							}
							else {
								$children=0;
							}
							$bomNonInv=$child["Noninventory"];
							//Set flag whether a route exist or not
							if ($child["estimateroutes"]==0 && $child["gproutecount"]==0) {
								$routeflag=0;
							}
							
							if ($child["estimateroutes"]>0) {
								$routeflag=1;
							}
							
							if (($child["gproutecount"]>0 && $child["estimateroutes"]==0) || ($bomNonInv==1 && $child["estimateroutes"]==0)) {
								$routeflag=2;
							}
					?>
					<tbody id="component<?php print $bomid; ?>">
					<tr>
						<td style="width:<?php print $editcol; ?>px;"><span data-toggle="modal" data-target=".modal" data-modtitle="Edit Component" data-modtitle="Edit Component" data-modsize="1024px" data-modsav="component" data-paragraphs="?estimate=<?php print $_REQUEST["estimateid"]; ?>&itemnumber=<?php print  urlencode($itemnum); ?>" data-url="addcomponent.php"><img src="/images/edit.png" alt="edit" width="20" height="20" data-toggle="tooltip" data-placement="top" title="Edit"></span></td>				
						<td style="width:<?php print $othersymcol; ?>px; padding:0px;margin:0px;">
							<?php
							if ($children==1) {
							?>
							<a onclick="getchildbom(<?php print "$estimateid, $bomid, $bomline.$childline"; ?>);"><img id="bom_childIndicator<?php print $bomid; ?>" src="images/plus.png" width="30" height="30"   data-toggle="tooltip" data-placement="top" title="Toggle BOM" ></a>
							<span data-toggle="modal" data-target=".modal" data-modtitle="Route for <?php print $itemnum; ?>" data-modsize="1280px" data-modsav="route" data-paragraphs="?estimate=<?php print $estimateid; ?>&bomid=<?php print $bomid; ?>" data-url="routing.php"><a onclick="getrouterbom(<?php print "$estimateid, $bomid, '$bomline.$childline'"; ?>);"><img src="images/<?php switch ($routeflag) { case 0: print "route_icon"; break; case 1: print "greenroute"; break; case 2: print "blueroute"; break; }?>.png" width="15" height="15"   data-toggle="tooltip" data-placement="top" title="<?php switch ($routeflag) { case 0: print "Route Missing"; break; case 1: print "Review Route"; break; case 2: print "Add route"; break; }?>"></a></span>
							<?php if ($bomMOcount!=0) { ?>
							<span data-toggle="modal" data-target="#modal-2" data-modtitle="MO Report for <?php print $itemnum; ?>" data-modsize="1400px" data-modsav="route" data-paragraphs="?estimate=<?php print $_REQUEST["estimateid"]; ?>&itemnumber=<?php print $itemnum; ?>" data-url="moreport.php"><a><img src="images/moreport.png" width="20" height="20"   data-toggle="tooltip" data-placement="top" title="MO Report"></a></span>

							<?php
									}
								}
							?>
							<?php 
								if (($bomNonInv==0 && $children==1) || $bomNonInv==1) {
							?>
							
							<span data-toggle="modal" data-target=".modal" data-modtitle="<?php if ($children==0) { print "Make Sub Bom"; } else { print "Add A Component"; } ?>" data-modsize="1024px" data-modsav="component" data-paragraphs="?estimate=<?php print $estimateid; ?>&parentid=<?php print $bomid; ?>" data-url="addcomponent.php"><img src="/images/add.png" alt="delete" width="20" height="20" data-toggle="tooltip" data-placement="top" title="<?php if ($children==0) { print "Make Sub Bom"; } else { print "Add A Component"; } ?>"></span>
							<?php
								}
							?>
							</td>
						</td>
						
						<td style="width:<?php print $linenumcol; ?>px;text-align:right;"><?php print $bomline.".".$childline; ?></td>
						<td style="width:<?php print $partnumcol; ?>px;padding-left:3px;padding-right:1px;">
							<?php if ($bomNonInv==0) { ?>
								<a href="dgpp://DGPB/?Db=&Srv=<?php print $server; ?>&Cmp=<?php print $company; ?>&Prod=3830&Act=OPEN&Func=ITEM&ITEMNMBR=<?php print $itemnum; ?>">
							<?php } ?>
								<?php print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$itemnum"; ?>
							<?php if ($bomNonInv==0) { ?>
								</a>
							<?php } ?>
						</td>
						<td style="width:<?php print $desccol; ?>px;padding-right:1px;"><?php print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$child["ITEMDESC"]; ?></td>
						<td style="width:<?php print $stotypcol; ?>px;"><?php print $bomStockingType; ?></td>
						<td style="width:<?php print $qtycol; ?>px;text-align:right;"><?php print $child["QTY"]; ?></td>
						<td style="width:<?php print $stndcostcol; ?>px;text-align: right"><?php print $child["StandardCost"]; ?></td>
						<td style="width:<?php print $uofmcol; ?>px;text-align:right;"><?php print $child["UofM"]; ?></td>		
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
							$stmt = $db1->prepare("select b.EstimateId, b.ITEMNMBR, QuoteLineId, b.QTY, ISNULL(UnitCost,0) as UnitCost, CAST(ROUND((((b.QTY*ROUND(ISNULL(UnitCost,0)+.004,2)))),2) as DECIMAL(18,2)) as ExtCost
													from DUREX_EstimateBOM b 
														inner join DUREX_EstimateQuantity q on q.EstimateId=b.EstimateId
														inner join DUREX_EstimateBOMCosts c on c.EstimateQuantityId=q.EstimateQtyId and c.EstimateBOMId=b.EstimateBOMId 
													where b.EstimateBOMId=:id");
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
							foreach($stmt as $costs) {
						?>
						<td style="width:<?php print $costcol; ?>px;text-align: right;" data-toggle="tooltip" data-html="true" title="Unit Cost: <?php print "$".number_format($costs["UnitCost"],2); ?>"><?php print $costs["ExtCost"]; ?></td>
						<?php
							}
						?>
						<td style="width:<?php print $notecol; ?>px;"><?php print $child["Note"]; ?></td>
						<td style="width:<?php print $delcol; ?>px;"><span data-toggle="modal" data-target=".modal" data-modtitle="Delete Component" data-modfooter="hide" data-modsize="512px" data-modsav="component" data-paragraphs="?bomid=<?php print $bomid; ?>" data-url="deletecomponent.php"><img src="/images/delete.png" alt="delete" width="20" height="20" data-toggle="tooltip" data-placement="top" title="Delete"></span></td>		
					</tr>
				<?php
					}
				?>
			</table></td></tr>
		</tbody>
	<?php		
		}
	?>