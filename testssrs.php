<?php
	require_once 'SSRS/SSRSReport.php';
	
	define("UID", "sa");
	define("PASWD", "Access123");
	define("SERVICE_URL", "http://drx-vm-gpsql01/ReportServer");
	try
	{
		$ssrs_report = new SSRSReport(new Credentials(UID, PASWD), SERVICE_URL);                
	}
	catch (SSRSReportException $serviceException)
	{
		echo $serviceException->GetErrorMessage();
	}       	
?>
<?php 
	include "footer.php";
?>
<scrip>
 

</script>

