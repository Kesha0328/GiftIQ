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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password - GiftIQ</title>
  <link rel="icon" type="image/png" href="../uploads/favicon.png" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    :root {
      --accent-pink: #f7d4d1;
      --accent-gold: #ffe6b3;
      --accent-text: #d47474;
      --white: #fff;
      --shadow: 0 6px 20px rgba(0,0,0,0.06);
    }

    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background: linear-gradient(135deg, #fff8f6, #ffeecb);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: #333;
    }

    .collection-header {
      text-align: center;
      margin-bottom: 1rem;
    }

    .collection-header h1 {
      background: linear-gradient(90deg, #f4b8b4, #ffd9a0);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-size: 2rem;
      font-weight: 700;
    }

    .customize-section {
      background: var(--white);
      width: 90%;
      max-width: 420px;
      padding: 2rem;
      border-radius: 18px;
      box-shadow: var(--shadow);
      text-align: center;
    }

    label {
      display: block;
      font-weight: 600;
      color: var(--accent-text);
      text-align: left;
      margin-bottom: 5px;
    }

    input[type="text"], input[type="password"] {
      width: 100%;
      padding: 10px 12px;
      border: 2px solid #f3dede;
      border-radius: 10px;
      background: #fffdfb;
      margin-bottom: 1rem;
      font-size: 1rem;
      outline: none;
      transition: 0.2s;
    }

    input:focus {
      border-color: var(--accent-pink);
      box-shadow: 0 0 6px rgba(247,212,209,0.4);
    }

    .btn-primary {
      width: 100%;
      background: linear-gradient(135deg, var(--accent-gold), var(--accent-pink));
      border: none;
      color: #fff;
      padding: 12px;
      border-radius: 12px;
      font-weight: 700;
      cursor: pointer;
      font-size: 1rem;
      transition: all 0.25s ease;
    }

    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(212,116,116,0.15);
    }

    .message {
      padding: 0.8rem;
      border-radius: 10px;
      margin-bottom: 1rem;
      font-weight: 500;
    }

    .message.error {
      background: #f8d7da;
      color: #721c24;
    }

    .message.success {
      background: #d4edda;
      color: #155724;
    }

    .customize-section a {
      color: var(--accent-text);
      text-decoration: none;
      font-weight: 600;
    }

    .customize-section a:hover {
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .customize-section {
        width: 92%;
        padding: 1.5rem;
      }

      .collection-header h1 {
        font-size: 1.6rem;
      }

      input[type="text"], input[type="password"] {
        font-size: 0.95rem;
      }

      .btn-primary {
        padding: 10px;
        font-size: 0.95rem;
      }
    }
  </style>
</head>
<body>
  <div class="collection-header">
    <h1>🔄 Reset Password</h1>
  </div>

  <div class="customize-section fadeInUp">
    <?php if (!empty($error)) echo "<div class='message error'>$error</div>"; ?>
    <?php if (!empty($success)) echo "<div class='message success'>$success</div>"; ?>

    <?php if (empty($success)) { ?>
      <form method="post">
        <label>OTP</label>
        <input type="text" name="otp" required>
        
        <label>New Password</label>
        <input type="password" name="password" required>
        
        <button type="submit" class="btn-primary">Reset Password</button>
      </form>
    <?php } ?>
  </div>
</body>
</html>
