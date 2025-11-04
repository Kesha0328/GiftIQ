<?php
session_start();
include '../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../admin/mail_template.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $pass  = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$email'";
    $res = $conn->query($sql);

    if ($res->num_rows == 1) {
        $user = $res->fetch_assoc();

        if (password_verify($pass, $user['password'])) {

          $otp = rand(100000, 999999);
            $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));


            $conn->query("UPDATE users SET otp_code='$otp', otp_expiry='$expiry' WHERE id={$user['id']}");

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'denishsaliya@gmail.com';
                $mail->Password   = 'byzr lpev fsbb fvvs';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('denishsaliya@gmail.com', 'GiftIQ');
                $mail->addAddress($user['email']);
                $mail->isHTML(true);
                $mail->Subject = 'Your GiftIQ OTP Code';

                $content = "
                    <p>Hello <strong>{$user['name']}</strong>,</p>
                    <p>Your login OTP is:</p>
                    <h2 style='text-align:center;color:#c87941;font-size:28px;'>$otp</h2>
                    <p>This OTP will expire in <strong>10 minutes</strong>.</p>
                ";

                $mail->Body = giftIQMailTemplate("Your GiftIQ Login OTP", $content);
                $mail->send();

                $_SESSION['pending_user'] = $user['id'];

                header("Location: verify_otp.php");
                exit;

            } catch (Exception $e) {
                $error = "⚠️ Failed to send OTP email: {$mail->ErrorInfo}";
            }

        } else {
            $error = "❌ Invalid password.";
        }
    } else {
        $error = "⚠️ No account found with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - GiftIQ</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="../uploads/favicon.png" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #fff8f6, #ffeecb);
      margin: 0;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-container {
      background: #fff;
      padding: 2rem;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.08);
      width: 90%;
      max-width: 400px;
      text-align: center;
    }
    h1 {
      background: linear-gradient(90deg, #f4b8b4, #ffd9a0);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-size: 1.8rem;
      margin-bottom: 1rem;
    }
    input {
      width: 100%;
      padding: 0.8rem;
      margin: 0.6rem 0;
      border: 2px solid #f3dede;
      border-radius: 10px;
      font-size: 1rem;
      outline: none;
    }
    input:focus {
      border-color: #f7b47d;
    }
    .btn-primary {
      width: 100%;
      background: linear-gradient(135deg, #ffe6b3, #f7d4d1);
      color: #fff;
      border: none;
      border-radius: 10px;
      padding: 0.9rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s;
    }
    .btn-primary:hover {
      transform: translateY(-3px);
    }
    .error {
      background: #ffe6e6;
      color: #b30000;
      padding: 0.7rem;
      border-radius: 8px;
      margin-bottom: 1rem;
    }
    a {
      color: #d47474;
      text-decoration: none;
      font-size: 0.9rem;
    }
    a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <div class="login-container">
    <h1>🔐 Login</h1>
    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="post">
      <input type="email" name="email" placeholder="Enter Email" required>
      <input type="password" name="password" placeholder="Enter Password" required>
      <button type="submit" class="btn-primary">Send OTP</button>
    </form>
    <p><a href="register.php">Create Account</a> | <a href="forgot_password.php">Forgot Password?</a></p>
  </div>
</body>
</html>
