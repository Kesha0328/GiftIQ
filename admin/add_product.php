<?php
include '../config.php'; // your DB connection

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $collection_id = $_POST['collection_id'];

    // Handle image upload
    $imageName = $_FILES['image']['name'];
    $tmpName   = $_FILES['image']['tmp_name'];
    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $targetFile = $targetDir . basename($imageName);
    move_uploaded_file($tmpName, $targetFile);

    // Insert product
    $sql = "INSERT INTO products (name, description, price, image, collection_id) 
            VALUES ('$name', '$description', '$price', '$imageName', '$collection_id')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Product added successfully!');window.location='add_product.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch collections for dropdown
$collections = $conn->query("SELECT * FROM collections");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Product</title>
  <link rel="stylesheet" href="adminpanel.css">
</head>
<body>
  <div class="navbar">
    <div class="logo">Admin Panel</div>
    <div class="nav-links">
      <a href="add_product.php" class="active">Add Product</a>
      <a href="manage_products.php">Manage Products</a>
      <a href="manage_orders.php" >Manage Orders</a>
    </div>
  </div>

  <div class="customize-section">
    <h2>Add New Product</h2>
    <form action="" method="POST" enctype="multipart/form-data">
      <div class="customize-options">
        <div class="customize-card">
          <label>Product Name</label>
          <input type="text" name="name" required>
        </div>
        <div class="customize-card">
          <label>Description</label>
          <textarea name="description" required></textarea>
        </div>
        <div class="customize-card">
          <label>Price</label>
          <input type="number" step="0.01" name="price" required>
        </div>
        <div class="customize-card">
          <label>Collection</label>
          <select name="collection_id" required>
            <option value="">-- Select Collection --</option>
            <?php while ($row = $collections->fetch_assoc()): ?>
              <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="customize-card">
          <label>Product Image</label>
          <input type="file" name="image" accept="image/*" required>
        </div>
      <div class="customize-card">
        <button type="submit" class="btn-primary">Add Product</button>
      </div>
    </form>
  </div>
</body>
</html>
