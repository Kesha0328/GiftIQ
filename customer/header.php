<?php
// customer/header.php — Left-side slide-in menu (Option 2)
// Keep this file as a single drop-in replacement for your existing header.php

if (session_status() === PHP_SESSION_NONE) session_start();
include __DIR__ . '/../config.php';

// Get logged user name (if any)
$user_name = "";
if (!empty($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    $stmt = $conn->prepare("SELECT name FROM users WHERE id=? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $user_name = $res->fetch_assoc()['name'];
            $_SESSION['fullname'] = $user_name;
        }
        $stmt->close();
    }
}

// Cart count from session (works with both custom/standard item shapes)
$cart_count = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $it) {
        $cart_count += isset($it['quantity']) ? intval($it['quantity']) : 1;
    }
}

// Use safe basename for active link highlight
$current = basename($_SERVER['PHP_SELF']);
?>
<!-- keep your existing CSS includes, plus the new script/styles below -->
<link rel="stylesheet" href="assets/main.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<header class="navbar" role="banner" aria-label="Main navigation">
  <!-- LEFT: hamburger & logo (hamburger on left as requested) -->
  <div class="nav-left" style="display:flex;align-items:center;gap:12px">
    <button id="menuToggle" class="menu-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="mobileMenu">
      <span></span><span></span><span></span>
    </button>

    <a class="logo" href="/GiftIQ-main/index.php" aria-label="GiftIQ home">
      <img src="/GiftIQ-main/customer/images/logo.png" alt="GiftIQ" class="logo-img">
    </a>
  </div>

  <!-- center (desktop) area — kept minimal so header height small -->
  <div class="nav-center" aria-hidden="true"></div>

  <!-- RIGHT: normal desktop links (hidden on small screens) -->
  <nav class="nav-links desktop-only" role="navigation" aria-label="Primary">
    <a href="/GiftIQ-main/index.php" class="<?= $current == 'index.php' ? 'active' : '' ?>">Home</a>
    <a href="/GiftIQ-main/customer/collection.php" class="<?= $current == 'collection.php' ? 'active' : '' ?>">Collection</a>
    <a href="/GiftIQ-main/customer/customize.php" class="<?= $current == 'customize.php' ? 'active' : '' ?>">Customize</a>
    <a href="/GiftIQ-main/customer/my_order.php" class="<?= $current == 'my_order.php' ? 'active' : '' ?>">My Orders</a>
    <a href="/GiftIQ-main/customer/contact.php" class="<?= $current == 'contact.php' ? 'active' : '' ?>">Contact</a>
    <a href="/GiftIQ-main/customer/about.php" class="<?= $current == 'about.php' ? 'active' : '' ?>">About</a>
    <a href="/GiftIQ-main/customer/cart.php" class="cart-link">🛒 Cart (<?= intval($cart_count) ?>)</a>
    <?php if (!empty($_SESSION['fullname'])): ?>
      <a href="/GiftIQ-main/customer/profile.php" class="profile-link">Profile (<?= htmlspecialchars($_SESSION['fullname']) ?>)</a>
      <a href="/GiftIQ-main/customer/logout.php" class="logout-link">Logout</a>
    <?php else: ?>
      <a href="/GiftIQ-main/customer/login.php" class="login-link">Login</a>
    <?php endif; ?>
  </nav>
</header>

<aside id="mobileMenu" class="mobile-menu" role="dialog" aria-modal="true" aria-hidden="true">
  <div class="mobile-menu-head">
    <a class="logo" href="/GiftIQ-main/index.php">
      <img src="/GiftIQ-main/customer/images/logo.png" alt="GiftIQ" class="logo-img">
    </a>
    <button id="menuClose" class="menu-close" aria-label="Close menu">✕</button>
  </div>

  <nav class="mobile-links" role="navigation" aria-label="Mobile primary">
    <a href="/GiftIQ-main/index.php" class="<?= $current == 'index.php' ? 'active' : '' ?>">Home</a>
    <a href="/GiftIQ-main/customer/collection.php" class="<?= $current == 'collection.php' ? 'active' : '' ?>">Collection</a>
    <a href="/GiftIQ-main/customer/customize.php" class="<?= $current == 'customize.php' ? 'active' : '' ?>">Customize</a>
    <a href="/GiftIQ-main/customer/my_order.php" class="<?= $current == 'my_order.php' ? 'active' : '' ?>">My Orders</a>
    <a href="/GiftIQ-main/customer/contact.php" class="<?= $current == 'contact.php' ? 'active' : '' ?>">Contact</a>
    <a href="/GiftIQ-main/customer/about.php" class="<?= $current == 'about.php' ? 'active' : '' ?>">About</a>
    <a href="/GiftIQ-main/customer/cart.php">🛒 Cart (<?= intval($cart_count) ?>)</a>

    <?php if (!empty($_SESSION['fullname'])): ?>
      <a href="/GiftIQ-main/customer/profile.php">Profile (<?= htmlspecialchars($_SESSION['fullname']) ?>)</a>
      <a href="/GiftIQ-main/customer/logout.php">Logout</a>
    <?php else: ?>
      <a href="/GiftIQ-main/customer/login.php">Login</a>
    <?php endif; ?>
  </nav>

  <div class="mobile-footer">
    <small>© <?= date('Y') ?> Mad Smile — Because every smile deserves a gift.</small>
  </div>
</aside>

<div id="menuOverlay" class="menu-overlay" tabindex="-1" aria-hidden="true"></div>

<style>
.navbar {
  position: sticky;
  top: 0;
  z-index: 9999;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 5%;
  background: #fff;
  box-shadow: 0 3px 12px rgba(0,0,0,0.06);
  border-bottom-left-radius: 10px;
  border-bottom-right-radius: 10px;
}
.logo-img { height: 42px; display:block; }

.nav-links a { margin-left: 10px; text-decoration:none; padding:8px 12px; border-radius:8px; color:#222; font-weight:500; }
.nav-links a.active, .nav-links a:hover {
  background: linear-gradient(135deg,#f7b4a3,#ffdba1);
  color: #fff;
}

.menu-toggle {
  display: inline-flex;
  align-items:center;
  justify-content:center;
  width:44px; height:44px;
  border-radius:10px;
  border:0;
  background:transparent;
  cursor:pointer;
}
.menu-toggle span { display:block; width:22px; height:2px; background:#d47474; margin:3px 0; border-radius:2px; transition:all .25s; }

.desktop-only { display:flex; gap:8px; align-items:center; }

.mobile-menu {
  position: fixed;
  top: 0;
  left: -100%;
  width: 86%;
  max-width: 360px;
  height: 100vh;
  background: #fff;
  z-index: 10000;
  box-shadow: 6px 0 24px rgba(0,0,0,0.12);
  transition: left .36s cubic-bezier(.2,.9,.3,1);
  display:flex;
  flex-direction:column;
  justify-content:space-between;
}
.mobile-menu.open { left: 0; }

.mobile-menu-head { display:flex; align-items:center; justify-content:space-between; padding:18px; border-bottom:1px solid #f3e6e2; }
.menu-close { background:transparent; border:0; font-size:20px; cursor:pointer; color:#d47474; }

.mobile-links { padding:22px; display:flex; flex-direction:column; gap:12px; }
.mobile-links a { padding:12px 14px; border-radius:10px; text-decoration:none; color:#333; background:#fff7f6; font-weight:600; text-align:left; }
.mobile-links a.active, .mobile-links a:hover { background: linear-gradient(135deg,#f7b4a3,#ffdba1); color:#fff; transform:translateX(2px); }

.mobile-footer { padding:16px; border-top:1px solid #f3e6e2; text-align:center; color:#777; font-size:0.9rem; }

.menu-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.45);
  opacity: 0;
  visibility: hidden;
  transition: opacity .3s ease;
  z-index: 9999;
}
.menu-overlay.active { opacity: 1; visibility: visible; }

@media (max-width: 992px) {
  .desktop-only { display:none; }
}
@media (min-width: 993px) {
  .menu-toggle { display: none; }
  .mobile-menu, .menu-overlay { display:none; }
}
</style>

<script>
(function(){
  var toggle = document.getElementById('menuToggle');
  var menu = document.getElementById('mobileMenu');
  var overlay = document.getElementById('menuOverlay');
  var closeBtn = document.getElementById('menuClose');

  if (!toggle || !menu || !overlay) return;

  function openMenu() {
    menu.classList.add('open');
    overlay.classList.add('active');
    toggle.setAttribute('aria-expanded','true');
    menu.setAttribute('aria-hidden','false');

    document.body.style.overflow = 'hidden';
  }
  function closeMenu() {
    menu.classList.remove('open');
    overlay.classList.remove('active');
    toggle.setAttribute('aria-expanded','false');
    menu.setAttribute('aria-hidden','true');
    document.body.style.overflow = '';
  }

  toggle.addEventListener('click', function(e){
    if (menu.classList.contains('open')) closeMenu(); else openMenu();
  });
  overlay.addEventListener('click', closeMenu);
  if (closeBtn) closeBtn.addEventListener('click', closeMenu);

  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape' && menu.classList.contains('open')) closeMenu();
  });

  Array.prototype.slice.call(document.querySelectorAll('.mobile-links a')).forEach(function(a){
    a.addEventListener('click', function(){
      setTimeout(closeMenu, 120);
    });
  });
})();
</script>
