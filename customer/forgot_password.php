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

$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    // --- SECURE QUERY: Use Prepared Statements ---
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows > 0) {
        $user = $res->fetch_assoc();
        $otp = rand(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes")); // Add expiry for reset

        // --- SECURE UPDATE: Use Prepared Statements ---
        $stmt = $conn->prepare("UPDATE users SET otp_code = ?, otp_expiry = ? WHERE id = ?");
        $stmt->bind_param("ssi", $otp, $expiry, $user['id']);
        $stmt->execute();
        
        // Store user ID in session for verification on the next page
        $_SESSION['reset_user'] = $user['id'];
        // Note: Storing reset_otp in session is less common than just checking the DB
        // but we will keep your logic.
        $_SESSION['reset_otp'] = $otp; 

        // --- Send Mail ---
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'denishsaliya@gmail.com'; // Your email
            $mail->Password   = 'byzr lpev fsbb fvvs'; // Your App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Use your real email for 'setFrom'
            $mail->setFrom('denishsaliya@gmail.com', 'GiftIQ'); 
            $mail->addAddress($email, $user['name']);
            $mail->isHTML(true);
            $mail->Subject = 'GiftIQ - Password Reset OTP';

            $content = '
                <p>Hi <strong>' . htmlspecialchars($user['name']) . '</strong>,</p>
                <p>Your password reset OTP is:</p>
                <h2 style="color:#d47474;text-align:center;letter-spacing:2px;">' . $otp . '</h2>
                <p>Please use this OTP to reset your password. It is valid for 10 minutes.</p>
                <p>If you didn’t request this, please ignore this email.</p>
            ';

            $mail->Body = giftIQMailTemplate("Reset Your GiftIQ Password", $content);
            $mail->send();

            $success = "✅ OTP sent to your email. Please check your inbox.";
            
            // Redirect to the reset page after 2 seconds
            header("Refresh:2; url=reset_password.php"); 
            
        } catch (Exception $e) {
            $error = "❌ Failed to send email. Please try again later.";
        }
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
            --text-light: #666;
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
           2. FORM CONTAINER & LOGO
           ========================================= */
        .form-container { /* Renamed from .forgot-container for consistency */
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
            margin-bottom: 0.75rem;
        }
        
        h1 i {
            margin-right: 0.5rem;
        }

        .form-description {
            color: var(--text-light);
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        /* =========================================
           3. FORM STYLES
           ========================================= */
        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            /* Hide form if success message is shown */
            <?php if (!empty($success)) echo "display: none;"; ?>
        }

        .input-group {
            text-align: left;
        }

        label {
            display: block;
            font-weight: 600;
            color: var(--accent-text);
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
        }

        input[type="email"] {
            width: 100%;
            padding: 0.9rem; /* Consistent padding */
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
        
        .btn-primary {
            width: 100%;
            background: var(--button-gradient);
            color: var(--accent-text); /* Better contrast */
            font-weight: 700;
            font-size: 1rem;
            border: none;
            border-radius: 10px;
            padding: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
              box-shadow: 0 14px 30px rgba(0, 0, 0, 0.08);
        }

        /* =========================================
           4. ALERTS & LINKS
           ========================================= */
        .alert { /* Renamed from .message */
            padding: 0.8rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .alert.error {
            background: #f8d7da;
            color: #721c24;
        }
        .alert.success {
            background: #d4edda;
            color: #155724;
        }

        .back-link {
            display: inline-block;
            margin-top: 1.5rem;
            color: var(--accent-text);
            font-weight: 500;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .back-link i {
            margin-right: 0.25rem;
        }
        .back-link:hover {
            text-decoration: underline;
        }

        /* =========================================
           5. RESPONSIVE DESIGN
           ========================================= */
        @media (max-width: 480px) {
            .form-container {
                padding: 2rem 1.5rem;
            }
            h1 {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>

    <div class="form-container">
        
        <div class="logo-container">
            <img src="../uploads/favicon.png" alt="GiftIQ Logo">
        </div>

        <h1><i class="fa fa-key"></i> Forgot Password</h1>
        
        <?php if (!empty($error)) echo "<div class='alert error'>$error</div>"; ?>
        <?php if (!empty($success)) echo "<div class='alert success'>$success</div>"; ?>
        
        <p class="form-description <?php if (!empty($success)) echo "hide"; ?>">
            Enter your email below to receive an OTP to reset your password.
        </p>

        <?php if (empty($success)): /* Only show form if success is not set */ ?>
            <form method="post">
                <div class="input-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit" class="btn-primary">Send OTP</button>
            </form>
        <?php endif; ?>

        <a href="login.php" class="back-link"><i class="fa fa-arrow-left"></i> Back to Login</a>
    </div>

</body>
</html>