<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mad Smile | Gift Hampers</title>
  <link rel="stylesheet" href="index.css"/>
  <script defer src="app.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body>
  <header class="navbar">
    <div class="logo"><img src="logo.png"></div>
    <nav class="nav-links">
      <a href="index.html" class="active">Home</a>
      <a href="collection.html">Collection</a>
      <a href="about.html">About</a>
      <a href="contact.html">Contact</a>
      <a href="login.html">Login</a>
      <a href="login.html">Login</a>
      <a href="signup.html">Sign Up</a>
    </nav>
  </header>

<section class="hero">
  <div class="hero-slider">
    <div class="hero-slide active">
      <img src="1.jpeg" alt="Classic Hamper">
      <div class="hero-slide-text">
        <h1>Crafted with Joy, Wrapped with Love</h1>
        <p>Discover the perfect gift hamper for every occasion</p>
        <a href="collection.html" class="btn-primary">Order Now</a>
      </div>
    </div>
    <div class="hero-slide">
      <img src="2.jpeg" alt="Birthday Bliss">
      <div class="hero-slide-text">
        <h1>Birthday Bliss Hampers</h1>
        <p>Make birthdays extra special with our curated hampers</p>
        <a href="collection.html" class="btn-primary">Order Now</a>
      </div>
    </div>
    <div class="hero-slide">
      <img src="3.jpeg" alt="Elegant Joy Crate">
      <div class="hero-slide-text">
        <h1>Elegant Joy Crate</h1>
        <p>Elegant Joy Crate Simply Elegant. Purely Joyful. Thoughtfully Curated for Moments That Matter.</p>
        <a href="collection.html" class="btn-primary">Order Now</a>
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
      <li>Handpicked premium products</li>
      <li>Customizable options for every occasion</li>
      <li>Eco-friendly packaging</li>
      <li>Fast & safe delivery</li>
    </ul>
  </section>

  <section class="featured">
    <h2>Best Sellers</h2>
    <div class="cards">
      <div class="card">
        <img src="10.jpeg" alt="Hamper 1"/>
        <h3>Classic Love Box</h3>
        <p>₹1,499</p>
        <a href="collection.html" class="btn-primary">Add to Cart</a>
      </div>
      <div class="card">
        <img src="5.jpeg" alt="Hamper 2"/>
        <h3>Birthday Bliss</h3>
        <p>₹1,799</p>
        <a href="collection.html" class="btn-primary">Add to Cart</a>
      </div>
      <div class="card">
        <img src="6.jpeg" alt="Hamper 3"/>
        <h3>Elegant Joy Crate</h3>
        <p>₹2,099</p>
        <a href="collection.html" class="btn-primary">Add to Cart</a>
      </div>
      <div class="card">
        <img src="7.jpeg" alt="Hamper 4"/>
        <h3>Festive Cheer Basket</h3>
        <p>₹1,999</p>
        <a href="collection.html" class="btn-primary">Add to Cart</a>
      </div>
      <div class="card">
        <img src="8.jpeg" alt="Hamper 5"/>
        <h3>Wellness Delight</h3>
        <p>₹1,599</p>
        <a href="collection.html" class="btn-primary">Add to Cart</a>
      </div>
      <div class="card">
        <img src="9.jpeg" alt="Hamper 6"/>
        <h3>Luxury Treat Box</h3>
        <p>₹2,499</p>
        <a href="collection.html" class="btn-primary">Add to Cart</a>
      </div>
    </div>
  </section>

  <section class="how-it-works">
    <h2>How It Works</h2>
    <div class="steps">
      <div class="step">
        <i class="fas fa-search"></i>
        <h3>Browse</h3>
        <p>Explore our curated collection and find the perfect hamper for your occasion.</p>
      </div>
      <div class="step">
        <i class="fas fa-gift"></i>
        <h3>Customize</h3>
        <p>Personalize your hamper with special messages and select your favorite items.</p>
      </div>
      <div class="step">
        <i class="fas fa-truck"></i>
        <h3>Deliver</h3>
        <p>We pack your hamper with care and deliver it safely to your loved ones.</p>
      </div>
    </div>
  </section>

  <section class="testimonials">
    <h2>What Our Customers Say</h2>
    <div class="testimonial-cards">
      <div class="testimonial-card">
        <i class="fas fa-quote-left"></i>
        <p>"Mad Smile made my sister's birthday unforgettable! The hamper was beautiful and delivered on time."</p>
        <span>- Priya S.</span>
      </div>
      <div class="testimonial-card">
        <i class="fas fa-quote-left"></i>
        <p>"Loved the eco-friendly packaging and the thoughtful selection. Highly recommend!"</p>
        <span>- Rahul M.</span>
      </div>
      <div class="testimonial-card">
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
    <p>&copy; 2025 Mad Smile – Because every smile deserves a gift.</p>
    <div class="social-icons">
  <a href="mailto:madsmileee@gmail.com" target="_blank" title="Email"><i class="fas fa-envelope"></i></a>
  <a href="https://github.com/Kesha0328" target="_blank" title="GitHub"><i class="fab fa-github"></i></a>
  <a href="https://www.instagram.com/mad_smileee" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
</div>
  </footer>
</body>
</html>