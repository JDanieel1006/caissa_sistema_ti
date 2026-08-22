INSERT INTO categorias_equipo (nombre, icono)
SELECT 'Starlink', 'bi-router'
WHERE NOT EXISTS (
  SELECT 1 FROM categorias_equipo WHERE nombre = 'Starlink'
);

INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'tipo_kit', 'Tipo de kit', 'select', 'Standard|Standard Actuated|Mini|High Performance|Otro', 1
FROM categorias_equipo c
WHERE c.nombre = 'Starlink'
  AND NOT EXISTS (SELECT 1 FROM campos_categoria cc WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'tipo_kit');

INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'modelo_power_supply', 'Modelo del power supply', 'texto', NULL, 2
FROM categorias_equipo c
WHERE c.nombre = 'Starlink'
  AND NOT EXISTS (SELECT 1 FROM campos_categoria cc WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'modelo_power_supply');

INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'serie_power_supply', 'N.º de serie del power supply', 'texto', NULL, 3
FROM categorias_equipo c
WHERE c.nombre = 'Starlink'
  AND NOT EXISTS (SELECT 1 FROM campos_categoria cc WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'serie_power_supply');

INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'modelo_router', 'Modelo del router', 'texto', NULL, 4
FROM categorias_equipo c
WHERE c.nombre = 'Starlink'
  AND NOT EXISTS (SELECT 1 FROM campos_categoria cc WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'modelo_router');

INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'serie_router', 'N.º de serie del router', 'texto', NULL, 5
FROM categorias_equipo c
WHERE c.nombre = 'Starlink'
  AND NOT EXISTS (SELECT 1 FROM campos_categoria cc WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'serie_router');

INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'mac_router', 'MAC del router', 'texto', NULL, 6
FROM categorias_equipo c
WHERE c.nombre = 'Starlink'
  AND NOT EXISTS (SELECT 1 FROM campos_categoria cc WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'mac_router');

INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'plan_servicio', 'Plan / servicio', 'texto', NULL, 7
FROM categorias_equipo c
WHERE c.nombre = 'Starlink'
  AND NOT EXISTS (SELECT 1 FROM campos_categoria cc WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'plan_servicio');

INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'tipo_servicio', 'Tipo de servicio', 'select', 'Residencial|Empresarial|Roam|Otro', 8
FROM categorias_equipo c
WHERE c.nombre = 'Starlink'
  AND NOT EXISTS (SELECT 1 FROM campos_categoria cc WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'tipo_servicio');

INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'id_servicio', 'ID de servicio', 'texto', NULL, 9
FROM categorias_equipo c
WHERE c.nombre = 'Starlink'
  AND NOT EXISTS (SELECT 1 FROM campos_categoria cc WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'id_servicio');

INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'estado_servicio', 'Estado del servicio', 'select', 'Activo|Suspendido|Cancelado', 10
FROM categorias_equipo c
WHERE c.nombre = 'Starlink'
  AND NOT EXISTS (SELECT 1 FROM campos_categoria cc WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'estado_servicio');

INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'ubicacion_instalacion', 'Ubicación de instalación', 'texto', NULL, 11
FROM categorias_equipo c
WHERE c.nombre = 'Starlink'
  AND NOT EXISTS (SELECT 1 FROM campos_categoria cc WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'ubicacion_instalacion');

UPDATE campos_categoria cc
JOIN categorias_equipo c ON c.id = cc.categoria_id
SET cc.opciones = 'Standard|Standard Actuated|Mini|High Performance|Otro',
    cc.etiqueta = 'Tipo de kit',
    cc.orden = 1
WHERE c.nombre = 'Starlink' AND cc.nombre_campo = 'tipo_kit';

UPDATE campos_categoria cc
JOIN categorias_equipo c ON c.id = cc.categoria_id
SET cc.opciones = 'Residencial|Empresarial|Roam|Otro',
    cc.orden = 8
WHERE c.nombre = 'Starlink' AND cc.nombre_campo = 'tipo_servicio';

UPDATE campos_categoria cc
JOIN categorias_equipo c ON c.id = cc.categoria_id
SET cc.opciones = 'Activo|Suspendido|Cancelado',
    cc.orden = 10
WHERE c.nombre = 'Starlink' AND cc.nombre_campo = 'estado_servicio';
