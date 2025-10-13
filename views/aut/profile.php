<?php
session_start();
if (!isset($_SESSION['fullname'])) {
    header("Location: views/aut/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Profile</title>
</head>
<body>
  <h1>Welcome, <?= $_SESSION['fullname']; ?> 🎉</h1>
  <p>Your email and more details can go here.</p>
  <a href="logout.php">Logout</a>
</body>
</html>
