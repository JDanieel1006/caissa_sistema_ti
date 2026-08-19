INSERT INTO categorias_equipo (nombre, icono)
SELECT 'Móvil / Tablet', 'bi-phone'
WHERE NOT EXISTS (
  SELECT 1 FROM categorias_equipo WHERE nombre IN ('Móvil / Tablet', 'Movil / Tablet', 'Móvil/Tablet', 'Movil/Tablet')
);

INSERT INTO categorias_equipo (nombre, icono)
SELECT 'Starlink', 'bi-router'
WHERE NOT EXISTS (
  SELECT 1 FROM categorias_equipo WHERE nombre = 'Starlink'
);

INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'tipo_dispositivo', 'Tipo de dispositivo', 'select', 'Móvil|Tablet', 1
FROM categorias_equipo c
WHERE c.nombre IN ('Móvil / Tablet', 'Movil / Tablet', 'Móvil/Tablet', 'Movil/Tablet')
  AND NOT EXISTS (
    SELECT 1 FROM campos_categoria cc
    WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'tipo_dispositivo'
  );

INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'sistema_operativo', 'Sistema operativo', 'select', 'Android|iOS|iPadOS|Otro', 2
FROM categorias_equipo c
WHERE c.nombre IN ('Móvil / Tablet', 'Movil / Tablet', 'Móvil/Tablet', 'Movil/Tablet')
  AND NOT EXISTS (
    SELECT 1 FROM campos_categoria cc
    WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'sistema_operativo'
  );

INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'imei', 'IMEI', 'texto', NULL, 3
FROM categorias_equipo c
WHERE c.nombre IN ('Móvil / Tablet', 'Movil / Tablet', 'Móvil/Tablet', 'Movil/Tablet')
  AND NOT EXISTS (
    SELECT 1 FROM campos_categoria cc
    WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'imei'
  );

INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'capacidad', 'Capacidad', 'texto', NULL, 4
FROM categorias_equipo c
WHERE c.nombre IN ('Móvil / Tablet', 'Movil / Tablet', 'Móvil/Tablet', 'Movil/Tablet')
  AND NOT EXISTS (
    SELECT 1 FROM campos_categoria cc
    WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'capacidad'
  );

INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'linea_telefono', 'Línea telefónica', 'texto', NULL, 5
FROM categorias_equipo c
WHERE c.nombre IN ('Móvil / Tablet', 'Movil / Tablet', 'Móvil/Tablet', 'Movil/Tablet')
  AND NOT EXISTS (
    SELECT 1 FROM campos_categoria cc
    WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'linea_telefono'
  );

INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'kit', 'Kit', 'select', 'Residencial|Roam|Prioridad|Empresarial|Otro', 1
FROM categorias_equipo c
WHERE c.nombre = 'Starlink'
  AND NOT EXISTS (
    SELECT 1 FROM campos_categoria cc
    WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'kit'
  );

INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'router', 'Router', 'texto', NULL, 2
FROM categorias_equipo c
WHERE c.nombre = 'Starlink'
  AND NOT EXISTS (
    SELECT 1 FROM campos_categoria cc
    WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'router'
  );

INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'antena', 'Antena', 'texto', NULL, 3
FROM categorias_equipo c
WHERE c.nombre = 'Starlink'
  AND NOT EXISTS (
    SELECT 1 FROM campos_categoria cc
    WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'antena'
  );

INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'plan_servicio', 'Plan / servicio', 'texto', NULL, 4
FROM categorias_equipo c
WHERE c.nombre = 'Starlink'
  AND NOT EXISTS (
    SELECT 1 FROM campos_categoria cc
    WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'plan_servicio'
  );

INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'ubicacion_instalacion', 'Ubicación de instalación', 'texto', NULL, 5
FROM categorias_equipo c
WHERE c.nombre = 'Starlink'
  AND NOT EXISTS (
    SELECT 1 FROM campos_categoria cc
    WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'ubicacion_instalacion'
  );
