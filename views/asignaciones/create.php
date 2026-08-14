<?php
$pageTitle = 'Nueva Asignación';
require __DIR__ . '/../layouts/header.php';

// Pasar usuarios como JSON para autocompletar departamento y puesto
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
    'tecnico_instrumentista'  => 'Técnico Instrumentista',
    'admin'                   => 'Administrador',
    'tecnico'                 => 'Técnico',
    'maestro'                 => 'Maestro',
];
$usuariosJson = json_encode(array_map(fn($u) => [
    'id'          => $u['id'],
    'nombre'      => $u['nombre'] . ' ' . $u['apellido'],
    'departamento'=> $u['departamento'] ?? '',
    'rol'         => $roleLabels[$u['rol']] ?? ucwords(str_replace('_', ' ', $u['rol'])),
    'activo'      => $u['activo'],
], $usuarios));
?>
<div class="row justify-content-center"><div class="col-lg-8">
<?php if ($error): ?>
<div class="alert alert-danger d-flex gap-2 mb-3">
  <i class="bi bi-exclamation-triangle-fill"></i><?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<div class="card">
  <div class="card-header"><i class="bi bi-file-earmark-plus me-2 text-success"></i>Nueva Asignación de Equipo</div>
  <div class="card-body p-4">
  <form method="POST" action="index.php?c=asignaciones&a=create">

    <!-- ── Datos del Responsable ── -->
    <h6 style="font-family:'Syne',sans-serif;font-weight:700;margin-bottom:1rem">
      <i class="bi bi-person me-2 text-primary"></i>Datos del Responsable
    </h6>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label">Nombre *</label>
        <select name="usuario_id" id="selUsuario" class="form-select" required>
          <option value="">— Selecciona un usuario —</option>
          <?php foreach ($usuarios as $u): if (!$u['activo']) continue; ?>
          <option value="<?= $u['id'] ?>"
                  data-depto="<?= htmlspecialchars($u['departamento'] ?? '') ?>"
                  data-rol="<?= htmlspecialchars($roleLabels[$u['rol']] ?? ucwords(str_replace('_', ' ', $u['rol']))) ?>"
                  <?= (($_POST['usuario_id'] ?? '') == $u['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Puesto</label>
        <input type="text" id="infoPuesto" class="form-control"
               style="background:#f7faff" readonly
               placeholder="Se llena al seleccionar usuario">
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label">Departamento</label>
        <input type="text" id="infoDepto" class="form-control"
               style="background:#f7faff" readonly
               placeholder="Se llena al seleccionar usuario">
      </div>
      <div class="col-md-6">
        <label class="form-label">Fecha de asignación</label>
        <input type="text" class="form-control" style="background:#f7faff" readonly
               value="<?= date('d/m/Y') ?>">
        <input type="hidden" name="fecha_asignacion" value="<?= date('Y-m-d') ?>">
      </div>
    </div>

    <!-- ── Datos del Contrato / Obra ── -->
    <hr>
    <h6 style="font-family:'Syne',sans-serif;font-weight:700;margin-bottom:1rem">
      <i class="bi bi-file-text me-2 text-warning"></i>Datos del Contrato
    </h6>

    <div class="row g-3 mb-3">
      <div class="col-md-8">
        <label class="form-label">Nombre de la Obra</label>
        <input type="text" name="nombre_obra" class="form-control"
               placeholder="Nombre del proyecto u obra"
               value="<?= htmlspecialchars($_POST['nombre_obra'] ?? '') ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">No. de Contrato</label>
        <input type="text" name="numero_contrato" class="form-control"
               placeholder="Ej. CTT-2025-001"
               value="<?= htmlspecialchars($_POST['numero_contrato'] ?? '') ?>">
      </div>
    </div>

    <!-- ── Equipo ── -->
    <hr>
    <h6 style="font-family:'Syne',sans-serif;font-weight:700;margin-bottom:1rem">
      <i class="bi bi-box-seam me-2 text-info"></i>Equipo a Asignar
    </h6>

    <div class="mb-3">
      <label class="form-label">Buscar equipo *</label>
      <div class="position-relative">
        <input type="text" id="buscarEquipo" class="form-control"
               placeholder="Escribe código, marca o modelo..." autocomplete="off"
               value="<?php
                 if ($equipoPresel) {
                     echo htmlspecialchars($equipoPresel['codigo'] . ' — ' . ($equipoPresel['marca'] ?? '') . ' ' . ($equipoPresel['modelo'] ?? ''));
                 } else {
                     echo htmlspecialchars($_POST['equipo_texto'] ?? '');
                 }
               ?>">
        <div id="listaEquipos" style="position:absolute;top:100%;left:0;right:0;z-index:999;background:#fff;border:1px solid #dde4ef;border-radius:0 0 10px 10px;max-height:220px;overflow-y:auto;display:none"></div>
      </div>
      <input type="hidden" name="equipo_id" id="hidEquipo"
             value="<?= $equipoPresel ? $equipoPresel['id'] : ($_POST['equipo_id'] ?? '') ?>">
    </div>

    <!-- ── Condición y Devolución ── -->
    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label">Condición de entrega</label>
        <select name="condicion_entrega" class="form-select">
          <?php foreach ($condiciones as $k => $c): ?>
          <option value="<?= $k ?>"><?= $c['label'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Devolución esperada</label>
        <input type="date" name="fecha_devolucion_esperada" class="form-control"
               value="<?= htmlspecialchars($_POST['fecha_devolucion_esperada'] ?? '') ?>">
      </div>
    </div>

    <div class="mb-4">
      <label class="form-label">Notas de entrega</label>
      <textarea name="notas_entrega" class="form-control" rows="2"
                placeholder="Observaciones sobre el estado o accesorios incluidos..."><?= htmlspecialchars($_POST['notas_entrega'] ?? '') ?></textarea>
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-success px-4">
        <i class="bi bi-check2 me-1"></i>Registrar Asignación
      </button>
      <a href="index.php?c=asignaciones" class="btn btn-outline-secondary px-4">Cancelar</a>
    </div>
  </form>
  </div>
</div>
</div></div>

<script>
// ── Autocompletar Departamento y Puesto al seleccionar usuario ──
document.getElementById('selUsuario')?.addEventListener('change', function () {
    const opt   = this.options[this.selectedIndex];
    const depto = opt.dataset.depto || '';
    const rol   = opt.dataset.rol   || '';
    document.getElementById('infoDepto').value  = depto;
    document.getElementById('infoPuesto').value = rol;
});

// Precargar si ya venía seleccionado (ej. error de validación)
(function () {
    const sel = document.getElementById('selUsuario');
    if (sel && sel.value) {
        const opt = sel.options[sel.selectedIndex];
        document.getElementById('infoDepto').value  = opt.dataset.depto || '';
        document.getElementById('infoPuesto').value = opt.dataset.rol   || '';
    }
})();

// ── Buscador de equipos ──
let timer;
const inp   = document.getElementById('buscarEquipo');
const lista = document.getElementById('listaEquipos');
const hid   = document.getElementById('hidEquipo');

inp.addEventListener('input', function () {
    clearTimeout(timer);
    const q = this.value.trim();
    if (q.length < 2) { lista.style.display = 'none'; return; }
    timer = setTimeout(() => {
        fetch('index.php?c=asignaciones&a=buscarEquipos&q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
                if (!data.length) { lista.style.display = 'none'; return; }
                lista.innerHTML = data.map(e => `
                    <div class="px-3 py-2 d-flex justify-content-between align-items-center eq-item"
                         style="cursor:pointer;font-size:.85rem;border-bottom:1px solid #f0f4f9;${!e.disponible ? 'opacity:.5' : ''}"
                         data-id="${e.id}" data-txt="${e.codigo} — ${e.nombre}">
                      <span><strong>${e.codigo}</strong> — ${e.nombre} <small class="text-muted">${e.categoria}</small></span>
                      <span class="badge bg-${e.disponible ? 'success' : 'danger'}">${e.disponible ? 'Disponible' : 'Asignado'}</span>
                    </div>`).join('');
                lista.style.display = 'block';
                lista.querySelectorAll('.eq-item').forEach(el => {
                    el.addEventListener('click', function () {
                        if (this.style.opacity === '0.5') return;
                        inp.value = this.dataset.txt;
                        hid.value = this.dataset.id;
                        lista.style.display = 'none';
                    });
                    el.addEventListener('mouseenter', () => el.style.background = '#f7faff');
                    el.addEventListener('mouseleave', () => el.style.background = '');
                });
            });
    }, 300);
});

document.addEventListener('click', e => {
    if (!lista.contains(e.target) && e.target !== inp) lista.style.display = 'none';
});
</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
