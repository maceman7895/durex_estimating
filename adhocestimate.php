<!doctype html>
<html lang="en">
<?php
	include 'security.php';
	
	//Connect to the GP database.  If error, display the error message and exit.
	
	include 'dbconn.php';

	if (isset($_REQUEST["estimate"])) {
		$estimateid=$_REQUEST["estimate"];
	}
	else { 
		$estimateid=0;
	}

	$prodgroups=array("Cast Heaters", "Cartridge Heaters", "Straight Tubular Heaters", "Formed Tubular Heaters");
	$sql_prodgroups="select * from IV40600 where USCATNUM=1 and USCATVAL not in( select ProductTypeCode from [DUREX_Estimate ProductType])";  //Query for the product groups from GP
	$sql_estimators="select USERID, USERNAME from DYNAMICS..SY01400 where UserStatus=1 order by USERNAME";  //Query for getting the mask for a product group
	$sql_estimate="select *,case when noninventory=0 then (select USCATVLS_1 from IV00101 where ITEMNMBR=e.ITEMNMBR) else '' end as ItemClass,case when noninventory=0 then (select USCATVLS_2 from IV00101 where ITEMNMBR=e.ITEMNMBR) else '' end as ProdSubGroup from DUREX_Estimates e where EstimateId=$estimateid"; 
	$sql_quantity="select EstimateQtyId, EstimateId, QuoteLineId, cast(QTY as int) as QTY from DUREX_EstimateQuantity where EstimateId=$estimateid";
	$sql_BOM="select * from DUREX_EstimateBOM where EstimateId=$estimateid";
	
	
	$quantity=Array(0,0,0,0,0);
	$claimedestimate=0; 
	
	$qtenumber='';
	$qteCustomer='';
	$qteCustomerType='';
	$qteContact='';
	$qtePhone='';
	$qteCSR='';
	$qteSalesPerson='';
	$qteCustomerStatus='Unknown';
	
	$estNumber='';
	$estQuote='';
	$estItem='';
	$estItemDesc='';
	$estCommission=0;
	$estMOCharge=0;
	$estNRECharge=0;
	$estNREChargeType="Amortize";
	$estDesignLead='';
	$estProdLead='';
	//$estApprovalBy='';
	$estApprovedDrawings='';
	$estPQA=0;
	$estReady=0;
	$estContact='';
	$estPhone='';
	$estPartNumber='';
	$estPartDesc='';
	$estNoninventory='';
	$estItemClass='';
	$estProdSubGroup='';
	$estEstimator='';
	$estMOCharge='';
	$estNRECharge='';
	$estNREChargeType='';
	
	

		
	foreach($db->query($sql_estimate) as $estimate) {
		$estNumber=$estimate["EstimateId"];
		$estQuote=$estimate["SOPNUMBE"];
		$estItem=trim($estimate["ITEMNMBR"]);
		$estItemDesc=$estimate["ITEMDESC"];
		if ($estimate["Commission"]=='') {
			$estCommission=0.00;
		}
		else {
			$estCommission=number_format($estimate["Commission"],2);
		}
		$estDesignLead=$estimate["LeadTimeDesign"];
		$estProdLead=$estimate["LeadtimeProduction"];
		$estApprovalBy=$estimate["ApprovalBy"];
		$estApprovedDrawings=$estimate["ApprovedDrawings"];
		$estPQA=$estimate["PQA"];
		$estReady=$estimate["ReadyForApproval"];
		$estContact=$estimate["Contact"];
		$estPhone=$estimate["ContactPhone"];
		$estPartNumber=$estimate["EstimatePartNum"];
		$estPartDesc=$estimate["EstimateDesc"];
		$estNoninventory=$estimate["Noninventory"];
		$estItemClass=$estimate["ItemClass"];
		$estProdSubGroup=$estimate["ProdSubGroup"];
		$estEstimator=$estimate["Estimator"];
		$estMOCharge=$estimate["MOCharge"];
		$estNRECharge=$estimate["NRECharge"];
		$estNREChargeType=$estimate["NREChargeType"];
	}
	
	if ($estEstimator=='') { $estEstimator=$_SESSION["username"]; $claimedestimate=1; }

	$sql_quotedata="select RTRIM(SOPNUMBE) as QuoteNumber, RTRIM(CUSTNAME) as CUSTNAME, (select RTRIM(CLASDSCR) from RM00201 where CLASSID in (select CUSTCLAS from RM00101 where CUSTNMBR=q.CUSTNMBR)) as CustomerType,RTRIM(CNTCPRSN) AS Contact, '('+Substring(PHNUMBR1,1,3) + ') '+ Substring(PHNUMBR1,4,3) + '-'+ Substring(PHNUMBR1,7,4) as PhoneNumber, (select RTRIM(SLPRSNFN)+' '+RTRIM(SPRSNSLN) as SalesName from RM00301 where SLPRSNID=q.SLPRSNID) as SalesPerson, (select RTRIM(USERNAME) from DYNAMICS..SY01400 where USERID=q.USER2ENT) as CSR,
						case 
							when (select count(*) from SOP10100 where SOPTYPE=2 and CUSTNMBR=q.CUSTNMBR)>0 then 'Existing'
							when (select count(*) from SOP30200 where SOPTYPE=2 and CUSTNMBR=q.CUSTNMBR)>0 then 'Existing' 
							when (select count(*) from SOP10100 where SOPTYPE=1 and CUSTNMBR=q.CUSTNMBR)>0 and (select count(*) from SOP30200 where SOPTYPE=2 and CUSTNMBR=q.CUSTNMBR)=0 and (select count(*) from SOP10100 where SOPTYPE=2 and CUSTNMBR=q.CUSTNMBR)=0  then 'New/Quoted'
							when (select count(*) from SOP30200 where SOPTYPE=1 and CUSTNMBR=q.CUSTNMBR)>0  and (select count(*) from SOP30200 where SOPTYPE=2 and CUSTNMBR=q.CUSTNMBR)=0 and (select count(*) from SOP10100 where SOPTYPE=2 and CUSTNMBR=q.CUSTNMBR)=0  then 'New/Quoted'
							else 'New'
						end as CustomerStatus,
					* from SOP10100 q where SOPNUMBE='".$estQuote."'";
	//print "SQL: $sql_quotedata";
	if ($estQuote!='') { 
		foreach($db->query($sql_quotedata) as $quote) {
				$qtenumber=$quote["QuoteNumber"];
				$qteCustomer=$quote["CUSTNAME"];
				$qteCustomerType=$quote["CustomerType"];
				$qteContact=$quote["Contact"];
				$qtePhone=$quote["PhoneNumber"];
				$qteCSR=$quote["CSR"];
				$qteSalesPerson=$quote["SalesPerson"];
				$qteCustomerStatus=$quote["CustomerStatus"];
		}
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

	<head>
<?php
	include "header.php";
?>
		<title>Durex Quote & Estimating</title>	
		<style>
		/* Apply & remove to fix dynamic content scroll issues on iOS 9.0 */
			.modal-scrollfix.modal-scrollfix {
				overflow-y: hidden;
			}
			
			.switch {
			  position: relative;
			  display: inline-block;
			  width: 60px;
			  height: 34px;
			}

			.switch input { 
			  opacity: 0;
			  width: 0;
			  height: 0;
			}

			.slider {
			  position: absolute;
			  cursor: pointer;
			  top: 0;
			  left: 0;
			  right: 0;
			  bottom: 0;
			  background-color: #ccc;
			  -webkit-transition: .4s;
			  transition: .4s;
			}

			.slider:before {
			  position: absolute;
			  content: "";
			  height: 26px;
			  width: 26px;
			  left: 4px;
			  bottom: 4px;
			  background-color: white;
			  -webkit-transition: .4s;
			  transition: .4s;
			}

			input:checked + .slider {
			  background-color: #2196F3;
			}

			input:focus + .slider {
			  box-shadow: 0 0 1px #2196F3;
			}

			input:checked + .slider:before {
			  -webkit-transform: translateX(26px);
			  -ms-transform: translateX(26px);
			  transform: translateX(26px);
			}

			/* Rounded sliders */
			.slider.round {
			  border-radius: 34px;
			}

			.slider.round:before {
			  border-radius: 50%;
			}
			
			.modal-body {
				max-height: 600px;
				overflow: auto !important; // you will need important here to override
			}
			.needs-validation input:invalid {
			  border-color: salmon;
			}
			
			.claimed input:readonly { 
				background: #79f2e4;
			}
			
			#modal-1 {
				overflow: auto !important; // you will need important here to override
			}
		</style>
	</head>
	<body>

		<?php
			include "navbar.php";
		?>
		<div style="padding:10px;">
			<form class="needs-validation" novalidate>
			  <input type="hidden" id="adhoc" name="adhoc" value="1">
			  <div class="form-row">
				<div class="form-group col-md-1">
				  <label for="estimatenum">Estimate:</label>
				  <input type="text" class="form-control" id="estimatenum" value="<?php print $estimateid; ?>" readonly>
				</div>
				<div class="form-group col-md-3">
				  <label for="customer">Customer: <span id="cstatus" style="border: 1px solid black; margin-left: 3px; padding: 2px; background-color:<?php if ($qteCustomerStatus=="Existing") { print "#66f736"; } else { print "#F77987"; } ?>;"><?php print $qteCustomerStatus; ?></span></label>
				  <input type="text" class="form-control" id="customer" value="<?php print $qteCustomer; ?>" readonly>
				</div>
				<div class="form-group col-md-2">
				  <label for="custtype">Customer Class: </label>
				  <input type="text" class="form-control" id="custtype" value="<?php print $qteCustomerType; ?>" readonly>
				</div>
				<div class="form-group col-md-2">
				  <label for="custtype">CSR: </label>
				  <input type="text" class="form-control" id="custtype" value="<?php print $qteCSR; ?>" readonly>
				</div>
				<div class="form-group col-md-2">
				  <label for="custtype">Sales Rep: </label>
				  <input type="text" class="form-control" id="custtype" value="<?php print $qteSalesPerson; ?>" readonly>
				</div>
				<div class="form-group col-md-1 hide">
					<button type="button" class="btn btn-primary" id="estimateSave" onclick="saveEstimate();">Save</button>
					<a id="esitmateCancel" class="btn  btn-secondary" href="estimates.php">Cancel</a>
				</div>				
			  </div>
			  <div class="form-row">
				<div class="form-group col-md-1">
				  <label for="Contact">Contact:<span style="color:#ff0000"> *</span></label>
				  <input type="text" class="form-control" id="contact" value="<?php if ($estContact=='') { print $qteContact; } else { print $estContact; } ?>" onblur="validateContact();" required>
				        <div id="valerror" class="invalid-feedback">
							This is required!
						</div>
				</div>
				<div class="form-group col-md-3">
				  <label for="phone">Phone Number:<span style="color:#ff0000"> *</span></label>
				  <input type="text" class="form-control" id="phone" value="<?php if ($estPhone=='') { print $qtePhone; } else { print $estPhone; }?>" required>
				        <div id="phoneerror" class="invalid-feedback">
							This is required!
						</div>
				</div>
				<div class="form-group col-md-2">
				  &nbsp;
				</div>
				<div class="form-group col-md-2">
					<label for="estimator">Estimator:</label>
					<input type="text" class="form-control"  <?php if ($claimedestimate==1) { print 'style="background: #79f2e4;"'; } ?> id="estimator" name="estimator" value="<?php print $estEstimator; ?>" readonly>
				</div>
				<div class="form-group col-md-1">
					<label for="prodleadtime">Commission:</label>
					<input type="number" class="form-control" id="commission" value="<?php print $estCommission; ?>">
				</div>
			  </div>
			  <div class="form-row">
				<div class="form-group col-md-1">
				  <label for="quotenum">Quote:</label>
				  <input type="text" class="form-control" id="quotenum" value="<?php print $qtenumber; ?>" readonly>
				</div>
				<div class="form-group col-md-1">
				  <label for="linenum">Line Item: </label>
				  <input type="text" class="form-control" id="linenum" value="<?php print $lines; ?>" readonly>
				</div>
				<div class="form-group col-sm-1">
					<label for="qty1">QTY 1:</label>
					<input type="text" class="form-control" id="qty1" value="<?php print $quantity[0]; ?>">
				</div>
				<div class="form-group col-md-1">
					<label for="qty2">QTY 2:</label>
					<input type="text" class="form-control" id="qyt2" value="<?php print $quantity[1]; ?>">
				</div>
				<div class="form-group col-md-1">
					<label for="qty3">QTY 3:</label>
					<input type="text" class="form-control" id="qty3" value="<?php print $quantity[2]; ?>">
				</div>
				<div class="form-group col-md-1">
					<label for="qty4">QTY 4:</label>
					<input type="text" class="form-control" id="qyt4" value="<?php print $quantity[3]; ?>">
				</div>
				<div class="form-group col-md-1">
					<label for="qty4">QTY 5:</label>
					<input type="text" class="form-control" id="qyt5" value="<?php print $quantity[4]; ?>">
				</div>
				<div class="form-group col-md-2">
					<label for="designleadtime">Design Lead Time:</label>
					<input type="text" class="form-control" id="designleadtime" value="<?php print $estDesignLead; ?>">
				</div>
				<div class="form-group col-md-2">
					<label for="prodleadtime">Production Lead Time:</label>
					<input type="text" class="form-control" id="prodleadtime" value="<?php print $estProdLead; ?>">
				</div>		
			  </div>
			  <div class="form-row">
				<div class="form-group col-md-2">
				  <label for="quotenum">Customer P/N:</label>
				  <input type="text" class="form-control" id="quotenum" value="<?php if ($estNoninventory==1) { print $estItem; } else { print ""; } ?>" readonly>
				</div>
				<div class="form-group col-md-3">
				  <label for="linenum">Description:</label>
				  <input type="text" class="form-control" id="linenum" value="<?php if ($estNoninventory==1) { print $estItemDesc; } else { print ""; } ?>" readonly>
				</div>
				<div class="form-group col-md-2">
				  <label for="durexpn">Durex Part:</label>
				  <input type="text" class="form-control" id="durexpn" value="<?php if ($estNoninventory==0) { print $estItem; } else { print ""; } ?>" readonly>
				</div>
				<div class="form-group col-md-3">
				  <label for="durexdesc">Description:</label>
				  <input type="text" class="form-control" id="durexdesc" value="<?php if ($estNoninventory==0) { print $estItemDesc; } else { print ""; } ?>" readonly>
				</div>
				<div class="form-group col-md-1">
				  <label for="itemclass">Product Group:</label>
				  <input type="text" class="form-control" id="itemclass" value="<?php print $estItemClass; ?>" readonly>
				</div>
				<div class="form-group col-md-1">
				  <label for="itemclass">Product Subgroup:</label>
				  <input type="text" class="form-control" id="itemclass" value="<?php print $estProdSubGroup; ?>" readonly>
				</div>
			  </div>
			  <div class="form-row">
				<div class="form-group col-md-2">
				  <label for="quotenum">Durex Estimate P/N:</label>
				  <input type="text" class="form-control" id="estpartnum" value="<?php if ($estpartnum='') { print $estItem; } else { print $estPartNumber; } ?>" >
				</div>
				<div class="form-group col-md-3">
				  <label for="linenum">Description:</label>
				  <input type="text" class="form-control" id="estpartdesc" value="<?php if ($estpartdesc='') { print $estItemDesc; } else { print $estPartDesc; } ?>">
				</div>
				<div class="form-group col-md-2">
				  <label for="approval">Approval By:</label>
				  <select class="form-control" id="approval">
					<option>PGM</option>
					<option>GM</option>
					<option>Estimator</option>					
				  </select>
				</div>
				<div class="form-group col-md-2" style="text-align:center;">
				  <label for="drawing">Approved Drawing Required</label>
				  <input type="checkbox" class="form-control" id="drawing"<?php if ($estApprovedDrawings==1) { print " checked"; } ?>>
				</div>
				<div class="form-group col-sm-1" style="text-align:center;">
				  <label for="pqa">PQA Required</label>
				  <input type="checkbox" class="form-control" id="pqa"<?php if ($estPQA==1) { print " checked"; } ?>>
				</div>
				<div class="form-group col-sm-1" style="text-align:center;">
				  <label for="ready">Ready for Approval</label>
				  <input type="checkbox" class="form-control" id="ready"<?php if ($estReady==1) { print " checked"; } ?> >
				  <img id="readyexplain" src="images/question.png" style="width:30px;height:20px;" data-toggle="tooltip" data-placement="top" title="Tooltip on top">
				</div>
			  </div>
			</form>
		</div>
	</body>
<?php 
	include "footer.php";
?>
	<script src="js/estimate.js"></script>