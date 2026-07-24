<?php
$pageTitle = 'Nueva Incidencia';
require __DIR__ . '/../layouts/header.php';
?>
<div class="row justify-content-center"><div class="col-lg-8">
  <div class="d-flex align-items-center gap-2 mb-3">
    <a href="index.php?c=inventario&a=detail&id=<?= $equipo['id'] ?>" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>Volver al equipo
    </a>
    <span class="folio-badge"><?= htmlspecialchars($equipo['codigo']) ?></span>
  </div>

  <?php if ($error): ?>
  <div class="alert alert-danger d-flex gap-2 mb-3">
    <i class="bi bi-exclamation-triangle-fill"></i><?= htmlspecialchars($error) ?>
  </div>
  <?php endif; ?>

  <div class="card">
    <div class="card-header">
      <i class="bi bi-exclamation-triangle me-2 text-danger"></i>Registrar incidencia del equipo
    </div>
    <div class="card-body p-4">
      <div class="mb-4 p-3 rounded-3" style="background:#f7faff;border:1px solid #dde4ef">
        <div style="font-size:.78rem;color:#9aafca;text-transform:uppercase;font-weight:700">Equipo</div>
        <div style="font-weight:800"><?= htmlspecialchars($equipo['codigo']) ?></div>
        <div style="font-size:.88rem;color:#6b7c93">
          <?= htmlspecialchars($equipo['categoria_nombre']) ?> ·
          <?= htmlspecialchars(trim(($equipo['marca'] ?? '') . ' ' . ($equipo['modelo'] ?? '')) ?: 'Sin marca/modelo') ?>
        </div>
      </div>

      <form method="POST" action="index.php?c=inventario&a=incidenciaCreate" enctype="multipart/form-data">
        <input type="hidden" name="equipo_id" value="<?= $equipo['id'] ?>">

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label">Tipo *</label>
            <select name="tipo" class="form-select" required>
              <?php foreach ($tipos as $k => $t): ?>
              <option value="<?= $k ?>" <?= (($_POST['tipo'] ?? 'averia') === $k) ? 'selected' : '' ?>>
                <?= $t['label'] ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Severidad *</label>
            <select name="severidad" class="form-select" required>
              <?php foreach ($severidades as $k => $s): ?>
              <option value="<?= $k ?>" <?= (($_POST['severidad'] ?? 'media') === $k) ? 'selected' : '' ?>>
                <?= $s['label'] ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Título *</label>
          <input type="text" name="titulo" class="form-control" maxlength="180"
                 placeholder="Ej. Pantalla rota, no enciende, falla de red..."
                 value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Descripción del problema *</label>
          <textarea name="descripcion" class="form-control" rows="5" required
                    placeholder="Describe qué presenta el equipo, cuándo ocurrió, síntomas, ubicación, pruebas realizadas..."><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Actualizar estado del equipo</label>
          <select name="estado_equipo" class="form-select">
            <option value="">Mantener estado actual</option>
            <?php foreach ($estadosEquipo as $k => $e): ?>
            <option value="<?= $k ?>" <?= (($_POST['estado_equipo'] ?? '') === $k) ? 'selected' : '' ?>>
              <?= $e['label'] ?>
            </option>
            <?php endforeach; ?>
          </select>
          <div class="form-text">Úsalo si esta incidencia debe reflejarse en inventario, por ejemplo como Dañado o En reparación.</div>
        </div>

        <div class="mb-4">
          <label class="form-label">Fotos / evidencia <span class="text-muted">(máx. <?= \IncidenciaEquipo::MAX_IMAGENES ?> imágenes, 5 MB c/u)</span></label>
          <input type="file" name="evidencias[]" class="form-control" multiple
                 accept="image/jpeg,image/png,image/webp">
          <div class="form-text">Puedes subir fotos de la falla, etiqueta del equipo, pantalla, golpe, cables, etc.</div>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-danger px-4">
            <i class="bi bi-save me-1"></i>Guardar incidencia
          </button>
          <a href="index.php?c=inventario&a=detail&id=<?= $equipo['id'] ?>" class="btn btn-outline-secondary px-4">Cancelar</a>
        </div>
      </form>
    </div>
  </div>
</div></div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
