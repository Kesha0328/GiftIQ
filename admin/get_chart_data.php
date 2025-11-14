<?php
include __DIR__ . '/../config.php';

// Daily sales
$daily = $conn->query("
  SELECT DATE(created_at) AS label, SUM(total) AS value
  FROM orders
  GROUP BY DATE(created_at)
  ORDER BY DATE(created_at)
");
$dailyData = ['labels'=>[],'values'=>[]];
while($r=$daily->fetch_assoc()){ $dailyData['labels'][]=$r['label']; $dailyData['values'][]=(float)$r['value']; }

// Monthly sales
$monthly = $conn->query("
  SELECT DATE_FORMAT(created_at,'%Y-%m') AS label, SUM(total) AS value
  FROM orders
  GROUP BY DATE_FORMAT(created_at,'%Y-%m')
  ORDER BY label
");
$monthlyData = ['labels'=>[],'values'=>[]];
while($r=$monthly->fetch_assoc()){ $monthlyData['labels'][]=$r['label']; $monthlyData['values'][]=(float)$r['value']; }

// Top selling products
$top = $conn->query("
  SELECT p.name AS label, SUM(oi.quantity) AS value
  FROM order_items oi
  JOIN products p ON oi.product_id=p.id
  GROUP BY p.name
  ORDER BY value DESC
  LIMIT 5
");
$topData = ['labels'=>[],'values'=>[]];
while($r=$top->fetch_assoc()){ $topData['labels'][]=$r['label']; $topData['values'][]=(int)$r['value']; }

header('Content-Type: application/json');
echo json_encode(['daily'=>$dailyData,'monthly'=>$monthlyData,'top'=>$topData]);
