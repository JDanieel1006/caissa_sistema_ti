<?php
$pageTitle = 'Asignacion: ' . $asig['folio'];
require __DIR__ . '/../layouts/header.php';

$est = $estados[$asig['estado']] ?? ['label' => $asig['estado'], 'color' => 'secondary'];
$cond = $condiciones[$asig['condicion_entrega']] ?? ['label' => $asig['condicion_entrega'], 'color' => 'secondary'];
$vencida = $asig['estado'] === 'activa'
    && $asig['fecha_devolucion_esperada']
    && strtotime($asig['fecha_devolucion_esperada']) < time();
?>
<?php if ($success): ?>
<?php
$successText = match ($success) {
    'creada'          => 'Asignacion registrada correctamente.',
    'devuelta'        => 'Devolucion registrada.',
    'cancelada'       => 'Asignacion cancelada.',
    'comentario'      => 'Comentario agregado al historial.',
    'email_reenviado' => 'Correo de asignacion reenviado correctamente.',
    'email_error'     => 'No se pudo reenviar el correo. Revisa la configuracion SMTP o el log del sistema.',
    default           => $success,
};
$successClass = $success === 'email_error' ? 'alert-warning' : 'alert-success';
$successIcon  = $success === 'email_error' ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill';
?>
<div class="alert <?= $successClass ?> d-flex gap-2 mb-3">
  <i class="bi <?= $successIcon ?>"></i><?= htmlspecialchars($successText) ?>
</div>
<?php endif; ?>

<?php if (!empty($error)): ?>
<div class="alert alert-danger d-flex gap-2 mb-3">
  <i class="bi bi-exclamation-triangle-fill"></i><?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<?php if ($vencida): ?>
<div class="alert alert-danger d-flex gap-2 mb-3">
  <i class="bi bi-exclamation-circle-fill"></i>
  Esta asignacion esta <strong>vencida</strong> - la fecha de devolucion esperada ya paso.
</div>
<?php endif; ?>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-body p-4">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
          <div>
            <span class="folio-badge me-2"><?= htmlspecialchars($asig['folio']) ?></span>
            <span class="badge bg-<?= $est['color'] ?>"><?= $est['label'] ?></span>
          </div>
          <div class="d-flex gap-2 flex-wrap">
            <a href="index.php?c=asignaciones&a=acta&id=<?= $asig['id'] ?>" target="_blank" class="btn btn-sm btn-outline-danger">
              <i class="bi bi-file-pdf me-1"></i>Acta
            </a>
            <a href="index.php?c=asignaciones&a=contrato&id=<?= $asig['id'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
              <i class="bi bi-file-earmark-text me-1"></i>Contrato
            </a>
            <form method="POST" action="index.php?c=asignaciones&a=reenviarEmail" class="d-inline" onsubmit="return confirm('Reenviar el correo de asignacion a <?= htmlspecialchars($asig['email_usuario'], ENT_QUOTES) ?>?')">
              <input type="hidden" name="id" value="<?= $asig['id'] ?>">
              <button type="submit" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-envelope-arrow-up me-1"></i>Reenviar email
              </button>
            </form>
            <a href="index.php?c=asignaciones" class="btn btn-sm btn-outline-secondary">
              <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-sm-6">
            <div class="p-3 rounded-3" style="background:#f7faff;border:1px solid #dde4ef">
              <div style="font-size:.72rem;color:#9aafca;text-transform:uppercase;font-weight:700;margin-bottom:.3rem">Equipo</div>
              <div style="font-weight:700"><?= htmlspecialchars($asig['equipo_codigo']) ?></div>
              <div style="font-size:.83rem;color:#6b7c93">
                <?= htmlspecialchars($asig['categoria_nombre']) ?> &middot;
                <?= htmlspecialchars(trim(($asig['equipo_marca'] ?? '') . ' ' . ($asig['equipo_modelo'] ?? '')) ?: $asig['equipo_codigo']) ?>
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="p-3 rounded-3" style="background:#f7faff;border:1px solid #dde4ef">
              <div style="font-size:.72rem;color:#9aafca;text-transform:uppercase;font-weight:700;margin-bottom:.3rem">Asignado a</div>
              <div style="font-weight:700"><?= htmlspecialchars($asig['nombre_usuario']) ?></div>
              <div style="font-size:.83rem;color:#6b7c93"><?= htmlspecialchars($asig['email_usuario']) ?></div>
            </div>
          </div>
        </div>

        <?php if ($asig['notas_entrega']): ?>
        <div class="mt-3 p-3 rounded-3" style="background:#f7faff;border:1px solid #dde4ef;font-size:.87rem">
          <strong>Notas de entrega:</strong><br><?= htmlspecialchars($asig['notas_entrega']) ?>
        </div>
        <?php endif; ?>

        <?php if ($asig['estado'] === 'devuelta' && $asig['notas_devolucion']): ?>
        <div class="mt-2 p-3 rounded-3" style="background:#d4edda;border:1px solid #b8dfc4;font-size:.87rem">
          <strong>Notas de devolucion:</strong><br><?= htmlspecialchars($asig['notas_devolucion']) ?>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <i class="bi bi-chat-left-text me-2 text-primary"></i>Comentarios / Seguimiento
      </div>
      <div class="card-body p-4">
        <form method="POST" class="mb-4">
          <input type="hidden" name="action" value="comentar">
          <label class="form-label">Nuevo comentario</label>
          <textarea name="comentario" class="form-control mb-2" rows="3" placeholder="Ej. Se entrego cargador adicional, usuario reporta falla intermitente, pendiente revision, etc."></textarea>
          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-plus-circle me-1"></i>Agregar comentario
            </button>
          </div>
        </form>

        <?php if (empty($comentarios)): ?>
        <div class="text-center py-4 text-muted" style="border:1px dashed #d8e1ec;border-radius:14px;background:#f8fbff">
          <i class="bi bi-chat-square-dots" style="font-size:2rem"></i>
          <div class="mt-2">Aun no hay comentarios registrados para esta asignacion.</div>
        </div>
        <?php else: ?>
        <div class="d-flex flex-column gap-3">
          <?php foreach ($comentarios as $comentario): ?>
          <div class="comment-bubble">
            <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
              <div>
                <strong><?= htmlspecialchars($comentario['nombre_usuario']) ?></strong>
                <span class="text-muted small"> &middot; <?= htmlspecialchars($comentario['rol_usuario']) ?></span>
              </div>
              <span class="text-muted small">
                <i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($comentario['creado_en'])) ?>
              </span>
            </div>
            <div style="white-space:pre-line"><?= htmlspecialchars($comentario['comentario']) ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($asig['estado'] === 'activa'): ?>
    <div class="card">
      <div class="card-header">
        <i class="bi bi-arrow-return-left me-2 text-success"></i>Registrar Devolucion
      </div>
      <div class="card-body p-4">
        <form method="POST">
          <input type="hidden" name="action" value="devolver">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Condicion al devolver</label>
              <select name="condicion_devolucion" class="form-select">
                <?php foreach ($condiciones as $k => $c): ?>
                <option value="<?= $k ?>"><?= $c['label'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha real de devolucion</label>
              <input type="date" name="fecha_devolucion_real" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Notas de devolucion</label>
            <textarea name="notas_devolucion" class="form-control" rows="2" placeholder="Observaciones sobre el estado en que fue devuelto..."></textarea>
          </div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success px-4">
              <i class="bi bi-check2 me-1"></i>Confirmar Devolucion
            </button>
            <button type="submit" form="fCancelar" class="btn btn-outline-danger">
              <i class="bi bi-x me-1"></i>Cancelar Asignacion
            </button>
          </div>
        </form>
        <form method="POST" id="fCancelar" onsubmit="return confirm('Cancelar esta asignacion?')">
          <input type="hidden" name="action" value="cancelar">
        </form>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <div class="col-lg-4">
    <div class="card">
      <div class="card-header"><i class="bi bi-calendar me-2"></i>Fechas</div>
      <div class="card-body p-3">
        <?php foreach ([
          ['Asignacion', date('d/m/Y', strtotime($asig['fecha_asignacion']))],
          ['Dev. Esperada', $asig['fecha_devolucion_esperada'] ? date('d/m/Y', strtotime($asig['fecha_devolucion_esperada'])) : 'No definida'],
          ['Dev. Real', $asig['fecha_devolucion_real'] ? date('d/m/Y', strtotime($asig['fecha_devolucion_real'])) : 'Pendiente'],
          ['Entregado por', $asig['nombre_admin']],
        ] as [$k, $v]): ?>
        <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid #f0f4f9;font-size:.84rem">
          <span style="color:#6b7c93;font-weight:500"><?= $k ?></span>
          <span><?= htmlspecialchars($v) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
