<?php
include '../config.php';

// Delete product
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($conn->query("DELETE FROM products WHERE id=$id")) {
        header("Location: manage_products.php?msg=deleted");
    } else {
        header("Location: manage_products.php?msg=error");
    }
    exit;
}

// Fetch products
$sql = "SELECT p.*, c.name AS collection_name
        FROM products p
        LEFT JOIN collections c ON p.collection_id = c.id
        ORDER BY p.id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
    <link rel="stylesheet" href="adminpanel.css">
</head>
<body>
    <!-- Navbar -->
<div class="navbar">
    <div class="logo">Admin Panel</div>
    <div class="nav-links">
        <a href="add_product.php">Add Product</a>
        <a href="manage_products.php" class="active">Manage Products</a>
        <a href="manage_orders.php">Manage Orders</a>
    </div>
</div>

    <!-- Header -->
<div class="collection-header fadeInUp">
    <h1><span class="header-icon">🛠️</span> Manage Products</h1>
</div>

    <!-- Message -->
    <?php if (isset($_GET['msg'])): ?>
    <div class="message
        <?php echo ($_GET['msg'] == 'deleted' || $_GET['msg'] == 'updated') ? 'success' : 'error'; ?>">
        <?php
        if ($_GET['msg'] == 'deleted') echo "✅ Product deleted successfully.";
        elseif ($_GET['msg'] == 'updated') echo "✅ Product updated successfully.";
        else echo "❌ Something went wrong.";
        ?>
    </div>
    <?php endif; ?>

    <!-- Product list -->
    <div class="category-section">
    <h2>All Products</h2>
    <div class="category-cards">
        <?php while($row = $result->fetch_assoc()): ?>
        <div class="card">
            <img src="../uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
            <h3><?php echo $row['name']; ?></h3>
            <p>₹<?php echo $row['price']; ?></p>
            <div class="details">
            Collection: <?php echo $row['collection_name']; ?><br>
            <?php echo $row['description']; ?>
            </div>
            <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn-primary">Edit</a>
            <a href="manage_products.php?delete=<?php echo $row['id']; ?>"
                class="btn-primary" style="background:#e74c3c;"
                onclick="return confirm('Delete this product?');">Delete</a>
        </div>
        <?php endwhile; ?>
    </div>
    </div>
</body>
</html>
