    </div><!-- /page-body -->
</div><!-- /main-content -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    if (toggle) {
        toggle.addEventListener('click', () => sidebar.classList.toggle('show'));
        document.addEventListener('click', e => { if (!sidebar.contains(e.target) && e.target !== toggle) sidebar.classList.remove('show'); });
    }
</script>
<?php if (isset($extraJs)): ?><script><?= $extraJs ?></script><?php endif; ?>
</body>
</html>
