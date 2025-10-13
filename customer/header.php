<?php
// customer/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
include __DIR__ . '/../config.php';

// Detect logged-in user
$user_name = "";
if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    $stmt = $conn->prepare("SELECT name FROM users WHERE id=?");
    $stmt->bind_param("i",$uid);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows>0){
        $user_name = $res->fetch_assoc()['name'];
        $_SESSION['fullname'] = $user_name;
    }
}

// ✅ Get cart count directly from session
$cart_count = 0;
if (!empty($_SESSION['cart'])){
    if (array_values($_SESSION['cart']) === $_SESSION['cart']) {
        foreach($_SESSION['cart'] as $it) {
            $cart_count += isset($it['quantity']) ? intval($it['quantity']) : 1;
        }
    } else {
        $cart_count = array_sum($_SESSION['cart']);
    }
}
?>
<link rel="stylesheet" href="/GiftIQ-main/assets/css/customerpanel.css">

<header class="navbar">
    <div class="logo">
    <a href="/GiftIQ-main/index.php">
        <img src="/GiftIQ-main/customer/images/logo.png" alt="logo" style="height:40px;">
    </a>
    </div>

<nav class="nav-links">
    <a href="/GiftIQ-main/index.php" class="<?= basename($_SERVER['PHP_SELF'])=='index.php'?'active':'' ?>">Home</a>
    <a href="/GiftIQ-main/customer/collection.php" class="<?= basename($_SERVER['PHP_SELF'])=='collection.php'?'active':'' ?>">Collection</a>
    <a href="/GiftIQ-main/customer/about.php" class="<?= basename($_SERVER['PHP_SELF'])=='about.php'?'active':'' ?>">About</a>
    <a href="/GiftIQ-main/customer/my_order.php" class="<?= basename($_SERVER['PHP_SELF'])=='my_order.php'?'active':'' ?>">My Orders</a>
    <a href="/GiftIQ-main/customer/contact.php" class="<?= basename($_SERVER['PHP_SELF'])=='contact.php'?'active':'' ?>">Contact</a>

    <!-- Cart count is session-based -->
    <a href="/GiftIQ-main/customer/cart.php">🛒 Cart (<?= $cart_count ?>)</a>

    <?php if (!empty($_SESSION['fullname'])): ?>
        <a href="/GiftIQ-main/customer/profile.php">Profile (<?= htmlspecialchars($_SESSION['fullname']); ?>)</a>
        <a href="/GiftIQ-main/customer/logout.php">Logout</a>
    <?php else: ?>
        <a href="/GiftIQ-main/customer/login.php">Login</a>
    <?php endif; ?>
    </nav>
</header>
