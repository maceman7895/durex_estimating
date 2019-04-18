<?php
	//Connect to the GP database.  If error, display the error message and exit.
	include 'dbconn.php';

	//Holds the idea of the attachment to download
	$attachmentid="";
	
	//If the attachment id is passed set the variable
	if (isset($_REQUEST["attachmentid"])) { 
		$attachmentid=$_REQUEST["attachmentid"];
	}
	
	//The sql query string to lookup the attachment
	$sql_attachment="select fileName, datalength(BinaryBlob) as datasize, BinaryBlob as content,  (case when fileName like '%.%' then reverse(left(reverse(fileName), charindex('.', reverse(fileName)) - 1)) else '' end) as Extension, (select MimeType from DUREX_EstimateExtension2Mimetype where Extension = '.'+(case when fileName like '%.%' then reverse(left(reverse(fileName), charindex('.', reverse(fileName)) - 1)) else '' end)) as mimetype  from coAttachmentItems where Attachment_ID='$attachmentid'";

	if ($attachmentid!="") { 
		foreach($db->query($sql_attachment) as $attach) { 
			$size=$attach["datasize"];
			$type=$attach["mimetype"];
			//$type="application/vnd.ms-excel";
			$file=$attach["fileName"];
			$content=$attach["content"];
			header("Content-length: $size");
			header("Content-type: $type");
			header("Content-Disposition: attachment; filename=$file");
			ob_clean();
			flush();
			echo hex2bin($content);
		}
	}
	else { 
		print "No attachment id passed";
	}

?>