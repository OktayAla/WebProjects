</div>
    </div>

    <footer class="footer mt-auto py-3 bg-white shadow-sm">
        <div class="container-fluid">
            <div class="row">
                <div class="col text-muted">
                    &copy; <?php echo date('Y'); ?> İK Yönetim Sistemi
                </div>
                <div class="col text-end">
                    <span class="text-muted">Version 1.0</span>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/tables.js"></script>
    <?php if (isset($extraScripts)) echo $extraScripts; ?>
</body>
</html>
