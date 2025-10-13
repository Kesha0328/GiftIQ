<?php
include '../config.php'; // DB connection

if (!isset($_GET['order_id'])) {
    die("No order selected");
}

$order_id = intval($_GET['order_id']);

// Fetch order + customer info
$sql = "SELECT o.id, o.created_at, o.total, o.status,
            s.name AS customer_name, s.phone, s.address,
            s.city, s.postal_code, s.country, u.email
        FROM orders o
        LEFT JOIN shipping s ON o.id = s.order_id
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.id = $order_id";

$order = $conn->query($sql)->fetch_assoc();

// Fetch items
$sql_items = "SELECT p.name, oi.quantity
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = $order_id";
$items = $conn->query($sql_items);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Shipping Slip - Order #<?= $order['id']; ?></title>
    <link rel="stylesheet" href="adminpanel.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { text-align: center; margin-bottom: 30px; }
        .section { border: 1px solid #333; padding: 15px; margin-bottom: 20px; }
        .flex { display: flex; justify-content: space-between; }
        .box { width: 48%; border: 1px solid #666; padding: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table, th, td { border: 1px solid #333; }
        th, td { padding: 8px; text-align: left; }
        .footer { margin-top: 30px; font-size: 12px; text-align: center; }
        .print-btn { margin: 20px 0; text-align: center; }
    </style>
</head>
<body>
    <h2>Shipping Slip</h2>

    <div class="flex">
        <!-- Sender Section -->
        <div class="box">
            <h3>Sender</h3>
            <p><strong>Shop Name:</strong> GiftIQ</p>
            <p><strong>Email:</strong> support@giftiq.com</p>
            <p><strong>Phone:</strong> +91 1234567890</p>
            <p><strong>Address:</strong> Surat, India</p>
        </div>

        <!-- Receiver Section -->
        <div class="box">
            <h3>Receiver</h3>
            <p><strong>Name:</strong> <?= $order['customer_name']; ?></p>
            <p><strong>Email:</strong> <?= $order['email']; ?></p>
            <p><strong>Phone:</strong> <?= $order['phone']; ?></p>
            <p><strong>Address:</strong> <?= $order['address']; ?>, <?= $order['city']; ?> - <?= $order['postal_code']; ?>, <?= $order['country']; ?></p>
        </div>
    </div>

    <div class="section">
        <p><strong>Order ID:</strong> <?= $order['id']; ?></p>
        <p><strong>Date:</strong> <?= $order['created_at']; ?></p>
        <p><strong>Status:</strong> <?= $order['status']; ?></p>
    </div>
    <li>
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
    </li>

    <h3>Products</h3>
    <table>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
        </tr>
        <?php while($item = $items->fetch_assoc()) { ?>
        <tr>
            <td><?= $item['name']; ?></td>
            <td><?= $item['quantity']; ?></td>
        </tr>
        <?php } ?>
    </table>

    <p><strong>Total Amount:</strong> $<?= number_format($order['total'], 2); ?></p>

    <div class="footer">
        <p>Thank you for shopping with GiftIQ!</p>
    </div>

    <div class="card">
        <button onclick="window.print()" class="btn-primary">🖨 Print Shipping Slip</button>
    </div>
</body>
</html>
