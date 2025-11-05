<?php
include '../config.php';
session_start(); // Start session after config, before header

// Fetch all collections
$collections = $conn->query("SELECT * FROM collections ORDER BY name ASC");

include "header.php"; 
?>

<link rel="stylesheet" href="assets/collection.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<main class="collection-main">

  <section class="collection-header">
    <h1>Our Collection</h1>
    <p>Hand-crafted hampers and unique gifts for every occasion.</p>
  </section>

  <?php if ($collections->num_rows > 0): ?>
    <?php while ($col = $collections->fetch_assoc()): ?>
      <section class="category-section">
        <h2><?= htmlspecialchars($col['name']); ?></h2>

        <div class="category-grid">
          <?php
          $products = $conn->query("SELECT * FROM products WHERE collection_id=".(int)$col['id']);
          if ($products->num_rows > 0):
            while ($row = $products->fetch_assoc()):
              $img = !empty($row['image']) && file_exists("../uploads/".$row['image'])
                  ? "../uploads/".$row['image']
                  : "assets/no-image.png";
          ?>
              <div class="card" data-scroll>
                <div class="card-image-container">
                  <img src="<?= $img; ?>" alt="<?= htmlspecialchars($row['name']); ?>">
                </div>
                <div class="card-content">
                  <h3><?= htmlspecialchars($row['name']); ?></h3>
                  <div class="details"><?= htmlspecialchars($row['description']); ?></div>
                  <p class="price">₹<?= number_format($row['price']); ?></p>

                  <form method="post" action="cart.php">
                    <input type="hidden" name="product_id" value="<?= $row['id']; ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" name="add_to_cart" class="btn-primary">
                      <i class="fas fa-cart-shopping"></i> Buy Now
                    </button>
                  </form>
                </div>
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
  <?php else: ?>
      <p class='no-products'>No collections have been added yet.</p>
  <?php endif; ?>

</main>

<?php include 'footer.php'; ?>

<script>
/* This is your excellent IntersectionObserver script.
  It adds the 'is-visible' class to elements with 'data-scroll'
  when they enter the viewport.
  Our CSS uses this to create the fade-in effect.
*/
const observer = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('is-visible');
    }
  });
}, { 
  threshold: 0.1 // Start fading in when 10% of the card is visible
});

// Observe all elements with [data-scroll]
document.querySelectorAll('[data-scroll]').forEach(el => {
  observer.observe(el);
});
</script>