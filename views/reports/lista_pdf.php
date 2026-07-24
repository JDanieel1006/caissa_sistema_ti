<?php
$school='Centro de Cómputo Escolar';
$totalT=count($tickets);$abiertos=$stats['por_estado']['abierto']??0;$resueltos=$stats['por_estado']['resuelto']??0;
?><!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Reporte de Tickets</title>
<style>*{box-sizing:border-box;margin:0;padding:0;}body{font-family:'Segoe UI',Arial,sans-serif;font-size:10pt;color:#000;padding:1.5cm;}
.header{display:flex;justify-content:space-between;align-items:flex-start;border-bottom:2px solid #0077ff;padding-bottom:12px;margin-bottom:16px;}
.school-name{font-size:13pt;font-weight:700;}.school-sub{font-size:9pt;color:#555;margin-top:2px;}
.badge{display:inline-block;padding:1px 7px;border-radius:4px;font-size:8.5pt;font-weight:700;}
.badge-primary{background:#cfe2ff;color:#084298;}.badge-success{background:#d1e7dd;color:#0a3622;}.badge-warning{background:#fff3cd;color:#664d03;}.badge-danger{background:#f8d7da;color:#842029;}.badge-secondary{background:#e2e3e5;color:#41464b;}.badge-dark{background:#333;color:#fff;}.badge-info{background:#cff4fc;color:#055160;}
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:16px;}
.stat-box{background:#f7faff;border:1px solid #dde4ef;border-radius:6px;padding:8px 12px;}
.stat-num{font-size:18pt;font-weight:800;color:#0d1b2a;line-height:1;}.stat-lbl{font-size:8pt;color:#6b7c93;}
table{width:100%;border-collapse:collapse;font-size:9.5pt;}th{background:#0d1b2a;color:#fff;padding:6px 8px;text-align:left;font-size:8.5pt;letter-spacing:.04em;}
td{padding:5px 8px;border-bottom:1px solid #f0f4f9;vertical-align:top;}tr:hover{background:#f7faff;}
.folio{font-family:monospace;font-size:9pt;font-weight:700;color:#0044bb;}
.btn-print{position:fixed;bottom:20px;right:20px;background:#0077ff;color:#fff;border:none;border-radius:10px;padding:10px 20px;cursor:pointer;font-size:13pt;box-shadow:0 4px 15px rgba(0,119,255,.4);}
@media print{.btn-print{display:none;}@page{margin:1.5cm;size:letter landscape;}}
</style></head><body>
<div class="header">
  <div><div class="school-name"><?=$school?></div><div class="school-sub">Mesa de Ayuda — Reporte de Tickets · Generado el <?=date('d/m/Y H:i')?></div></div>
  <div style="font-size:9pt;color:#555;text-align:right"><?=$totalT?> tickets<?php if(!empty($_GET['estado'])):?> · Estado: <?=$estados[$_GET['estado']]['label']??$_GET['estado']?><?php endif;?></div>
</div>
<div class="stats">
  <div class="stat-box"><div class="stat-num"><?=$totalT?></div><div class="stat-lbl">Total</div></div>
  <div class="stat-box"><div class="stat-num"><?=$abiertos?></div><div class="stat-lbl">Abiertos</div></div>
  <div class="stat-box"><div class="stat-num"><?=$resueltos?></div><div class="stat-lbl">Resueltos</div></div>
  <div class="stat-box"><div class="stat-num"><?=$stats['semana']??0?></div><div class="stat-lbl">Esta semana</div></div>
</div>
<table>
  <thead><tr><th>Folio</th><th>Título</th><?php if(in_array($_SESSION['user_rol'],['admin','tecnico'])):?><th>Solicitante</th><?php endif;?><th>Categoría</th><th>Prioridad</th><th>Estado</th><th>Técnico</th><th>Fecha</th></tr></thead>
  <tbody>
  <?php foreach($tickets as $t):$est=$estados[$t['estado']]??['label'=>$t['estado'],'color'=>'secondary'];$pri=$prioridades[$t['prioridad']]??['label'=>$t['prioridad'],'color'=>'secondary'];$cat=$categorias[$t['categoria']]??['label'=>$t['categoria']];?>
  <tr>
    <td class="folio"><?=htmlspecialchars($t['folio'])?></td>
    <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?=htmlspecialchars($t['titulo'])?></td>
    <?php if(in_array($_SESSION['user_rol'],['admin','tecnico'])):?><td style="font-size:9pt"><?=htmlspecialchars($t['nombre_usuario'])?></td><?php endif;?>
    <td style="font-size:9pt"><?=$cat['label']?></td>
    <td><span class="badge badge-<?=$pri['color']?>"><?=$pri['label']?></span></td>
    <td><span class="badge badge-<?=$est['color']?>"><?=$est['label']?></span></td>
    <td style="font-size:9pt"><?=htmlspecialchars($t['nombre_tecnico']??'—')?></td>
    <td style="font-size:9pt;white-space:nowrap"><?=date('d/m/y',strtotime($t['creado_en']))?></td>
  </tr>
  <?php endforeach;?>
  </tbody>
</table>
<button class="btn-print" onclick="window.print()">🖨 Imprimir</button>
</body></html>
