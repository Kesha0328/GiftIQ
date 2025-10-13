<?php
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $pass  = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $res = $conn->query($sql);

    if ($res->num_rows == 1) {
        $user = $res->fetch_assoc();
        if (password_verify($pass, $user['password'])) {
            // Generate demo OTP (fixed 123456 or random)
            $otp = rand(100000, 999999);
            $conn->query("UPDATE users SET otp_code='$otp' WHERE id={$user['id']}");

            // For demo: show OTP directly
            $_SESSION['pending_user'] = $user['id'];
            $_SESSION['demo_otp'] = $otp;
            header("Location: verify_otp.php");
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="customerpanel.css">
</head>
<body>
    <div class="collection-header">
        <h1>🔐 Login</h1>
    </div>
    <div class="customize-section">
        <?php if (!empty($error)) echo "<div class='message error'>$error</div>"; ?>
        <form method="post">
            <p><label>Email</label><br><input type="email" class="filter-select" name="email" required></p>
            <p><label>Password</label><br><input type="password" class="filter-select" name="password" required></p>
            <button type="submit" class="btn-primary">Login</button>
        </form>

        <p style="margin-top:1rem;">
            <a href="register.php">Create an account</a>
            <a href="forgot_password.php">Forgot Password?</a>
        </p>

    </div>
</body>
</html>
