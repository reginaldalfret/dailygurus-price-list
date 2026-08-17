<?php
/**
 * DailyGurus Admin - Logout
 */

require_once __DIR__ . '/../includes/auth.php';

admin_logout();
set_flash('success', 'You have been successfully logged out.');
header('Location: login.php');
exit;
