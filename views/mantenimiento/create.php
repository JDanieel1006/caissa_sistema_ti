<?php
$pageTitle = 'Programar Mantenimiento';
require __DIR__ . '/../layouts/header.php';
?>
<div class="row justify-content-center"><div class="col-lg-8">
<?php if ($error): ?>
<div class="alert alert-danger d-flex gap-2 mb-3">
    <i class="bi bi-exclamation-triangle-fill"></i><?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>
<div class="card">
    <div class="card-header"><i class="bi bi-tools me-2 text-primary"></i>Programar Mantenimiento</div>
    <div class="card-body p-4">
    <form method="POST" action="index.php?c=mantenimiento&a=create">

        <!-- Paso 1: Usuario -->
        <div class="mb-3">
            <label class="form-label">Usuario responsable del equipo *</label>
            <select name="usuario_id" id="selUsuario" class="form-select" required>
                <option value="">— Selecciona un usuario —</option>
                <?php foreach ($usuarios as $u): ?>
                <option value="<?= $u['id'] ?>"
                        <?= (($_POST['usuario_id'] ?? '') == $u['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?>
                    <?php if ($u['departamento']): ?>
                        — <?= htmlspecialchars($u['departamento']) ?>
                    <?php endif; ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Paso 2: Equipos asignados al usuario -->
        <div class="mb-3" id="seccionEquipos" style="display:<?= !empty($_POST['usuario_id']) ? 'block' : 'none' ?>">
            <label class="form-label">Equipo a dar mantenimiento *</label>
            <select name="equipo_id" id="selEquipo" class="form-select" required>
                <option value="">— Selecciona un equipo —</option>
                <?php if (!empty($equiposUsuario)): ?>
                    <?php foreach ($equiposUsuario as $eq): ?>
                    <option value="<?= $eq['equipo_id'] ?>"
                            <?= (($_POST['equipo_id'] ?? '') == $eq['equipo_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($eq['equipo_codigo']) ?>
                        <?php if ($eq['equipo_marca'] || $eq['equipo_modelo']): ?>
                            — <?= htmlspecialchars(trim(($eq['equipo_marca'] ?? '') . ' ' . ($eq['equipo_modelo'] ?? ''))) ?>
                        <?php endif; ?>
                        (<?= htmlspecialchars($eq['categoria_nombre']) ?>)
                    </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <div id="sinEquipos" class="form-text text-danger" style="display:none">
                <i class="bi bi-exclamation-circle me-1"></i>Este usuario no tiene equipos asignados actualmente.
            </div>
        </div>

        <!-- Tipo y Fecha -->
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Tipo *</label>
                <select name="tipo" class="form-select">
                    <?php foreach ($tipos as $k => $t): ?>
                    <option value="<?= $k ?>" <?= (($_POST['tipo'] ?? 'preventivo') === $k) ? 'selected' : '' ?>>
                        <?= $t['label'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Fecha programada *</label>
                <input type="date" name="fecha_programada" class="form-control"
                       value="<?= htmlspecialchars($_POST['fecha_programada'] ?? '') ?>" required>
            </div>
        </div>

        <!-- Descripción -->
        <div class="mb-4">
            <label class="form-label">Descripción / Actividades a realizar</label>
            <textarea name="descripcion" class="form-control" rows="4"
                      placeholder="Describe las actividades de mantenimiento que se realizarán..."><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-calendar-check me-1"></i>Programar
            </button>
            <a href="index.php?c=mantenimiento" class="btn btn-outline-secondary px-4">Cancelar</a>
        </div>
    </form>
    </div>
</div>
</div></div>

<script>
document.getElementById('selUsuario')?.addEventListener('change', function () {
    const uid = this.value;
    const sec = document.getElementById('seccionEquipos');
    const sel = document.getElementById('selEquipo');
    const msg = document.getElementById('sinEquipos');

    if (!uid) { sec.style.display = 'none'; return; }

    fetch('index.php?c=mantenimiento&a=equiposPorUsuario&usuario_id=' + uid)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">— Selecciona un equipo —</option>';
            if (!data.length) {
                sec.style.display = 'block';
                msg.style.display = 'block';
                sel.style.display = 'none';
            } else {
                msg.style.display = 'none';
                sel.style.display = 'block';
                data.forEach(eq => {
                    const opt = document.createElement('option');
                    opt.value = eq.equipo_id;
                    opt.textContent = eq.equipo_codigo + ' — ' + eq.nombre + ' (' + eq.categoria + ')';
                    sel.appendChild(opt);
                });
                sec.style.display = 'block';
            }
        });
});
</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
