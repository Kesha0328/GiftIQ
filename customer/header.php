<?php
// customer/header.php
// v8: Added !important to body padding to prevent overrides

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
        <button id="mobileProfileToggle" class="navbar-mobile-btn <?= ($current == 'profile.php' || $current == 'my_order.php') ? 'active' : '' ?>" aria-label="Open profile menu" aria-expanded="false">
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
    <nav id="mobileProfileMenu" class="mobile-profile-dropdown" aria-hidden="true" role="dialog" aria-modal="true">
        <div class="dropdown-header">
            Hello, <strong><?= htmlspecialchars($_SESSION['fullname']) ?></strong>
        </div>
        <a href="<?php echo $base_path; ?>/customer/profile.php">
            <i class="fa fa-user-circle"></i> My Profile
        </a>
        <a href="<?php echo $base_path; ?>/customer/my_order.php">
            <i class="fa fa-box"></i> My Orders
        </a>
        <a href="<?php echo $base_path; ?>/customer/help.php">
            <i class="fa fa-question-circle"></i> Help
        </a>
        <a href="javascript:void(0);" onclick="confirmLogout();" class="logout-link">
            <i class="fa fa-sign-out-alt"></i> Logout
        </a>
    </nav>
<?php endif; ?>

<div id="menuOverlay" class="menu-overlay" tabindex="-1" aria-hidden="true"></div>

<style>
/* --- This imports the public Font Awesome library --- */
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css');

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

/* --- Page Fade Animations --- */
@keyframes bodyFadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes bodyFadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}
body {
    animation: bodyFadeIn 0.2s ease-out;
    /* THIS IS THE FIX: Added !important 
      This prevents other CSS files from removing the padding.
    */
    padding-bottom: var(--mobile-nav-height) !important; 
    padding-top: 65px !important; 
}
body.body-fading-out {
    animation: bodyFadeOut 0.1s ease-in forwards; 
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
    padding: 0 5%;
    height: var(--header-height);
    background: var(--white);
    box-shadow: var(--shadow);
}
.logo-img {
    height: 42px;
    display: block;
}
.nav-left {
    flex: 1; 
    display: flex;
    justify-content: flex-start;
}
.nav-center {
    flex: 2; 
    display: flex;
    justify-content: center;
    gap: 2.5rem; 
}
.nav-right {
    flex: 1; 
    display: flex;
    justify-content: flex-end; 
    align-items: center;
    gap: 1.25rem; 
}

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
    text-decoration: none;
    transition: color 0.2s ease;
}
/* --- ICON FIX 1: Target <i> AND <svg> --- */
.navbar-desktop .nav-icon-link i,
.navbar-desktop .nav-icon-link svg {
    font-size: 1.25rem !important; 
    width: 1.25rem !important;
    height: 1.25rem !important;
    vertical-align: middle;
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

/* --- Profile Dropdown (Desktop) --- */
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
/* --- ICON FIX 2: Target <i> AND <svg> --- */
nav.profile-dropdown a i,
nav.profile-dropdown a svg {
    font-size: 1.25rem !important; 
    width: 1.25rem !important; 
    height: 1.25rem !important;
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
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 999;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 5%;
    height: 65px; 
    background: var(--white);
    box-shadow: var(--shadow);
}
.navbar-mobile-top .logo-img {
    height: 40px;
}
/* --- ICON FIX 3: Target <i> AND <svg> --- */
.navbar-mobile-top .nav-icon-link i,
.navbar-mobile-top .nav-icon-link svg {
    font-size: 1.25rem !important;
    width: 1.25rem !important;
    height: 1.25rem !important;
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
    flex: 1;
    text-align: center;
}
/* --- ICON FIX 4: Target <i> AND <svg> --- */
nav.navbar-mobile-bottom a i,
nav.navbar-mobile-bottom .navbar-mobile-btn i,
nav.navbar-mobile-bottom a svg,
nav.navbar-mobile-bottom .navbar-mobile-btn svg {
    font-size: 1.25rem !important;
    width: 1.25rem !important;
    height: 1.25rem !important;
}
.navbar-mobile-bottom a.active,
.navbar-mobile-btn.active {
    color: var(--accent-text);
}
.cart-link-mobile {
    position: relative;
}
.navbar-mobile-bottom .cart-count {
    top: -4px;
    right: 15px;
    width: 18px;
    height: 18px;
    font-size: 0.7rem;
}
.profile-avatar-img-mobile {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid var(--text-light);
}
.navbar-mobile-btn.active .profile-avatar-img-mobile {
    border-color: var(--accent-text);
}

/* =========================================
    MOBILE PROFILE DROPDOWN
    ========================================= */
.mobile-profile-dropdown {
    position: fixed;
    bottom: calc(var(--mobile-nav-height) + 10px); 
    right: 10px;
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
.mobile-profile-dropdown.open {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}
.mobile-profile-dropdown .dropdown-header {
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
    color: var(--text-light);
    border-bottom: 1px solid var(--border);
}
.mobile-profile-dropdown .dropdown-header strong {
    color: var(--text-dark);
}
.mobile-profile-dropdown a {
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
/* --- ICON FIX 5: Target <i> AND <svg> --- */
nav.mobile-profile-dropdown a i,
nav.mobile-profile-dropdown a svg {
    font-size: 1.25rem !important; 
    width: 1.25rem !important; 
    height: 1.25rem !important;
    text-align: center;
    color: var(--accent-text);
}
.mobile-profile-dropdown a:hover {
    background: #fff7f6;
    color: var(--accent-text);
}
.mobile-profile-dropdown a.logout-link {
    color: #e53935;
}
.mobile-profile-dropdown a.logout-link:hover {
    background: #fff0f0;
}

/* =========================================
    OVERLAY & RESPONSIVE HIDING
    ========================================= */
.menu-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(0px); 
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, backdrop-filter 0.3s ease; 
    z-index: 1000;
}
.menu-overlay.active {
    opacity: 1;
    visibility: visible;
    backdrop-filter: blur(4px);
}

@media (max-width: 992px) {
    .navbar-desktop {
        display: none;
    }
}
@media (min-width: 993px) {
    .navbar-mobile-top,
    .navbar-mobile-bottom,
    .mobile-profile-dropdown,
    .menu-overlay { 
        display: none !important;
    }
    body {
        padding-bottom: 0 !important; /* Made !important */
        padding-top: 0 !important; /* Made !important */
    }
}

/* =Settings...CSS (unchanged) */
.my-swal-popup{font-family:'Poppins',sans-serif;border-radius:16px;border:1px solid #fff;box-shadow:0 10px 30px rgba(0,0,0,.08)}.my-swal-title{font-family:'Poppins',sans-serif;color:#d47474;font-weight:600;font-size:1.6rem}.my-swal-text{color:#666;font-size:1rem}.my-swal-icon{color:#d47474;border-color:#f7d4d1}.my-swal-popup .swal2-actions{gap:.75rem}.my-swal-confirm,.my-swal-cancel{font-family:'Poppins',sans-serif;font-weight:600!important;border-radius:10px!important;padding:.7rem 1.5rem!important;font-size:.95rem!important;border:none!important;transition:all .2s ease!important;box-shadow:none!important}.my-swal-confirm{background:linear-gradient(135deg,#ffe6b3,#f7d4d1)!important;color:#333!important}.my-swal-confirm:hover{opacity:.9;transform:translateY(-2px)}.my-swal-confirm:focus{box-shadow:0 0 0 3px rgba(247,212,209,.5)!important}.my-swal-cancel{background:#f1f1f1!important;color:#666!important}.my-swal-cancel:hover{background:#e7e7e7!important}.my-swal-cancel:focus{box-shadow:0 0 0 3px rgba(0,0,0,.1)!important}
</style>

<script>
(function(){
    // --- Get all menu elements ---
    var profileToggle = document.getElementById('profileToggle');       // Desktop avatar
    var profileMenu = document.getElementById('profileMenu');       // Desktop dropdown
    
    var mobileProfileToggle = document.getElementById('mobileProfileToggle'); // Mobile profile button
    var mobileProfileMenu = document.getElementById('mobileProfileMenu');   // Mobile dropdown
    
    var overlay = document.getElementById('menuOverlay');         // Universal overlay

    // --- Desktop Profile Dropdown Logic ---
    if (profileToggle && profileMenu) {
        profileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            // Close mobile menu if it's open
            if (mobileProfileMenu && mobileProfileMenu.classList.contains('open')) {
                closeMobileMenu();
            }
            // Toggle desktop menu
            var isOpen = profileMenu.classList.toggle('open');
            profileToggle.setAttribute('aria-expanded', isOpen);
            profileMenu.setAttribute('aria-hidden', !isOpen);
            overlay.classList.toggle('active', isOpen);
        });
    }

    // --- Mobile Profile Dropdown Logic ---
    if (mobileProfileToggle && mobileProfileMenu) {
        function openMobileMenu() {
            // Close desktop menu if it's open
            if (profileMenu && profileMenu.classList.contains('open')) {
                closeDesktopMenu();
            }
            mobileProfileMenu.classList.add('open');
            mobileProfileMenu.setAttribute('aria-hidden', 'false');
            mobileProfileToggle.setAttribute('aria-expanded', 'true');
            mobileProfileToggle.classList.add('active');
            overlay.classList.add('active');
        }

        function closeMobileMenu() {
            mobileProfileMenu.classList.remove('open');
            mobileProfileMenu.setAttribute('aria-hidden', 'true');
            mobileProfileToggle.setAttribute('aria-expanded', 'false');
            mobileProfileToggle.classList.remove('active');
            overlay.classList.remove('active');
        }

        mobileProfileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            if (mobileProfileMenu.classList.contains('open')) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });
    }

    // --- Universal Close Functionality ---
    function closeDesktopMenu() {
        if (profileMenu) {
            profileMenu.classList.remove('open');
            profileToggle.setAttribute('aria-expanded', 'false');
            profileMenu.setAttribute('aria-hidden', 'true');
        }
    }

    function closeAllMenus() {
        var wasOpen = false; 
        
        if (profileMenu && profileMenu.classList.contains('open')) {
            closeDesktopMenu();
            wasOpen = true;
        }
        if (mobileProfileMenu && mobileProfileMenu.classList.contains('open')) {
            if(typeof closeMobileMenu === 'function') {
                closeMobileMenu();
            }
            wasOpen = true;
        }
        
        if (wasOpen) {
            overlay.classList.remove('active');
        }
    }

    // Close menus when overlay is clicked
    if (overlay) {
        overlay.addEventListener('click', closeAllMenus);
    }

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


    // --- Smooth Page Transition (Fade-Out) ---
    function handleSmoothNavigation(event) {
        if (event.ctrlKey || event.metaKey || event.button === 1) {
            return;
        }
        event.preventDefault();
        const destinationUrl = this.href;
        document.body.classList.add('body-fading-out');
        setTimeout(() => {
            window.location.href = destinationUrl;
        }, 100); 
    }

    // Select all internal navigation links
    const selectors = [
        '.nav-left a',
        '.nav-center a',
        '.nav-icon-link', // Added cart link
        '.navbar-mobile-bottom a',
        '.profile-dropdown a',
        '.mobile-profile-dropdown a' 
    ];

    const navLinks = document.querySelectorAll(selectors.join(', '));

    navLinks.forEach(link => {
        if (link.href && !link.href.includes('javascript:void(0)')) {
            link.addEventListener('click', handleSmoothNavigation);
        }
    });

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
            // Add fade-out effect before logging out
            document.body.classList.add('body-fading-out');
            setTimeout(() => {
                window.location.href = '<?php echo $base_path; ?>/customer/logout.php';
            }, 100); // Match fade-out animation
        }
    });
}
</script>