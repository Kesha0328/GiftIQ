<?php
session_start();
include '../config.php';

if (!isset($_SESSION['reset_user'])) {
    header("Location: forgot_password.php");
    exit;
}

$user_id = $_SESSION['reset_user'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $otp = $_POST['otp'];
    $newpass = $_POST['password'];

    $res = $conn->query("SELECT otp_code FROM users WHERE id=$user_id");
    $row = $res->fetch_assoc();

    if ($row['otp_code'] == $otp) {
        $hash = password_hash($newpass, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password='$hash', otp_code=NULL WHERE id=$user_id");

        unset($_SESSION['reset_user'], $_SESSION['reset_otp']);
        $success = "Password reset successful! <a href='login.php'>Login here</a>";
    } else {
        $error = "Invalid OTP.";
    }
}
    $success = "Password reset successful! <a href='login.php'>Login here</a>";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="customerpanel.css">
</head>
<body>
    <div class="collection-header">
        <h1>🔄 Reset Password</h1>
    </div>
    <div class="customize-section">
        <?php if (!empty($error)) echo "<div class='message error'>$error</div>"; ?>
        <?php if (!empty($success)) echo "<div class='message success'>$success</div>"; ?>

        <?php if (empty($success)) { ?>
            <p class="message success">Demo OTP: <strong><?= $_SESSION['reset_otp']; ?></strong></p>
            <form method="post">
                <p><label>OTP</label><br><input type="text" name="otp" required></p>
                <p><label>New Password</label><br><input type="password" name="password" required></p>
                <button type="submit" class="btn-primary">Reset Password</button>
            </form>
        <?php } ?>
    </div>
</body>
</html>
