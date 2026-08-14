<?php
// views/asignaciones/acta_pdf.php

// Construir descripción del equipo con todas sus especificaciones
$descripcion = trim(($asig['equipo_marca'] ?? '') . ' ' . ($asig['equipo_modelo'] ?? ''));
$especsLinea = [];
foreach ($specs as $s) {
    if ($s['valor']) $especsLinea[] = '<strong>' . htmlspecialchars($s['etiqueta']) . ':</strong> ' . htmlspecialchars($s['valor']);
}
if (!empty($especsLinea)) $descripcion .= ' — ' . implode(', ', $especsLinea);
if ($asig['equipo_serie']) $descripcion .= '. No. Serie: ' . $asig['equipo_serie'];

$roleLabels = [
    'auxiliar_administrativo' => 'Auxiliar Administrativo',
    'coordinador'             => 'Coordinador',
    'operario'                => 'Operario',
    'ayudante'                => 'Ayudante',
    'residente_becario'       => 'Residente/Becario',
    'auxiliar_seguridad'      => 'Auxiliar de Seguridad',
    'auxiliar_oficina'        => 'Auxiliar de Oficina',
    'control_de_obra'         => 'Control de Obra',
    'supervisor_seguridad'    => 'Supervisor de Seguridad',
    'contra_incendios'        => 'Contra Incendios',
    'tecnico_instrumentista'  => 'Técnico Instrumentista',
    'admin'                   => 'Administrador',
    'tecnico'                 => 'Técnico',
    'maestro'                 => 'Maestro',
];
$puesto  = $roleLabels[$asig['rol_usuario'] ?? ''] ?? ucwords(str_replace('_', ' ', $asig['rol_usuario'] ?? ''));
$folio   = str_pad($asig['id'], 6, '0', STR_PAD_LEFT);
$logoUrl = '../public/img/logo.jpg'; // Ruta relativa desde views/asignaciones/
$totalFilasVacias = max(0, 8 - 1); // 8 filas totales menos 1 (el equipo asignado)
?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Vale <?= $folio ?> — CAISSA</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: Arial, Helvetica, sans-serif; font-size:10px; color:#000; background:#fff; }
.page { width:210mm; min-height:297mm; margin:0 auto; padding:8mm 10mm; }

/* FOLIO */
.folio-row { display:flex; justify-content:flex-end; margin-bottom:1mm; }
.folio-row .f-label { border:1px solid #000; border-right:none; padding:2px 8px; font-weight:bold; font-size:10px; }
.folio-row .f-value { border:1px solid #000; padding:2px 14px; font-size:10px; min-width:32mm; text-align:center; font-weight:bold; }

/* HEADER */
.header-outer { width:100%; border-collapse:collapse; border:1.5px solid #000; }
.header-outer td { border:1px solid #000; vertical-align:middle; }
.h-logo { width:36mm; text-align:center; padding:6px 8px; }
.h-logo img { max-width:30mm; max-height:20mm; display:block; margin:0 auto; }
.h-company { text-align:center; padding:6px 10px; font-size:10px; font-weight:bold; line-height:1.5; }
.h-title { text-align:center; padding:6px 10px; font-size:13px; font-weight:bold; }
.h-info { width:44mm; padding:0; vertical-align:top !important; }
.h-info-row { padding:2px 5px; border-bottom:1px solid #000; line-height:1.5; }
.h-info-row:last-child { border-bottom:none; }
.h-info-label { font-size:7.5px; display:block; }
.h-info-val { font-size:9px; font-weight:bold; display:block; }

/* DATOS SOLICITANTE */
.data-grid { width:100%; border-collapse:collapse; border:1.5px solid #000; margin-top:5px; }
.data-grid td { border:1px solid #000; padding:2px 5px; font-size:10px; vertical-align:middle; height:7mm; }
.data-grid .lbl { font-weight:bold; white-space:nowrap; width:30mm; }
.data-grid .val { width:50mm; }

/* TABLA ARTÍCULOS */
.items-table { width:100%; border-collapse:collapse; border:1.5px solid #000; margin-top:5px; }
.items-table th { border:1px solid #000; padding:3px 4px; font-size:10px; font-weight:bold; text-align:center; }
.items-table td { border:1px solid #000; padding:2px 4px; font-size:10px; vertical-align:middle; height:7mm; }
.c-num   { width:9mm;  text-align:center; }
.c-code  { width:22mm; }
.c-desc  { }
.c-unit  { width:18mm; text-align:center; }
.c-qty   { width:14mm; text-align:center; }
.c-brand { width:20mm; text-align:center; }
.c-obs   { width:35mm; }

/* FIRMAS */
.firma-row { display:flex; justify-content:space-between; margin-top:12mm; gap:20mm; }
.firma-box { flex:1; text-align:center; }
.firma-line { border-top:1px solid #000; padding-top:3px; font-size:9px; font-weight:bold; }

/* OBSERVACIONES */
.obs-section { margin-top:5mm; border:1px solid #000; }
.obs-header { border-bottom:1px solid #000; padding:2px 6px; font-weight:bold; font-size:10px; }
.obs-body { padding:4px 6px; min-height:20mm; font-size:10px; line-height:1.5; }

/* PRINT */
@media print {
    body { margin:0; }
    .no-print { display:none !important; }
    .page { padding:6mm 8mm; }
}
.no-print { text-align:center; padding:8px; background:#f0f0f0; border-bottom:1px solid #ccc; position:sticky; top:0; z-index:99; }
.no-print button { padding:6px 20px; margin:0 4px; border-radius:5px; border:none; cursor:pointer; font-size:13px; }
.btn-p { background:#0d6efd; color:#fff; }
.btn-c { background:#6c757d; color:#fff; }
</style>
</head>
<body>

<div class="no-print">
  <button class="btn-p" onclick="window.print()">🖨 Imprimir</button>
  <button class="btn-c" onclick="window.close()">✕ Cerrar</button>
</div>

<div class="page">

  <!-- FOLIO -->
  <div class="folio-row">
    <span class="f-label">FOLIO:</span>
    <span class="f-value"><?= $folio ?></span>
  </div>

  <!-- HEADER -->
  <table class="header-outer">
    <tr>
      <td class="h-logo" rowspan="2">
        <img src="https://tech-ti.caissa-mx.com/public/img/logo.jpg" alt="CAISSA">
      </td>
      <td class="h-company">
        CAPACITACIÓN, AUTOMATIZACIÓN INDUSTRIAL, SERVICIOS Y SOLUCIONES AVANZADAS, S.A DE C.V
      </td>
      <td class="h-info" rowspan="2">
        <div class="h-info-row">
          <span class="h-info-label">Clave:</span>
          <span class="h-info-val">CAISSA – GRC – MT – 001</span>
        </div>
        <div class="h-info-row">
          <span class="h-info-label">Fecha de Emisión:</span>
          <span class="h-info-val">27 Oct 2025</span>
        </div>
        <div class="h-info-row">
          <span class="h-info-label">Próxima Revisión:</span>
          <span class="h-info-val">27 Sep 2026</span>
        </div>
        <div class="h-info-row">
          <span class="h-info-val">Versión 3</span>
        </div>
      </td>
    </tr>
    <tr>
      <td class="h-title">Formato de Vale para Entrega de Equipos y Herramientas</td>
    </tr>
  </table>

  <!-- DATOS SOLICITANTE -->
  <table class="data-grid">
    <tr>
      <td class="lbl">NOMBRE:</td>
      <td class="val"><?= htmlspecialchars($asig['nombre_usuario']) ?></td>
      <td class="lbl">PUESTO:</td>
      <td><?= htmlspecialchars($puesto) ?></td>
    </tr>
    <tr>
      <td class="lbl">DEPARTAMENTO:</td>
      <td><?= htmlspecialchars($asig['dept_usuario'] ?? '') ?></td>
      <td class="lbl">FECHA:</td>
      <td><?= date('d/m/Y', strtotime($asig['fecha_asignacion'])) ?></td>
    </tr>
    <tr>
      <td class="lbl">NOMBRE DE LA OBRA:</td>
      <td><?= htmlspecialchars($asig['nombre_obra'] ?? '') ?></td>
      <td class="lbl">No. DE CTTO:</td>
      <td><?= htmlspecialchars($asig['numero_contrato'] ?? '') ?></td>
    </tr>
  </table>

  <!-- ARTÍCULOS -->
  <table class="items-table">
    <thead>
      <tr>
        <th class="c-num">No.</th>
        <th class="c-code">Código</th>
        <th class="c-desc">Descripción</th>
        <th class="c-unit">Unidad</th>
        <th class="c-qty">Cant.</th>
        <th class="c-brand">Marca</th>
        <th class="c-obs">Observaciones</th>
      </tr>
    </thead>
    <tbody>
      <!-- Equipo asignado -->
      <tr>
        <td class="c-num">1</td>
        <td class="c-code"><?= htmlspecialchars($asig['equipo_codigo']) ?></td>
        <td style="line-height:1.5"><?= nl2br($descripcion) ?></td>
        <td class="c-unit">PZA</td>
        <td class="c-qty">1</td>
        <td class="c-brand"><?= htmlspecialchars(strtoupper($asig['equipo_marca'] ?? '')) ?></td>
        <td class="c-obs"><?= htmlspecialchars($asig['notas_entrega'] ?? '') ?></td>
      </tr>
      <!-- Filas vacías -->
      <?php for ($i = 0; $i < $totalFilasVacias; $i++): ?>
      <tr>
        <td class="c-num">&nbsp;</td>
        <td class="c-code"></td>
        <td class="c-desc"></td>
        <td class="c-unit"></td>
        <td class="c-qty"></td>
        <td class="c-brand"></td>
        <td class="c-obs"></td>
      </tr>
      <?php endfor; ?>
    </tbody>
  </table>

  <!-- FIRMAS -->
  <div class="firma-row">
    <div class="firma-box">
      <div class="firma-line">ENTREGO: NOMBRE Y FIRMA</div>
    </div>
    <div class="firma-box">
      <div class="firma-line">RECIBIO: NOMBRE Y FIRMA</div>
    </div>
  </div>

  <!-- OBSERVACIONES -->
  <div class="obs-section">
    <div class="obs-header">OBSERVACIONES:</div>
    <div class="obs-body"></div>
  </div>

</div>

<script>
  window.addEventListener('load', () => setTimeout(() => window.print(), 400));
</script>
</body>
</html>
