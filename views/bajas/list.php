<?php
$pageTitle = 'Bajas de Equipo';
require __DIR__ . '/../layouts/header.php';
?>
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
  <div>
    <h5 class="mb-0 fw-bold">Bajas de Equipo</h5>
    <small class="text-muted">Registro de equipos dados de baja</small>
  </div>
  <a href="index.php?c=bajas&a=create" class="btn btn-danger btn-sm">
    <i class="bi bi-trash3 me-1"></i>Registrar Baja
  </a>
</div>

<?php if ($success): ?>
<div class="alert alert-success d-flex gap-2 mb-3">
  <i class="bi bi-check-circle-fill mt-1"></i>Baja registrada correctamente.
</div>
<?php endif; ?>

<!-- Stats -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="card text-center"><div class="card-body py-3">
      <div class="fw-bold fs-4"><?= $stats['total'] ?></div>
      <small class="text-muted">Total bajas</small>
    </div></div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center"><div class="card-body py-3">
      <div class="fw-bold fs-4 text-danger"><?= $stats['este_anio'] ?></div>
      <small class="text-muted">Este año</small>
    </div></div>
  </div>
  <?php foreach (array_slice($stats['por_motivo'], 0, 2) as $pm): ?>
  <div class="col-6 col-md-3">
    <div class="card text-center"><div class="card-body py-3">
      <div class="fw-bold fs-4"><?= $pm['total'] ?></div>
      <small class="text-muted"><?= htmlspecialchars($motivos[$pm['motivo']]['label'] ?? $pm['motivo']) ?></small>
    </div></div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Filtros -->
<div class="card mb-3">
  <div class="card-body p-3">
  <form method="GET" action="index.php" class="row g-2 align-items-end">
    <input type="hidden" name="c" value="bajas">
    <div class="col-md-4">
      <input type="text" name="buscar" class="form-control form-control-sm"
             placeholder="Buscar folio, código, marca..." value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>">
    </div>
    <div class="col-md-4">
      <select name="motivo" class="form-select form-select-sm">
        <option value="">— Todos los motivos —</option>
        <?php foreach ($motivos as $k => $m): ?>
        <option value="<?= $k ?>" <?= ($_GET['motivo'] ?? '') === $k ? 'selected' : '' ?>><?= $m['label'] ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-auto d-flex gap-2">
      <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filtrar</button>
      <a href="index.php?c=bajas" class="btn btn-outline-secondary btn-sm">Limpiar</a>
    </div>
  </form>
  </div>
</div>

<!-- Tabla -->
<div class="card">
  <div class="card-body p-0">
  <div class="table-responsive">
  <table class="table table-hover mb-0 align-middle" id="tblBajas">
    <thead class="table-dark">
      <tr>
        <th>Folio</th>
        <th>Equipo</th>
        <th>Categoría</th>
        <th>Motivo</th>
        <th>Asign. cancelada</th>
        <th>Fecha</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($bajas)): ?>
      <tr><td colspan="7" class="text-center py-4 text-muted">No hay registros de bajas.</td></tr>
      <?php else: foreach ($bajas as $b):
        $mot = $motivos[$b['motivo']] ?? ['label'=>$b['motivo'],'icon'=>'bi-dash','color'=>'secondary'];
      ?>
      <tr>
        <td><span class="badge bg-dark font-monospace"><?= htmlspecialchars($b['folio']) ?></span></td>
        <td>
          <div class="fw-semibold"><?= htmlspecialchars($b['equipo_codigo']) ?></div>
          <small class="text-muted"><?= htmlspecialchars(trim(($b['equipo_marca']??'').' '.($b['equipo_modelo']??''))) ?></small>
        </td>
        <td><i class="<?= htmlspecialchars($b['categoria_icono']??'bi-box') ?> me-1"></i><?= htmlspecialchars($b['categoria_nombre']) ?></td>
        <td>
          <span class="badge bg-<?= $mot['color'] ?>">
            <i class="<?= $mot['icon'] ?> me-1"></i><?= $mot['label'] ?>
          </span>
        </td>
        <td class="text-center">
          <?php if ($b['tenia_asignacion']): ?>
          <span class="badge bg-warning text-dark"><i class="bi bi-check me-1"></i>Sí</span>
          <?php else: ?>
          <span class="text-muted small">No</span>
          <?php endif; ?>
        </td>
        <td><?= date('d/m/Y', strtotime($b['creado_en'])) ?></td>
        <td>
          <a href="index.php?c=bajas&a=detail&id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-eye"></i>
          </a>
        </td>
      </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
  </div></div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
