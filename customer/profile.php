<?php
session_start();
include '../config.php';

// --- User Authentication ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);
$success = $error = "";

// --- Helper Functions ---
function fetchProfile($conn, $user_id) {
    $stmt = $conn->prepare("SELECT id, name, email, phone, address, city, postal_code, country, avatar FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->fetch_assoc();
}
// Helper to safely print profile data
function pf($arr, $key, $fallback = '') {
    return htmlspecialchars(isset($arr[$key]) ? $arr[$key] : $fallback);
}
$profile = fetchProfile($conn, $user_id);

// --- Form Handling: Avatar Upload ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_avatar') {
    if (!empty($_FILES['avatar']['name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (in_array($ext, $allowed)) {
            $filename = "user_" . $user_id . "_" . time() . "." . $ext; // Added time() to prevent cache issues
            $path = "../uploads/profile/" . $filename;
            if (!is_dir("../uploads/profile/")) mkdir("../uploads/profile/", 0777, true);
            
            // Delete old avatar if it exists
            if (!empty($profile['avatar']) && file_exists("../uploads/profile/".$profile['avatar'])) {
                unlink("../uploads/profile/".$profile['avatar']);
            }

            move_uploaded_file($_FILES['avatar']['tmp_name'], $path);
            $conn->query("UPDATE users SET avatar='$filename' WHERE id=$user_id");
            $success = "Profile photo updated successfully ✅";
            $profile['avatar'] = $filename; // Update for current page load
        } else $error = "Invalid image format (jpg, jpeg, png, gif, webp).";
    } else $error = "Please select an image file.";
}

// --- Form Handling: Profile Update ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    // Sanitize inputs
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $phone = $conn->real_escape_string(trim($_POST['phone']));
    $address = $conn->real_escape_string(trim($_POST['address']));
    $city = $conn->real_escape_string(trim($_POST['city']));
    $postal = $conn->real_escape_string(trim($_POST['postal_code']));
    $country = $conn->real_escape_string(trim($_POST['country']));
    
    // Update main profile
    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, address=?, city=?, postal_code=?, country=? WHERE id=?");
    $stmt->bind_param("sssssssi", $name, $email, $phone, $address, $city, $postal, $country, $user_id);
    $stmt->execute();

    // Password update logic
    if (!empty($_POST['password'])) {
        if ($_POST['password'] === $_POST['confirm_password']) {
            $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $conn->query("UPDATE users SET password='$hash' WHERE id=$user_id");
            $success = "Profile and password updated ✅";
        } else {
            $error = "Passwords do not match ❌";
        }
    } else {
        $success = "Profile updated successfully ✅";
    }
    
    $_SESSION['fullname'] = $name; // Update session name
    $profile = fetchProfile($conn, $user_id); // Re-fetch profile to show new data
}

// --- Data Fetching: Orders ---
$orders = $conn->query("SELECT id, total, status, created_at FROM orders WHERE user_id=$user_id ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile | GiftIQ</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="icon" type="image/png" href="../uploads/favicon.png" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
    /* =========================================
       1. ROOT VARIABLES & GLOBAL STYLES
       ========================================= */
    :root {
        --accent-pink: #f7d4d1;
        --accent-pink-light: #fff4f2;
        --accent-pink-border: #f3dede;
        --accent-gold: #ffe6b3;
        --accent-text: #d47474;
        --text-dark: #333;
        --text-light: #666;
        --white: #fff;
        --bg-main: linear-gradient(135deg, #fff8f6, #ffeecb);
        --bg-gradient: linear-gradient(90deg, #f4b8b4, #ffd9a0);
        --shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        --shadow-hover: 0 10px 30px rgba(0, 0, 0, 0.08);
        --radius: 16px;
    }
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    body {
        font-family: 'Poppins', sans-serif;
        background: var(--bg-main);
        color: var(--text-dark);
        -webkit-font-smoothing: antialiased;
    }

    /* =========================================
       2. LAYOUT & STRUCTURE
       ========================================= */
    .profile-title {
        text-align: center;
        font-size: 2.5rem; /* Adjusted for balance */
        font-weight: 700;
        background: var(--bg-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin: 2.5rem 0 1rem;
        letter-spacing: 0.5px;
    }
    .profile-title i {
        margin-right: 10px;
        /* Icon gets gradient color automatically */
    }
    .container {
        max-width: 1200px;
        margin: 2rem auto 4rem; /* Balanced margins */
        display: flex;
        flex-wrap: wrap; /* Allows natural wrapping on tablets */
        gap: 2rem; /* Consistent spacing */
        justify-content: center;
        padding: 0 1rem;
    }

    /* =========================================
       3. COMPONENT: PROFILE CARD (Left)
       ========================================= */
    .profile-card {
        flex: 1 1 320px; /* Grow, Shrink, Basis */
        max-width: 350px;
        background: var(--white);
        padding: 2rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        text-align: center;
        animation: fadeIn 1s ease;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        align-self: flex-start; /* Aligns to top */
    }
    .profile-card:hover {
        box-shadow: var(--shadow-hover);
    }
    .avatar {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background: var(--bg-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 4rem;
        font-weight: 700;
        margin: 0 auto 1rem;
        overflow: hidden;
        border: 4px solid var(--white);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .profile-card h2 {
        color: var(--accent-text);
        font-size: 1.5rem;
        margin-bottom: 0.25rem;
    }
    .profile-card p {
        color: var(--text-light);
        font-size: 1rem;
        word-break: break-all; /* Prevents long emails from overflowing */
    }
    .profile-card form {
        margin-top: 1.5rem;
        display: flex;
        gap: 0.5rem; /* Space between buttons */
    }
    .upload-btn {
        flex: 1; /* Make buttons share space */
        background: var(--accent-pink-light);
        border: 1px solid var(--accent-pink-border);
        border-radius: 10px;
        padding: 10px 15px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--accent-text);
        transition: all 0.2s ease;
    }
    .upload-btn:hover {
        background: var(--accent-pink);
        color: var(--white);
    }
    .upload-btn i {
        margin-right: 6px;
    }

    /* =========================================
       4. COMPONENT: DETAILS PANEL (Right)
       ========================================= */
    .profile-details {
        flex: 2 1 500px; /* Grow, Shrink, Basis */
        background: var(--white);
        border-radius: var(--radius);
        padding: 2rem 2.5rem;
        box-shadow: var(--shadow);
        min-width: 300px; /* Prevents over-squishing */
    }
    .profile-details h3 {
        color: var(--accent-text);
        margin-bottom: 1.5rem;
        text-align: center;
        font-size: 1.6rem;
        font-weight: 600;
    }
    
    /* =========================================
       5. COMPONENT: FORMS & BUTTONS
       ========================================= */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr; /* 2-column grid for form */
        gap: 1rem;
    }
    .form-group {
        display: flex;
        flex-direction: column;
    }
    /* Make specific fields span 2 columns */
    .form-group.full-width {
        grid-column: 1 / -1;
    }
    label {
        display: block;
        font-weight: 600;
        color: var(--accent-text);
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }
    input[type="text"],
    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 14px 16px;
        border-radius: 12px;
        border: 2px solid var(--accent-pink-border);
        background: #fffdfb;
        font-size: 1rem;
        font-family: 'Poppins', sans-serif;
        transition: all 0.2s ease;
    }
    input:focus {
        border-color: var(--accent-pink);
        outline: none;
        box-shadow: 0 0 0 4px rgba(247, 180, 163, 0.25);
    }
    .button-group {
        grid-column: 1 / -1; /* Span full width */
        margin-top: 1.5rem;
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap; /* Allow buttons to stack on small screens */
    }
    .btn-primary, .btn-secondary {
        border: none;
        border-radius: 12px;
        padding: 12px 20px;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-primary {
        background: var(--bg-gradient);
        color: var(--white);
    }
    .btn-primary:hover {
        opacity: 0.9;
        box-shadow: 0 4px 15px rgba(247, 180, 163, 0.4);
    }
    .btn-secondary {
        background: #f1f1f1;
        color: var(--text-light);
    }
    .btn-secondary:hover {
        background: #e7e7e7;
    }
    .btn-primary i, .btn-secondary i {
        margin-right: 6px;
    }

    /* =========================================
       6. COMPONENT: ALERTS & ORDERS
       ========================================= */
    .alert {
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        font-weight: 500;
        grid-column: 1 / -1; /* Span full width */
    }
    .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
    .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
    
    .orders {
        margin-top: 2.5rem;
        grid-column: 1 / -1; /* Span full width */
    }
    .order-box {
        background: var(--accent-pink-light);
        border: 1px solid var(--accent-pink-border);
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 10px;
        line-height: 1.6;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }
    .order-box:hover {
        border-color: var(--accent-pink);
    }
    .order-box strong {
        color: var(--accent-text);
        font-weight: 600;
    }

    /* =========================================
       7. KEYFRAME ANIMATIONS
       ========================================= */
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.98); }
        to { opacity: 1; transform: scale(1); }
    }

    /* =========================================
       8. RESPONSIVE MEDIA QUERIES
       ========================================= */
    /* --- Tablet (and large phone) --- */
    @media (max-width: 900px) {
        .container {
            /* This will stack the columns since 320px + 500px + gap > 100% */
            flex-direction: column;
            align-items: center; /* Center the cards */
            gap: 1.5rem;
        }
        .profile-card, .profile-details {
            flex-basis: auto; /* Let them grow to 100% width */
            width: 100%;
            max-width: 500px; /* Constrain profile card width */
        }
        .profile-details {
            max-width: 600px; /* Allow details to be a bit wider */
        }
    }

    /* --- Mobile --- */
    @media (max-width: 600px) {
        .profile-title {
            font-size: 2rem;
        }
        .container {
            margin-top: 1rem;
            padding: 0 0.75rem;
        }
        .profile-card {
            padding: 1.5rem;
        }
        .profile-details {
            padding: 1.5rem 1.25rem;
        }
        .profile-details h3 {
            font-size: 1.4rem;
        }
        /* Stack the form grid into 1 column */
        .form-grid {
            grid-template-columns: 1fr;
        }
        .form-group.full-width {
            grid-column: 1 / 1; /* Reset column span */
        }
        .button-group {
            flex-direction: column; /* Stack buttons */
        }
        .btn-primary, .btn-secondary {
            width: 100%; /* Make buttons full-width */
        }
    }
</style>
</head>
<body>

<?php include 'header.php'; ?>
<div class="profile-title">
    <i class="fa fa-user-circle"></i> My Profile
</div>

<div class="container">
    <div class="profile-card">
        <div class="avatar">
            <?php if (!empty($profile['avatar']) && file_exists("../uploads/profile/".$profile['avatar'])): ?>
                <img src="../uploads/profile/<?= htmlspecialchars($profile['avatar']); ?>" alt="Avatar">
            <?php else: ?>
                <?= strtoupper(substr($profile['name'] ?? $_SESSION['fullname'] ?? 'U', 0, 1)); ?>
            <?php endif; ?>
        </div>

        <h2><?= pf($profile, 'name'); ?></h2>
        <p><?= pf($profile, 'email'); ?></p>

        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="upload_avatar">
            <input type="file" name="avatar" accept="image/*" id="avatarInput" style="display:none;">
            <button type="button" class="upload-btn" onclick="document.getElementById('avatarInput').click()">
                <i class="fa fa-upload"></i> Upload
            </button>
            <button type="submit" class="upload-btn">
                <i class="fa fa-save"></i> Save
            </button>
        </form>
    </div>

    <div class="profile-details">
        <form method="post">
            <input type="hidden" name="action" value="update_profile">
            <div class="form-grid">
                <h3>Account Information</h3>

                <?php if ($success): ?><div class="alert success"><?= $success ?></div><?php endif; ?>
                <?php if ($error): ?><div class="alert error"><?= $error ?></div><?php endif; ?>

                <div class="form-group full-width">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="<?= pf($profile, 'name'); ?>" required>
                </div>
                
                <div class="form-group full-width">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= pf($profile, 'email'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" value="<?= pf($profile, 'phone'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="<?= pf($profile, 'address'); ?>">
                </div>

                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" value="<?= pf($profile, 'city'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="postal_code">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" value="<?= pf($profile, 'postal_code'); ?>">
                </div>
                
                <div class="form-group full-width">
                    <label for="country">Country</label>
                    <input type="text" id="country" name="country" value="<?= pf($profile, 'country'); ?>">
                </div>

                <div class="form-group">
                    <label for="password">New Password (leave blank to keep)</label>
                    <input type="password" id="password" name="password" autocomplete="new-password">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password">
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-primary"><i class="fa fa-save"></i> Save Changes</button>
                    <button type="reset" class="btn-secondary"><i class="fa fa-times"></i> Cancel</button>
                </div>

                <div class="orders">
                    <h3>🧾 Recent Orders</h3>
                    <?php if ($orders && $orders->num_rows > 0): ?>
                        <?php while ($o = $orders->fetch_assoc()): ?>
                            <div class="order-box">
                                <strong>Order #<?= $o['id']; ?></strong>
                                <span>(<?= date("d M Y", strtotime($o['created_at'])); ?>)</span><br>
                                Status: <?= htmlspecialchars($o['status']); ?><br>
                                Total: ₹<?= number_format($o['total'], 2); ?>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: var(--text-light);">No recent orders found.</p>
                    <?php endif; ?>
                </div>
            </div> </form>
    </div>
</div>

<script>
    // Your existing script, just formatted
    const avatarInput = document.getElementById('avatarInput');
    avatarInput?.addEventListener('change', e => {
        if(e.target.files[0]) {
            console.log("Selected file:", e.target.files[0].name);
            // You could add a preview here
        }
    });
</script>

<?php include 'footer.php'; ?>
</body>
</html>