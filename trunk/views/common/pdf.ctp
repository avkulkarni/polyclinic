<?php
$textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans'

// init pdf
$tcpdf->init();

$tcpdf->pdf->AddPage();
$tcpdf->pdf->writeHTML( $table, true, 0, true, 0);

$tcpdf->pdf->Output('filename.pdf', 'D');
?>