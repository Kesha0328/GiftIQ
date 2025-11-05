<?php
require '../config.php';

$oid = intval($_GET['order']);
$order = $conn->query("SELECT o.*, u.name, u.email FROM orders o LEFT JOIN users u ON o.user_id=u.id WHERE o.id=$oid")->fetch_assoc();
$items = $conn->query("SELECT * FROM order_items WHERE order_id=$oid");
?>
<html>
<head>
<style>
body { font-family: Arial; padding: 40px; }
table { width:100%; border-collapse:collapse; margin-top:20px; }
th,td { padding:10px; border-bottom:1px solid #ddd; }
h2 { margin-bottom: 4px; }
</style>
</head>
<body onload="window.print()">

<h2>Invoice #<?= $oid ?></h2>
<p><strong>Customer:</strong> <?= $order['name'] ?><br>
<strong>Email:</strong> <?= $order['email'] ?><br>
<strong>Date:</strong> <?= $order['created_at'] ?><br>
<strong>Tracking ID:</strong> <?= $order['tracking_id'] ?: 'Not Updated' ?></p>

<table>
<tr><th>Item</th><th>Qty</th><th>Price</th></tr>
<?php while($it = $items->fetch_assoc()): ?>
<tr>
    <td><?= $it['product_id'] ?></td>
    <td><?= $it['quantity'] ?></td>
    <td>₹<?= number_format($it['price'],2) ?></td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
