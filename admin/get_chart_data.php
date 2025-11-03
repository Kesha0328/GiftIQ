<?php
include '../config.php';
header('Content-Type: application/json');

$daily = [];
$res = $conn->query("SELECT DATE(created_at) AS d, SUM(total) AS s FROM orders GROUP BY DATE(created_at) ORDER BY d ASC");
while ($r = $res->fetch_assoc()) {
  $daily['labels'][] = $r['d'];
  $daily['values'][] = (float)$r['s'];
}

$monthly = [];
$res2 = $conn->query("SELECT DATE_FORMAT(created_at, '%Y-%m') AS m, SUM(total) AS s FROM orders GROUP BY m ORDER BY m ASC");
while ($r = $res2->fetch_assoc()) {
  $monthly['labels'][] = $r['m'];
  $monthly['values'][] = (float)$r['s'];
}

$top = [];
$res3 = $conn->query("
  SELECT p.name AS product, SUM(oi.quantity) AS qty
  FROM order_items oi 
  JOIN products p ON p.id = oi.product_id
  GROUP BY oi.product_id 
  ORDER BY qty DESC 
  LIMIT 5
");
while ($r = $res3->fetch_assoc()) {
  $top['labels'][] = $r['product'];
  $top['values'][] = (int)$r['qty'];
}

echo json_encode([
  'daily' => $daily,
  'monthly' => $monthly,
  'top' => $top
]);
?>
