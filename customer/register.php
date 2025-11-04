<?php
session_start();
include '../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../admin/mail_template.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->query("SELECT id FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        $error = "Email already registered.";
    } else {
        $conn->query("INSERT INTO users (name,email,password) VALUES ('$name','$email','$pass')");
        $success = "Registration successful! <a href='login.php'>Login here</a>";

        $subject = "🎉 Welcome to GiftIQ!";
        $content = "
            <p>Hi <strong>$name</strong>,</p>
            <p>Thank you for joining <strong>GiftIQ</strong>! 🎁</p>
            <p>Your account has been created successfully. You can now log in and start exploring our curated gift collection.</p>
            <p>Click below to log in:</p>
            <p><a href='" . (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/login.php' 
                style='display:inline-block;padding:10px 18px;background:#e9b89a;color:#111;text-decoration:none;border-radius:8px;font-weight:600;'>Login Now</a></p>
        ";
        $html = giftIQMailTemplate($subject, $content);

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
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $html;

            $mail->send();
        } catch (Exception $e) {
            $error = "Registration successful, but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - GiftIQ</title>
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

    .register-container {
      background: #fff;
      padding: 2rem;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.08);
      width: 90%;
      max-width: 420px;
      text-align: center;
      animation: fadeInUp 0.5s ease-in-out;
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h1 {
      background: linear-gradient(90deg, #f4b8b4, #ffd9a0);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-size: 1.8rem;
      margin-bottom: 1.2rem;
    }

    input {
      width: 100%;
      padding: 0.8rem;
      margin: 0.6rem 0;
      border: 2px solid #f3dede;
      border-radius: 10px;
      font-size: 1rem;
      outline: none;
      transition: 0.2s;
    }

    input:focus {
      border-color: #f7b47d;
      box-shadow: 0 0 0 3px rgba(247,180,125,0.2);
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
      margin-top: 0.5rem;
      transition: all 0.3s;
    }

    .btn-primary:hover {
      transform: translateY(-3px);
    }

    .error, .success {
      padding: 0.8rem;
      border-radius: 8px;
      margin-bottom: 1rem;
      font-size: 0.95rem;
    }

    .error {
      background: #ffe6e6;
      color: #b30000;
    }

    .success {
      background: #eaffea;
      color: #155724;
    }

    .success a {
      color: #c87941;
      font-weight: 600;
      text-decoration: none;
    }

    a {
      color: #d47474;
      text-decoration: none;
      font-size: 0.9rem;
    }

    a:hover { text-decoration: underline; }

    p {
      font-size: 0.9rem;
      margin-top: 0.8rem;
    }

    @media (max-width: 480px) {
      .register-container {
        padding: 1.5rem;
      }
      h1 {
        font-size: 1.5rem;
      }
      input {
        font-size: 0.95rem;
      }
      .btn-primary {
        font-size: 0.95rem;
      }
    }
  </style>
</head>
<body>
  <div class="register-container">
    <h1>📝 Register</h1>

    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
    <?php if (!empty($success)) echo "<div class='success'>$success</div>"; ?>

    <form method="post">
      <input type="text" name="name" placeholder="Enter Full Name" required>
      <input type="email" name="email" placeholder="Enter Email" required>
      <input type="password" name="password" placeholder="Create Password" required>
      <button type="submit" class="btn-primary">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
  </div>
</body>
</html>
