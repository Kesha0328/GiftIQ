<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mad Smile | Gift Hampers</title>

  <link rel="stylesheet" href="customer/assets/main.css">
  <link rel="stylesheet" href="customer/assets/index.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="icon" type="image/png" href="uploads/favicon.png" />
  <script defer src="customer/assets/slider.js"></script>
</head>
<body>

  <?php include "customer/header.php"; ?>

  <section class="hero" id="hero">
    <div class="hero-slider">
      <div class="hero-slide active">
        <img src="customer/images/1.jpeg" alt="Classic Hamper">
        <div class="hero-slide-text">
          <h1>Crafted with Joy, Wrapped with Love</h1>
          <p>Discover the perfect gift hamper for every occasion</p>
          <a href="customer/collection.php" class="btn-primary">Order Now</a>
        </div>
      </div>

      <div class="hero-slide">
        <img src="customer/images/2.jpeg" alt="Birthday Bliss">
        <div class="hero-slide-text">
          <h1>Birthday Bliss Hampers</h1>
          <p>Make birthdays extra special with our curated hampers</p>
          <a href="customer/collection.php" class="btn-primary">Order Now</a>
        </div>
      </div>

      <div class="hero-slide">
        <img src="customer/images/3.jpeg" alt="Elegant Joy Crate">
        <div class="hero-slide-text">
          <h1>Elegant Joy Crate</h1>
          <p>Simply Elegant. Purely Joyful. Thoughtfully curated for moments that matter.</p>
          <a href="customer/collection.php" class="btn-primary">Order Now</a>
        </div>
      </div>
    </div>
  </section>

  <section class="about-hampers">
    <h2>Why Choose Mad Smile Hampers?</h2>
    <p>
      Each hamper is thoughtfully curated to bring happiness and surprise to your loved ones. 
      From birthdays to anniversaries, our hampers are designed to make every moment memorable.
    </p>
    <ul>
      <li>🎁 Handpicked premium products</li>
      <li>✨ Customizable options for every occasion</li>
      <li>🌱 Eco-friendly packaging</li>
      <li>🚚 Fast & safe delivery</li>
    </ul>
  </section>

  <section class="featured">
  <h2>Best Sellers</h2>

  <div class="cards">

  <?php
    require "config.php"; // ✅ ensures DB connection

    $sql = "SELECT * FROM products WHERE bestseller = 1 ORDER BY id DESC LIMIT 6";
    $res = $conn->query($sql);

    while($p = $res->fetch_assoc()):
  ?>
      <div class="card" data-scroll>
        <img src="uploads/<?= $p['image'] ?>" alt="<?= htmlspecialchars($p['name']) ?>">
        <h3><?= htmlspecialchars($p['name']) ?></h3>
        <p>₹<?= number_format($p['price']) ?></p>

        <a href="customer/product_details.php?id=<?= $p['id'] ?>" class="btn-primary">Add to Cart</a>
      </div>

  <?php endwhile; ?>
  </div>
</section>

  <section class="how-it-works">
    <h2>How It Works</h2>
    <div class="steps">
      <div class="step" data-scroll>
        <i class="fas fa-search"></i>
        <h3>Browse</h3>
        <p>Explore our curated collection and find the perfect hamper for your occasion.</p>
      </div>
      <div class="step" data-scroll>
        <i class="fas fa-gift"></i>
        <h3>Customize</h3>
        <p>Personalize your hamper with messages and select your favorite items.</p>
      </div>
      <div class="step" data-scroll>
        <i class="fas fa-truck"></i>
        <h3>Deliver</h3>
        <p>We pack your hamper with care and deliver it safely to your loved ones.</p>
      </div>
    </div>
  </section>

  <section class="testimonials">
    <h2>What Our Customers Say</h2>
    <div class="testimonial-cards">
      <div class="testimonial-card" data-scroll>
        <i class="fas fa-quote-left"></i>
        <p>"Mad Smile made my sister's birthday unforgettable! The hamper was beautiful and delivered on time."</p>
        <span>- Priya S.</span>
      </div>
      <div class="testimonial-card" data-scroll>
        <i class="fas fa-quote-left"></i>
        <p>"Loved the eco-friendly packaging and the thoughtful selection. Highly recommend!"</p>
        <span>- Rahul M.</span>
      </div>
      <div class="testimonial-card" data-scroll>
        <i class="fas fa-quote-left"></i>
        <p>"Excellent service and amazing hampers. Will order again for sure!"</p>
        <span>- Sneha P.</span>
      </div>
    </div>
  </section>

  <section class="feedback-unique">
    <h2>Share Your Smile!</h2>
    <p>Tell us how our hampers made your day brighter. Your feedback helps us spread more joy!</p>
    <form id="feedbackForm">
      <input type="text" name="name" placeholder="Your Name" required />
      <textarea name="message" rows="3" placeholder="Your Experience..." required></textarea>
      <div class="rating">
        <span>Rate your smile:</span>
        <label><input type="radio" name="smile" value="1" />😊</label>
        <label><input type="radio" name="smile" value="2" />😁</label>
        <label><input type="radio" name="smile" value="3" />😍</label>
      </div>
      <button type="submit" class="btn-primary">Send Feedback</button>
    </form>
    <div id="feedbackDisplay"></div>
  </section>

  <footer class="footer">
    <p>&copy; <?= date('Y') ?> Mad Smile – Because every smile deserves a gift.</p>
    <div class="social-icons">
      <a href="mailto:madsmileee@gmail.com" target="_blank"><i class="fas fa-envelope"></i></a>
      <a href="https://github.com/Kesha0328" target="_blank"><i class="fab fa-github"></i></a>
      <a href="https://www.instagram.com/mad_smileee" target="_blank"><i class="fab fa-instagram"></i></a>
    </div>
  </footer>

  <script>
  let slides = document.querySelectorAll('.hero-slide');
  let index = 0;
  setInterval(()=>{
    slides[index].classList.remove('active');
    index = (index + 1) % slides.length;
    slides[index].classList.add('active');
  },5000);

  const observer = new IntersectionObserver(entries=>{
    entries.forEach(e=>{
      if(e.isIntersecting){ e.target.classList.add('is-visible'); }
    });
  });
  document.querySelectorAll('[data-scroll]').forEach(el=>observer.observe(el));

  const form=document.getElementById('feedbackForm');
form.addEventListener('submit',e=>{
  e.preventDefault();
  const formData = new FormData(form);

  fetch("customer/save_feedback.php", {
      method: "POST",
      body: formData
  })
  .then(res => res.text())
  .then(data => {
      if (data === "success") {
          document.getElementById('feedbackDisplay').innerHTML =
              `<p style="color:green; font-weight:bold;">✅ Thank you! Your feedback is saved.</p>`;
          form.reset();
      } else {
          document.getElementById('feedbackDisplay').innerHTML =
              `<p style="color:red;">❌ Something went wrong. Please try again.</p>`;
      }
  });
});

  </script>
</body>
</html>
