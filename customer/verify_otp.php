<?php
session_start();
include '../config.php';

if (!isset($_SESSION['pending_user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['pending_user'];
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $otp = trim($_POST['otp']);
    $res = $conn->query("SELECT otp_code, otp_expiry FROM users WHERE id=$user_id");
    $row = $res->fetch_assoc();

    if ($row) {
        if ($row['otp_code'] == $otp && strtotime($row['otp_expiry']) > time()) {
            $_SESSION['user_id'] = $user_id;
            $conn->query("UPDATE users SET otp_code=NULL, otp_expiry=NULL WHERE id=$user_id");
            unset($_SESSION['pending_user'], $_SESSION['demo_otp']);
            header("Location: ../index.php");
            exit;
        } else {
            $error = "❌ Invalid or expired OTP. Please try again.";
        }
    } else {
        $error = "⚠️ Something went wrong, please log in again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Verify OTP - GiftIQ</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="../uploads/favicon.png" />
  <style>
    :root {
      --accent-pink: #f7d4d1;
      --accent-gold: #ffe6b3;
      --accent-text: #d47474;
      --white: #fff;
      --shadow: 0 6px 24px rgba(0,0,0,0.08);
    }

    body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #fff8f6, #ffeecb);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .otp-container {
      background: var(--white);
      box-shadow: var(--shadow);
      border-radius: 16px;
      padding: 2rem;
      max-width: 400px;
      width: 90%;
      text-align: center;
      animation: fadeIn 0.5s ease;
    }

    h1 {
      color: var(--accent-text);
      font-size: 1.8rem;
      margin-bottom: 0.5rem;
    }

    p {
      color: #666;
      font-size: 0.95rem;
      margin-bottom: 1.2rem;
    }

    input[type="text"] {
      width: 100%;
      padding: 0.8rem;
      font-size: 1.1rem;
      border-radius: 10px;
      border: 2px solid #f3dede;
      background: #fffdfb;
      text-align: center;
      letter-spacing: 4px;
      font-weight: bold;
      outline: none;
      margin-bottom: 1rem;
      transition: border-color 0.3s;
    }

    input[type="text"]:focus {
      border-color: var(--accent-pink);
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--accent-gold), var(--accent-pink));
      color: #fff;
      border: none;
      padding: 0.9rem;
      width: 100%;
      font-weight: 700;
      font-size: 1rem;
      border-radius: 12px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 18px rgba(232,124,124,0.25);
    }

    .error-box {
      background: #ffe6e6;
      color: #b30000;
      padding: 0.7rem;
      border-radius: 10px;
      margin-bottom: 1rem;
      font-size: 0.9rem;
    }

    .success-box {
      background: #eafbea;
      color: #155724;
      padding: 0.7rem;
      border-radius: 10px;
      margin-bottom: 1rem;
      font-size: 0.9rem;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 500px) {
      .otp-container {
        padding: 1.5rem;
        border-radius: 12px;
      }
      h1 { font-size: 1.5rem; }
      input[type="text"] { font-size: 1rem; }
    }
  </style>
</head>
<body>
  <div class="otp-container">
    <h1>📩 Verify OTP</h1>
    <p>Enter the 6-digit code sent to your registered email.</p>

    <?php if (!empty($error)): ?>
      <div class="error-box"><?= $error ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['demo_otp'])): ?>
      <div class="success-box">Demo OTP (for testing): <strong><?= $_SESSION['demo_otp'] ?></strong></div>
    <?php endif; ?>

    <form method="post">
      <input type="text" name="otp" maxlength="6" placeholder="Enter OTP" required>
      <button type="submit" class="btn-primary">Verify OTP</button>
    </form>
  </div>
</body>
</html>
