<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mad Smile - About Us</title>
  <link rel="stylesheet" href="assets/about.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body>

<?php include "header.php"; ?>


<div class="about-section">
  <h1>About Mad Smile</h1>
  <p>
    Mad Smile is dedicated to spreading happiness through thoughtfully curated gift hampers. 
    Our mission is to make every occasion memorable with premium products, beautiful packaging, and heartfelt service.
  </p>
  <ul class="values-list">
    <li>Premium Quality</li>
    <li>Eco-Friendly Packaging</li>
    <li>Customizable Hampers</li>
    <li>Fast Delivery</li>
    <li>Customer Happiness</li>
  </ul>
</div>
<div class="team">
  <div class="team-member">
    <img src="team1.jpg" alt="Team Member 1">
    <h3>Kesha Gabani</h3>
    <div class="role">Founder & CEO</div>
    <div class="social">
      <a href="mailto:kesha@gmail.com"><i class="fas fa-envelope"></i></a>
      <a href="https://instagram.com/kesha0328"><i class="fab fa-instagram"></i></a>
    </div>
  </div>  <div class="team-member">
    <img src="team1.jpg" alt="Team Member 1">
    <h3>Drashti Patel</h3>
    <div class="role">Front-end Developer</div>
    <div class="social">
      <a href="mailto:kesha@email.com"><i class="fas fa-envelope"></i></a>
      <a href="https://instagram.com/kesha0328"><i class="fab fa-instagram"></i></a>
    </div>
  </div>
  <div class="team-member">
    <img src="team2.jpg" alt="Team Member 2">
    <h3>Aditi Bhatt</h3>
    <div class="role">Back-end Developer</div>
    <div class="social">
      <a href="mailto:priya@email.com"><i class="fas fa-envelope"></i></a>
      <a href="https://instagram.com/priya"><i class="fab fa-instagram"></i></a>
    </div>
  </div>
</div>

  <?php include 'footer.php'; ?>

</body>
</html>