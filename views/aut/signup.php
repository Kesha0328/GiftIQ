<?php
// include signup controller
include __DIR__ . "/../../src/controllers/authcontrollers.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up | Mad Smile</title>
  <!-- Link your CSS -->
  <link rel="stylesheet" href="../../public/assets/signup.css">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body>

  <div class="main-signup-wrapper">
    <div class="signup-container">
      <h2>Create Your Account</h2>
      <form action="../../src/controllers/authcontrollers.php" method="POST" class="signup-form">

        <div class="input-group">
          <input type="text" name="fullname" placeholder="Full Name" required>
          <span class="icon"><i class="fas fa-user"></i></span>
        </div>

        <div class="input-group">
          <input type="email" name="email" placeholder="Email Address" required>
          <span class="icon"><i class="fas fa-envelope"></i></span>
        </div>

        <div class="input-group">
          <input type="password" name="password" placeholder="Password" required>
          <span class="icon"><i class="fas fa-lock"></i></span>
        </div>

        <div class="input-group">
          <input type="password" name="confirm_password" placeholder="Confirm Password" required>
          <span class="icon"><i class="fas fa-lock"></i></span>
        </div>

        <button type="submit" name="signup">Sign Up</button>
      </form>
    </div>
  </div>

</body>
</html>