<?php
session_start();
include '../config.php';

// If the user hasn't requested a reset, send them back.
if (!isset($_SESSION['reset_user'])) {
    header("Location: forgot_password.php");
    exit;
}

$user_id = $_SESSION['reset_user'];
$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $otp = $_POST['otp'];
    $newpass = $_POST['password'];

    // --- SECURE QUERY: Use Prepared Statements ---
    // Added otp_expiry check
    $stmt = $conn->prepare("SELECT otp_code, otp_expiry FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $now = date("Y-m-d H:i:s");

        // --- Improved OTP Logic ---
        if ($row['otp_expiry'] < $now) {
            $error = "❌ OTP has expired. Please request a new one.";
        } elseif ($row['otp_code'] == $otp) {
            // OTP is correct and valid
            $hash = password_hash($newpass, PASSWORD_DEFAULT);

            // --- SECURE UPDATE: Use Prepared Statements ---
            // Clear OTP fields after successful reset
            $stmt = $conn->prepare("UPDATE users SET password = ?, otp_code = NULL, otp_expiry = NULL WHERE id = ?");
            $stmt->bind_param("si", $hash, $user_id);
            $stmt->execute();

            unset($_SESSION['reset_user'], $_SESSION['reset_otp']);
            $success = "✅ Password reset successful! <a href='login.php'>Login here</a>";
        } else {
            $error = "❌ Invalid OTP.";
        }
    } else {
        $error = "An error occurred. Please try again.";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        /* =========================================
           1. ROOT VARIABLES & GLOBAL STYLES
           (Consistent with login/forgot)
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
           (Consistent with login/forgot)
           ========================================= */
        .form-container {
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
           (Consistent with forgot)
           ========================================= */
        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
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

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.9rem;
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
            color: var(--accent-text);
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
           (Consistent with login/forgot)
           ========================================= */
        .alert {
            padding: 0.8rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
        }
        .alert.error {
            background: #f8d7da;
            color: #721c24;
        }
        .alert.success {
            background: #d4edda;
            color: #155724;
        }
        
        /* Style for the <a href> in the success message */
        .alert.success a {
            color: #0d4a13; /* Darker green for link */
            font-weight: 700;
            text-decoration: underline;
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
        
        <h1><i class="fa fa-sync-alt"></i> Reset Password</h1>

        <?php if (!empty($error)) echo "<div class='alert error'>$error</div>"; ?>
        <?php if (!empty($success)) echo "<div class='alert success'>$success</div>"; ?>

        <?php if (empty($success)) { /* Only show form if not successful */ ?>
            <form method="post">
                <div class="input-group">
                    <label for="otp">OTP Code</label>
                    <input type="text" id="otp" name="otp" required autocomplete="one-time-code">
                </div>
                
                <div class="input-group">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" required autocomplete="new-password">
                </div>
                
                <button type="submit" class="btn-primary">Set New Password</button>
            </form>
            
            <a href="login.php" class="back-link"><i class="fa fa-arrow-left"></i> Back to Login</a>
        
        <?php } ?>

    </div>

</body>
</html>