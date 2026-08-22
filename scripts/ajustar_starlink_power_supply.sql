INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'modelo_power_supply', 'Modelo del power supply', 'texto', NULL, 2
FROM categorias_equipo c
WHERE c.nombre = 'Starlink'
  AND NOT EXISTS (
    SELECT 1 FROM campos_categoria cc
    WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'modelo_power_supply'
  );

INSERT INTO campos_categoria (categoria_id, nombre_campo, etiqueta, tipo, opciones, orden)
SELECT c.id, 'serie_power_supply', 'N.º de serie del power supply', 'texto', NULL, 3
FROM categorias_equipo c
WHERE c.nombre = 'Starlink'
  AND NOT EXISTS (
    SELECT 1 FROM campos_categoria cc
    WHERE cc.categoria_id = c.id AND cc.nombre_campo = 'serie_power_supply'
  );

UPDATE campos_categoria cc
JOIN categorias_equipo c ON c.id = cc.categoria_id
SET cc.orden = 2, cc.etiqueta = 'Modelo del power supply'
WHERE c.nombre = 'Starlink' AND cc.nombre_campo = 'modelo_power_supply';

UPDATE campos_categoria cc
JOIN categorias_equipo c ON c.id = cc.categoria_id
SET cc.orden = 3, cc.etiqueta = 'N.º de serie del power supply'
WHERE c.nombre = 'Starlink' AND cc.nombre_campo = 'serie_power_supply';

UPDATE campos_categoria cc
JOIN categorias_equipo c ON c.id = cc.categoria_id
SET cc.orden = 12
WHERE c.nombre = 'Starlink' AND cc.nombre_campo = 'modelo_terminal';

UPDATE campos_categoria cc
JOIN categorias_equipo c ON c.id = cc.categoria_id
SET cc.orden = 13
WHERE c.nombre = 'Starlink' AND cc.nombre_campo = 'serie_terminal';
