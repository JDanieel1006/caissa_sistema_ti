<?php
$pageTitle = 'Registrar Baja';
require __DIR__ . '/../layouts/header.php';
?>
<div class="row justify-content-center"><div class="col-lg-7">
<?php if ($error): ?>
<div class="alert alert-danger d-flex gap-2 mb-3">
  <i class="bi bi-exclamation-triangle-fill"></i><?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>
<div class="card">
  <div class="card-header"><i class="bi bi-trash3 me-2 text-danger"></i>Registrar Baja de Equipo</div>
  <div class="card-body p-4">
  <form method="POST" action="index.php?c=bajas&a=create" enctype="multipart/form-data">

    <!-- Búsqueda de equipo -->
    <div class="mb-3">
      <label class="form-label">Equipo *</label>
      <?php if ($equipoPresel): ?>
      <input type="hidden" name="equipo_id" value="<?= $equipoPresel['id'] ?>">
      <div class="form-control bg-light">
        <i class="<?= htmlspecialchars($equipoPresel['categoria_icono']??'bi-box') ?> me-2 text-muted"></i>
        <strong><?= htmlspecialchars($equipoPresel['codigo']) ?></strong> —
        <?= htmlspecialchars(trim(($equipoPresel['equipo_marca']??$equipoPresel['marca']??'').' '.($equipoPresel['equipo_modelo']??$equipoPresel['modelo']??''))) ?>
        (<?= htmlspecialchars($equipoPresel['categoria_nombre']) ?>)
      </div>
      <?php else: ?>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" id="buscarEquipo" class="form-control" placeholder="Buscar por código, marca o modelo...">
      </div>
      <input type="hidden" name="equipo_id" id="equipoId" value="<?= htmlspecialchars($_POST['equipo_id'] ?? '') ?>">
      <div id="resultadosEquipo" class="list-group mt-1" style="display:none;position:absolute;z-index:999;width:auto"></div>
      <div id="equipoSeleccionado" class="mt-2" style="display:none">
        <div class="alert alert-warning py-2 mb-0 d-flex align-items-center justify-content-between">
          <span><i class="bi bi-box-seam me-2"></i><span id="equipoNombre"></span></span>
          <button type="button" class="btn-close btn-sm" onclick="limpiarEquipo()"></button>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Motivo -->
    <div class="mb-3">
      <label class="form-label">Motivo de baja *</label>
      <div class="row g-2">
        <?php foreach ($motivos as $k => $m): ?>
        <div class="col-6">
          <input type="radio" class="btn-check" name="motivo" id="mot_<?= $k ?>" value="<?= $k ?>"
                 <?= (($_POST['motivo'] ?? '') === $k) ? 'checked' : '' ?> required>
          <label class="btn btn-outline-secondary w-100 text-start" for="mot_<?= $k ?>">
            <i class="<?= $m['icon'] ?> me-2"></i><?= $m['label'] ?>
          </label>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Descripción -->
    <div class="mb-3">
      <label class="form-label">Descripción / Detalles</label>
      <textarea name="descripcion" class="form-control" rows="3"
                placeholder="Describe el estado del equipo, circunstancias, etc."><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
    </div>

    <!-- Evidencias -->
    <div class="mb-4">
      <label class="form-label">Evidencia fotográfica <span class="text-muted">(máx. 3 imágenes)</span></label>
      <div class="row g-3" id="previews">
        <?php for ($i = 0; $i < 3; $i++): ?>
        <div class="col-4">
          <label class="upload-box" for="file<?= $i ?>">
            <input type="file" name="evidencias[]" id="file<?= $i ?>" class="d-none"
                   accept="image/jpeg,image/png,image/webp" onchange="previewImg(this, <?= $i ?>)">
            <div class="upload-placeholder" id="ph<?= $i ?>">
              <i class="bi bi-camera fs-2 text-muted"></i>
              <div class="text-muted small mt-1">Imagen <?= $i+1 ?></div>
            </div>
            <img id="prev<?= $i ?>" src="" alt="" class="upload-preview" style="display:none">
          </label>
          <div class="text-center mt-1" id="delBtn<?= $i ?>" style="display:none">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="quitarImg(<?= $i ?>)">
              <i class="bi bi-x"></i> Quitar
            </button>
          </div>
        </div>
        <?php endfor; ?>
      </div>
    </div>

    <div class="alert alert-danger d-flex gap-2 mb-4" style="font-size:.87rem">
      <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
      <span>Esta acción es irreversible. El equipo cambiará a estado <strong>Dado de Baja</strong> y cualquier asignación activa será cancelada automáticamente.</span>
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-danger px-4"><i class="bi bi-trash3 me-1"></i>Registrar Baja</button>
      <a href="index.php?c=bajas" class="btn btn-outline-secondary px-4">Cancelar</a>
    </div>
  </form>
  </div>
</div>
</div></div>

<style>
.upload-box { display:block; border:2px dashed #dee2e6; border-radius:10px; cursor:pointer; overflow:hidden; aspect-ratio:1; position:relative; transition:.2s; }
.upload-box:hover { border-color:#0077ff; background:#f7faff; }
.upload-placeholder { display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; }
.upload-preview { width:100%; height:100%; object-fit:cover; }
</style>

<script>
function previewImg(input, i) {
    if (!input.files[0]) return;
    const url = URL.createObjectURL(input.files[0]);
    document.getElementById('ph' + i).style.display   = 'none';
    document.getElementById('prev' + i).src            = url;
    document.getElementById('prev' + i).style.display  = 'block';
    document.getElementById('delBtn' + i).style.display= 'block';
}
function quitarImg(i) {
    document.getElementById('file' + i).value           = '';
    document.getElementById('prev' + i).style.display   = 'none';
    document.getElementById('ph' + i).style.display     = 'flex';
    document.getElementById('delBtn' + i).style.display = 'none';
}

// Búsqueda de equipo
let searchTimer;
document.getElementById('buscarEquipo')?.addEventListener('input', function () {
    clearTimeout(searchTimer);
    const q = this.value.trim();
    if (q.length < 2) { document.getElementById('resultadosEquipo').style.display = 'none'; return; }
    searchTimer = setTimeout(() => {
        fetch('index.php?c=bajas&a=buscarEquipos&q=' + encodeURIComponent(q))
            .then(r => r.json()).then(data => {
                const box = document.getElementById('resultadosEquipo');
                if (!data.length) { box.style.display = 'none'; return; }
                box.innerHTML = data.map(eq =>
                    `<button type="button" class="list-group-item list-group-item-action" onclick="selEquipo(${eq.id},'${eq.codigo} — ${eq.nombre} (${eq.categoria})')">
                        <strong>${eq.codigo}</strong> — ${eq.nombre} <small class="text-muted">${eq.categoria}</small>
                    </button>`
                ).join('');
                box.style.display = 'block';
            });
    }, 300);
});
function selEquipo(id, nombre) {
    document.getElementById('equipoId').value           = id;
    document.getElementById('equipoNombre').textContent = nombre;
    document.getElementById('equipoSeleccionado').style.display = 'block';
    document.getElementById('resultadosEquipo').style.display   = 'none';
    document.getElementById('buscarEquipo').value = '';
}
function limpiarEquipo() {
    document.getElementById('equipoId').value           = '';
    document.getElementById('equipoSeleccionado').style.display = 'none';
}
</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
