<?php
/**
 * DailyGurus Price List - Historical Price Archive
 */

require_once __DIR__ . '/includes/functions.php';

$published_dates = get_all_published_dates();
$latest_date = get_latest_price_date();

// Check if a specific date is selected or default to the most recent archive / latest date
$selected_date = isset($_GET['date']) ? trim($_GET['date']) : ($latest_date ?: date('Y-m-d'));

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selected_date) || !is_date_published($selected_date)) {
    $selected_date = $latest_date ?: date('Y-m-d');
}

$date_info = get_price_date_info($selected_date);
$categorized_data = get_categorized_prices($selected_date);
$stats = get_price_stats($categorized_data);

// SEO Meta
$page_title = 'Historical Wholesale Rates (' . format_date_short($selected_date) . ') - DailyGurus Price List';
$page_description = 'Browse past wholesale vegetable and fruit mandi prices from Koyambedu Market Chennai. Track price fluctuations and historical market archives.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <h1 class="page-hero-title">Historical Price Archives</h1>
        <p class="page-hero-desc">
            Explore daily wholesale rates recorded across past trading sessions in Koyambedu Market. Select a date below to view the complete price snapshot.
        </p>
    </div>
</section>

<section class="page-body-section">
    <div class="container">
        <!-- Date Selector Card -->
        <div class="history-date-picker-card">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 12px;">
                <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--color-text-main);">
                    📅 Select Archive Date
                </h3>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <button onclick="window.print()" class="section-toggle-btn" type="button">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                        Print Snapshot
                    </button>
                    <a href="index.php?date=<?= e($selected_date) ?>" class="section-toggle-btn" style="background-color: var(--color-primary-subtle); color: var(--color-primary-dark); border-color: var(--color-primary-border);">
                        Open in Full View &rarr;
                    </a>
                </div>
            </div>

            <!-- Published Dates Grid -->
            <div class="history-grid-dates">
                <?php foreach ($published_dates as $row): ?>
                    <?php $is_curr = ($row['price_date'] === $selected_date); ?>
                    <a href="history.php?date=<?= e($row['price_date']) ?>" class="history-date-card <?= $is_curr ? 'active' : '' ?>">
                        <div>
                            <div class="history-date-card-title">
                                <?= e(format_date_short($row['price_date'])) ?>
                            </div>
                            <div class="history-date-card-count">
                                <?= e($row['item_count']) ?> items priced
                            </div>
                        </div>
                        <?php if ($row['price_date'] === $latest_date): ?>
                            <span class="pulse-indicator" style="font-size: 11px; padding: 2px 6px;">Latest</span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Selected Date Snapshot Header -->
        <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 28px; box-shadow: var(--shadow-sm);">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                <div>
                    <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-primary);">Snapshot View</span>
                    <h2 style="font-size: 1.6rem; font-weight: 800; color: var(--color-text-main); margin-top: 4px;">
                        <?= e(format_date_long($selected_date)) ?>
                    </h2>
                    <?php if (!empty($date_info['notes'])): ?>
                        <p style="color: var(--color-text-muted); font-size: 14px; margin-top: 4px;">
                            📝 <em><?= e($date_info['notes']) ?></em>
                        </p>
                    <?php endif; ?>
                </div>

                <div style="display: flex; gap: 16px;">
                    <div style="background: var(--color-bg-page); padding: 10px 18px; border-radius: var(--radius-md); text-align: center; border: 1px solid var(--color-border);">
                        <div style="font-size: 1.3rem; font-weight: 800; color: var(--color-primary-dark);"><?= $stats['veg_count'] ?></div>
                        <div style="font-size: 12px; color: var(--color-text-muted); font-weight: 600;">Vegetables</div>
                    </div>
                    <div style="background: var(--color-bg-page); padding: 10px 18px; border-radius: var(--radius-md); text-align: center; border: 1px solid var(--color-border);">
                        <div style="font-size: 1.3rem; font-weight: 800; color: var(--color-fruit-primary);"><?= $stats['fruit_count'] ?></div>
                        <div style="font-size: 12px; color: var(--color-text-muted); font-weight: 600;">Fruits</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Bar for this Historical Snapshot -->
        <div class="search-section" style="position: static; margin-bottom: 24px; padding: 0;">
            <div class="search-container-box">
                <div class="search-icon-wrap">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <input type="text" id="priceSearchInput" class="search-input" placeholder="Search produce in this snapshot..." aria-label="Search produce items and rates">
                <span class="search-count-badge" id="searchCountBadge"></span>
                <button type="button" id="searchClearBtn" class="search-clear-btn" aria-label="Clear search">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
        </div>

        <!-- Empty Search State -->
        <div class="no-results-box" id="noSearchResults">
            <div class="no-results-icon">🔍</div>
            <h4 class="no-results-title">No produce items found</h4>
            <p class="no-results-text">We couldn't find any results matching "<span id="searchTermDisplay"></span>" in this archive.</p>
        </div>

        <!-- Vegetables List -->
        <div class="section-header-wrap">
            <div class="section-title-badge badge-veg">
                <span>🥦</span> VEGETABLES WHOLESALE
            </div>
            <button type="button" class="section-toggle-btn" id="toggleAllVegBtn">
                <span class="toggle-text">Collapse All</span>
            </button>
        </div>

        <div class="accordions-list" id="vegetablesAccordions">
            <?php if (isset($categorized_data['vegetables']['subcategories'])): ?>
                <?php foreach ($categorized_data['vegetables']['subcategories'] as $subcat): ?>
                    <div class="accordion-card is-open">
                        <button class="accordion-header" type="button">
                            <div class="accordion-title-wrap">
                                <span class="accordion-icon"><?= e($subcat['icon'] ?: '🥦') ?></span>
                                <span class="accordion-title"><?= e($subcat['name']) ?></span>
                                <span class="accordion-badge"><?= count($subcat['products']) ?> items</span>
                            </div>
                            <div class="accordion-chevron">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </button>
                        <div class="accordion-content">
                            <table class="price-table">
                                <thead class="desktop-only-thead">
                                    <tr>
                                        <th>Produce Item</th>
                                        <th class="desktop-unit-col">Unit</th>
                                        <th style="text-align: right;">Wholesale Price</th>
                                    </tr>
                                </thead>
                                <tbody>
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
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="item-unit-cell desktop-unit-col">
                                                <span class="unit-tag"><?= e($prod['price_unit'] ?: $prod['default_unit'] ?: 'kg') ?></span>
                                            </td>
                                            <td class="item-price-cell">
                                                <div class="price-box-wrapper">
                                                    <?= format_price_html($prod['price'], $prod['price_unit'] ?: $prod['default_unit']) ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Fruits List -->
        <div class="section-header-wrap" style="margin-top: 36px;">
            <div class="section-title-badge badge-fruit">
                <span>🍎</span> FRUITS WHOLESALE
            </div>
            <button type="button" class="section-toggle-btn btn-fruit" id="toggleAllFruitBtn">
                <span class="toggle-text">Collapse Fruit List</span>
            </button>
        </div>

        <div class="accordions-list card-fruit-theme" id="fruitsAccordions">
            <?php if (isset($categorized_data['fruits']['subcategories'])): ?>
                <?php foreach ($categorized_data['fruits']['subcategories'] as $subcat): ?>
                    <div class="accordion-card is-open">
                        <button class="accordion-header" type="button">
                            <div class="accordion-title-wrap">
                                <span class="accordion-icon"><?= e($subcat['icon'] ?: '🍎') ?></span>
                                <span class="accordion-title"><?= e($subcat['name']) ?></span>
                                <span class="accordion-badge"><?= count($subcat['products']) ?> items</span>
                            </div>
                            <div class="accordion-chevron">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </button>
                        <div class="accordion-content">
                            <table class="price-table">
                                <thead class="desktop-only-thead">
                                    <tr>
                                        <th>Produce Item</th>
                                        <th class="desktop-unit-col">Unit</th>
                                        <th style="text-align: right;">Wholesale Price</th>
                                    </tr>
                                </thead>
                                <tbody>
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
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="item-unit-cell desktop-unit-col">
                                                <span class="unit-tag"><?= e($prod['price_unit'] ?: $prod['default_unit'] ?: 'kg') ?></span>
                                            </td>
                                            <td class="item-price-cell">
                                                <div class="price-box-wrapper">
                                                    <?= format_price_html($prod['price'], $prod['price_unit'] ?: $prod['default_unit']) ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
