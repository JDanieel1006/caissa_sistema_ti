<?php
$pageTitle='Inventario';require __DIR__.'/../layouts/header.php';
?>
<?php if($success):?><div class="alert alert-success d-flex gap-2 mb-3"><i class="bi bi-check-circle-fill"></i><?=htmlspecialchars($success)?></div><?php endif;?>
<!-- Stats row -->
<div class="row g-3 mb-4">
<?php foreach([['Total','bi-box-seam','#e8f0fe','#0077ff',$stats['total']],['Buenos','bi-shield-check','#d4edda','#198754',$stats['buenos']],['Dañados','bi-exclamation-triangle','#fce4ec','#c62828',$stats['dañados']],['Reparación','bi-tools','#fff3cd','#d97700',$stats['reparacion']]] as [$l,$i,$bg,$c,$v]):?>
<div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon" style="background:<?=$bg?>;color:<?=$c?>"><i class="<?=$i?>"></i></div><div><div class="stat-value"><?=$v?></div><div class="stat-label"><?=$l?></div></div></div></div>
<?php endforeach;?>
</div>
<!-- Filtros -->
<div class="card mb-4"><div class="card-body p-3">
<form method="GET" action="index.php" class="row g-2 align-items-end">
  <input type="hidden" name="c" value="inventario">
  <div class="col-12 col-md-4"><label class="form-label mb-1" style="font-size:.78rem">Buscar</label><input type="text" name="buscar" class="form-control form-control-sm" placeholder="Código, marca, modelo..." value="<?=htmlspecialchars($_GET['buscar']??'')?>"></div>
  <div class="col-6 col-md-3"><label class="form-label mb-1" style="font-size:.78rem">Categoría</label><select name="categoria_id" class="form-select form-select-sm"><option value="">Todas</option><?php foreach($categorias as $c):?><option value="<?=$c['id']?>" <?=(($_GET['categoria_id']??'')==$c['id'])?'selected':''?>><?=htmlspecialchars($c['nombre'])?></option><?php endforeach;?></select></div>
  <div class="col-6 col-md-3"><label class="form-label mb-1" style="font-size:.78rem">Estado</label><select name="estado" class="form-select form-select-sm"><option value="">Todos</option><?php foreach($estados as $k=>$e):?><option value="<?=$k?>" <?=(($_GET['estado']??'')===$k)?'selected':''?>><?=$e['label']?></option><?php endforeach;?></select></div>
  <div class="col-12 col-md-2 d-flex gap-2"><button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search me-1"></i>Filtrar</button><a href="index.php?c=inventario" class="btn btn-outline-secondary btn-sm" title="Limpiar"><i class="bi bi-x-lg"></i></a></div>
</form></div></div>
<!-- Tabla -->
<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <span><i class="bi bi-box-seam me-2 text-primary"></i>Equipos<span class="badge bg-primary ms-2"><?=count($equipos)?></span></span>
    <a href="index.php?c=inventario&a=create" class="btn btn-sm btn-primary"><i class="bi bi-plus me-1"></i>Nuevo Equipo</a>
  </div>
  <?php if(empty($equipos)):?>
  <div class="card-body text-center py-5"><i class="bi bi-box-seam text-muted" style="font-size:3rem"></i><p class="text-muted mt-3 mb-1">Sin equipos registrados.</p><a href="index.php?c=inventario&a=create" class="btn btn-primary btn-sm mt-2"><i class="bi bi-plus me-1"></i>Registrar equipo</a></div>
  <?php else:?>
  <div class="table-responsive"><table class="table mb-0">
    <thead><tr><th>Imagen</th><th>Código</th><th>Categoría</th><th>Marca / Modelo</th><th>Ubicación</th><th>Estado</th><th style="width:80px"></th></tr></thead>
    <tbody>
    <?php foreach($equipos as $eq):$est=$estados[$eq['estado']]??['label'=>$eq['estado'],'color'=>'secondary'];?>
    <tr style="cursor:pointer" onclick="location.href='index.php?c=inventario&a=detail&id=<?=$eq['id']?>'">
      <td><div style="width:44px;height:44px;border-radius:8px;overflow:hidden;background:#f0f4f9;display:grid;place-items:center">
        <?php if($eq['img_principal']):?><img src="index.php?c=inventario&a=imagen&img_id=<?=$eq['img_principal_id']?>&thumb=1" style="width:44px;height:44px;object-fit:cover" loading="lazy"><?php else:?><i class="<?=$eq['categoria_icono']?> text-muted"></i><?php endif;?></div></td>
      <td><span class="folio-badge"><?=htmlspecialchars($eq['codigo'])?></span></td>
      <td style="font-size:.83rem"><i class="<?=$eq['categoria_icono']?> me-1 text-muted"></i><?=htmlspecialchars($eq['categoria_nombre'])?></td>
      <td style="font-size:.85rem"><strong><?=htmlspecialchars($eq['marca']??'—')?></strong><?php if($eq['modelo']):?><br><span style="color:#9aafca;font-size:.78rem"><?=htmlspecialchars($eq['modelo'])?></span><?php endif;?></td>
      <td style="font-size:.84rem"><?=htmlspecialchars($eq['ubicacion']??'—')?></td>
      <td><span class="badge bg-<?=$est['color']?>"><?=$est['label']?></span></td>
      <td onclick="event.stopPropagation()"><a href="index.php?c=inventario&a=detail&id=<?=$eq['id']?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-eye"></i></a><a href="index.php?c=inventario&a=edit&id=<?=$eq['id']?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a></td>
    </tr>
    <?php endforeach;?>
    </tbody>
  </table></div>
  <?php endif;?>
</div>
<?php require __DIR__.'/../layouts/footer.php';?>
