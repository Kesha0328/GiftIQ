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
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #fff4e8, #ffe8d6);
  margin: 0;
  padding: 0;
  color: #333;
}

.profile-title {
  text-align: center;
  font-size: 3rem;
  font-weight: 900;
  background: linear-gradient(90deg, #e8594fff, #f5a732ff);
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
  color: #f81818ff;
}

.profile-title::after {
  content: "";
  display: block;
  width: 120px;
  height: 4px;
  border-radius: 4px;
  background: linear-gradient(90deg, #f7b4a3, #ffdba1);
  margin: 3px auto 0;
}

@keyframes fadeInDown {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}



.container {
  max-width: 1200px;
  margin: 60px auto;
  display: flex;
  flex-wrap: wrap;
  gap: 30px;
  justify-content: center;
  padding: 20px;
}

.profile-card {
    flex: 0 0 320px;
  background: #fff;
  height: 260px;
margin-top: -30px;
  padding: 30px;
  border-radius: 20px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.05);
  text-align: center;
  animation: fadeIn 1s ease;
}
.profile-card:hover { transform: translateY(-4px); }

.avatar {
  width: 130px;
  height: 130px;
  border-radius: 50%;
  background: linear-gradient(135deg, #f7b4a3, #ffdba1);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 40px;
  font-weight: 700;
  margin: 0 auto 15px;
  overflow: hidden;
}
.avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}

.profile-card h2 {
  color: #d47474;
  font-size: 1.4rem;
  margin-bottom: 6px;
}

.profile-card p {
  color: #777;
  font-size: 0.95rem;
}

.profile-card form {
  margin-top: 15px;
}
.upload-btn {
  background: linear-gradient(135deg, #f8d7c8, #ffeab5);
  border: none;
  border-radius: 10px;
  padding: 10px 18px;
  margin: 5px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.2s;
}
.upload-btn:hover {
  transform: translateY(-2px);
}

.profile-details {
  flex: 1;
  background: #fff;
  border-radius: 20px;
  margin-top: -30px;
  padding: 40px 50px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.06);
  min-width: 350px;
}

h3 {
  color: #d47474;
  margin-bottom: 20px;
  text-align: center;
  font-size: 1.5rem;
}

label {
  display: block;
  font-weight: 600;
  color: #d47474;
  margin: 12px 0 5px;
}

input {
  width: 100%;
  padding: 14px 16px;
  border-radius: 12px;
  border: 2px solid #f3dede;
  background: #fffdfb;
  font-size: 1rem;
  transition: all 0.2s ease;
}
input:focus {
  border-color: #f7b4a3;
  outline: none;
  box-shadow: 0 0 0 3px rgba(247,180,163,0.25);
}

.btn-primary, .btn-secondary {
  border: none;
  border-radius: 12px;
  padding: 12px 18px;
  font-weight: 600;
  cursor: pointer;
  margin: 10px 5px;
  transition: all 0.3s ease;
}
.btn-primary {
  background: linear-gradient(135deg, #f7d4d1, #ffe6b3);
  color: #333;
}
.btn-primary:hover { transform: translateY(-3px); }
.btn-secondary {
  background: #f5f5f5;
  color: #666;
}
.btn-secondary:hover { background: #ececec; }

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
  border: 1px solid #f3dede;
  border-radius: 12px;
  padding: 12px 16px;
  margin-bottom: 10px;
  line-height: 1.6;
}
.order-box strong { color: #d47474; }

@media(max-width:768px) {
  .container { flex-direction: column; align-items: center; }
  .profile-details { width: 95%; padding: 30px; }
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
      <button type="button" class="upload-btn" onclick="document.getElementById('avatarInput').click()">Upload</button>
      <button type="submit" class="upload-btn">Save</button>
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

      <button type="submit" class="btn-primary">💾 Save Changes</button>
      <button type="reset" class="btn-secondary">✖ Cancel</button>
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
