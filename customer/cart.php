<?php
session_start();
include '../config.php';
if (!isset($_SESSION['user_id'])) {
    // redirect guest to login
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart actions
if (isset($_GET['action'])) {
    $id = intval($_GET['id']);
    switch ($_GET['action']) {
        case "add":
            $qty = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
            $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty;
            break;
        case "remove":
            unset($_SESSION['cart'][$id]);
            break;
        case "empty":
            $_SESSION['cart'] = [];
            break;
    }
    header("Location: cart.php");
    exit;
}

$products = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(",", array_keys($_SESSION['cart']));
    $sql = "SELECT * FROM products WHERE id IN ($ids)";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $row['quantity'] = $_SESSION['cart'][$row['id']];
        $row['subtotal'] = $row['price'] * $row['quantity'];
        $products[] = $row;
        $total += $row['subtotal'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Cart</title>
    <link rel="stylesheet" href="customerpanel.css">
</head>
<body>

<?php include "header.php"; ?>

    <div class="collection-header">
        <h1>🛒 My Cart</h1>
    </div>

    <div class="category-section">
        <?php if (empty($products)) { ?>
            <div class="message error">Your cart is empty.</div>
        <?php } else { ?>
            <table style="width:100%; background:#fff; border-radius:16px; box-shadow: var(--shadow);">
                <tr style="background: var(--accent-pink); color:#fff;">
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($products as $p) { ?>
                <tr>
                    <td><?= htmlspecialchars($p['name']); ?></td>
                    <td>$<?= number_format($p['price'], 2); ?></td>
                    <td><?= $p['quantity']; ?></td>
                    <td>$<?= number_format($p['subtotal'], 2); ?></td>
                    <td>
                        <a class="btn-primary" href="cart.php?action=remove&id=<?= $p['id']; ?>">Remove</a>
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <td colspan="3"><strong>Total</strong></td>
                    <td colspan="2"><strong>$<?= number_format($total, 2); ?></strong></td>
                </tr>
            </table>

            <div class= "card" style="margin-top:1.5rem; display:flex; gap:1rem;">
                <a class="btn-primary" href="cart.php?action=empty">Empty Cart</a>
                <a class="btn-primary" href="checkout.php">Proceed to Checkout</a>
            </div>
        <?php } ?>
    </div>
    <?php include 'footer.php'; ?>

</body>
</html>
