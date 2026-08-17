<?php
/**
 * DailyGurus Price List - Header Component
 */
if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/../config.php';
}
if (!function_exists('e')) {
    require_once __DIR__ . '/functions.php';
}

$current_page = basename($_SERVER['PHP_SELF'] ?? 'index.php');
$page_title = $page_title ?? (SITE_NAME . ' - ' . SITE_TAGLINE);
$page_description = $page_description ?? 'Daily wholesale market prices for fresh vegetables and fruits in Chennai Koyambedu Mandi. Updated daily with transparent rates.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?></title>
    <meta name="description" content="<?= e($page_description) ?>">
    <meta name="keywords" content="DailyGurus, Koyambedu market price, wholesale vegetable price Chennai, wholesale fruit price, daily vegetable rates Tamil Nadu, mandi price list">
    <meta name="author" content="DailyGurus">
    
    <!-- Open Graph / Social Meta -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($page_title) ?>">
    <meta property="og:description" content="<?= e($page_description) ?>">
    <meta property="og:image" content="<?= e(SITE_URL) ?>/assets/images/hero-produce.jpg">
    <meta property="og:url" content="<?= e(SITE_URL) ?>/<?= e($current_page) ?>">
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Core Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?: time() ?>">
    
    <!-- Favicon (SVG) -->
    <link rel="icon" type="image/svg+xml" href="assets/images/logo.svg">
</head>
<body>
    <!-- Top Announcement Bar -->
    <div class="topbar">
        <div class="container topbar-container">
            <div class="topbar-left">
                <span class="topbar-badge">📍 Koyambedu Wholesale Market</span>
                <span class="topbar-text">Chennai's Official Daily Mandi Rates</span>
            </div>
            <div class="topbar-right">
                <a href="tel:<?= e(str_replace(' ', '', CONTACT_PHONE)) ?>" class="topbar-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    <?= e(CONTACT_PHONE) ?>
                </a>
                <span class="topbar-divider">|</span>
                <a href="admin/login.php" class="topbar-link topbar-admin-link">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    Merchant Login
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="site-header" id="siteHeader">
        <div class="container header-container">
            <!-- Brand Logo -->
            <a href="index.php" class="header-logo" aria-label="DailyGurus Price List Home">
                <img src="assets/images/logo.svg" alt="DailyGurus Price List" width="220" height="48" class="logo-img">
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="desktop-nav" aria-label="Main Navigation">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php#vegetables" class="nav-link">Price List</a>
                    </li>
                    <li class="nav-item">
                        <a href="history.php" class="nav-link <?= $current_page === 'history.php' ? 'active' : '' ?>">History</a>
                    </li>
                    <li class="nav-item">
                        <a href="about.php" class="nav-link <?= $current_page === 'about.php' ? 'active' : '' ?>">About Us</a>
                    </li>
                </ul>
            </nav>

            <!-- Header Action Button -->
            <div class="header-actions">
                <a href="contact.php" class="btn-contact-pill <?= $current_page === 'contact.php' ? 'active' : '' ?>">
                    Contact Us
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                
                <!-- Mobile Hamburger Button -->
                <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Open mobile navigation menu" aria-expanded="false" aria-controls="mobileNavDrawer">
                    <span class="hamburger-bar"></span>
                    <span class="hamburger-bar"></span>
                    <span class="hamburger-bar"></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Slide-out Drawer & Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>
    <aside class="mobile-nav-drawer" id="mobileNavDrawer" aria-hidden="true">
        <div class="drawer-header">
            <a href="index.php" class="drawer-logo">
                <img src="assets/images/logo.svg" alt="DailyGurus Logo" width="180" height="40">
            </a>
            <button class="drawer-close-btn" id="drawerCloseBtn" aria-label="Close navigation menu">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <nav class="mobile-nav-menu">
            <ul>
                <li>
                    <a href="index.php" class="mobile-nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>">
                        <span class="nav-icon">🏠</span> Home
                    </a>
                </li>
                <li>
                    <a href="index.php#vegetables" class="mobile-nav-link">
                        <span class="nav-icon">🥦</span> Vegetables Price List
                    </a>
                </li>
                <li>
                    <a href="index.php#fruits" class="mobile-nav-link">
                        <span class="nav-icon">🍎</span> Fruits Price List
                    </a>
                </li>
                <li>
                    <a href="history.php" class="mobile-nav-link <?= $current_page === 'history.php' ? 'active' : '' ?>">
                        <span class="nav-icon">📅</span> Historical Rates
                    </a>
                </li>
                <li>
                    <a href="about.php" class="mobile-nav-link <?= $current_page === 'about.php' ? 'active' : '' ?>">
                        <span class="nav-icon">ℹ️</span> About Us
                    </a>
                </li>
                <li>
                    <a href="contact.php" class="mobile-nav-link <?= $current_page === 'contact.php' ? 'active' : '' ?>">
                        <span class="nav-icon">📞</span> Contact Us
                    </a>
                </li>
            </ul>
        </nav>
        <div class="drawer-footer">
            <div class="drawer-market-status">
                <span class="status-indicator-dot"></span>
                <span>Market Open: 4:00 AM - 12:00 PM</span>
            </div>
            <a href="admin/login.php" class="drawer-admin-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                Merchant / Admin Login
            </a>
        </div>
    </aside>

    <main id="mainContent">
