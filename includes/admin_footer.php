<?php
/**
 * DailyGurus Price List - Admin Footer
 */
?>
        </div><!-- /.admin-content-wrapper -->

        <!-- Admin Footer -->
        <footer class="admin-page-footer">
            <div class="footer-left">
                <span>&copy; <?= date('Y') ?> <strong><?= SITE_NAME ?></strong> — Wholesale Market Pricing Platform</span>
            </div>
            <div class="footer-right">
                <span class="status-indicator-dot online"></span>
                <span>Server Time: <?= date('Y-m-d H:i:s T') ?></span>
            </div>
        </footer>
    </div><!-- /.admin-main -->
</div><!-- /.admin-layout -->

<!-- Admin JavaScript -->
<script src="<?= SITE_URL ?>/assets/js/admin.js?v=<?= time() ?>"></script>
</body>
</html>
