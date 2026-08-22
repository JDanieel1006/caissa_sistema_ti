<?php
$folio   = str_pad($mant['id'], 6, '0', STR_PAD_LEFT);
$logoUrl = $_SERVER['DOCUMENT_ROOT'] . '/helpdesk/public/img/logo.jpg';
$logoB64 = file_exists($logoUrl) ? 'data:image/jpeg;base64,'.base64_encode(file_get_contents($logoUrl)) : '';
$tip     = ['preventivo'=>'Mantenimiento Preventivo','correctivo'=>'Mantenimiento Correctivo'][$mant['tipo']] ?? $mant['tipo'];
$esStarlink = stripos($mant['categoria_nombre'] ?? '', 'starlink') !== false;
$specsMap = [];
foreach (($specs ?? []) as $s) {
    if (!empty($s['valor'])) $specsMap[$s['nombre_campo']] = ['etiqueta' => $s['etiqueta'], 'valor' => $s['valor']];
}
$starlinkSpecs = [
    'tipo_kit',
    'modelo_power_supply',
    'serie_power_supply',
    'modelo_router',
    'serie_router',
    'mac_router',
];
$starlinkServicio = [
    'plan_servicio',
    'tipo_servicio',
    'id_servicio',
    'estado_servicio',
];
$starlinkInstalacion = ['ubicacion_instalacion'];
?><!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><title>Vale Mantenimiento <?=$folio?></title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:Arial,Helvetica,sans-serif;font-size:10px;color:#000;background:#fff;}
.page{width:210mm;min-height:297mm;margin:0 auto;padding:8mm 10mm;}
.folio-row{display:flex;justify-content:flex-end;margin-bottom:1mm;}
.folio-row .f-label{border:1px solid #000;border-right:none;padding:2px 8px;font-weight:bold;}
.folio-row .f-value{border:1px solid #000;padding:2px 14px;min-width:32mm;text-align:center;font-weight:bold;}
.header-outer{width:100%;border-collapse:collapse;border:1.5px solid #000;}
.header-outer td{border:1px solid #000;vertical-align:middle;}
.h-logo{width:36mm;text-align:center;padding:6px 8px;}
.h-logo img{max-width:30mm;max-height:20mm;display:block;margin:0 auto;}
.h-company{text-align:center;padding:6px 10px;font-size:10px;font-weight:bold;line-height:1.5;}
.h-title{text-align:center;padding:6px 10px;font-size:12px;font-weight:bold;}
.h-info{width:44mm;padding:0;vertical-align:top!important;}
.h-info-row{padding:2px 5px;border-bottom:1px solid #000;line-height:1.5;}
.h-info-row:last-child{border-bottom:none;}
.h-info-label{font-size:7.5px;display:block;}
.h-info-val{font-size:9px;font-weight:bold;display:block;}
.data-grid{width:100%;border-collapse:collapse;border:1.5px solid #000;margin-top:5px;}
.data-grid td{border:1px solid #000;padding:2px 5px;font-size:10px;vertical-align:middle;height:7mm;}
.data-grid .lbl{font-weight:bold;white-space:nowrap;width:34mm;background:#f9f9f9;}
.items-table{width:100%;border-collapse:collapse;border:1.5px solid #000;margin-top:5px;}
.items-table th{border:1px solid #000;padding:3px 4px;font-size:10px;font-weight:bold;text-align:center;background:#f0f0f0;}
.items-table td{border:1px solid #000;padding:4px;font-size:10px;vertical-align:top;}
.spec-title{font-weight:bold;background:#f0f0f0;text-align:left;}
.c-num{width:9mm;text-align:center;}
.c-act{text-align:left;}
.c-estado{width:28mm;text-align:center;}
.c-obs{width:40mm;}
.firma-row{display:flex;justify-content:space-between;margin-top:14mm;gap:20mm;}
.firma-box{flex:1;text-align:center;}
.firma-line{border-top:1px solid #000;padding-top:3px;font-size:9px;font-weight:bold;}
.obs-section{margin-top:5mm;border:1px solid #000;}
.obs-header{border-bottom:1px solid #000;padding:2px 6px;font-weight:bold;font-size:10px;}
.obs-body{padding:4px 6px;min-height:18mm;font-size:10px;line-height:1.6;}
@media print{body{margin:0;}.no-print{display:none!important;}.page{padding:6mm 8mm;}}
.no-print{text-align:center;padding:8px;background:#f0f0f0;border-bottom:1px solid #ccc;position:sticky;top:0;z-index:99;}
.no-print button{padding:6px 20px;margin:0 4px;border-radius:5px;border:none;cursor:pointer;font-size:13px;}
.btn-p{background:#0d6efd;color:#fff;}.btn-c{background:#6c757d;color:#fff;}
</style></head><body>
<div class="no-print">
  <button class="btn-p" onclick="window.print()">🖨 Imprimir</button>
  <button class="btn-c" onclick="window.close()">✕ Cerrar</button>
</div>
<div class="page">
  <div class="folio-row">
    <span class="f-label">FOLIO:</span>
    <span class="f-value"><?=$folio?></span>
  </div>
  <table class="header-outer">
    <tr>
      <td class="h-logo" rowspan="2">
        <?php if($logoB64):?><img src="<?=$logoB64?>" alt="CAISSA"><?php else:?><strong style="font-size:13px">CAISSA</strong><?php endif;?>
      </td>
      <td class="h-company">CAPACITACIÓN, AUTOMATIZACIÓN INDUSTRIAL, SERVICIOS Y SOLUCIONES AVANZADAS, S.A DE C.V</td>
      <td class="h-info" rowspan="2">
        <div class="h-info-row"><span class="h-info-label">Clave:</span><span class="h-info-val">CAISSA – GRC – MT – 001</span></div>
        <div class="h-info-row"><span class="h-info-label">Fecha de Emisión:</span><span class="h-info-val">27 Oct 2025</span></div>
        <div class="h-info-row"><span class="h-info-label">Próxima Revisión:</span><span class="h-info-val">27 Sep 2026</span></div>
        <div class="h-info-row"><span class="h-info-val">Versión 3</span></div>
      </td>
    </tr>
    <tr><td class="h-title">Orden de <?=$tip?></td></tr>
  </table>

  <table class="data-grid">
    <tr>
      <td class="lbl">EQUIPO / CÓDIGO:</td>
      <td><?=htmlspecialchars($mant['equipo_codigo'].' — '.($mant['equipo_marca']??'').' '.($mant['equipo_modelo']??''))?></td>
      <td class="lbl">TIPO:</td>
      <td><?=$tip?></td>
    </tr>
    <tr>
      <td class="lbl">CATEGORÍA:</td>
      <td><?=htmlspecialchars($mant['categoria_nombre'])?></td>
      <td class="lbl">FECHA PROGRAMADA:</td>
      <td><?=date('d/m/Y',strtotime($mant['fecha_programada']))?></td>
    </tr>
    <tr>
      <td class="lbl">TÉCNICO RESPONSABLE:</td>
      <td><?=htmlspecialchars($mant['nombre_tecnico']??'Sin asignar')?></td>
      <td class="lbl">FECHA REALIZADA:</td>
      <td><?=$mant['fecha_realizada']?date('d/m/Y',strtotime($mant['fecha_realizada'])):'_______________'?></td>
    </tr>
    <?php if($mant['equipo_ubicacion']):?>
    <tr>
      <td class="lbl">UBICACIÓN:</td>
      <td colspan="3"><?=htmlspecialchars($mant['equipo_ubicacion'])?></td>
    </tr>
    <?php endif;?>
  </table>

  <?php if ($esStarlink && !empty($specsMap)): ?>
  <table class="items-table" style="margin-top:5px">
    <thead><tr><th colspan="4" class="spec-title">ESPECIFICACIONES STARLINK</th></tr></thead>
    <tbody>
      <?php foreach (array_chunk($starlinkSpecs, 2) as $par): ?>
      <tr>
        <?php foreach ($par as $campo): ?>
        <td class="lbl" style="width:28mm"><?= htmlspecialchars($specsMap[$campo]['etiqueta'] ?? '') ?></td>
        <td><?= htmlspecialchars($specsMap[$campo]['valor'] ?? '') ?></td>
        <?php endforeach; ?>
        <?php if (count($par) === 1): ?><td></td><td></td><?php endif; ?>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <table class="items-table" style="margin-top:5px">
    <thead><tr><th colspan="4" class="spec-title">SERVICIO STARLINK</th></tr></thead>
    <tbody>
      <?php foreach (array_chunk($starlinkServicio, 2) as $par): ?>
      <tr>
        <?php foreach ($par as $campo): ?>
        <td class="lbl" style="width:28mm"><?= htmlspecialchars($specsMap[$campo]['etiqueta'] ?? '') ?></td>
        <td><?= htmlspecialchars($specsMap[$campo]['valor'] ?? '') ?></td>
        <?php endforeach; ?>
        <?php if (count($par) === 1): ?><td></td><td></td><?php endif; ?>
      </tr>
      <?php endforeach; ?>
      <?php foreach ($starlinkInstalacion as $campo): if (!empty($specsMap[$campo]['valor'])): ?>
      <tr>
        <td class="lbl">UBICACIÓN DE INSTALACIÓN:</td>
        <td colspan="3"><?= htmlspecialchars($specsMap[$campo]['valor']) ?></td>
      </tr>
      <?php endif; endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>

  <table class="items-table" style="margin-top:5px">
    <thead><tr>
      <th class="c-num">No.</th>
      <th class="c-act">Actividad / Descripción</th>
      <th class="c-estado">Estado</th>
      <th class="c-obs">Observaciones</th>
    </tr></thead>
    <tbody>
      <tr>
        <td class="c-num">1</td>
        <td class="c-act" style="line-height:1.6"><?=nl2br(htmlspecialchars($mant['descripcion']??'Sin descripción'))?></td>
        <td class="c-estado"></td>
        <td class="c-obs"></td>
      </tr>
      <?php for($i=0;$i<6;$i++):?>
      <tr><td class="c-num">&nbsp;</td><td class="c-act" style="height:8mm"></td><td class="c-estado"></td><td class="c-obs"></td></tr>
      <?php endfor;?>
    </tbody>
  </table>

  <div class="firma-row">
    <div class="firma-box"><div class="firma-line">TÉCNICO: NOMBRE Y FIRMA</div></div>
    <div class="firma-box"><div class="firma-line">SUPERVISÓ: NOMBRE Y FIRMA</div></div>
  </div>

  <div class="obs-section">
    <div class="obs-header">OBSERVACIONES:</div>
    <div class="obs-body"><?=nl2br(htmlspecialchars($mant['notas']??''))?></div>
  </div>
</div>
<script>window.addEventListener('load',()=>setTimeout(()=>window.print(),400));</script>
</body></html>
