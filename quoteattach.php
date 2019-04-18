<?php
	include 'dbconn.php';
	
		
	if (isset($_REQUEST["quote"])) {
		$quote=$_REQUEST["quote"];
	}
	else {
		$quote='';
	}
	
	$sql_attach="select c2.fileName, isnull(c2.Attachment_Description,'                                       ') as attachment from CO00102 c1 inner join CO00101 c2 on c2.Attachment_ID=c1.Attachment_ID where BusObjKey='0\System\Notes\'+(select REPLACE(CONVERT(varchar(20),CAST(CAST(NOTEINDX as INT) as BINARY(4)),1),'0x','') from SY03900 where NOTEINDX in (select NOTEINDX from SOP10100 where SOPNUMBE='$quote'))";


?>
	<table class="table" style="margin-top: 5px;background-color:#ffffff;" id="mytable" style="width:1061px;">
	<tr style="color:#000000; background-color:#9bd1f7"> 
		<th value="width: 35%">File Name</th>
		<th value="width: 60%">Description</th>
		<th></th>
	</tr>
<?php
	$attach_count=0;
	
 	foreach($db->query($sql_attach) as $attach) { 
		$attach_count=$attach_count+1;
		//print_r($attach);
?>
	<tr>
		<td value="width: 35%"><?php print trim($attach["fileName"]); ?></td>
		<td value="width: 60%"><?php if ($attach["attachment"]=='')  { print str_repeat("&nbsp;", 200); } else { print $attach["attachment"]; } ?></td>		
		<td><img src="images/download.png" alt="download" width="25" height="25" data-toggle="tooltip" data-placement="top" title="Download"></td>
	</tr>
<?php
	}
	if ($attach_count=0) {
?>
	<tr>
		<td colspan=3 style="text-align: center">No attachments found.</td>
	</tr>
<?php
	}
?>	
</table>