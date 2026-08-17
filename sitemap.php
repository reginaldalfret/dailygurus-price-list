<?php
/**
 * DailyGurus Price List - Dynamic XML Sitemap
 */

require_once __DIR__ . '/includes/functions.php';

header("Content-Type: application/xml; charset=utf-8");

$base_url = rtrim(SITE_URL, '/');
$dates = get_all_published_dates();

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Main Public Pages -->
    <url>
        <loc><?= e($base_url) ?>/</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?= e($base_url) ?>/history.php</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= e($base_url) ?>/about.php</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <url>
        <loc><?= e($base_url) ?>/contact.php</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>

    <!-- Historical Daily Price List URLs -->
    <?php foreach ($dates as $d): ?>
        <url>
            <loc><?= e($base_url) ?>/index.php?date=<?= e($d['price_date']) ?></loc>
            <lastmod><?= date('Y-m-d', strtotime($d['updated_at'] ?? $d['price_date'])) ?></lastmod>
            <changefreq>never</changefreq>
            <priority>0.7</priority>
        </url>
    <?php endforeach; ?>
</urlset>
