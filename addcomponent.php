<?php
	// security check
	include 'security.php';
	//Default SQl querys 
	$estimateid=$_REQUEST["estimate"];
	$itemnumber='';
	$qtyida=0;
	$parentid=0;
	
	
	$bomId=0;
	$bomItemNumber='';
	$bomNonInventory=1;
	$bomItemDescription='';
	$bomQuantity=0;
	$bomUofM='';
	$bomNote='';
	$bomMaterialAdder=0;
	
	if (isset($_REQUEST["itemnumber"])) {
		$itemnumber=$_REQUEST["itemnumber"];
	}

	if (isset($_REQUEST["parentid"])) {
		$parentid=$_REQUEST["parentid"];
	}
	
	//print "Item Number: $itemnumber <br>";
	
	$sql_items="select RTRIM(ITEMNMBR) as ITEMNMBR, RTRIM(ITEMDESC) as ITEMDESC, Cast(Round((STNDCOST+.004),2) as decimal(18,2)) from IV00101 order by ITEMNMBR";	
	$sql_quantity="select EstimateQtyId, EstimateId, QuoteLineId, cast(QTY as int) as QTY from DUREX_EstimateQuantity where EstimateId=".$_REQUEST["estimate"]."order by QuoteLineId";
	$sql_BOM="select * from DUREX_EstimateBOM where EstimateId='$estimateid' and ITEMNMBR='$itemnumber'";
	$sql_costs="select * from DUREX_EstimateBOMCosts c where c.EstimateQuantityId='$qtyida' and EstimateBOMId='$bomId'"; 
	$sql_uofm="select UofMId, rtrim(UofM) as UofM from DUREX_EstimateUnitofMeasures order by UofM";
	$quantity=Array(0,0,0,0,0);
	
	//Connect to the GP database.  If error, display the error message and exit.
	include 'dbconn.php';
	
	//print "item Number: ".$itemnumber;
	
	if ($itemnumber!='') { 
		//print "SQL: $sql_BOM";
		foreach($db->query($sql_BOM) as $component) {
			$bomId=$component["EstimateBOMId"];
			$bomNonInventory=$component["Noninventory"];
			$bomItemNumber=$component["ITEMNMBR"];
			$bomItemDescription=$component["ITEMDESC"];
			$bomQuantity=$component["QTY"];
			$bomUofM=trim($component["UofM"]);
			$bomNote=$component["Note"];
			$bomMaterialAdder=$component["NoMaterialAdder"];
		}
	}
	
	//print "<BR> $bomUofM";
	
	$num=0;
	foreach($db->query($sql_quantity) as $qty) {
		$qtyida=$qty["EstimateQtyId"];
		$qtycost[$num]=0;
		$quantity[$num]=$qty["QTY"];
		$qtyid[$num]=$qty["EstimateQtyId"];
		if($bomId!=0) {
			try {
				$db1=new PDO("odbc:DRIVER={SQL Server};Server=DRX-VM-GPSQL01;Database=DTEST;UID=sa;PWD=Access123");
			}
			catch ( PDOException $e) {
				print $e->getMessage();
				exit;
			}
			$sql_costs="select  EstimateBOMCostId,EstimateBOMId, EstimateQuantityId, cast(UnitCost as Decimal(18,2)) as UnitCost from DUREX_EstimateBOMCosts c where c.EstimateQuantityId='$qtyida' and EstimateBOMId='$bomId'";
			//print "SQL: $sql_costs";
			foreach($db1->query($sql_costs) as $cost) { 
				$qtycost[$num]=$cost["UnitCost"];
			}
		}
		$num=$num+1;
	}
	
?>
	<style>
		.table-wrapper-scroll-y {
			display: block;
			max-height: 200px;
			overflow-y: auto;
			-ms-overflow-style: -ms-autohiding-scrollbar;
		}	
	</style>
<?php //print "Parent ID: $parentid"; ?>
<form id="componentForm" name="componentForm">	
	<input type="hidden" id="estimateid" value="<?php print $estimateid; ?>">
	<input type="hidden" id="bomid" value="<?php print $bomId; ?>">
	<input type="hidden" id="parentid" value="<?php print $parentid; ?>">
	
	<?php
		$qtynum=0;
		foreach($quantity as $key=>$val) {
			$qtynum=$qtynum+1;
	?>

	<input type="hidden" id="qtypriceid<?php print $qtynum; ?>" name="qtypriceid<?php print $qtynum; ?>" value="<?php if (isset($qtyid[$qtynum-1])) { print $qtyid[$qtynum-1]; } else { print "0"; } ?>">

	<?php
			//}
		}
	?>	
	
	<div class="custom-control custom-switch">
	  <input type="checkbox" class="custom-control-input" name="noninventory" id="noninventory" <?php if($bomNonInventory==1) { print "checked"; } ?> onchange="showItem();">
	  <label class="custom-control-label" for="noninventory">Non-inventory</label>
	</div>

	<div id="writein" class="collapse <?php if ($bomNonInventory==1) {print "show";} ?>">
		<div class="form-group">
			<label for="nonitemnumber">Item Number:</label>
			<input type="text" class="form-control" name="nonitemnumber" id="nonitemnumber" value="<?php print $bomItemNumber; ?>"> 
		</div>
	</div>
		
	<div id="gpitem" class="collapse <?php if ($bomNonInventory==0) {print "show";} ?>">
		<div class="form-group">
			<label for="gpitemnumber">Item Number:</label>
			<select class="form-control" name="gpitemnumber" id="gpitemnumber" onchange="getItemDesc();" style="width:100%">
			<?php if ($bomItemNumber!='' && $bomNonInventory==0)  { ?>
				<option value"<?php print $bomItemNumber; ?>"><?php print $bomItemNumber; ?></option>
			<?php
				}
			?>
			</select>
		</div>
	</div>

	<div class="form-group">
		<label for="itemdesc">Description:</label>
		<input type="text" class="form-control" id="itemdesc" name="itemdesc" value="<?php print htmlspecialchars(utf8_encode($bomItemDescription)); ?>"> 
	</div>	

	<div class="form-group">
		<label for="qty">Quantity:</label>
		<input type="number" id="quantity" name="quantity" placeholder="0" style="width:110px; text-align:right; margin-right: 10px;" value="<?php print $bomQuantity; ?>">
		<label for="uofm">U of M:</label>
		<select id="uofm" name="uofm" style="width:110px;">
			<?php	foreach($db->query($sql_uofm) as $uofm1) { ?>
			  <option value="<?php print $uofm1["UofM"];?>"<?php if ($bomUofM==$uofm1["UofM"]) { print " selected='selected'"; } ?>><?php print  $uofm1["UofM"]; ?></option>
			<?php } ?>
		</select>
		<div class="form-check form-check-inline" style="margin-left: 100px;">
		  <input class="form-check-input" style="width: 1.5em; height: 1.5em;" type="checkbox" id="adder" value="noadder" <?php if ($bomMaterialAdder==1) { print "checked"; } ?>>
		  <label class="form-check-label" for="adder">No Material Adder</label>
		</div>
	</div>	
	Unit Price:<br>
	<table class="table table-striped">
		<tr>
			<?php
				foreach($quantity as $key=>$val) {
					//if ($val!=0) {
			?>
			<th style="column-width:110px;border-right: 1px ridge #636161;text-align: center; font-size:16px;">Cost<br>(<?php print $val; ?>)</th>
			<?php
					//}
				}
			?>
		</tr>
		<tr>
			<?php
				$qtynum=0;
				foreach($quantity as $key=>$val) {
					$qtynum=$qtynum+1;
			?>
			<td style="column-width:110px;text-align: center">
				<input type="number" id="qtyprice<?php print $qtynum; ?>" name="qtyprice<?php print $qtynum; ?>" placeholder="0.00" style="width:110px; text-align:right" value="<?php print $qtycost[$qtynum-1]; ?>" <?php if ($quantity[$qtynum-1]==0) { print "disabled"; }?>>
			</td>
			<?php
					//}
				}
			?>	
		</tr>
	</table>

	<div class="form-group">
		<label for="notes">Notes:</label>
		<textarea class="form-control" id="note" name="note" rows="3"><?php print $bomNote; ?></textarea>
	</div>


<form>

<form id="attahcments"  action="attachments.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="MasterType" id="MasterType" value="BOM">
	<input type="hidden" name="MasterId" id="MasterId" value="<?php print $bomId; ?>">
	<input type="hidden" name="username" id="username" value="<?php print $username; ?>">
	<p class="statusMsg"></p>
	<div class="form-group">
		<label for="attachment"><b>Add Attachment:<b></label>
		<input type="file" class="form-control-file" name="attachment" id="attachment" aria-describedby="fileHelp" onblur="checkattach();">
	</div>	
	<div class="form-group">
		<label for="attachmentdesc"><b>Description:<b></label>
		<input type="text" class="form-control-file" name="attachmentdesc" id="attachmentdesc" aria-describedby="fileHelp" onblur="checkattach();">
	</div>	
	<input type="submit" name="attach_btn" class="btn btn-primary submitBtn" value="Attach"/>

	<div class="table-wrapper-scroll-y">
		<table class="table table-striped" style="margin-top: 5px;width: 950px;background-color:#ffffff;">
			<tr>
				<th style="column-width:400px; border-right: 1px ridge #636161">Attachment</th>
				<th style="column-width:300px;border-right: 1px ridge #636161">Added By</th>
				<th style="column-width:150px;border-right: 1px ridge #636161">Added On</th>
				<th></th>
			</tr>
			<?php 
				//foreach ($db->query($sql_estimates) as $estimate) {
			?>
			<tr>
				<td style="column-width:400px;"><?php //print $estimate["Customer"]; ?></td>
				<td style="column-width:300px;"><?php //print $estimate["QuoteNumber"]; ?></td>
				<td style="column-width:150px;"><?php //print $estimate["Approval"]; ?></td>
				<td><img src="/images/delete.png" alt="delete" width="25" height="25" data-toggle="tooltip" data-placement="top" title="Delete"></td>
			</tr>
			<?php
				//}
			?>
		</table>
	</div>
	<div id="form-footer" class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		<button type="button" class="btn btn-primary" id="modalSave" data-dismiss="modal" onclick="submitComponentForm();">Save changes</button>
	</div>	
</form>
<div id="err"></div>

<script>
// Show loader & then get content when modal is shown

	// In your Javascript (external .js resource or <script> tag)
	$(document).ready(function() {
		$("#gpitemnumber").select2({
		  ajax: {
			url: "items.php",
			dataType: 'json',
			delay: 250,
			data: function (params) {
			  return {
				search: params.term, // search term
				page: params.page
			  };
			},
			processResults: function (data, params) {
			  // parse the results into the format expected by Select2
			  // since we are using custom formatting functions we do not need to
			  // alter the remote JSON data, except to indicate that infinite
			  // scrolling can be used
			  params.page = params.page || 1;

			  return {
				results: data.results,
				pagination: {
				  more: (params.page * 1000) < data.total_count
				}
			  };
			},
			cache: true
		  },
		  placeholder: 'Search for a item',
		  escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
		  minimumInputLength: 1,
		  templateResult: formatRepo,
		  templateSelection: formatRepoSelection
		});
		
		$('#attahcments').on('submit', function(e){
			e.preventDefault();
			alert("Attached Clicked!");
			$.ajax({
				type: 'POST',
				url: 'attachmentsubmit.php',
				data: new FormData($("attahcments")),
				contentType: false,
				cache: false,
				processData:false,
				beforeSend: function(){
					$('.submitBtn').attr("disabled","disabled");
					$('#attahcments').css("opacity",".5");
				},
				success: function(msg){
					$('.statusMsg').html('');
					alert('Message: '+msg);
					if(msg == 'ok'){
						$('#attahcments')[0].reset();
						$('.statusMsg').html('<span style="font-size:18px;color:#34A853">Form data submitted successfully.</span>');
					}else{
						$('.statusMsg').html('<span style="font-size:18px;color:#EA4335">Some problem occurred, please try again.</span>');
					}
					$('#attahcments').css("opacity","");
					$("#attach_btn").removeAttr("disabled");
				},
				error: function(jqxhr, textStatus, errorThrown) {
					alert("Error1: "+ jqxhr.status + "  Status"+ textStatus);
					alert("Error2!");
				}
			});	
			alert("After Post!");
		});
	});

	function formatRepo (repo) {
	  if (repo.loading) {
		return repo.text;
	  }
	 // var markup = "<div class='select2-result-repository clearfix'>" +
	//	"<div class='select2-result-repository__avatar'><img src='" + repo.owner.avatar_url + "' /></div>" +
	//	"<div class='select2-result-repository__meta'>" +
	//	  "<div class='select2-result-repository__title'>" + repo.full_name + "</div>";

	//  if (repo.description) {
	//	markup += "<div class='select2-result-repository__description'>" + repo.description + "</div>";
	//  }

	  markup = "<div style='float:left;'>"+repo.itemnumber+": </div><div style='float: right:'>"+repo.itemdesc+"</div>";

	  return markup;
	}

	function formatRepoSelection (repo) {
	  return repo.id;
	}

	function showItem() {
		timeoutcheck();
		var radioValue = $("#noninventory:checked").val();
		if (radioValue) { 
			$("#writein").collapse("show");
			$("#gpitem").collapse("hide");
		}
		else { 
			$("#writein").collapse("hide");
			$("#gpitem").collapse("show");		
		}
	}
	
	function getItemDesc() {
		timeoutcheck();
		var desc=$("#gpitemnumber option:selected" ).text().split(" : ");
		var itemnumber=$("#gpitemnumber option:selected" ).val();
		//alert('meassage '+$("#gpitemnumber option:selected" ).text());
		//alert('desc: '+desc[1]);
		//$('#itemdesc').val(desc[1].trim());
		
		$.post("getgpitem.php", {
			itemnumber: itemnumber
		}, function (data, status) {
			// close the popup
			//alert("Status: "+status);
			//alert("Data: "+data);
			$('#itemdesc').val(data.ITEMDESC);
			$('#uofm').val(data.UofM);
		<?php
			$qtynum=0;
			foreach($quantity as $key1=>$val1) {
				$qtynum=$qtynum+1;
		?>
			$('#qtyprice<?php print $qtynum; ?>').val(data.Cost);
		<?php 
			}

		?>
	 
			// read records again
			//readRecords();
	 
			// clear fields from the popup
		},"json");

	}
	
	$(function() {
	  $('#qty').on('input', function() {
		this.value = this.value
		  .replace(/[^\d.]/g, '')             // numbers and decimals only
		  .replace(/(^[\d]{4})[\d]/g, '$1')   // not more than 2 digits at the beginning
		  .replace(/(\..*)\./g, '$1')         // decimal can't exist more than once
		  .replace(/(\.[\d]{4})./g, '$1');    // not more than 4 digits after decimal
	  });
	});
	
	function submitComponentForm(){
		timeoutcheck();
		// Initiate Variables With Form Content
		var estimateid='<?php print $_REQUEST["estimate"]; ?>';
		var noninv=0;
		var itemnumber='';
		var datastr='?';
		var nomaterialadder=0;
		if ($("#noninventory:checked").val()) { 
			noninv=1;
			itemnumber=$('#nonitemnumber').val();			
		}
		else {
			itemnumber=$('#gpitemnumber').val();
		}
		if ($("#adder:checked").val()) { 
			nomaterialadder=1;			
		}
		var itemdesc=$('#itemdesc').val();
		var quantity=$('#quantity').val();
		var parentid=$('#parentid').val();
		var uofm=$('#uofm').val();		
		var note=$('#note').val();
		var bomid=$('#bomid').val();
		<?php
			$qtynum=0;
			foreach($quantity as $key1=>$val1) {
				$qtynum=$qtynum+1;
		?>
		var qtyprice<?php print $qtynum; ?>=$('#qtyprice<?php print $qtynum; ?>').val();
		var qtypriceid<?php print $qtynum; ?>=$('#qtypriceid<?php print $qtynum; ?>').val();
		<?php 
			}

		?>
		//alert("bomid: "+bomid);
		
		$.post("addtobom.php", {
			estimateid: estimateid,
			bomid: bomid,
			parentid: parentid,
			noninventory: noninv,
			itemnumber: itemnumber,
			itemdesc: itemdesc,
			quantity: quantity,
		<?php
			$qtynum=0;
			foreach($quantity as $key1=>$val1) {
				$qtynum=$qtynum+1;
		?>
			qtyprice<?php print $qtynum; ?>: qtyprice<?php print $qtynum; ?>,
			qtypriceid<?php print $qtynum; ?>: qtypriceid<?php print $qtynum; ?>,
		<?php 
			}
		?>	
			uofm: uofm,
			note: note,
			nomaterialadder: nomaterialadder
		}, function (data, status) {
			// close the popup
			//alert("Status: "+status);
			//alert("Data: "+data);
			$('#bom-tab').click();
			// read records again
			//readRecords();
	 
			// clear fields from the popup
		});
	}
	//$("#writein").collapse("show");
	
	function checkattach() {
		if ($("#attachment").val()!='' && $("#attachmentdesc").val()!='') {
			$("#attach_btn").removeAttr("disabled");
		}
		else {
			$("#attach_btn").attr("disabled","disabled");
		}
	}

</script>


