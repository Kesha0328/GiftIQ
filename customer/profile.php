<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = intval($_SESSION['user_id']);
$success = $error = "";

function fetchProfile($conn, $user_id) {
  $stmt = $conn->prepare("SELECT id, name, email, phone, address, city, postal_code, country, avatar FROM users WHERE id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $res = $stmt->get_result();
  return $res->fetch_assoc();
}
function pf($arr, $key, $fallback = '') {
  return isset($arr[$key]) ? $arr[$key] : $fallback;
}
$profile = fetchProfile($conn, $user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'upload_avatar') {
  if (!empty($_FILES['avatar']['name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','webp'];
    if (in_array($ext, $allowed)) {
      $filename = "user_" . $user_id . "." . $ext;
      $path = "../uploads/profile/" . $filename;
      if (!is_dir("../uploads/profile/")) mkdir("../uploads/profile/", 0777, true);
      move_uploaded_file($_FILES['avatar']['tmp_name'], $path);
      $conn->query("UPDATE users SET avatar='$filename' WHERE id=$user_id");
      $success = "Profile photo updated successfully ✅";
      $profile['avatar'] = $filename;
    } else $error = "Invalid image format.";
  } else $error = "Please select an image file.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update_profile') {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);
  $address = trim($_POST['address']);
  $city = trim($_POST['city']);
  $postal = trim($_POST['postal_code']);
  $country = trim($_POST['country']);
  $conn->query("UPDATE users SET name='$name', email='$email', phone='$phone', address='$address', city='$city', postal_code='$postal', country='$country' WHERE id=$user_id");

  if (!empty($_POST['password']) && $_POST['password'] === $_POST['confirm_password']) {
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $conn->query("UPDATE users SET password='$hash' WHERE id=$user_id");
    $success = "Profile and password updated ✅";
  } elseif (!empty($_POST['password'])) {
    $error = "Passwords do not match ❌";
  } else {
    $success = "Profile updated successfully ✅";
  }
  $_SESSION['fullname'] = $name;
  $profile = fetchProfile($conn, $user_id);
}

$orders = $conn->query("SELECT id, total, status, created_at FROM orders WHERE user_id=$user_id ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile | GiftIQ</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="icon" type="image/png" href="../uploads/favicon.png" />
<style>
/* ----------- GLOBAL STYLES ----------- */
:root {
  /* Define main colors for easy changes */
  --primary-color: #d47474;
  --primary-light: #f7b4a3;
  --secondary-light: #ffdba1;
  --bg-page: linear-gradient(135deg, #fff4e8, #ffe8d6);
  --bg-card: #fff;
  --bg-alt: #f1f1f1;
  --text-dark: #333;
  --text-light: #777;
  --border-color: #f3dede;
  --shadow-soft: 0 4px 16px rgba(0,0,0,0.05);
  --shadow-medium: 0 6px 16px rgba(212, 116, 116, 0.4);
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Poppins', sans-serif;
  background: var(--bg-page);
  margin: 0;
  padding: 0;
  color: var(--text-dark);
}

.container {
  max-width: 1200px;
  margin: 40px auto; /* Reduced margin */
  display: flex;
  flex-wrap: wrap;
  gap: 30px;
  justify-content: center;
  padding: 20px;
}

h3 {
  color: var(--primary-color);
  margin-bottom: 20px;
  text-align: center;
  font-size: 1.5rem;
}

/* ----------- TITLE ----------- */
.profile-title {
  text-align: center;
  font-size: 3rem;
  font-weight: 900;
  background: var(--primary-color); /* Use variable */
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  margin-top: 20px;
  margin-bottom: 20px;
  letter-spacing: 0.6px;
  position: relative;
  animation: fadeInDown 0.8s ease;
}

.profile-title i {
  margin-right: 8px;
  /* [FIX] Changed from bright red to match text */
  color: var(--primary-color); 
}

/* --- Profile Card (Left) --- */
.profile-card {
  flex: 0 0 320px;
  background: var(--bg-card);
  padding: 1.5rem; /* 24px */
  border-radius: 16px;
  box-shadow: var(--shadow-soft);
  text-align: center;
  animation: fadeIn 1s ease;
  height: fit-content; /* Let height be automatic */
}

.avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--primary-light), var(--secondary-light));
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--bg-card);
  font-size: 32px;
  font-weight: 700;
  margin: 0 auto 1rem;
  overflow: hidden;
}
.avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.profile-card h2 {
  color: var(--text-dark);
  font-size: 1.25rem;
  margin-bottom: 0.25rem;
}

.profile-card p {
  color: var(--text-light);
  font-size: 0.9rem;
  margin-bottom: 1.5rem;
}

.profile-card form {
  display: flex;
  flex-direction: column; 
  gap: 0.5rem;
}

/* --- Profile Details (Right) --- */
.profile-details {
  flex: 1;
  background: var(--bg-card);
  border-radius: 20px;
  margin-top: -30px; /* This is the overlap effect */
  padding: 30px 35px; /* Reduced padding */
  box-shadow: 0 10px 30px rgba(0,0,0,0.06);
  min-width: 350px;
}

.profile-details label {
  display: block;
  font-weight: 600;
  /* [FIX] Changed from pink to a readable dark gray */
  color: #555; 
  margin: 12px 0 5px;
}

.profile-details input {
  width: 100%;
  padding: 14px 16px;
  border-radius: 12px;
  border: 2px solid var(--border-color);
  background: #fffdfb;
  font-size: 1rem;
  transition: all 0.2s ease;
}
.profile-details input:focus {
  border-color: var(--primary-light);
  outline: none;
  box-shadow: 0 0 0 3px rgba(247,180,163,0.25);
}

/* --- UNIFIED BUTTON STYLES --- */
/* This one base class replaces all other button styles */
.btn {
  border: none;
  border-radius: 12px;
  padding: 12px 18px;
  font-weight: 600;
  cursor: pointer;
  font-size: 0.9rem;
  text-align: center;
  transition: all 0.3s ease;
}

/* Primary action (Save, Upload, etc.) */
.btn-primary {
  background: var(--primary-color); 
  color: #fff;
}
.btn-primary:hover {
   box-shadow: var(--shadow-soft);
}

/* Secondary action (Cancel, View, etc.) */
.btn-secondary {
  background: var(--bg-alt); 
  color: #555;
}
.btn-secondary:hover {
  box-shadow: var(--shadow-soft);
}
/* --- END OF UNIFIED STYLES --- */


/* --- Other Components --- */
.success, .error {
  padding: 12px;
  border-radius: 10px;
  margin-bottom: 15px;
  font-weight: 500;
}
.success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
.error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }

.orders {
  margin-top: 30px;
}
.order-box {
  background: #fff9f8;
  border: 1px solid var(--border-color);
  border-radius: 12px;
  padding: 12px 16px;
  margin-bottom: 10px;
  line-height: 1.6;
}
.order-box strong { color: var(--primary-color); }

/* ----------- RESPONSIVE (MOBILE) FIX ----------- */
@media(max-width:768px) {
  .container { 
    flex-direction: column; 
    align-items: center; 
    margin-top: 20px;
    gap: 20px; /* Closer together on mobile */
  }

  .profile-card {
    /* This card is now on top, so it needs a 
       bottom margin, not a negative top margin */
    margin-top: 0;
  }
  
  .profile-details { 
    width: 95%; 
    padding: 25px 20px;
    /* [THE BUG FIX] 
       Resets the negative margin to 0 so it 
       doesn't crash into the card above it. */
    margin-top: 0; 
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

    <h2><?= htmlspecialchars($profile['name']); ?></h2>
    <p><?= htmlspecialchars($profile['email']); ?></p>

    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="action" value="upload_avatar">
      <input type="file" name="avatar" accept="image/*" id="avatarInput" style="display:none;">
      
      <button type="button" class="btn btn-secondary" onclick="document.getElementById('avatarInput').click()">Upload</button>
      
      <button type="submit" class="btn btn-primary">Save</button>
    </form>
  </div>

  <div class="profile-details">
    <h3>Account Information</h3>
    <?php if ($success): ?><div class="success"><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>

    <form method="post">
      <input type="hidden" name="action" value="update_profile">

      <label>Full Name</label>
      <input type="text" name="name" value="<?= htmlspecialchars($profile['name']); ?>" required>

      <label>Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($profile['email']); ?>" required>

      <label>Phone</label>
      <input type="text" name="phone" value="<?= htmlspecialchars($profile['phone']); ?>">

      <label>Address</label>
      <input type="text" name="address" value="<?= htmlspecialchars($profile['address']); ?>">

      <label>City</label>
      <input type="text" name="city" value="<?= htmlspecialchars($profile['city']); ?>">

      <label>Postal Code</label>
      <input type="text" name="postal_code" value="<?= htmlspecialchars($profile['postal_code']); ?>">

      <label>Country</label>
      <input type="text" name="country" value="<?= htmlspecialchars($profile['country']); ?>">

      <label>New Password (leave blank to keep)</label>
      <input type="password" name="password">

      <label>Confirm Password</label>
      <input type="password" name="confirm_password">

      <button type="submit" class="btn btn-primary">💾 Save Changes</button>
      
      <button type="reset" class="btn btn-secondary">✖ Cancel</button>
    </form>

    <div class="orders">
      <h3>🧾 Recent Orders</h3>
      <?php if ($orders && $orders->num_rows > 0): ?>
        <?php while ($o = $orders->fetch_assoc()): ?>
          <div class="order-box">
            <strong>Order #<?= $o['id']; ?></strong><br>
            Status: <?= htmlspecialchars($o['status']); ?><br>
            Total: ₹<?= number_format($o['total'], 2); ?><br>
            Date: <?= date("d M Y", strtotime($o['created_at'])); ?>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No orders yet.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  const avatarInput = document.getElementById('avatarInput');
  avatarInput?.addEventListener('change', e => {
    if(e.target.files[0]) console.log("Selected file:", e.target.files[0].name);
  });
</script>
</body>
</html>
