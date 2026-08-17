<?php
/**
 * DailyGurus Admin - Common Header
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();

$admin_user = get_current_admin();
$admin_page = basename($_SERVER['PHP_SELF'] ?? 'dashboard.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($admin_title ?? 'Admin Dashboard') ?> | <?= e(SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css?v=<?= filemtime(__DIR__ . '/../assets/css/admin.css') ?>">
    <link rel="icon" type="image/svg+xml" href="../assets/images/logo.svg">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar" id="admin-sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php">
                    <img src="../assets/images/logo-white.svg" alt="<?= e(SITE_NAME) ?>" class="sidebar-logo">
                </a>
            </div>

            <div class="sidebar-nav">
                <div class="nav-category-title">Price Management</div>
                <ul class="admin-nav-list">
                    <li>
                        <a href="dashboard.php" class="admin-nav-link <?= ($admin_page === 'dashboard.php') ? 'active' : '' ?>">
                            <span>📊</span> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="prices.php" class="admin-nav-link <?= ($admin_page === 'prices.php') ? 'active' : '' ?>">
                            <span>💰</span> Daily Prices
                        </a>
                    </li>
                    <li>
                        <a href="import.php" class="admin-nav-link <?= ($admin_page === 'import.php') ? 'active' : '' ?>">
                            <span>📋</span> Bulk Import
                        </a>
                    </li>
                    <li>
                        <a href="history.php" class="admin-nav-link <?= ($admin_page === 'history.php') ? 'active' : '' ?>">
                            <span>📜</span> Price History
                        </a>
                    </li>
                </ul>

                <div class="nav-category-title" style="margin-top: 14px;">Catalog & Setup</div>
                <ul class="admin-nav-list">
                    <li>
                        <a href="products.php" class="admin-nav-link <?= ($admin_page === 'products.php') ? 'active' : '' ?>">
                            <span>📦</span> Products Catalog
                        </a>
                    </li>
                    <li>
                        <a href="settings.php" class="admin-nav-link <?= ($admin_page === 'settings.php') ? 'active' : '' ?>">
                            <span>⚙️</span> Settings & Password
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sidebar-footer">
                <span style="font-size: 0.82rem; color: #64748B;">👤 <?= e($admin_user['username']) ?></span>
                <a href="logout.php" style="color: #EF4444; font-size: 0.85rem; font-weight: 600; text-decoration: none;">Logout &rarr;</a>
            </div>
        </aside>

        <!-- Main Admin Content Area -->
        <div class="admin-main">
            <header class="admin-topbar">
                <div class="topbar-left">
                    <button type="button" class="sidebar-toggle-btn" id="sidebar-toggle" aria-label="Toggle Sidebar">&#9776;</button>
                    <h2 class="topbar-title"><?= e($admin_title ?? 'Dashboard') ?></h2>
                </div>
                <div class="topbar-right">
                    <a href="../index.php" target="_blank" class="btn-view-site">
                        <span>🌐</span> View Public Site &rarr;
                    </a>
                </div>
            </header>

            <main class="admin-content">
                <?php $flash = get_flash(); if ($flash): ?>
                    <div class="alert alert-<?= e($flash['type']) ?>">
                        <span><?= e($flash['message']) ?></span>
                    </div>
                <?php endif; ?>
