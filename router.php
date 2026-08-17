<?php
/**
 * DailyGurus Price List - Secure Server Router
 * Blocks direct access to database files, protected directories, and hidden files.
 */

$raw_uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = urldecode($raw_uri);

// 1. Strict Security Blocklist: Protect database, includes, logs, config, and sensitive extensions
if (preg_match('#^/(?:database|includes|\.git|\.cloudflared)/|\.(?:sqlite|sqlite3|db|sql|log|bak|ini|env|yml|yaml)$#i', $uri)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    echo "403 Forbidden: Direct access to protected resources is strictly prohibited.";
    exit;
}

// 2. Allow static public assets (css, js, images, svgs, fonts)
$filePath = __DIR__ . $uri;
if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    // If it's a php file, execute it
    if (substr($filePath, -4) === '.php') {
        require $filePath;
        exit;
    }
    return false; // Let PHP built-in server handle MIME type and serve static asset
}

// 3. Handle directory index
if (is_dir($filePath) && file_exists($filePath . '/index.php')) {
    require $filePath . '/index.php';
    exit;
}

// 4. Handle clean URLs or fall back to main index
if (file_exists($filePath . '.php')) {
    require $filePath . '.php';
    exit;
}

// 5. Default route
require __DIR__ . '/index.php';
