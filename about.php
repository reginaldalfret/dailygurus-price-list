<?php
/**
 * DailyGurus Price List - About Us Page
 */

require_once __DIR__ . '/includes/functions.php';

$page_title = 'About DailyGurus - Daily Wholesale Produce Market Intelligence';
$page_description = 'Learn about DailyGurus, Chennai\'s leading wholesale market price tracking platform providing daily transparent rates for vegetables and fruits directly from Koyambedu Mandi.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <h1 class="page-hero-title">About DailyGurus</h1>
        <p class="page-hero-desc">
            Empowering traders, restaurants, retailers, and consumers with accurate, transparent daily wholesale market prices from Koyambedu, Chennai.
        </p>
    </div>
</section>

<!-- About Main Content -->
<section class="page-body-section">
    <div class="container">
        <div style="max-width: 900px; margin: 0 auto;">
            <!-- Mission Card -->
            <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 36px; box-shadow: var(--shadow-sm); margin-bottom: 36px;">
                <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-primary);">Our Purpose</span>
                <h2 style="font-size: 1.8rem; font-weight: 800; color: var(--color-primary-dark); margin: 8px 0 16px 0;">
                    Democratizing Wholesale Market Prices
                </h2>
                <p style="font-size: 16px; color: var(--color-text-body); line-height: 1.7; margin-bottom: 16px;">
                    Koyambedu Wholesale Market in Chennai is one of Asia's largest perishable goods markets, handling thousands of metric tonnes of fresh vegetables and fruits every morning. However, volatile auction prices and lack of real-time price dissemination often leave vendors, chefs, and retail buyers in the dark.
                </p>
                <p style="font-size: 16px; color: var(--color-text-body); line-height: 1.7;">
                    <strong>DailyGurus Price List</strong> was established to bridge this information gap. By recording and publishing verified morning trading auction prices every single day, we enable market participants to buy with confidence, forecast costs accurately, and trade with total transparency.
                </p>
            </div>

            <!-- Stats Bar -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 40px;">
                <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 24px; text-align: center; box-shadow: var(--shadow-card);">
                    <div style="font-size: 2.2rem; font-weight: 800; color: var(--color-primary);">80+</div>
                    <div style="font-size: 14px; font-weight: 600; color: var(--color-text-muted); margin-top: 4px;">Daily Tracked Commodities</div>
                </div>
                <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 24px; text-align: center; box-shadow: var(--shadow-card);">
                    <div style="font-size: 2.2rem; font-weight: 800; color: var(--color-fruit-primary);">5:00 AM</div>
                    <div style="font-size: 14px; font-weight: 600; color: var(--color-text-muted); margin-top: 4px;">Daily Auction Rates Published</div>
                </div>
                <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 24px; text-align: center; box-shadow: var(--shadow-card);">
                    <div style="font-size: 2.2rem; font-weight: 800; color: var(--color-primary-dark);">100%</div>
                    <div style="font-size: 14px; font-weight: 600; color: var(--color-text-muted); margin-top: 4px;">Free &amp; Open Access</div>
                </div>
            </div>

            <!-- How We Operate (3 Steps) -->
            <div style="margin-bottom: 40px;">
                <h3 style="font-size: 1.5rem; font-weight: 800; color: var(--color-text-main); text-align: center; margin-bottom: 24px;">
                    How We Collect &amp; Verify Rates
                </h3>
                
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                    <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 24px;">
                        <div style="font-size: 28px; margin-bottom: 12px;">🚛</div>
                        <h4 style="font-size: 1.1rem; font-weight: 700; color: var(--color-text-main); margin-bottom: 8px;">1. Mandi Influx</h4>
                        <p style="font-size: 14px; color: var(--color-text-muted); line-height: 1.6;">
                            Trucks and farm consignments arrive overnight from across Tamil Nadu, Karnataka, Andhra Pradesh, and Maharashtra.
                        </p>
                    </div>

                    <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 24px;">
                        <div style="font-size: 28px; margin-bottom: 12px;">⚖️</div>
                        <h4 style="font-size: 1.1rem; font-weight: 700; color: var(--color-text-main); margin-bottom: 8px;">2. Auction Verification</h4>
                        <p style="font-size: 14px; color: var(--color-text-muted); line-height: 1.6;">
                            Our ground representatives track opening auctions and wholesale transactions across tomato, onion, potato, and fruit complexes.
                        </p>
                    </div>

                    <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 24px;">
                        <div style="font-size: 28px; margin-bottom: 12px;">📱</div>
                        <h4 style="font-size: 1.1rem; font-weight: 700; color: var(--color-text-main); margin-bottom: 8px;">3. Instant Broadcast</h4>
                        <p style="font-size: 14px; color: var(--color-text-muted); line-height: 1.6;">
                            Verified prices with standardized packaging units (crates, bags, kg) are published online for immediate access.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Call to Action Card -->
            <div style="background: linear-gradient(135deg, #0D4715 0%, #1B5E20 100%); color: #FFFFFF; border-radius: var(--radius-lg); padding: 36px; text-align: center; box-shadow: var(--shadow-lg);">
                <h3 style="font-size: 1.6rem; font-weight: 800; margin-bottom: 10px;">Stay Ahead of Mandi Price Trends</h3>
                <p style="font-size: 15px; color: #DCFCE7; max-width: 540px; margin: 0 auto 24px auto;">
                    Bookmark DailyGurus or check in every morning at 5:00 AM for Chennai's official wholesale produce price benchmark.
                </p>
                <div style="display: flex; justify-content: center; gap: 14px; flex-wrap: wrap;">
                    <a href="index.php" style="background-color: #FFFFFF; color: #0D4715; font-weight: 700; padding: 10px 24px; border-radius: var(--radius-full);">
                        View Today's Prices &rarr;
                    </a>
                    <a href="contact.php" style="background: rgba(255, 255, 255, 0.15); color: #FFFFFF; border: 1px solid rgba(255, 255, 255, 0.3); font-weight: 600; padding: 10px 24px; border-radius: var(--radius-full);">
                        Contact Market Team
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
