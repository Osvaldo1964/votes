-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-12-2025 a las 04:26:31
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
-- Estructura de tabla para la tabla `lideres`
--

CREATE TABLE `lideres` (
  `id_lider` bigint(20) NOT NULL,
  `ident_lider` varchar(10) NOT NULL,
  `ape1_lider` text NOT NULL,
  `ape2_lider` text NOT NULL,
  `nom1_lider` text NOT NULL,
  `nom2_lider` text NOT NULL,
  `telefono_lider` varchar(10) NOT NULL,
  `email_lider` text NOT NULL,
  `dpto_lider` bigint(20) NOT NULL,
  `muni_lider` bigint(20) NOT NULL,
  `direccion_lider` text NOT NULL,
  `estado_lider` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lideres`
--

INSERT INTO `lideres` (`id_lider`, `ident_lider`, `ape1_lider`, `ape2_lider`, `nom1_lider`, `nom2_lider`, `telefono_lider`, `email_lider`, `dpto_lider`, `muni_lider`, `direccion_lider`, `estado_lider`) VALUES
(1, '5656', 'SUAREZ', 'PEREZ', 'JUAN', 'ALBERTO', '654', 'juan@gmail.com', 9, 362, 'calle 1 no. 20-22', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `lideres`
--
ALTER TABLE `lideres`
  ADD PRIMARY KEY (`id_lider`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `lideres`
--
ALTER TABLE `lideres`
  MODIFY `id_lider` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
