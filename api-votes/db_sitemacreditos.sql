-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-03-2023 a las 09:01:15
-- Versión del servidor: 10.4.25-MariaDB
-- Versión de PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `db_sitemacreditos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `idcliente` bigint(20) NOT NULL,
  `identificacion` varchar(50) COLLATE utf8mb4_spanish_ci NOT NULL,
  `nombres` varchar(200) COLLATE utf8mb4_spanish_ci NOT NULL,
  `apellidos` varchar(200) COLLATE utf8mb4_spanish_ci NOT NULL,
  `telefono` bigint(20) DEFAULT NULL,
  `email` varchar(200) COLLATE utf8mb4_spanish_ci NOT NULL,
  `direccion` varchar(200) COLLATE utf8mb4_spanish_ci NOT NULL,
  `nit` varchar(20) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `nombrefiscal` varchar(200) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `direccionfiscal` varchar(200) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `datecreated` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`idcliente`, `identificacion`, `nombres`, `apellidos`, `telefono`, `email`, `direccion`, `nit`, `nombrefiscal`, `direccionfiscal`, `datecreated`, `status`) VALUES
(1, '67868797', 'Carlos', 'Mora', 4687879, 'ejemplo@abelosh.com', 'Calzada Buena Vista', '2409111', 'Abel OSH', 'Antigua Guatemala', '2022-09-21 00:06:39', 0),
(4, '65465465', 'Abel', 'O Sh', 2425262728, 'hola@abelosh.com', 'Calzada Buena Vista', '2409111', 'Abel OSH', 'Antigua Guatemala', '2023-03-20 01:23:18', 0),
(5, '465468752', 'Roberto', 'Pérez', 545454, 'roberto@info.com', 'Calzada Buena Vista', '4155454', 'Roberto Pérez', 'Antigua Guatemala', '2023-03-20 01:27:45', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuenta`
--

CREATE TABLE `cuenta` (
  `idcuenta` bigint(20) NOT NULL,
  `clienteid` bigint(20) NOT NULL,
  `productoid` bigint(20) NOT NULL,
  `frecuenciaid` bigint(20) NOT NULL,
  `monto` decimal(10,0) NOT NULL,
  `cuotas` int(11) NOT NULL,
  `monto_cuotas` decimal(10,0) NOT NULL,
  `cargo` decimal(10,0) NOT NULL,
  `saldo` decimal(10,0) NOT NULL,
  `datecreated` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `cuenta`
--

INSERT INTO `cuenta` (`idcuenta`, `clienteid`, `productoid`, `frecuenciaid`, `monto`, `cuotas`, `monto_cuotas`, `cargo`, `saldo`, `datecreated`, `status`) VALUES
(1, 1, 2, 2, '1000', 10, '100', '1000', '1000', '2022-10-03 01:27:10', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `frecuencia`
--

CREATE TABLE `frecuencia` (
  `idfrecuencia` bigint(20) NOT NULL,
  `frecuencia` varchar(200) COLLATE utf8mb4_spanish_ci NOT NULL,
  `datecreated` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `frecuencia`
--

INSERT INTO `frecuencia` (`idfrecuencia`, `frecuencia`, `datecreated`, `status`) VALUES
(1, 'Semanal', '2022-09-29 01:38:43', 1),
(2, 'Quincenal', '2022-09-29 01:51:19', 1),
(3, 'Mensual', '2022-09-29 01:52:04', 1),
(4, 'Semestral', '2022-09-29 01:56:34', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento`
--

CREATE TABLE `movimiento` (
  `idmovimiento` bigint(20) NOT NULL,
  `cuentaid` bigint(20) NOT NULL,
  `tipomovimientoid` bigint(20) NOT NULL,
  `movimiento` int(11) DEFAULT NULL,
  `monto` decimal(10,0) NOT NULL,
  `descripcion` text COLLATE utf8mb4_spanish_ci NOT NULL,
  `datecreated` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `movimiento`
--

INSERT INTO `movimiento` (`idmovimiento`, `cuentaid`, `tipomovimientoid`, `movimiento`, `monto`, `descripcion`, `datecreated`, `status`) VALUES
(1, 1, 1, 1, '100', 'Abono mensual', '2022-10-11 00:47:07', 1),
(2, 1, 1, 1, '100', 'Abono mensual', '2022-10-11 00:48:35', 1),
(3, 1, 3, 2, '50', 'Cargo por mora', '2022-10-11 00:49:03', 1);

--
-- Disparadores `movimiento`
--
DELIMITER $$
CREATE TRIGGER `movimiento_A_I` AFTER INSERT ON `movimiento` FOR EACH ROW BEGIN
        DECLARE saldoActual DECIMAL(10,2);
        SELECT saldo into saldoActual FROM cuenta WHERE idcuenta = new.cuentaid;
        if new.movimiento = 1 then
            UPDATE cuenta SET saldo = saldoActual - new.monto WHERE idcuenta = new.cuentaid;
        else
            UPDATE cuenta SET saldo = saldoActual + new.monto WHERE idcuenta = new.cuentaid;
        end if;
    END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `idproducto` bigint(20) NOT NULL,
  `codigo` varchar(200) COLLATE utf8mb4_spanish_ci NOT NULL,
  `nombre` varchar(200) COLLATE utf8mb4_spanish_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_spanish_ci NOT NULL,
  `precio` decimal(10,0) NOT NULL,
  `datecreated` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`idproducto`, `codigo`, `nombre`, `descripcion`, `precio`, `datecreated`, `status`) VALUES
(1, '242526', 'Teclado USB', 'Teclado USB', '200', '2022-09-24 03:05:28', 1),
(2, '123456', 'Televisor LED 48 Pulgadas', 'Televisor LED 48 pulgadas', '8000', '2022-09-24 03:23:45', 1),
(3, '478547', 'Mouse USB', 'Descripción producto', '150', '2022-09-25 03:28:17', 1),
(4, '987878', 'Monitor LED 24 Pulgadas', 'Descripción monitor', '2500', '2022-09-25 03:29:28', 1),
(5, '465465456', 'Mouse USB', 'Mouse USB', '100', '2023-03-20 01:54:57', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_movimiento`
--

CREATE TABLE `tipo_movimiento` (
  `idtipomovimiento` bigint(20) NOT NULL,
  `movimiento` varchar(200) COLLATE utf8mb4_spanish_ci NOT NULL,
  `tipo_movimiento` int(11) NOT NULL,
  `descripcion` text COLLATE utf8mb4_spanish_ci NOT NULL,
  `datecreated` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `tipo_movimiento`
--

INSERT INTO `tipo_movimiento` (`idtipomovimiento`, `movimiento`, `tipo_movimiento`, `descripcion`, `datecreated`, `status`) VALUES
(1, 'Abono', 1, 'Abono recurrente', '2022-10-01 01:10:14', 1),
(2, 'Cargo', 2, 'Cargo a la cuenta', '2022-10-01 01:21:08', 1),
(3, 'Cargo Por Mora', 2, 'Cargo por mora a la cuenta', '2022-10-01 01:21:52', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` bigint(20) NOT NULL,
  `nombre` varchar(200) COLLATE utf8mb4_spanish_ci NOT NULL,
  `apellido` varchar(200) COLLATE utf8mb4_spanish_ci NOT NULL,
  `email` varchar(200) COLLATE utf8mb4_spanish_ci NOT NULL,
  `password` varchar(200) COLLATE utf8mb4_spanish_ci NOT NULL,
  `datecreated` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `apellido`, `email`, `password`, `datecreated`, `status`) VALUES
(1, 'Abel', 'OS', 'info@abelosh.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '2022-11-12 01:23:00', 1),
(2, 'María', 'Pérez', 'mary@contacto.com', 'ba7816bf8f01cfea414140de5dae2223b00361a396177a9cb410ff61f20015ad', '2022-11-12 01:29:44', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`idcliente`);

--
-- Indices de la tabla `cuenta`
--
ALTER TABLE `cuenta`
  ADD PRIMARY KEY (`idcuenta`),
  ADD KEY `clienteid` (`clienteid`),
  ADD KEY `productoid` (`productoid`),
  ADD KEY `frecuenciaid` (`frecuenciaid`);

--
-- Indices de la tabla `frecuencia`
--
ALTER TABLE `frecuencia`
  ADD PRIMARY KEY (`idfrecuencia`);

--
-- Indices de la tabla `movimiento`
--
ALTER TABLE `movimiento`
  ADD PRIMARY KEY (`idmovimiento`),
  ADD KEY `cuentaid` (`cuentaid`),
  ADD KEY `tipomovimientoid` (`tipomovimientoid`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`idproducto`);

--
-- Indices de la tabla `tipo_movimiento`
--
ALTER TABLE `tipo_movimiento`
  ADD PRIMARY KEY (`idtipomovimiento`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `idcliente` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `cuenta`
--
ALTER TABLE `cuenta`
  MODIFY `idcuenta` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `frecuencia`
--
ALTER TABLE `frecuencia`
  MODIFY `idfrecuencia` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `movimiento`
--
ALTER TABLE `movimiento`
  MODIFY `idmovimiento` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `idproducto` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tipo_movimiento`
--
ALTER TABLE `tipo_movimiento`
  MODIFY `idtipomovimiento` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cuenta`
--
ALTER TABLE `cuenta`
  ADD CONSTRAINT `cuenta_ibfk_1` FOREIGN KEY (`clienteid`) REFERENCES `cliente` (`idcliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cuenta_ibfk_2` FOREIGN KEY (`productoid`) REFERENCES `producto` (`idproducto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cuenta_ibfk_3` FOREIGN KEY (`frecuenciaid`) REFERENCES `frecuencia` (`idfrecuencia`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimiento`
--
ALTER TABLE `movimiento`
  ADD CONSTRAINT `movimiento_ibfk_1` FOREIGN KEY (`tipomovimientoid`) REFERENCES `tipo_movimiento` (`idtipomovimiento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `movimiento_ibfk_2` FOREIGN KEY (`cuentaid`) REFERENCES `cuenta` (`idcuenta`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
