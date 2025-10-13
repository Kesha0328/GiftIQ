<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    // redirect guest to login
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart']) || empty($_SESSION['shipping'])) {
    header("Location: cart.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $shipping = $_SESSION['shipping'];

    // Insert order
    $conn->query("INSERT INTO orders (user_id, total, status, created_at) VALUES ($user_id, 0, 'Pending', NOW())");
    $order_id = $conn->insert_id;

    $total = 0;
    foreach ($_SESSION['cart'] as $product_id => $qty) {
    $res = $conn->query("SELECT * FROM products WHERE id=$product_id");
    if ($res && $res->num_rows > 0) {
        $prod = $res->fetch_assoc();
        $subtotal = $prod['price'] * $qty;
        $total += $subtotal;

        $conn->query("INSERT INTO order_items (order_id, product_id, quantity)
                        VALUES ($order_id, $product_id, $qty)");
    } else {
        // Skip invalid product IDs
        continue;
    }
    }


    // Update total
    $conn->query("UPDATE orders SET total=$total WHERE id=$order_id");

    // Insert shipping
    $conn->query("INSERT INTO shipping (order_id, name, phone, address, city, postal_code, country)
                    VALUES ($order_id, '{$shipping['name']}', '{$shipping['phone']}',
                        '{$shipping['address']}', '{$shipping['city']}',
                        '{$shipping['postal_code']}', '{$shipping['country']}')");

    // Clear cart + shipping
    unset($_SESSION['cart'], $_SESSION['shipping']);

    header("Location: order_success.php?order_id=$order_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment</title>
    <link rel="stylesheet" href="customerpanel.css">
</head>
<body>
    <div class="collection-header">
        <h1>💰 Payment</h1>
    </div>

    <div class="customize-section">
        <form method="post">
            <p><label><input type="radio" name="payment" value="COD" checked> Cash on Delivery</label></p>
            <div class="card">
            <button type="submit" class="btn-primary">Place Order</button>
        </form>
    </div>
</body>
</html>
