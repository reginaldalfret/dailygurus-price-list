<?php
/**
 * DailyGurus Price List - Admin Dashboard
 */

$page_title = 'Dashboard Overview';
$page_subtitle = 'Daily wholesale pricing operations & market summary';
$current_page = 'dashboard';

require_once __DIR__ . '/../includes/admin_header.php';

// Fetch key dashboard statistics
$today_date = date('Y-m-d');
$total_products = (int)$db->query("SELECT COUNT(*) FROM products WHERE active = 1")->fetchColumn();
$veg_products = (int)$db->query("SELECT COUNT(*) FROM products p JOIN categories c ON p.category_id = c.id WHERE c.slug = 'vegetables' AND p.active = 1")->fetchColumn();
$fruit_products = (int)$db->query("SELECT COUNT(*) FROM products p JOIN categories c ON p.category_id = c.id WHERE c.slug = 'fruits' AND p.active = 1")->fetchColumn();

// Latest price date & status
$latest_date_stmt = $db->query("SELECT price_date, is_published, updated_at FROM price_dates ORDER BY price_date DESC LIMIT 1");
$latest_record = $latest_date_stmt->fetch(PDO::FETCH_ASSOC);

$latest_date = $latest_record['price_date'] ?? null;
$is_published = $latest_record ? (int)$latest_record['is_published'] : 0;

// Count how many items priced for latest date
$priced_count = 0;
if ($latest_date) {
    $priced_stmt = $db->prepare("SELECT COUNT(*) FROM daily_prices WHERE price_date = ? AND TRIM(price) != ''");
    $priced_stmt->execute([$latest_date]);
    $priced_count = (int)$priced_stmt->fetchColumn();
}

// Check today's price status
$today_stmt = $db->prepare("SELECT * FROM price_dates WHERE price_date = ?");
$today_stmt->execute([$today_date]);
$today_record = $today_stmt->fetch(PDO::FETCH_ASSOC);

// Recent price dates history (last 5 entries)
$recent_dates_sql = "
    SELECT 
        pd.price_date,
        pd.is_published,
        pd.notes,
        pd.updated_at,
        COUNT(dp.id) AS total_items,
        SUM(CASE WHEN TRIM(dp.price) != '' THEN 1 ELSE 0 END) AS priced_items
    FROM price_dates pd
    LEFT JOIN daily_prices dp ON pd.price_date = dp.price_date
    GROUP BY pd.price_date
    ORDER BY pd.price_date DESC
    LIMIT 6
";
$recent_dates = $db->query($recent_dates_sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Metrics Grid -->
<div class="metrics-grid">
    <div class="stat-card">
        <div class="stat-icon-wrapper stat-icon-green">
            <span>📅</span>
        </div>
        <div class="stat-content">
            <span class="stat-label">Today's Date</span>
            <span class="stat-value"><?= date('d M Y') ?></span>
            <span class="stat-sub">
                <?php if ($today_record): ?>
                    <?= $today_record['is_published'] ? '<span class="badge badge-published">● Today Published</span>' : '<span class="badge badge-draft">● Today Draft</span>' ?>
                <?php else: ?>
                    <span class="badge badge-draft" style="background:#fee2e2; color:#991b1b; border-color:#fecaca;">⚠️ Today Not Created</span>
                <?php endif; ?>
            </span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrapper stat-icon-blue">
            <span>📦</span>
        </div>
        <div class="stat-content">
            <span class="stat-label">Active Catalog</span>
            <span class="stat-value"><?= format_inr($total_products) ?> <small style="font-size:0.85rem; font-weight:500; color:var(--text-muted);">Items</small></span>
            <span class="stat-sub"><?= $veg_products ?> Vegetables • <?= $fruit_products ?> Fruits</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrapper stat-icon-amber">
            <span>🏷️</span>
        </div>
        <div class="stat-content">
            <span class="stat-label">Latest Price List</span>
            <span class="stat-value"><?= $latest_date ? format_date_display($latest_date, 'd M Y') : 'None' ?></span>
            <span class="stat-sub">
                <?= $priced_count ?> / <?= $total_products ?> items priced
            </span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrapper stat-icon-purple">
            <span>🚀</span>
        </div>
        <div class="stat-content">
            <span class="stat-label">Publish Status</span>
            <span class="stat-value">
                <?php if ($latest_record && $is_published): ?>
                    <span style="color: var(--success); font-size:1.15rem;">Live Online</span>
                <?php else: ?>
                    <span style="color: var(--warning); font-size:1.15rem;">Draft / Offline</span>
                <?php endif; ?>
            </span>
            <span class="stat-sub">
                Latest date: <?= $latest_date ?? 'N/A' ?>
            </span>
        </div>
    </div>
</div>

<!-- Quick Action Banner -->
<div class="card" style="background: linear-gradient(135deg, #064e3b 0%, #0f172a 100%); color: #fff; border: none; margin-bottom: 28px;">
    <div class="card-body" style="padding: 28px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
            <div>
                <h2 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 6px; color: #fff;">Daily Wholesale Price Workflow</h2>
                <p style="color: #cbd5e1; font-size: 0.9rem; max-width: 600px;">
                    Update, publish, or copy previous market rates for Koyambedu wholesale market in seconds.
                </p>
            </div>
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="prices.php?date=<?= $today_date ?>" class="btn btn-primary" style="background: #10b981; border-color: #10b981; font-weight: 700;">
                    <span>📝 Update Today's Prices</span>
                </a>
                <a href="import.php?date=<?= $today_date ?>" class="btn btn-outline" style="color: #fff; border-color: rgba(255,255,255,0.3); background: rgba(255,255,255,0.1);">
                    <span>⚡ Bulk Paste Import</span>
                </a>
                <a href="products.php" class="btn btn-outline" style="color: #fff; border-color: rgba(255,255,255,0.3); background: rgba(255,255,255,0.1);">
                    <span>🥬 Manage Products</span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row" style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- Recent Price Dates History Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <span>🕒</span>
                <span>Recent Price Updates</span>
            </h3>
            <a href="history.php" class="btn btn-outline btn-sm">View All History →</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Priced Items</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_dates)): ?>
                        <tr>
                            <td colspan="5" class="text-center" style="padding: 30px; color: var(--text-muted);">
                                No price records found. Click <strong>Update Today's Prices</strong> to create your first price list.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recent_dates as $row): ?>
                            <tr>
                                <td>
                                    <strong><?= format_date_display($row['price_date']) ?></strong>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?= htmlspecialchars($row['price_date']) ?></div>
                                </td>
                                <td>
                                    <?php if ($row['is_published']): ?>
                                        <span class="badge badge-published">● Published</span>
                                    <?php else: ?>
                                        <span class="badge badge-draft">● Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="font-weight: 600;"><?= (int)$row['priced_items'] ?></span>
                                    <span style="color: var(--text-muted); font-size: 0.8rem;">/ <?= (int)$row['total_items'] ?></span>
                                </td>
                                <td style="font-size: 0.8rem; color: var(--text-muted);">
                                    <?= date('d M, h:i A', strtotime($row['updated_at'] ?? $row['price_date'])) ?>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="prices.php?date=<?= urlencode($row['price_date']) ?>" class="btn btn-primary btn-sm">
                                            <span>Edit Prices</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Shortcuts & System Overview -->
    <div class="side-column">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <span>⚡</span>
                    <span>Quick Actions</span>
                </h3>
            </div>
            <div class="card-body" style="display: flex; flex-direction: column; gap: 12px;">
                <a href="prices.php?date=<?= $today_date ?>" class="btn btn-primary" style="justify-content: flex-start;">
                    <span>📝</span>
                    <span>Edit Today's Price Sheet</span>
                </a>
                <a href="import.php" class="btn btn-secondary" style="justify-content: flex-start;">
                    <span>📋</span>
                    <span>Paste Bulk WhatsApp / Text List</span>
                </a>
                <a href="products.php?action=add" class="btn btn-secondary" style="justify-content: flex-start;">
                    <span>➕</span>
                    <span>Add New Vegetable / Fruit</span>
                </a>
                <a href="history.php" class="btn btn-secondary" style="justify-content: flex-start;">
                    <span>📅</span>
                    <span>Browse All Dates Archive</span>
                </a>
                <a href="<?= SITE_URL ?>/index.php" target="_blank" class="btn btn-outline" style="justify-content: flex-start; margin-top: 6px;">
                    <span>🌐</span>
                    <span>Preview Public Customer View ↗</span>
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <span>ℹ️</span>
                    <span>Market Info</span>
                </h3>
            </div>
            <div class="card-body" style="font-size: 0.84rem;">
                <p><strong>Primary Market:</strong> <?= CONTACT_ADDRESS ?></p>
                <p style="margin-top: 6px;"><strong>Contact Phone:</strong> <?= CONTACT_PHONE ?></p>
                <p style="margin-top: 6px;"><strong>Database:</strong> SQLite (Local Fast Engine)</p>
                <div style="margin-top: 14px; padding: 10px; background: #f8fafc; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                    💡 <em>Tip: Publish changes whenever daily arrivals complete to update customer price lists instantly.</em>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
