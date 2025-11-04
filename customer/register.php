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
    // --- Get All Form Data ---
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $pass    = $_POST['password']; // Will be hashed
    $phone   = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $city    = trim($_POST['city']);
    $postal  = trim($_POST['postal_code']);
    $country = trim($_POST['country']);

    // --- SECURE CHECK: Use Prepared Statements ---
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $check = $stmt->get_result();

    if ($check->num_rows > 0) {
        $error = "Email already registered. Please <a href='login.php'>login here</a>.";
    } else {
        // --- Hash Password ---
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
        
        // --- SECURE INSERT: Use Prepared Statements (without avatar first) ---
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, address, city, postal_code, country) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $name, $email, $hashed_pass, $phone, $address, $city, $postal, $country);
        
        if ($stmt->execute()) {
            $user_id = $conn->insert_id; // Get the ID of the new user
            $avatar_filename = NULL;

            // --- Handle Avatar Upload ---
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','webp', 'gif'];
                
                if (in_array($ext, $allowed)) {
                    $avatar_filename = "user_" . $user_id . "_" . time() . "." . $ext;
                    $path = "../uploads/profile/" . $avatar_filename;
                    
                    if (!is_dir("../uploads/profile/")) {
                        mkdir("../uploads/profile/", 0777, true);
                    }
                    
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $path)) {
                        // Avatar moved, now update the user's record
                        $conn->query("UPDATE users SET avatar = '$avatar_filename' WHERE id = $user_id");
                    } else {
                        $error = "Registration successful, but profile photo failed to upload.";
                    }
                } else {
                     $error = "Registration successful, but profile photo was an invalid format (jpg, jpeg, png, webp, gif only).";
                }
            }
            
            // Set success message (even if avatar failed, registration worked)
            if (empty($error)) {
                 $success = "Registration successful! <a href='login.php'>Login here</a>";
            }

            // --- Send Welcome Email ---
            $subject = "🎉 Welcome to GiftIQ!";
            $content = "
                <p>Hi <strong>" . htmlspecialchars($name) . "</strong>,</p>
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
                $mail->Username   = 'denishsaliya@gmail.com'; // Your email
                $mail->Password   = 'byzr lpev fsbb fvvs'; // Your App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('denishsaliya@gmail.com', 'GiftIQ');
                $mail->addAddress($email, $name);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $html;
                $mail->send();
            } catch (Exception $e) {
                // Do not override the main $error if registration failed
                if (!empty($success)) {
                    $error = "Registration successful, but welcome email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            }

        } else {
            $error = "Registration failed. Please try again. " . $conn->error;
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
            padding: 2rem 1rem; /* Padding for scroll */
        }

        /* =========================================
           2. FORM CONTAINER & LOGO
           (Wider for 2-column layout)
           ========================================= */
        .form-container {
            background: var(--white);
            padding: 2.5rem 2rem;
            border-radius: 16px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 800px; /* Wider for 2-col grid */
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
           3. FORM STYLES (2-Column Grid)
           ========================================= */
        form {
            text-align: left;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr; /* 2 columns */
            gap: 1rem;
        }
        
        .form-group {
            position: relative;
            display: flex;
            flex-direction: column;
        }

        /* Class to make an item span both columns */
        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            font-weight: 600;
            color: var(--accent-text);
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 43px; /* Adjusted for label */
            color: #ccc;
            transition: color 0.2s ease-in-out;
        }

        input[type="text"],
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
        
        /* Adjust padding for inputs without icons */
        input.no-icon {
             padding-left: 0.9rem;
        }

        /* Minimal Focus Effect */
        input:focus {
            border-color: var(--accent-pink);
            box-shadow: 0 0 0 4px rgba(247, 212, 209, 0.4);
        }
        input:focus + .input-icon {
            color: var(--accent-text);
        }
        
        /* --- Custom File Upload Button --- */
        input[type="file"] {
            display: none; /* Hide the default input */
        }
        
        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 0.9rem;
            border: 2px dashed var(--accent-border);
            border-radius: 10px;
            cursor: pointer;
            color: var(--text-light);
            transition: all 0.2s ease-in-out;
        }
        .file-upload-label:hover {
            border-color: var(--accent-pink);
            color: var(--accent-text);
            background: #fffaf8;
        }
        .file-upload-label i {
            font-size: 1.2rem;
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
            margin-top: 0.5rem;
        }
        .btn-primary:hover {
              box-shadow: 0 14px 30px rgba(0, 0, 0, 0.08);
        }

        /* =========================================
           4. ALERTS & LINKS
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
        .alert.error a {
            color: #721c24;
            font-weight: 700;
        }
        .alert.success {
            background: #d4edda;
            color: #155724;
        }
        .alert.success a {
            color: #0d4a13;
            font-weight: 700;
        }

        .links {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }
        .links a {
            color: var(--accent-text);
            text-decoration: none;
            font-weight: 500;
        }
        .links a:hover {
            text-decoration: underline;
        }

        /* =========================================
           5. RESPONSIVE DESIGN
           ========================================= */
        @media (max-width: 700px) {
            .form-container {
                padding: 2rem 1.5rem;
            }
            /* Stack grid to 1 column */
            .form-grid {
                grid-template-columns: 1fr;
            }
            /* Reset column span */
            .form-group.full-width {
                grid-column: 1 / 1;
            }
        }
        @media (max-width: 480px) {
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

        <h1><i class="fa fa-user-plus"></i> Create Account</h1>
        
        <?php if (!empty($error)) echo "<div class='alert error'>$error</div>"; ?>
        <?php if (!empty($success)) echo "<div class='alert success'>$success</div>"; ?>
        
        <form method="post" enctype="multipart/form-data">
            <div class="form-grid">

                <div class="form-group full-width">
                    <label for="name">Full Name</label>
                    <div class="input-group">
                        <input type="text" id="name" name="name" placeholder="Enter Full Name" required>
                        <i class="fa fa-user input-icon"></i>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="email">Email</label>
                    <div class="input-group">
                        <input type="email" id="email" name="email" placeholder="Enter Email" required>
                        <i class="fa fa-envelope input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Create Password</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" placeholder="Create Password" required>
                        <i class="fa fa-key input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <div class="input-group">
                        <input type="text" id="phone" name="phone" placeholder="Phone Number" class="no-icon">
                    </div>
                </div>
                
                <div class="form-group full-width">
                    <label for="address">Address</label>
                    <div class="input-group">
                        <input type="text" id="address" name="address" placeholder="Street Address" class="no-icon">
                    </div>
                </div>

                <div class="form-group">
                    <label for="city">City</label>
                    <div class="input-group">
                        <input type="text" id="city" name="city" placeholder="City" class="no-icon">
                    </div>
                </div>

                <div class="form-group">
                    <label for="postal_code">Postal Code</label>
                    <div class="input-group">
                        <input type="text" id="postal_code" name="postal_code" placeholder="Postal Code" class="no-icon">
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="country">Country</label>
                    <div class="input-group">
                        <input type="text" id="country" name="country" placeholder="Country" class="no-icon">
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>Profile Picture (Optional)</label>
                    <label for="avatar" class="file-upload-label">
                        <i class="fa fa-upload"></i>
                        <span id="file-name-span">Choose a file...</span>
                    </label>
                    <input type="file" id="avatar" name="avatar" accept="image/*">
                </div>

                <div class="form-group full-width">
                    <button type="submit" class="btn-primary">Register</button>
                </div>

            </div> </form>
        
        <div class="links">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>

    </div>

    <script>
        // Script to show the selected file name in the custom upload button
        const avatarInput = document.getElementById('avatar');
        const fileNameSpan = document.getElementById('file-name-span');
        
        if (avatarInput) {
            avatarInput.addEventListener('change', e => {
                if (e.target.files && e.target.files.length > 0) {
                    fileNameSpan.textContent = e.target.files[0].name;
                } else {
                    fileNameSpan.textContent = 'Choose a file...';
                }
            });
        }
    </script>

</body>
</html>