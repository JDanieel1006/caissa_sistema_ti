<?php
$pageTitle = 'Detalle de Baja';
require __DIR__ . '/../layouts/header.php';
$mot = $motivos[$baja['motivo']] ?? ['label'=>$baja['motivo'],'icon'=>'bi-dash','color'=>'secondary'];
?>
<?php if ($success): ?>
<div class="alert alert-success d-flex gap-2 mb-3">
  <i class="bi bi-check-circle-fill mt-1"></i>Baja registrada correctamente.
</div>
<?php endif; ?>

<div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
  <a href="index.php?c=bajas" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a>
  <div>
    <span class="badge bg-dark font-monospace fs-6"><?= htmlspecialchars($baja['folio']) ?></span>
  </div>
  <span class="badge bg-<?= $mot['color'] ?> fs-6">
    <i class="<?= $mot['icon'] ?> me-1"></i><?= $mot['label'] ?>
  </span>
  <?php if ($baja['tenia_asignacion']): ?>
  <span class="badge bg-warning text-dark"><i class="bi bi-person-x me-1"></i>Asignación cancelada</span>
  <?php endif; ?>
</div>

<div class="row g-3">

  <!-- Info principal -->
  <div class="col-lg-7">
    <div class="card mb-3">
      <div class="card-header fw-bold"><i class="bi bi-box-seam me-2 text-primary"></i>Equipo dado de baja</div>
      <div class="card-body">
        <div class="row g-2" style="font-size:.88rem">
          <div class="col-6"><span class="text-muted">Código:</span> <strong><?= htmlspecialchars($baja['equipo_codigo']) ?></strong></div>
          <div class="col-6"><span class="text-muted">Categoría:</span> <?= htmlspecialchars($baja['categoria_nombre']) ?></div>
          <div class="col-6"><span class="text-muted">Marca:</span> <?= htmlspecialchars($baja['equipo_marca'] ?? '—') ?></div>
          <div class="col-6"><span class="text-muted">Modelo:</span> <?= htmlspecialchars($baja['equipo_modelo'] ?? '—') ?></div>
          <div class="col-6"><span class="text-muted">Serie:</span> <?= htmlspecialchars($baja['equipo_serie'] ?? '—') ?></div>
          <div class="col-6"><span class="text-muted">Estado anterior:</span>
            <span class="badge bg-secondary"><?= htmlspecialchars($baja['estado_anterior']) ?></span>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header fw-bold"><i class="bi bi-info-circle me-2 text-warning"></i>Detalles de la baja</div>
      <div class="card-body" style="font-size:.88rem">
        <div class="mb-2"><span class="text-muted">Registrado por:</span> <?= htmlspecialchars($baja['nombre_creador']) ?></div>
        <div class="mb-2"><span class="text-muted">Fecha:</span> <?= date('d/m/Y H:i', strtotime($baja['creado_en'])) ?></div>
        <?php if ($baja['descripcion']): ?>
        <div class="mt-3 p-3 rounded-3 bg-light border">
          <div class="text-muted small fw-bold mb-1 text-uppercase">Descripción</div>
          <div style="white-space:pre-wrap;line-height:1.6"><?= nl2br(htmlspecialchars($baja['descripcion'])) ?></div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Ver equipo -->
    <a href="index.php?c=inventario&a=detail&id=<?= $baja['equipo_id'] ?>" class="btn btn-sm btn-outline-primary mb-3">
      <i class="bi bi-box-seam me-1"></i>Ver ficha del equipo
    </a>
  </div>

  <!-- Evidencias -->
  <div class="col-lg-5">
    <div class="card">
      <div class="card-header fw-bold"><i class="bi bi-images me-2 text-info"></i>Evidencia fotográfica</div>
      <div class="card-body">
        <?php if (empty($imagenes)): ?>
        <p class="text-muted small text-center py-3">No se adjuntó evidencia fotográfica.</p>
        <?php else: ?>
        <div class="row g-2">
          <?php foreach ($imagenes as $img): ?>
          <div class="col-<?= count($imagenes) == 1 ? '12' : (count($imagenes) == 2 ? '6' : '4') ?>">
            <a href="index.php?c=bajas&a=imagen&img_id=<?= $img['id'] ?>" target="_blank">
              <img src="index.php?c=bajas&a=imagen&img_id=<?= $img['id'] ?>"
                   class="img-fluid rounded border" style="width:100%;aspect-ratio:1;object-fit:cover"
                   alt="Evidencia <?= $img['id'] ?>">
            </a>
            <div class="text-muted text-center" style="font-size:.7rem;margin-top:2px">
              <?= htmlspecialchars($img['nombre_original']) ?>
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
