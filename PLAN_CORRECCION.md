# Plan de corrección del sistema CAISSA TI

Este documento define el plan propuesto para corregir y estabilizar el sistema antes de hacer cambios de código. La idea es avanzar por fases pequeñas, verificables y con bajo riesgo.

## Objetivo general

Dejar el sistema consistente, más seguro y más mantenible, sin cambiar su arquitectura principal. El sistema actual ya funciona como una aplicación PHP MVC liviana; la corrección se enfocará en sincronizar base de datos, configuración, seguridad, formularios, uploads y detalles funcionales detectados en el análisis.

## Estado actual resumido

El sistema tiene estos módulos principales:

- Autenticación y usuarios.
- Tickets de soporte.
- Comentarios e historial de estados.
- Adjuntos de tickets.
- Inventario de equipos.
- Imágenes de inventario.
- Asignaciones de equipos.
- Reportes / documentos imprimibles.
- Mantenimientos.
- Bajas de equipo.
- Notificaciones por correo SMTP.

La estructura general es correcta, pero hay puntos que deben corregirse antes de crecer el sistema:

- El esquema SQL no está completamente sincronizado con el código.
- Hay credenciales dentro de archivos versionables.
- Faltan protecciones CSRF en formularios.
- Algunas acciones sensibles usan GET.
- Hay inconsistencias de roles y estados.
- Algunas tablas/columnas usadas por el código no aparecen en el SQL base.
- Hay problemas de encoding en textos con acentos.
- Se almacenan datos sensibles de equipos en texto claro.

## Fase 1: Respaldo y preparación

Antes de modificar archivos se debe asegurar un punto de retorno.

### Tareas

- Revisar estado actual de Git.
- Crear una rama de trabajo, por ejemplo `codex/plan-correccion-caissa-ti`.
- Identificar archivos modificados por el usuario para no pisarlos.
- Crear respaldo del SQL actual antes de tocar scripts.
- Registrar qué archivo SQL será considerado el esquema principal del sistema.

### Entregables

- Rama de trabajo.
- Lista de archivos que se modificarán.
- Respaldo o copia del esquema SQL actual.

## Fase 2: Sincronización de base de datos

Esta es la fase más importante, porque el código actual depende de tablas y columnas que no están completas en los scripts SQL.

### Problemas detectados

El código usa tablas que no aparecen en `scripts/helpdesk_escuela.sql`:

- `mantenimientos`
- `bajas_equipo`
- `imagenes_baja`

El código usa columnas que no están en el SQL base:

- En `equipos`:
  - `direccion_mac`
  - `direccion_ip`
  - `usuario_pc`
  - `contrasena_pc`

- En `asignaciones`:
  - `nombre_obra`
  - `numero_contrato`

También hay inconsistencias de valores:

- El modelo `Equipo` usa estado `usado`, pero el SQL base no lo define.
- El controlador de usuarios usa rol `auxiliar_administrativo`, pero el SQL base solo define `maestro`, `tecnico`, `admin`.

### Tareas

- Crear o actualizar un script SQL principal coherente con el código actual.
- Agregar tablas faltantes:
  - `mantenimientos`
  - `bajas_equipo`
  - `imagenes_baja`
- Agregar columnas faltantes en `equipos` y `asignaciones`.
- Definir si el rol `auxiliar_administrativo` debe existir realmente o si debe reemplazarse por `maestro` u otro rol.
- Definir si el estado `usado` debe quedarse o eliminarse.
- Revisar `BD/caissa_ti.sql`, porque parece pertenecer a otro sistema de RH/vacaciones y no al sistema actual de TI.

### Entregables

- SQL actualizado.
- Script de migración incremental, si se necesita aplicar sobre una base existente.
- Documento breve con cambios de esquema.

## Fase 3: Configuración segura

Actualmente hay credenciales de base de datos y correo directamente en archivos PHP.

### Tareas

- Crear un archivo de ejemplo `.env.example` sin credenciales reales.
- Crear un cargador simple de variables de entorno o configuración local.
- Modificar:
  - `config/database.php`
  - `config/mail.php`
- Evitar dejar contraseñas reales en archivos versionables.
- Documentar cómo configurar entorno local y producción.

### Entregables

- `.env.example`
- Configuración basada en entorno.
- Documentación de variables requeridas.

## Fase 4: Seguridad de sesiones y formularios

El sistema valida sesión y rol, pero le faltan defensas importantes.

### Tareas

- Agregar token CSRF global.
- Incluir token CSRF en formularios POST.
- Validar token CSRF en controladores antes de procesar cambios.
- Cambiar acciones sensibles que usan GET a POST:
  - eliminar equipo
  - activar/desactivar usuario
  - otras acciones destructivas similares
- Regenerar ID de sesión después de login.
- Revisar permisos por rol en cada controlador.

### Entregables

- Helper CSRF.
- Formularios protegidos.
- Acciones sensibles migradas a POST.
- Sesión más segura.

## Fase 5: Corrección de roles y permisos

Hay una mezcla conceptual entre `maestro`, `auxiliar_administrativo`, `tecnico` y `admin`.

### Tareas

- Definir matriz de roles:
  - qué puede ver cada rol;
  - qué puede crear;
  - qué puede editar;
  - qué puede eliminar;
  - qué puede imprimir.
- Corregir registro público para asignar un rol válido.
- Corregir creación/edición de usuarios.
- Evitar que usuarios no admin accedan a módulos administrativos.

### Propuesta inicial de roles

| Rol | Acceso propuesto |
| --- | --- |
| `admin` | Todo el sistema |
| `tecnico` | Tickets y mantenimiento operativo |
| `maestro` o `auxiliar_administrativo` | Crear/ver sus tickets y consultar información propia |

La decisión pendiente es si se conserva `maestro`, se reemplaza por `auxiliar_administrativo`, o se permiten ambos.

### Entregables

- Matriz de permisos.
- Código y SQL alineados con los roles definitivos.

## Fase 6: Corrección de encoding

Hay textos con caracteres dañados como `ContraseÃ±a`, `DaÃ±ado`, `ResoluciÃ³n`.

### Tareas

- Revisar archivos PHP y SQL con problemas de codificación.
- Convertir archivos a UTF-8.
- Corregir textos visibles en vistas, modelos y servicios.
- Verificar que los valores guardados en base de datos coincidan con los ENUM esperados.

### Entregables

- Archivos normalizados en UTF-8.
- Textos corregidos en la interfaz.
- SQL sin mojibake.

## Fase 7: Datos sensibles de inventario

El sistema guarda usuario y contraseña de PC/equipo. Esto puede ser útil operativamente, pero debe tratarse como información sensible.

### Tareas

- Decidir si `contrasena_pc` debe seguir almacenándose.
- Si debe conservarse:
  - cifrar el valor en base de datos;
  - restringir visualización solo a admin;
  - evitar exponerla en vistas públicas, PDFs o correos;
  - registrar auditoría cuando se consulte.
- Si no debe conservarse:
  - eliminar campo de vistas, modelo y base de datos.

### Entregables

- Manejo seguro de `contrasena_pc`.
- Restricciones de visualización.

## Fase 8: Uploads y archivos

Los uploads de tickets e inventario tienen `.htaccess`, pero bajas todavía necesita endurecimiento.

### Tareas

- Crear protección para `uploads/bajas`.
- Validar extensiones y MIME en todos los uploads.
- Normalizar límites de tamaño.
- Evitar servir archivos sin verificar permisos.
- Revisar eliminación física de archivos para no borrar rutas inesperadas.
- Opcional: centralizar lógica de uploads en un servicio.

### Entregables

- `.htaccess` para bajas.
- Validación homogénea de uploads.
- Manejo más seguro de archivos.

## Fase 9: Correos SMTP

El sistema tiene un cliente SMTP propio, pero desactiva validación TLS.

### Tareas

- Mover credenciales SMTP a variables de entorno.
- Activar verificación TLS en producción.
- Mejorar manejo de errores de correo para no bloquear operaciones críticas.
- Revisar plantillas de correo con textos corregidos.
- Evaluar reemplazar `SmtpMailer` manual por PHPMailer si se desea más robustez.

### Entregables

- Configuración SMTP segura.
- Plantillas corregidas.
- Manejo de errores consistente.

## Fase 10: Correcciones funcionales puntuales

### Tareas detectadas

- Hacer que `UserController::edit()` procese el campo `password_nueva`.
- Revisar generación de folios con `COUNT(*) + 1`, porque puede duplicarse con concurrencia.
- Agregar validaciones más estrictas en:
  - tickets;
  - usuarios;
  - inventario;
  - asignaciones;
  - mantenimiento;
  - bajas.
- Revisar vistas imprimibles que usan rutas absolutas o dominios fijos.
- Separar configuración de `APP_URL` por entorno.
- Revisar que la vista pública de mantenimiento no exponga información sensible.

### Entregables

- Bugs corregidos.
- Validaciones reforzadas.
- Folios más seguros.

## Fase 11: Limpieza de estructura

### Tareas

- Decidir si `BD/caissa_ti.sql` debe conservarse, moverse o marcarse como archivo ajeno/legacy.
- Revisar `vendor/fpdf`; actualmente los reportes parecen ser HTML imprimible, no generación real con FPDF.
- Documentar instalación local.
- Documentar flujo de despliegue.
- Crear README técnico mínimo.

### Entregables

- Estructura más clara.
- README de instalación/uso técnico.
- SQL principal identificado.

## Fase 12: Pruebas y verificación

No se pudo ejecutar `php -l` porque PHP no está disponible en el PATH local. Cuando haya runtime PHP disponible, se deben ejecutar validaciones.

### Tareas

- Validar sintaxis PHP con `php -l`.
- Probar login/logout.
- Probar alta de usuario.
- Probar creación de ticket.
- Probar comentario y adjunto.
- Probar cambio de estado de ticket.
- Probar alta/edición de equipo.
- Probar imágenes de inventario.
- Probar asignación y devolución.
- Probar mantenimiento y vista pública por token.
- Probar baja de equipo.
- Probar documentos imprimibles.
- Probar envío de correo en entorno controlado.

### Entregables

- Checklist de pruebas manuales.
- Registro de errores encontrados.
- Confirmación de flujo completo.

## Orden recomendado de implementación

1. Crear rama y respaldo.
2. Sincronizar SQL.
3. Sacar credenciales a entorno.
4. Corregir roles/estados inconsistentes.
5. Corregir encoding.
6. Agregar CSRF y convertir acciones sensibles a POST.
7. Endurecer uploads.
8. Corregir bugs puntuales.
9. Revisar correos.
10. Documentar instalación y pruebas.

## Riesgo estimado por fase

| Fase | Riesgo | Motivo |
| --- | --- | --- |
| SQL | Alto | Puede afectar instalación o datos existentes |
| Configuración | Medio | Cambia cómo se conecta a DB/correo |
| CSRF/POST | Medio | Requiere tocar muchos formularios |
| Roles | Medio | Afecta accesos |
| Encoding | Bajo/Medio | Puede afectar ENUMs si se corrige mal |
| Uploads | Medio | Afecta archivos existentes |
| Correos | Bajo/Medio | Puede afectar notificaciones |
| Documentación | Bajo | No cambia ejecución |

## Pendientes de decisión

Antes de implementar, conviene decidir:

1. ¿El rol correcto para usuarios normales será `maestro`, `auxiliar_administrativo` o ambos?
2. ¿Se debe seguir guardando `contrasena_pc`?
3. ¿El archivo `BD/caissa_ti.sql` pertenece a este proyecto o es de otro sistema?
4. ¿Los documentos imprimibles deben seguir siendo HTML con `window.print()` o quieres PDFs reales generados en servidor?
5. ¿La base de datos actual ya tiene datos en producción que deban migrarse sin pérdida?

## Resultado esperado

Al terminar este plan, el sistema debería quedar:

- instalable desde cero con un SQL coherente;
- más seguro en credenciales, formularios y uploads;
- consistente en roles, estados y tablas;
- con textos corregidos en UTF-8;
- con menos riesgo de errores por base de datos desactualizada;
- documentado para mantenimiento futuro.
