<?php
/**
 * DailyGurus Price List - Application Configuration
 */

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Base Application Settings
define('SITE_NAME', 'DailyGurus Price List');
define('SITE_TAGLINE', 'Daily Wholesale Price List for Vegetables & Fruits');

// Normalize script dir for cross-platform compatibility (Windows backslash fix)
$raw_script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$base_dir = preg_replace('#/admin/?$#', '', $raw_script_dir);
$base_dir = rtrim($base_dir, '/');
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
define('SITE_URL', $protocol . '://' . $host . $base_dir);

// Contact & Market Info (Easily editable here)
define('CONTACT_PHONE', '+91 12345 67890');
define('CONTACT_EMAIL', 'info@dailygurus.com');
define('CONTACT_ADDRESS', 'Koyambedu Market, Chennai, Tamil Nadu');

// Currency & Formatting
define('CURRENCY_SYMBOL', '₹');

// Database Configuration (SQLite stored securely outside web document root)
define('DB_PATH', file_exists('E:/DG_Data/database.sqlite') ? 'E:/DG_Data/database.sqlite' : __DIR__ . '/database/database.sqlite');

// Admin Default Credentials for initial seed (Admin can change anytime from Settings)
define('DEFAULT_ADMIN_USER', 'admin');
define('DEFAULT_ADMIN_PASS', 'admin123');

// Session Settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('DG_PRICELIST_SESS');
    session_start();
}
