<?php
session_start();
include '../config.php';

// --- PHPMailer Imports ---
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../admin/mail_template.php'; // Your custom email template

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $pass  = trim($_POST['password']);

    // --- SECURE LOGIN: Use Prepared Statements to prevent SQL Injection ---
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 1) {
        $user = $res->fetch_assoc();

        // Verify password
        if (password_verify($pass, $user['password'])) {

            // Generate OTP
            $otp = rand(100000, 999999);
            $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

            // --- SECURE UPDATE: Use Prepared Statements ---
            $stmt = $conn->prepare("UPDATE users SET otp_code = ?, otp_expiry = ? WHERE id = ?");
            $stmt->bind_param("ssi", $otp, $expiry, $user['id']);
            $stmt->execute();

            // --- Send OTP Email ---
            $mail = new PHPMailer(true);
            try {
                // Note: For production, store credentials securely (e.g., environment variables)
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'denishsaliya@gmail.com'; // Your email
                $mail->Password   = 'byzr lpev fsbb fvvs'; // Your App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('denishsaliya@gmail.com', 'GiftIQ');
                $mail->addAddress($user['email']);
                $mail->isHTML(true);
                $mail->Subject = 'Your GiftIQ OTP Code';

                $content = "
                    <p>Hello <strong>{$user['name']}</strong>,</p>
                    <p>Your login OTP is:</p>
                    <h2 style='text-align:center;color:#d47474;font-size:28px;letter-spacing: 2px;'>$otp</h2>
                    <p>This OTP will expire in <strong>10 minutes</strong>.</p>
                ";

                $mail->Body = giftIQMailTemplate("Your GiftIQ Login OTP", $content);
                $mail->send();

                // Set session to verify OTP on the next page
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        /* =========================================
           1. ROOT VARIABLES & GLOBAL STYLES
           ========================================= */
        :root {
            --accent-pink: #f7d4d1;
            --accent-gold: #ffe6b3;
            --accent-text: #d47474;
            --accent-border: #f3dede;
            --white: #fff;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
            --bg-gradient: linear-gradient(135deg, #fff8f6, #ffeecb);
            --heading-gradient: linear-gradient(90deg, #f4b8b4, #ffd9a0);
            --button-gradient: linear-gradient(135deg, #ffe6b3, #f7d4d1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }

        /* =========================================
           2. LOGIN CONTAINER & LOGO
           ========================================= */
        .login-container {
            background: var(--white);
            padding: 2.5rem 2rem;
            border-radius: 16px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 420px;
            text-align: center;
            border: 1px solid var(--white);
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.98); }
            to { opacity: 1; transform: scale(1); }
        }

        .logo-container {
            width: 70px;
            height: 70px;
            margin: 0 auto 1rem;
            background: #fffaf8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--accent-border);
        }

        .logo-container img {
            width: 40px;
            height: 40px;
        }

        h1 {
            background: var(--heading-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
        }
        
        h1 i {
            margin-right: 0.5rem;
        }

        /* =========================================
           3. FORM STYLES
           ========================================= */
        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .input-group {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #ccc;
            transition: color 0.2s ease-in-out;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.9rem 0.9rem 0.9rem 2.5rem; /* Left padding for icon */
            border: 1px solid var(--accent-border);
            border-radius: 10px;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            outline: none;
            background: #fffdfb;
            transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        /* Minimal Focus Effect */
        input:focus {
            border-color: var(--accent-pink);
            box-shadow: 0 0 0 4px rgba(247, 212, 209, 0.4);
        }
        
        input:focus + .input-icon {
            color: var(--accent-text);
        }
        
        .btn-primary {
            width: 100%;
            background: var(--button-gradient);
            color: var(--accent-text); /* Better contrast than white */
            font-weight: 700;
            font-size: 1rem;
            border: none;
            border-radius: 10px;
            padding: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
        }
        .btn-primary:hover {
              box-shadow: 0 14px 30px rgba(0, 0, 0, 0.08);
        }

        /* =========================================
           4. ALERTS & LINKS
           ========================================= */
        .error {
            background: #ffe6e6;
            color: #b30000;
            padding: 0.8rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .links {
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }

        .links a {
            color: var(--accent-text);
            text-decoration: none;
            font-weight: 500;
            margin: 0 0.5rem;
        }
        .links a:hover {
            text-decoration: underline;
        }

        /* =========================================
           5. RESPONSIVE DESIGN
           ========================================= */
        @media (max-width: 480px) {
            .login-container {
                padding: 2rem 1.5rem;
            }
            h1 {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        
        <div class="logo-container">
            <img src="../uploads/favicon.png" alt="GiftIQ Logo">
        </div>

        <h1><i class="fa fa-lock"></i> Login</h1>
        
        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
        
        <form method="post">
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required>
                <i class="fa fa-envelope input-icon"></i>
            </div>
            
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
                <i class="fa fa-key input-icon"></i>
            </div>
            
            <button type="submit" class="btn-primary">Send OTP</button>
        </form>
        
        <div class="links">
            <a href="register.php">Create Account</a> | <a href="forgot_password.php">Forgot Password?</a>
        </div>

    </div>

</body>
</html>