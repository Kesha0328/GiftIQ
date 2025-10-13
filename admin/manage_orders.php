<?php
session_start();
include '../config.php';

// Initialize filter
$where = "";
if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
    $from_date = date('Y-m-d', strtotime($_GET['from_date']));
    $to_date   = date('Y-m-d', strtotime($_GET['to_date']));
    $where = "WHERE DATE(o.created_at) BETWEEN '$from_date' AND '$to_date'";
}

// Handle status update
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status   = $_POST['status'];
    $conn->query("UPDATE orders SET status='$status' WHERE id=$order_id");
}

// Handle CSV export
if (isset($_GET['export']) && $_GET['export'] == "csv") {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=orders_export.csv');
    $output = fopen("php://output", "w");
    fputcsv($output, ["Order ID", "Date", "Customer", "Products", "Total", "Status"]);

    $sql = "SELECT o.id, o.created_at, o.total, o.status,
                    u.name AS customer_name,
                    GROUP_CONCAT(p.name SEPARATOR ', ') AS products
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN products p ON oi.product_id = p.id
            $where
            GROUP BY o.id
            ORDER BY o.created_at DESC";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['id'],
            $row['created_at'],
            $row['customer_name'],
            $row['products'],
            $row['total'],
            $row['status']
        ]);
    }
    fclose($output);
    exit;
}

// Fetch orders for display
$sql = "SELECT o.id, o.created_at, o.total, o.status,
                u.name AS customer_name,
                GROUP_CONCAT(p.name SEPARATOR ', ') AS products
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        $where
        GROUP BY o.id
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="adminpanel.css">
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
        form { display:inline; }
    </style>
</head>
<body>

    <!-- Navbar -->
<div class="navbar">
    <div class="logo">Admin Panel</div>
    <div class="nav-links">
        <a href="add_product.php">Add Product</a>
        <a href="manage_products.php" >Manage Products</a>
        <a href="manage_orders.php" class="active">Manage Orders</a>
    </div>
</div>
    <div class="collection-header">
        <h1>📦 Manage Orders</h1>
    </div>

    <div class="customize-section">
        <!-- Filter by Date -->
        <form method="get" style="margin-bottom:1rem;">
            <label>From: </label>
            <input type="date" name="from_date" value="<?= $_GET['from_date'] ?? ''; ?>">
            <label>To: </label>
            <input type="date" name="to_date" value="<?= $_GET['to_date'] ?? ''; ?>">
            <button type="submit" class="btn-primary">Filter</button>
            <a href="manage_orders.php" class="btn-primary">Reset</a>
            <button type="submit" name="export" value="csv" class="btn-primary">Export CSV</button>
        </form>

        <?php if ($result->num_rows == 0) { ?>
            <div class="message error">No orders found.</div>
        <?php } else { ?>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Products</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php while($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td>#<?= $row['id']; ?></td>
                    <td><?= $row['created_at']; ?></td>
                    <td><?= htmlspecialchars($row['customer_name']); ?></td>
                    <td><?= htmlspecialchars($row['products']); ?></td>
                    <td>$<?= number_format($row['total'], 2); ?></td>
                    <td>
                        <!-- Status dropdown -->
                        <form method="post">
                            <input type="hidden" name="order_id" value="<?= $row['id']; ?>">
                            <select name="status" class="filter-select" onchange="this.form.submit()">
                                <option value="Pending"   <?= $row['status']=='Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Processing" <?= $row['status']=='Processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="Shipped"   <?= $row['status']=='Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="Delivered" <?= $row['status']=='Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="Cancelled" <?= $row['status']=='Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                            <input type="hidden" name="update_status" value="1">
                        </form>
                    </td>
                    <td>
                        <a href="../customer/print_invoice.php?order_id=<?= $row['id']; ?>" class="btn-primary" target="_blank">View Invoice</a>
                        <a href="print_shipping.php?order_id=<?= $row['id']; ?>" class="btn-primary" target="_blank">View Shipping Slip</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>
</body>
</html>
