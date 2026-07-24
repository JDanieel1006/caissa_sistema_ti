<?php
$pageTitle='Ticket: '.$ticket['folio'];
require __DIR__.'/../layouts/header.php';
$est=$estados[$ticket['estado']]??['label'=>$ticket['estado'],'color'=>'secondary'];
$pri=$prioridades[$ticket['prioridad']]??['label'=>$ticket['prioridad'],'color'=>'secondary'];
$cat=$categorias[$ticket['categoria']]??['label'=>$ticket['categoria'],'icon'=>'bi-tools'];
?>
<?php if($msgSuccess):?><div class="alert alert-success d-flex gap-2 mb-3"><i class="bi bi-check-circle-fill"></i><?=$msgSuccess?></div><?php endif;?>
<?php if($msgError):?><div class="alert alert-danger d-flex gap-2 mb-3"><i class="bi bi-exclamation-triangle-fill"></i><?=htmlspecialchars($msgError)?></div><?php endif;?>
<div class="row g-4">
  <div class="col-lg-8">
    <!-- Cabecera ticket -->
    <div class="card mb-4">
      <div class="card-body p-4">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
          <div>
            <span class="folio-badge me-2"><?=htmlspecialchars($ticket['folio'])?></span>
            <span class="badge bg-<?=$est['color']?>"><?=$est['label']?></span>
            <span class="badge bg-<?=$pri['color']?> ms-1"><?=$pri['label']?></span>
          </div>
          <div class="d-flex gap-2">
            <a href="index.php?c=reports&a=ticket&id=<?=$ticket['id']?>" target="_blank" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
            <a href="index.php?c=tickets" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a>
          </div>
        </div>
        <h4 style="font-family:'Syne',sans-serif;font-weight:800;margin-bottom:.5rem"><?=htmlspecialchars($ticket['titulo'])?></h4>
        <div class="d-flex flex-wrap gap-3 mb-3" style="font-size:.83rem;color:#6b7c93">
          <span><i class="<?=$cat['icon']?> me-1"></i><?=$cat['label']?></span>
          <?php if($ticket['ubicacion']):?><span><i class="bi bi-geo-alt me-1"></i><?=htmlspecialchars($ticket['ubicacion'])?></span><?php endif;?>
          <span><i class="bi bi-person me-1"></i><?=htmlspecialchars($ticket['nombre_usuario'])?></span>
          <span><i class="bi bi-calendar me-1"></i><?=date('d/m/Y H:i',strtotime($ticket['creado_en']))?></span>
        </div>
        <div class="p-3 rounded-3" style="background:#f7faff;border:1px solid #dde4ef;font-size:.9rem;line-height:1.7;white-space:pre-wrap"><?=htmlspecialchars($ticket['descripcion'])?></div>
        <?php if($ticket['resolucion']):?>
        <div class="mt-3 p-3 rounded-3" style="background:#d4edda;border:1px solid #b8dfc4">
          <div style="font-size:.75rem;font-weight:700;color:#155724;text-transform:uppercase;margin-bottom:.3rem"><i class="bi bi-check-circle me-1"></i>Resolución</div>
          <div style="font-size:.9rem;white-space:pre-wrap"><?=htmlspecialchars($ticket['resolucion'])?></div>
        </div>
        <?php endif;?>
      </div>
    </div>

    <!-- Adjuntos -->
    <?php if(!empty($adjuntos)):?>
    <div class="card mb-4">
      <div class="card-header"><i class="bi bi-paperclip me-2"></i>Archivos adjuntos (<?=count($adjuntos)?>)</div>
      <div class="card-body p-3">
        <div class="row g-2">
        <?php foreach($adjuntos as $adj):$esImg=str_starts_with($adj['tipo_mime'],'image/');?>
        <div class="col-sm-6 col-md-4">
          <div class="p-2 rounded-3 d-flex align-items-center gap-2" style="border:1px solid #dde4ef;font-size:.82rem">
            <?php if($esImg):?><i class="bi bi-image text-primary" style="font-size:1.3rem;flex-shrink:0"></i><?php else:?><i class="bi bi-file-earmark-pdf text-danger" style="font-size:1.3rem;flex-shrink:0"></i><?php endif;?>
            <div style="min-width:0;flex:1">
              <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?=htmlspecialchars($adj['nombre_original'])?></div>
              <div style="color:#9aafca"><?=Adjunto::formatBytes($adj['tamano'])?></div>
            </div>
            <div class="d-flex gap-1">
              <a href="index.php?c=tickets&a=adjunto&adj_id=<?=$adj['adjunto_id']?>" target="_blank" class="btn btn-sm btn-outline-primary p-1" style="line-height:1"><i class="bi bi-eye" style="font-size:.8rem"></i></a>
              <?php if(in_array($_SESSION['user_rol'],['admin','tecnico'])||$adj['usuario_id']==$_SESSION['user_id']):?>
              <form method="POST" style="display:inline" onsubmit="return confirm('¿Eliminar archivo?')">
                <input type="hidden" name="action" value="eliminar_adjunto">
                <input type="hidden" name="adjunto_id" value="<?=$adj['adjunto_id']?>">
                <button class="btn btn-sm btn-outline-danger p-1" style="line-height:1"><i class="bi bi-trash" style="font-size:.8rem"></i></button>
              </form>
              <?php endif;?>
            </div>
          </div>
        </div>
        <?php endforeach;?>
        </div>
      </div>
    </div>
    <?php endif;?>

    <!-- Comentarios -->
    <div class="card mb-4">
      <div class="card-header"><i class="bi bi-chat-left-text me-2 text-primary"></i>Comentarios (<?=count($comentarios)?>)</div>
      <div class="card-body p-4">
        <?php if(empty($comentarios)):?><p class="text-muted text-center py-3" style="font-size:.87rem">Sin comentarios aún.</p><?php endif;?>
        <?php foreach($comentarios as $c):?>
        <div class="comment-bubble <?=$c['es_interno']?'internal':''?>">
          <div class="comment-meta"><strong><?=htmlspecialchars($c['nombre_usuario'])?></strong> · <?=date('d/m/Y H:i',strtotime($c['creado_en']))?><?php if($c['es_interno']):?> · <span style="color:#d97700"><i class="bi bi-lock me-1"></i>Nota interna</span><?php endif;?></div>
          <div style="font-size:.88rem;line-height:1.6;white-space:pre-wrap"><?=htmlspecialchars($c['mensaje'])?></div>
        </div>
        <?php endforeach;?>
        <!-- Formulario comentario -->
        <?php if(!in_array($ticket['estado'],['cerrado'])):?>
        <form method="POST" class="mt-3">
          <input type="hidden" name="action" value="comentar">
          <div class="mb-2"><textarea name="mensaje" class="form-control" rows="3" placeholder="Escribe un comentario..." required></textarea></div>
          <?php if($esAdmin):?><div class="form-check mb-2"><input type="checkbox" name="es_interno" class="form-check-input" id="chkInterno"><label class="form-check-label" for="chkInterno" style="font-size:.83rem">Nota interna (no visible para el solicitante)</label></div><?php endif;?>
          <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-send me-1"></i>Comentar</button>
        </form>
        <?php endif;?>
      </div>
    </div>

    <!-- Subir adjunto -->
    <?php if(!in_array($ticket['estado'],['cerrado'])):?>
    <div class="card mb-4">
      <div class="card-header"><i class="bi bi-paperclip me-2"></i>Agregar archivo adjunto</div>
      <div class="card-body p-3">
        <form method="POST" enctype="multipart/form-data" class="d-flex gap-2 align-items-end flex-wrap">
          <input type="hidden" name="action" value="subir_adjunto">
          <div style="flex:1;min-width:200px"><input type="file" name="adjunto" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf"></div>
          <button type="submit" class="btn btn-outline-primary btn-sm"><i class="bi bi-upload me-1"></i>Subir</button>
        </form>
        <div style="font-size:.75rem;color:#9aafca;margin-top:.4rem">Imágenes y PDF, máx. 5 MB.</div>
      </div>
    </div>
    <?php endif;?>
  </div>

  <!-- Sidebar -->
  <div class="col-lg-4">
    <?php if($esAdmin&&!in_array($ticket['estado'],['cerrado'])):?>
    <!-- Cambiar estado -->
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-arrow-left-right me-2 text-warning"></i>Cambiar Estado</div>
      <div class="card-body p-3">
        <form method="POST">
          <input type="hidden" name="action" value="cambiar_estado">
          <div class="mb-2"><label class="form-label mb-1" style="font-size:.8rem">Nuevo estado</label>
            <select name="estado" class="form-select form-select-sm">
              <?php foreach($estados as $k=>$e):?>
              <option value="<?=$k?>" <?=$ticket['estado']===$k?'selected':''?>><?=$e['label']?></option>
              <?php endforeach;?>
            </select>
          </div>
          <div class="mb-2"><label class="form-label mb-1" style="font-size:.8rem">Técnico asignado</label>
            <select name="tecnico_id" class="form-select form-select-sm">
              <option value="">— Sin asignar —</option>
              <?php foreach($tecnicos as $t):?>
              <option value="<?=$t['id']?>" <?=$ticket['tecnico_id']==$t['id']?'selected':''?>><?=htmlspecialchars($t['nombre'].' '.$t['apellido'])?></option>
              <?php endforeach;?>
            </select>
          </div>
          <div class="mb-2"><label class="form-label mb-1" style="font-size:.8rem">Nota</label><input type="text" name="nota" class="form-control form-control-sm" placeholder="Opcional..."></div>
          <div class="mb-3" id="resDiv" style="display:<?=in_array($ticket['estado'],['resuelto','cerrado'])?'block':'none'?>">
            <label class="form-label mb-1" style="font-size:.8rem">Resolución</label>
            <textarea name="resolucion" class="form-control form-control-sm" rows="2" placeholder="Describe cómo se resolvió..."><?=htmlspecialchars($ticket['resolucion']??'')?></textarea>
          </div>
          <button type="submit" class="btn btn-warning btn-sm w-100" style="color:#000"><i class="bi bi-check2 me-1"></i>Actualizar Estado</button>
        </form>
      </div>
    </div>
    <?php endif;?>

    <!-- Info ticket -->
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-info-circle me-2"></i>Información</div>
      <div class="card-body p-3">
        <?php foreach([['Estado','<span class="badge bg-'.$est['color'].'">'.$est['label'].'</span>'],['Prioridad','<span class="badge bg-'.$pri['color'].'">'.$pri['label'].'</span>'],['Categoría','<i class="'.$cat['icon'].' me-1 text-muted"></i>'.$cat['label']],['Técnico',$ticket['nombre_tecnico']?htmlspecialchars($ticket['nombre_tecnico']):'<span class="text-muted">Sin asignar</span>'],['Solicitante',htmlspecialchars($ticket['nombre_usuario'])],['Creado',date('d/m/Y H:i',strtotime($ticket['creado_en']))],$ticket['cerrado_en']?['Cerrado',date('d/m/Y H:i',strtotime($ticket['cerrado_en']))]:[null,null]] as [$k,$v]):if(!$k)continue;?>
        <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid #f0f4f9;font-size:.84rem">
          <span style="color:#6b7c93;font-weight:500"><?=$k?></span><span><?=$v?></span>
        </div>
        <?php endforeach;?>
      </div>
    </div>

    <!-- Historial -->
    <div class="card">
      <div class="card-header"><i class="bi bi-clock-history me-2 text-muted"></i>Historial</div>
      <div class="card-body p-3">
        <div class="timeline">
        <?php foreach(array_reverse($historial) as $h):$n=Ticket::ESTADOS[$h['estado_nuevo']]??['label'=>$h['estado_nuevo'],'color'=>'secondary'];?>
        <div class="timeline-item">
          <div class="timeline-date"><?=date('d/m/Y H:i',strtotime($h['creado_en']))?> · <?=htmlspecialchars($h['nombre_usuario'])?></div>
          <div class="timeline-text"><span class="badge bg-<?=$n['color']?> me-1"><?=$n['label']?></span><?php if($h['nota']):?><span style="font-size:.82rem;color:#6b7c93"><?=htmlspecialchars($h['nota'])?></span><?php endif;?></div>
        </div>
        <?php endforeach;?>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
document.querySelector('select[name="estado"]')?.addEventListener('change',function(){
  document.getElementById('resDiv').style.display=(['resuelto','cerrado'].includes(this.value))?'block':'none';
});
</script>
<?php require __DIR__.'/../layouts/footer.php';?>
