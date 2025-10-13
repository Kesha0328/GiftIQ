<?php
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $res = $conn->query("SELECT id FROM users WHERE email='$email'");

    if ($res->num_rows == 1) {
        $user = $res->fetch_assoc();
        $otp = rand(100000, 999999);
        $conn->query("UPDATE users SET otp_code='$otp' WHERE id={$user['id']}");

        $_SESSION['reset_user'] = $user['id'];
        $_SESSION['reset_otp'] = $otp; // Demo: show OTP directly
        header("Location: reset_password.php");
        exit;
    } else {
        $error = "No account found with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="customerpanel.css">
</head>
<body>
    <div class="collection-header">
        <h1>🔑 Forgot Password</h1>
    </div>
    <div class="customize-section">
        <?php if (!empty($error)) echo "<div class='message error'>$error</div>"; ?>
        <form method="post">
            <p><label>Email</label><br><input type="email" name="email" required></p>
            <button type="submit" class="btn-primary">Send OTP</button>
        </form>

        <p style="margin-top:1rem;">
            Remembered your password? <a href="login.php">Login here</a>
        </p>

    </div>
</body>
</html>
