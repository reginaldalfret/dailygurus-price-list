<?php
/**
 * DailyGurus Price List - Products Catalog Management
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();

$admin_user = get_current_admin();
$db = get_db();

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf($token)) {
        set_flash('error', 'Security token expired. Please reload.');
        header('Location: products.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $cat_id = (int)($_POST['category_id'] ?? 1);
        $subcat_id = (int)($_POST['subcategory_id'] ?? 0) ?: null;
        $default_unit = trim($_POST['default_unit'] ?? '');
        $display_order = (int)($_POST['display_order'] ?? 0);
        $active = isset($_POST['active']) ? 1 : 0;

        if (!empty($name)) {
            try {
                $stmt = $db->prepare("
                    INSERT INTO products (category_id, subcategory_id, name, default_unit, display_order, active)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$cat_id, $subcat_id, $name, $default_unit, $display_order, $active]);
                set_flash('success', "Product '{$name}' created successfully.");
            } catch (Exception $e) {
                set_flash('error', 'Error creating product: ' . $e->getMessage());
            }
        } else {
            set_flash('error', 'Product name cannot be blank.');
        }
        header('Location: products.php');
        exit;
    }

    if ($action === 'edit') {
        $id = (int)($_POST['product_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $cat_id = (int)($_POST['category_id'] ?? 1);
        $subcat_id = (int)($_POST['subcategory_id'] ?? 0) ?: null;
        $default_unit = trim($_POST['default_unit'] ?? '');
        $display_order = (int)($_POST['display_order'] ?? 0);
        $active = isset($_POST['active']) ? 1 : 0;

        if ($id > 0 && !empty($name)) {
            try {
                $stmt = $db->prepare("
                    UPDATE products 
                    SET category_id = ?, subcategory_id = ?, name = ?, default_unit = ?, display_order = ?, active = ?
                    WHERE id = ?
                ");
                $stmt->execute([$cat_id, $subcat_id, $name, $default_unit, $display_order, $active, $id]);
                set_flash('success', "Product '{$name}' updated successfully.");
            } catch (Exception $e) {
                set_flash('error', 'Error updating product: ' . $e->getMessage());
            }
        } else {
            set_flash('error', 'Invalid product data.');
        }
        header('Location: products.php');
        exit;
    }

    if ($action === 'toggle_active') {
        $id = (int)($_POST['product_id'] ?? 0);
        if ($id > 0) {
            $db->prepare("UPDATE products SET active = (1 - active) WHERE id = ?")->execute([$id]);
            set_flash('success', 'Product visibility status updated.');
        }
        header('Location: products.php');
        exit;
    }

    if ($action === 'delete') {
        $id = (int)($_POST['product_id'] ?? 0);
        if ($id > 0) {
            $db->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
            set_flash('success', 'Product deleted permanently from catalog.');
        }
        header('Location: products.php');
        exit;
    }
}

// Fetch products with category & subcategory details
$cat_filter = (int)($_GET['category_id'] ?? 0);
$subcat_filter = (int)($_GET['subcategory_id'] ?? 0);

$where = [];
$params = [];
if ($cat_filter > 0) {
    $where[] = "p.category_id = ?";
    $params[] = $cat_filter;
}
if ($subcat_filter > 0) {
    $where[] = "p.subcategory_id = ?";
    $params[] = $subcat_filter;
}

$where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$products_sql = "
    SELECT 
        p.*,
        c.name AS category_name,
        c.type AS category_type,
        c.icon AS category_icon,
        s.name AS subcategory_name,
        s.icon AS subcategory_icon,
        (SELECT COUNT(*) FROM daily_prices dp WHERE dp.product_id = p.id AND TRIM(dp.price) != '') AS total_price_records
    FROM products p
    JOIN categories c ON p.category_id = c.id
    LEFT JOIN subcategories s ON p.subcategory_id = s.id
    {$where_clause}
    ORDER BY c.display_order ASC, s.display_order ASC, p.display_order ASC, p.id ASC
";
$stmt = $db->prepare($products_sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$catalog_tree = get_categories_tree($db);

$page_title = 'Products Catalog';
$page_subtitle = 'Manage vegetables & fruits catalog, units, categories, and display order';
$current_page = 'products';

require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="card">
    <div class="card-header">
        <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
            <h3 class="card-title">
                <span>🥬</span>
                <span>Wholesale Product List (<?= count($products) ?> Items)</span>
            </h3>
            
            <!-- Category Filter -->
            <div style="display: flex; gap: 8px;">
                <a href="products.php" class="btn btn-sm <?= $cat_filter === 0 ? 'btn-primary' : 'btn-outline' ?>">All</a>
                <?php foreach ($catalog_tree as $c): ?>
                    <a href="products.php?category_id=<?= $c['id'] ?>" class="btn btn-sm <?= $cat_filter === $c['id'] ? 'btn-primary' : 'btn-outline' ?>">
                        <?= $c['icon'] ?> <?= htmlspecialchars($c['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 12px;">
            <div class="quick-filter-box">
                <span class="filter-icon">🔍</span>
                <input type="text" class="table-search-input" placeholder="Search catalog..." data-target-table="productsCatalogTable">
            </div>
            <button type="button" class="btn btn-primary btn-sm" onclick="openModal('addProductModal')">
                <span>➕ Add Product</span>
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover" id="productsCatalogTable">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Subcategory</th>
                    <th>Default Unit</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 30px; color: var(--text-muted);">
                            No products found in this category.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $idx = 1; foreach ($products as $prod): ?>
                        <tr>
                            <td style="color: var(--text-muted); font-size: 0.8rem;"><?= $idx++ ?></td>
                            <td>
                                <strong style="color: var(--text-main); font-size: 0.92rem;"><?= htmlspecialchars($prod['name']) ?></strong>
                            </td>
                            <td>
                                <span class="badge <?= $prod['category_type'] === 'fruit' ? 'badge-fruit' : 'badge-veg' ?>">
                                    <?= $prod['category_icon'] ?> <?= htmlspecialchars($prod['category_name']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($prod['subcategory_name'])): ?>
                                    <span style="font-size: 0.85rem; color: var(--secondary);">
                                        <?= $prod['subcategory_icon'] ?> <?= htmlspecialchars($prod['subcategory_name']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <code style="font-family: var(--font-mono); background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">
                                    <?= htmlspecialchars($prod['default_unit'] ?: 'N/A') ?>
                                </code>
                            </td>
                            <td><?= (int)$prod['display_order'] ?></td>
                            <td>
                                <form method="POST" action="products.php" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="action" value="toggle_active">
                                    <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                                    <button type="submit" class="btn btn-sm <?= $prod['active'] ? 'badge badge-active' : 'badge badge-inactive' ?>" style="border:none; cursor:pointer;" title="Click to toggle active status">
                                        <?= $prod['active'] ? '● Active' : '○ Inactive' ?>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <button type="button" class="btn btn-secondary btn-sm" onclick='editProduct(<?= json_encode($prod) ?>)'>
                                        <span>✏️ Edit</span>
                                    </button>
                                    <form method="POST" action="products.php" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete <?= htmlspecialchars(addslashes($prod['name'])) ?>? This will also remove its historical prices.');">
                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete product">
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

<!-- Modal: Add Product -->
<div class="modal-overlay" id="addProductModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 class="modal-title">➕ Add New Product</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('addProductModal')">&times;</button>
        </div>
        <form method="POST" action="products.php">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="add">

            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Product Name <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Tomato big crates (premium)" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Category <span class="required">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <?php foreach ($catalog_tree as $cat_opt): ?>
                                <option value="<?= $cat_opt['id'] ?>"><?= $cat_opt['icon'] ?> <?= htmlspecialchars($cat_opt['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Subcategory</label>
                        <select name="subcategory_id" class="form-select">
                            <option value="">-- General / Direct --</option>
                            <?php foreach ($catalog_tree as $cat_opt): ?>
                                <optgroup label="<?= htmlspecialchars($cat_opt['name']) ?>">
                                    <?php foreach ($cat_opt['subcategories'] as $sub_opt): ?>
                                        <option value="<?= $sub_opt['id'] ?>"><?= $sub_opt['icon'] ?> <?= htmlspecialchars($sub_opt['name']) ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Default Wholesale Unit</label>
                        <input type="text" name="default_unit" class="form-control" placeholder="e.g. kg, crate, 50 kg, box">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Display Order</label>
                        <input type="number" name="display_order" class="form-control" value="0">
                    </div>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer;">
                        <input type="checkbox" name="active" value="1" checked class="form-check-input">
                        <span>Active in Daily Price Lists</span>
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addProductModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Product -->
<div class="modal-overlay" id="editProductModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 class="modal-title">✏️ Edit Product</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('editProductModal')">&times;</button>
        </div>
        <form method="POST" action="products.php">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="product_id" id="edit_product_id" value="">

            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Product Name <span class="required">*</span></label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Category <span class="required">*</span></label>
                        <select name="category_id" id="edit_category_id" class="form-select" required>
                            <?php foreach ($catalog_tree as $cat_opt): ?>
                                <option value="<?= $cat_opt['id'] ?>"><?= $cat_opt['icon'] ?> <?= htmlspecialchars($cat_opt['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Subcategory</label>
                        <select name="subcategory_id" id="edit_subcategory_id" class="form-select">
                            <option value="">-- General / Direct --</option>
                            <?php foreach ($catalog_tree as $cat_opt): ?>
                                <optgroup label="<?= htmlspecialchars($cat_opt['name']) ?>">
                                    <?php foreach ($cat_opt['subcategories'] as $sub_opt): ?>
                                        <option value="<?= $sub_opt['id'] ?>"><?= $sub_opt['icon'] ?> <?= htmlspecialchars($sub_opt['name']) ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Default Wholesale Unit</label>
                        <input type="text" name="default_unit" id="edit_default_unit" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Display Order</label>
                        <input type="number" name="display_order" id="edit_display_order" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer;">
                        <input type="checkbox" name="active" id="edit_active" value="1" class="form-check-input">
                        <span>Active in Daily Price Lists</span>
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editProductModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function editProduct(prod) {
    document.getElementById('edit_product_id').value = prod.id;
    document.getElementById('edit_name').value = prod.name;
    document.getElementById('edit_category_id').value = prod.category_id;
    document.getElementById('edit_subcategory_id').value = prod.subcategory_id || '';
    document.getElementById('edit_default_unit').value = prod.default_unit || '';
    document.getElementById('edit_display_order').value = prod.display_order || '0';
    document.getElementById('edit_active').checked = (prod.active == 1);
    openModal('editProductModal');
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
