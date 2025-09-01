<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign Up | Mad Smile</title>
  <link rel="stylesheet" href="signup.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body>
  <div class="main-signup-wrapper">
    <section class="signup-container">
      <h2>Create Your Account</h2>
      <form class="signup-form">
        <div class="input-group">
          <input type="text" placeholder="Full Name" required />
          <span class="icon"><i class="fas fa-user"></i></span>
        </div>
        <div class="input-group">
          <input type="email" placeholder="Email Address" required />
          <span class="icon"><i class="fas fa-envelope"></i></span>
        </div>
        <div class="input-group">
          <input type="password" placeholder="Password" required />
          <span class="icon"><i class="fas fa-lock"></i></span>
        </div>
        <div class="input-group">
          <input type="password" placeholder="Confirm Password" required />
          <span class="icon"><i class="fas fa-lock"></i></span>
        </div>
        <button type="submit">Sign Up</button>
      </form>
    </section>
  </div>
  <script src="app.js"></script>
</body>
</html>