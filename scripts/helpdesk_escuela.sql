-- Mesa de Ayuda - Centro de Cómputo Escolar
-- Script completo de base de datos v9
-- Contraseña de usuarios demo: Admin123!

CREATE DATABASE IF NOT EXISTS helpdesk_escuela CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE helpdesk_escuela;

CREATE TABLE IF NOT EXISTS usuarios (id INT AUTO_INCREMENT PRIMARY KEY, nombre VARCHAR(100) NOT NULL, apellido VARCHAR(100) NOT NULL, email VARCHAR(150) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL, rol ENUM('maestro','tecnico','admin','auxiliar_administrativo','coordinador','operario','ayudante','residente_becario','auxiliar_seguridad','auxiliar_oficina','control_de_obra','supervisor_seguridad','contra_incendios','tecnico_instrumentista') NOT NULL DEFAULT 'auxiliar_administrativo', departamento VARCHAR(100) DEFAULT NULL, activo TINYINT(1) NOT NULL DEFAULT 1, creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tickets (id INT AUTO_INCREMENT PRIMARY KEY, folio VARCHAR(20) NOT NULL UNIQUE, titulo VARCHAR(200) NOT NULL, descripcion TEXT NOT NULL, categoria ENUM('internet','computadora','impresora','proyector','red','software','hardware','otro') NOT NULL, prioridad ENUM('baja','media','alta','critica') NOT NULL DEFAULT 'media', estado ENUM('abierto','en_proceso','en_espera','resuelto','cerrado') NOT NULL DEFAULT 'abierto', ubicacion VARCHAR(150) DEFAULT NULL, usuario_id INT NOT NULL, tecnico_id INT DEFAULT NULL, resolucion TEXT DEFAULT NULL, creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, cerrado_en DATETIME DEFAULT NULL, FOREIGN KEY (usuario_id) REFERENCES usuarios(id), FOREIGN KEY (tecnico_id) REFERENCES usuarios(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS comentarios (id INT AUTO_INCREMENT PRIMARY KEY, ticket_id INT NOT NULL, usuario_id INT NOT NULL, mensaje TEXT NOT NULL, es_interno TINYINT(1) NOT NULL DEFAULT 0, creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE, FOREIGN KEY (usuario_id) REFERENCES usuarios(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS historial_estados (id INT AUTO_INCREMENT PRIMARY KEY, ticket_id INT NOT NULL, usuario_id INT NOT NULL, estado_anterior ENUM('abierto','en_proceso','en_espera','resuelto','cerrado') DEFAULT NULL, estado_nuevo ENUM('abierto','en_proceso','en_espera','resuelto','cerrado') NOT NULL, nota VARCHAR(255) DEFAULT NULL, creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE, FOREIGN KEY (usuario_id) REFERENCES usuarios(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS adjuntos (id INT AUTO_INCREMENT PRIMARY KEY, ticket_id INT NOT NULL, usuario_id INT NOT NULL, nombre_archivo VARCHAR(255) NOT NULL, nombre_original VARCHAR(255) NOT NULL, tipo_mime VARCHAR(100) NOT NULL, tamano INT NOT NULL, creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE, FOREIGN KEY (usuario_id) REFERENCES usuarios(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categorias_equipo (id INT AUTO_INCREMENT PRIMARY KEY, nombre VARCHAR(100) NOT NULL, icono VARCHAR(50) NOT NULL DEFAULT 'bi-tools', activa TINYINT(1) NOT NULL DEFAULT 1) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS campos_categoria (id INT AUTO_INCREMENT PRIMARY KEY, categoria_id INT NOT NULL, nombre_campo VARCHAR(100) NOT NULL, etiqueta VARCHAR(100) NOT NULL, tipo ENUM('texto','numero','select') NOT NULL DEFAULT 'texto', opciones VARCHAR(500) DEFAULT NULL, requerido TINYINT(1) NOT NULL DEFAULT 0, orden INT NOT NULL DEFAULT 0, FOREIGN KEY (categoria_id) REFERENCES categorias_equipo(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS equipos (id INT AUTO_INCREMENT PRIMARY KEY, categoria_id INT NOT NULL, codigo VARCHAR(50) NOT NULL UNIQUE, marca VARCHAR(100) DEFAULT NULL, modelo VARCHAR(100) DEFAULT NULL, numero_serie VARCHAR(150) DEFAULT NULL, ubicacion VARCHAR(150) DEFAULT NULL, estado ENUM('bueno','dañado','en_reparacion','dado_de_baja') NOT NULL DEFAULT 'bueno', notas TEXT DEFAULT NULL, fecha_compra DATE DEFAULT NULL, creado_por INT NOT NULL, creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, FOREIGN KEY (categoria_id) REFERENCES categorias_equipo(id), FOREIGN KEY (creado_por) REFERENCES usuarios(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS especificaciones_equipo (id INT AUTO_INCREMENT PRIMARY KEY, equipo_id INT NOT NULL, campo_id INT NOT NULL, valor TEXT DEFAULT NULL, UNIQUE KEY uq_equipo_campo (equipo_id, campo_id), FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE CASCADE, FOREIGN KEY (campo_id) REFERENCES campos_categoria(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS imagenes_equipo (id INT AUTO_INCREMENT PRIMARY KEY, equipo_id INT NOT NULL, nombre_archivo VARCHAR(255) NOT NULL, nombre_original VARCHAR(255) NOT NULL, tamano INT NOT NULL, es_principal TINYINT(1) NOT NULL DEFAULT 0, creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS asignaciones (id INT AUTO_INCREMENT PRIMARY KEY, equipo_id INT NOT NULL, usuario_id INT NOT NULL, entregado_por INT NOT NULL, folio VARCHAR(30) NOT NULL UNIQUE, estado ENUM('activa','devuelta','cancelada') NOT NULL DEFAULT 'activa', condicion_entrega ENUM('bueno','dañado','en_reparacion') NOT NULL DEFAULT 'bueno', condicion_devolucion ENUM('bueno','dañado','en_reparacion') DEFAULT NULL, fecha_asignacion DATE NOT NULL, fecha_devolucion_esperada DATE DEFAULT NULL, fecha_devolucion_real DATE DEFAULT NULL, notas_entrega TEXT DEFAULT NULL, notas_devolucion TEXT DEFAULT NULL, creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, FOREIGN KEY (equipo_id) REFERENCES equipos(id), FOREIGN KEY (usuario_id) REFERENCES usuarios(id), FOREIGN KEY (entregado_por) REFERENCES usuarios(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS comentarios_asignacion (id INT AUTO_INCREMENT PRIMARY KEY, asignacion_id INT NOT NULL, usuario_id INT NOT NULL, comentario TEXT NOT NULL, creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, KEY idx_comentarios_asignacion (asignacion_id), KEY idx_comentarios_asignacion_usuario (usuario_id), CONSTRAINT fk_comentarios_asignacion_asignacion FOREIGN KEY (asignacion_id) REFERENCES asignaciones(id) ON DELETE CASCADE, CONSTRAINT fk_comentarios_asignacion_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuarios demo (contraseña: Admin123!)
INSERT INTO usuarios (nombre,apellido,email,password,rol,departamento) VALUES
('Administrador','Sistema','admin@escuela.edu.mx','$2b$12$0JErIiovxgnHbm1t.Dz8v.XQHDlECaQWGqZL0t/5OMYhgk8YLpsES','admin','Centro de Cómputo'),
('Carlos','Mendoza','tecnico@escuela.edu.mx','$2b$12$0JErIiovxgnHbm1t.Dz8v.XQHDlECaQWGqZL0t/5OMYhgk8YLpsES','tecnico','Soporte Técnico'),
('María','González','maestro@escuela.edu.mx','$2b$12$0JErIiovxgnHbm1t.Dz8v.XQHDlECaQWGqZL0t/5OMYhgk8YLpsES','maestro','Matemáticas'),
('Roberto','Herrera','rherrera@escuela.edu.mx','$2b$12$0JErIiovxgnHbm1t.Dz8v.XQHDlECaQWGqZL0t/5OMYhgk8YLpsES','maestro','Ciencias');

INSERT INTO categorias_equipo (id,nombre,icono) VALUES (1,'CPU / Computadora','bi-pc-display'),(2,'Proyector','bi-projector'),(3,'Teclado','bi-keyboard'),(4,'Mouse','bi-mouse'),(5,'Cable de Red','bi-ethernet'),(6,'Control Remoto','bi-wifi'),(7,'Bocinas','bi-speaker');

INSERT INTO campos_categoria (categoria_id,nombre_campo,etiqueta,tipo,orden) VALUES (1,'procesador','Procesador','texto',1),(1,'ram','Memoria RAM','texto',2),(1,'almacenamiento','Almacenamiento','texto',3),(1,'sistema_op','Sistema Operativo','texto',4),(1,'tipo','Tipo','select',5),(2,'lumens','Lúmenes','numero',1),(2,'resolucion','Resolución','select',2),(2,'tecnologia','Tecnología','select',3),(3,'conexion','Conexión','select',1),(3,'idioma','Idioma','select',2),(4,'conexion','Conexión','select',1),(4,'dpi','DPI','numero',2),(5,'categoria','Categoría','select',1),(5,'longitud','Longitud (metros)','numero',2),(7,'potencia','Potencia (W)','numero',1),(7,'conexion','Conexión','select',2);

UPDATE campos_categoria SET opciones='Escritorio|Laptop|All-in-One|Servidor' WHERE nombre_campo='tipo' AND categoria_id=1;
UPDATE campos_categoria SET opciones='XGA (1024x768)|HD (1280x720)|Full HD (1920x1080)|4K' WHERE nombre_campo='resolucion' AND categoria_id=2;
UPDATE campos_categoria SET opciones='DLP|LCD|LED|Láser' WHERE nombre_campo='tecnologia' AND categoria_id=2;
UPDATE campos_categoria SET opciones='USB|PS/2|Bluetooth|Inalámbrico' WHERE nombre_campo='conexion' AND categoria_id=3;
UPDATE campos_categoria SET opciones='Español|Inglés' WHERE nombre_campo='idioma' AND categoria_id=3;
UPDATE campos_categoria SET opciones='USB|PS/2|Bluetooth|Inalámbrico' WHERE nombre_campo='conexion' AND categoria_id=4;
UPDATE campos_categoria SET opciones='Cat 5|Cat 5e|Cat 6|Cat 6a|Cat 7' WHERE nombre_campo='categoria' AND categoria_id=5;
UPDATE campos_categoria SET opciones='USB|Jack 3.5mm|Bluetooth|RCA' WHERE nombre_campo='conexion' AND categoria_id=7;

INSERT INTO tickets (folio,titulo,descripcion,categoria,prioridad,estado,ubicacion,usuario_id,tecnico_id) VALUES
('TKT-2025-001','Sin conexión a internet en aula 3B','Las computadoras del aula 3B no tienen acceso a internet desde esta mañana.','internet','alta','en_proceso','Aula 3B',3,2),
('TKT-2025-002','Proyector no enciende','El proyector del aula de usos múltiples no enciende. La luz parpadea en rojo.','proyector','media','abierto','Sala de Usos Múltiples',4,NULL),
('TKT-2025-003','Computadora reinicia sola','La PC número 5 del laboratorio se reinicia cada 20-30 minutos sin aviso.','computadora','alta','resuelto','Laboratorio de Cómputo',3,2);

INSERT INTO historial_estados (ticket_id,usuario_id,estado_anterior,estado_nuevo,nota) VALUES (1,2,'abierto','en_proceso','Técnico asignado'),(3,2,'abierto','en_proceso','Diagnóstico iniciado'),(3,2,'en_proceso','resuelto','RAM reemplazada exitosamente');
