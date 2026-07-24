<?php
$pageTitle = 'Equipo: ' . $equipo['codigo'];
require __DIR__ . '/../layouts/header.php';
$est = $estados[$equipo['estado']] ?? ['label' => $equipo['estado'], 'color' => 'secondary'];
?>
<?php if ($success): ?>
<div class="alert alert-success d-flex gap-2 mb-3"><i class="bi bi-check-circle-fill"></i><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger d-flex gap-2 mb-3"><i class="bi bi-exclamation-triangle-fill"></i><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="row g-4">
  <div class="col-lg-8">

    <!-- Galería -->
    <div class="card mb-4">
      <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-images me-2 text-primary"></i>Galería (<?= count($imagenes) ?>/<?= \ImagenEquipo::MAX_IMAGENES ?>)</span>
      </div>
      <div class="card-body p-3">
        <?php if (!empty($imagenes)): ?>
        <div class="row g-2 mb-3">
          <?php foreach ($imagenes as $img): ?>
          <div class="col-4 col-md-3">
            <div class="position-relative" style="border-radius:10px;overflow:hidden;border:2px solid <?= $img['es_principal'] ? '#0077ff' : '#dde4ef' ?>">
              <a href="index.php?c=inventario&a=imagen&img_id=<?= $img['id'] ?>" target="_blank">
                <img src="index.php?c=inventario&a=imagen&img_id=<?= $img['id'] ?>&thumb=1"
                     style="width:100%;height:80px;object-fit:cover" loading="lazy">
              </a>
              <div class="d-flex gap-1 p-1" style="background:#f7faff">
                <?php if (!$img['es_principal']): ?>
                <form method="POST" style="flex:1">
                  <input type="hidden" name="action" value="set_principal">
                  <input type="hidden" name="imagen_id" value="<?= $img['id'] ?>">
                  <button class="btn btn-xs btn-outline-primary w-100" style="font-size:.68rem;padding:2px 4px" title="Principal"><i class="bi bi-star"></i></button>
                </form>
                <?php else: ?>
                <span style="font-size:.68rem;padding:2px 4px;color:#0077ff;font-weight:700;text-align:center;flex:1"><i class="bi bi-star-fill"></i></span>
                <?php endif; ?>
                <form method="POST" onsubmit="return confirm('¿Eliminar imagen?')">
                  <input type="hidden" name="action" value="eliminar_imagen">
                  <input type="hidden" name="imagen_id" value="<?= $img['id'] ?>">
                  <button class="btn btn-xs btn-outline-danger" style="font-size:.68rem;padding:2px 6px"><i class="bi bi-trash"></i></button>
                </form>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php if (count($imagenes) < \ImagenEquipo::MAX_IMAGENES): ?>
        <form method="POST" enctype="multipart/form-data" class="d-flex gap-2 align-items-center flex-wrap">
          <input type="hidden" name="action" value="subir_imagenes">
          <input type="file" name="imagenes[]" class="form-control form-control-sm" multiple
                 accept=".jpg,.jpeg,.png,.gif,.webp" style="flex:1;min-width:180px">
          <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-upload me-1"></i>Subir</button>
        </form>
        <div style="font-size:.75rem;color:#9aafca;margin-top:.3rem">Múltiples imágenes, máx 5 MB c/u.</div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Red y Credenciales -->
    <?php if ($equipo['direccion_mac'] || $equipo['direccion_ip'] || $equipo['usuario_pc'] || $equipo['contrasena_pc']): ?>
    <div class="card mb-4">
      <div class="card-header"><i class="bi bi-hdd-network me-2 text-info"></i>Red y Credenciales</div>
      <div class="card-body p-3">
        <div class="row g-3">
          <?php if ($equipo['direccion_mac']): ?>
          <div class="col-sm-6">
            <div class="p-2 rounded-3" style="background:#f7faff;border:1px solid #dde4ef;font-size:.84rem">
              <div style="font-size:.72rem;color:#9aafca;text-transform:uppercase;font-weight:700">Dirección MAC</div>
              <div style="font-weight:600;font-family:monospace"><?= htmlspecialchars($equipo['direccion_mac']) ?></div>
            </div>
          </div>
          <?php endif; ?>
          <?php if ($equipo['direccion_ip']): ?>
          <div class="col-sm-6">
            <div class="p-2 rounded-3" style="background:#f7faff;border:1px solid #dde4ef;font-size:.84rem">
              <div style="font-size:.72rem;color:#9aafca;text-transform:uppercase;font-weight:700">Dirección IP</div>
              <div style="font-weight:600;font-family:monospace"><?= htmlspecialchars($equipo['direccion_ip']) ?></div>
            </div>
          </div>
          <?php endif; ?>
          <?php if ($equipo['usuario_pc']): ?>
          <div class="col-sm-6">
            <div class="p-2 rounded-3" style="background:#f7faff;border:1px solid #dde4ef;font-size:.84rem">
              <div style="font-size:.72rem;color:#9aafca;text-transform:uppercase;font-weight:700">Usuario</div>
              <div style="font-weight:600"><?= htmlspecialchars($equipo['usuario_pc']) ?></div>
            </div>
          </div>
          <?php endif; ?>
          <?php if ($equipo['contrasena_pc']): ?>
          <div class="col-sm-6">
            <div class="p-2 rounded-3" style="background:#fff3cd;border:1px solid #ffe58a;font-size:.84rem">
              <div style="font-size:.72rem;color:#9aafca;text-transform:uppercase;font-weight:700">Contraseña</div>
              <div class="d-flex align-items-center gap-2">
                <span id="passText" style="font-weight:600;font-family:monospace;letter-spacing:.12em">••••••••</span>
                <button type="button" id="btnVerPass" class="btn btn-xs btn-outline-secondary p-1" style="line-height:1" title="Mostrar">
                  <i class="bi bi-eye" id="icoPass" style="font-size:.8rem"></i>
                </button>
              </div>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Especificaciones -->
    <?php if (!empty($specs)): ?>
    <div class="card mb-4">
      <div class="card-header"><i class="bi bi-list-check me-2 text-info"></i>Especificaciones</div>
      <div class="card-body p-3">
        <div class="row g-2">
          <?php foreach ($specs as $s): if (!$s['valor']) continue; ?>
          <div class="col-sm-6">
            <div class="p-2 rounded-3" style="background:#f7faff;border:1px solid #dde4ef;font-size:.84rem">
              <div style="font-size:.72rem;color:#9aafca;text-transform:uppercase;font-weight:700"><?= htmlspecialchars($s['etiqueta']) ?></div>
              <div style="font-weight:600;color:#0d1b2a"><?= htmlspecialchars($s['valor']) ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Historial de incidencias -->
    <div class="card mb-4">
      <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-activity me-2 text-danger"></i>Historial de Incidencias</span>
        <a href="index.php?c=inventario&a=incidenciaCreate&equipo_id=<?= $equipo['id'] ?>" class="btn btn-sm btn-danger">
          <i class="bi bi-plus-circle me-1"></i>Registrar incidencia
        </a>
      </div>
      <div class="card-body p-3">
        <?php if (empty($incidencias)): ?>
        <div class="text-center text-muted py-4">
          <i class="bi bi-shield-check" style="font-size:2.4rem"></i>
          <p class="mb-0 mt-2">Este equipo no tiene incidencias registradas.</p>
        </div>
        <?php else: ?>
        <div class="d-flex flex-column gap-2">
          <?php foreach ($incidencias as $inc):
            $estInc = $estadosIncidencia[$inc['estado']] ?? ['label'=>$inc['estado'],'color'=>'secondary'];
            $sevInc = $severidadesIncidencia[$inc['severidad']] ?? ['label'=>$inc['severidad'],'color'=>'secondary'];
            $tipInc = $tiposIncidencia[$inc['tipo']] ?? ['label'=>$inc['tipo'],'icon'=>'bi-dot'];
          ?>
          <a href="index.php?c=inventario&a=incidenciaDetail&id=<?= $inc['id'] ?>"
             class="text-decoration-none text-reset">
            <div class="p-3 rounded-3" style="border:1px solid #dde4ef;background:#fff;transition:.18s">
              <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                <div>
                  <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="folio-badge"><?= htmlspecialchars($inc['folio']) ?></span>
                    <span class="badge bg-<?= $estInc['color'] ?>"><?= $estInc['label'] ?></span>
                    <span class="badge bg-<?= $sevInc['color'] ?>">Severidad <?= $sevInc['label'] ?></span>
                  </div>
                  <div class="mt-2" style="font-weight:700;color:#0d1b2a">
                    <i class="<?= $tipInc['icon'] ?> me-1 text-muted"></i><?= htmlspecialchars($inc['titulo']) ?>
                  </div>
                  <div style="font-size:.82rem;color:#6b7c93">
                    <?= htmlspecialchars($tipInc['label']) ?> · Reportó <?= htmlspecialchars($inc['nombre_reporta']) ?> ·
                    <?= date('d/m/Y H:i', strtotime($inc['creado_en'])) ?>
                  </div>
                </div>
                <div class="text-muted" style="font-size:.8rem">
                  <i class="bi bi-images me-1"></i><?= (int)$inc['total_imagenes'] ?>
                </div>
              </div>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Asignación activa -->
    <?php if ($asigActiva): ?>
    <div class="card mb-4" style="border-color:#0077ff">
      <div class="card-header" style="background:#e8f0fe"><i class="bi bi-file-earmark-check me-2 text-primary"></i>Asignación Activa</div>
      <div class="card-body p-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div>
            <span class="folio-badge"><?= htmlspecialchars($asigActiva['folio']) ?></span>
            <span class="ms-2" style="font-size:.88rem">Asignado a <strong><?= htmlspecialchars($asigActiva['nombre_usuario']) ?></strong></span>
            <?php if ($asigActiva['fecha_devolucion_esperada']):
              $vencida = strtotime($asigActiva['fecha_devolucion_esperada']) < time(); ?>
            <span class="ms-2 badge bg-<?= $vencida ? 'danger' : 'secondary' ?>">
              Dev. <?= date('d/m/Y', strtotime($asigActiva['fecha_devolucion_esperada'])) ?>
            </span>
            <?php endif; ?>
          </div>
          <div class="d-flex gap-2">
            <a href="index.php?c=asignaciones&a=detail&id=<?= $asigActiva['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i>Ver</a>
            <a href="index.php?c=asignaciones&a=acta&id=<?= $asigActiva['id'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-pdf me-1"></i>Acta</a>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

  </div>

  <!-- Sidebar -->
  <div class="col-lg-4">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-info-circle me-2"></i>Datos Generales</span>
        <a href="index.php?c=inventario&a=edit&id=<?= $equipo['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
      </div>
      <div class="card-body p-3">
        <?php foreach ([
            ['Código',    $equipo['codigo']],
            ['Categoría', $equipo['categoria_nombre']],
            ['Marca',     $equipo['marca']     ?? '—'],
            ['Modelo',    $equipo['modelo']    ?? '—'],
            ['N° Serie',  $equipo['numero_serie'] ?? '—'],
            ['Ubicación', $equipo['ubicacion'] ?? '—'],
            ['Estado',    '<span class="badge bg-'.$est['color'].'">'.$est['label'].'</span>'],
        ] as [$k, $v]): ?>
        <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid #f0f4f9;font-size:.84rem">
          <span style="color:#6b7c93;font-weight:500"><?= $k ?></span>
          <span><?= is_string($v) && !str_contains($v, 'badge') ? htmlspecialchars($v) : $v ?></span>
        </div>
        <?php endforeach; ?>
        <?php if (!$asigActiva): ?>
        <div class="mt-3">
          <a href="index.php?c=asignaciones&a=create&equipo_id=<?= $equipo['id'] ?>" class="btn btn-success btn-sm w-100">
            <i class="bi bi-file-earmark-plus me-1"></i>Asignar Equipo
          </a>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
// Mostrar/ocultar contraseña en detalle
const btnVer  = document.getElementById('btnVerPass');
const passDiv = document.getElementById('passText');
const icoPass = document.getElementById('icoPass');
const realPass = <?= json_encode($equipo['contrasena_pc'] ?? '') ?>;

btnVer?.addEventListener('click', function () {
    if (passDiv.innerText === '••••••••') {
        passDiv.innerText = realPass;
        icoPass.className = 'bi bi-eye-slash';
    } else {
        passDiv.innerText = '••••••••';
        icoPass.className = 'bi bi-eye';
    }
});
</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
