<?php
/**
 * DailyGurus Price List - Price History & Date Archive
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();

$admin_user = get_current_admin();
$db = get_db();

// Handle Actions (Toggle publish, Duplicate, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf($token)) {
        set_flash('error', 'Security token expired. Please reload.');
        header('Location: history.php');
        exit;
    }

    $action = $_POST['action'] ?? '';
    $date = $_POST['price_date'] ?? '';

    if ($action === 'toggle_publish' && $date) {
        $db->prepare("UPDATE price_dates SET is_published = (1 - is_published), updated_at = CURRENT_TIMESTAMP WHERE price_date = ?")
           ->execute([$date]);
        set_flash('success', "Publish status updated for " . format_date_display($date) . ".");
        header('Location: history.php');
        exit;
    }

    if ($action === 'duplicate' && $date) {
        $target_date = $_POST['target_date'] ?? '';
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $target_date)) {
            $db->beginTransaction();
            try {
                $db->prepare("
                    INSERT INTO price_dates (price_date, is_published, notes, updated_at)
                    VALUES (?, 0, ?, CURRENT_TIMESTAMP)
                    ON CONFLICT(price_date) DO UPDATE SET updated_at = CURRENT_TIMESTAMP
                ")->execute([$target_date, "Duplicated from {$date}"]);

                $db->prepare("
                    INSERT OR REPLACE INTO daily_prices (price_date, product_id, price, unit, notes, updated_at)
                    SELECT ?, product_id, price, unit, notes, CURRENT_TIMESTAMP
                    FROM daily_prices
                    WHERE price_date = ?
                ")->execute([$target_date, $date]);

                $db->commit();
                set_flash('success', "Prices from " . format_date_display($date) . " duplicated to " . format_date_display($target_date) . " as Draft.");
                header('Location: prices.php?date=' . urlencode($target_date));
                exit;
            } catch (Exception $e) {
                $db->rollBack();
                set_flash('error', 'Duplication error: ' . $e->getMessage());
            }
        } else {
            set_flash('error', 'Please enter a valid target date (YYYY-MM-DD).');
        }
        header('Location: history.php');
        exit;
    }

    if ($action === 'delete' && $date) {
        try {
            $db->prepare("DELETE FROM price_dates WHERE price_date = ?")->execute([$date]);
            set_flash('success', "Price records for " . format_date_display($date) . " permanently deleted.");
        } catch (Exception $e) {
            set_flash('error', 'Delete error: ' . $e->getMessage());
        }
        header('Location: history.php');
        exit;
    }
}

// Fetch all price dates with aggregated statistics
$history_sql = "
    SELECT 
        pd.price_date,
        pd.is_published,
        pd.notes,
        pd.created_at,
        pd.updated_at,
        COUNT(dp.id) AS total_items,
        SUM(CASE WHEN TRIM(dp.price) != '' THEN 1 ELSE 0 END) AS priced_items
    FROM price_dates pd
    LEFT JOIN daily_prices dp ON pd.price_date = dp.price_date
    GROUP BY pd.price_date
    ORDER BY pd.price_date DESC
";
$history_records = $db->query($history_sql)->fetchAll(PDO::FETCH_ASSOC);
$total_catalog = (int)$db->query("SELECT COUNT(*) FROM products WHERE active = 1")->fetchColumn();

$page_title = 'Price History & Archive';
$page_subtitle = 'View past price dates, toggle publish status, duplicate dates, or edit prices';
$current_page = 'history';

require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <span>📅</span>
            <span>Recorded Price Dates (<?= count($history_records) ?> Days)</span>
        </h3>

        <div style="display: flex; gap: 12px; align-items: center;">
            <div class="quick-filter-box">
                <span class="filter-icon">🔍</span>
                <input type="text" class="table-search-input" placeholder="Search date..." data-target-table="historyTable">
            </div>
            <a href="prices.php?date=<?= date('Y-m-d') ?>" class="btn btn-primary btn-sm">
                <span>➕ Create Today's Price Sheet</span>
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover" id="historyTable">
            <thead>
                <tr>
                    <th>Price Date</th>
                    <th>Status</th>
                    <th>Priced Items</th>
                    <th>Notes / Market Memo</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($history_records)): ?>
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 36px; color: var(--text-muted);">
                            No price records recorded in history yet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($history_records as $rec): ?>
                        <tr>
                            <td>
                                <strong style="font-size: 0.95rem; color: var(--text-main);"><?= format_date_display($rec['price_date']) ?></strong>
                                <div style="font-size: 0.76rem; color: var(--text-muted); font-family: var(--font-mono);">
                                    <?= htmlspecialchars($rec['price_date']) ?>
                                </div>
                            </td>
                            <td>
                                <form method="POST" action="history.php" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="action" value="toggle_publish">
                                    <input type="hidden" name="price_date" value="<?= htmlspecialchars($rec['price_date']) ?>">
                                    <button type="submit" class="btn btn-sm <?= $rec['is_published'] ? 'badge badge-published' : 'badge badge-draft' ?>" style="border:none; cursor:pointer;" title="Click to toggle publish status">
                                        <?= $rec['is_published'] ? '● Published' : '○ Draft' ?>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <strong style="color: <?= (int)$rec['priced_items'] > 0 ? 'var(--primary-dark)' : 'var(--danger)' ?>;">
                                    <?= (int)$rec['priced_items'] ?>
                                </strong>
                                <span style="font-size: 0.8rem; color: var(--text-muted);">/ <?= $total_catalog ?> active</span>
                            </td>
                            <td>
                                <span style="font-size: 0.82rem; color: var(--text-muted);">
                                    <?= htmlspecialchars($rec['notes'] ?: '—') ?>
                                </span>
                            </td>
                            <td style="font-size: 0.8rem; color: var(--text-muted);">
                                <?= date('d M Y, h:i A', strtotime($rec['updated_at'] ?? $rec['price_date'])) ?>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="prices.php?date=<?= urlencode($rec['price_date']) ?>" class="btn btn-primary btn-sm">
                                        <span>✏️ Edit</span>
                                    </a>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="openDuplicateModal('<?= htmlspecialchars($rec['price_date']) ?>')">
                                        <span>📋 Duplicate</span>
                                    </button>
                                    <form method="POST" action="history.php" style="display: inline;" onsubmit="return confirm('Permanently delete all price records for <?= htmlspecialchars($rec['price_date']) ?>?');">
                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="price_date" value="<?= htmlspecialchars($rec['price_date']) ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete price date">
                                            <span>🗑️</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Duplicate Date -->
<div class="modal-overlay" id="duplicateDateModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 class="modal-title">📋 Duplicate Price Sheet</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('duplicateDateModal')">&times;</button>
        </div>
        <form method="POST" action="history.php">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="duplicate">
            <input type="hidden" name="price_date" id="dup_source_date" value="">

            <div class="modal-body">
                <p style="font-size: 0.9rem; margin-bottom: 16px;">
                    Clone all price entries from source date <strong id="dup_source_label"></strong> to a target new date.
                </p>

                <div class="form-group">
                    <label class="form-label">Target New Date <span class="required">*</span></label>
                    <input type="date" name="target_date" id="dup_target_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('duplicateDateModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Duplicate & Open Editor</button>
            </div>
        </form>
    </div>
</div>

<script>
function openDuplicateModal(sourceDate) {
    document.getElementById('dup_source_date').value = sourceDate;
    document.getElementById('dup_source_label').textContent = sourceDate;
    openModal('duplicateDateModal');
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
