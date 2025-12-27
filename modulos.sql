-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-12-2025 a las 14:49:08
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
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id_modulo` bigint(20) NOT NULL,
  `titulo_modulo` text NOT NULL,
  `descript_modulo` text NOT NULL,
  `estado_modulo` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id_modulo`, `titulo_modulo`, `descript_modulo`, `estado_modulo`) VALUES
(1, 'Dashboard', 'Pagina pricipal', 1),
(2, 'Usuarios', 'CRUD Usuarios del Sistema', 1),
(3, 'Roles', 'Permisos de Usuarios', 1),
(4, 'Candidatos', 'CRUD Candidatos', 1),
(5, 'Lideres', 'CRUD Lideres de Campaña', 1),
(6, 'Electores', 'CRUD Electores', 1),
(7, 'Terceros', 'CRUD Terceros para Ingresos y Gastos', 1),
(8, 'Conceptos', 'CRUD Conceptos de Ingresos y Gastos', 1),
(9, 'Elementos', 'CRUD Elementos de Campaña', 1),
(10, 'Movimientos', 'Registro de Ingresos y Gastos de Campaña', 1),
(11, 'Entradas', 'Entrada de Elementos de Campaña', 1),
(12, 'Salidas', 'Salidas Elementos de Campaña', 1),
(13, 'InformeElectores', 'Informe de Electores', 1),
(14, 'Agenda', 'Agenda de Eventos', 1),
(15, 'Votación', 'Registro de Votos Exitpooll', 1),
(16, 'Resultados', 'Registro formularios E-14', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id_modulo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id_modulo` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
