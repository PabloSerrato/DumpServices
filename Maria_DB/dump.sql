-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-07-2024 a las 19:11:26
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `dump`
--
CREATE DATABASE IF NOT EXISTS `dump` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `dump`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones`
--

CREATE TABLE `calificaciones` (
  `id` int(11) NOT NULL,
  `id_operario` int(11) NOT NULL,
  `id_solicitud` int(11) NOT NULL,
  `calificacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL,
  `id_solicitud` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `fecha_envio` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `operarios`
--

CREATE TABLE `operarios` (
  `id_operario` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `marca_motocarro` varchar(100) DEFAULT NULL,
  `modelo_motocarro` varchar(100) DEFAULT NULL,
  `año_motocarro` int(11) DEFAULT NULL,
  `placa_motocarro` varchar(20) DEFAULT NULL,
  `foto_motocarro` varchar(255) DEFAULT NULL,
  `foto_2` varchar(255) DEFAULT NULL,
  `foto_3` varchar(255) DEFAULT NULL,
  `foto_4` varchar(255) DEFAULT NULL,
  `foto_5` varchar(255) DEFAULT NULL,
  `foto_6` varchar(255) DEFAULT NULL,
  `foto_7` varchar(255) DEFAULT NULL,
  `foto_8` varchar(255) DEFAULT NULL,
  `foto_9` varchar(255) DEFAULT NULL,
  `foto_10` varchar(255) DEFAULT NULL,
  `direccion_domicilio` varchar(255) DEFAULT NULL,
  `certificado_antecedentes_judiciales` varchar(255) DEFAULT NULL,
  `certificado_seguridad_social` varchar(255) DEFAULT NULL,
  `licencia_conduccion` varchar(50) DEFAULT NULL,
  `seguro_vehiculo` varchar(255) DEFAULT NULL,
  `calificacion` int(11) DEFAULT NULL,
  `otros_detalles` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `operarios`
--

INSERT INTO `operarios` (`id_operario`, `id_usuario`, `marca_motocarro`, `modelo_motocarro`, `año_motocarro`, `placa_motocarro`, `foto_motocarro`, `foto_2`, `foto_3`, `foto_4`, `foto_5`, `foto_6`, `foto_7`, `foto_8`, `foto_9`, `foto_10`, `direccion_domicilio`, `certificado_antecedentes_judiciales`, `certificado_seguridad_social`, `licencia_conduccion`, `seguro_vehiculo`, `calificacion`, `otros_detalles`) VALUES
(13, 14, 'Yamaha', '2011', 2011, 'eag09c', 'media/fotos/RO42hZ9.png', 'media/fotos/10c.jpg', 'media/fotos/10c 10.jpg', 'media/fotos/pixelcut-export-19-1264x734.jpg', 'media/fotos/IMG-20191103-WA0033.jpg', 'media/fotos/1717524693.png', 'media/fotos/10c  6.jpg', 'media/fotos/10c 2.jpg', 'media/fotos/10c  7.jpg', 'media/fotos/10c  6.jpg', 'calle 4b sur #5-11', 'media/certificados/antecedentes/10c  6.jpg', 'media/certificados/seguridad/10c  7.jpg', 'media/certificados/licencia/10c  15.jpg', 'media/certificados/seguro/10c 8.jpg', NULL, 'fgbhjnk'),
(14, 15, 'pepe', '2011', 2011, 'e', 'media/fotos/10c  7.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'calle 2', 'media/certificados/antecedentes/10c  15.jpg', 'media/certificados/seguridad/10c  7.jpg', 'media/certificados/licencia/10c  7.jpg', 'media/certificados/seguro/10c  6.jpg', NULL, 'eerere');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `restablecer_password`
--

CREATE TABLE `restablecer_password` (
  `id` int(11) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `codigo` int(11) NOT NULL,
  `expira` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `restablecer_password`
--

INSERT INTO `restablecer_password` (`id`, `correo`, `codigo`, `expira`) VALUES
(1, 'cabrerasarrialuis@gmail.com', 696709, '2024-06-11 18:58:26'),
(2, 'cabrerasarrialuis@gmail.com', 931710, '2024-06-11 19:05:04'),
(3, 'cabrerasarrialuis@gmail.com', 515155, '2024-06-11 19:08:26'),
(4, 'cabrerasarrialuis@gmail.com', 311492, '2024-06-11 19:08:35'),
(5, 'cabrerasarrialuis@gmail.com', 156593, '2024-06-11 19:15:02'),
(6, 'admin2@example.com', 357317, '2024-06-11 19:15:16'),
(7, 'admin2@example.com', 707513, '2024-06-11 19:16:09'),
(8, 'cabrerasarrialuis@gmail.com', 436205, '2024-06-11 19:16:17'),
(9, 'cabrerasarrialuis@gmail.com', 915694, '2024-06-11 19:22:27'),
(10, 'cabrerasarrialuis@gmail.com', 996522, '2024-06-11 19:22:33'),
(11, 'cabrerasarrialuis@gmail.com', 930690, '2024-06-11 19:22:56'),
(12, 'cabrerasarrialuis@gmail.com', 190732, '2024-06-11 19:23:54'),
(13, 'cabrerasarrialuis@gmail.com', 254734, '2024-06-12 02:20:19'),
(14, 'cabrerasarrialuis@gmail.com', 535810, '2024-07-03 00:43:01'),
(15, 'cabrerasarrialuis@gmail.com', 813252, '2024-07-03 00:43:39'),
(19, 'cabrerasarrialuis@gmail.com', 214962, '2024-07-03 01:11:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `id` int(11) NOT NULL,
  `id_operario` int(11) NOT NULL,
  `id_solicitante` int(11) NOT NULL,
  `direccion_acarreo` varchar(255) NOT NULL,
  `detalles_acarreo` text DEFAULT NULL,
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(50) NOT NULL DEFAULT 'Espera'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `tipo_documento` enum('cc','ce') NOT NULL,
  `numero_documento` varchar(20) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `rol` enum('administrador','operador_logistico','solicitante_transporte') NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellidos`, `fecha_nacimiento`, `tipo_documento`, `numero_documento`, `correo`, `rol`, `contrasena`, `telefono`, `avatar`) VALUES
(4, 'Don Fernando', 'Marciano', '1990-01-01', 'cc', '123456789', 'admin@example.com', 'administrador', '123456789', '3133855958', NULL),
(14, 'Carlos Eduardo', 'cabrerasarrialuis@gmail.com', '2024-07-07', 'cc', '10258748', 'cabrerasarrialuis@gmail.com', 'operador_logistico', '123654', '3186004016', NULL),
(15, 'Paul Fernando', 'Cueto Chambo', '2024-07-31', 'ce', '123456789', 'cabreraarrialuis@gmail.com', 'operador_logistico', '654', '3186004016', NULL),
(16, 'Paul Fernando', 'cabrera', '2024-06-30', 'cc', '10258748', 'adcccmin@example.com', 'operador_logistico', '123456', '3162004516', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_operario` (`id_operario`),
  ADD KEY `id_solicitud` (`id_solicitud`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `operarios`
--
ALTER TABLE `operarios`
  ADD PRIMARY KEY (`id_operario`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `restablecer_password`
--
ALTER TABLE `restablecer_password`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_operario` (`id_operario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `operarios`
--
ALTER TABLE `operarios`
  MODIFY `id_operario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `restablecer_password`
--
ALTER TABLE `restablecer_password`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD CONSTRAINT `calificaciones_ibfk_1` FOREIGN KEY (`id_operario`) REFERENCES `operarios` (`id_operario`),
  ADD CONSTRAINT `calificaciones_ibfk_2` FOREIGN KEY (`id_solicitud`) REFERENCES `solicitudes` (`id`);

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `operarios`
--
ALTER TABLE `operarios`
  ADD CONSTRAINT `operarios_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `solicitudes_ibfk_1` FOREIGN KEY (`id_operario`) REFERENCES `operarios` (`id_operario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
