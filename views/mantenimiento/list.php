<?php
$pageTitle = 'Mantenimiento';
require __DIR__ . '/../layouts/header.php';
?>
<?php if ($success): ?>
<div class="alert alert-success d-flex gap-2 mb-3"><i class="bi bi-check-circle-fill"></i>Operación completada correctamente.</div>
<?php endif; ?>

<!-- Stats -->
<div class="row g-3 mb-4">
<?php foreach ([
    ['Total',       'bi-tools',              '#e8f0fe','#0077ff', $stats['total']],
    ['Pendientes',  'bi-hourglass-split',    '#fff3cd','#d97700', $stats['pendientes']],
    ['En Proceso',  'bi-arrow-repeat',       '#e3f2fd','#0288d1', $stats['en_proceso']],
    ['Completados', 'bi-check-circle',       '#d4edda','#198754', $stats['completados']],
    ['Vencidos',    'bi-exclamation-circle', '#fce4ec','#c62828', $stats['vencidos']],
] as [$l,$i,$bg,$c,$v]): ?>
<div class="col-6 col-md">
  <div class="stat-card">
    <div class="stat-icon" style="background:<?=$bg?>;color:<?=$c?>"><i class="<?=$i?>"></i></div>
    <div><div class="stat-value"><?=$v?></div><div class="stat-label"><?=$l?></div></div>
  </div>
</div>
<?php endforeach; ?>
</div>

<!-- Filtros -->
<div class="card mb-4"><div class="card-body p-3">
<form method="GET" action="index.php" class="row g-2 align-items-end">
  <input type="hidden" name="c" value="mantenimiento">
  <div class="col-12 col-md-4"><label class="form-label mb-1" style="font-size:.78rem">Buscar</label>
    <input type="text" name="buscar" class="form-control form-control-sm" placeholder="Folio, código, marca..." value="<?=htmlspecialchars($_GET['buscar']??'')?>">
  </div>
  <div class="col-6 col-md-2"><label class="form-label mb-1" style="font-size:.78rem">Estado</label>
    <select name="estado" class="form-select form-select-sm"><option value="">Todos</option>
      <?php foreach($estados as $k=>$e):?><option value="<?=$k?>" <?=(($_GET['estado']??'')===$k)?'selected':''?>><?=$e['label']?></option><?php endforeach;?>
    </select>
  </div>
  <div class="col-6 col-md-2"><label class="form-label mb-1" style="font-size:.78rem">Tipo</label>
    <select name="tipo" class="form-select form-select-sm"><option value="">Todos</option>
      <?php foreach($tipos as $k=>$t):?><option value="<?=$k?>" <?=(($_GET['tipo']??'')===$k)?'selected':''?>><?=$t['label']?></option><?php endforeach;?>
    </select>
  </div>
  <div class="col-12 col-md-2 d-flex gap-2">
    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search me-1"></i>Filtrar</button>
    <a href="index.php?c=mantenimiento" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
  </div>
</form></div></div>

<!-- Tabla -->
<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <span><i class="bi bi-tools me-2 text-primary"></i>Mantenimientos <span class="badge bg-primary ms-1"><?=count($mantenimientos)?></span></span>
    <a href="index.php?c=mantenimiento&a=create" class="btn btn-sm btn-primary"><i class="bi bi-plus me-1"></i>Programar</a>
  </div>
  <?php if(empty($mantenimientos)):?>
  <div class="card-body text-center py-5 text-muted"><i class="bi bi-tools" style="font-size:3rem"></i><p class="mt-3 mb-1">Sin mantenimientos registrados.</p><a href="index.php?c=mantenimiento&a=create" class="btn btn-primary btn-sm mt-2"><i class="bi bi-plus me-1"></i>Programar mantenimiento</a></div>
  <?php else:?>
  <div class="table-responsive"><table class="table mb-0 js-data-table">
    <thead><tr><th>Folio</th><th>Equipo</th><th>Tipo</th><th>Fecha Prog.</th><th>Técnico</th><th>Estado</th><th style="width:80px"></th></tr></thead>
    <tbody>
    <?php foreach($mantenimientos as $m):
      $est=$estados[$m['estado']]??['label'=>$m['estado'],'color'=>'secondary'];
      $tip=$tipos[$m['tipo']]??['label'=>$m['tipo'],'color'=>'secondary','icon'=>'bi-tools'];
      $vencido=in_array($m['estado'],['pendiente','en_proceso'])&&strtotime($m['fecha_programada'])<strtotime('today');
    ?>
    <tr style="cursor:pointer;<?=$vencido?'background:#fff5f5':''?>" onclick="location.href='index.php?c=mantenimiento&a=detail&id=<?=$m['id']?>'">
      <td><span class="folio-badge"><?=htmlspecialchars($m['folio'])?></span></td>
      <td style="font-size:.84rem">
        <strong><?=htmlspecialchars($m['equipo_codigo'])?></strong>
        <?php if($m['equipo_marca']):?><br><span style="color:#9aafca;font-size:.78rem"><?=htmlspecialchars($m['equipo_marca'].' '.($m['equipo_modelo']??''))?></span><?php endif;?>
      </td>
      <td><span class="badge bg-<?=$tip['color']?>"><i class="<?=$tip['icon']?> me-1"></i><?=$tip['label']?></span></td>
      <td style="font-size:.84rem;color:<?=$vencido?'#c62828':'inherit'?>">
        <?=date('d/m/Y',strtotime($m['fecha_programada']))?>
        <?php if($vencido):?><br><span style="font-size:.72rem"><i class="bi bi-exclamation-circle me-1"></i>Vencido</span><?php endif;?>
      </td>
      <td style="font-size:.83rem"><?=$m['nombre_tecnico']?htmlspecialchars($m['nombre_tecnico']):'<span class="text-muted">—</span>'?></td>
      <td><span class="badge bg-<?=$est['color']?>"><?=$est['label']?></span></td>
      <td onclick="event.stopPropagation()">
        <a href="index.php?c=mantenimiento&a=detail&id=<?=$m['id']?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-eye"></i></a>
        <a href="index.php?c=mantenimiento&a=vale&id=<?=$m['id']?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-pdf"></i></a>
      </td>
    </tr>
    <?php endforeach;?>
    </tbody>
  </table></div>
  <?php endif;?>
</div>
<?php require __DIR__.'/../layouts/footer.php';?>
