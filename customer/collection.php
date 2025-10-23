<?php
include '../config.php';

$collections = $conn->query("SELECT * FROM collections");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Our Collection</title>
  <link rel="stylesheet" href="assets/collection.css">
</head>
<body>

<?php include "header.php"; ?>


  <div class="collection-header fadeInUp">
    <h1><span class="header-icon">✨</span> Our Collection</h1>
  </div>

  <?php while($col = $collections->fetch_assoc()): ?>
    <div class="category-section">
      <h2><?php echo $col['name']; ?></h2>
      <div class="category-cards">
        <?php
        $products = $conn->query("SELECT * FROM products WHERE collection_id=".$col['id']);
        while($row = $products->fetch_assoc()):
        ?>
          <div class="card">
            <img src="../uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
            <h3><?php echo $row['name']; ?></h3>
            <p>₹<?php echo $row['price']; ?></p>
            <div class="details"><?php echo $row['description']; ?></div>

            <form method="post" action="cart.php?action=add&id=<?= $row['id']; ?>">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="btn-primary">Buy Now</button>
            </form>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  <?php endwhile; ?>

          <?php include 'footer.php'; ?>


</body>
</html>
