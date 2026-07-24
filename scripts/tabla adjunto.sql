CREATE TABLE IF NOT EXISTS adjuntos (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id        INT           NOT NULL,
    usuario_id       INT           NOT NULL,
    nombre_archivo   VARCHAR(255)  NOT NULL,
    nombre_original  VARCHAR(255)  NOT NULL,
    tipo_mime        VARCHAR(100)  NOT NULL,
    tamano           INT           NOT NULL,
    creado_en        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_adjunto_ticket  FOREIGN KEY (ticket_id)  REFERENCES tickets(id)  ON DELETE CASCADE,
    CONSTRAINT fk_adjunto_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 