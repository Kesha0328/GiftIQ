<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    // redirect guest to login
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

if (!isset($_GET['order_id'])) {
    header("Location: collection.php");
    exit;
}

$order_id = intval($_GET['order_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Success</title>
    <link rel="stylesheet" href="customerpanel.css">
</head>
<body>
    <div class="collection-header">
        <h1>✅ Order Placed Successfully</h1>
    </div>

    <div class="message success">
        <p>Thank you! Your order <strong>#<?= $order_id; ?></strong> has been placed successfully.</p>
    </div>

    <div class="customize-section" style="text-align:center;">
        <a href="print_invoice.php?order_id=<?= $order_id; ?>" class="btn-primary" target="_blank">🧾 Print Invoice</a>
        <a href="my_order.php" class="btn-primary">View My Orders</a>
        <a href="collection.php" class="btn-primary">Continue Shopping</a>
    </div>
</body>
</html>
