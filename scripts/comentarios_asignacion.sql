CREATE TABLE IF NOT EXISTS comentarios_asignacion (
  id INT AUTO_INCREMENT PRIMARY KEY,
  asignacion_id INT NOT NULL,
  usuario_id INT NOT NULL,
  comentario TEXT NOT NULL,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_comentarios_asignacion (asignacion_id),
  KEY idx_comentarios_asignacion_usuario (usuario_id),
  CONSTRAINT fk_comentarios_asignacion_asignacion
    FOREIGN KEY (asignacion_id) REFERENCES asignaciones(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_comentarios_asignacion_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
