-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: mysql
-- Tiempo de generación: 06-07-2026 a las 14:02:05
-- Versión del servidor: 8.0.46
-- Versión de PHP: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `caissa_ti`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora`
--

CREATE TABLE `bitacora` (
  `id` int NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `accion` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `modulo` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  `ip` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bitacora`
--

INSERT INTO `bitacora` (`id`, `usuario_id`, `accion`, `modulo`, `descripcion`, `ip`, `created_at`) VALUES
(1, 1, 'login', 'auth', 'Inicio de sesión: admin', '::1', '2026-06-17 09:22:59'),
(2, 1, 'login', 'auth', 'Inicio de sesión: admin', '::1', '2026-06-17 09:59:54'),
(3, 1, 'crear', 'empleados', 'Empleado creado: 2', '::1', '2026-06-17 10:05:52'),
(4, 1, 'upload', 'documentos', 'Documento subido para empleado ID: 1', '::1', '2026-06-17 10:06:08'),
(5, 1, 'upload', 'documentos', 'Documento subido para empleado ID: 1', '::1', '2026-06-17 10:06:18'),
(6, 1, 'upload', 'documentos', 'Documento subido para empleado ID: 1', '::1', '2026-06-17 10:06:24'),
(7, 1, 'upload', 'documentos', 'Documento subido para empleado ID: 1', '::1', '2026-06-17 10:06:29'),
(8, 1, 'upload', 'documentos', 'Documento subido para empleado ID: 1', '::1', '2026-06-17 10:06:34'),
(9, 1, 'login', 'auth', 'Inicio de sesión: admin', '::1', '2026-06-17 18:39:42'),
(10, 1, 'crear', 'catalogos', 'Catálogo departamentos creado', '::1', '2026-06-17 18:54:54'),
(11, 1, 'editar', 'empleados', 'Empleado editado: 2', '::1', '2026-06-17 18:58:21'),
(12, 1, 'crear', 'vacaciones', 'Solicitud de vacaciones creada ID: 1', '::1', '2026-06-17 18:58:37'),
(13, 1, 'aprobar', 'vacaciones', 'Solicitud aprobada ID: 1', '::1', '2026-06-17 18:58:41'),
(14, 1, 'crear', 'faltas', 'Falta registrada ID: 1', '::1', '2026-06-17 19:54:41'),
(15, 1, 'baja', 'empleados', 'Baja de empleado ID: 1', '::1', '2026-06-17 20:31:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos`
--

CREATE TABLE `departamentos` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `departamentos`
--

INSERT INTO `departamentos` (`id`, `nombre`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Scada', '', 1, '2026-06-17 18:54:54', '2026-06-17 18:54:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int NOT NULL,
  `numero_empleado` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` varchar(80) COLLATE utf8mb4_general_ci NOT NULL,
  `apellido_paterno` varchar(80) COLLATE utf8mb4_general_ci NOT NULL,
  `apellido_materno` varchar(80) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `curp` varchar(18) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rfc` varchar(13) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nss` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `correo` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `domicilio` text COLLATE utf8mb4_general_ci,
  `fecha_nacimiento` date DEFAULT NULL,
  `departamento_id` int DEFAULT NULL,
  `puesto_id` int DEFAULT NULL,
  `tipo_contrato_id` int DEFAULT NULL,
  `fecha_ingreso` date NOT NULL,
  `fecha_fin_contrato` date DEFAULT NULL,
  `tipo_pago` enum('semanal','quincenal') COLLATE utf8mb4_general_ci DEFAULT 'quincenal',
  `monto_pago` decimal(12,2) DEFAULT '0.00',
  `estatus` enum('activo','inactivo','baja') COLLATE utf8mb4_general_ci DEFAULT 'activo',
  `observaciones` text COLLATE utf8mb4_general_ci,
  `created_by` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado_documentos`
--

CREATE TABLE `empleado_documentos` (
  `id` int NOT NULL,
  `empleado_id` int NOT NULL,
  `tipo_documento_id` int NOT NULL,
  `nombre_archivo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ruta_archivo` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `extension` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tamanio` int DEFAULT NULL,
  `fecha_carga` datetime DEFAULT CURRENT_TIMESTAMP,
  `cargado_por` int DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `faltas_empleados`
--

CREATE TABLE `faltas_empleados` (
  `id` int NOT NULL,
  `empleado_id` int NOT NULL,
  `fecha_falta` date NOT NULL,
  `tipo_descuento_id` int NOT NULL,
  `dias_horas` decimal(5,2) DEFAULT '1.00',
  `monto_descontado` decimal(12,2) DEFAULT '0.00',
  `motivo` text COLLATE utf8mb4_general_ci,
  `estatus` enum('activo','cancelado') COLLATE utf8mb4_general_ci DEFAULT 'activo',
  `registrado_por` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `politicas_vacaciones`
--

CREATE TABLE `politicas_vacaciones` (
  `id` int NOT NULL,
  `anio_inicio` int NOT NULL,
  `anio_fin` int NOT NULL,
  `dias` int NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `politicas_vacaciones`
--

INSERT INTO `politicas_vacaciones` (`id`, `anio_inicio`, `anio_fin`, `dias`, `activo`, `created_at`) VALUES
(1, 1, 1, 12, 1, '2026-06-17 09:19:51'),
(2, 2, 2, 14, 1, '2026-06-17 09:19:51'),
(3, 3, 3, 16, 1, '2026-06-17 09:19:51'),
(4, 4, 4, 18, 1, '2026-06-17 09:19:51'),
(5, 5, 5, 20, 1, '2026-06-17 09:19:51'),
(6, 6, 10, 22, 1, '2026-06-17 09:19:51'),
(7, 11, 15, 24, 1, '2026-06-17 09:19:51'),
(8, 16, 20, 26, 1, '2026-06-17 09:19:51'),
(9, 21, 25, 28, 1, '2026-06-17 09:19:51'),
(10, 26, 30, 30, 1, '2026-06-17 09:19:51'),
(11, 31, 35, 32, 1, '2026-06-17 09:19:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puestos`
--

CREATE TABLE `puestos` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `departamento_id` int DEFAULT NULL,
  `descripcion` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'administrador', 'Acceso total al sistema', 1, '2026-06-17 09:19:51', '2026-06-17 09:19:51'),
(2, 'recursos_humanos', 'Gestión de empleados y RH', 1, '2026-06-17 09:19:51', '2026-06-17 09:19:51'),
(3, 'consulta', 'Solo lectura', 1, '2026-06-17 09:19:51', '2026-06-17 09:19:51'),
(4, 'autorizador', 'Aprueba solicitudes', 1, '2026-06-17 09:19:51', '2026-06-17 09:19:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_contrato`
--

CREATE TABLE `tipos_contrato` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_contrato`
--

INSERT INTO `tipos_contrato` (`id`, `nombre`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Indefinido', NULL, 1, '2026-06-17 09:19:51', '2026-06-17 09:19:51'),
(2, 'Temporal', NULL, 1, '2026-06-17 09:19:51', '2026-06-17 09:19:51'),
(3, 'Por obra determinada', NULL, 1, '2026-06-17 09:19:51', '2026-06-17 09:19:51'),
(4, 'Periodo de prueba', NULL, 1, '2026-06-17 09:19:51', '2026-06-17 09:19:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_descuento`
--

CREATE TABLE `tipos_descuento` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_descuento`
--

INSERT INTO `tipos_descuento` (`id`, `nombre`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Falta injustificada', 'Ausencia sin justificación', 1, '2026-06-17 09:19:51', '2026-06-17 09:19:51'),
(2, 'Falta justificada', 'Ausencia con justificación', 1, '2026-06-17 09:19:51', '2026-06-17 09:19:51'),
(3, 'Retardo', 'Llegada tarde', 1, '2026-06-17 09:19:51', '2026-06-17 09:19:51'),
(4, 'Permiso sin goce', 'Permiso sin goce de sueldo', 1, '2026-06-17 09:19:51', '2026-06-17 09:19:51'),
(5, 'Otro', 'Otro tipo de descuento', 1, '2026-06-17 09:19:51', '2026-06-17 09:19:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_documento`
--

CREATE TABLE `tipos_documento` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `requerido` tinyint(1) DEFAULT '0',
  `activo` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_documento`
--

INSERT INTO `tipos_documento` (`id`, `nombre`, `requerido`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'CV', 1, 1, '2026-06-17 09:19:51', '2026-06-17 20:48:07'),
(2, 'Comprobante de domicilio', 1, 1, '2026-06-17 09:19:51', '2026-06-17 09:19:51'),
(3, 'Comprobante de estudios', 1, 1, '2026-06-17 09:19:51', '2026-06-17 09:19:51'),
(4, 'Identificación oficial', 1, 1, '2026-06-17 09:19:51', '2026-06-17 09:19:51'),
(5, 'Contrato firmado', 1, 1, '2026-06-17 09:19:51', '2026-06-17 09:19:51'),
(6, 'Otro', 0, 1, '2026-06-17 09:19:51', '2026-06-17 09:19:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre_completo` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rol_id` int NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `ultimo_acceso` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `nombre_completo`, `email`, `rol_id`, `activo`, `ultimo_acceso`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2b$12$0JErIiovxgnHbm1t.Dz8v.XQHDlECaQWGqZL0t/5OMYhgk8YLpsES', 'Administrador del Sistema', 'admin@sistema.com', 1, 1, '2026-06-17 18:39:42', '2026-06-17 09:19:51', '2026-06-17 18:39:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacaciones_movimientos`
--

CREATE TABLE `vacaciones_movimientos` (
  `id` int NOT NULL,
  `empleado_id` int NOT NULL,
  `solicitud_id` int DEFAULT NULL,
  `tipo` enum('acumulado','consumido','ajuste') COLLATE utf8mb4_general_ci NOT NULL,
  `dias` int NOT NULL,
  `descripcion` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacaciones_solicitudes`
--

CREATE TABLE `vacaciones_solicitudes` (
  `id` int NOT NULL,
  `empleado_id` int NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `dias_solicitados` int NOT NULL,
  `comentarios` text COLLATE utf8mb4_general_ci,
  `estatus` enum('pendiente','aprobada','rechazada','cancelada') COLLATE utf8mb4_general_ci DEFAULT 'pendiente',
  `aprobado_por` int DEFAULT NULL,
  `fecha_aprobacion` datetime DEFAULT NULL,
  `motivo_rechazo` text COLLATE utf8mb4_general_ci,
  `created_by` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_empleado` (`numero_empleado`),
  ADD KEY `departamento_id` (`departamento_id`),
  ADD KEY `puesto_id` (`puesto_id`),
  ADD KEY `tipo_contrato_id` (`tipo_contrato_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indices de la tabla `empleado_documentos`
--
ALTER TABLE `empleado_documentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `empleado_id` (`empleado_id`),
  ADD KEY `tipo_documento_id` (`tipo_documento_id`),
  ADD KEY `cargado_por` (`cargado_por`);

--
-- Indices de la tabla `faltas_empleados`
--
ALTER TABLE `faltas_empleados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `empleado_id` (`empleado_id`),
  ADD KEY `tipo_descuento_id` (`tipo_descuento_id`),
  ADD KEY `registrado_por` (`registrado_por`);

--
-- Indices de la tabla `politicas_vacaciones`
--
ALTER TABLE `politicas_vacaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `puestos`
--
ALTER TABLE `puestos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `departamento_id` (`departamento_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `tipos_contrato`
--
ALTER TABLE `tipos_contrato`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `tipos_descuento`
--
ALTER TABLE `tipos_descuento`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `tipos_documento`
--
ALTER TABLE `tipos_documento`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `rol_id` (`rol_id`);

--
-- Indices de la tabla `vacaciones_movimientos`
--
ALTER TABLE `vacaciones_movimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `empleado_id` (`empleado_id`),
  ADD KEY `solicitud_id` (`solicitud_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indices de la tabla `vacaciones_solicitudes`
--
ALTER TABLE `vacaciones_solicitudes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `empleado_id` (`empleado_id`),
  ADD KEY `aprobado_por` (`aprobado_por`),
  ADD KEY `created_by` (`created_by`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `empleado_documentos`
--
ALTER TABLE `empleado_documentos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `faltas_empleados`
--
ALTER TABLE `faltas_empleados`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `politicas_vacaciones`
--
ALTER TABLE `politicas_vacaciones`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `puestos`
--
ALTER TABLE `puestos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipos_contrato`
--
ALTER TABLE `tipos_contrato`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipos_descuento`
--
ALTER TABLE `tipos_descuento`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tipos_documento`
--
ALTER TABLE `tipos_documento`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `vacaciones_movimientos`
--
ALTER TABLE `vacaciones_movimientos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `vacaciones_solicitudes`
--
ALTER TABLE `vacaciones_solicitudes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD CONSTRAINT `bitacora_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`),
  ADD CONSTRAINT `empleados_ibfk_2` FOREIGN KEY (`puesto_id`) REFERENCES `puestos` (`id`),
  ADD CONSTRAINT `empleados_ibfk_3` FOREIGN KEY (`tipo_contrato_id`) REFERENCES `tipos_contrato` (`id`),
  ADD CONSTRAINT `empleados_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `empleado_documentos`
--
ALTER TABLE `empleado_documentos`
  ADD CONSTRAINT `empleado_documentos_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`),
  ADD CONSTRAINT `empleado_documentos_ibfk_2` FOREIGN KEY (`tipo_documento_id`) REFERENCES `tipos_documento` (`id`),
  ADD CONSTRAINT `empleado_documentos_ibfk_3` FOREIGN KEY (`cargado_por`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `faltas_empleados`
--
ALTER TABLE `faltas_empleados`
  ADD CONSTRAINT `faltas_empleados_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`),
  ADD CONSTRAINT `faltas_empleados_ibfk_2` FOREIGN KEY (`tipo_descuento_id`) REFERENCES `tipos_descuento` (`id`),
  ADD CONSTRAINT `faltas_empleados_ibfk_3` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `puestos`
--
ALTER TABLE `puestos`
  ADD CONSTRAINT `puestos_ibfk_1` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);

--
-- Filtros para la tabla `vacaciones_movimientos`
--
ALTER TABLE `vacaciones_movimientos`
  ADD CONSTRAINT `vacaciones_movimientos_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`),
  ADD CONSTRAINT `vacaciones_movimientos_ibfk_2` FOREIGN KEY (`solicitud_id`) REFERENCES `vacaciones_solicitudes` (`id`),
  ADD CONSTRAINT `vacaciones_movimientos_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `vacaciones_solicitudes`
--
ALTER TABLE `vacaciones_solicitudes`
  ADD CONSTRAINT `vacaciones_solicitudes_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`),
  ADD CONSTRAINT `vacaciones_solicitudes_ibfk_2` FOREIGN KEY (`aprobado_por`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `vacaciones_solicitudes_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
