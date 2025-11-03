<?php
include '../config.php';

$collections = $conn->query("SELECT * FROM collections");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Our Collection | Mad Smile</title>

  <link rel="stylesheet" href="assets/collection.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  
  <link rel="icon" type="image/png" href="../uploads/favicon.png" />
</head>
<body>

<?php include "header.php"; ?>

<section class="collection-header fadeInUp">
  <h1><span class="header-icon">✨</span> Our Collection</h1>
</section>

<?php while ($col = $collections->fetch_assoc()): ?>
  <section class="category-section">
    <h2><?= htmlspecialchars($col['name']); ?></h2>

    <div class="category-cards">
      <?php
      $products = $conn->query("SELECT * FROM products WHERE collection_id=".(int)$col['id']);
      if ($products->num_rows > 0):
        while ($row = $products->fetch_assoc()):
          $img = !empty($row['image']) && file_exists("../uploads/".$row['image'])
                ? "../uploads/".$row['image']
                : "assets/no-image.png";
      ?>
          <div class="card" data-scroll>
            <img src="<?= $img; ?>" alt="<?= htmlspecialchars($row['name']); ?>">
            <h3><?= htmlspecialchars($row['name']); ?></h3>
            <p>₹<?= number_format($row['price']); ?></p>
            <div class="details"><?= htmlspecialchars($row['description']); ?></div>

            <form method="post" action="cart.php">
              <input type="hidden" name="product_id" value="<?= $row['id']; ?>">
              <input type="hidden" name="quantity" value="1">
              <button type="submit" name="add_to_cart" class="btn-primary">
                <i class="fas fa-cart-shopping"></i> Buy Now
              </button>
            </form>
          </div>
      <?php
        endwhile;
      else:
        echo "<p class='no-products'>No products available in this collection yet.</p>";
      endif;
      ?>
    </div>
  </section>
<?php endwhile; ?>

<?php include 'footer.php'; ?>

<script>
const observer = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (entry.isIntersecting) entry.target.classList.add('is-visible');
  });
}, { threshold: 0.2 });

document.querySelectorAll('[data-scroll]').forEach(el => observer.observe(el));
</script>

</body>
</html>
