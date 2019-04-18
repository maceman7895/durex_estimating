<?php
	include 'dbconn.php';
	
	$sql_gpquotenotes="select *, CAST(CAST(NOTEINDX as INT) as BINARY(4)) as BinaryForm from SY03900 where NOTEINDX in (select NOTEINDX from SOP10100 where SOPNUMBE='$quotenumber')";
	
	foreach($db->query($sql_gpquotenotes) as $gpnote { 
		$notetext=$gpnote["TXTFIELD"];
	}
?>
<div> 
	<?php print $notetext; ?>
</div>