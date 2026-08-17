<?php
/**
 * DailyGurus Price List - Database Connection & Migration
 */

require_once __DIR__ . '/config.php';

function get_db(): PDO {
    static $db = null;
    if ($db === null) {
        $db_dir = dirname(DB_PATH);
        if (!is_dir($db_dir)) {
            mkdir($db_dir, 0755, true);
        }

        $needs_init = !file_exists(DB_PATH);

        try {
            $db = new PDO('sqlite:' . DB_PATH);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $db->exec('PRAGMA foreign_keys = ON;');
        } catch (PDOException $e) {
            die('Database Connection Error: ' . htmlspecialchars($e->getMessage()));
        }

        if ($needs_init) {
            init_database($db);
        }
    }
    return $db;
}

function init_database(PDO $db): void {
    // 1. Create Tables
    $db->exec("
        CREATE TABLE IF NOT EXISTS admins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password_hash TEXT NOT NULL,
            email TEXT DEFAULT '',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT UNIQUE NOT NULL,
            type TEXT NOT NULL DEFAULT 'vegetable', -- 'vegetable' or 'fruit'
            icon TEXT DEFAULT '🥦',
            display_order INTEGER DEFAULT 0
        );

        CREATE TABLE IF NOT EXISTS subcategories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category_id INTEGER NOT NULL REFERENCES categories(id) ON DELETE CASCADE,
            name TEXT NOT NULL,
            slug TEXT NOT NULL,
            icon TEXT DEFAULT '🍅',
            display_order INTEGER DEFAULT 0
        );

        CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category_id INTEGER NOT NULL REFERENCES categories(id) ON DELETE CASCADE,
            subcategory_id INTEGER REFERENCES subcategories(id) ON DELETE SET NULL,
            name TEXT NOT NULL,
            tamil_name TEXT DEFAULT '',
            image_url TEXT DEFAULT '',
            icon TEXT DEFAULT '',
            default_unit TEXT DEFAULT '',
            display_order INTEGER DEFAULT 0,
            active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS price_dates (
            price_date TEXT PRIMARY KEY, -- 'YYYY-MM-DD'
            is_published INTEGER DEFAULT 1,
            notes TEXT DEFAULT '',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS daily_prices (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            price_date TEXT NOT NULL REFERENCES price_dates(price_date) ON DELETE CASCADE,
            product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
            price TEXT NOT NULL,
            unit TEXT DEFAULT '',
            notes TEXT DEFAULT '',
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE(price_date, product_id)
        );

        CREATE INDEX IF NOT EXISTS idx_products_cat ON products(category_id, subcategory_id);
        CREATE INDEX IF NOT EXISTS idx_daily_prices_date ON daily_prices(price_date);
        CREATE INDEX IF NOT EXISTS idx_daily_prices_prod ON daily_prices(product_id);
    ");

    // 2. Seed Default Admin
    $admin_check = $db->query("SELECT COUNT(*) FROM admins")->fetchColumn();
    if ($admin_check == 0) {
        $stmt = $db->prepare("INSERT INTO admins (username, password_hash, email) VALUES (?, ?, ?)");
        $stmt->execute([
            DEFAULT_ADMIN_USER,
            password_hash(DEFAULT_ADMIN_PASS, PASSWORD_DEFAULT),
            CONTACT_EMAIL
        ]);
    }

    // 3. Seed Initial Categories & Subcategories
    seed_catalog_and_initial_prices($db);
}

function seed_catalog_and_initial_prices(PDO $db): void {
    $db->beginTransaction();
    try {
        // Categories
        $cat_stmt = $db->prepare("INSERT OR IGNORE INTO categories (id, name, slug, type, icon, display_order) VALUES (?, ?, ?, ?, ?, ?)");
        $cat_stmt->execute([1, 'Vegetables', 'vegetables', 'vegetable', '🥦', 1]);
        $cat_stmt->execute([2, 'Fruits', 'fruits', 'fruit', '🍎', 2]);

        // Subcategories
        $subcat_stmt = $db->prepare("INSERT OR IGNORE INTO subcategories (id, category_id, name, slug, icon, display_order) VALUES (?, ?, ?, ?, ?, ?)");
        // Vegetables
        $subcat_stmt->execute([1, 1, 'Tomato', 'tomato', '🍅', 1]);
        $subcat_stmt->execute([2, 1, 'Onion', 'onion', '🧅', 2]);
        $subcat_stmt->execute([3, 1, 'Potato', 'potato', '🥔', 3]);
        $subcat_stmt->execute([4, 1, 'Other Vegetables', 'other-vegetables', '🥦', 4]);
        $subcat_stmt->execute([5, 1, 'Greens (Keerai)', 'greens-keerai', '🌿', 5]);
        $subcat_stmt->execute([6, 1, 'Garlic', 'garlic', '🧄', 6]);

        // Fruits
        $subcat_stmt->execute([7, 2, 'Banana', 'banana', '🍌', 1]);
        $subcat_stmt->execute([8, 2, 'Mango', 'mango', '🥭', 2]);
        $subcat_stmt->execute([9, 2, 'Other Fruits', 'other-fruits', '🍎', 3]);

        // Initial Date
        $initial_date = '2026-08-14';
        $db->prepare("INSERT OR IGNORE INTO price_dates (price_date, is_published, notes) VALUES (?, 1, 'Initial Wholesale Market Rates')")
           ->execute([$initial_date]);

        $prod_stmt = $db->prepare("INSERT INTO products (category_id, subcategory_id, name, default_unit, display_order, active) VALUES (?, ?, ?, ?, ?, 1)");
        $price_stmt = $db->prepare("INSERT INTO daily_prices (price_date, product_id, price, unit) VALUES (?, ?, ?, ?)");

        // Initial Products & 14/08/2026 Prices List
        $initial_items = [
            // Tomato
            [1, 1, 'Tomato big crates (premium)', 'crate', '650'],
            [1, 1, 'Tomato (local) big crates', 'crate', '550'],
            [1, 1, 'Tomato Paper-Mixed Size', 'crate', '400'],
            [1, 1, 'Tomato (hyd) - Standard (Big)', 'crate', '750'],

            // Onion
            [1, 2, 'Nashik new - Big (60+)', '50 kg', '1900/1950'],
            [1, 2, 'Onion Nashik new (50+)', '50 kg', '1850/1800'],
            [1, 2, 'Onion Nashik new (45+)', '50 kg', '1700/1750'],

            // Potato
            [1, 3, 'Potato Agra', 'kg', '17/16'],
            [1, 3, 'Potato Jothi (40 kg) red', '40 kg', '22/24'],
            [1, 3, 'Potato Hasan', 'kg', '32'],

            // Other Vegetables
            [1, 4, 'Cabbage local', 'kg', '600/500'],
            [1, 4, 'Cauliflower small', 'piece', '180'],
            [1, 4, 'Cauliflower Big', 'piece', '200/220'],
            [1, 4, 'Cucumber Indian', 'kg', '50/40'],
            [1, 4, 'Cucumber English', 'kg', '60'],
            [1, 4, 'Ladies finger', 'kg', '25'],
            [1, 4, 'Ladies finger - Tray', 'tray', '40'],
            [1, 4, 'Beans (Avarakai)', 'kg', '65'],
            [1, 4, 'Beans (Chikadi kai)', 'kg', '60'],
            [1, 4, 'Beans (Cluster)', 'kg', '40/35'],
            [1, 4, 'Beans (Cow pea)', 'kg', '60'],
            [1, 4, 'Beans (Haricot)', 'kg', '90/80'],
            [1, 4, 'Beans (Local)', 'kg', '60/55'],
            [1, 4, 'Brinjal small (Purple Stripes)', 'kg', '25/30'],
            [1, 4, 'Brinjal small (Purple Stripes box)', 'box', ''],
            [1, 4, 'Brinjal Ujjala', 'kg', '35'],
            [1, 4, 'Brinjal Ujjala box', 'box', ''],
            [1, 4, 'Capsicum (green)', 'kg', '40'],
            [1, 4, 'Chilli (bajji)', 'kg', '60/50'],
            [1, 4, 'Chilli long', 'kg', '40'],
            [1, 4, 'Chilli (V kota)', 'kg', '60/65'],
            [1, 4, 'Chilli (short)', 'kg', '50'],
            [1, 4, 'Coconut medium (500/600g)', 'piece', '60'],
            [1, 4, 'Coconut medium (400/500g)', 'piece', '60'],
            [1, 4, 'Coconut medium (250g)', 'piece', '60'],
            [1, 4, 'Ginger new', 'kg', ''],
            [1, 4, 'Ginger old', 'kg', '8400'],
            [1, 4, 'Lemon (KG)', 'kg', '110'],
            [1, 4, 'Bitter gourd (regular)', 'kg', '40/50'],
            [1, 4, 'Bitter gourd (small)', 'kg', '50/55'],
            [1, 4, 'Bottle gourd', 'kg', '30/35'],
            [1, 4, 'Baby Bottle Gourd', 'kg', '35/40'],
            [1, 4, 'Chow chow', 'kg', '30'],
            [1, 4, 'Ivy gourd', 'kg', '35/30'],
            [1, 4, 'Ridge gourd', 'kg', '40/35'],
            [1, 4, 'Snake gourd', 'kg', '35'],
            [1, 4, 'Beetroot (Yeldur)', 'kg', '40/45'],
            [1, 4, 'Beetroot (Ooty)', 'kg', '80/85'],
            [1, 4, 'Carrot Ooty', 'kg', '95'],
            [1, 4, 'Carrot (local)', 'kg', '60'],
            [1, 4, 'Knol khol', 'kg', '25/30'],
            [1, 4, 'Radish', 'kg', '500'],
            [1, 4, 'Aamla', 'kg', '100'],
            [1, 4, 'Arvi', 'kg', '38'],
            [1, 4, 'Ash gourd', 'kg', '17'],
            [1, 4, 'Green peas', 'kg', '130'],
            [1, 4, 'Green peas (Kodaikanal)', 'kg', ''],
            [1, 4, 'Onion (sambar)', 'kg', '60'],
            [1, 4, 'Onion Sambar - Premium', 'kg', '70'],
            [1, 4, 'Pumpkin (big)', 'kg', '15'],
            [1, 4, 'Raw banana (in kg)', 'kg', '50/60'],
            [1, 4, 'Mango Totapuri', 'kg', '90'],
            [1, 4, 'Sweet corn (KG)', 'kg', '25'],
            [1, 4, 'Sweet potato', 'kg', '35/40'],
            [1, 4, 'Tapioca', 'kg', '22/25'],
            [1, 4, 'Yam', 'kg', '40'],
            [1, 4, 'Cucumber sambar', 'kg', '20'],
            [1, 4, 'Brinjal barta/bottle', 'kg', '30/40'],
            [1, 4, 'Curry Leaves (Kg)', 'kg', '30/35'],
            [1, 4, 'Drumstick (Kg)', 'kg', '35'],
            [1, 4, 'Parwal', 'kg', '55'],
            [1, 4, 'Banana stem', 'kg', '60'],
            [1, 4, 'Banana flower', 'kg', '15/kg'],

            // Greens / Keerai
            [1, 5, 'Agathi Keerai Bunch (200-250g)', 'bunch', '15'],
            [1, 5, 'Arai Keerai - Bunch (250-300g)', 'bunch', '15'],
            [1, 5, 'Manatha kali Keerai Bunch (250-300g)', 'bunch', '15'],
            [1, 5, 'Mulai Keerai Bunch (250-300g)', 'bunch', '15'],
            [1, 5, 'Murungai Keerai - Bunch 1 kattu', 'bunch', '40/50'],
            [1, 5, 'Ponna ganni Keerai Bunch (250-300g)', 'bunch', '15'],
            [1, 5, 'Pulichi (Gongura) Bunch (200-250g)', 'bunch', '15'],
            [1, 5, 'Siru Keerai - Bunch (250-300g)', 'bunch', '15'],
            [1, 5, 'Palak Keerai - Bunch (250-300g)', 'bunch', '10/15'],
            [1, 5, 'Kothamalli 5 kg', '5 kg', '30/200/150 per 5 kg'],
            [1, 5, 'Pudina 5 kg', '5 kg', '30/150 per 5 kg'],

            // Garlic
            [1, 6, 'Garlic Boom', 'kg', '250'],
            [1, 6, 'Garlic Big A4', 'kg', '220'],
            [1, 6, 'Garlic MD A3', 'kg', '210'],

            // Fruits - Banana
            [2, 7, 'Banana - Karpoora Valli', 'kg', '70'],
            [2, 7, 'Banana Nendram', 'kg', '70/65'],
            [2, 7, 'Banana Pacha Bale (16 kg)', '16 kg box', '550/600 (box)'],
            [2, 7, 'Banana Pachabale Crates (15kg)', '15 kg crate', ''],
            [2, 7, 'Banana Poovan', 'kg', '45/50'],
            [2, 7, 'Banana Red (KG)', 'kg', '80/75'],
            [2, 7, 'Banana Yellaki', 'kg', '80/85'],

            // Fruits - Mango
            [2, 8, 'Mango Banganapalle', 'kg', '70/95'],
            [2, 8, 'Mango Sindhura', 'kg', ''],
            [2, 8, 'Mango Jawadhu', 'kg', ''],
            [2, 8, 'Mango Alphonso', 'kg', ''],
            [2, 8, 'Mango Imampasand', 'kg', ''],
            [2, 8, 'Mango Rumani', 'kg', '50/60'],
            [2, 8, 'Mango Neelam', 'kg', '70/80'],

            // Fruits - Other
            [2, 9, 'Orange Nagour', 'kg', 'Nill'],
            [2, 9, 'Chicco (Sapota) box (12 kgs)', '12 kg box', '600'],
            [2, 9, 'Custard Apple', '20 kg box', '1300 (20kg box)'],
            [2, 9, 'Guava', 'kg', '45/55'],
            [2, 9, 'Mosambi (Juice grade)', 'kg', '70/75'],
            [2, 9, 'Mosambi (Sweet lime)', 'kg', '80/85'],
            [2, 9, 'Musk melon (local)', 'kg', '30/35'],
            [2, 9, 'Papaya (straight)', 'kg', '40'],
            [2, 9, 'Papaya (round)', 'kg', '40'],
            [2, 9, 'Pineapple', 'kg', '62/67'],
            [2, 9, 'Watermelon (stripes)', 'kg', '10/12'],
            [2, 9, 'Watermelon Kiran', 'kg', '10/12']
        ];

        $order = 1;
        foreach ($initial_items as $item) {
            $cat_id = $item[0];
            $subcat_id = $item[1];
            $name = $item[2];
            $unit = $item[3];
            $price_val = $item[4];

            $prod_stmt->execute([$cat_id, $subcat_id, $name, $unit, $order++]);
            $prod_id = $db->lastInsertId();

            $price_stmt->execute([$initial_date, $prod_id, $price_val, $unit]);
        }

        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}
