<?php

	//Default SQl querys 
	//print_r($_POST);
	//Connect to the GP database.  If error, display the error message and exit.

	include 'dbconn.php';
	
	$estimateid='';
	$itemnumber='';
	
	if (isset($_REQUEST["estimateid"])) { $estimateid=$_REQUEST['estimateid']; }
	if (isset($_REQUEST["itemnumber"])) { $itemnumber=$_REQUEST['itemnumber']; }
	
	$sql_getbom="select  CPN_I, (select ITEMDESC from IV00101 where ITEMNMBR=b.CPN_I) as ITEMDESC, 0 as noninventory, QUANTITY_I, UOFM, (select STNDCOST from IV00101 where ITEMNMBR=b.CPN_I) as Cost   from bm010115 b where PPN_I='$itemnumber'";
?>
<form>
	<input type="hidden" id=estimateid" value="<?php print $estimateid; ?>">
	<div>
		<div class="form-group">
			<label for="itemnumber">Item Number:</label>
			<input type="text" class="form-control" name="itemnumber" id="itemnumber"> 
		</div>
	</div>
	<div styel="tex-align:center">
		<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="submitCopyBOM();">Copy BOM</button>	
	</div>
</form>

<script>
function submitCopyBOM() {
	var estimateid=$("#estimatenum").val();
	var itemnumber=$("#itemnumber").val();
	
	$.ajax({
	  type: 'POST',
	  url: "copybom2estimate.php",
	  data: {
		estimateid: estimateid,
		itemnumber: itemnumber
	},
	  success: function (data, status) {
		// close the popup
		//alert("Status: "+status);
		//alert("Data: "+data);

 
		// read records again
		//readRecords();
 
		// clear fields from the popup
	},
	  dataType: "text",
	  async:true
	});
	
	setTimeout(function(){$('#bom-tab').click();}, 1000);
	
	$('#bom-tab').click();	
	
/*	$.post("copybom2estimate.php", {
		estimateid: estimateid,
		itemnumber: itemnumber
	}, function (data, status) {
		// close the popup
		//alert("Status: "+status);
		//alert("Data: "+data);

 
		// read records again
		//readRecords();
 
		// clear fields from the popup
	});
	$('#bom-tab').click();	
*/
}

</script> 
