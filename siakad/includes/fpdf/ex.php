<?php
define('FPDF_FONTPATH','font/');
require('fpdf_protection.php');

$pdf=new FPDF_Protection();
$pdf->SetProtection(array('print'));
$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('Arial');
$pdf->Write(10,'You can print me but not copy my text.');
$pdf->Output();
?>
