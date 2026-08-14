<?php
$rol=$_SESSION['user_rol'];
$pageTitle=in_array($rol,['admin','tecnico'])?'Todos los Tickets':'Mis Tickets';
require __DIR__.'/../layouts/header.php';
?>
<?php if(!empty($_GET['creado'])):?>
<div class="alert alert-success d-flex gap-2 align-items-center mb-3">
  <i class="bi bi-check-circle-fill"></i>Ticket <strong><?=htmlspecialchars($_GET['creado'])?></strong> creado exitosamente.
</div>
<?php endif;?>
<div class="card mb-4"><div class="card-body p-3">
  <form method="GET" action="index.php" class="row g-2 align-items-end">
    <input type="hidden" name="c" value="tickets">
    <div class="col-12 col-md-4"><label class="form-label mb-1" style="font-size:.78rem">Buscar</label><input type="text" name="buscar" class="form-control form-control-sm" placeholder="Folio, título..." value="<?=htmlspecialchars($_GET['buscar']??'')?>"></div>
    <div class="col-6 col-md-2"><label class="form-label mb-1" style="font-size:.78rem">Estado</label><select name="estado" class="form-select form-select-sm"><option value="">Todos</option><?php foreach($estados as $k=>$e):?><option value="<?=$k?>" <?=(($_GET['estado']??'')===$k)?'selected':''?>><?=$e['label']?></option><?php endforeach;?></select></div>
    <div class="col-6 col-md-2"><label class="form-label mb-1" style="font-size:.78rem">Categoría</label><select name="categoria" class="form-select form-select-sm"><option value="">Todas</option><?php foreach($categorias as $k=>$c):?><option value="<?=$k?>" <?=(($_GET['categoria']??'')===$k)?'selected':''?>><?=$c['label']?></option><?php endforeach;?></select></div>
    <div class="col-6 col-md-2"><label class="form-label mb-1" style="font-size:.78rem">Prioridad</label><select name="prioridad" class="form-select form-select-sm"><option value="">Todas</option><?php foreach($prioridades as $k=>$p):?><option value="<?=$k?>" <?=(($_GET['prioridad']??'')===$k)?'selected':''?>><?=$p['label']?></option><?php endforeach;?></select></div>
    <div class="col-6 col-md-2 d-flex gap-2"><button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search me-1"></i>Filtrar</button><a href="index.php?c=tickets" class="btn btn-outline-secondary btn-sm" title="Limpiar"><i class="bi bi-x-lg"></i></a></div>
  </form>
</div></div>
<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <span><i class="bi bi-ticket-detailed me-2 text-primary"></i><?=$pageTitle?><span class="badge bg-primary ms-2"><?=count($tickets)?></span></span>
    <div class="d-flex gap-2">
      <a href="index.php?c=tickets&a=create" class="btn btn-sm btn-primary"><i class="bi bi-plus me-1"></i>Nuevo</a>
      <a href="index.php?c=reports&a=lista&<?=http_build_query(array_filter(['estado'=>$_GET['estado']??'','categoria'=>$_GET['categoria']??'','prioridad'=>$_GET['prioridad']??'','buscar'=>$_GET['buscar']??'']))?>" target="_blank" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
    </div>
  </div>
  <?php if(empty($tickets)):?>
  <div class="card-body text-center py-5"><i class="bi bi-inbox text-muted" style="font-size:3rem"></i><p class="text-muted mt-3 mb-1">No hay tickets con los filtros actuales.</p><a href="index.php?c=tickets&a=create" class="btn btn-primary btn-sm mt-2"><i class="bi bi-plus me-1"></i>Crear ticket</a></div>
  <?php else:?>
  <div class="table-responsive"><table class="table mb-0 js-data-table">
    <thead><tr><th>Folio</th><th>Título</th><?php if(in_array($rol,['admin','tecnico'])):?><th>Solicitante</th><?php endif;?><th>Categoría</th><th>Prioridad</th><th>Estado</th><?php if(in_array($rol,['admin','tecnico'])):?><th>Técnico</th><?php endif;?><th>Fecha</th><th style="width:60px"></th></tr></thead>
    <tbody>
    <?php foreach($tickets as $t):$est=$estados[$t['estado']]??['label'=>$t['estado'],'color'=>'secondary'];$pri=$prioridades[$t['prioridad']]??['label'=>$t['prioridad'],'color'=>'secondary'];$cat=$categorias[$t['categoria']]??['label'=>$t['categoria'],'icon'=>'bi-tools'];?>
    <tr style="cursor:pointer" onclick="location.href='index.php?c=tickets&a=detail&id=<?=$t['id']?>'">
      <td><span class="folio-badge"><?=htmlspecialchars($t['folio'])?></span></td>
      <td><div style="font-weight:500;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?=htmlspecialchars($t['titulo'])?></div><?php if($t['ubicacion']):?><div style="font-size:.73rem;color:#6b7c93"><i class="bi bi-geo-alt me-1"></i><?=htmlspecialchars($t['ubicacion'])?></div><?php endif;?></td>
      <?php if(in_array($rol,['admin','tecnico'])):?><td style="font-size:.84rem"><?=htmlspecialchars($t['nombre_usuario'])?></td><?php endif;?>
      <td style="font-size:.83rem"><i class="<?=$cat['icon']?> me-1 text-muted"></i><?=$cat['label']?></td>
      <td><span class="badge bg-<?=$pri['color']?>"><?=$pri['label']?></span></td>
      <td><span class="badge bg-<?=$est['color']?>"><?=$est['label']?></span></td>
      <?php if(in_array($rol,['admin','tecnico'])):?><td style="font-size:.82rem;color:#6b7c93"><?=$t['nombre_tecnico']?htmlspecialchars($t['nombre_tecnico']):'<span style="color:#ccc">—</span>'?></td><?php endif;?>
      <td style="font-size:.77rem;color:#6b7c93;white-space:nowrap"><?=date('d/m/y',strtotime($t['creado_en']))?><br><?=date('H:i',strtotime($t['creado_en']))?></td>
      <td><a href="index.php?c=tickets&a=detail&id=<?=$t['id']?>" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation()"><i class="bi bi-eye"></i></a></td>
    </tr>
    <?php endforeach;?>
    </tbody>
  </table></div>
  <?php endif;?>
</div>
<?php require __DIR__.'/../layouts/footer.php';?>
