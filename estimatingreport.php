<?php
require __DIR__ . '/vendor/autoload.php';

$item=$_REQUEST["estimateid"];

$options = array(
    'username' => 'durex\readonly',
    'password' => 'vS25KOAs18dq'
);

$ssrs = new \SSRS\Report('http://drx-vm-gpsql01/reportserver/', $options);
$result = $ssrs->loadReport('/Dynamics GP 2013/DUREX/Sales/Development/Worksheet for Estimate/Estimate Summary Report');

$reportParameters = array(
    'EstimateId' => $item
);

$ssrs->setSessionId($result->executionInfo->ExecutionID);
$ssrs->setExecutionParameters(($reportParameters));
 
$output = $ssrs->render('PDF'); // PDF | XML | CSV

  header('Content-type: application/pdf');
  header('Content-Disposition: inline; filename="report.pdf"');
  header('Content-Transfer-Encoding: binary');
  header('Accept-Ranges: bytes');
echo $output;

?>



