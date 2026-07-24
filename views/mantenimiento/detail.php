<?php
$pageTitle = 'Mantenimiento: '.$mant['folio'];
require __DIR__ . '/../layouts/header.php';
$est = $estados[$mant['estado']] ?? ['label'=>$mant['estado'],'color'=>'secondary'];
$tip = $tipos[$mant['tipo']]     ?? ['label'=>$mant['tipo'],  'color'=>'secondary','icon'=>'bi-tools'];
$vencido = in_array($mant['estado'],['pendiente','en_proceso']) && strtotime($mant['fecha_programada']) < strtotime('today');
?>
<?php if($success):?><div class="alert alert-success d-flex gap-2 mb-3"><i class="bi bi-check-circle-fill"></i>
<?php echo match($success){'creado'=>'Mantenimiento programado. Se envió correo al técnico asignado.','actualizado'=>'Estado actualizado correctamente.',default=>$success};?>
</div><?php endif;?>
<?php if($vencido):?><div class="alert alert-danger d-flex gap-2 mb-3"><i class="bi bi-exclamation-circle-fill"></i>Este mantenimiento está <strong>vencido</strong> — la fecha programada ya pasó.</div><?php endif;?>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="card mb-4"><div class="card-body p-4">
      <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
        <div>
          <span class="folio-badge me-2"><?=htmlspecialchars($mant['folio'])?></span>
          <span class="badge bg-<?=$tip['color']?>"><i class="<?=$tip['icon']?> me-1"></i><?=$tip['label']?></span>
          <span class="badge bg-<?=$est['color']?> ms-1"><?=$est['label']?></span>
        </div>
        <div class="d-flex gap-2">
          <a href="index.php?c=mantenimiento&a=vale&id=<?=$mant['id']?>" target="_blank" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-pdf me-1"></i>Vale PDF</a>
          <a href="index.php?c=mantenimiento" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a>
        </div>
      </div>

      <!-- Info equipo -->
      <div class="p-3 rounded-3 mb-3" style="background:#f7faff;border:1px solid #dde4ef">
        <div class="d-flex gap-3 align-items-center">
          <i class="<?=$mant['categoria_icono']?> text-primary" style="font-size:1.5rem"></i>
          <div>
            <div style="font-weight:700"><?=htmlspecialchars($mant['equipo_codigo'])?></div>
            <div style="font-size:.84rem;color:#6b7c93"><?=htmlspecialchars($mant['categoria_nombre'])?> · <?=htmlspecialchars(($mant['equipo_marca']??'').' '.($mant['equipo_modelo']??''))?></div>
            <?php if($mant['equipo_ubicacion']):?><div style="font-size:.82rem;color:#9aafca"><i class="bi bi-geo-alt me-1"></i><?=htmlspecialchars($mant['equipo_ubicacion'])?></div><?php endif;?>
          </div>
          <a href="index.php?c=inventario&a=detail&id=<?=$mant['equipo_id']?>" class="btn btn-sm btn-outline-primary ms-auto"><i class="bi bi-eye me-1"></i>Ver equipo</a>
        </div>
      </div>

      <?php if($mant['descripcion']):?>
      <div><div style="font-size:.78rem;font-weight:700;color:#9aafca;text-transform:uppercase;margin-bottom:.4rem">Actividades a realizar</div>
      <div class="p-3 rounded-3" style="background:#f7faff;border:1px solid #dde4ef;font-size:.9rem;line-height:1.7;white-space:pre-wrap"><?=htmlspecialchars($mant['descripcion'])?></div></div>
      <?php endif;?>

      <?php if($mant['notas']):?>
      <div class="mt-3"><div style="font-size:.78rem;font-weight:700;color:#9aafca;text-transform:uppercase;margin-bottom:.4rem">Notas de cierre</div>
      <div class="p-3 rounded-3" style="background:#d4edda;border:1px solid #b8dfc4;font-size:.9rem;white-space:pre-wrap"><?=htmlspecialchars($mant['notas'])?></div></div>
      <?php endif;?>
    </div></div>

    <!-- Cambiar estado -->
    <?php if(!in_array($mant['estado'],['completado','cancelado'])):?>
    <div class="card"><div class="card-header"><i class="bi bi-arrow-left-right me-2 text-warning"></i>Actualizar Estado</div>
    <div class="card-body p-4">
    <form method="POST">
      <input type="hidden" name="action" value="cambiar_estado">
      <div class="row g-3 mb-3">
        <div class="col-md-6"><label class="form-label">Nuevo estado</label>
          <select name="estado" class="form-select">
            <?php foreach($estados as $k=>$e):?>
            <option value="<?=$k?>" <?=$mant['estado']===$k?'selected':''?>><?=$e['label']?></option>
            <?php endforeach;?>
          </select>
        </div>
        <div class="col-md-6" id="divFecha" style="display:<?=in_array($mant['estado'],['completado'])?'block':'none'?>">
          <label class="form-label">Fecha realizada</label>
          <input type="date" name="fecha_realizada" class="form-control" value="<?=date('Y-m-d')?>">
        </div>
      </div>
      <div class="mb-3"><label class="form-label">Notas</label>
        <textarea name="notas" class="form-control" rows="3" placeholder="Observaciones del mantenimiento realizado..."><?=htmlspecialchars($mant['notas']??'')?></textarea>
      </div>
      <button type="submit" class="btn btn-warning" style="color:#000"><i class="bi bi-check2 me-1"></i>Actualizar</button>
    </form></div></div>
    <?php endif;?>
  </div>

  <!-- Sidebar -->
  <div class="col-lg-4">
    <div class="card"><div class="card-header"><i class="bi bi-info-circle me-2"></i>Detalles</div>
    <div class="card-body p-3">
      <?php foreach([
        ['Folio',       $mant['folio']],
        ['Tipo',        '<span class="badge bg-'.$tip['color'].'">'.$tip['label'].'</span>'],
        ['Estado',      '<span class="badge bg-'.$est['color'].'">'.$est['label'].'</span>'],
        ['Técnico',     $mant['nombre_tecnico']?htmlspecialchars($mant['nombre_tecnico']):'<span class="text-muted">Sin asignar</span>'],
        ['F. Programada',date('d/m/Y',strtotime($mant['fecha_programada']))],
        ['F. Realizada', $mant['fecha_realizada']?date('d/m/Y',strtotime($mant['fecha_realizada'])):'Pendiente'],
        ['Creado por',  htmlspecialchars($mant['nombre_creador'])],
        ['Creado',      date('d/m/Y H:i',strtotime($mant['creado_en']))],
      ] as [$k,$v]):?>
      <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid #f0f4f9;font-size:.84rem">
        <span style="color:#6b7c93;font-weight:500"><?=$k?></span><span><?=$v?></span>
      </div>
      <?php endforeach;?>
    </div></div>
  </div>
</div>

<script>
document.querySelector('select[name="estado"]')?.addEventListener('change', function() {
    document.getElementById('divFecha').style.display = this.value === 'completado' ? 'block' : 'none';
});
</script>
<?php require __DIR__.'/../layouts/footer.php';?>
