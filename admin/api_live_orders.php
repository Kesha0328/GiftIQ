<?php
include __DIR__ . '/../config.php';
header('Content-Type: application/json');
$res=$conn->query("SELECT COUNT(*) AS c FROM orders WHERE DATE(created_at)=CURDATE()");
echo json_encode(['count'=>$res?$res->fetch_assoc()['c']:0]);
