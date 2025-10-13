<?php
include '../config.php';
$id = intval($_GET['id']);

// Fetch product + collections
$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
$collections = $conn->query("SELECT * FROM collections");

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $collection_id = $_POST['collection_id'];

    // Upload new image if chosen
    if (!empty($_FILES['image']['name'])) {
        $imageName = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/".$imageName);
    } else {
        $imageName = $product['image'];
    }

    $sql = "UPDATE products
            SET name='$name', description='$description', price='$price', image='$imageName', collection_id='$collection_id'
            WHERE id=$id";
    $conn->query($sql);

    header("Location: manage_products.php?msg=updated");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="adminpanel.css">
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
    <div class="logo">Admin Panel</div>
    <div class="nav-links">
        <a href="manage_products.php">Manage Products</a>
        <a href="add_product.php">Add Product</a>
    </div>
    </div>

    <!-- Form -->
    <div class="customize-section">
    <h2>Edit Product</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="customize-options">
        <div class="customize-card">
            <label>Product Name</label>
            <input type="text" name="name" value="<?php echo $product['name']; ?>" required>
        </div>
        <div class="customize-card">
            <label>Description</label>
            <textarea name="description" required><?php echo $product['description']; ?></textarea>
        </div>
        <div class="customize-card">
            <label>Price</label>
            <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required>
        </div>
        <div class="customize-card">
            <label>Collection</label>
            <select name="collection_id" required>
            <?php while($row = $collections->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>"
                <?php if ($row['id'] == $product['collection_id']) echo "selected"; ?>>
                <?php echo $row['name']; ?>
                </option>
            <?php endwhile; ?>
            </select>
        </div>
        <div class="customize-card">
            <label>Product Image</label><br>
            <img src="uploads/<?php echo $product['image']; ?>" width="120"><br><br>
            <input type="file" name="image" accept="image/*">
        </div>
        </div class="customize-card">
        <button type="submit" class="btn-primary">Update Product</button>
    </form>
    </div>
</body>
</html>
