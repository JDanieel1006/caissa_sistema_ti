<?php
$pageTitle = 'Dashboard';
require __DIR__ . '/../layouts/header.php';

$total    = $stats['total'] ?? 0;
$abiertos = $stats['por_estado']['abierto']    ?? 0;
$proceso  = $stats['por_estado']['en_proceso']  ?? 0;
$resueltos= $stats['por_estado']['resuelto']   ?? 0;

$diasLabels = json_encode(array_map(fn($d) => date('d/M', strtotime($d)), array_keys($ticketsPorDia)));
$diasData   = json_encode(array_values($ticketsPorDia));
$estLabels  = json_encode(array_map(fn($k) => Ticket::ESTADOS[$k]['label'] ?? $k, array_keys($stats['por_estado'] ?? [])));
$estData    = json_encode(array_values($stats['por_estado'] ?? []));
$catLabels  = json_encode(array_map(fn($r) => Ticket::CATEGORIAS[$r['categoria']]['label'] ?? $r['categoria'], $stats['por_categoria'] ?? []));
$catData    = json_encode(array_column($stats['por_categoria'] ?? [], 'total'));
$invData    = json_encode([$statsEquipos['buenos'], $statsEquipos['danados'] ?? $statsEquipos['dañados'] ?? 0, $statsEquipos['reparacion'], $statsEquipos['baja']]);
?>

<!-- KPIs -->
<div class="row g-3 mb-4">
<?php foreach([
    ['Tickets Total',  'bi-ticket-detailed',    '#e8f0fe','#0077ff', $total],
    ['Abiertos',       'bi-circle',             '#fff3cd','#d97700', $abiertos],
    ['En Proceso',     'bi-arrow-repeat',        '#e3f2fd','#0288d1', $proceso],
    ['Equipos',        'bi-box-seam',            '#f3e5f5','#7b1fa2', $statsEquipos['total']],
    ['Resueltos',      'bi-check-circle',        '#d4edda','#198754', $resueltos],
    ['Asig. Activas',  'bi-file-earmark-check',  '#e8f5e9','#2e7d32', $statsAsig['activas']],
    ['Asig. Vencidas', 'bi-exclamation-circle',  '#fce4ec','#c62828', $statsAsig['vencidas']],
    ['Equipos Buenos', 'bi-shield-check',        '#e0f2f1','#00695c', $statsEquipos['buenos']],
] as [$lbl,$ico,$bg,$col,$val]): ?>
<div class="col-6 col-md-3">
  <div class="stat-card">
    <div class="stat-icon" style="background:<?= $bg ?>;color:<?= $col ?>"><i class="<?= $ico ?>"></i></div>
    <div><div class="stat-value"><?= $val ?></div><div class="stat-label"><?= $lbl ?></div></div>
  </div>
</div>
<?php endforeach; ?>
</div>

<!-- Gráficas fila 1 -->
<div class="row g-4 mb-4">
  <div class="col-lg-8">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-graph-up me-2 text-primary"></i>Tickets — últimos 14 días</div>
      <div class="card-body"><canvas id="cLine" height="90"></canvas></div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-pie-chart me-2 text-warning"></i>Por Estado</div>
      <div class="card-body d-flex align-items-center justify-content-center"><canvas id="cEst" height="160"></canvas></div>
    </div>
  </div>
</div>

<!-- Gráficas fila 2 -->
<div class="row g-4 mb-4">
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-bar-chart me-2 text-info"></i>Por Categoría</div>
      <div class="card-body"><canvas id="cCat" height="160"></canvas></div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-box-seam me-2 text-success"></i>Estado del Inventario</div>
      <div class="card-body d-flex align-items-center justify-content-center"><canvas id="cInv" height="140"></canvas></div>
    </div>
  </div>
</div>

<!-- Tablas -->
<div class="row g-4">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-clock-history me-2 text-primary"></i>Tickets Recientes</span>
        <a href="index.php?c=tickets" class="btn btn-sm btn-outline-primary">Ver todos</a>
      </div>
      <div class="table-responsive"><table class="table mb-0 js-data-table">
        <thead><tr><th>Folio</th><th>Título</th><th>Estado</th><th>Prioridad</th><th>Fecha</th></tr></thead>
        <tbody>
        <?php foreach ($ticketsRecientes as $t):
            $est = $estados[$t['estado']]     ?? ['label' => $t['estado'],    'color' => 'secondary'];
            $pri = $prioridades[$t['prioridad']] ?? ['label' => $t['prioridad'], 'color' => 'secondary'];
        ?>
        <tr style="cursor:pointer" onclick="location.href='index.php?c=tickets&a=detail&id=<?= $t['id'] ?>'">
          <td><span class="folio-badge"><?= htmlspecialchars($t['folio']) ?></span></td>
          <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-weight:500"><?= htmlspecialchars($t['titulo']) ?></td>
          <td><span class="badge bg-<?= $est['color'] ?>"><?= $est['label'] ?></span></td>
          <td><span class="badge bg-<?= $pri['color'] ?>"><?= $pri['label'] ?></span></td>
          <td style="font-size:.78rem;color:#6b7c93;white-space:nowrap"><?= date('d/m/y', strtotime($t['creado_en'])) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table></div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-file-earmark-check me-2 text-success"></i>Asignaciones Activas</span>
        <a href="index.php?c=asignaciones" class="btn btn-sm btn-outline-success">Ver todas</a>
      </div>
      <?php if (empty($asignacionesActivas)): ?>
      <div class="card-body text-center py-4 text-muted">
        <i class="bi bi-inbox" style="font-size:2rem"></i>
        <p class="mt-2 mb-0" style="font-size:.85rem">Sin asignaciones activas.</p>
      </div>
      <?php else: ?>
      <div class="table-responsive"><table class="table mb-0 js-data-table">
        <thead><tr><th>Folio</th><th>Equipo</th><th>Usuario</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($asignacionesActivas as $a):
            $vencida = $a['fecha_devolucion_esperada'] && strtotime($a['fecha_devolucion_esperada']) < time();
        ?>
        <tr>
          <td><span class="folio-badge" style="color:<?= $vencida ? '#c62828' : '#0044bb' ?>"><?= htmlspecialchars($a['folio']) ?></span></td>
          <td style="font-size:.83rem"><?= htmlspecialchars($a['equipo_codigo']) ?>
            <?php if ($a['equipo_marca']): ?><br><span style="color:#9aafca;font-size:.72rem"><?= htmlspecialchars($a['equipo_marca']) ?></span><?php endif; ?>
          </td>
          <td style="font-size:.83rem"><?= htmlspecialchars($a['nombre_usuario']) ?></td>
          <td><a href="index.php?c=asignaciones&a=acta&id=<?= $a['id'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-pdf"></i></a></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
const COLORS = ['#0077ff','#00c2ff','#ff6b35','#ffd500','#00d68f','#ff4757','#a855f7','#22d3ee'];

document.addEventListener('DOMContentLoaded', function() {
    // Línea — tickets por día
    new Chart(document.getElementById('cLine'), {
        type: 'line',
        data: {
            labels: <?= $diasLabels ?>,
            datasets: [{
                label: 'Tickets',
                data: <?= $diasData ?>,
                borderColor: '#0077ff',
                backgroundColor: 'rgba(0,119,255,.1)',
                fill: true, tension: .4, pointRadius: 3, pointBackgroundColor: '#0077ff'
            }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

    // Dona — estados
    new Chart(document.getElementById('cEst'), {
        type: 'doughnut',
        data: { labels: <?= $estLabels ?>, datasets: [{ data: <?= $estData ?>, backgroundColor: COLORS }] },
        options: { plugins: { legend: { position: 'bottom' } }, cutout: '65%' }
    });

    // Barras — categorías
    new Chart(document.getElementById('cCat'), {
        type: 'bar',
        data: { labels: <?= $catLabels ?>, datasets: [{ label: 'Tickets', data: <?= $catData ?>, backgroundColor: '#0077ff' }] },
        options: { indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

    // Dona — inventario
    new Chart(document.getElementById('cInv'), {
        type: 'doughnut',
        data: {
            labels: ['Bueno', 'Dañado', 'En Reparación', 'Baja'],
            datasets: [{ data: <?= $invData ?>, backgroundColor: ['#00d68f','#ff4757','#ffd500','#6b7c93'] }]
        },
        options: { plugins: { legend: { position: 'bottom' } }, cutout: '60%' }
    });
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
