-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-12-2025 a las 04:26:46
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `db-votes`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id_permiso` bigint(20) NOT NULL,
  `rol_permiso` bigint(20) NOT NULL,
  `modulo_permiso` bigint(20) NOT NULL,
  `r_permiso` int(1) NOT NULL DEFAULT 0,
  `w_permiso` int(1) NOT NULL DEFAULT 0,
  `u_permiso` int(1) NOT NULL DEFAULT 0,
  `d_permiso` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id_permiso`, `rol_permiso`, `modulo_permiso`, `r_permiso`, `w_permiso`, `u_permiso`, `d_permiso`) VALUES
(142, 6, 1, 1, 0, 1, 0),
(143, 6, 2, 1, 0, 0, 0),
(144, 6, 3, 1, 0, 1, 1),
(145, 6, 4, 1, 1, 0, 1),
(146, 6, 5, 0, 0, 0, 0),
(147, 6, 6, 0, 0, 0, 0),
(148, 6, 7, 0, 0, 0, 0),
(149, 6, 8, 0, 0, 0, 0),
(150, 6, 9, 0, 0, 0, 0),
(151, 6, 10, 0, 0, 0, 0),
(152, 6, 11, 0, 0, 0, 0),
(153, 6, 12, 0, 0, 0, 0),
(154, 6, 13, 0, 0, 0, 0),
(155, 1, 1, 1, 1, 1, 1),
(156, 1, 2, 1, 1, 1, 1),
(157, 1, 3, 1, 1, 1, 1),
(158, 1, 4, 1, 1, 1, 1),
(159, 1, 5, 1, 1, 1, 1),
(160, 1, 6, 1, 1, 1, 1),
(161, 1, 7, 1, 1, 1, 1),
(162, 1, 8, 1, 1, 1, 1),
(163, 1, 9, 1, 1, 1, 1),
(164, 1, 10, 1, 1, 1, 1),
(165, 1, 11, 1, 1, 1, 1),
(166, 1, 12, 1, 1, 1, 1),
(167, 1, 13, 1, 1, 1, 1),
(168, 1, 14, 1, 1, 1, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id_permiso`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id_permiso` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
