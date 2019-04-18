<?php
require('html_table.php');

$pdf=new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);

$html=file_get_contents('http://localhost/estimating/review.php?estimateid=1000');

$pdf->WriteHTML($html);
$pdf->Output();
?>