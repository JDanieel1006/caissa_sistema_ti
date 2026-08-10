-- Agrega el rol Residente/Becario a usuarios.rol si la columna es ENUM.
-- Ejecutar sobre la base de datos actual del sistema.

ALTER TABLE usuarios
MODIFY rol ENUM(
    'maestro',
    'tecnico',
    'admin',
    'auxiliar_administrativo',
    'coordinador',
    'operario',
    'ayudante',
    'residente_becario',
    'control_de_obra',
    'supervisor_seguridad',
    'contra_incendios',
    'tecnico_instrumentista'
) NOT NULL DEFAULT 'auxiliar_administrativo';
