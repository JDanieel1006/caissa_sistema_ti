<?php
$pageTitle = 'Nueva Asignacion';

$roleLabels = [
    'auxiliar_administrativo' => 'Auxiliar Administrativo',
    'coordinador'             => 'Coordinador',
    'operario'                => 'Operario',
    'ayudante'                => 'Ayudante',
    'residente_becario'       => 'Residente/Becario',
    'auxiliar_seguridad'      => 'Auxiliar de Seguridad',
    'auxiliar_oficina'        => 'Auxiliar de Oficina',
    'control_de_obra'         => 'Control de Obra',
    'supervisor_seguridad'    => 'Supervisor de Seguridad',
    'contra_incendios'        => 'Contra Incendios',
    'tecnico_instrumentista'  => 'Tecnico Instrumentista',
    'admin'                   => 'Administrador',
    'tecnico'                 => 'Tecnico',
    'maestro'                 => 'Maestro',
];

$usuarioSeleccionado = null;
if (!empty($_POST['usuario_id'])) {
    foreach ($usuarios as $u) {
        if ((int)$u['id'] === (int)$_POST['usuario_id']) {
            $usuarioSeleccionado = $u;
            break;
        }
    }
}

$extraJs = <<<'JS'
if (window.jQuery && $.fn.select2) {
    const $usuario = $('#selUsuario');
    const $equipo = $('#selEquipo');

    $usuario.select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Busca y selecciona un usuario',
        minimumInputLength: 0,
        ajax: {
            url: 'index.php?c=asignaciones&a=buscarUsuarios',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term || '', page: params.page || 1 }),
            processResults: data => data
        }
    });

    $usuario.on('select2:select', function (e) {
        const data = e.params.data || {};
        $('#infoDepto').val(data.departamento || '');
        $('#infoPuesto').val(data.rol || '');
    });

    const selectedUser = $usuario.find(':selected');
    if (selectedUser.val()) {
        $('#infoDepto').val(selectedUser.data('depto') || '');
        $('#infoPuesto').val(selectedUser.data('rol') || '');
    }

    $equipo.select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Busca por codigo, marca, modelo o serie',
        minimumInputLength: 0,
        ajax: {
            url: 'index.php?c=asignaciones&a=buscarEquipos',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term || '', page: params.page || 1 }),
            processResults: data => data
        },
        templateResult: function (item) {
            if (!item.id) return item.text;
            const estado = item.disponible ? '<span class="badge bg-success ms-2">Disponible</span>' : '<span class="badge bg-danger ms-2">Asignado</span>';
            const serie = item.serie ? ' | Serie: ' + item.serie : '';
            return $('<div><strong>' + item.codigo + '</strong> - ' + (item.nombre || item.categoria || '') + estado + '<br><small class="text-muted">' + (item.categoria || '') + serie + '</small></div>');
        },
        escapeMarkup: markup => markup
    });
}
JS;

require __DIR__ . '/../layouts/header.php';
?>
<div class="row justify-content-center"><div class="col-lg-8">
<?php if ($error): ?>
<div class="alert alert-danger d-flex gap-2 mb-3">
  <i class="bi bi-exclamation-triangle-fill"></i><?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<div class="card">
  <div class="card-header"><i class="bi bi-file-earmark-plus me-2 text-success"></i>Nueva Asignacion de Equipo</div>
  <div class="card-body p-4">
  <form method="POST" action="index.php?c=asignaciones&a=create">

    <h6 style="font-family:'Syne',sans-serif;font-weight:700;margin-bottom:1rem">
      <i class="bi bi-person me-2 text-primary"></i>Datos del Responsable
    </h6>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label">Nombre *</label>
        <select name="usuario_id" id="selUsuario" class="form-select no-select2" required>
          <option value="">Busca y selecciona un usuario</option>
          <?php if ($usuarioSeleccionado): ?>
          <option value="<?= $usuarioSeleccionado['id'] ?>"
                  data-depto="<?= htmlspecialchars($usuarioSeleccionado['departamento'] ?? '') ?>"
                  data-rol="<?= htmlspecialchars($roleLabels[$usuarioSeleccionado['rol']] ?? ucwords(str_replace('_', ' ', $usuarioSeleccionado['rol']))) ?>"
                  selected>
            <?= htmlspecialchars($usuarioSeleccionado['nombre'] . ' ' . $usuarioSeleccionado['apellido']) ?>
          </option>
          <?php endif; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Puesto</label>
        <input type="text" id="infoPuesto" class="form-control" style="background:#f7faff" readonly placeholder="Se llena al seleccionar usuario">
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label">Departamento</label>
        <input type="text" id="infoDepto" class="form-control" style="background:#f7faff" readonly placeholder="Se llena al seleccionar usuario">
      </div>
      <div class="col-md-6">
        <label class="form-label">Fecha de asignacion</label>
        <input type="text" class="form-control" style="background:#f7faff" readonly value="<?= date('d/m/Y') ?>">
        <input type="hidden" name="fecha_asignacion" value="<?= date('Y-m-d') ?>">
      </div>
    </div>

    <hr>
    <h6 style="font-family:'Syne',sans-serif;font-weight:700;margin-bottom:1rem">
      <i class="bi bi-file-text me-2 text-warning"></i>Datos del Contrato
    </h6>

    <div class="row g-3 mb-3">
      <div class="col-md-8">
        <label class="form-label">Nombre de la Obra</label>
        <input type="text" name="nombre_obra" class="form-control" placeholder="Nombre del proyecto u obra" value="<?= htmlspecialchars($_POST['nombre_obra'] ?? '') ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">No. de Contrato</label>
        <input type="text" name="numero_contrato" class="form-control" placeholder="Ej. CTT-2025-001" value="<?= htmlspecialchars($_POST['numero_contrato'] ?? '') ?>">
      </div>
    </div>

    <hr>
    <h6 style="font-family:'Syne',sans-serif;font-weight:700;margin-bottom:1rem">
      <i class="bi bi-box-seam me-2 text-info"></i>Equipo a Asignar
    </h6>

    <div class="mb-3">
      <label class="form-label">Buscar equipo *</label>
      <select name="equipo_id" id="selEquipo" class="form-select no-select2" required>
        <option value="">Busca por codigo, marca, modelo o serie</option>
        <?php if ($equipoPresel): ?>
        <option value="<?= $equipoPresel['id'] ?>" selected>
          <?= htmlspecialchars($equipoPresel['codigo'] . ' - ' . trim(($equipoPresel['marca'] ?? '') . ' ' . ($equipoPresel['modelo'] ?? '')) . (($equipoPresel['numero_serie'] ?? '') ? ' | Serie: ' . $equipoPresel['numero_serie'] : '')) ?>
        </option>
        <?php endif; ?>
      </select>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label">Condicion de entrega</label>
        <select name="condicion_entrega" class="form-select">
          <?php foreach ($condiciones as $k => $c): ?>
          <option value="<?= $k ?>"><?= $c['label'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Devolucion esperada</label>
        <input type="date" name="fecha_devolucion_esperada" class="form-control" value="<?= htmlspecialchars($_POST['fecha_devolucion_esperada'] ?? '') ?>">
      </div>
    </div>

    <div class="mb-4">
      <label class="form-label">Notas de entrega</label>
      <textarea name="notas_entrega" class="form-control" rows="2" placeholder="Observaciones sobre el estado o accesorios incluidos..."><?= htmlspecialchars($_POST['notas_entrega'] ?? '') ?></textarea>
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-success px-4">
        <i class="bi bi-check2 me-1"></i>Registrar Asignacion
      </button>
      <a href="index.php?c=asignaciones" class="btn btn-outline-secondary px-4">Cancelar</a>
    </div>
  </form>
  </div>
</div>
</div></div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
