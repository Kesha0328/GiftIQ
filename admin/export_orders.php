<?php
require_once 'auth_check.php';
include '../config.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Orders');

$sheet->fromArray(["Order ID", "Date", "Customer", "Total", "Status"], NULL, 'A1');

$result = $conn->query("SELECT o.id, o.created_at, u.name, o.total, o.status
                        FROM orders o
                        LEFT JOIN users u ON o.user_id = u.id
                        ORDER BY o.created_at DESC");

$rowNum = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue("A$rowNum", $row['id']);
    $sheet->setCellValue("B$rowNum", $row['created_at']);
    $sheet->setCellValue("C$rowNum", $row['name']);
    $sheet->setCellValue("D$rowNum", $row['total']);
    $sheet->setCellValue("E$rowNum", $row['status']);
    $rowNum++;
}

$writer = new Xlsx($spreadsheet);
$filename = "orders_export_" . date("Y-m-d") . ".xlsx";

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=$filename");
$writer->save("php://output");
exit;
?>
