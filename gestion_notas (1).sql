-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-10-2024 a las 23:38:12
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
-- Base de datos: `gestion_notas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_notas`
--

CREATE TABLE `detalle_notas` (
  `id` int(11) NOT NULL,
  `id_matriculados` int(11) DEFAULT NULL,
  `id_periodo` int(11) DEFAULT NULL,
  `nota` varchar(11) NOT NULL,
  `fecha` date NOT NULL,
  `aprobo_reprobo` enum('Aprobado','Reprobado') NOT NULL,
  `id_materia` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_notas`
--

INSERT INTO `detalle_notas` (`id`, `id_matriculados`, `id_periodo`, `nota`, `fecha`, `aprobo_reprobo`, `id_materia`) VALUES
(1, 1, 1, '4,6', '2024-10-07', 'Aprobado', NULL),
(2, 1, 1, '3,4', '2024-10-07', 'Aprobado', NULL),
(3, 1, 1, '3.8', '2024-10-07', 'Aprobado', NULL),
(4, 2, 1, '4', '2024-10-07', 'Aprobado', NULL),
(5, 2, 1, '5', '2024-10-07', 'Aprobado', NULL),
(6, 2, 1, '4', '2024-10-07', 'Aprobado', NULL),
(7, 1, 2, '4.6', '2024-10-07', 'Aprobado', NULL),
(8, 1, 2, '2.6', '2024-10-07', 'Aprobado', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

CREATE TABLE `estudiantes` (
  `id` int(11) NOT NULL,
  `primer_nombre` varchar(50) NOT NULL,
  `segundo_nombre` varchar(50) DEFAULT NULL,
  `primer_apellido` varchar(50) NOT NULL,
  `segundo_apellido` varchar(50) DEFAULT NULL,
  `cedula` varchar(20) NOT NULL,
  `correo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`id`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `cedula`, `correo`) VALUES
(1, 'Alaia', NULL, 'Tordecilla', 'Perez', '1003', 'ala@mail.com'),
(2, 'Carlos', 'Mario', 'Pérez', 'Alaez', '238', 'car@mail.com'),
(3, 'Ana', 'Lucia', 'López', 'Montes', '343', 'ana@mail.com'),
(4, 'Pedro', 'Luiss', 'Martínez', 'Ruiz', '773888', 'ped@mail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_profesor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias`
--

INSERT INTO `materias` (`id`, `nombre`, `id_profesor`) VALUES
(1, 'PHP', 1),
(2, 'Ingles', 2),
(3, 'Programación', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matriculadosmaterias`
--

CREATE TABLE `matriculadosmaterias` (
  `id` int(11) NOT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `id_materia` int(11) DEFAULT NULL,
  `id_profesor` int(11) DEFAULT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `matriculadosmaterias`
--

INSERT INTO `matriculadosmaterias` (`id`, `id_estudiante`, `id_materia`, `id_profesor`, `fecha`) VALUES
(1, 3, 1, 1, '2024-10-07'),
(2, 3, 2, 2, '2024-10-07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores`
--

CREATE TABLE `profesores` (
  `id` int(11) NOT NULL,
  `primer_nombre` varchar(50) NOT NULL,
  `segundo_nombre` varchar(50) DEFAULT NULL,
  `primer_apellido` varchar(50) NOT NULL,
  `segundo_apellido` varchar(50) DEFAULT NULL,
  `cedula` varchar(20) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `profesores`
--

INSERT INTO `profesores` (`id`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `cedula`, `correo`, `password`, `foto`) VALUES
(1, 'Alaia', '', 'Tordecilla', 'Romero', '1003', 'a@gmail.com', '$2y$10$oDmEOuqMOf8asGcDAHMMseNHCR4mUeVOWnxzGYBq/uCikqhJkA23e', 'uploads/67042864171c4_WhatsApp Image 2024-09-22 at 3.07.41 PM.jpeg'),
(2, 'Thiago', 'Jose', 'Perez', 'Romero', '1103', 'thiago@gmail.com', '$2y$10$XL87.b4emu.HkF1pqalwu.r2O7Mnuq2FcyC6zYknQxLStJ1sWRlcy', 'uploads/670428dd87110_WhatsApp Image 2024-09-22 at 3.07.43 PM (2).jpeg');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `detalle_notas`
--
ALTER TABLE `detalle_notas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_matriculados` (`id_matriculados`),
  ADD KEY `id_periodo` (`id_periodo`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cedula` (`cedula`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_profesor` (`id_profesor`);

--
-- Indices de la tabla `matriculadosmaterias`
--
ALTER TABLE `matriculadosmaterias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_materia` (`id_materia`),
  ADD KEY `id_profesor` (`id_profesor`);

--
-- Indices de la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cedula` (`cedula`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `detalle_notas`
--
ALTER TABLE `detalle_notas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `matriculadosmaterias`
--
ALTER TABLE `matriculadosmaterias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `profesores`
--
ALTER TABLE `profesores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_notas`
--
ALTER TABLE `detalle_notas`
  ADD CONSTRAINT `detalle_notas_ibfk_1` FOREIGN KEY (`id_matriculados`) REFERENCES `matriculadosmaterias` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_notas_ibfk_2` FOREIGN KEY (`id_periodo`) REFERENCES `periodos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `materias`
--
ALTER TABLE `materias`
  ADD CONSTRAINT `materias_ibfk_1` FOREIGN KEY (`id_profesor`) REFERENCES `profesores` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `matriculadosmaterias`
--
ALTER TABLE `matriculadosmaterias`
  ADD CONSTRAINT `matriculadosmaterias_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matriculadosmaterias_ibfk_2` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matriculadosmaterias_ibfk_3` FOREIGN KEY (`id_profesor`) REFERENCES `profesores` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
