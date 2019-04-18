<form id="attahcments"  action="attachments.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="MasterType" id="MasterType" value="2">
	<input type="hidden" name="MasterId" id="MasterId" value="321">
	
	<div class="form-group">
		<label for="attachment"><b>Add Attachment:<b></label>
		<input type="file" class="form-control-file" name="attachment" id="attachment" aria-describedby="fileHelp">
	</div>	
	<div class="form-group">
		<label for="attachmentdesc"><b>Description:<b></label>
		<input type="text" class="form-control-file" name="attachmentdesc" id="attachmentdesc" aria-describedby="fileHelp">
	</div>	
    <button id="attach_btn" type="submit" class="btn">Attach</button>
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
</form>