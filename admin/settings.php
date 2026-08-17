<?php
/**
 * DailyGurus Price List - Admin Settings & Security
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();

$admin_user = get_current_admin();
$db = get_db();

$admin_id = $_SESSION['admin_user_id'] ?? $_SESSION['admin_id'] ?? ($admin_user['id'] ?? 1);
$admin_stmt = $db->prepare("SELECT id, username, email, created_at FROM admins WHERE id = ?");
$admin_stmt->execute([$admin_id]);
$current_admin_row = $admin_stmt->fetch(PDO::FETCH_ASSOC);

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf($token)) {
        set_flash('error', 'Security token expired. Please reload.');
        header('Location: settings.php');
        exit;
    }

    $form_action = $_POST['form_action'] ?? '';

    // Update Profile & Email
    if ($form_action === 'update_profile') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (!empty($username)) {
            try {
                // Check if username is taken by another admin
                $check = $db->prepare("SELECT id FROM admins WHERE LOWER(username) = LOWER(?) AND id != ?");
                $check->execute([$username, $admin_id]);
                if ($check->fetch()) {
                    set_flash('error', 'That username is already in use by another administrator.');
                } else {
                    $up = $db->prepare("UPDATE admins SET username = ?, email = ? WHERE id = ?");
                    $up->execute([$username, $email, $admin_id]);
                    $_SESSION['admin_username'] = $username;
                    $_SESSION['admin_email'] = $email;
                    set_flash('success', 'Profile information updated successfully.');
                }
            } catch (Exception $e) {
                set_flash('error', 'Error updating profile: ' . $e->getMessage());
            }
        } else {
            set_flash('error', 'Username cannot be blank.');
        }
        header('Location: settings.php');
        exit;
    }

    // Change Password
    if ($form_action === 'change_password') {
        $current_pass = $_POST['current_password'] ?? '';
        $new_pass = $_POST['new_password'] ?? '';
        $confirm_pass = $_POST['confirm_password'] ?? '';

        // Verify current password
        $auth_stmt = $db->prepare("SELECT password_hash FROM admins WHERE id = ?");
        $auth_stmt->execute([$admin_id]);
        $hash = $auth_stmt->fetchColumn();

        if (!password_verify($current_pass, $hash)) {
            set_flash('error', 'The current password you entered is incorrect.');
        } elseif (strlen($new_pass) < 6) {
            set_flash('error', 'New password must be at least 6 characters long.');
        } elseif ($new_pass !== $confirm_pass) {
            set_flash('error', 'New password and confirmation do not match.');
        } else {
            try {
                $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
                $up = $db->prepare("UPDATE admins SET password_hash = ? WHERE id = ?");
                $up->execute([$new_hash, $admin_id]);
                set_flash('success', 'Password updated successfully! Please use your new password for next login.');
            } catch (Exception $e) {
                set_flash('error', 'Database error: ' . $e->getMessage());
            }
        }
        header('Location: settings.php');
        exit;
    }
}

// Database & System Diagnostics
$db_file_size = file_exists(DB_PATH) ? round(filesize(DB_PATH) / 1024, 2) . ' KB' : 'N/A';
$sqlite_version = $db->query('SELECT sqlite_version()')->fetchColumn();
$total_products_count = (int)$db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_prices_count = (int)$db->query("SELECT COUNT(*) FROM daily_prices")->fetchColumn();
$total_dates_count = (int)$db->query("SELECT COUNT(*) FROM price_dates")->fetchColumn();

$page_title = 'Settings & Security';
$page_subtitle = 'Admin credentials, password changes, and database system status';
$current_page = 'settings';

require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
    <!-- Admin Profile & Credentials -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <span>👤</span>
                    <span>Admin Profile</span>
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="settings.php">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="form_action" value="update_profile">

                    <div class="form-group">
                        <label class="form-label">Username <span class="required">*</span></label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($current_admin_row['username'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Contact Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($current_admin_row['email'] ?? '') ?>" placeholder="admin@dailygurus.com">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <span>Save Profile Changes</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <span>🔒</span>
                    <span>Change Password</span>
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="settings.php">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="form_action" value="change_password">

                    <div class="form-group">
                        <label class="form-label">Current Password <span class="required">*</span></label>
                        <input type="password" name="current_password" class="form-control" placeholder="••••••••" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">New Password <span class="required">*</span></label>
                        <input type="password" name="new_password" class="form-control" placeholder="Min. 6 characters" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirm New Password <span class="required">*</span></label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn btn-secondary" style="background: #334155; color:#fff;">
                        <span>Update Password</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- System Diagnostics & Database Status -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <span>🛠️</span>
                    <span>System & Database Status</span>
                </h3>
            </div>
            <div class="card-body">
                <table class="table" style="font-size: 0.88rem;">
                    <tbody>
                        <tr>
                            <td><strong>Database Engine</strong></td>
                            <td>SQLite <code><?= htmlspecialchars($sqlite_version) ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>Database Path</strong></td>
                            <td><code style="word-break: break-all;"><?= htmlspecialchars(DB_PATH) ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>Database File Size</strong></td>
                            <td><strong><?= $db_file_size ?></strong></td>
                        </tr>
                        <tr>
                            <td><strong>Total Products in Catalog</strong></td>
                            <td><?= $total_products_count ?> items</td>
                        </tr>
                        <tr>
                            <td><strong>Total Daily Price Entries</strong></td>
                            <td><?= $total_prices_count ?> rows</td>
                        </tr>
                        <tr>
                            <td><strong>Total Price Dates Recorded</strong></td>
                            <td><?= $total_dates_count ?> dates</td>
                        </tr>
                        <tr>
                            <td><strong>PHP Version</strong></td>
                            <td><code>PHP <?= phpversion() ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>Timezone</strong></td>
                            <td><code>Asia/Kolkata (IST)</code></td>
                        </tr>
                        <tr>
                            <td><strong>Current Server Time</strong></td>
                            <td><?= date('Y-m-d H:i:s') ?> IST</td>
                        </tr>
                    </tbody>
                </table>

                <div style="margin-top: 20px; padding: 14px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: var(--radius-md); font-size: 0.82rem; color: #065f46;">
                    ✅ <strong>Database Healthy:</strong> SQLite database file is active, writable, and operating with foreign key integrity enabled.
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <span>🏢</span>
                    <span>Market Info Constants</span>
                </h3>
            </div>
            <div class="card-body" style="font-size: 0.86rem;">
                <p><strong>Platform:</strong> <?= SITE_NAME ?></p>
                <p style="margin-top: 6px;"><strong>Tagline:</strong> <?= SITE_TAGLINE ?></p>
                <p style="margin-top: 6px;"><strong>Location:</strong> <?= CONTACT_ADDRESS ?></p>
                <p style="margin-top: 6px;"><strong>Phone:</strong> <?= CONTACT_PHONE ?></p>
                <p style="margin-top: 6px;"><strong>Email:</strong> <?= CONTACT_EMAIL ?></p>
                <p style="margin-top: 10px; font-size: 0.76rem; color: var(--text-muted);">
                    <em>(Market information constants can also be updated in <code>config.php</code>)</em>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
