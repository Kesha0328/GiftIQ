<?php
session_start();
include '../config.php';

// If the user hasn't started the login process, send them back.
if (!isset($_SESSION['pending_user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['pending_user'];
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $otp = trim($_POST['otp']);

    // --- SECURE QUERY: Use Prepared Statements ---
    $stmt = $conn->prepare("SELECT otp_code, otp_expiry FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $now_time = time();

        // Check if OTP is correct AND not expired
        if ($row['otp_code'] == $otp && strtotime($row['otp_expiry']) > $now_time) {
            
            // --- Success: Log the user in ---
            $_SESSION['user_id'] = $user_id;
            
            // --- SECURE UPDATE: Use Prepared Statements ---
            // Clear the OTP fields after successful login
            $stmt = $conn->prepare("UPDATE users SET otp_code = NULL, otp_expiry = NULL WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            // Clear all temporary session variables
            unset($_SESSION['pending_user']);
            if (isset($_SESSION['demo_otp'])) { // Also clear the demo_otp if it exists
                unset($_SESSION['demo_otp']);
            }
            
            header("Location: ../index.php"); // Redirect to main app
            exit;
            
        } elseif ($row['otp_code'] == $otp && strtotime($row['otp_expiry']) <= $now_time) {
            $error = "❌ Your OTP has expired. Please log in again to get a new one.";
        } else {
            $error = "❌ Invalid OTP. Please try again.";
        }
    } else {
        $error = "⚠️ Something went wrong. Please log in again.";
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        .form-container { /* Changed from .otp-container */
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
           3. FORM STYLES (OTP Specific)
           ========================================= */
        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        /* Special style for OTP input */
        .otp-input {
            width: 100%;
            padding: 0.9rem;
            border: 1px solid var(--accent-border);
            border-radius: 10px;
            font-size: 1.5rem; /* Larger font */
            font-family: 'Poppins', sans-serif;
            outline: none;
            background: #fffdfb;
            transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            
            /* OTP specific */
            text-align: center;
            letter-spacing: 0.5rem; /* Space out numbers */
            font-weight: 700;
        }

        /* Minimal Focus Effect */
        .otp-input:focus {
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
           ========================================= */
        .alert { /* Standardized class */
            padding: 0.8rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .alert.error { /* Renamed from .error-box */
            background: #f8d7da;
            color: #721c24;
        }
        .alert.success { /* Renamed from .success-box */
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
            .otp-input {
                font-size: 1.2rem;
                letter-spacing: 0.3rem;
            }
        }
    </style>
</head>
<body>

    <div class="form-container">
        
        <div class="logo-container">
            <img src="../uploads/favicon.png" alt="GiftIQ Logo">
        </div>

        <h1><i class="fa fa-shield-alt"></i> Verify OTP</h1>
        <p class="form-description">Enter the 6-digit code sent to your registered email.</p>

        <?php if (!empty($error)): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['demo_otp'])): // Your demo_otp display ?>
            <div class="alert success">Demo OTP (for testing): <strong><?= $_SESSION['demo_otp'] ?></strong></div>
        <?php endif; ?>

        <form method="post">
            <input type="text" name="otp" class="otp-input" maxlength="6" placeholder="------" required autocomplete="one-time-code">
            <button type="submit" class="btn-primary">Verify OTP</button>
        </form>
        
        <a href="login.php" class="back-link"><i class="fa fa-arrow-left"></i> Back to Login</a>
        
    </div>

</body>
</html>