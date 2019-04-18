<pre>
<?php

	include 'dbconn.php';
	
	$sql="select *, (select count(*) from  DUREX_EstimateBOM where EstimateId=1004 and ParentEstimateBOMId=b.EstimateBOMId) as Children from  DUREX_EstimateBOM b where EstimateId=:estimate and ParentEstimateBOMId=:id";
	//$sql_child="select * from  DUREX_EstimateBOM where EstimateId=1004 and ParentEstimateBOMId=:id";
	$child=0;
	$estimate=1004;
	$sth = $db->prepare($sql);
	$sth->bindParam(':id', $child, PDO::PARAM_INT);
	$sth->bindParam(':estimate', $estimate, PDO::PARAM_INT);
	$sth->execute(); 
	$result = $sth->fetchAll();
	
	//print_r($result);
	foreach($result as $row) { 
		if ($row["Children"]!=0) {
			$child=$row["EstimateBOMId"];
			$sth = $db->prepare($sql);
			$sth->bindParam(':id', $child, PDO::PARAM_INT);
			$sth->bindParam(':estimate', $estimate, PDO::PARAM_INT);
			$sth->execute(); 
			$children = $sth->fetchAll();
			$row["child"]=$children;
		}	
		$bom[]=$row;
	}
	
	var_dump($bom);
?>
</pre>