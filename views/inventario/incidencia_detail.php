<?php
$pageTitle = 'Incidencia: ' . $incidencia['folio'];
require __DIR__ . '/../layouts/header.php';
$estado = $estados[$incidencia['estado']] ?? ['label'=>$incidencia['estado'],'color'=>'secondary'];
$sev    = $severidades[$incidencia['severidad']] ?? ['label'=>$incidencia['severidad'],'color'=>'secondary'];
$tipo   = $tipos[$incidencia['tipo']] ?? ['label'=>$incidencia['tipo'],'icon'=>'bi-dot'];
?>

<div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
  <a href="index.php?c=inventario&a=detail&id=<?= $incidencia['equipo_id'] ?>" class="btn btn-sm btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i>Volver al equipo
  </a>
  <span class="folio-badge"><?= htmlspecialchars($incidencia['folio']) ?></span>
  <span class="badge bg-<?= $estado['color'] ?>"><?= $estado['label'] ?></span>
  <span class="badge bg-<?= $sev['color'] ?>">Severidad <?= $sev['label'] ?></span>
</div>

<?php if ($success): ?>
<div class="alert alert-success d-flex gap-2 mb-3">
  <i class="bi bi-check-circle-fill"></i>
  <?= $success === 'creada' ? 'Incidencia registrada correctamente.' : 'Incidencia actualizada correctamente.' ?>
</div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger d-flex gap-2 mb-3"><i class="bi bi-exclamation-triangle-fill"></i><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="card mb-3">
      <div class="card-header fw-bold">
        <i class="<?= $tipo['icon'] ?> me-2 text-danger"></i><?= htmlspecialchars($incidencia['titulo']) ?>
      </div>
      <div class="card-body">
        <div class="row g-2 mb-3" style="font-size:.88rem">
          <div class="col-sm-6"><span class="text-muted">Tipo:</span> <?= htmlspecialchars($tipo['label']) ?></div>
          <div class="col-sm-6"><span class="text-muted">Reportó:</span> <?= htmlspecialchars($incidencia['nombre_reporta']) ?></div>
          <div class="col-sm-6"><span class="text-muted">Creada:</span> <?= date('d/m/Y H:i', strtotime($incidencia['creado_en'])) ?></div>
          <div class="col-sm-6"><span class="text-muted">Actualizada:</span> <?= date('d/m/Y H:i', strtotime($incidencia['actualizado_en'])) ?></div>
          <?php if ($incidencia['cerrado_en']): ?>
          <div class="col-sm-6"><span class="text-muted">Cerrada:</span> <?= date('d/m/Y H:i', strtotime($incidencia['cerrado_en'])) ?></div>
          <?php endif; ?>
        </div>

        <div class="p-3 rounded-3 mb-3" style="background:#f7faff;border:1px solid #dde4ef">
          <div class="text-muted small fw-bold text-uppercase mb-1">Descripción</div>
          <div style="white-space:pre-wrap;line-height:1.6"><?= nl2br(htmlspecialchars($incidencia['descripcion'])) ?></div>
        </div>

        <?php if ($incidencia['notas_cierre']): ?>
        <div class="p-3 rounded-3" style="background:#d4edda;border:1px solid #b8dfc4">
          <div class="text-muted small fw-bold text-uppercase mb-1">Notas de cierre / solución</div>
          <div style="white-space:pre-wrap;line-height:1.6"><?= nl2br(htmlspecialchars($incidencia['notas_cierre'])) ?></div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="card">
      <div class="card-header fw-bold"><i class="bi bi-pencil-square me-2 text-primary"></i>Actualizar estado</div>
      <div class="card-body">
        <form method="POST">
          <div class="row g-3 mb-3">
            <div class="col-md-5">
              <label class="form-label">Estado</label>
              <select name="estado" class="form-select">
                <?php foreach ($estados as $k => $e): ?>
                <option value="<?= $k ?>" <?= $incidencia['estado'] === $k ? 'selected' : '' ?>><?= $e['label'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-7">
              <label class="form-label">Notas de cierre / seguimiento</label>
              <input type="text" name="notas_cierre" class="form-control"
                     value="<?= htmlspecialchars($incidencia['notas_cierre'] ?? '') ?>"
                     placeholder="Ej. Se reemplazó cable, pendiente refacción...">
            </div>
          </div>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i>Actualizar incidencia
          </button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card mb-3">
      <div class="card-header fw-bold"><i class="bi bi-box-seam me-2 text-primary"></i>Equipo</div>
      <div class="card-body" style="font-size:.88rem">
        <div class="mb-1"><span class="text-muted">Código:</span> <strong><?= htmlspecialchars($incidencia['equipo_codigo']) ?></strong></div>
        <div class="mb-1"><span class="text-muted">Categoría:</span> <?= htmlspecialchars($incidencia['categoria_nombre']) ?></div>
        <div class="mb-1"><span class="text-muted">Marca:</span> <?= htmlspecialchars($incidencia['equipo_marca'] ?? '—') ?></div>
        <div class="mb-1"><span class="text-muted">Modelo:</span> <?= htmlspecialchars($incidencia['equipo_modelo'] ?? '—') ?></div>
        <div class="mb-3"><span class="text-muted">Serie:</span> <?= htmlspecialchars($incidencia['equipo_serie'] ?? '—') ?></div>
        <a href="index.php?c=inventario&a=detail&id=<?= $incidencia['equipo_id'] ?>" class="btn btn-sm btn-outline-primary w-100">
          <i class="bi bi-eye me-1"></i>Ver ficha completa
        </a>
      </div>
    </div>

    <div class="card">
      <div class="card-header fw-bold"><i class="bi bi-images me-2 text-info"></i>Evidencias</div>
      <div class="card-body">
        <?php if (empty($imagenes)): ?>
        <p class="text-muted text-center small py-3 mb-0">Sin fotos adjuntas.</p>
        <?php else: ?>
        <div class="row g-2">
          <?php foreach ($imagenes as $img): ?>
          <div class="col-6">
            <a href="index.php?c=inventario&a=incidenciaImagen&img_id=<?= $img['id'] ?>" target="_blank">
              <img src="index.php?c=inventario&a=incidenciaImagen&img_id=<?= $img['id'] ?>"
                   class="img-fluid rounded border" style="width:100%;aspect-ratio:1;object-fit:cover"
                   alt="Evidencia <?= $img['id'] ?>">
            </a>
            <div class="text-muted text-center" style="font-size:.68rem;margin-top:2px">
              <?= \IncidenciaEquipo::formatBytes((int)$img['tamano']) ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
