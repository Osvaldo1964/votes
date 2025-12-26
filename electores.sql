-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-12-2025 a las 04:26:25
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
-- Estructura de tabla para la tabla `electores`
--

CREATE TABLE `electores` (
  `id_elector` bigint(20) NOT NULL,
  `ident_elector` varchar(10) NOT NULL,
  `ape1_elector` text NOT NULL,
  `ape2_elector` text NOT NULL,
  `nom1_elector` text NOT NULL,
  `nom2_elector` text NOT NULL,
  `sexo_elector` varchar(10) NOT NULL,
  `telefono_elector` varchar(10) NOT NULL,
  `email_elector` text NOT NULL,
  `direccion_elector` text NOT NULL,
  `lider_elector` bigint(20) NOT NULL,
  `dpto_elector` bigint(20) NOT NULL,
  `muni_elector` bigint(20) NOT NULL,
  `zona_elector` bigint(20) NOT NULL,
  `barrio_elector` bigint(20) NOT NULL,
  `poll_elector` int(1) NOT NULL DEFAULT 0,
  `estado_elector` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `electores`
--

INSERT INTO `electores` (`id_elector`, `ident_elector`, `ape1_elector`, `ape2_elector`, `nom1_elector`, `nom2_elector`, `sexo_elector`, `telefono_elector`, `email_elector`, `direccion_elector`, `lider_elector`, `dpto_elector`, `muni_elector`, `zona_elector`, `barrio_elector`, `poll_elector`, `estado_elector`) VALUES
(1, '73111404', 'VILLALOBOS', 'CORTINA', 'OSVALDO', 'JOSE', '', '3023898254', 'osvicor@hotmail.com', 'carrera 66 #48-106', 1, 15, 570, 0, 0, 1, 1),
(2, '955873', 'VILARETE', 'PEREZ', 'EDUARDO', 'EMILIO', '', '996', 'vila@vila.com', 'centro calle 1', 1, 15, 574, 0, 0, 0, 1),
(3, '1678894', 'FORNARIS', '', 'ORLANDO', 'EMILIO', '', '96365', 'forna@glglg.com', 'el barrio de la calle', 1, 15, 569, 0, 0, 0, 1),
(4, '1683940', 'OROZCO', 'LEIVA', 'TOMAS', 'VALENTIN', '', '753142', 'oroz@kdkld.com', 'la que sea', 1, 15, 569, 0, 0, 0, 1),
(5, '5003667', 'OSORIO', 'VASQUEZ', 'PROSPERO', '', '', '6241', 'kdkd@ldld.com', 'sdsdl 333', 1, 15, 569, 0, 0, 0, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `electores`
--
ALTER TABLE `electores`
  ADD PRIMARY KEY (`id_elector`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `electores`
--
ALTER TABLE `electores`
  MODIFY `id_elector` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
