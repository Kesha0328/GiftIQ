<?php
require '../config.php';

$oid = intval($_GET['order']);
$order = $conn->query("SELECT o.*, u.name, u.email FROM orders o LEFT JOIN users u ON o.user_id=u.id WHERE o.id=$oid")->fetch_assoc();
$items = $conn->query("SELECT * FROM order_items WHERE order_id=$oid");

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);

$pdf->Cell(0,10,"Packing Slip - Order #$oid",0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Ln(4);

$pdf->Cell(0,6,"Customer: {$order['name']}",0,1);
$pdf->Cell(0,6,"Email: {$order['email']}",0,1);
$pdf->Cell(0,6,"Tracking ID: " . ($order['tracking_id'] ?: 'Not Assigned'),0,1);
$pdf->Ln(4);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(100,8,"Item",1);
$pdf->Cell(30,8,"Qty",1);
$pdf->Cell(40,8,"Price",1);
$pdf->Ln();

$pdf->SetFont('Arial','',12);
while($it = $items->fetch_assoc()) {
    $pdf->Cell(100,8,"Product: {$it['product_id']}",1);
    $pdf->Cell(30,8,$it['quantity'],1);
    $pdf->Cell(40,8,"₹".$it['price'],1);
    $pdf->Ln();
}

$pdf->Output();
