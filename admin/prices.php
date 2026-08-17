<?php
/**
 * DailyGurus Price List - Daily Prices Editor (Primary Daily Workflow)
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();

$admin_user = get_current_admin();
$db = get_db();

$selected_date = $_GET['date'] ?? date('Y-m-d');
// Validate date format YYYY-MM-DD
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selected_date)) {
    $selected_date = date('Y-m-d');
}

// Handle Form Submissions BEFORE any HTML output (prevents headers already sent)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf($token)) {
        set_flash('error', 'Security token expired. Please reload and submit again.');
        header('Location: prices.php?date=' . urlencode($selected_date));
        exit;
    }

    $form_action = $_POST['form_action'] ?? 'save_draft';
    $notes = trim($_POST['date_notes'] ?? '');

    // Action: Copy previous day's prices
    if ($form_action === 'copy_previous') {
        $source_date_stmt = $db->prepare("SELECT price_date FROM price_dates WHERE price_date < ? ORDER BY price_date DESC LIMIT 1");
        $source_date_stmt->execute([$selected_date]);
        $source_date = $source_date_stmt->fetchColumn();

        if ($source_date) {
            $db->beginTransaction();
            try {
                // Ensure date record exists
                $db->prepare("INSERT OR IGNORE INTO price_dates (price_date, is_published, notes) VALUES (?, 0, ?)")
                   ->execute([$selected_date, "Copied from {$source_date}"]);

                // Copy daily_prices
                $copy_sql = "
                    INSERT OR REPLACE INTO daily_prices (price_date, product_id, price, unit, notes, updated_at)
                    SELECT ?, product_id, price, unit, notes, CURRENT_TIMESTAMP
                    FROM daily_prices
                    WHERE price_date = ?
                ";
                $db->prepare($copy_sql)->execute([$selected_date, $source_date]);
                $db->commit();

                set_flash('success', "Prices successfully copied from {$source_date} to {$selected_date}. You can now edit and publish.");
            } catch (Exception $e) {
                $db->rollBack();
                set_flash('error', 'Failed to copy prices: ' . $e->getMessage());
            }
        } else {
            set_flash('warning', 'No prior date found with existing prices to copy from.');
        }

        header('Location: prices.php?date=' . urlencode($selected_date));
        exit;
    }

    // Action: Add new custom item inline
    if ($form_action === 'add_inline_item') {
        $name = trim($_POST['new_item_name'] ?? '');
        $cat_id = (int)($_POST['new_item_category_id'] ?? 1);
        $subcat_id = (int)($_POST['new_item_subcategory_id'] ?? 0) ?: null;
        $price = trim($_POST['new_item_price'] ?? '');
        $unit = trim($_POST['new_item_unit'] ?? '');

        if (!empty($name)) {
            $db->beginTransaction();
            try {
                // Insert product
                $prod_stmt = $db->prepare("INSERT INTO products (category_id, subcategory_id, name, default_unit, active) VALUES (?, ?, ?, ?, 1)");
                $prod_stmt->execute([$cat_id, $subcat_id, $name, $unit]);
                $new_prod_id = $db->lastInsertId();

                // Ensure price date exists
                $db->prepare("INSERT OR IGNORE INTO price_dates (price_date, is_published, notes) VALUES (?, 0, '')")
                   ->execute([$selected_date]);

                // Insert price
                $price_stmt = $db->prepare("INSERT OR REPLACE INTO daily_prices (price_date, product_id, price, unit) VALUES (?, ?, ?, ?)");
                $price_stmt->execute([$selected_date, $new_prod_id, $price, $unit]);

                $db->commit();
                set_flash('success', "Item '{$name}' added to catalog and priced for {$selected_date}.");
            } catch (Exception $e) {
                $db->rollBack();
                set_flash('error', 'Error adding item: ' . $e->getMessage());
            }
        } else {
            set_flash('error', 'Product name cannot be empty.');
        }

        header('Location: prices.php?date=' . urlencode($selected_date));
        exit;
    }

    // Action: Save Draft or Publish Today's Prices
    $is_published = ($form_action === 'publish') ? 1 : 0;
    $items = $_POST['items'] ?? [];

    $db->beginTransaction();
    try {
        // 1. Insert or update price_dates record
        $date_stmt = $db->prepare("
            INSERT INTO price_dates (price_date, is_published, notes, updated_at)
            VALUES (?, ?, ?, CURRENT_TIMESTAMP)
            ON CONFLICT(price_date) DO UPDATE SET
                is_published = excluded.is_published,
                notes = excluded.notes,
                updated_at = CURRENT_TIMESTAMP
        ");
        $date_stmt->execute([$selected_date, $is_published, $notes]);

        // 2. Batch update prices
        $price_upsert = $db->prepare("
            INSERT INTO daily_prices (price_date, product_id, price, unit, updated_at)
            VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
            ON CONFLICT(price_date, product_id) DO UPDATE SET
                price = excluded.price,
                unit = excluded.unit,
                updated_at = CURRENT_TIMESTAMP
        ");

        $saved_count = 0;
        foreach ($items as $prod_id => $data) {
            $price_val = trim($data['price'] ?? '');
            $unit_val = trim($data['unit'] ?? '');
            $price_upsert->execute([$selected_date, (int)$prod_id, $price_val, $unit_val]);
            if ($price_val !== '') {
                $saved_count++;
            }
        }

        $db->commit();

        $action_label = $is_published ? 'published live for customers' : 'saved as draft';
        set_flash('success', "Prices for " . format_date_display($selected_date) . " successfully {$action_label}! ({$saved_count} items priced)");
    } catch (Exception $e) {
        $db->rollBack();
        set_flash('error', 'Database error while saving prices: ' . $e->getMessage());
    }

    header('Location: prices.php?date=' . urlencode($selected_date));
    exit;
}

// Fetch price data for the selected date
$price_data = get_prices_by_date($db, $selected_date);
$meta = $price_data['meta'];
$categories = $price_data['categories'];

// Find most recent prior date for the "copy previous day" option
$prior_date_stmt = $db->prepare("SELECT price_date FROM price_dates WHERE price_date < ? ORDER BY price_date DESC LIMIT 1");
$prior_date_stmt->execute([$selected_date]);
$prior_date = $prior_date_stmt->fetchColumn();

// Tree for inline new product modal
$catalog_tree = get_categories_tree($db);

$page_title = 'Daily Wholesale Prices';
$page_subtitle = 'Enter, update, draft, and publish daily market rates';
$current_page = 'prices';

require_once __DIR__ . '/../includes/admin_header.php';
?>

<!-- Control Bar (Date selection, Copy from previous day, Quick Search) -->
<div class="price-control-bar">
    <div class="date-selector-group">
        <label for="dateSelector" style="font-weight: 700; font-size: 0.88rem; color: var(--text-main);">Select Date:</label>
        <input type="date" id="dateSelector" class="date-picker-input" value="<?= htmlspecialchars($selected_date) ?>" onchange="window.location.href='prices.php?date=' + this.value;">
        
        <?php if ($meta['exists']): ?>
            <?php if ($meta['is_published']): ?>
                <span class="badge badge-published" style="font-size: 0.82rem; padding: 6px 12px;">● Published Live</span>
            <?php else: ?>
                <span class="badge badge-draft" style="font-size: 0.82rem; padding: 6px 12px;">● Draft / Unpublished</span>
            <?php endif; ?>
        <?php else: ?>
            <span class="badge badge-draft" style="background:#fee2e2; color:#991b1b; border-color:#fecaca; font-size: 0.82rem; padding: 6px 12px;">● No Record for this Date</span>
        <?php endif; ?>
    </div>

    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
        <?php if ($prior_date): ?>
            <form method="POST" action="prices.php?date=<?= urlencode($selected_date) ?>" style="display: inline;" onsubmit="return confirm('Copy all prices from <?= htmlspecialchars($prior_date) ?> into <?= htmlspecialchars($selected_date) ?>? This will overwrite existing values for this date.');">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="form_action" value="copy_previous">
                <button type="submit" class="btn btn-secondary btn-sm" title="Pre-populate with previous day's rates">
                    <span>📋 Copy from <?= format_date_display($prior_date, 'd M') ?></span>
                </button>
            </form>
        <?php endif; ?>

        <button type="button" class="btn btn-secondary btn-sm" onclick="openModal('addItemModal')">
            <span>➕ Add New Item</span>
        </button>

        <div class="quick-filter-box">
            <input type="text" id="adminTableSearch" placeholder="Filter items in table..." onkeyup="filterAdminPriceTable(this.value)">
        </div>
    </div>
</div>

<!-- Main Price Form -->
<form method="POST" action="prices.php?date=<?= urlencode($selected_date) ?>" id="dailyPriceForm">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <input type="hidden" name="form_action" id="formActionInput" value="save_draft">

    <!-- Optional Market Day Notes Banner -->
    <div class="admin-card" style="margin-bottom: 20px;">
        <div class="card-body" style="padding: 16px 20px;">
            <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                <label for="dateNotes" style="font-weight: 700; font-size: 0.88rem; color: var(--text-main); white-space: nowrap;">Market Notes / Arrival Status:</label>
                <input type="text" 
                       id="dateNotes" 
                       name="date_notes" 
                       value="<?= htmlspecialchars((string)($meta['notes'] ?? '')) ?>" 
                       class="form-control" 
                       placeholder="e.g. Heavy morning arrivals, stable vegetable prices..." 
                       style="flex: 1; min-width: 260px;">
            </div>
        </div>
    </div>

    <!-- Categorized Price Tables -->
    <div class="price-tables-container">
        <?php if (empty($categories)): ?>
            <div class="empty-state card" style="padding: 48px; text-align: center;">
                <p style="color: var(--text-muted); font-size: 1.1rem;">No active products found in catalog.</p>
                <a href="products.php" class="btn btn-primary" style="margin-top: 12px;">Add Products to Catalog &rarr;</a>
            </div>
        <?php else: ?>
            <?php foreach ($categories as $cat): ?>
                <div class="category-header-banner" style="margin: 28px 0 14px; display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid <?= $cat['slug'] === 'fruits' ? '#f97316' : '#10b981' ?>; padding-bottom: 6px;">
                    <h2 style="font-size: 1.25rem; font-weight: 800; color: <?= $cat['slug'] === 'fruits' ? '#c2410c' : '#047857' ?>; text-transform: uppercase; letter-spacing: 0.04em;">
                        <?= $cat['slug'] === 'fruits' ? '🍎' : '🥦' ?> <?= htmlspecialchars($cat['name']) ?>
                    </h2>
                    <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">
                        <?= count($cat['subcategories']) ?> Subcategories
                    </span>
                </div>

                <?php foreach ($cat['subcategories'] as $subcat): ?>
                    <div class="admin-card subcat-table-card" data-subcat-name="<?= strtolower(htmlspecialchars($subcat['name'])) ?>">
                        <div class="card-header" style="background: #f8fafc;">
                            <h3 class="card-title" style="font-size: 0.98rem; font-weight: 700; color: var(--text-main);">
                                <span><?= htmlspecialchars($subcat['name']) ?></span>
                                <span class="badge" style="background: #e2e8f0; color: #475569; font-size: 0.72rem; margin-left: 6px;">
                                    <?= count($subcat['products']) ?> items
                                </span>
                            </h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover admin-price-table">
                                <thead>
                                    <tr>
                                        <th style="width: 45%;">Produce Name</th>
                                        <th style="width: 25%;">Price (₹)</th>
                                        <th style="width: 20%;">Unit / Package</th>
                                        <th style="width: 10%; text-align: center;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subcat['products'] as $prod): ?>
                                        <tr>
                                            <td>
                                                <strong style="color: var(--text-main);"><?= htmlspecialchars($prod['name'] ?? '') ?></strong>
                                            </td>
                                            <td>
                                                <div class="price-input-cell">
                                                    <input type="text" 
                                                           name="items[<?= $prod['id'] ?>][price]" 
                                                           value="<?= htmlspecialchars((string)($prod['price'] ?? '')) ?>" 
                                                           class="price-input" 
                                                           placeholder="e.g. 650 or 600/500" 
                                                           autocomplete="off">
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" 
                                                       name="items[<?= $prod['id'] ?>][unit]" 
                                                       value="<?= htmlspecialchars((string)($prod['unit'] ?? '')) ?>" 
                                                       class="unit-input" 
                                                       placeholder="<?= htmlspecialchars((string)($prod['default_unit'] ?? 'kg/box')) ?>">
                                            </td>
                                            <td style="text-align: center;">
                                                <?php if (!empty($prod['is_priced'])): ?>
                                                    <span class="badge badge-active" title="Price set" style="background:#dcfce7; color:#15803d;">● Set</span>
                                                <?php else: ?>
                                                    <span class="price-na" title="No price entered" style="color:#94a3b8; font-weight:700;">—</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Sticky Bottom Action Bar -->
    <div class="sticky-action-bar">
        <div class="sticky-bar-info">
            <span class="sticky-bar-title">Price Sheet for <?= format_date_display($selected_date, 'd M Y') ?></span>
            <span class="sticky-bar-stats">
                <strong id="activePricedCount"><?= (int)($meta['total_priced'] ?? 0) ?></strong> / <?= (int)($meta['total_items'] ?? 0) ?> items priced
            </span>
        </div>
        <div class="sticky-bar-actions">
            <button type="submit" onclick="document.getElementById('formActionInput').value='save_draft';" class="btn btn-secondary btn-lg" style="background: #334155; color: #fff; border: 1px solid #475569;">
                <span>💾 Save Draft</span>
            </button>
            <button type="submit" onclick="document.getElementById('formActionInput').value='publish';" class="btn btn-primary btn-lg" style="background: #10b981; border-color: #10b981; font-weight: 700;">
                <span>🚀 Publish Today's Prices</span>
            </button>
        </div>
    </div>
</form>

<!-- Modal: Add New Item Inline -->
<div class="admin-modal" id="addItemModal" style="display: none;">
    <div class="modal-backdrop" onclick="closeModal('addItemModal')"></div>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">➕ Add New Item to Catalog & Price</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('addItemModal')">&times;</button>
            </div>
            <form method="POST" action="prices.php?date=<?= urlencode($selected_date) ?>">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="form_action" value="add_inline_item">

                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="newItemName">Product Name *</label>
                        <input type="text" id="newItemName" name="new_item_name" class="form-control" placeholder="e.g. Baby Corn (Fresh)" required>
                    </div>

                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        <div class="form-group">
                            <label class="form-label" for="newItemCategory">Category *</label>
                            <select id="newItemCategory" name="new_item_category_id" class="form-control" onchange="updateSubcatOptions(this.value)">
                                <?php foreach ($catalog_tree as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="newItemSubcategory">Subcategory</label>
                            <select id="newItemSubcategory" name="new_item_subcategory_id" class="form-control">
                                <!-- Populated by JS -->
                            </select>
                        </div>
                    </div>

                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        <div class="form-group">
                            <label class="form-label" for="newItemPrice">Price for <?= format_date_display($selected_date, 'd M') ?></label>
                            <input type="text" id="newItemPrice" name="new_item_price" class="form-control" placeholder="e.g. 60 or 50/60">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="newItemUnit">Unit / Packaging</label>
                            <input type="text" id="newItemUnit" name="new_item_unit" class="form-control" placeholder="e.g. kg, box, crate">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addItemModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save & Add to Sheet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const catalogTreeData = <?= json_encode($catalog_tree) ?>;

function updateSubcatOptions(selectedCatId) {
    const subcatSelect = document.getElementById('newItemSubcategory');
    if (!subcatSelect) return;
    subcatSelect.innerHTML = '<option value="0">-- General / Default --</option>';

    const category = catalogTreeData.find(c => c.id == selectedCatId);
    if (category && category.subcategories) {
        category.subcategories.forEach(sub => {
            const opt = document.createElement('option');
            opt.value = sub.id;
            opt.textContent = sub.name;
            subcatSelect.appendChild(opt);
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const catSelect = document.getElementById('newItemCategory');
    if (catSelect) {
        updateSubcatOptions(catSelect.value);
    }
});

function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.style.display = 'block';
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.style.display = 'none';
}

function filterAdminPriceTable(keyword) {
    keyword = keyword.toLowerCase().trim();
    const rows = document.querySelectorAll('.admin-price-table tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(keyword) ? '' : 'none';
    });
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
