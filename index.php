<?php
/**
 * DailyGurus Price List - Main Homepage
 * Wholesale Vegetable & Fruit Market Rates (Koyambedu, Chennai)
 */

require_once __DIR__ . '/includes/functions.php';

// Determine active price date
$requested_date = isset($_GET['date']) ? trim($_GET['date']) : '';
$latest_date = get_latest_price_date();

if ($requested_date && preg_match('/^\d{4}-\d{2}-\d{2}$/', $requested_date) && is_date_published($requested_date)) {
    $active_date = $requested_date;
} else {
    $active_date = $latest_date ?: date('Y-m-d');
}

$is_historical_view = ($latest_date && $active_date !== $latest_date);
$date_info = get_price_date_info($active_date);
$categorized_data = get_categorized_prices($active_date);
$stats = get_price_stats($categorized_data);

// SEO Meta
$page_title = 'DailyGurus Price List - Daily Wholesale Price List for Vegetables & Fruits';
$page_description = 'Check today\'s verified wholesale market prices for vegetables and fruits at Koyambedu Mandi, Chennai. Updated daily with transparent bulk rates.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-main-title">dailygurus price list</h1>
            <p class="hero-subtitle">Daily Wholesale Price List for Vegetables &amp; Fruits</p>
            
            <!-- Mobile & Desktop Date Card -->
            <div class="hero-date-card">
                <div class="date-card-eyebrow">TODAY'S WHOLESALE PRICES</div>
                <div class="date-card-main">
                    <span class="cal-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </span>
                    <span class="date-card-text"><?= e(format_date_long($active_date)) ?></span>
                    <?php if (!$is_historical_view): ?>
                        <span class="pulse-indicator">
                            <span class="pulse-dot"></span>
                            Updated Today
                        </span>
                    <?php else: ?>
                        <span class="pulse-indicator pulse-archive">
                            Archive View
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($is_historical_view): ?>
            <div class="historical-alert-bar">
                <div class="historical-alert-text">
                    ⚠️ You are viewing historical market prices for <strong><?= e(format_date_short($active_date)) ?></strong>.
                </div>
                <a href="index.php" class="historical-alert-btn">
                    View Today's Live Rates &rarr;
                </a>
            </div>
        <?php endif; ?>

        <!-- Hero Produce Banner (Hidden on compact mobile) -->
        <div class="hero-banner-card">
            <img src="assets/images/hero-produce.jpg" alt="Fresh Farm Produce Wholesale Basket" width="1000" height="428" loading="eager">
        </div>
    </div>
</section>

<!-- Category Quick Cards Section (2 Equal Columns on Mobile) -->
<section class="quick-cards-section">
    <div class="container">
        <div class="quick-cards-grid">
            <!-- Vegetables Quick Card -->
            <a href="#vegetables" class="quick-card card-veg">
                <div class="quick-card-img">
                    <img src="assets/images/veg-crate.jpg" alt="Vegetables Wholesale Prices" width="96" height="96" loading="lazy">
                </div>
                <div class="quick-card-body">
                    <div class="quick-card-badge">🥬 VEGETABLES</div>
                    <h3>Vegetables Wholesale</h3>
                    <p><?= $stats['veg_count'] ?> varieties &bull; Mandi Rates</p>
                    <span class="quick-card-btn">
                        View Prices <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </span>
                </div>
            </a>

            <!-- Fruits Quick Card -->
            <a href="#fruits" class="quick-card card-fruit">
                <div class="quick-card-img">
                    <img src="assets/images/fruit-basket.jpg" alt="Fruits Wholesale Prices" width="96" height="96" loading="lazy">
                </div>
                <div class="quick-card-body">
                    <div class="quick-card-badge badge-fruit-alt">🍎 FRUITS</div>
                    <h3>Fruits Wholesale</h3>
                    <p><?= $stats['fruit_count'] ?> varieties &bull; Mandi Rates</p>
                    <span class="quick-card-btn">
                        View Prices <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </span>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- Sticky Mobile & Desktop Category Jump Bar -->
<nav class="sticky-category-bar" id="stickyCategoryBar" aria-label="Quick Category Navigation">
    <div class="container sticky-category-container">
        <div class="category-pills-scroll">
            <a href="#vegetables" class="cat-pill active" data-target="vegetables">
                <span>🥦</span> Vegetables
            </a>
            <a href="#accordion-tomato" class="cat-pill" data-target="accordion-tomato">
                <span>🍅</span> Tomato
            </a>
            <a href="#accordion-onion" class="cat-pill" data-target="accordion-onion">
                <span>🧅</span> Onion
            </a>
            <a href="#accordion-potato" class="cat-pill" data-target="accordion-potato">
                <span>🥔</span> Potato
            </a>
            <a href="#accordion-greens-keerai" class="cat-pill" data-target="accordion-greens-keerai">
                <span>🌿</span> Greens
            </a>
            <a href="#accordion-garlic" class="cat-pill" data-target="accordion-garlic">
                <span>🧄</span> Garlic
            </a>
            <a href="#fruits" class="cat-pill cat-pill-fruit" data-target="fruits">
                <span>🍎</span> Fruits
            </a>
            <a href="#accordion-banana" class="cat-pill cat-pill-fruit" data-target="accordion-banana">
                <span>🍌</span> Banana
            </a>
            <a href="#accordion-mango" class="cat-pill cat-pill-fruit" data-target="accordion-mango">
                <span>🥭</span> Mango
            </a>
        </div>
    </div>
</nav>

<!-- Search Bar Section -->
<section class="search-section" id="searchSection">
    <div class="container">
        <div class="search-container-box">
            <div class="search-icon-wrap">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>
            <input type="text" id="priceSearchInput" class="search-input" placeholder="Search vegetables, fruits or prices... (Press '/' to focus)" aria-label="Search produce items and rates" autocomplete="off">
            <span class="search-count-badge" id="searchCountBadge"></span>
            <button type="button" id="searchClearBtn" class="search-clear-btn" aria-label="Clear search">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
    </div>
</section>

<!-- Main Produce Price Lists -->
<div class="container">
    <!-- Empty Search State -->
    <div class="no-results-box" id="noSearchResults">
        <div class="no-results-icon">🔍</div>
        <h4 class="no-results-title">No produce items found</h4>
        <p class="no-results-text">We couldn't find any results matching "<span id="searchTermDisplay"></span>". Try another item name like Tomato, Onion, or Mango.</p>
    </div>

    <!-- 1. VEGETABLES SECTION -->
    <section id="vegetables" class="price-category-section">
        <div class="section-header-wrap">
            <div class="section-title-badge badge-veg">
                <span>🥦</span> VEGETABLES WHOLESALE
            </div>
            <button type="button" class="section-toggle-btn" id="toggleAllVegBtn">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 13l5 5 5-5M7 6l5 5 5-5"/></svg>
                <span class="toggle-text">Collapse All</span>
            </button>
        </div>

        <div class="accordions-list" id="vegetablesAccordions">
            <?php if (isset($categorized_data['vegetables']['subcategories']) && !empty($categorized_data['vegetables']['subcategories'])): ?>
                <?php foreach ($categorized_data['vegetables']['subcategories'] as $index => $subcat): ?>
                    <div class="accordion-card is-open" id="accordion-<?= e($subcat['slug']) ?>">
                        <button class="accordion-header" type="button" aria-expanded="true" aria-controls="panel-<?= e($subcat['slug']) ?>">
                            <div class="accordion-title-wrap">
                                <span class="accordion-icon"><?= e($subcat['icon'] ?: '🥦') ?></span>
                                <span class="accordion-title"><?= e($subcat['name']) ?></span>
                                <span class="accordion-badge"><?= count($subcat['products']) ?> items</span>
                            </div>
                            <div class="accordion-chevron">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </button>

                        <div class="accordion-content" id="panel-<?= e($subcat['slug']) ?>" role="region">
                            <table class="price-table">
                                <thead class="desktop-only-thead">
                                    <tr>
                                        <th>Produce Item</th>
                                        <th class="desktop-unit-col">Unit</th>
                                        <th style="text-align: right;">Wholesale Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($subcat['products'])): ?>
                                        <?php foreach ($subcat['products'] as $prod): ?>
                                            <tr class="produce-row">
                                                <td class="item-name-cell">
                                                    <div class="produce-item-flex">
                                                        <div class="produce-img-wrap">
                                                            <img src="<?= e($prod['image_url'] ?: 'assets/images/produce/' . ($prod['icon'] ?: 'generic-veg.svg')) ?>" 
                                                                 alt="<?= e($prod['name']) ?>" 
                                                                 class="produce-thumbnail" 
                                                                 loading="lazy" 
                                                                 width="36" 
                                                                 height="36" 
                                                                 onerror="this.style.display='none'">
                                                        </div>
                                                        <div class="produce-name-details">
                                                            <div class="produce-title-row">
                                                                <span class="produce-name-en"><?= e($prod['name']) ?></span>
                                                            </div>
                                                            <?php if (!empty($prod['tamil_name'])): ?>
                                                                <span class="produce-name-ta" lang="ta"><?= e($prod['tamil_name']) ?></span>
                                                            <?php endif; ?>
                                                            <div class="produce-meta-mobile">
                                                                <?php if (!empty($prod['price_unit'] ?: $prod['default_unit'])): ?>
                                                                    <span class="unit-tag-mobile"><?= e($prod['price_unit'] ?: $prod['default_unit']) ?></span>
                                                                <?php endif; ?>
                                                                <?php if (!empty($prod['price_notes'])): ?>
                                                                    <span class="produce-notes-pill"><?= e($prod['price_notes']) ?></span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="item-unit-cell desktop-unit-col">
                                                    <?php if (!empty($prod['price_unit'] ?: $prod['default_unit'])): ?>
                                                        <span class="unit-tag"><?= e($prod['price_unit'] ?: $prod['default_unit']) ?></span>
                                                    <?php else: ?>
                                                        <span style="color: var(--color-text-subtle);">—</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="item-price-cell">
                                                    <div class="price-box-wrapper">
                                                        <?= format_price_html($prod['price'], $prod['price_unit'] ?: $prod['default_unit']) ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" style="text-align: center; color: var(--color-text-muted); padding: 20px;">
                                                No items listed for this subcategory on this date.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- 2. FRUITS SECTION -->
    <section id="fruits" class="price-category-section" style="margin-top: 40px;">
        <div class="section-header-wrap">
            <div class="section-title-badge badge-fruit">
                <span>🍎</span> FRUITS WHOLESALE
            </div>
            <button type="button" class="section-toggle-btn btn-fruit" id="toggleAllFruitBtn">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 13l5 5 5-5M7 6l5 5 5-5"/></svg>
                <span class="toggle-text">Collapse Fruit List</span>
            </button>
        </div>

        <div class="accordions-list card-fruit-theme" id="fruitsAccordions">
            <?php if (isset($categorized_data['fruits']['subcategories']) && !empty($categorized_data['fruits']['subcategories'])): ?>
                <?php foreach ($categorized_data['fruits']['subcategories'] as $index => $subcat): ?>
                    <div class="accordion-card is-open" id="accordion-<?= e($subcat['slug']) ?>">
                        <button class="accordion-header" type="button" aria-expanded="true" aria-controls="panel-<?= e($subcat['slug']) ?>">
                            <div class="accordion-title-wrap">
                                <span class="accordion-icon"><?= e($subcat['icon'] ?: '🍎') ?></span>
                                <span class="accordion-title"><?= e($subcat['name']) ?></span>
                                <span class="accordion-badge"><?= count($subcat['products']) ?> items</span>
                            </div>
                            <div class="accordion-chevron">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </button>

                        <div class="accordion-content" id="panel-<?= e($subcat['slug']) ?>" role="region">
                            <table class="price-table">
                                <thead class="desktop-only-thead">
                                    <tr>
                                        <th>Produce Item</th>
                                        <th class="desktop-unit-col">Unit</th>
                                        <th style="text-align: right;">Wholesale Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($subcat['products'])): ?>
                                        <?php foreach ($subcat['products'] as $prod): ?>
                                            <tr class="produce-row">
                                                <td class="item-name-cell">
                                                    <div class="produce-item-flex">
                                                        <div class="produce-img-wrap">
                                                            <img src="<?= e($prod['image_url'] ?: 'assets/images/produce/' . ($prod['icon'] ?: 'generic-fruit.svg')) ?>" 
                                                                 alt="<?= e($prod['name']) ?>" 
                                                                 class="produce-thumbnail" 
                                                                 loading="lazy" 
                                                                 width="36" 
                                                                 height="36" 
                                                                 onerror="this.style.display='none'">
                                                        </div>
                                                        <div class="produce-name-details">
                                                            <div class="produce-title-row">
                                                                <span class="produce-name-en"><?= e($prod['name']) ?></span>
                                                            </div>
                                                            <?php if (!empty($prod['tamil_name'])): ?>
                                                                <span class="produce-name-ta" lang="ta"><?= e($prod['tamil_name']) ?></span>
                                                            <?php endif; ?>
                                                            <div class="produce-meta-mobile">
                                                                <?php if (!empty($prod['price_unit'] ?: $prod['default_unit'])): ?>
                                                                    <span class="unit-tag-mobile"><?= e($prod['price_unit'] ?: $prod['default_unit']) ?></span>
                                                                <?php endif; ?>
                                                                <?php if (!empty($prod['price_notes'])): ?>
                                                                    <span class="produce-notes-pill"><?= e($prod['price_notes']) ?></span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="item-unit-cell desktop-unit-col">
                                                    <?php if (!empty($prod['price_unit'] ?: $prod['default_unit'])): ?>
                                                        <span class="unit-tag"><?= e($prod['price_unit'] ?: $prod['default_unit']) ?></span>
                                                    <?php else: ?>
                                                        <span style="color: var(--color-text-subtle);">—</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="item-price-cell">
                                                    <div class="price-box-wrapper">
                                                        <?= format_price_html($prod['price'], $prod['price_unit'] ?: $prod['default_unit']) ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" style="text-align: center; color: var(--color-text-muted); padding: 20px;">
                                                No fruit items listed on this date.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>

<!-- Trust & Feature Cards Section -->
<section class="trust-section">
    <div class="container">
        <h3 class="trust-section-title">Why Wholesalers &amp; Retailers Trust DailyGurus</h3>
        <div class="trust-grid">
            <!-- Card 1 -->
            <div class="trust-card">
                <div class="trust-icon-box">
                    <span>⚡</span>
                </div>
                <h4>Daily Updated Rates</h4>
                <p>Prices verified every morning straight from Koyambedu Wholesale Mandi auctions by 5:00 AM.</p>
            </div>

            <!-- Card 2 -->
            <div class="trust-card">
                <div class="trust-icon-box">
                    <span>📊</span>
                </div>
                <h4>Wholesale Transparency</h4>
                <p>Authentic bulk prices for crates, bags, and kg batches, providing clear market trends.</p>
            </div>

            <!-- Card 3 -->
            <div class="trust-card">
                <div class="trust-icon-box">
                    <span>📱</span>
                </div>
                <h4>Mobile &amp; Fast</h4>
                <p>Optimized for instantaneous loading on mobile browsers with zero lag and offline search.</p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
