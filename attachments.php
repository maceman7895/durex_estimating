<?php
session_start();
include 'dbconn.php';
$sql_attachment="INSERT INTO DUREX_EstimateAttachments (AttachedTo, MasterId, Filename, AddedBy, AddedOn, AttachmentDescription) values ( :mastertype, :masterid, :fname, :addedby, :addedon, :description)";

print "Attachments!<br>"; 
print "Method: ".$_SERVER["REQUEST_METHOD"];
print "Request:<br>";
print_r($_REQUEST);
print "_FILES:<br>";
print_r($_FILES);

// Check if the form was submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Check if file was uploaded without errors
    if(isset($_FILES["attachment"]) && $_FILES["attachment"]["error"] == 0){
        //$allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
		$time=time();
		$datestamp=date("Y-m-d H:i:s");
        $filename = $time . '_' . $_FILES["attachment"]["name"];
        $filetype = $_FILES["attachment"]["type"];
        $filesize = $_FILES["attachment"]["size"];
		
        // Verify file extension
        //$ext = pathinfo($filename, PATHINFO_EXTENSION);
        //if(!array_key_exists($ext, $allowed)) die("Error: Please select a valid file format.");
    
        // Verify file size - 5MB maximum
        //$maxsize = 5 * 1024 * 1024;
        //if($filesize > $maxsize) die("Error: File size is larger than the allowed limit.");
    
        // Verify MYME type of the file
       //if(in_array($filetype, $allowed)){
            // Check whether file exists before uploading it
            if(file_exists("documents/" . $filename)){
                echo $filename . " is already exists.";
            } else{
                move_uploaded_file($_FILES["attachment"]["tmp_name"], "documents/" . $filename);
				if (isset($_REQUEST["MasterType"]) && isset($_REQUEST["MasterId"]) ) {
					$username='bmace'; //$SESSION["username"];
					$mastertype=$_REQUEST["MasterType"];
					$masterid=$_REQUEST["MasterId"];
					$attachdescription=$_REQUEST["attachmentdesc"];
					echo "$sql_attachment<br>";
					print "<br>username: $username <br>";
					print "mastertype: $mastertype <br>";
					print "masterid: $masterid <br>";
					print "attachmentdesciption: $attachdescription <br>";
					print "fname: $filename <br>";
					print "addedon: $datestamp"; 
					
					$stmt=$db->prepare($sql_attachment);
					$stmt->bindParam(':fname', $filename);					
					$stmt->bindParam(':mastertype', $mastertype);
					$stmt->bindParam(':masterid', $masterid);
					$stmt->bindParam(':addedby', $username);
					$stmt->bindParam(':addedon', $datestamp);
					$stmt->bindParam(':description', $attachdescription);					

					try {
						$result=$stmt->execute();
						$stmt->debugDumpParams();
						//var_dump($result);
						//print "executed!";
					}
					catch (Exception $e){
						throw $e;
					}	
					
					echo "Your file was uploaded successfully.";
				}
            } 
        //} else{
        //    echo "Error: There was a problem uploading your file. Please try again."; 
        //}
    } else{
        echo "Error: " . $_FILES["attachment"]["error"];
    }
}
?>