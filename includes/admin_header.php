<?php
/**
 * DailyGurus Price List - Admin Header
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/auth.php';

require_admin();

$admin_user = get_current_admin();
$db = get_db();
$current_page = $current_page ?? basename($_SERVER['PHP_SELF'], '.php');
$page_title = $page_title ?? 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> | <?= SITE_NAME ?> Admin</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Admin CSS -->
    <link rel="stylesheet" href="../assets/css/admin.css?v=<?= time() ?>">
</head>
<body class="admin-body">
<div class="admin-layout" id="adminLayout">
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <a href="<?= SITE_URL ?>/admin/dashboard.php" class="brand-link">
                <span class="brand-icon">🌱</span>
                <div class="brand-text">
                    <span class="brand-title">DailyGurus</span>
                    <span class="brand-badge">ADMIN</span>
                </div>
            </a>
            <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Close Sidebar">&times;</button>
        </div>

        <div class="sidebar-user-pill">
            <div class="user-avatar"><?= strtoupper(substr($admin_user['username'] ?? 'A', 0, 1)) ?></div>
            <div class="user-info">
                <span class="user-name"><?= htmlspecialchars($admin_user['username'] ?? 'Admin') ?></span>
                <span class="user-role">Wholesale Manager</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">MAIN NAVIGATION</div>
            <a href="<?= SITE_URL ?>/admin/dashboard.php" class="nav-item <?= $current_page === 'dashboard' ? 'active' : '' ?>">
                <span class="nav-icon">📊</span>
                <span class="nav-label">Dashboard</span>
            </a>
            <a href="<?= SITE_URL ?>/admin/prices.php" class="nav-item <?= $current_page === 'prices' ? 'active' : '' ?>">
                <span class="nav-icon">📝</span>
                <span class="nav-label">Daily Prices</span>
                <span class="nav-chip">Primary</span>
            </a>
            <a href="<?= SITE_URL ?>/admin/import.php" class="nav-item <?= $current_page === 'import' ? 'active' : '' ?>">
                <span class="nav-icon">⚡</span>
                <span class="nav-label">Bulk Quick Import</span>
            </a>
            <a href="<?= SITE_URL ?>/admin/products.php" class="nav-item <?= $current_page === 'products' ? 'active' : '' ?>">
                <span class="nav-icon">🥬</span>
                <span class="nav-label">Products Catalog</span>
            </a>
            <a href="<?= SITE_URL ?>/admin/history.php" class="nav-item <?= $current_page === 'history' ? 'active' : '' ?>">
                <span class="nav-icon">📅</span>
                <span class="nav-label">Price History</span>
            </a>

            <div class="nav-section-label">SYSTEM</div>
            <a href="<?= SITE_URL ?>/admin/settings.php" class="nav-item <?= $current_page === 'settings' ? 'active' : '' ?>">
                <span class="nav-icon">⚙️</span>
                <span class="nav-label">Settings & Security</span>
            </a>
            <a href="<?= SITE_URL ?>/index.php" target="_blank" class="nav-item nav-item-external">
                <span class="nav-icon">🌐</span>
                <span class="nav-label">View Live Price List</span>
                <span class="nav-ext-icon">↗</span>
            </a>
            <a href="<?= SITE_URL ?>/admin/logout.php" class="nav-item nav-item-logout" onclick="return confirm('Are you sure you want to log out?');">
                <span class="nav-icon">🚪</span>
                <span class="nav-label">Sign Out</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-version">DailyGurus v2.0 • Koyambedu</div>
        </div>
    </aside>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Main Content Area -->
    <div class="admin-main">
        <!-- Top Navbar -->
        <header class="admin-topbar">
            <div class="topbar-left">
                <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" aria-label="Toggle Sidebar">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <div class="page-headline">
                    <h1 class="page-title"><?= htmlspecialchars($page_title) ?></h1>
                    <?php if (!empty($page_subtitle)): ?>
                        <span class="page-subtitle"><?= htmlspecialchars($page_subtitle) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="topbar-right">
                <div class="topbar-market-clock">
                    <span class="clock-icon">🕒</span>
                    <span id="liveMarketClock"><?= date('d M Y, h:i A') ?> IST</span>
                </div>
                <a href="<?= SITE_URL ?>/index.php" target="_blank" class="btn btn-outline btn-sm topbar-live-btn">
                    <span>Public Price List</span>
                    <span class="icon-external">↗</span>
                </a>
                <div class="topbar-user">
                    <span class="user-greeting">Hi, <strong><?= htmlspecialchars($admin_user['username'] ?? 'Admin') ?></strong></span>
                    <a href="<?= SITE_URL ?>/admin/logout.php" class="topbar-logout-link" title="Log Out" onclick="return confirm('Sign out of Admin Panel?');">🚪</a>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="admin-content-wrapper">
            <?= render_alerts() ?>
