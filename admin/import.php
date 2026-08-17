<?php
/**
 * DailyGurus Price List - Bulk Quick Import & Parser
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();

$admin_user = get_current_admin();
$db = get_db();

$selected_date = $_GET['date'] ?? date('Y-m-d');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selected_date)) {
    $selected_date = date('Y-m-d');
}

// Handle Form Submission (Save imported prices)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf($token)) {
        set_flash('error', 'Security token expired. Please retry.');
        header('Location: import.php?date=' . urlencode($selected_date));
        exit;
    }

    $target_date = $_POST['import_date'] ?? $selected_date;
    $is_published = ($_POST['publish_status'] ?? '1') === '1' ? 1 : 0;
    $notes = trim($_POST['import_notes'] ?? 'Bulk imported from market list');
    $import_items = $_POST['import_items'] ?? [];

    if (!empty($import_items)) {
        $db->beginTransaction();
        try {
            // Ensure price_dates record
            $date_stmt = $db->prepare("
                INSERT INTO price_dates (price_date, is_published, notes, updated_at)
                VALUES (?, ?, ?, CURRENT_TIMESTAMP)
                ON CONFLICT(price_date) DO UPDATE SET
                    is_published = excluded.is_published,
                    notes = excluded.notes,
                    updated_at = CURRENT_TIMESTAMP
            ");
            $date_stmt->execute([$target_date, $is_published, $notes]);

            $price_stmt = $db->prepare("
                INSERT INTO daily_prices (price_date, product_id, price, unit, updated_at)
                VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
                ON CONFLICT(price_date, product_id) DO UPDATE SET
                    price = excluded.price,
                    unit = excluded.unit,
                    updated_at = CURRENT_TIMESTAMP
            ");

            $new_prod_stmt = $db->prepare("
                INSERT INTO products (category_id, subcategory_id, name, default_unit, active)
                VALUES (1, 4, ?, ?, 1)
            ");

            $imported_count = 0;

            foreach ($import_items as $item) {
                // Check if user checked this item
                if (empty($item['include'])) {
                    continue;
                }

                $prod_id = (int)($item['product_id'] ?? 0);
                $raw_name = trim($item['raw_name'] ?? '');
                $price = trim($item['price'] ?? '');
                $unit = trim($item['unit'] ?? '');

                if ($price === '') {
                    continue;
                }

                // If product doesn't exist yet, create it
                if ($prod_id <= 0 && $raw_name !== '') {
                    $new_prod_stmt->execute([$raw_name, $unit]);
                    $prod_id = (int)$db->lastInsertId();
                }

                if ($prod_id > 0) {
                    $price_stmt->execute([$target_date, $prod_id, $price, $unit]);
                    $imported_count++;
                }
            }

            $db->commit();
            $status_str = $is_published ? 'published live' : 'saved as draft';
            set_flash('success', "Successfully imported and {$status_str} {$imported_count} prices for " . format_date_display($target_date) . "!");
            header('Location: prices.php?date=' . urlencode($target_date));
            exit;
        } catch (Exception $e) {
            $db->rollBack();
            set_flash('error', 'Import error: ' . $e->getMessage());
        }
    } else {
        set_flash('warning', 'No prices were found in the submitted form.');
    }
}

// Fetch all active products for the client-side fuzzy parser catalog
$catalog_stmt = $db->query("
    SELECT p.id, p.name, p.default_unit, c.name AS category_name, c.type AS category_type
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE p.active = 1
    ORDER BY c.display_order ASC, p.display_order ASC
");
$catalog = $catalog_stmt->fetchAll(PDO::FETCH_ASSOC);

// Sample wholesale template text
$sample_text = "Tomato big crates: 650\nTomato local big crates: 550\nCabbage local: 600/500\nCauliflower small: 180\nNashik new - Big (60+): 1900/1950\nPotato Agra: 17/16\nGarlic Big A4: 220\nBanana Nendram: 70/65\nCustard Apple: 1300 (20kg box)";

$page_title = 'Bulk Quick Import';
$page_subtitle = 'Paste WhatsApp/Text market list, auto-match against catalog, and publish with 1 click';
$current_page = 'import';

require_once __DIR__ . '/../includes/admin_header.php';
?>

<!-- Import Form -->
<form method="POST" action="import.php?date=<?= urlencode($selected_date) ?>" id="bulkImportForm">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <!-- Top Settings Bar -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-body" style="padding: 16px 20px;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                    <div>
                        <label for="importDateInput" class="form-label" style="margin-bottom: 2px;">Target Price Date:</label>
                        <input type="date" name="import_date" id="importDateInput" class="form-control" value="<?= htmlspecialchars($selected_date) ?>" style="font-weight: 600;">
                    </div>
                    <div>
                        <label for="publishStatusSelect" class="form-label" style="margin-bottom: 2px;">Publish Mode:</label>
                        <select name="publish_status" id="publishStatusSelect" class="form-select">
                            <option value="1" selected>🚀 Publish Live Instantly</option>
                            <option value="0">💾 Save as Draft (Offline)</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('rawBulkInput').value = `<?= addslashes($sample_text) ?>`; window.parser.parseText();">
                        <span>Load Sample Market Text</span>
                    </button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('rawBulkInput').value = ''; window.parser.parseText();">
                        <span>Clear</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Split Grid: Left = Raw Text, Right = Live Matched Preview -->
    <div class="import-grid">
        <!-- Left: Paste Area -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <span>📋</span>
                    <span>Paste Daily Price List</span>
                </h3>
            </div>
            <div class="card-body">
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 12px;">
                    Paste items in common market formats such as <code>Item Name: Price</code> or <code>Item Name - Price</code>.
                </p>
                <textarea id="rawBulkInput" class="bulk-textarea" placeholder="Paste your text here... e.g.&#10;Tomato big crates: 650&#10;Tomato local big crates: 550&#10;Cabbage local: 600/500&#10;Cauliflower small: 180"></textarea>
                <div id="parserSummaryStats"></div>
            </div>
        </div>

        <!-- Right: Real-Time Matched Preview -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <span>⚡</span>
                    <span>Live Match Preview</span>
                </h3>
                <div style="display: flex; gap: 8px;">
                    <button type="button" class="btn btn-outline btn-sm" onclick="toggleSelectAllImport(true)">Check All</button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="toggleSelectAllImport(false)">Uncheck All</button>
                </div>
            </div>
            <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                <table class="table table-hover">
                    <thead style="position: sticky; top: 0; background: #f8fafc; z-index: 10;">
                        <tr>
                            <th style="width: 40px;">Inc.</th>
                            <th>Pasted Item</th>
                            <th>Matched Catalog Product</th>
                            <th>Price (₹)</th>
                            <th>Unit</th>
                        </tr>
                    </thead>
                    <tbody id="previewTableBody">
                        <tr>
                            <td colspan="5" class="text-center" style="padding: 32px; color: var(--text-muted);">
                                <em>Paste wholesale list lines on the left to see live preview matching...</em>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card-footer" style="padding: 16px 20px; background: #f8fafc; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary btn-lg" style="background: #10b981; border-color: #10b981; font-weight: 700;">
                    <span>🚀 Confirm & Import Prices</span>
                </button>
            </div>
        </div>
    </div>
</form>

<script>
const catalogData = <?= json_encode($catalog) ?>;
document.addEventListener('DOMContentLoaded', () => {
    window.parser = new BulkPriceParser(
        catalogData, 
        'rawBulkInput', 
        'previewTableBody', 
        'parserSummaryStats'
    );
});

function toggleSelectAllImport(checked) {
    document.querySelectorAll('#previewTableBody input[type="checkbox"]').forEach(cb => {
        cb.checked = checked;
    });
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
