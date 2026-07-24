<?php
// views/mantenimiento/publico.php
// Página de acceso público — no requiere login
// URL: index.php?c=mantenimiento&a=publico&token=XXXXXXXX

$estados = [
    'pendiente'  => ['label' => 'Pendiente',  'color' => '#d97700', 'bg' => '#fff3cd'],
    'en_proceso' => ['label' => 'En Proceso', 'color' => '#0288d1', 'bg' => '#e3f2fd'],
    'completado' => ['label' => 'Completado', 'color' => '#198754', 'bg' => '#d4edda'],
    'cancelado'  => ['label' => 'Cancelado',  'color' => '#c62828', 'bg' => '#fce4ec'],
];
$tipos = [
    'preventivo' => ['label' => 'Mantenimiento Preventivo', 'color' => '#0077ff'],
    'correctivo' => ['label' => 'Mantenimiento Correctivo', 'color' => '#c62828'],
];

$est = $estados[$mant['estado']] ?? ['label' => $mant['estado'], 'color' => '#555', 'bg' => '#eee'];
$tip = $tipos[$mant['tipo']]     ?? ['label' => $mant['tipo'],   'color' => '#555'];

$logoUrl = $_SERVER['DOCUMENT_ROOT'] . '/helpdesk/public/img/logo.jpg';
$logoB64 = file_exists($logoUrl)
    ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoUrl))
    : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Orden de Mantenimiento — <?= htmlspecialchars($mant['folio']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background: #f0f4f9; font-family: 'Segoe UI', Arial, sans-serif; }
.card { border-radius: 14px; border: 1px solid #dde4ef; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
.badge-estado {
    display: inline-block; padding: 5px 14px; border-radius: 20px; font-size: .82rem; font-weight: 700;
    background: <?= $est['bg'] ?>; color: <?= $est['color'] ?>;
}
.dato-row { display: flex; justify-content: space-between; padding: 8px 0;
            border-bottom: 1px solid #f0f4f9; font-size: .88rem; }
.dato-row:last-child { border-bottom: none; }
.dato-label { color: #6b7c93; font-weight: 600; }
.spec-item { background: #f7faff; border: 1px solid #dde4ef; border-radius: 8px; padding: 8px 12px; }
.spec-label { font-size: .7rem; color: #9aafca; text-transform: uppercase; font-weight: 700; }
.spec-val { font-size: .9rem; font-weight: 600; color: #0d1b2a; }
@media print { .no-print { display: none !important; } body { background: #fff; } }
</style>
</head>
<body class="py-4">

<div class="container" style="max-width:720px">

    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                <div class="d-flex align-items-center gap-3">
                    <?php if ($logoB64): ?>
                    <img src="<?= $logoB64 ?>" alt="CAISSA" style="max-height:48px;max-width:120px;object-fit:contain">
                    <?php else: ?>
                    <div style="font-size:1.3rem;font-weight:900;letter-spacing:1px">CAISSA</div>
                    <?php endif; ?>
                </div>
                <span class="badge-estado"><?= $est['label'] ?></span>
            </div>
            <div style="font-size:1.1rem;font-weight:800;color:#0d1b2a;margin-bottom:4px">
                <?= $tip['label'] ?>
            </div>
            <div style="font-family:monospace;font-size:.9rem;font-weight:700;color:<?= $tip['color'] ?>">
                <?= htmlspecialchars($mant['folio']) ?>
            </div>
        </div>
    </div>

    <!-- Equipo -->
    <div class="card mb-4">
        <div class="card-body p-4">
            <h6 class="mb-3" style="font-weight:700;color:#0d1b2a">
                <i class="bi bi-box-seam me-2 text-primary"></i>Equipo
            </h6>
            <div class="dato-row"><span class="dato-label">Código</span><span><?= htmlspecialchars($mant['equipo_codigo']) ?></span></div>
            <div class="dato-row"><span class="dato-label">Categoría</span><span><?= htmlspecialchars($mant['categoria_nombre']) ?></span></div>
            <?php if ($mant['equipo_marca']): ?>
            <div class="dato-row"><span class="dato-label">Marca</span><span><?= htmlspecialchars($mant['equipo_marca']) ?></span></div>
            <?php endif; ?>
            <?php if ($mant['equipo_modelo']): ?>
            <div class="dato-row"><span class="dato-label">Modelo</span><span><?= htmlspecialchars($mant['equipo_modelo']) ?></span></div>
            <?php endif; ?>
            <?php if ($mant['equipo_serie']): ?>
            <div class="dato-row"><span class="dato-label">N° Serie</span><span><?= htmlspecialchars($mant['equipo_serie']) ?></span></div>
            <?php endif; ?>
            <?php if ($mant['equipo_ubicacion']): ?>
            <div class="dato-row"><span class="dato-label">Ubicación</span><span><?= htmlspecialchars($mant['equipo_ubicacion']) ?></span></div>
            <?php endif; ?>

            <!-- Especificaciones técnicas (sin datos sensibles) -->
            <?php
            $camposOmitir = ['direccion_mac','direccion_ip','usuario_pc','contrasena_pc'];
            $specsVisibles = array_filter($specs, fn($s) => $s['valor'] && !in_array($s['nombre_campo'], $camposOmitir));
            ?>
            <?php if (!empty($specsVisibles)): ?>
            <div class="mt-3">
                <div style="font-size:.75rem;font-weight:700;color:#9aafca;text-transform:uppercase;margin-bottom:.6rem">
                    Especificaciones
                </div>
                <div class="row g-2">
                    <?php foreach ($specsVisibles as $s): ?>
                    <div class="col-sm-6">
                        <div class="spec-item">
                            <div class="spec-label"><?= htmlspecialchars($s['etiqueta']) ?></div>
                            <div class="spec-val"><?= htmlspecialchars($s['valor']) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Detalles del mantenimiento -->
    <div class="card mb-4">
        <div class="card-body p-4">
            <h6 class="mb-3" style="font-weight:700;color:#0d1b2a">
                <i class="bi bi-tools me-2 text-warning"></i>Detalles del Mantenimiento
            </h6>
            <div class="dato-row"><span class="dato-label">Tipo</span><span style="color:<?= $tip['color'] ?>;font-weight:700"><?= $tip['label'] ?></span></div>
            <div class="dato-row"><span class="dato-label">Fecha programada</span><span><?= date('d/m/Y', strtotime($mant['fecha_programada'])) ?></span></div>
            <?php if ($mant['fecha_realizada']): ?>
            <div class="dato-row"><span class="dato-label">Fecha realizada</span><span><?= date('d/m/Y', strtotime($mant['fecha_realizada'])) ?></span></div>
            <?php endif; ?>

            <?php if ($mant['descripcion']): ?>
            <div class="mt-3 p-3 rounded-3" style="background:#f7faff;border:1px solid #dde4ef">
                <div style="font-size:.75rem;font-weight:700;color:#9aafca;text-transform:uppercase;margin-bottom:.4rem">
                    Actividades a realizar
                </div>
                <div style="font-size:.9rem;line-height:1.7;white-space:pre-wrap"><?= htmlspecialchars($mant['descripcion']) ?></div>
            </div>
            <?php endif; ?>

            <?php if ($mant['notas']): ?>
            <div class="mt-3 p-3 rounded-3" style="background:#d4edda;border:1px solid #b8dfc4">
                <div style="font-size:.75rem;font-weight:700;color:#155724;text-transform:uppercase;margin-bottom:.4rem">
                    Notas de cierre
                </div>
                <div style="font-size:.9rem;line-height:1.7;white-space:pre-wrap"><?= htmlspecialchars($mant['notas']) ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center mb-4 no-print">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-printer me-1"></i>Imprimir
        </button>
    </div>
    <div class="text-center" style="font-size:.78rem;color:#9aafca">
        CAISSA · Documento generado el <?= date('d/m/Y H:i') ?>
    </div>
</div>
</body>
</html>
