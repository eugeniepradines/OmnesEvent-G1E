    </main>
    <footer class="footer">
        <p>OmnesEvent - Projet Web Dynamique ING2</p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="/omnesevent/assets/js/main.js"></script>
    <?php if (!empty($scripts)) { foreach ($scripts as $script) { ?>
        <script src="<?php echo e($script); ?>"></script>
    <?php } } ?>
</body>
</html>
