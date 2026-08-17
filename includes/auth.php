<?php
/**
 * DailyGurus Price List - Admin Authentication Guard
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../functions.php';

function is_admin_logged_in(): bool {
    return !empty($_SESSION['admin_logged_in']) && !empty($_SESSION['admin_user_id']);
}

function require_admin(): void {
    if (!is_admin_logged_in()) {
        $login_url = 'login.php';
        header('Location: ' . $login_url);
        exit;
    }
}

function admin_login(string $username, string $password, PDO $db): bool {
    $username = trim($username);
    $password = trim($password);
    
    if ($username === '' || $password === '') {
        return false;
    }

    // Case-insensitive username lookup
    $stmt = $db->prepare("SELECT * FROM admins WHERE LOWER(TRIM(username)) = LOWER(?) LIMIT 1");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        if (!headers_sent() && session_status() === PHP_SESSION_ACTIVE) {
            @session_regenerate_id(true);
        }
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_email'] = $admin['email'] ?? '';
        return true;
    }

    return false;
}

function admin_logout(): void {
    $_SESSION['admin_logged_in'] = false;
    unset($_SESSION['admin_logged_in'], $_SESSION['admin_user_id'], $_SESSION['admin_username'], $_SESSION['admin_email']);
    if (!headers_sent() && session_status() === PHP_SESSION_ACTIVE) {
        @session_regenerate_id(true);
    }
}

function get_current_admin(): ?array {
    if (!is_admin_logged_in()) {
        return null;
    }
    return [
        'id' => $_SESSION['admin_user_id'],
        'username' => $_SESSION['admin_username'],
        'email' => $_SESSION['admin_email'] ?? ''
    ];
}
