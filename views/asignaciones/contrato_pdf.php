<?php
$logoUrl = 'https://tech-ti.caissa-mx.com/public/img/logo.jpg';

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

$nombre  = htmlspecialchars($asig['nombre_usuario'] ?? '');
$cargo   = htmlspecialchars($roleLabels[$asig['rol_usuario'] ?? ''] ?? ucwords(str_replace('_', ' ', $asig['rol_usuario'] ?? '')));
$correo  = htmlspecialchars($asig['email_usuario']  ?? '');
$proceso = htmlspecialchars($asig['dept_usuario']   ?? '');
$tipo    = htmlspecialchars($asig['categoria_nombre'] ?? '');
$marca   = htmlspecialchars($asig['equipo_marca']     ?? '');
$modelo  = htmlspecialchars($asig['equipo_modelo']    ?? '');
$serial  = htmlspecialchars($asig['equipo_serie']     ?? '');

$procesador = ''; $ram = ''; $disco = ''; $cargador = '';
foreach ($specs as $s) {
    switch ($s['nombre_campo']) {
        case 'procesador':       $procesador  = htmlspecialchars($s['valor']); break;
        case 'frecuencia':       $procesador .= ($procesador ? ' ' : '') . htmlspecialchars($s['valor']); break;
        case 'ram':              $ram         = htmlspecialchars($s['valor']); break;
        case 'almacenamiento':   $disco       = htmlspecialchars($s['valor']); break;
        case 'incluye_cargador': $cargador    = htmlspecialchars($s['valor']); break;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Acta de Entrega — <?= $nombre ?></title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000; background: #fff; }

.page {
    width: 210mm;
    min-height: 277mm;
    margin: 0 auto 0;
    padding: 12mm 15mm;
    page-break-after: always;
}
.page:last-child { page-break-after: auto; }

/* Encabezado */
.header { display:flex; align-items:center; gap:10px; border-bottom:2px solid #000; padding-bottom:8px; margin-bottom:10px; }
.header img { max-height:45px; max-width:110px; }
.header .company { font-size:9pt; font-weight:bold; text-transform:uppercase; line-height:1.4; }

/* Título */
.doc-title { font-size:11pt; font-weight:bold; text-transform:uppercase; text-align:center; margin:8px 0 10px; }

/* Texto reglamento */
.intro { font-size:9.5pt; line-height:1.6; margin-bottom:8px; text-align:justify; }
.section-title { font-size:10pt; font-weight:bold; text-transform:uppercase; margin:10px 0 4px; }
.reglamento { font-size:9.5pt; line-height:1.6; text-align:justify; margin-bottom:6px; }
ul.rl { padding-left:16px; margin-bottom:6px; }
ul.rl li { font-size:9.5pt; line-height:1.6; margin-bottom:4px; text-align:justify; }

/* Observaciones líneas */
.obs-label { font-size:10pt; font-weight:bold; margin-bottom:5px; }
.obs-line { border-bottom:1px solid #000; height:16px; margin-bottom:5px; }

/* Tablas */
.data-table { width:100%; border-collapse:collapse; margin:8px 0; font-size:9.5pt; }
.data-table .th-header { background:#2E75B6; color:#fff; font-weight:bold; text-align:center; padding:5px 6px; text-transform:uppercase; border:1px solid #2E75B6; }
.data-table .lbl { background:#D6E4F0; font-weight:bold; text-transform:uppercase; padding:4px 6px; border:1px solid #999; font-size:8.5pt; text-align:center; }
.data-table .val { border:1px solid #999; padding:4px 6px; min-height:18px; }

/* Certificación */
.cert-table { width:100%; border-collapse:collapse; margin:8px 0; }
.cert-table .th-header { background:#2E75B6; color:#fff; font-weight:bold; text-align:center; padding:5px 6px; border:1px solid #2E75B6; text-transform:uppercase; }
.cert-table .cert-body { border:1px solid #999; padding:8px 10px; font-size:9.5pt; line-height:1.6; text-align:justify; background:#f9f9f9; }

/* Firmas */
.firma-row { display:flex; justify-content:space-around; margin-top:30px; gap:20mm; }
.firma-box { flex:1; text-align:center; }
.firma-line { border-top:1px solid #000; padding-top:4px; font-size:9pt; font-weight:bold; }

/* Print */
@media print {
    .no-print { display:none !important; }
    body { margin:0; }
    .page { padding:10mm 12mm; }
    @page { margin:0; size:letter; }
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

<!-- ══════════════════════════════════════════
     PÁGINA 1 — Reglamento
══════════════════════════════════════════ -->
<div class="page">

  <div class="header">
    <img src="<?= $logoUrl ?>" alt="CAISSA">
    <div class="company">Capacitación, Automatización Industrial,<br>Servicios y Soluciones Avanzadas. S.A. DE C.V.</div>
  </div>

  <div class="doc-title">Acta de Entrega y Reglamento de Uso de Equipos</div>

  <p class="intro">
    Por medio del presente documento se dispone la entrega de los siguientes equipos, propiedad de <strong>CAISSA</strong>., como herramienta de trabajo al colaborador(a):
    &nbsp;<u><?= $nombre ?></u>&nbsp;
    identificado, quien para efectos de lo aquí relacionado se denominará <strong>EL USUARIO</strong>,
    comprometiéndose a cumplir como mínimo los términos aquí expresados.
  </p>

  <div class="section-title">Seguridad Física</div>
  <ul class="rl">
    <li>Manipular el equipo con el mayor cuidado, evitando golpes, rayones, presiones o temperaturas excesivas y ambientes muy contaminados. Mantener líquidos y otras sustancias nocivas alejadas de el/los equipos.</li>
    <li>Dejar el/los equipos bajo llave o vigilancia, cuando no lo(s) esté utilizando.</li>
    <li>Abstenerse de abrir las cubiertas de el/los equipos, o manipular sus componentes internos y sellos de garantía.</li>
  </ul>
  <p class="reglamento">En caso de presentarse daño o pérdida por descuido o negligencia en el cumplimiento de los anteriores puntos, su compensación económica podrá ser exigida a <strong>EL USUARIO</strong> por parte de la empresa CAISSA.</p>

  <div class="section-title">Seguridad Lógica y Productividad</div>
  <p class="reglamento">Establecer y habilitar claves personales para el acceso al equipo, tanto en encendido como en protector de pantalla. En caso de mantenimiento y supervisión por parte de la empresa <strong>CAISSA</strong>, o ausencia prolongada, el equipo deberá ser entregado con las claves deshabilitadas.</p>
  <ul class="rl">
    <li>No instalar ni utilizar juegos.</li>
    <li>No instalar o almacenar software, salvo aquellos programas utilitarios libres de licencia o en evaluación, o de uso corporativo que requiera para su trabajo. En todo caso, deberán respetarse los acuerdos de licencia del software y <strong>EL USUARIO</strong> será el único responsable por todo aquél que no le haya sido instalado por personal de la empresa CAISSA.</li>
    <li>No almacenar ni distribuir pornografía, contenidos ofensivos o violatorios de las leyes vigentes.</li>
    <li>Se permite mantener archivos informáticos personales, siempre y cuando se separen de los corporativos (carpetas lógicas diferentes), no se deteriore con ellos el desempeño del equipo y no se incumplan esta ni otras reglamentaciones que sobre el uso de tecnología expida la empresa. Sin embargo, le corresponde a <strong>EL USUARIO</strong> la responsabilidad sobre la información almacenada en el equipo asignado y a la empresa la libre disposición sobre la misma.</li>
  </ul>
  <p class="reglamento">La empresa <strong>CAISSA</strong>., en cualquier momento solicitará la entrega de el/los equipos para verificar el cumplimiento de este reglamento mediante una auditoría, solicitud que debe ser atendida por <strong>EL USUARIO</strong>.</p>

</div>

<!-- ══════════════════════════════════════════
     PÁGINA 2 — Datos y Firmas
══════════════════════════════════════════ -->
<div class="page">

  <div class="header">
    <img src="https://tech-ti.caissa-mx.com/public/img/logo.jpg" alt="CAISSA">
    <div class="company">Capacitación, Automatización Industrial,<br>Servicios y Soluciones Avanzadas. S.A. DE C.V.</div>
  </div>

  <!-- Observaciones -->
  <div style="margin-bottom:12px">
    <div class="obs-label">OBSERVACIONES:</div>
    <div class="obs-line"></div>
    <div class="obs-line"></div>
    <div class="obs-line"></div>
  </div>

  <!-- Tabla Colaborador -->
  <table class="data-table">
    <tr><td colspan="4" class="th-header">Datos del Colaborador</td></tr>
    <tr>
      <td class="lbl" style="width:20%">Nombre</td>
      <td class="val" style="width:30%"><?= $nombre ?></td>
      <td class="lbl" style="width:20%">Cargo</td>
      <td class="val" style="width:30%"><?= $cargo ?></td>
    </tr>
    <tr>
      <td class="lbl">Correo</td>
      <td class="val"><?= $correo ?></td>
      <td class="lbl">Proceso</td>
      <td class="val"><?= $proceso ?></td>
    </tr>
  </table>

  <!-- Tabla Equipo -->
  <table class="data-table">
    <tr><td colspan="4" class="th-header">Datos del Equipo</td></tr>
    <tr>
      <td class="lbl" style="width:25%">Tipo</td>
      <td class="lbl" style="width:25%">Marca</td>
      <td class="lbl" style="width:25%">Modelo</td>
      <td class="lbl" style="width:25%">Serial</td>
    </tr>
    <tr>
      <td class="val"><?= $tipo ?></td>
      <td class="val"><?= $marca ?></td>
      <td class="val"><?= $modelo ?></td>
      <td class="val"><?= $serial ?></td>
    </tr>
    <tr>
      <td class="lbl">Procesador</td>
      <td class="lbl">Memoria RAM (GB)</td>
      <td class="lbl">Capacidad de Disco (GB)</td>
      <td class="lbl">Cargador</td>
    </tr>
    <tr>
      <td class="val"><?= $procesador ?></td>
      <td class="val"><?= $ram ?></td>
      <td class="val"><?= $disco ?></td>
      <td class="val"><?= $cargador ?></td>
    </tr>
  </table>

  <!-- Tabla Certificación -->
  <table class="cert-table">
    <tr><td class="th-header">Observaciones</td></tr>
    <tr>
      <td class="cert-body">
        Certifico que los elementos detallados en el presente documento, me han sido entregados para mi cuidado y custodia con el propósito de cumplir con las tareas y asignaciones propias de mi cargo en la agencia, siendo estos de mi única y exclusiva responsabilidad. Me comprometo a usar correctamente los recursos, y solo para los fines establecidos, a no instalar ni permitir la instalación de software por personal ajeno al grupo interno de trabajo de soporte de TI.
      </td>
    </tr>
  </table>

  <!-- Firmas -->
  <div class="firma-row">
    <div class="firma-box"><div class="firma-line">Entrego: Nombre y Firma</div></div>
    <div class="firma-box"><div class="firma-line">Recibo: Nombre y Firma</div></div>
  </div>

</div>

<script>window.addEventListener('load', () => setTimeout(() => window.print(), 400));</script>
</body>
</html>
