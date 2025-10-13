<?php
session_start();
include '../config.php';

if (!isset($_SESSION['pending_user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['pending_user'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $otp = $_POST['otp'];
    $res = $conn->query("SELECT otp_code FROM users WHERE id=$user_id");
    $row = $res->fetch_assoc();

    if ($row['otp_code'] == $otp) {
        // OTP valid -> login success
        $_SESSION['user_id'] = $user_id;
        $conn->query("UPDATE users SET otp_code=NULL WHERE id=$user_id");
        unset($_SESSION['pending_user'], $_SESSION['demo_otp']);
        header("Location: ../index.php");
        exit;
    } else {
        $error = "Invalid OTP.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="customerpanel.css">
</head>
<body>
    <div class="collection-header">
        <h1>📲 Verify OTP</h1>
    </div>
    <div class="customize-section">
        <?php if (!empty($error)) echo "<div class='message error'>$error</div>"; ?>
        <p class="message success">Demo OTP: <strong><?= $_SESSION['demo_otp']; ?></strong></p>
        <form method="post">
            <p><label>Enter OTP</label><br><input type="text" class="filter-select" name="otp" required></p>
            <button type="submit" class="btn-primary">Verify</button>
        </form>
    </div>
</body>
</html>
