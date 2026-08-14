    </div><!-- /page-body -->
</div><!-- /main-content -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    if (toggle) {
        toggle.addEventListener('click', () => sidebar.classList.toggle('show'));
        document.addEventListener('click', e => { if (!sidebar.contains(e.target) && e.target !== toggle) sidebar.classList.remove('show'); });
    }

    if (window.jQuery) {
        const dtLang = {
            emptyTable: 'No hay datos disponibles',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
            infoEmpty: 'Mostrando 0 a 0 de 0 registros',
            infoFiltered: '(filtrado de _MAX_ registros totales)',
            lengthMenu: 'Mostrar _MENU_ registros',
            loadingRecords: 'Cargando...',
            processing: 'Procesando...',
            search: 'Buscar:',
            zeroRecords: 'No se encontraron registros',
            paginate: { first: 'Primero', last: 'Ultimo', next: 'Siguiente', previous: 'Anterior' }
        };

        $('.js-data-table').each(function () {
            if (!$.fn.DataTable.isDataTable(this)) {
                $(this).DataTable({
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                    language: dtLang,
                    order: []
                });
            }
        });

        $('select.form-select:not(.no-select2):not(.select2-hidden-accessible)').each(function () {
            if ($(this).data('select2')) return;
            $(this).select2({
                theme: 'bootstrap-5',
                width: '100%',
                allowClear: !this.required,
                placeholder: $(this).find('option:first').text() || 'Selecciona'
            });
        });
    }
</script>
<?php if (isset($extraJs)): ?><script><?= $extraJs ?></script><?php endif; ?>
</body>
</html>
