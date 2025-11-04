<?php
// customer/header.php
// v4: Re-balanced 1:2:1 desktop layout

if (session_status() === PHP_SESSION_NONE) session_start();
include __DIR__ . '/../config.php';

// --- Get User Data (Now includes Avatar) ---
$user_name = "";
$user_avatar = ""; // Variable for profile pic
$user_initial = "U"; // Fallback initial

if (!empty($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    
    // Updated query to fetch name AND avatar
    $stmt = $conn->prepare("SELECT name, avatar FROM users WHERE id=? LIMIT 1");
    
    if ($stmt) {
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res && $res->num_rows > 0) {
            $user_row = $res->fetch_assoc();
            
            // Store user data
            $user_name = $user_row['name'];
            $user_avatar = $user_row['avatar'] ?? '';
            
            // Update session and set fallback initial
            $_SESSION['fullname'] = $user_name;
            if (!empty($user_name)) {
                $user_initial = strtoupper(substr($user_name, 0, 1));
            }
        }
        $stmt->close();
    }
}

// --- Get Cart Count ---
$cart_count = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $it) {
        $cart_count += isset($it['quantity']) ? intval($it['quantity']) : 1;
    }
}

// Use safe basename for active link highlight
$current = basename($_SERVER['PHP_SELF']);

// Base path for all assets and links
$base_path = "/GiftIQ-main"; 
?>

<header class="navbar-desktop" role="banner" aria-label="Main navigation">
    
    <div class="nav-left">
        <a class="logo" href="<?php echo $base_path; ?>/index.php" aria-label="GiftIQ home">
            <img src="<?php echo $base_path; ?>/customer/images/logo.png" alt="GiftIQ" class="logo-img">
        </a>
    </div>

    <nav class="nav-center" role="navigation" aria-label="Primary">
        <a href="<?php echo $base_path; ?>/index.php" class="<?= $current == 'index.php' ? 'active' : '' ?>">Home</a>
        <a href="<?php echo $base_path; ?>/customer/collection.php" class="<?= $current == 'collection.php' ? 'active' : '' ?>">Collection</a>
        <a href="<?php echo $base_path; ?>/customer/customize.php" class="<?= $current == 'customize.php' ? 'active' : '' ?>">Customize</a>
    </nav>

    <div class="nav-right">
        <a href="<?php echo $base_path; ?>/customer/cart.php" class="nav-icon-link cart-link" aria-label="Cart">
            <i class="fa fa-shopping-cart"></i>
            <span class="cart-count"><?= intval($cart_count) ?></span>
        </a>

        <?php if (!empty($_SESSION['fullname'])): ?>
            <div class="profile-dropdown-container">
                <button id="profileToggle" class="profile-avatar-btn" aria-label="Open profile menu" aria-expanded="false">
                    <?php if (!empty($user_avatar) && file_exists(__DIR__ . "/../uploads/profile/" . $user_avatar)): ?>
                        <img src="<?php echo $base_path; ?>/uploads/profile/<?= htmlspecialchars($user_avatar); ?>" alt="Profile" class="profile-avatar-img">
                    <?php else: ?>
                        <span class="profile-avatar-initial"><?= htmlspecialchars($user_initial); ?></span>
                    <?php endif; ?>
                </button>
                
                <nav id="profileMenu" class="profile-dropdown" aria-hidden="true">
                    <div class="dropdown-header">
                        Hello, <strong><?= htmlspecialchars($_SESSION['fullname']) ?></strong>
                    </div>
                    <a href="<?php echo $base_path; ?>/customer/profile.php">
                        <i class="fa fa-user-circle"></i> My Profile
                    </a>
                    <a href="<?php echo $base_path; ?>/customer/my_order.php">
                        <i class="fa fa-box"></i> My Orders
                    </a>
                    <a href="<?php echo $base_path; ?>/customer/contact.php">
                        <i class="fa fa-question-circle"></i> Help
                    </a>
                    <a href="javascript:void(0);" onclick="confirmLogout();" class="logout-link">
                        <i class="fa fa-sign-out-alt"></i> Logout
                    </a>
                </nav>
            </div>

        <?php else: ?>
            <a href="<?php echo $base_path; ?>/customer/login.php" class="nav-login-btn">Login</a>
        <?php endif; ?>
    </div>
</header>

<header class="navbar-mobile-top" role="banner" aria-label="Mobile top bar">
    <a class="logo" href="<?php echo $base_path; ?>/index.php" aria-label="GiftIQ home">
        <img src="<?php echo $base_path; ?>/customer/images/logo.png" alt="GiftIQ" class="logo-img">
    </a>
    <a href="<?php echo $base_path; ?>/customer/cart.php" class="nav-icon-link cart-link" aria-label="Cart">
        <i class="fa fa-shopping-cart"></i>
        <span class="cart-count"><?= intval($cart_count) ?></span>
    </a>
</header>

<nav class="navbar-mobile-bottom" role="navigation" aria-label="Mobile navigation">
    <a href="<?php echo $base_path; ?>/index.php" class="<?= $current == 'index.php' ? 'active' : '' ?>">
        <i class="fa fa-home"></i>
        <span>Home</span>
    </a>
    <a href="<?php echo $base_path; ?>/customer/collection.php" class="<?= $current == 'collection.php' ? 'active' : '' ?>">
        <i class="fa fa-th-large"></i>
        <span>Collection</span>
    </a>
    <a href="<?php echo $base_path; ?>/customer/customize.php" class="<?= $current == 'customize.php' ? 'active' : '' ?>">
        <i class="fa fa-magic"></i>
        <span>Customize</span>
    </a>
    
    <?php if (!empty($_SESSION['fullname'])): ?>
        <button id="mobileProfileToggle" class="navbar-mobile-btn" aria-label="Open profile menu" aria-expanded="false">
            <?php if (!empty($user_avatar) && file_exists(__DIR__ . "/../uploads/profile/" . $user_avatar)): ?>
                <img src="<?php echo $base_path; ?>/uploads/profile/<?= htmlspecialchars($user_avatar); ?>" alt="Profile" class="profile-avatar-img-mobile">
            <?php else: ?>
                <i class="fa fa-user"></i>
            <?php endif; ?>
            <span>Profile</span>
        </button>
    <?php else: ?>
        <a href="<?php echo $base_path; ?>/customer/login.php" class="<?= $current == 'login.php' ? 'active' : '' ?>">
            <i class="fa fa-sign-in-alt"></i>
            <span>Login</span>
        </a>
    <?php endif; ?>
</nav>

<?php if (!empty($_SESSION['fullname'])): ?>
    <nav id="mobileProfileMenu" class="mobile-profile-menu" aria-hidden="true" role="dialog" aria-modal="true">
        <div class="mobile-profile-header">
            <span>Logged in as <strong><?= htmlspecialchars($_SESSION['fullname']) ?></strong></span>
            <button id="mobileProfileClose" class="mobile-profile-close" aria-label="Close menu">✕</button>
        </div>
        <a href="<?php echo $base_path; ?>/customer/profile.php">
            <i class="fa fa-user-circle"></i> My Profile
        </a>
        <a href="<?php echo $base_path; ?>/customer/my_order.php">
            <i class="fa fa-box"></i> My Orders
        </a>
        <a href="<?php echo $base_path; ?>/customer/contact.php">
            <i class="fa fa-question-circle"></i> Help
        </a>
        <a href="javascript:void(0);" onclick="confirmLogout();" class="logout-link">
            <i class="fa fa-sign-out-alt"></i> Logout
        </a>
    </nav>
<?php endif; ?>

<div id="menuOverlay" class="menu-overlay" tabindex="-1" aria-hidden="true"></div>

<style>
/* --- Import Poppins Font (if not already imported) --- */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

/* --- Root Variables --- */
:root {
    --header-height: 75px;
    --mobile-nav-height: 60px;
    --accent-pink: #f7d4d1;
    --accent-gold: #ffe6b3;
    --accent-text: #d47474;
    --text-dark: #333;
    --text-light: #666;
    --white: #fff;
    --border: #f3e6e2;
    --shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    --shadow-strong: 0 6px 20px rgba(0, 0, 0, 0.1);
    --gradient-hover: linear-gradient(135deg, #f7b4a3, #ffdba1);
}

/* --- Base Body Padding (for mobile bottom nav) --- */
body {
    padding-bottom: var(--mobile-nav-height);
}

/* =========================================
   DESKTOP HEADER (navbar-desktop)
   ========================================= */
.navbar-desktop {
    position: sticky;
    top: 0;
    z-index: 1000;
    display: flex;
    align-items: center;
    padding: 0 5%; /* Side padding */
    height: var(--header-height);
    background: var(--white);
    box-shadow: var(--shadow);
}
.logo-img {
    height: 42px;
    display: block;
}

/* --- THIS IS THE FIX: 1:2:1 Layout --- */
.nav-left {
    flex: 1; /* 1 part */
    display: flex;
    justify-content: flex-start; /* Align logo left */
}
.nav-center {
    flex: 2; /* 2 parts (main space) */
    display: flex;
    justify-content: center;
    gap: 1.5rem; /* Increased gap to fill space */
}
.nav-right {
    flex: 1; /* 1 part */
    display: flex;
    justify-content: flex-end; /* Align items right */
    align-items: center;
    padding: 10px;
    gap: 1rem;
}
/* --- End of Fix --- */


/* --- Center Nav Links --- */
.nav-center a {
    padding: 8px 14px;
    border-radius: 8px;
    text-decoration: none;
    color: var(--text-dark);
    font-weight: 500;
    font-size: 0.95rem;
    transition: all 0.2s ease;
}
.nav-center a.active,
.nav-center a:hover {
    background: var(--gradient-hover);
    color: var(--white);
}

/* --- Right Nav Icons/Buttons --- */
.nav-icon-link {
    position: relative;
    color: var(--text-light);
    font-size: 1.5rem;
    text-decoration: none;
    transition: color 0.2s ease;
}
.nav-icon-link:hover {
    color: var(--accent-text);
}
.cart-count {
    position: absolute;
    top: -5px;
    right: -10px;
    background: var(--accent-text);
    color: var(--white);
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}
.nav-login-btn {
    background: var(--gradient-hover);
    color: var(--white);
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.95rem;
    text-decoration: none;
    transition: all 0.2s ease;
}
.nav-login-btn:hover {
    opacity: 0.9;
    box-shadow: 0 4px 10px rgba(247, 180, 163, 0.3);
}

/* --- Profile Dropdown --- */
.profile-dropdown-container {
    position: relative;
}
.profile-avatar-btn {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    border: 2px solid var(--accent-pink);
    padding: 2px;
    background: var(--white);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.profile-avatar-img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}
.profile-avatar-initial {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 1.2rem;
    color: var(--accent-text);
}
.profile-dropdown {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    width: 220px;
    background: var(--white);
    border-radius: 12px;
    box-shadow: var(--shadow-strong);
    border: 1px solid var(--border);
    padding: 0.5rem;
    z-index: 1001;
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px);
    transition: all 0.2s ease-out;
}
.profile-dropdown.open {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}
.dropdown-header {
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
    color: var(--text-light);
    border-bottom: 1px solid var(--border);
}
.dropdown-header strong {
    color: var(--text-dark);
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.profile-dropdown a {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    text-decoration: none;
    color: var(--text-dark);
    font-weight: 500;
    font-size: 0.95rem;
    border-radius: 8px;
    transition: all 0.2s ease;
}
.profile-dropdown a i {
    width: 18px;
    text-align: center;
    color: var(--accent-text);
}
.profile-dropdown a:hover {
    background: #fff7f6;
    color: var(--accent-text);
}
.profile-dropdown a.logout-link {
    color: #e53935;
}
.profile-dropdown a.logout-link:hover {
    background: #fff0f0;
}

/* =========================================
   MOBILE HEADER (TOP BAR)
   ========================================= */
.navbar-mobile-top {
    position: sticky;
    top: 0;
    z-index: 999;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 5%;
    height: var(--header-height);
    background: var(--white);
    box-shadow: var(--shadow);
}
.navbar-mobile-top .logo-img {
    height: 40px;
}

/* =========================================
   MOBILE NAVIGATION (BOTTOM BAR)
   ========================================= */
.navbar-mobile-bottom {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: var(--mobile-nav-height);
    background: var(--white);
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.07);
    z-index: 998;
    display: flex;
    justify-content: space-around;
    align-items: flex-start;
    padding-top: 8px;
}
.navbar-mobile-bottom a,
.navbar-mobile-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    text-decoration: none;
    color: var(--text-light);
    font-size: 0.75rem;
    font-weight: 500;
    padding: 0 0.5rem;
    background: none;
    border: none;
    cursor: pointer;
    font-family: 'Poppins', sans-serif;
}
.navbar-mobile-bottom a i,
.navbar-mobile-btn i {
    font-size: 1.25rem;
}
.navbar-mobile-bottom a.active,
.navbar-mobile-btn.active {
    color: var(--accent-text);
}
.profile-avatar-img-mobile {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid var(--accent-text);
}

/* =========================================
   MOBILE PROFILE MENU
   ========================================= */
.mobile-profile-menu {
    position: fixed;
    bottom: var(--mobile-nav-height);
    left: 0;
    right: 0;
    background: var(--white);
    z-index: 1001;
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
    visibility: hidden;
    transform: translateY(100%);
    transition: all 0.3s cubic-bezier(0.2, 0.9, 0.3, 1);
    padding: 0.5rem;
    padding-bottom: 1rem;
}
.mobile-profile-menu.open {
    visibility: visible;
    transform: translateY(0);
}
.mobile-profile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.25rem;
    font-size: 0.95rem;
    color: var(--text-light);
}
.mobile-profile-close {
    background: none;
    border: none;
    font-size: 1.25rem;
    color: var(--text-light);
    cursor: pointer;
}
.mobile-profile-menu a {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.9rem 1.25rem;
    text-decoration: none;
    color: var(--text-dark);
    font-weight: 500;
    border-radius: 8px;
    transition: all 0.2s ease;
}
.mobile-profile-menu a i {
    width: 20px;
    text-align: center;
    color: var(--accent-text);
    font-size: 1.1rem;
}
.mobile-profile-menu a:hover,
.mobile-profile-menu a:active {
    background: #fff7f6;
    color: var(--accent-text);
}
.mobile-profile-menu a.logout-link {
    color: #e53935;
}
.mobile-profile-menu a.logout-link:hover {
    background: #fff0f0;
}

/* =========================================
   OVERLAY & RESPONSIVE HIDING
   ========================================= */
.menu-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease;
    z-index: 1000;
}
.menu-overlay.active {
    opacity: 1;
    visibility: visible;
}

@media (max-width: 992px) {
    /* Hide desktop nav, show mobile navs */
    .navbar-desktop {
        display: none;
    }
}
@media (min-width: 993px) {
    /* Hide mobile navs, show desktop nav */
    .navbar-mobile-top,
    .navbar-mobile-bottom,
    .mobile-profile-menu,
    .menu-overlay { /* Also hide overlay on desktop */
        display: none !important;
    }
    /* Remove body padding on desktop */
    body {
        padding-bottom: 0;
    }
}

/* =========================================
   CUSTOM LOGOUT POP-UP STYLES
   ========================================= */
.my-swal-popup {
    font-family: 'Poppins', sans-serif;
    border-radius: 16px;
    border: 1px solid #fff;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
}
.my-swal-title {
    font-family: 'Poppins', sans-serif;
    color: #d47474;
    font-weight: 600;
    font-size: 1.6rem;
}
.my-swal-text {
    color: #666;
    font-size: 1rem;
}
.my-swal-icon {
    color: #d47474;
    border-color: #f7d4d1;
}
.my-swal-popup .swal2-actions {
    gap: 0.75rem;
}
.my-swal-confirm,
.my-swal-cancel {
    font-family: 'Poppins', sans-serif;
    font-weight: 600 !important;
    border-radius: 10px !important;
    padding: 0.7rem 1.5rem !important;
    font-size: 0.95rem !important;
    border: none !important;
    transition: all 0.2s ease !important;
    box-shadow: none !important;
}
.my-swal-confirm {
    background: linear-gradient(135deg, #ffe6b3, #f7d4d1) !important;
    color: #333 !important;
}
.my-swal-confirm:hover {
    opacity: 0.9;
    transform: translateY(-2px);
}
.my-swal-confirm:focus {
    box-shadow: 0 0 0 3px rgba(247, 212, 209, 0.5) !important;
}
.my-swal-cancel {
    background: #f1f1f1 !important;
    color: #666 !important;
}
.my-swal-cancel:hover {
    background: #e7e7e7 !important;
}
.my-swal-cancel:focus {
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1) !important;
}
</style>

<script>
(function(){
    // --- Desktop Profile Dropdown ---
    var profileToggle = document.getElementById('profileToggle');
    var profileMenu = document.getElementById('profileMenu');
    var desktopOverlay = document.getElementById('menuOverlay'); // Re-using the overlay

    if (profileToggle && profileMenu) {
        profileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            var isOpen = profileMenu.classList.toggle('open');
            profileToggle.setAttribute('aria-expanded', isOpen);
            profileMenu.setAttribute('aria-hidden', !isOpen);
            desktopOverlay.classList.toggle('active', isOpen);
        });
    }

    // --- Mobile Profile Menu ---
    var mobileProfileToggle = document.getElementById('mobileProfileToggle');
    var mobileProfileMenu = document.getElementById('mobileProfileMenu');
    var mobileProfileClose = document.getElementById('mobileProfileClose');
    var mobileOverlay = document.getElementById('menuOverlay');

    if (mobileProfileToggle && mobileProfileMenu) {
        function openMobileMenu() {
            mobileProfileMenu.classList.add('open');
            mobileProfileMenu.setAttribute('aria-hidden', 'false');
            mobileProfileToggle.setAttribute('aria-expanded', 'true');
            mobileProfileToggle.classList.add('active');
            mobileOverlay.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeMobileMenu() {
            mobileProfileMenu.classList.remove('open');
            mobileProfileMenu.setAttribute('aria-hidden', 'true');
            mobileProfileToggle.setAttribute('aria-expanded', 'false');
            mobileProfileToggle.classList.remove('active');
            mobileOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        mobileProfileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            if (mobileProfileMenu.classList.contains('open')) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });

        if (mobileProfileClose) {
            mobileProfileClose.addEventListener('click', closeMobileMenu);
        }
    }

    // --- Universal Close Functionality ---
    function closeAllMenus() {
        if (profileMenu && profileMenu.classList.contains('open')) {
            profileMenu.classList.remove('open');
            profileToggle.setAttribute('aria-expanded', 'false');
            profileMenu.setAttribute('aria-hidden', 'true');
            desktopOverlay.classList.remove('active');
        }
        if (mobileProfileMenu && mobileProfileMenu.classList.contains('open')) {
            mobileProfileMenu.classList.remove('open');
            mobileProfileMenu.setAttribute('aria-hidden', 'true');
            mobileProfileToggle.setAttribute('aria-expanded', 'false');
            mobileProfileToggle.classList.remove('active');
            mobileOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    // Close menus when overlay is clicked
    desktopOverlay.addEventListener('click', closeAllMenus);

    // Close menus on 'Escape' key
    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape') {
            closeAllMenus();
        }
    });

    // Close mobile menu if a link inside it is clicked
    if (mobileProfileMenu) {
        Array.prototype.slice.call(mobileProfileMenu.querySelectorAll('a')).forEach(function(a){
            a.addEventListener('click', function(){
                setTimeout(closeAllMenus, 120);
            });
        });
    }

})();

// --- Your Logout Confirmation Script (Unchanged) ---
function confirmLogout() {
    Swal.fire({
        title: 'Are you sure?',
        text: "You will be logged out of your session.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Log Out',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'my-swal-popup',
            title: 'my-swal-title',
            htmlContainer: 'my-swal-text',
            icon: 'my-swal-icon',
            confirmButton: 'my-swal-confirm',
            cancelButton: 'my-swal-cancel'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Using the full path for safety
            window.location.href = '<?php echo $base_path; ?>/customer/logout.php';
        }
    });
}
</script>