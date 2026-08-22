<?php
$pageTitle = 'Editar Equipo';
require __DIR__ . '/../layouts/header.php';
$esCPU       = ($equipo['categoria_id'] == 1);
$catActual   = $equipo['categoria_nombre'] ?? '';
$esImpresora = stripos($catActual, 'impresora') !== false;
$esStarlink  = stripos($catActual, 'starlink') !== false;
$catNombres  = [];
foreach ($categorias as $c) { $catNombres[$c['id']] = $c['nombre']; }
?>
<div class="row justify-content-center"><div class="col-lg-8">
<?php if (!empty($_GET['error'])): ?>
<div class="alert alert-warning d-flex gap-2 mb-3"><i class="bi bi-exclamation-triangle-fill"></i><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger d-flex gap-2 mb-3"><i class="bi bi-exclamation-triangle-fill"></i><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<div class="card">
  <div class="card-header"><i class="bi bi-pencil me-2 text-primary"></i>Editar: <?= htmlspecialchars($equipo['codigo']) ?></div>
  <div class="card-body p-4">
  <form method="POST" action="index.php?c=inventario&a=edit&id=<?= $equipo['id'] ?>">

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label">Categoría</label>
        <select name="categoria_id" class="form-select" id="selCat">
          <?php foreach ($categorias as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $equipo['categoria_id'] == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Código</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($equipo['codigo']) ?>" disabled>
        <small class="text-muted">El código no se puede cambiar.</small>
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6"><label class="form-label">Marca</label><input type="text" name="marca" class="form-control" value="<?= htmlspecialchars($equipo['marca'] ?? '') ?>"></div>
      <div class="col-md-6"><label class="form-label">Modelo</label><input type="text" name="modelo" class="form-control" value="<?= htmlspecialchars($equipo['modelo'] ?? '') ?>"></div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6"><label class="form-label">N° de Serie</label><input type="text" name="numero_serie" class="form-control" value="<?= htmlspecialchars($equipo['numero_serie'] ?? '') ?>"></div>
      <div class="col-md-6"><label class="form-label">Ubicación</label><input type="text" name="ubicacion" class="form-control" value="<?= htmlspecialchars($equipo['ubicacion'] ?? '') ?>"></div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label">Estado</label>
        <select name="estado" class="form-select">
          <?php foreach ($estados as $k => $e): ?>
          <option value="<?= $k ?>" <?= $equipo['estado'] === $k ? 'selected' : '' ?>><?= $e['label'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6"><label class="form-label">Fecha de compra</label><input type="date" name="fecha_compra" class="form-control" value="<?= htmlspecialchars($equipo['fecha_compra'] ?? '') ?>"></div>
    </div>

    <div class="mb-3"><label class="form-label">Notas</label><textarea name="notas" class="form-control" rows="2"><?= htmlspecialchars($equipo['notas'] ?? '') ?></textarea></div>

    <!-- Red y Credenciales — solo CPU -->
    <div id="seccionRed" style="display:<?= $esCPU ? 'block' : 'none' ?>">
      <hr>
      <h6 style="font-family:'Syne',sans-serif;font-weight:700;margin-bottom:1rem"><i class="bi bi-hdd-network me-2 text-info"></i>Red y Credenciales</h6>
      <div class="row g-3 mb-3">
        <div class="col-md-6"><label class="form-label">Dirección MAC</label><input type="text" name="direccion_mac" class="form-control" placeholder="AA:BB:CC:DD:EE:FF" value="<?= htmlspecialchars($equipo['direccion_mac'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Dirección IP</label><input type="text" name="direccion_ip" class="form-control" placeholder="192.168.1.10" value="<?= htmlspecialchars($equipo['direccion_ip'] ?? '') ?>"></div>
      </div>
      <div class="row g-3 mb-3">
        <div class="col-md-6"><label class="form-label">Usuario del equipo</label><input type="text" name="usuario_pc" class="form-control" value="<?= htmlspecialchars($equipo['usuario_pc'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Contraseña</label>
          <div class="input-group">
            <input type="password" name="contrasena_pc" id="inpPass" class="form-control" value="<?= htmlspecialchars($equipo['contrasena_pc'] ?? '') ?>">
            <button type="button" class="btn btn-outline-secondary" id="btnVerPass"><i class="bi bi-eye" id="icoPass"></i></button>
          </div>
        </div>
      </div>
    </div>

    <!-- Sección Impresora -->
    <div id="seccionImpresora" style="display:<?= $esImpresora ? 'block' : 'none' ?>">
      <hr>
      <h6 style="font-family:'Syne',sans-serif;font-weight:700;margin-bottom:1rem"><i class="bi bi-printer me-2 text-primary"></i>Características</h6>
      <div class="row g-3 mb-3">
        <div class="col-md-4"><label class="form-label">Tipo</label><select name="_imp_tipo" id="impTipo" class="form-select"><option value="">— Selecciona —</option><?php foreach(['Láser','Inyección de tinta','Matricial','Térmica'] as $op):?><option <?=($specsActuales['tipo_impresora']??'')===$op?'selected':''?>><?=$op?></option><?php endforeach;?></select></div>
        <div class="col-md-4"><label class="form-label">Color</label><select name="_imp_color" id="impColor" class="form-select"><option value="">— Selecciona —</option><?php foreach(['Sí','No'] as $op):?><option <?=($specsActuales['color']??'')===$op?'selected':''?>><?=$op?></option><?php endforeach;?></select></div>
        <div class="col-md-4"><label class="form-label">Resolución (DPI)</label><select name="_imp_dpi" id="impDpi" class="form-select"><option value="">— Selecciona —</option><?php foreach(['600 DPI','1200 DPI','2400 DPI','4800 DPI'] as $op):?><option <?=($specsActuales['resolucion_dpi']??'')===$op?'selected':''?>><?=$op?></option><?php endforeach;?></select></div>
      </div>
      <div class="row g-3 mb-3">
        <div class="col-md-4"><label class="form-label">Velocidad (PPM)</label><input type="number" name="_imp_ppm" id="impPpm" class="form-control" value="<?= htmlspecialchars($specsActuales['velocidad_ppm'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Bandeja (hojas)</label><input type="number" name="_imp_bandeja" id="impBandeja" class="form-control" value="<?= htmlspecialchars($specsActuales['bandeja_hojas'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Contador páginas</label><input type="number" name="_imp_contador" id="impContador" class="form-control" value="<?= htmlspecialchars($specsActuales['contador_paginas'] ?? '') ?>"></div>
      </div>
      <h6 style="font-family:'Syne',sans-serif;font-weight:700;margin:1rem 0"><i class="bi bi-wifi me-2 text-info"></i>Conectividad</h6>
      <div class="row g-3 mb-3">
        <div class="col-md-4"><label class="form-label">Conexión</label><select name="_imp_conex" id="impConex" class="form-select"><option value="">— Selecciona —</option><?php foreach(['USB','WiFi','Red (Ethernet)','Bluetooth','USB y WiFi'] as $op):?><option <?=($specsActuales['conectividad']??'')===$op?'selected':''?>><?=$op?></option><?php endforeach;?></select></div>
        <div class="col-md-4"><label class="form-label">Dirección IP</label><input type="text" name="_imp_ip" id="impIp" class="form-control" value="<?= htmlspecialchars($specsActuales['direccion_ip'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Dirección MAC</label><input type="text" name="_imp_mac" id="impMac" class="form-control" value="<?= htmlspecialchars($specsActuales['direccion_mac'] ?? '') ?>"></div>
      </div>
      <h6 style="font-family:'Syne',sans-serif;font-weight:700;margin:1rem 0"><i class="bi bi-droplet me-2 text-warning"></i>Consumibles</h6>
      <div class="row g-3 mb-3">
        <div class="col-md-12"><label class="form-label">Tóner / Cartucho</label><input type="text" name="_imp_toner" id="impToner" class="form-control" value="<?= htmlspecialchars($specsActuales['toner_cartucho'] ?? '') ?>"></div>
      </div>
      <!-- Hidden inputs para specs de impresora -->
      <div id="hiddenSpecs">
        <?php foreach ($campos as $campo):
          $impMap = ['tipo_impresora'=>'impTipo','color'=>'impColor','resolucion_dpi'=>'impDpi','velocidad_ppm'=>'impPpm','bandeja_hojas'=>'impBandeja','contador_paginas'=>'impContador','conectividad'=>'impConex','direccion_ip'=>'impIp','direccion_mac'=>'impMac','toner_cartucho'=>'impToner'];
          $val = $specsActuales[$campo['nombre_campo']] ?? '';
        ?>
        <input type="hidden" name="spec[<?= $campo['id'] ?>]" id="hid_<?= $campo['id'] ?>" data-campo="<?= htmlspecialchars($campo['nombre_campo']) ?>" value="<?= htmlspecialchars($val) ?>">
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Especificaciones — CPU y demás categorías (NO impresora / NO Starlink) -->
    <?php if (!$esImpresora && !$esStarlink && !empty($campos)): ?>
    <hr>
    <h6 style="font-family:'Syne',sans-serif;font-weight:700;margin-bottom:1rem">Especificaciones</h6>
    <div class="row g-3 mb-3" id="camposDinamicos">
      <?php foreach ($campos as $campo):
        $val = $specsActuales[$campo['nombre_campo']] ?? ''; ?>
      <div class="col-md-6">
        <label class="form-label"><?= htmlspecialchars($campo['etiqueta']) ?></label>
        <?php if ($campo['tipo'] === 'select' && $campo['opciones']): $opts = explode('|', $campo['opciones']); ?>
        <select name="spec[<?= $campo['id'] ?>]" class="form-select">
          <option value="">— Selecciona —</option>
          <?php foreach ($opts as $o): ?><option value="<?= htmlspecialchars($o) ?>" <?= $val === $o ? 'selected' : '' ?>><?= htmlspecialchars($o) ?></option><?php endforeach; ?>
        </select>
        <?php elseif ($campo['tipo'] === 'numero'): ?>
        <input type="number" name="spec[<?= $campo['id'] ?>]" class="form-control" value="<?= htmlspecialchars($val) ?>">
        <?php else: ?>
        <input type="text" name="spec[<?= $campo['id'] ?>]" class="form-control" value="<?= htmlspecialchars($val) ?>">
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div id="camposDinamicos"></div>
    <?php endif; ?>

    <hr>
    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i>Guardar Cambios</button>
      <a href="index.php?c=inventario&a=detail&id=<?= $equipo['id'] ?>" class="btn btn-outline-secondary px-4">Cancelar</a>
      <a href="index.php?c=inventario&a=delete&id=<?= $equipo['id'] ?>" class="btn btn-outline-danger ms-auto" onclick="return confirm('¿Eliminar este equipo?')"><i class="bi bi-trash me-1"></i>Eliminar</a>
    </div>
  </form>
  </div>
</div>
</div></div>

<script>
const CAT_COMPUTADORA = 1;
const CAT_NOMBRES = <?= json_encode($catNombres) ?>;
const SPEC_VALUES = <?= json_encode($specsActuales, JSON_UNESCAPED_UNICODE) ?>;
const IMP_MAP = {'tipo_impresora':'impTipo','color':'impColor','resolucion_dpi':'impDpi','velocidad_ppm':'impPpm','bandeja_hojas':'impBandeja','contador_paginas':'impContador','conectividad':'impConex','direccion_ip':'impIp','direccion_mac':'impMac','toner_cartucho':'impToner'};

function esStarlink(nombre) {
    return (nombre || '').toLowerCase().includes('starlink');
}

function escapeHtml(v) {
    return String(v ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
}

function campoHtml(c, col = 'col-md-6') {
    const val = SPEC_VALUES[c.nombre_campo] || '';
    let h = '<div class="'+col+'"><label class="form-label">'+c.etiqueta+'</label>';
    if (c.tipo === 'select' && c.opciones) {
        const opts = c.opciones.split('|');
        h += '<select name="spec['+c.id+']" class="form-select"><option value="">— Selecciona —</option>';
        opts.forEach(o => h += '<option value="'+escapeHtml(o)+'" '+(val === o ? 'selected' : '')+'>'+escapeHtml(o)+'</option>');
        h += '</select>';
    } else if (c.tipo === 'numero') {
        h += '<input type="number" name="spec['+c.id+']" class="form-control" value="'+escapeHtml(val)+'">';
    } else {
        h += '<input type="text" name="spec['+c.id+']" class="form-control" value="'+escapeHtml(val)+'">';
    }
    return h + '</div>';
}

function buscarCampo(campos, nombre) {
    return campos.find(c => c.nombre_campo === nombre);
}

function renderGrupo(campos, nombres, cols = 'col-md-6') {
    return nombres.map(n => buscarCampo(campos, n)).filter(Boolean).map(c => campoHtml(c, cols)).join('');
}

function renderStarlink(campos) {
    const specs = renderGrupo(campos, [
        'tipo_kit',
        'modelo_power_supply',
        'serie_power_supply',
        'modelo_router',
        'serie_router',
        'mac_router'
    ]);
    const servicio = renderGrupo(campos, [
        'plan_servicio',
        'tipo_servicio',
        'id_servicio',
        'estado_servicio'
    ]);
    const instalacion = renderGrupo(campos, ['ubicacion_instalacion'], 'col-md-12');

    document.getElementById('camposDinamicos').innerHTML =
        '<hr><h6 style="font-family:\'Syne\',sans-serif;font-weight:700;margin-bottom:1rem"><i class="bi bi-router me-2 text-primary"></i>Especificaciones Starlink</h6>' +
        '<div class="row g-3 mb-3">' + specs + '</div>' +
        '<hr><h6 style="font-family:\'Syne\',sans-serif;font-weight:700;margin-bottom:1rem"><i class="bi bi-broadcast me-2 text-success"></i>Servicio Starlink</h6>' +
        '<div class="row g-3 mb-3">' + servicio + '</div>' +
        '<hr><h6 style="font-family:\'Syne\',sans-serif;font-weight:700;margin-bottom:1rem"><i class="bi bi-geo-alt me-2 text-warning"></i>Instalación</h6>' +
        '<div class="row g-3 mb-3">' + instalacion + '</div>';
}

function renderGenerico(campos) {
    let h = '<hr><h6 style="font-family:\'Syne\',sans-serif;font-weight:700;margin-bottom:1rem">Especificaciones</h6><div class="row g-3">';
    campos.forEach(c => { h += campoHtml(c); });
    h += '</div>';
    document.getElementById('camposDinamicos').innerHTML = h;
}

document.getElementById('btnVerPass')?.addEventListener('click', function () {
    const inp = document.getElementById('inpPass'), ico = document.getElementById('icoPass');
    inp.type = inp.type === 'password' ? 'text' : 'password';
    ico.className = inp.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
});

function sincronizarHidden() {
    document.querySelectorAll('#hiddenSpecs input[data-campo]').forEach(hid => {
        const el = IMP_MAP[hid.dataset.campo] ? document.getElementById(IMP_MAP[hid.dataset.campo]) : null;
        if (el) hid.value = el.value;
    });
}
Object.values(IMP_MAP).forEach(elId => { document.getElementById(elId)?.addEventListener('input', sincronizarHidden); });
sincronizarHidden();

document.getElementById('selCat')?.addEventListener('change', function () {
    const cid    = parseInt(this.value);
    const nombre = CAT_NOMBRES[cid] || '';
    const esCPU  = cid === CAT_COMPUTADORA;
    const esImp  = nombre.toLowerCase().includes('impresora');
    const esStar = esStarlink(nombre);

    document.getElementById('seccionRed').style.display       = esCPU ? 'block' : 'none';
    document.getElementById('seccionImpresora').style.display  = esImp ? 'block' : 'none';
    document.getElementById('camposDinamicos').innerHTML       = '';
    document.getElementById('hiddenSpecs').innerHTML           = '';

    fetch('index.php?c=inventario&a=apiCategoria&cat_id=' + this.value)
        .then(r => r.json())
        .then(data => {
            if (esImp) {
                let h = '';
                data.campos.forEach(c => { h += '<input type="hidden" name="spec['+c.id+']" id="hid_'+c.id+'" data-campo="'+c.nombre_campo+'" value="">'; });
                document.getElementById('hiddenSpecs').innerHTML = h;
                Object.values(IMP_MAP).forEach(elId => { document.getElementById(elId)?.addEventListener('input', sincronizarHidden); });
            } else if (esStar) {
                renderStarlink(data.campos || []);
            } else if (data.campos?.length) {
                renderGenerico(data.campos);
            }
        });
});

const selCatInicial = document.getElementById('selCat');
const catInicialNombre = CAT_NOMBRES[parseInt(selCatInicial?.value || '0')] || '';
if (selCatInicial?.value && esStarlink(catInicialNombre)) {
    fetch('index.php?c=inventario&a=apiCategoria&cat_id=' + encodeURIComponent(selCatInicial.value))
        .then(r => r.json())
        .then(data => renderStarlink(data.campos || []));
}
</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
