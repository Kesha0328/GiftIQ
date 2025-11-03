<?php
session_start();
include '../config.php';
include '../admin/mail_template.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $res = $conn->query("SELECT id, name FROM users WHERE email='$email'");

    if ($res && $res->num_rows > 0) {
        $user = $res->fetch_assoc();
        $otp = rand(100000, 999999);
        $conn->query("UPDATE users SET otp_code='$otp' WHERE id={$user['id']}");
        $_SESSION['reset_user'] = $user['id'];
        $_SESSION['reset_otp'] = $otp;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'denishsaliya@gmail.com';
            $mail->Password   = 'byzr lpev fsbb fvvs';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('youremail@gmail.com', 'GiftIQ');
            $mail->addAddress($email, $user['name']);
            $mail->isHTML(true);
            $mail->Subject = 'GiftIQ - Password Reset OTP';

            $content = '
                <p>Hi <strong>' . htmlspecialchars($user['name']) . '</strong>,</p>
                <p>Your password reset OTP is:</p>
                <h2 style="color:#c87941;text-align:center;">' . $otp . '</h2>
                <p>Please use this OTP to reset your password. It is valid for 10 minutes.</p>
                <p>If you didn’t request this, please ignore this email.</p>
            ';

            $mail->Body = giftIQMailTemplate("Reset Your GiftIQ Password", $content);
            $mail->send();

            $success = "✅ OTP sent to your email. Please check your inbox.";
        } catch (Exception $e) {
            $error = "❌ Failed to send email. Please try again later.";
        }

        header("Refresh:2; url=reset_password.php");
    } else {
        $error = "No account found with this email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password - GiftIQ</title>
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
      align-items: center;
      justify-content: center;
      color: #333;
    }

    .forgot-container {
      background: var(--white);
      width: 90%;
      max-width: 420px;
      padding: 2rem;
      border-radius: 18px;
      box-shadow: var(--shadow);
      text-align: center;
      animation: fadeIn 0.5s ease;
    }

    h1 {
      background: linear-gradient(90deg, #f4b8b4, #ffd9a0);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-size: 2rem;
      margin-bottom: 1rem;
      font-weight: 700;
    }

    p {
      color: #666;
      font-size: 0.95rem;
      margin-bottom: 1.5rem;
    }

    label {
      display: block;
      font-weight: 600;
      color: var(--accent-text);
      text-align: left;
      margin-bottom: 5px;
    }

    input[type="email"] {
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

    a {
      display: inline-block;
      margin-top: 1rem;
      color: var(--accent-text);
      font-weight: 600;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .forgot-container {
        width: 92%;
        padding: 1.5rem;
      }

      h1 {
        font-size: 1.7rem;
      }

      .btn-primary {
        padding: 10px;
        font-size: 0.95rem;
      }
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

  <div class="forgot-container">
    <h1>🔐 Forgot Password</h1>
    <p>Enter your registered email below to receive an OTP for resetting your password.</p>

    <?php if (!empty($error)) echo "<div class='message error'>$error</div>"; ?>
    <?php if (!empty($success)) echo "<div class='message success'>$success</div>"; ?>

    <form method="post">
      <label>Email Address</label>
      <input type="email" name="email" required>
      <button type="submit" class="btn-primary">Send OTP</button>
    </form>

    <a href="login.php">⬅ Back to Login</a>
  </div>

</body>
</html>
