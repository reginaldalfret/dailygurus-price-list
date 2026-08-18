<?php
/**
 * DailyGurus Price List - SQLite Data Exporter for Supabase PostgreSQL Migration
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';

$db = get_db();

$export = [
    'exported_at' => date('c'),
    'categories' => $db->query("SELECT * FROM categories ORDER BY display_order, id")->fetchAll(PDO::FETCH_ASSOC),
    'subcategories' => $db->query("SELECT * FROM subcategories ORDER BY category_id, display_order, id")->fetchAll(PDO::FETCH_ASSOC),
    'products' => $db->query("SELECT * FROM products ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC),
    'price_dates' => $db->query("SELECT * FROM price_dates ORDER BY price_date ASC")->fetchAll(PDO::FETCH_ASSOC),
    'daily_prices' => $db->query("SELECT * FROM daily_prices ORDER BY price_date ASC, product_id ASC")->fetchAll(PDO::FETCH_ASSOC),
];

$output_file = __DIR__ . '/sqlite_dump.json';
file_put_contents($output_file, json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "✓ Successfully exported SQLite data to scripts/sqlite_dump.json\n";
echo "  Categories: " . count($export['categories']) . "\n";
echo "  Subcategories: " . count($export['subcategories']) . "\n";
echo "  Products: " . count($export['products']) . "\n";
echo "  Price Dates: " . count($export['price_dates']) . "\n";
echo "  Daily Prices: " . count($export['daily_prices']) . "\n";
