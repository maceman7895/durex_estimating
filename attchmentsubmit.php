<?php
//print_r($_FILES);

if(!empty($_POST['name']) || !empty($_POST['email']) || !empty($_FILES['file']['name'])){
    $uploadedFile = '';
    if(!empty($_FILES["file"]["type"])){
        $fileName = time().'_'.$_FILES['file']['name'];
        $valid_extensions = array("jpeg", "jpg", "png");
        $temporary = explode(".", $_FILES["file"]["name"]);
        $file_extension = end($temporary);
		$sourcePath = $_FILES['file']['tmp_name'];
		$targetPath = "documents/".$fileName;
		if(move_uploaded_file($sourcePath,$targetPath)){
			$uploadedFile = $fileName;
		}
   }
    $attachedto = $_POST{"MasterType"];

    $masterid = $_POST['MasterId'];
    $description = $_POST['attachmentdesc'];
	$addedby=$_POST['username'];
	
    
    //include database configuration file
    include_once 'dbconn.php';
    
    //insert form data in the database
	$sql="INSERT DUREX_EstimateAttachments (AttachedTo, MasterId, Filename, AddedBy, AddedOn, AttachmentDescription) select '".$attachedto."','".$masterid."','".$uploadedFile."','".$addedby."',GETDATE()"
	print "SQL: $sql";
	$insert = $db->query("INSERT DUREX_EstimateAttachments (AttachedTo, MasterId, Filename, AddedBy, AddedOn, AttachmentDescription) VALUES ('".$attachedto."','".$masterid."','".$uploadedFile."','".$addedby."',GETDATE())");
    
    echo $insert?'ok':'err';
}
?>