-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-12-2025 a las 04:26:18
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
-- Estructura de tabla para la tabla `candidatos`
--

CREATE TABLE `candidatos` (
  `id_candidato` bigint(20) NOT NULL,
  `ident_candidato` varchar(10) NOT NULL,
  `ape1_candidato` text NOT NULL,
  `ape2_candidato` text NOT NULL,
  `nom1_candidato` text NOT NULL,
  `nom2_candidato` text NOT NULL,
  `telefono_candidato` varchar(10) NOT NULL,
  `email_candidato` text NOT NULL,
  `dpto_candidato` bigint(20) NOT NULL,
  `muni_candidato` bigint(20) NOT NULL,
  `direccion_candidato` text NOT NULL,
  `curul_candidato` text NOT NULL,
  `partido_candidato` text NOT NULL,
  `estado_candidato` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `candidatos`
--

INSERT INTO `candidatos` (`id_candidato`, `ident_candidato`, `ape1_candidato`, `ape2_candidato`, `nom1_candidato`, `nom2_candidato`, `telefono_candidato`, `email_candidato`, `dpto_candidato`, `muni_candidato`, `direccion_candidato`, `curul_candidato`, `partido_candidato`, `estado_candidato`) VALUES
(1, '7585', 'VILLALOBOS', 'CORTINA', 'OSVALDO', 'JOSE', '3023898254', 'osvicor@hotmail.com', 15, 579, 'urb san lorenzo mz j cs 34', '1', '3', 1),
(2, '7575', 'RUIZ', 'VILLALOBOS', 'OSVALDO', 'CARLOS', '333', 'mail@mail.com', 15, 569, 'carrera 66 #48-106', '2', '4', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `candidatos`
--
ALTER TABLE `candidatos`
  ADD PRIMARY KEY (`id_candidato`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `candidatos`
--
ALTER TABLE `candidatos`
  MODIFY `id_candidato` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
