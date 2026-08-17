<?php
/**
 * DailyGurus Price List - Contact & Market Information
 */

require_once __DIR__ . '/includes/functions.php';

$success_msg = '';
$error_msg = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $topic = trim($_POST['topic'] ?? 'General Inquiry');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($phone) || empty($message)) {
        $error_msg = 'Please provide your name, phone number, and message.';
    } else {
        // Form submitted successfully (in real world could send mail or save to DB)
        $success_msg = 'Thank you for reaching out, ' . e($name) . '! Your message regarding "' . e($topic) . '" has been received. Our market desk will get back to you shortly.';
    }
}

$page_title = 'Contact DailyGurus - Koyambedu Wholesale Market Chennai';
$page_description = 'Get in touch with the DailyGurus market desk. Market location, phone, email, and wholesale produce rate inquiries.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <h1 class="page-hero-title">Contact Us</h1>
        <p class="page-hero-desc">
            Have questions about today's mandi auction prices, bulk procurement, or produce listings? Reach out to our Koyambedu Market desk.
        </p>
    </div>
</section>

<!-- Contact Body -->
<section class="page-body-section">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 36px; max-width: 1100px; margin: 0 auto;">
            <!-- Left: Info Cards & Market Hours -->
            <div>
                <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 28px; box-shadow: var(--shadow-sm); margin-bottom: 24px;">
                    <h3 style="font-size: 1.3rem; font-weight: 800; color: var(--color-text-main); margin-bottom: 20px;">
                        Market Desk Info
                    </h3>

                    <!-- Address -->
                    <div class="contact-item" style="margin-bottom: 20px;">
                        <div class="contact-icon" style="color: var(--color-primary);">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        </div>
                        <div class="contact-text">
                            <strong style="color: var(--color-text-main); font-size: 15px;"><?= e(CONTACT_ADDRESS) ?></strong>
                            <span style="color: var(--color-text-muted);">Vegetable Complex, Gate No. 2, Koyambedu, Chennai - 600092</span>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="contact-item" style="margin-bottom: 20px;">
                        <div class="contact-icon" style="color: var(--color-primary);">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        </div>
                        <div class="contact-text">
                            <a href="tel:<?= e(str_replace(' ', '', CONTACT_PHONE)) ?>" style="color: var(--color-primary-dark); font-weight: 700; font-size: 15px;"><?= e(CONTACT_PHONE) ?></a>
                            <span style="color: var(--color-text-muted);">Mon – Sun: 4:30 AM to 8:00 PM</span>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="contact-item">
                        <div class="contact-icon" style="color: var(--color-primary);">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        </div>
                        <div class="contact-text">
                            <a href="mailto:<?= e(CONTACT_EMAIL) ?>" style="color: var(--color-primary-dark); font-weight: 700; font-size: 15px;"><?= e(CONTACT_EMAIL) ?></a>
                            <span style="color: var(--color-text-muted);">For general inquiries &amp; merchant partnerships</span>
                        </div>
                    </div>
                </div>

                <!-- Mandi Trading Schedule -->
                <div style="background: var(--color-primary-subtle); border: 1px solid var(--color-primary-border); border-radius: var(--radius-lg); padding: 24px;">
                    <h4 style="font-size: 1.1rem; font-weight: 700; color: var(--color-primary-dark); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                        <span>⏰</span> Mandi Trading Schedule
                    </h4>
                    <ul style="display: flex; flex-direction: column; gap: 8px; font-size: 13.5px; color: var(--color-text-body);">
                        <li style="display: flex; justify-content: space-between;">
                            <strong>Wholesale Mandi Auction:</strong>
                            <span>4:00 AM – 10:00 AM</span>
                        </li>
                        <li style="display: flex; justify-content: space-between;">
                            <strong>Semi-Wholesale Trading:</strong>
                            <span>8:00 AM – 1:00 PM</span>
                        </li>
                        <li style="display: flex; justify-content: space-between;">
                            <strong>Rates Online Publishing:</strong>
                            <span style="color: var(--color-primary-dark); font-weight: 700;">5:00 AM Daily</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right: Inquiry Form -->
            <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow-sm);">
                <h3 style="font-size: 1.3rem; font-weight: 800; color: var(--color-text-main); margin-bottom: 8px;">
                    Send an Inquiry
                </h3>
                <p style="font-size: 14px; color: var(--color-text-muted); margin-bottom: 24px;">
                    Fill in the form below and our market representatives will get back to you.
                </p>

                <?php if ($success_msg): ?>
                    <div class="alert-success">
                        <?= $success_msg ?>
                    </div>
                <?php endif; ?>

                <?php if ($error_msg): ?>
                    <div style="background-color: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; padding: 14px 18px; border-radius: var(--radius-md); margin-bottom: 20px; font-weight: 600;">
                        <?= e($error_msg) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="contact.php" class="form-grid">
                    <div class="form-group">
                        <label for="name" class="form-label">Your Name *</label>
                        <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. Ramesh Kumar">
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" class="form-control" required placeholder="e.g. +91 98765 43210">
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address (Optional)</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="e.g. ramesh@example.com">
                    </div>

                    <div class="form-group">
                        <label for="topic" class="form-label">Subject / Produce Type</label>
                        <select id="topic" name="topic" class="form-control">
                            <option value="Vegetables Wholesale">Vegetables Wholesale</option>
                            <option value="Fruits Wholesale">Fruits Wholesale</option>
                            <option value="Bulk Supply Procurement">Bulk Supply Procurement</option>
                            <option value="Merchant Partnership">Merchant Partnership</option>
                            <option value="Price Discrepancy / Feedback">Price Discrepancy / Feedback</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="message" class="form-label">Your Message *</label>
                        <textarea id="message" name="message" class="form-control" required placeholder="Tell us about the produce items, quantity, or questions you have..."></textarea>
                    </div>

                    <div class="form-group full-width" style="margin-top: 10px;">
                        <button type="submit" class="btn-primary-submit">
                            <span>Submit Message</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- FAQ Section -->
        <div style="max-width: 900px; margin: 60px auto 0 auto;">
            <h3 style="font-size: 1.5rem; font-weight: 800; color: var(--color-text-main); text-align: center; margin-bottom: 24px;">
                Frequently Asked Questions
            </h3>

            <div class="accordions-list">
                <div class="accordion-card is-open">
                    <button class="accordion-header" type="button">
                        <div class="accordion-title-wrap">
                            <span class="accordion-title">What time are the daily wholesale prices updated?</span>
                        </div>
                        <div class="accordion-chevron">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                    </button>
                    <div class="accordion-content" style="padding: 16px 20px; font-size: 14.5px; color: var(--color-text-body); line-height: 1.6;">
                        Our market desk begins monitoring opening transactions at Koyambedu Market at 4:00 AM. Prices are compiled, verified with mandi commission agents, and published by 5:00 AM every morning.
                    </div>
                </div>

                <div class="accordion-card">
                    <button class="accordion-header" type="button">
                        <div class="accordion-title-wrap">
                            <span class="accordion-title">What do price ranges like ₹1,900 / ₹1,950 mean?</span>
                        </div>
                        <div class="accordion-chevron">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                    </button>
                    <div class="accordion-content" style="padding: 16px 20px; font-size: 14.5px; color: var(--color-text-body); line-height: 1.6;">
                        In wholesale auctions, prices vary according to grade, freshness, and consignment size. When a range is listed (e.g. ₹1,900 / ₹1,950 per 50 kg), the lower value represents standard grade produce, and the higher value represents premium export-grade produce.
                    </div>
                </div>

                <div class="accordion-card">
                    <button class="accordion-header" type="button">
                        <div class="accordion-title-wrap">
                            <span class="accordion-title">Are these prices wholesale or retail?</span>
                        </div>
                        <div class="accordion-chevron">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                    </button>
                    <div class="accordion-content" style="padding: 16px 20px; font-size: 14.5px; color: var(--color-text-body); line-height: 1.6;">
                        All prices listed on DailyGurus are strictly <strong>wholesale mandi rates</strong> (crates, 50kg sacks, boxes, or bulk bundles). Local retail neighborhood rates are typically 25% to 50% higher to account for sorting, transport, spoilage, and retail markup.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
