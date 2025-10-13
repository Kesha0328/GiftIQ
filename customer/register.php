<?php
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->query("SELECT id FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        $error = "Email already registered.";
    } else {
        $conn->query("INSERT INTO users (name,email,password) VALUES ('$name','$email','$pass')");
        $success = "Registration successful! <a href='login.php'>Login here</a>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="customerpanel.css">
</head>
<body>
    <div class="collection-header">
        <h1>📝 Register</h1>
    </div>
    <div class="customize-section">
        <?php if (!empty($error)) echo "<div class='message error'>$error</div>"; ?>
        <?php if (!empty($success)) echo "<div class='message success'>$success</div>"; ?>

        <form method="post">
            <p><label>Name</label><br><input type="text" class="filter-select" name="name" required></p>
            <p><label>Email</label><br><input type="email"class="filter-select" name="email" required></p>
            <p><label>Password</label><br><input type="password" class="filter-select" name="password" required></p>
            <button type="submit" class="btn-primary">Register</button>
        </form>

        <p style="margin-top:1rem;">Already have an account? <a href="login.php">Login here</a></p>

    </div>
</body>
</html>
