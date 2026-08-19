<?php
$pageTitle = 'Nuevo Equipo';
require __DIR__ . '/../layouts/header.php';
$catSeleccionada = (int)($_POST['categoria_id'] ?? $_GET['cat'] ?? 0);
$catNombres = [];
foreach ($categorias as $c) { $catNombres[$c['id']] = $c['nombre']; }
?>
<div class="row justify-content-center"><div class="col-lg-8">
<?php if ($error): ?>
<div class="alert alert-danger d-flex gap-2 mb-3"><i class="bi bi-exclamation-triangle-fill"></i><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<div class="card">
  <div class="card-header"><i class="bi bi-plus-circle me-2 text-primary"></i>Registrar Equipo</div>
  <div class="card-body p-4">
  <form method="POST" action="index.php?c=inventario&a=create" id="fEquipo">

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label">Categoría *</label>
        <select name="categoria_id" class="form-select no-select2" id="selCat" required>
          <option value="">— Selecciona —</option>
          <?php foreach ($categorias as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $catSeleccionada == $c['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['nombre']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Código *</label>
        <input type="text" name="codigo" id="inpCodigo" class="form-control"
               value="<?= htmlspecialchars($_POST['codigo'] ?? '') ?>" required>
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label">Marca</label>
        <input type="text" name="marca" class="form-control" value="<?= htmlspecialchars($_POST['marca'] ?? '') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Modelo</label>
        <input type="text" name="modelo" class="form-control" value="<?= htmlspecialchars($_POST['modelo'] ?? '') ?>">
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label">N° de Serie</label>
        <input type="text" name="numero_serie" class="form-control" value="<?= htmlspecialchars($_POST['numero_serie'] ?? '') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Ubicación</label>
        <input type="text" name="ubicacion" class="form-control" value="<?= htmlspecialchars($_POST['ubicacion'] ?? '') ?>">
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label">Estado</label>
        <select name="estado" class="form-select">
          <?php foreach ($estados as $k => $e): ?>
          <option value="<?= $k ?>"><?= $e['label'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Fecha de compra</label>
        <input type="date" name="fecha_compra" class="form-control" value="<?= htmlspecialchars($_POST['fecha_compra'] ?? '') ?>">
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Notas</label>
      <textarea name="notas" class="form-control" rows="2"><?= htmlspecialchars($_POST['notas'] ?? '') ?></textarea>
    </div>

    <!-- Red y Credenciales — solo CPU -->
    <div id="seccionRed" style="display:<?= $catSeleccionada == 1 ? 'block' : 'none' ?>">
      <hr>
      <h6 style="font-family:'Syne',sans-serif;font-weight:700;margin-bottom:1rem">
        <i class="bi bi-hdd-network me-2 text-info"></i>Red y Credenciales
      </h6>
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label">Dirección MAC</label>
          <input type="text" name="direccion_mac" class="form-control" placeholder="AA:BB:CC:DD:EE:FF"
                 value="<?= htmlspecialchars($_POST['direccion_mac'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Dirección IP</label>
          <input type="text" name="direccion_ip" class="form-control" placeholder="192.168.1.10"
                 value="<?= htmlspecialchars($_POST['direccion_ip'] ?? '') ?>">
        </div>
      </div>
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label">Usuario del equipo</label>
          <input type="text" name="usuario_pc" class="form-control" placeholder="Ej. alumno01"
                 value="<?= htmlspecialchars($_POST['usuario_pc'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Contraseña</label>
          <div class="input-group">
            <input type="password" name="contrasena_pc" id="inpPass" class="form-control"
                   value="<?= htmlspecialchars($_POST['contrasena_pc'] ?? '') ?>">
            <button type="button" class="btn btn-outline-secondary" id="btnVerPass">
              <i class="bi bi-eye" id="icoPass"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Sección Impresora -->
    <div id="seccionImpresora" style="display:none">
      <hr>
      <h6 style="font-family:'Syne',sans-serif;font-weight:700;margin-bottom:1rem">
        <i class="bi bi-printer me-2 text-primary"></i>Características
      </h6>
      <div class="row g-3 mb-3">
        <div class="col-md-4"><label class="form-label">Tipo</label><select name="_imp_tipo" id="impTipo" class="form-select"><option value="">— Selecciona —</option><option>Láser</option><option>Inyección de tinta</option><option>Matricial</option><option>Térmica</option></select></div>
        <div class="col-md-4"><label class="form-label">Color</label><select name="_imp_color" id="impColor" class="form-select"><option value="">— Selecciona —</option><option>Sí</option><option>No</option></select></div>
        <div class="col-md-4"><label class="form-label">Resolución (DPI)</label><select name="_imp_dpi" id="impDpi" class="form-select"><option value="">— Selecciona —</option><option>600 DPI</option><option>1200 DPI</option><option>2400 DPI</option><option>4800 DPI</option></select></div>
      </div>
      <div class="row g-3 mb-3">
        <div class="col-md-4"><label class="form-label">Velocidad (PPM)</label><input type="number" name="_imp_ppm" id="impPpm" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Bandeja (hojas)</label><input type="number" name="_imp_bandeja" id="impBandeja" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Contador páginas</label><input type="number" name="_imp_contador" id="impContador" class="form-control"></div>
      </div>
      <h6 style="font-family:'Syne',sans-serif;font-weight:700;margin:1rem 0"><i class="bi bi-wifi me-2 text-info"></i>Conectividad</h6>
      <div class="row g-3 mb-3">
        <div class="col-md-4"><label class="form-label">Conexión</label><select name="_imp_conex" id="impConex" class="form-select"><option value="">— Selecciona —</option><option>USB</option><option>WiFi</option><option>Red (Ethernet)</option><option>Bluetooth</option><option>USB y WiFi</option></select></div>
        <div class="col-md-4"><label class="form-label">Dirección IP</label><input type="text" name="_imp_ip" id="impIp" class="form-control" placeholder="192.168.1.20"></div>
        <div class="col-md-4"><label class="form-label">Dirección MAC</label><input type="text" name="_imp_mac" id="impMac" class="form-control" placeholder="AA:BB:CC:DD:EE:FF"></div>
      </div>
      <h6 style="font-family:'Syne',sans-serif;font-weight:700;margin:1rem 0"><i class="bi bi-droplet me-2 text-warning"></i>Consumibles</h6>
      <div class="row g-3 mb-3">
        <div class="col-md-12"><label class="form-label">Tóner / Cartucho</label><input type="text" name="_imp_toner" id="impToner" class="form-control" placeholder="Ej. HP 664, Canon PG-745..."></div>
      </div>
      <div id="hiddenSpecs"></div>
    </div>

    <!-- Especificaciones dinámicas (todas las categorías incluido CPU) -->
    <div id="camposDinamicos">
      <?php if (!empty($campos) && !stripos($catNombres[$catSeleccionada] ?? '', 'impresora')): ?>
      <hr>
      <h6 style="font-family:'Syne',sans-serif;font-weight:700;margin-bottom:1rem">Especificaciones</h6>
      <div class="row g-3">
        <?php foreach ($campos as $campo): ?>
        <div class="col-md-6">
          <label class="form-label"><?= htmlspecialchars($campo['etiqueta']) ?></label>
          <?php if ($campo['tipo'] === 'select' && $campo['opciones']): $opts = explode('|', $campo['opciones']); ?>
          <select name="spec[<?= $campo['id'] ?>]" class="form-select">
            <option value="">— Selecciona —</option>
            <?php foreach ($opts as $o): ?><option value="<?= htmlspecialchars($o) ?>"><?= htmlspecialchars($o) ?></option><?php endforeach; ?>
          </select>
          <?php elseif ($campo['tipo'] === 'numero'): ?>
          <input type="number" name="spec[<?= $campo['id'] ?>]" class="form-control">
          <?php else: ?>
          <input type="text" name="spec[<?= $campo['id'] ?>]" class="form-control">
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <hr>
    <div class="d-flex gap-2">
      <button type="submit" name="guardar" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i>Guardar Equipo</button>
      <a href="index.php?c=inventario" class="btn btn-outline-secondary px-4">Cancelar</a>
    </div>
  </form>
  </div>
</div>
</div></div>

<script>
const CAT_COMPUTADORA = 1;
const CAT_NOMBRES = <?= json_encode($catNombres) ?>;
const IMP_MAP = {'tipo_impresora':'impTipo','color':'impColor','resolucion_dpi':'impDpi','velocidad_ppm':'impPpm','bandeja_hojas':'impBandeja','contador_paginas':'impContador','conectividad':'impConex','direccion_ip':'impIp','direccion_mac':'impMac','toner_cartucho':'impToner'};

document.getElementById('btnVerPass')?.addEventListener('click', function () {
    const inp = document.getElementById('inpPass'), ico = document.getElementById('icoPass');
    inp.type = inp.type === 'password' ? 'text' : 'password';
    ico.className = inp.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
});

function sincronizarHiddenImpresora() {
    const campos = window._impCampos || [];
    let html = '';
    campos.forEach(c => {
        const el = IMP_MAP[c.nombre_campo] ? document.getElementById(IMP_MAP[c.nombre_campo]) : null;
        html += '<input type="hidden" name="spec['+c.id+']" value="'+(el?el.value:'').replace(/"/g,'&quot;')+'">';
    });
    document.getElementById('hiddenSpecs').innerHTML = html;
}

function cargarDatosCategoria() {
    const selCat = document.getElementById('selCat');
    if (!selCat) return;
    const codigoActual = document.getElementById('inpCodigo')?.value || '';
    const debeGenerarCodigo = codigoActual.trim() === '';
    const cid    = parseInt(selCat.value);
    const value  = selCat.value;
    const nombre = CAT_NOMBRES[cid] || '';
    const esCPU  = cid === CAT_COMPUTADORA;
    const esImp  = nombre.toLowerCase().includes('impresora');

    document.getElementById('seccionRed').style.display       = esCPU ? 'block' : 'none';
    document.getElementById('seccionImpresora').style.display  = esImp ? 'block' : 'none';
    document.getElementById('camposDinamicos').innerHTML       = '';
    document.getElementById('hiddenSpecs').innerHTML           = '';

    if (!esCPU) {
        ['direccion_mac','direccion_ip','usuario_pc','contrasena_pc'].forEach(n => {
            const el = document.querySelector('[name="'+n+'"]'); if(el) el.value='';
        });
    }
    if (!value) return;

    fetch('index.php?c=inventario&a=apiCategoria&cat_id=' + encodeURIComponent(value))
        .then(r => r.json())
        .then(data => {
            if (debeGenerarCodigo || data.codigo) {
                document.getElementById('inpCodigo').value = data.codigo || '';
            }
            if (esImp) {
                window._impCampos = data.campos;
                sincronizarHiddenImpresora();
                Object.values(IMP_MAP).forEach(elId => {
                    document.getElementById(elId)?.addEventListener('input', sincronizarHiddenImpresora);
                });
            } else if (data.campos?.length) {
                // CPU y todas las demás categorías muestran especificaciones
                let h = '<hr><h6 style="font-family:\'Syne\',sans-serif;font-weight:700;margin-bottom:1rem">Especificaciones</h6><div class="row g-3">';
                data.campos.forEach(c => {
                    h += '<div class="col-md-6"><label class="form-label">'+c.etiqueta+'</label>';
                    if (c.tipo === 'select' && c.opciones) {
                        const opts = c.opciones.split('|');
                        h += '<select name="spec['+c.id+']" class="form-select"><option value="">— Selecciona —</option>';
                        opts.forEach(o => h += '<option value="'+o+'">'+o+'</option>');
                        h += '</select>';
                    } else if (c.tipo === 'numero') {
                        h += '<input type="number" name="spec['+c.id+']" class="form-control">';
                    } else {
                        h += '<input type="text" name="spec['+c.id+']" class="form-control">';
                    }
                    h += '</div>';
                });
                h += '</div>';
                document.getElementById('camposDinamicos').innerHTML = h;
            }
        });
}

document.getElementById('selCat')?.addEventListener('change', cargarDatosCategoria);

if (document.getElementById('selCat')?.value && !document.getElementById('inpCodigo')?.value) {
    cargarDatosCategoria.call(document.getElementById('selCat'));
}
</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
