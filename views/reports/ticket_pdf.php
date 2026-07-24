<?php
$school='Centro de Cómputo Escolar';
$est=$estados[$ticket['estado']]??['label'=>$ticket['estado'],'color'=>'secondary'];
$pri=$prioridades[$ticket['prioridad']]??['label'=>$ticket['prioridad'],'color'=>'secondary'];
$cat=$categorias[$ticket['categoria']]??['label'=>$ticket['categoria'],'icon'=>'bi-tools'];
?><!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Ticket <?=$ticket['folio']?></title>
<style>*{box-sizing:border-box;margin:0;padding:0;}body{font-family:'Segoe UI',Arial,sans-serif;font-size:10.5pt;color:#000;padding:1.5cm;}
.header{display:flex;justify-content:space-between;align-items:flex-start;border-bottom:2px solid #0077ff;padding-bottom:12px;margin-bottom:16px;}
.school-name{font-size:13pt;font-weight:700;color:#0d1b2a;}.school-sub{font-size:9pt;color:#555;margin-top:2px;}
.folio{font-family:monospace;font-size:12pt;font-weight:800;color:#0044bb;}.badge{display:inline-block;padding:2px 8px;border-radius:4px;font-size:9pt;font-weight:700;margin-left:6px;}
.badge-primary{background:#cfe2ff;color:#084298;}.badge-success{background:#d1e7dd;color:#0a3622;}.badge-warning{background:#fff3cd;color:#664d03;}.badge-danger{background:#f8d7da;color:#842029;}.badge-secondary{background:#e2e3e5;color:#41464b;}.badge-dark{background:#333;color:#fff;}.badge-info{background:#cff4fc;color:#055160;}
.seccion{font-size:10pt;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#0077ff;border-bottom:1px solid #dde4ef;padding-bottom:4px;margin:16px 0 8px;}
table.info{width:100%;border-collapse:collapse;margin-bottom:12px;}
table.info td{padding:5px 10px;border:1px solid #dde4ef;font-size:10pt;}table.info td:first-child{background:#f7faff;font-weight:600;width:30%;}
.descripcion{background:#f7faff;border:1px solid #dde4ef;border-radius:6px;padding:12px;font-size:10pt;line-height:1.6;white-space:pre-wrap;margin-bottom:12px;}
.comentario{border:1px solid #dde4ef;border-radius:6px;padding:8px 12px;margin-bottom:8px;font-size:10pt;}
.comentario.interno{background:#fffde7;border-color:#ffe58a;}
.comentario-meta{font-size:9pt;color:#555;margin-bottom:4px;}
.historial-item{padding:4px 0;font-size:10pt;border-bottom:1px solid #f0f4f9;}
.firmas{display:grid;grid-template-columns:1fr 1fr;gap:60px;margin-top:40px;}
.firma-linea{border-top:1px solid #000;margin:50px 20px 6px;}.firma-nombre{font-weight:700;font-size:10pt;text-align:center;}.firma-cargo{font-size:9pt;color:#555;text-align:center;}
.btn-print{position:fixed;bottom:20px;right:20px;background:#0077ff;color:#fff;border:none;border-radius:10px;padding:10px 20px;cursor:pointer;font-size:13pt;box-shadow:0 4px 15px rgba(0,119,255,.4);}
@media print{.btn-print{display:none;}@page{margin:1.5cm;size:letter;}}
</style></head><body>
<div class="header">
  <div><div class="school-name"><?=$school?></div><div class="school-sub">Mesa de Ayuda — Reporte de Ticket</div></div>
  <div class="text-right"><div class="folio"><?=htmlspecialchars($ticket['folio'])?></div><span class="badge badge-<?=$est['color']?>"><?=$est['label']?></span><span class="badge badge-<?=$pri['color']?>"><?=$pri['label']?></span></div>
</div>
<div style="font-size:14pt;font-weight:800;margin-bottom:4px"><?=htmlspecialchars($ticket['titulo'])?></div>
<div style="font-size:9pt;color:#555;margin-bottom:16px">Generado el <?=date('d/m/Y H:i')?> · Solicitante: <?=htmlspecialchars($ticket['nombre_usuario'])?></div>
<div class="seccion">Información del Ticket</div>
<table class="info">
  <tr><td>Categoría</td><td><?=$cat['label']?></td><td>Prioridad</td><td><span class="badge badge-<?=$pri['color']?>"><?=$pri['label']?></span></td></tr>
  <tr><td>Estado</td><td><span class="badge badge-<?=$est['color']?>"><?=$est['label']?></span></td><td>Técnico</td><td><?=htmlspecialchars($ticket['nombre_tecnico']??'Sin asignar')?></td></tr>
  <tr><td>Solicitante</td><td><?=htmlspecialchars($ticket['nombre_usuario'])?></td><td>Ubicación</td><td><?=htmlspecialchars($ticket['ubicacion']??'—')?></td></tr>
  <tr><td>Fecha apertura</td><td><?=date('d/m/Y H:i',strtotime($ticket['creado_en']))?></td><td>Fecha cierre</td><td><?=$ticket['cerrado_en']?date('d/m/Y H:i',strtotime($ticket['cerrado_en'])):'Pendiente'?></td></tr>
</table>
<div class="seccion">Descripción</div>
<div class="descripcion"><?=htmlspecialchars($ticket['descripcion'])?></div>
<?php if($ticket['resolucion']):?>
<div class="seccion">Resolución</div>
<div class="descripcion" style="background:#d1e7dd;border-color:#b8dfc4"><?=htmlspecialchars($ticket['resolucion'])?></div>
<?php endif;?>
<?php if(!empty($comentarios)):?>
<div class="seccion">Comentarios (<?=count($comentarios)?>)</div>
<?php foreach($comentarios as $c):?>
<div class="comentario <?=$c['es_interno']?'interno':''?>">
  <div class="comentario-meta"><strong><?=htmlspecialchars($c['nombre_usuario'])?></strong> · <?=date('d/m/Y H:i',strtotime($c['creado_en']))?><?=$c['es_interno']?' · Nota interna':''?></div>
  <div style="white-space:pre-wrap"><?=htmlspecialchars($c['mensaje'])?></div>
</div>
<?php endforeach;?>
<?php endif;?>
<?php if(!empty($historial)):?>
<div class="seccion">Historial de Estados</div>
<?php foreach($historial as $h):$hn=Ticket::ESTADOS[$h['estado_nuevo']]??['label'=>$h['estado_nuevo'],'color'=>'secondary'];?>
<div class="historial-item"><?=date('d/m/Y H:i',strtotime($h['creado_en']))?> · <strong><?=htmlspecialchars($h['nombre_usuario'])?></strong> → <span class="badge badge-<?=$hn['color']?>"><?=$hn['label']?></span><?=$h['nota']?' — '.htmlspecialchars($h['nota']):''?></div>
<?php endforeach;?>
<?php endif;?>
<div class="firmas">
  <div><div class="firma-linea"></div><div class="firma-nombre"><?=htmlspecialchars($ticket['nombre_usuario'])?></div><div class="firma-cargo">Solicitante</div></div>
  <div><div class="firma-linea"></div><div class="firma-nombre"><?=htmlspecialchars($ticket['nombre_tecnico']??'Técnico de Soporte')?></div><div class="firma-cargo">Técnico Responsable</div></div>
</div>
<button class="btn-print" onclick="window.print()">🖨 Imprimir</button>
</body></html>
