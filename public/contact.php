<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mad Smile - Contact</title>
  <link rel="stylesheet" href="assets/contact.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body>
  <header class="navbar">
    <div class="logo"><img src="images/logo.png"></div>
    <nav class="nav-links">
      <a href="index.php">Home</a>
      <a href="collection.php">Collection</a>
      <a href="about.php">About</a>
      <a href="contact.php" class="active">Contact</a>
      <a href="login.php">Login</a>
      <a href="signup.php">Sign Up</a>
    </nav>
  </header>

<div class="contact-main-container">
  <div class="contact-info-panel">
    <h2>Contact Us</h2>
    <p>We'd love to hear from you! Reach out for orders, feedback, or partnership.</p>
    <div class="contact-details">
      <strong><i class="fas fa-envelope"></i> Email:</strong> madsmileee@gmail.com<br>
      <strong><i class="fas fa-phone"></i> Phone:</strong> +91 98765 43210<br>
      <strong><i class="fas fa-map-marker-alt"></i> Address:</strong> 123 Smile Street, Ahmedabad, India
    </div>
    <div class="contact-hours">
      <i class="fas fa-clock"></i> Mon-Sat: 10am - 7pm<br>
      <i class="fas fa-clock"></i> Sun: Closed
    </div>
    <div class="contact-social">
      <a href="mailto:madsmileee@gmail.com" title="Email"><i class="fas fa-envelope"></i></a>
      <a href="https://github.com/Kesha0328" title="GitHub"><i class="fab fa-github"></i></a>
      <a href="https://www.instagram.com/mad_smileee" title="Instagram"><i class="fab fa-instagram"></i></a>
    </div>
  </div>
  <div class="form-container">
    <h3>Send Us a Message</h3>
    <div class="form-desc">Fill out the form and our team will get back to you soon.</div>
    <form class="contact-form">
      <input type="text" name="name" placeholder="Your Name" required>
      <input type="email" name="email" placeholder="Your Email" required>
      <textarea name="message" rows="4" placeholder="Your Message" required></textarea>
      <button type="submit">Send Message</button>
    </form>
  </div>
</div>
<div class="map-section">
  <iframe src="https://maps.google.com/maps?q=Ahmedabad&t=&z=13&ie=UTF8&iwloc=&output=embed"></iframe>
</div>
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