-- Módulo de historial de incidencias por equipo
-- Ejecutar sobre la base de datos actual del sistema CAISSA TI.

CREATE TABLE IF NOT EXISTS incidencias_equipo (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    folio          VARCHAR(30) NOT NULL UNIQUE,
    equipo_id      INT NOT NULL,
    tipo           ENUM('averia','dano_fisico','software','red','rendimiento','otro') NOT NULL DEFAULT 'averia',
    titulo         VARCHAR(180) NOT NULL,
    descripcion    TEXT NOT NULL,
    severidad      ENUM('baja','media','alta','critica') NOT NULL DEFAULT 'media',
    estado         ENUM('abierta','en_revision','resuelta','descartada') NOT NULL DEFAULT 'abierta',
    reportado_por  INT NOT NULL,
    notas_cierre   TEXT DEFAULT NULL,
    cerrado_en     DATETIME DEFAULT NULL,
    creado_en      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_incidencias_equipo (equipo_id),
    KEY idx_incidencias_estado (estado),
    KEY idx_incidencias_reportado_por (reportado_por),
    CONSTRAINT fk_incidencia_equipo FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE CASCADE,
    CONSTRAINT fk_incidencia_usuario FOREIGN KEY (reportado_por) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS imagenes_incidencia (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    incidencia_id   INT NOT NULL,
    nombre_archivo  VARCHAR(255) NOT NULL,
    nombre_original VARCHAR(255) NOT NULL,
    tipo_mime       VARCHAR(100) NOT NULL,
    tamano          INT NOT NULL,
    creado_en       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_imagenes_incidencia (incidencia_id),
    CONSTRAINT fk_imagen_incidencia FOREIGN KEY (incidencia_id) REFERENCES incidencias_equipo(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
