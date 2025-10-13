<?php
session_start();
include '../config.php';

if (!isset($_GET['order_id'])) {
    die("No order selected");
}


$order_id = intval($_GET['order_id']);

// Fetch order + shipping + user
$sql = "SELECT o.id, o.created_at, o.total, o.status,
                s.name, s.phone, s.address, s.city, s.postal_code, s.country,
                u.email
        FROM orders o
        LEFT JOIN shipping s ON o.id = s.order_id
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.id = $order_id";
$order = $conn->query($sql)->fetch_assoc();

// Fetch items
$sql_items = "SELECT p.name, oi.quantity, p.price
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = $order_id";
$items = $conn->query($sql_items);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - Order #<?= $order['id']; ?></title>
    <link rel="stylesheet" href="customerpanel.css">
    <style>
        @media print {
            .print-btn { display: none; }
        }
        table {
            width: 100%;
            margin-top: 1rem;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background: var(--accent-pink);
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="collection-header">
        <h1>🧾 Invoice</h1>
    </div>

    <div class="customize-section">
        <h2 style="text-align:center;">Thank you for your order!</h2>

        <h3>Customer Details</h3>
        <p><strong>Name:</strong> <?= htmlspecialchars($order['name']); ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($order['email']); ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']); ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($order['address']); ?>, 
            <?= htmlspecialchars($order['city']); ?> - <?= htmlspecialchars($order['postal_code']); ?>, 
            <?= htmlspecialchars($order['country']); ?></p>

        <h3>Order Details</h3>
        <p><strong>Order ID:</strong> #<?= $order['id']; ?></p>
        <p><strong>Date:</strong> <?= $order['created_at']; ?></p>
        <p><strong>Status:</strong> <?= $order['status']; ?></p>
        <p><strong>Payment Method:</strong> Cash on Delivery</p>

        <h3>Products</h3>
        <table>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
            <?php while($item = $items->fetch_assoc()) {
                $subtotal = $item['price'] * $item['quantity']; ?>
            <tr>
                <td><?= htmlspecialchars($item['name']); ?></td>
                <td><?= $item['quantity']; ?></td>
                <td>$<?= number_format($item['price'], 2); ?></td>
                <td>$<?= number_format($subtotal, 2); ?></td>
            </tr>
            <?php } ?>
            <tr>
                <td colspan="3" style="text-align:right;"><strong>Total</strong></td>
                <td><strong>$<?= number_format($order['total'], 2); ?></strong></td>
            </tr>

            <tr>
    <td>
    <?php if (!empty($item['custom_details'])):
        $details = json_decode($item['custom_details'], true); ?>
        <ul style="margin:5px 0; padding-left:15px;">
            <?php foreach ($details as $key => $value): ?>
            <?php if ($value && $value !== 'None'): ?>
                <li><?= $key; ?>: <?= htmlspecialchars($value); ?></li>
            <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>


        </table>

        <div style="text-align:center; margin-top:1.5rem;" class="print-btn">
        <button class="btn-primary" onclick="window.print()">🖨 Print Invoice</button>
        <a href="my_order.php" class="btn-primary">⬅ Back</a>
        </div>

    </div>
</body>
</html>
