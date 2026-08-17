<?php
/**
 * DailyGurus Price List - Core Functions & Helpers
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';

/**
 * Safely escape string for HTML output
 */
function e($str): string {
    return htmlspecialchars((string)($str ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Format Indian number with proper commas (e.g. 1950 -> 1,950, 100000 -> 1,00,000)
 */
function format_inr_num($num): string {
    $num = trim((string)$num);
    if (!is_numeric($num)) {
        return $num;
    }
    
    $parts = explode('.', $num);
    $int_part = $parts[0];
    $dec_part = isset($parts[1]) ? '.' . $parts[1] : '';

    $len = strlen($int_part);
    if ($len <= 3) {
        return $int_part . $dec_part;
    }

    $last3 = substr($int_part, -3);
    $remaining = substr($int_part, 0, $len - 3);
    
    $formatted_rem = '';
    while (strlen($remaining) > 2) {
        $formatted_rem = ',' . substr($remaining, -2) . $formatted_rem;
        $remaining = substr($remaining, 0, strlen($remaining) - 2);
    }
    $formatted_rem = $remaining . $formatted_rem;

    return $formatted_rem . ',' . $last3 . $dec_part;
}

function format_inr($number): string {
    return format_inr_num($number);
}

/**
 * Format wholesale price representation
 * Preserves dual prices (1900/1950), Nill, blank, and unit notes without erroneous currency prefixes on weights
 */
function format_price_display(?string $raw_price, ?string $unit = ''): string {
    if ($raw_price === null) {
        return '<span class="price-na">—</span>';
    }
    
    $trimmed = trim($raw_price);
    
    if ($trimmed === '' || $trimmed === '-' || $trimmed === '—' || strtolower($trimmed) === '[blank]') {
        return '<span class="price-na">—</span>';
    }
    
    if (strcasecmp($trimmed, 'nill') === 0 || strcasecmp($trimmed, 'nil') === 0) {
        return '<span class="price-nill">Nill</span>';
    }
    
    if (strcasecmp($trimmed, 'not available') === 0 || strcasecmp($trimmed, 'na') === 0) {
        return '<span class="price-na">Not Available</span>';
    }

    // Check for trailing notes such as "(20kg box)" or "per 5 kg" or "/kg"
    $note = '';
    if (preg_match('/(\s*(?:\(.*?\)|per\s+.*|\/kg|\/piece|\/bunch|\/box|\/tray))$/i', $trimmed, $m)) {
        $raw_matched_note = $m[1];
        $price_part = substr($trimmed, 0, -strlen($raw_matched_note));
        
        $clean_note = trim($raw_matched_note);
        if (substr($clean_note, 0, 1) === '/') {
            $note = ' <span class="price-slash">/</span> ' . ltrim($clean_note, '/');
        } else {
            $note = ' ' . $clean_note;
        }
    } else {
        $price_part = $trimmed;
    }

    // Replace price numbers with INR currency symbol
    $formatted_price = preg_replace_callback('/(\d+(?:\.\d+)?)/', function($matches) {
        return CURRENCY_SYMBOL . format_inr_num($matches[1]);
    }, $price_part);

    // Standardize spacing around slashes for clean readability (e.g. ₹1,900 / ₹1,950)
    $formatted_price = preg_replace('/\s*\/\s*/', ' <span class="price-slash">/</span> ', $formatted_price);

    return htmlspecialchars_decode(trim($formatted_price) . $note, ENT_QUOTES);
}

/**
 * Alias for format_price_html
 */
function format_price_html(?string $price, ?string $unit = ''): string {
    return format_price_display($price, $unit);
}

/**
 * Format Date to readable format (e.g. 2026-08-14 -> "14 August 2026")
 */
function format_date_display(?string $date_str): string {
    if (empty($date_str)) {
        return date('d F Y');
    }
    $timestamp = strtotime($date_str);
    if (!$timestamp) {
        return $date_str;
    }
    return date('d F Y', $timestamp);
}

function format_date_long(string $date_str): string {
    return format_date_display($date_str);
}

function format_date_short(string $date_str): string {
    if (empty($date_str)) return '';
    $timestamp = strtotime($date_str);
    return $timestamp ? date('d M Y', $timestamp) : $date_str;
}

/**
 * Check if given date is today
 */
function is_date_today(string $date_str): bool {
    return $date_str === date('Y-m-d');
}

/**
 * Get latest published price date
 */
function get_latest_price_date(): ?string {
    $db = get_db();
    $stmt = $db->query("SELECT price_date FROM price_dates WHERE is_published = 1 ORDER BY price_date DESC LIMIT 1");
    $date = $stmt->fetchColumn();
    if ($date) {
        return $date;
    }
    $stmt = $db->query("SELECT price_date FROM price_dates ORDER BY price_date DESC LIMIT 1");
    $date = $stmt->fetchColumn();
    return $date ?: '2026-08-14';
}

function get_latest_published_date(): ?string {
    return get_latest_price_date();
}

/**
 * Get all published price dates with counts
 */
function get_all_published_dates(): array {
    $db = get_db();
    $stmt = $db->query("
        SELECT pd.price_date, pd.notes, pd.updated_at, COUNT(dp.id) as item_count
        FROM price_dates pd
        LEFT JOIN daily_prices dp ON pd.price_date = dp.price_date
        WHERE pd.is_published = 1
        GROUP BY pd.price_date
        ORDER BY pd.price_date DESC
    ");
    return $stmt->fetchAll();
}

/**
 * Get all recorded price dates for admin
 */
function get_all_price_dates(PDO $db): array {
    $sql = "
        SELECT 
            pd.price_date,
            pd.is_published,
            pd.notes,
            pd.updated_at,
            COUNT(dp.id) AS total_items,
            COUNT(CASE WHEN dp.price != '' AND dp.price != '-' AND LOWER(dp.price) != 'nill' THEN 1 END) as priced_items
        FROM price_dates pd
        LEFT JOIN daily_prices dp ON pd.price_date = dp.price_date
        GROUP BY pd.price_date
        ORDER BY pd.price_date DESC
    ";
    return $db->query($sql)->fetchAll();
}

/**
 * Check if a specific date is published
 */
function is_date_published(string $date): bool {
    $db = get_db();
    $stmt = $db->prepare("SELECT COUNT(*) FROM price_dates WHERE price_date = ? AND is_published = 1");
    $stmt->execute([$date]);
    return (int)$stmt->fetchColumn() > 0;
}

/**
 * Get full price date record
 */
function get_price_date_info(string $date): ?array {
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM price_dates WHERE price_date = ? LIMIT 1");
    $stmt->execute([$date]);
    $result = $stmt->fetch();
    return $result ?: null;
}

/**
 * Get all products and prices structured by category and subcategory for a specific date (used by admin prices.php)
 */
function get_prices_by_date(PDO $db, string $date): array {
    // 1. Meta for the date
    $meta_stmt = $db->prepare("SELECT * FROM price_dates WHERE price_date = ? LIMIT 1");
    $meta_stmt->execute([$date]);
    $meta_record = $meta_stmt->fetch();

    $meta = [
        'price_date' => $date,
        'exists' => (bool)$meta_record,
        'is_published' => $meta_record ? (int)$meta_record['is_published'] : 0,
        'notes' => $meta_record['notes'] ?? '',
        'updated_at' => $meta_record['updated_at'] ?? ''
    ];

    // 2. Fetch categories
    $cat_stmt = $db->query("SELECT * FROM categories ORDER BY display_order ASC, id ASC");
    $categories_raw = $cat_stmt->fetchAll();

    $categories = [];
    $total_products_count = 0;
    $priced_products_count = 0;

    foreach ($categories_raw as $cat) {
        $cat_id = $cat['id'];
        
        $subcat_stmt = $db->prepare("SELECT * FROM subcategories WHERE category_id = ? ORDER BY display_order ASC, id ASC");
        $subcat_stmt->execute([$cat_id]);
        $subcats_raw = $subcat_stmt->fetchAll();

        $subcategories = [];

        // Direct products (no subcat)
        $direct_stmt = $db->prepare("
            SELECT p.id as product_id, p.id, p.name, p.default_unit, p.display_order, p.active,
                   dp.price, dp.unit as price_unit, dp.notes as price_notes
            FROM products p
            LEFT JOIN daily_prices dp ON p.id = dp.product_id AND dp.price_date = ?
            WHERE p.category_id = ? AND (p.subcategory_id IS NULL OR p.subcategory_id = 0) AND p.active = 1
            ORDER BY p.display_order ASC, p.id ASC
        ");
        $direct_stmt->execute([$date, $cat_id]);
        $direct_products = $direct_stmt->fetchAll();

        if (!empty($direct_products)) {
            foreach ($direct_products as $p) {
                $total_products_count++;
                if (!empty($p['price']) && $p['price'] !== '-' && strtolower($p['price']) !== 'nill') {
                    $priced_products_count++;
                }
            }
            $subcategories[] = [
                'id' => 0,
                'name' => 'General ' . $cat['name'],
                'slug' => 'general-' . $cat['slug'],
                'icon' => $cat['icon'],
                'products' => $direct_products
            ];
        }

        foreach ($subcats_raw as $subcat) {
            $subcat_id = $subcat['id'];
            $prod_stmt = $db->prepare("
                SELECT p.id as product_id, p.id, p.name, p.default_unit, p.display_order, p.active,
                       dp.price, dp.unit as price_unit, dp.notes as price_notes
                FROM products p
                LEFT JOIN daily_prices dp ON p.id = dp.product_id AND dp.price_date = ?
                WHERE p.subcategory_id = ? AND p.active = 1
                ORDER BY p.display_order ASC, p.id ASC
            ");
            $prod_stmt->execute([$date, $subcat_id]);
            $products = $prod_stmt->fetchAll();

            foreach ($products as $p) {
                $total_products_count++;
                if (!empty($p['price']) && $p['price'] !== '-' && strtolower($p['price']) !== 'nill') {
                    $priced_products_count++;
                }
            }

            $subcategories[] = [
                'id' => $subcat['id'],
                'name' => $subcat['name'],
                'slug' => $subcat['slug'],
                'icon' => $subcat['icon'],
                'products' => $products
            ];
        }

        $categories[] = [
            'id' => $cat['id'],
            'name' => $cat['name'],
            'slug' => $cat['slug'],
            'type' => $cat['type'],
            'icon' => $cat['icon'],
            'subcategories' => $subcategories
        ];
    }

    return [
        'meta' => $meta,
        'categories' => $categories,
        'stats' => [
            'total' => $total_products_count,
            'priced' => $priced_products_count
        ]
    ];
}

/**
 * Get categorized prices (used by public index/history)
 */
function get_categorized_prices(string $date): array {
    $db = get_db();
    $data = get_prices_by_date($db, $date);
    $result = [];
    foreach ($data['categories'] as $cat) {
        $result[$cat['slug']] = $cat;
    }
    return $result;
}

/**
 * Count total products and categorized items for stats
 */
function get_price_stats(array $categorized_data): array {
    $veg_count = 0;
    $fruit_count = 0;
    $total_count = 0;

    if (isset($categorized_data['vegetables']['subcategories'])) {
        foreach ($categorized_data['vegetables']['subcategories'] as $sub) {
            $veg_count += count($sub['products'] ?? []);
        }
    }

    if (isset($categorized_data['fruits']['subcategories'])) {
        foreach ($categorized_data['fruits']['subcategories'] as $sub) {
            $fruit_count += count($sub['products'] ?? []);
        }
    }

    $total_count = $veg_count + $fruit_count;

    return [
        'veg_count' => $veg_count,
        'fruit_count' => $fruit_count,
        'total_count' => $total_count
    ];
}

/**
 * Get category & subcategory tree for modal selectors
 */
function get_categories_tree(PDO $db): array {
    $cat_stmt = $db->query("SELECT * FROM categories ORDER BY display_order ASC, id ASC");
    $categories = $cat_stmt->fetchAll();

    $tree = [];
    foreach ($categories as $cat) {
        $sub_stmt = $db->prepare("SELECT * FROM subcategories WHERE category_id = ? ORDER BY display_order ASC, id ASC");
        $sub_stmt->execute([$cat['id']]);
        $cat['subcategories'] = $sub_stmt->fetchAll();
        $tree[] = $cat;
    }
    return $tree;
}

/**
 * Sanitize string input
 */
function sanitize(?string $input): string {
    return trim(strip_tags((string)$input));
}

/**
 * CSRF Protection Helpers
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}

function csrf_input(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Flash Messaging Helpers
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}

function get_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Render Flash Alert Banner
 */
function render_alerts(): string {
    $flash = get_flash();
    if (!$flash) {
        return '';
    }
    $type = e($flash['type'] ?? 'info');
    $msg = e($flash['message'] ?? '');
    $alert_class = ($type === 'success') ? 'alert-success' : (($type === 'error' || $type === 'danger') ? 'alert-error' : ($type === 'warning' ? 'alert-warning' : 'alert-info'));
    $icon = ($type === 'success') ? '✓' : (($type === 'error' || $type === 'danger') ? '⚠' : 'ℹ');
    return '<div class="alert ' . $alert_class . '"><span class="alert-icon">' . $icon . '</span> <span class="alert-msg">' . $msg . '</span></div>';
}

