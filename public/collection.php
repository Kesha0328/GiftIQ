<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mad Smile - Collections</title>
  <link rel="stylesheet" href="assets/collection.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body>
<header class="navbar">
  <div class="logo"><img src="images/logo.png" alt="logo"></div>

  <nav class="nav-links">
    <a href="index.php">Home</a>
    <a href="collection.php" class="active">Collection</a>
    <a href="about.php">About</a>
    <a href="contact.php">Contact</a>

    <?php if (isset($_SESSION['fullname'])): ?>
      <!-- user is logged in -->
      <a href="/GIFTIQ/views/aut/profile.php">Profile (<?= htmlspecialchars($_SESSION['fullname']); ?>)</a>
      <a href="/GIFTIQ/views/aut/logout.php">Logout</a>
    <?php else: ?>
      <!-- user not logged in -->
      <a href="/GIFTIQ/views/aut/login.php">Login</a>
      <a href="/GIFTIQ/views/aut/signup.php">Sign Up</a>
    <?php endif; ?>
  </nav>
</header>

  <section class="collection-header fadeInUp">
    <h1>
      <span class="header-icon"><i class="fas fa-gift"></i></span>
      Our Gift Hampers
      <span class="header-icon"><i class="fas fa-heart"></i></span>
    </h1>
    <div class="filters">
      <select class="filter-select">
        <option value="">Filter by Occasion</option>
        <option>Birthday</option>
        <option>Anniversary</option>
        <option>Festive</option>
        <option>Corporate</option>
      </select>
      <select class="filter-select">
        <option value="">Sort by Price</option>
        <option>Low to High</option>
        <option>High to Low</option>
      </select>
    </div>
  </section>

  <!-- Category: Birthday -->
  <section class="category-section fadeInUp">
    <h2>Birthday Hampers</h2>
    <div class="category-cards scroll-x">
      <div class="card">
        <img src="images/1.jpeg" alt="Birthday Hamper 1">
        <h3>Birthday Bliss</h3>
        <p>₹1,299</p>
        <div class="details">Includes chocolates, mug, greeting card</div>
        <a href="collection.html" class="btn-primary">Add to Cart</a>
      </div>
      <div class="card">
        <img src="images/2.jpeg" alt="Birthday Hamper 2">
        <h3>Sweet Surprise</h3>
        <p>₹1,499</p>
        <div class="details">Assorted sweets, plush toy, birthday badge</div>
        <a href="collection.html" class="btn-primary">Add to Cart</a>
      </div>
      <div class="card">
        <img src="images/3.jpeg" alt="Birthday Hamper 3">
        <h3>Party Box</h3>
        <p>₹1,899</p>
        <div class="details">Party poppers, snacks, custom photo frame</div>
        <a href="collection.html" class="btn-primary">Add to Cart</a>
      </div>
      <div class="card">
        <img src="images/4.jpeg" alt="Birthday Hamper 4">
        <h3>Colorful Wishes</h3>
        <p>₹1,599</p>
        <div class="details">Colorful balloons, cake jar, birthday cap</div>
        <a href="collection.html" class="btn-primary">Add to Cart</a>
      </div>
    </div>
  </section>

  <!-- Category: Anniversary -->
  <section class="category-section fadeInUp">
    <h2>Anniversary Hampers</h2>
    <div class="category-cards scroll-x">
      <div class="card">
        <img src="images/5.jpeg" alt="Anniversary Hamper 1">
        <h3>Elegant Love</h3>
        <p>₹1,599</p>
        <div class="details">Rose bouquet, chocolates, keepsake box</div>
        <a href="collection.html" class="btn-primary">Add to Cart</a>
      </div>
      <div class="card">
        <img src="images/6.jpeg" alt="Anniversary Hamper 2">
        <h3>Romantic Treat</h3>
        <p>₹1,999</p>
        <div class="details">Wine glass set, gourmet snacks, love card</div>
        <a href="collection.html" class="btn-primary">Add to Cart</a>
      </div>
      <div class="card">
        <img src="images/7.jpeg" alt="Anniversary Hamper 3">
        <h3>Forever Together</h3>
        <p>₹2,299</p>
        <div class="details">Personalized frame, scented candles</div>
        <a href="collection.html" class="btn-primary">Add to Cart</a>
      </div>
      <div class="card">
        <img src="images/8.jpeg" alt="Anniversary Hamper 4">
        <h3>Golden Moments</h3>
        <p>₹2,499</p>
        <div class="details">Gold-themed decor, cookies, couple mugs</div>
        <a href="collection.html" class="btn-primary">Add to Cart</a>
      </div>
    </div>
  </section>

  <!-- Category: Festive -->
  <section class="category-section fadeInUp">
    <h2>Festive Hampers</h2>
    <div class="category-cards scroll-x">
      <div class="card">
        <img src="images/9.jpeg" alt="Festive Hamper 1">
        <h3>Festive Cheer</h3>
        <p>₹1,499</p>
        <div class="details">Dry fruits, diyas, festive sweets</div>
        <a href="collection.html" class="btn-primary">Add to Cart</a>
      </div>
      <div class="card">
        <img src="images/10.jpeg" alt="Festive Hamper 2">
        <h3>Joyful Basket</h3>
        <p>₹1,799</p>
        <div class="details">Assorted snacks, candles, festival card</div>
        <a href="collection.html" class="btn-primary">Add to Cart</a>
      </div>
      <div class="card">
        <img src="images/5.jpeg" alt="Festive Hamper 3">
        <h3>Celebration Crate</h3>
        <p>₹2,099</p>
        <div class="details">Gift box, sweets, festive decor</div>
        <a href="collection.html" class="btn-primary">Add to Cart</a>
      </div>
      <div class="card">
        <img src="images/6.jpeg" alt="Festive Hamper 4">
        <h3>Seasonal Spark</h3>
        <p>₹1,999</p>
        <div class="details">Sparkling lights, cookies, festival mug</div>
        <a href="collection.html" class="btn-primary">Add to Cart</a>
      </div>
    </div>
  </section>

  <!-- Category: Corporate -->
  <section class="category-section fadeInUp">
    <h2>Corporate Hampers</h2>
    <div class="category-cards scroll-x">
      <div class="card">
        <img src="images/1.jpeg" alt="Corporate Hamper 1">
        <h3>Executive Box</h3>
        <p>₹1,999</p>
        <div class="details">Desk organizer, premium pen, snacks</div>
        <a href="collection.html" class="btn-primary">Add to Cart</a>
      </div>
      <div class="card">
        <img src="images/2.jpeg" alt="Corporate Hamper 2">
        <h3>Team Treat</h3>
        <p>₹2,299</p>
        <div class="details">Coffee mug, cookies, thank you card</div>
        <a href="collection.html" class="btn-primary">Add to Cart</a>
      </div>
      <div class="card">
        <img src="images/3.jpeg" alt="Corporate Hamper 3">
        <h3>Office Joy</h3>
        <p>₹2,499</p>
        <div class="details">Planner, chocolates, branded bottle</div>
        <a href="collection.html" class="btn-primary">Add to Cart</a>
      </div>
      <div class="card">
        <img src="images/4.jpeg" alt="Corporate Hamper 4">
        <h3>Success Pack</h3>
        <p>₹2,799</p>
        <div class="details">Motivational book, snacks, trophy</div>
        <a href="collection.html" class="btn-primary">Add to Cart</a>
      </div>
    </div>
  </section>

  <section class="customize-section">
  <h2>Customize Your Hamper</h2>
  <div class="customize-options">
    <div class="customize-card">
      <i class="fas fa-pen-nib"></i>
      <h3>Add a Personal Message</h3>
      <p>Include a heartfelt note to make your gift extra special.</p>
      <button class="btn-primary">Add Message</button>
    </div>
    <div class="customize-card">
      <i class="fas fa-box-open"></i>
      <h3>Choose Your Items</h3>
      <p>Select your favorite products to build a unique hamper.</p>
      <button class="btn-primary">Customize Items</button>
    </div>
    <div class="customize-card">
      <i class="fas fa-gift"></i>
      <h3>Pick Your Packaging</h3>
      <p>Choose from elegant, eco-friendly packaging options.</p>
      <button class="btn-primary">Select Packaging</button>
    </div>
  </div>
</section>

  <footer class="footer">
    <p>&copy; 2025 Mad Smile – Because every smile deserves a gift.</p>
    <div class="social-icons">
      <a href="mailto:madsmileee@gmail.com" target="_blank" title="Email"><i class="fas fa-envelope"></i></a>
      <a href="https://github.com/Kesha0328" target="_blank" title="GitHub"><i class="fab fa-github"></i></a>
      <a href="https://www.instagram.com/mad_smileee" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
    </div>
  </footer>
</body>
</html>