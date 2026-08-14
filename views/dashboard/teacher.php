<?php
$pageTitle='Mi Panel';require __DIR__.'/../layouts/header.php';
$abiertos=$stats['abierto']??0;$proceso=$stats['en_proceso']??0;$resueltos=$stats['resuelto']??0;$total=array_sum($stats);
?>
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon" style="background:#e8f0fe;color:#0077ff"><i class="bi bi-ticket-detailed"></i></div><div><div class="stat-value"><?=$total?></div><div class="stat-label">Mis Tickets</div></div></div></div>
  <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon" style="background:#fff3cd;color:#d97700"><i class="bi bi-hourglass-split"></i></div><div><div class="stat-value"><?=$abiertos?></div><div class="stat-label">Abiertos</div></div></div></div>
  <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon" style="background:#e3f2fd;color:#0288d1"><i class="bi bi-arrow-repeat"></i></div><div><div class="stat-value"><?=$proceso?></div><div class="stat-label">En Proceso</div></div></div></div>
  <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon" style="background:#d4edda;color:#198754"><i class="bi bi-check-circle"></i></div><div><div class="stat-value"><?=$resueltos?></div><div class="stat-label">Resueltos</div></div></div></div>
</div>
<div class="row g-4">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between"><span><i class="bi bi-clock-history me-2 text-primary"></i>Mis Tickets Recientes</span><a href="index.php?c=tickets" class="btn btn-sm btn-outline-primary">Ver todos</a></div>
      <div class="table-responsive"><table class="table mb-0 <?= empty($tickets) ? '' : 'js-data-table' ?>">
        <thead><tr><th>Folio</th><th>Título</th><th>Categoría</th><th>Estado</th><th>Fecha</th></tr></thead>
        <tbody>
        <?php foreach($tickets as $t):$est=$estados[$t['estado']]??['label'=>$t['estado'],'color'=>'secondary'];$cat=$categorias[$t['categoria']]??['label'=>$t['categoria'],'icon'=>'bi-tools'];?>
        <tr style="cursor:pointer" onclick="location.href='index.php?c=tickets&a=detail&id=<?=$t['id']?>'">
          <td><span class="folio-badge"><?=htmlspecialchars($t['folio'])?></span></td>
          <td style="font-weight:500;max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?=htmlspecialchars($t['titulo'])?></td>
          <td style="font-size:.83rem"><i class="<?=$cat['icon']?> me-1 text-muted"></i><?=$cat['label']?></td>
          <td><span class="badge bg-<?=$est['color']?>"><?=$est['label']?></span></td>
          <td style="font-size:.78rem;color:#6b7c93;white-space:nowrap"><?=date('d/m/y H:i',strtotime($t['creado_en']))?></td>
        </tr>
        <?php endforeach;if(empty($tickets)):?><tr><td colspan="5" class="text-center text-muted py-4">No tienes tickets aún.</td></tr><?php endif;?>
        </tbody>
      </table></div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card mb-3" style="background:linear-gradient(135deg,#0077ff,#00c2ff);color:#fff;border:none">
      <div class="card-body p-4 text-center">
        <i class="bi bi-plus-circle" style="font-size:2.5rem;opacity:.9"></i>
        <h5 class="mt-2 mb-1" style="font-family:'Syne',sans-serif;font-weight:800">¿Tienes un problema?</h5>
        <p style="font-size:.85rem;opacity:.9;margin-bottom:1.2rem">Reporta tu incidencia al equipo de soporte.</p>
        <a href="index.php?c=tickets&a=create" class="btn btn-light btn-sm" style="font-weight:700;border-radius:8px"><i class="bi bi-plus me-1"></i>Crear Ticket</a>
      </div>
    </div>
    <div class="card"><div class="card-header"><i class="bi bi-question-circle me-2 text-info"></i>¿Cómo funciona?</div>
      <div class="card-body p-3">
        <?php foreach([['bi-1-circle-fill','Crea un ticket describiendo el problema'],['bi-2-circle-fill','Un técnico será asignado a tu caso'],['bi-3-circle-fill','Recibirás actualizaciones de estado'],['bi-4-circle-fill','El ticket se cierra al resolverse']] as [$ic,$tx]):?>
        <div class="d-flex align-items-start gap-2 mb-2" style="font-size:.84rem"><i class="<?=$ic?> text-primary mt-1" style="flex-shrink:0"></i><span><?=$tx?></span></div>
        <?php endforeach;?>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__.'/../layouts/footer.php';?>
