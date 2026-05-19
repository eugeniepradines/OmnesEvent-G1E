    </main>
    <footer class="footer">
        <p>OmnesEvent - Projet Web Dynamique ING2</p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        window.APP_BASE_URL = document.body.dataset.baseUrl || '';
    </script>
    <script src="<?php echo e(urlSite('/assets/js/main.js') . '?v=' . ($assetVersion ?? '1')); ?>"></script>
    <?php if (!empty($scripts)) { foreach ($scripts as $script) { ?>
        <?php $scriptUrl = urlSite($script); ?>
        <script src="<?php echo e((preg_match('#^(https?:)?//#', $scriptUrl) ? $scriptUrl : $scriptUrl . '?v=' . ($assetVersion ?? '1'))); ?>"></script>
    <?php } } ?>
</body>
</html>
