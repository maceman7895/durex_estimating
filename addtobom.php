<?php
	// security check
	include 'security.php';
	//Default SQl querys 
	//print_r($_POST);
	//Connect to the GP database.  If error, display the error message and exit.
	include 'dbconn.php';
	
	$qtyprice=Array();
	$qtyid=Array();

    if(isset($_POST['estimateid']) && isset($_POST['noninventory']) && isset($_POST['itemnumber']))
    {
		print_r($_POST);
        // get values 
        $estimateid = $_POST['estimateid'];
		$noninventory = $_POST['noninventory'];
		$itemnumber = $_POST['itemnumber'];
		$itemdesc = $_POST['itemdesc'];
		$quantity = $_POST['quantity'];
		$qtyprice[] = $_POST['qtyprice1'];
		$qtyprice[] = $_POST['qtyprice2'];
		$qtyprice[] = $_POST['qtyprice3'];
		$qtyprice[] = $_POST['qtyprice4'];
		$qtyprice[] = $_POST['qtyprice5'];
		$qtyid[] = $_POST['qtypriceid1'];
		$qtyid[] = $_POST['qtypriceid2'];
		$qtyid[] = $_POST['qtypriceid3'];
		$qtyid[] = $_POST['qtypriceid4'];
		$qtyid[] = $_POST['qtypriceid5'];
		$uofm = $_POST['uofm'];	
		$note = $_POST['note'];
		$bomid= $_POST['bomid'];
		$parentid=$_POST['parentid'];
		$nomaterialadder=$_POST['nomaterialadder'];

		if ($bomid!=0) { 
			$sql="UPDATE b SET ITEMNMBR='$itemnumber', ITEMDESC='$itemdesc',QTY=$quantity,Noninventory=$noninventory,UofM='$uofm', Note='$note', NoMaterialAdder='$nomaterialadder' from DUREX_EstimateBOM b where EstimateBOMId=$bomid";
		}
		else {
			$sql = "INSERT INTO DUREX_EstimateBOM(EstimateId, ITEMNMBR, ITEMDESC,QTY,Noninventory,UofM, Note, NoMaterialAdder, ParentEstimateBOMId)  OUTPUT INSERTED.EstimateBOMId VALUES('$estimateid', '$itemnumber', '$itemdesc','$quantity', '$noninventory','$uofm','$note', '$nomaterialadder', '$parentid');";
		}
		echo $sql;
		$stmt= $db->prepare($sql);

		try {
			$stmt->execute();
			$result=$stmt->fetch(PDO::FETCH_ASSOC);
			$bomitemid=$result["EstimateBOMId"];
		}
		catch (Exception $e){
			throw $e;
		}
		//print "note: $note";
		
		$num=0;
		foreach($qtyprice as $price) {
			$quantityid=$qtyid[$num];
			if ($price!=0) {
				if ($bomid!=0) {
					$sql="UPDATE q SET UnitCost=$price FROM DUREX_EstimateBOMCosts q where EstimateBOMId=$bomid and EstimateQuantityId=$quantityid";
				}
				else {
					$sql = "INSERT INTO DUREX_EstimateBOMCosts(EstimateBOMId, EstimateQuantityId, UnitCost) VALUES ('$bomitemid', '$quantityid', '$price');";
				}
				//print $sql;
				$stmt= $db->prepare($sql);
				try {
					$stmt->execute();
				}
				catch (Exception $e){
					throw $e;
				}
			}
			$num=$num+1;
		}
	
       // echo "1 Record Added!";
		
	}
?>