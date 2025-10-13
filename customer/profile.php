<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch current user info
$res = $conn->query("SELECT name, email, phone FROM users WHERE id=$user_id");
$user = $res->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Update details
    $conn->query("UPDATE users SET name='$name', email='$email', phone='$phone' WHERE id=$user_id");

    // Password change (optional)
    if (!empty($_POST['password'])) {
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password='$hash' WHERE id=$user_id");
    }

    $success = "Profile updated successfully!";
    $res = $conn->query("SELECT name, email, phone FROM users WHERE id=$user_id");
    $user = $res->fetch_assoc(); // refresh data
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link rel="stylesheet" href="customerpanel.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="collection-header">
        <h1>👤 My Profile</h1>
    </div>

    <div class="customize-section">
        <?php if (!empty($success)) echo "<div class='message success'>$success</div>"; ?>

        <form method="post">
            <p><label>Name</label><br>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']); ?>" required></p>
            <p><label>Email</label><br>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required></p>
            <p><label>Phone</label><br>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']); ?>"></p>
            <p><label>New Password (leave blank to keep current)</label><br>
            <input type="password" name="password"></p>
            <button type="submit" class="btn-primary">Update Profile</button>
        </form>
    </div>
</body>
</html>
