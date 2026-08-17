<?php
/**
 * DailyGurus Admin - Login
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../includes/auth.php';

if (is_admin_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted_token = $_POST['csrf_token'] ?? '';
    // If CSRF token is provided or session token matched
    $csrf_ok = empty($_SESSION['csrf_token']) || verify_csrf($submitted_token);

    if ($csrf_ok) {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $db = get_db();

        if (admin_login($username, $password, $db)) {
            set_flash('success', 'Welcome back to DailyGurus Price Manager!');
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid admin username or password. Please check your credentials.';
        }
    } else {
        $error = 'Session token expired. Please reload and try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?= e(SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css?v=<?= time() ?>">
    <link rel="icon" type="image/svg+xml" href="../assets/images/logo.svg">
</head>
<body class="login-page-body">
    <div class="login-card">
        <a href="../index.php">
            <img src="../assets/images/logo.svg" alt="<?= e(SITE_NAME) ?>" class="login-logo">
        </a>
        <h1 class="login-title">Admin Management</h1>
        <p class="login-subtitle">Sign in to update wholesale prices and manage catalog</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error" style="text-align: left; margin-bottom: 16px;">
                <span>⚠ <?= e($error) ?></span>
            </div>
        <?php endif; ?>

        <?php $flash = get_flash(); if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?>" style="text-align: left; margin-bottom: 16px;">
                <span><?= e($flash['message']) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" style="text-align: left;">
            <?= csrf_input() ?>
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="admin" value="admin" required autofocus autocomplete="username">
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 8px; font-weight: 700;">
                Sign In to Dashboard &rarr;
            </button>
        </form>

        <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--admin-border); font-size: 0.82rem; color: var(--admin-muted); display: flex; justify-content: space-between; align-items: center;">
            <a href="../index.php" style="color: var(--admin-primary); text-decoration: none; font-weight: 600;">&larr; Public Website</a>
            <span style="font-size: 0.78rem; color: #94a3b8;">Default: admin / admin123</span>
        </div>
    </div>
</body>
</html>
