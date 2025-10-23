<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    // redirect guest to login
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}
// Require login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT o.id, o.created_at, o.total, o.status,
                GROUP_CONCAT(p.name SEPARATOR ', ') AS products
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = $user_id
        GROUP BY o.id
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <link rel="stylesheet" href="customerpanel.css">
    <style>
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
<header class="navbar">
    <div class="logo"><img src="images/logo.png" a href="../index.php" alt="logo"></div>

<nav class="nav-links">
    <a href="../index.php">Home</a>
    <a href="collection.php">Collection</a>
    <a href="about.php">About</a>
    <a href="my_order.php" class="active" >My Order</a>
    <a href="contact.php">Contact</a>

    <?php if (isset($_SESSION['fullname'])): ?>
    <!-- user is logged in -->
    <a href="profile.php">Profile (<?= htmlspecialchars($_SESSION['fullname']); ?>)</a>
    <a href="logout.php">Logout</a>
    <?php else: ?>
    <!-- user not logged in -->
    <a href="login.php">Login</a>
    <a href="register.php">Sign Up</a>
    <?php endif; ?>
    </nav>
</header>

    <div class="collection-header">
        <h1>📜 My Orders</h1>
    </div>

    <div class="customize-section">
        <?php if ($result->num_rows == 0) { ?>
            <div class="message error">You have no past orders.</div>
        <?php } else { ?>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Products</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
                <?php while($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td>#<?= $row['id']; ?></td>
                    <td><?= $row['created_at']; ?></td>
                    <td><?= $row['status']; ?></td>
                    <td><?= $row['products']; ?></td>
                    <td>$<?= number_format($row['total'], 2); ?></td>
                    <td>
                        <a href="print_invoice.php?order_id=<?= $row['id']; ?>" class="btn-primary" target="_blank">Print Invoice</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>

    <?php include 'footer.php'; ?>

</body>
</html>
