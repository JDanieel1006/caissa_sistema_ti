<?php
$pageTitle='Asignaciones';require __DIR__.'/../layouts/header.php';
$statsA=$asigModel->getStats();
?>
<?php if($success):?><div class="alert alert-success d-flex gap-2 mb-3"><i class="bi bi-check-circle-fill"></i>Operación completada.</div><?php endif;?>
<div class="row g-3 mb-4">
<?php foreach([['Total','bi-file-earmark-check','#e8f0fe','#0077ff',$statsA['total']],['Activas','bi-check-circle','#d4edda','#198754',$statsA['activas']],['Devueltas','bi-arrow-return-left','#f0f4f9','#6b7c93',$statsA['devueltas']],['Vencidas','bi-exclamation-circle','#fce4ec','#c62828',$statsA['vencidas']]] as [$l,$i,$bg,$c,$v]):?>
<div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon" style="background:<?=$bg?>;color:<?=$c?>"><i class="<?=$i?>"></i></div><div><div class="stat-value"><?=$v?></div><div class="stat-label"><?=$l?></div></div></div></div>
<?php endforeach;?>
</div>
<div class="card mb-4"><div class="card-body p-3">
<form method="GET" action="index.php" class="row g-2 align-items-end">
  <input type="hidden" name="c" value="asignaciones">
  <div class="col-12 col-md-5"><label class="form-label mb-1" style="font-size:.78rem">Buscar</label><input type="text" name="buscar" class="form-control form-control-sm" placeholder="Folio, usuario, código..." value="<?=htmlspecialchars($_GET['buscar']??'')?>"></div>
  <div class="col-6 col-md-3"><label class="form-label mb-1" style="font-size:.78rem">Estado</label><select name="estado" class="form-select form-select-sm"><option value="">Todos</option><?php foreach($estados as $k=>$e):?><option value="<?=$k?>" <?=(($_GET['estado']??'')===$k)?'selected':''?>><?=$e['label']?></option><?php endforeach;?></select></div>
  <div class="col-6 col-md-2 d-flex gap-2"><button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search me-1"></i>Filtrar</button><a href="index.php?c=asignaciones" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i></a></div>
</form></div></div>
<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <span><i class="bi bi-file-earmark-check me-2 text-success"></i>Asignaciones<span class="badge bg-primary ms-2"><?=count($asignaciones)?></span></span>
    <a href="index.php?c=asignaciones&a=create" class="btn btn-sm btn-success"><i class="bi bi-plus me-1"></i>Nueva</a>
  </div>
  <?php if(empty($asignaciones)):?><div class="card-body text-center py-5 text-muted"><i class="bi bi-inbox" style="font-size:3rem"></i><p class="mt-3 mb-0">Sin asignaciones.</p></div>
  <?php else:?>
  <div class="table-responsive"><table class="table mb-0">
    <thead><tr><th>Folio</th><th>Equipo</th><th>Asignado a</th><th>Entrega</th><th>Devolución</th><th>Estado</th><th style="width:80px"></th></tr></thead>
    <tbody>
    <?php foreach($asignaciones as $a):
      $est=$estados[$a['estado']]??['label'=>$a['estado'],'color'=>'secondary'];
      $vencida=$a['estado']==='activa'&&$a['fecha_devolucion_esperada']&&strtotime($a['fecha_devolucion_esperada'])<time();
    ?>
    <tr style="cursor:pointer;<?=$vencida?'background:#fff5f5':''?>" onclick="location.href='index.php?c=asignaciones&a=detail&id=<?=$a['id']?>'">
      <td><span class="folio-badge"><?=htmlspecialchars($a['folio'])?></span><?php if($vencida):?><br><span style="font-size:.7rem;color:#c62828"><i class="bi bi-exclamation-circle me-1"></i>Vencida</span><?php endif;?></td>
      <td style="font-size:.84rem"><strong><?=htmlspecialchars($a['equipo_codigo'])?></strong><br><span style="color:#9aafca;font-size:.78rem"><?=htmlspecialchars($a['categoria_nombre'])?></span></td>
      <td style="font-size:.84rem"><?=htmlspecialchars($a['nombre_usuario'])?><br><span style="color:#9aafca;font-size:.78rem"><?=htmlspecialchars($a['dept_usuario']??'')?></span></td>
      <td style="font-size:.83rem"><?=date('d/m/Y',strtotime($a['fecha_asignacion']))?></td>
      <td style="font-size:.83rem;color:<?=$vencida?'#c62828':'#6b7c93'?>"><?=$a['fecha_devolucion_esperada']?date('d/m/Y',strtotime($a['fecha_devolucion_esperada'])):'—'?></td>
      <td><span class="badge bg-<?=$est['color']?>"><?=$est['label']?></span></td>
      <td onclick="event.stopPropagation()"><a href="index.php?c=asignaciones&a=detail&id=<?=$a['id']?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-eye"></i></a><a href="index.php?c=asignaciones&a=acta&id=<?=$a['id']?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-pdf"></i></a></td>
    </tr>
    <?php endforeach;?>
    </tbody>
  </table></div>
  <?php endif;?>
</div>
<?php require __DIR__.'/../layouts/footer.php';?>
