<?php
	//Default SQl querys 
	$estimateid=$_REQUEST["estimate"];

	
	//Connect to the GP database.  If error, display the error message and exit.
	include 'dbconn.php';
	
?>
	<table class="table" style="margin-top: 5px;background-color:#ffffff;" id="mytable" style="width:1061px;">
		<tr style="color:#000000; background-color:#9bd1f7">
			<th style="width:3%;border-right: 1px ridge #636161"><span data-toggle="modal" data-target=".modal" data-modtitle="Add Note" data-modsize="1024px" data-modsav="misc" data-paragraphs="?estimate=<?php print $_REQUEST["estimate"]; ?>" data-url="addnote.php"><img src="/images/add.png" alt="delete" width="25" height="25" data-toggle="tooltip" data-placement="top" title="Add Note"></span></th>
			<th style="width:10%; border-right: 1px ridge #636161">Date</th>
			<th style="width:10%; border-right: 1px ridge #636161">Added By</th>			
			<th style="width:60%;border-right: 1px ridge #636161">Note</th>
			<th style="width:10%;border-right: 1px ridge #636161">Type</th>
			<th style="column-width:25px;"></th>
		</tr>
	<?php
		$bomline=0;
		//foreach($db->query($sql_BOM) as $bom) {
/*			$routeflag=0;
			$bomline=$bomline+1;
			$itemnum=$bom["ITEMNMBR"];
			$note=$bom["Note"];
			$bomid=$bom["EstimateBOMId"];
			$bomStockingType=$bom["StockingType"]; */
	?>
		<tr style="background-color: #fffff;">
			<td style="width:3%"><span data-toggle="modal" data-target="#modal-1" data-modtitle="Edit Charge" data-modtitle="Edit Note" data-modsize="1024px" data-modsav="misccharge" data-paragraphs="?estimate=<?php print $_REQUEST["estimate"]; ?>&itemnumber=<?php print  urlencode($itemnum); ?>" data-url="addnote.php"><img src="/images/edit.png" alt="edit" width="25" height="25" data-toggle="tooltip" data-placement="top" title="Edit"></span></td>				
			<td style="width:10%">03/13/2019</td>
			<td style="width:10%">BMACE</td>
			<td style="width:60%;">This is a sample estimate note</td>
			<td style="width:15%;">Internal</td>
			<td><span data-toggle="modal" data-target=".modal" data-modtitle="Delete Charge" data-modfooter="hide" data-modsize="512px" data-modsav="misccharge" data-paragraphs="?bomid=<?php print $bomid; ?>" data-url="deletecomponent.php"><img src="/images/delete.png" alt="delete" width="25" height="25" data-toggle="tooltip" data-placement="top" title="Delete"></span></td>		
		</tr>
		<tr style="background-color: #fffff;">
			<td style="width:3%"><span data-toggle="modal" data-target="#modal-1" data-modtitle="Edit Charge" data-modtitle="Edit Note" data-modsize="1024px" data-modsav="misccharge" data-paragraphs="?estimate=<?php print $_REQUEST["estimate"]; ?>&itemnumber=<?php print  urlencode($itemnum); ?>" data-url="addcomponent.php"><img src="/images/edit.png" alt="edit" width="25" height="25" data-toggle="tooltip" data-placement="top" title="Edit"></span></td>				
			<td style="width:10%">03/13/2019</td>
			<td style="width:10%">BMACE</td>
			<td style="width:60%;">This is a special note that shuold appear on the quote. </td>
			<td style="width:15%;">Quote</td>
			<td><span data-toggle="modal" data-target=".modal" data-modtitle="Delete Charge" data-modfooter="hide" data-modsize="512px" data-modsav="misccharge" data-paragraphs="?bomid=<?php print $bomid; ?>" data-url="deletecomponent.php"><img src="/images/delete.png" alt="delete" width="25" height="25" data-toggle="tooltip" data-placement="top" title="Delete"></span></td>		
		</tr>
	</tbody>

	<?php
		//}
	?>

	</table>
</html>
