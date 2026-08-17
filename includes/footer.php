    </main>

    <!-- Footer Component -->
    <footer class="site-footer">
        <div class="footer-top">
            <div class="container footer-grid">
                <!-- Column 1: Brand & Tagline -->
                <div class="footer-col brand-col">
                    <a href="index.php" class="footer-logo-link" aria-label="DailyGurus Home">
                        <img src="assets/images/logo-white.svg" alt="DailyGurus Wholesale Price List" width="240" height="52" class="footer-logo">
                    </a>
                    <p class="footer-tagline">
                        Your trusted source for daily wholesale prices of vegetables and fruits directly from Koyambedu Wholesale Market, Chennai.
                    </p>
                    <div class="footer-market-badge">
                        <span class="live-dot"></span>
                        <span>Mandi Open: 4:00 AM – 12:00 PM</span>
                    </div>
                </div>

                <!-- Column 2: Quick Links -->
                <div class="footer-col links-col">
                    <h4 class="footer-heading">Quick Links</h4>
                    <ul class="footer-nav">
                        <li><a href="index.php"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg> Home</a></li>
                        <li><a href="index.php#vegetables"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg> Vegetables Prices</a></li>
                        <li><a href="index.php#fruits"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg> Fruits Prices</a></li>
                        <li><a href="history.php"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg> Historical Rates</a></li>
                        <li><a href="about.php"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg> About Us</a></li>
                        <li><a href="contact.php"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg> Contact Us</a></li>
                    </ul>
                </div>

                <!-- Column 3: Categories & Key Produce -->
                <div class="footer-col">
                    <h4 class="footer-heading">Wholesale Produce</h4>
                    <ul class="footer-nav">
                        <li><a href="index.php#accordion-tomato"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg> Tomato (Crates & Local)</a></li>
                        <li><a href="index.php#accordion-onion"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg> Onion (Nashik & Sambar)</a></li>
                        <li><a href="index.php#accordion-potato"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg> Potato (Agra & Hasan)</a></li>
                        <li><a href="index.php#accordion-greens-keerai"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg> Greens & Keerai Bunches</a></li>
                        <li><a href="index.php#accordion-garlic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg> Garlic Boom & A4</a></li>
                        <li><a href="index.php#fruits"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg> Seasonal Fresh Fruits</a></li>
                    </ul>
                </div>

                <!-- Column 4: Contact Info -->
                <div class="footer-col contact-col">
                    <h4 class="footer-heading">Contact & Location</h4>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        </div>
                        <div class="contact-text">
                            <strong><?= e(CONTACT_ADDRESS) ?></strong>
                            <span>Market Gate 2, Koyambedu, Chennai - 600092</span>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        </div>
                        <div class="contact-text">
                            <a href="tel:<?= e(str_replace(' ', '', CONTACT_PHONE)) ?>"><?= e(CONTACT_PHONE) ?></a>
                            <span>Mon - Sun: 5:00 AM - 9:00 PM</span>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        </div>
                        <div class="contact-text">
                            <a href="mailto:<?= e(CONTACT_EMAIL) ?>"><?= e(CONTACT_EMAIL) ?></a>
                            <span>Direct Mandi Inquiries</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom Bar -->
        <div class="footer-bottom">
            <div class="container footer-bottom-flex">
                <div class="footer-copy">
                    &copy; <?= date('Y') ?> <strong><?= e(SITE_NAME) ?></strong>. All rights reserved. Daily Wholesale Price List for Vegetables & Fruits.
                </div>
                <div class="footer-bottom-links">
                    <a href="sitemap.php">Sitemap</a>
                    <span class="dot-sep">•</span>
                    <a href="admin/login.php">Admin Portal</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Floating Button -->
    <button class="back-to-top" id="backToTopBtn" aria-label="Scroll back to top" title="Back to top">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"></polyline></svg>
    </button>

    <!-- JavaScript Application Scripts -->
    <script src="assets/js/app.js?v=<?= filemtime(__DIR__ . '/../assets/js/app.js') ?: time() ?>"></script>
</body>
</html>
