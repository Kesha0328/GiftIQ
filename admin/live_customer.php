<?php
include '../config.php';
$count = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'] ?? 0;
echo $count;
?>
