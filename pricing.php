<?php
	include 'security.php';
	include 'dbconn.php';
	
	$notetext='';
	
	if (isset($_REQUEST["estimate"])) {
		$estimateid=$_REQUEST["estimate"];
	}
	else { 
		$estimateid=0;
	}
	//print_r($_REQUEST);
?>

<ul id="pricetabs" class="nav nav-pills" role="tablist">
	<li class="nav-item">
		<a class="nav-item nav-link" id="review-tab" role="tab" data-toggle="tab" href="#review" data-url="review.php" data-parms="estimateid=<?php print $estimateid; ?>">Review & Approval</a>
	</li>
	<li class="nav-item">
		<a class="nav-item nav-link show" id="breakdown-tab" role="tab" data-toggle="tab" href="#breakdown" data-url="breakdown.php" data-parms="estimateid=<?php print $estimateid; ?>">Labor Cost & Time Breakdown</a>
	</li>
</ul>

<div class="tab-content" id="pricetabContent">
	<div class="tab-pane fade" id="review" role="tabpanel" aria-labelledby="review-tab">
		<div style="text-align:center">
			<img src="images/loading.gif">
			<p>Loading...</p>
		</div>	
	</div>
	<div class="tab-pane fade" id="breakdown" role="tabpanel" aria-labelledby="breakdown-tab">
		<div style="text-align:center">
			<img src="images/loading.gif">
			<p>Loading...</p>
		</div>	
	</div>
</div>

<script>
	$('#pricetabs a').click(function (e) {
		e.preventDefault();
		timeoutcheck();
		var url = $(this).attr("data-url");
		var parms = $(this).attr("data-parms");	
		var href = this.hash;
		var pane = $(this);
				
		if (parms!='') {
			url=url+'?'+parms;
		}
		//alert('URL: '+url);
		// ajax load from data-url
		$(href).load(url,function(result){      
			pane.tab('show');
		});
		//alert("price tab clicked!");
	});
	
	$("#review-tab").click();
</script>