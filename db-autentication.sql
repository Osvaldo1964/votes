-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-12-2025 a las 21:56:15
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
-- Base de datos: `db-autentication`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_jwt`
--

CREATE TABLE `cliente_jwt` (
  `id_clientejwt` bigint(20) NOT NULL,
  `nombres` varchar(200) NOT NULL,
  `apellidos` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `cliente_jwt`
--

INSERT INTO `cliente_jwt` (`id_clientejwt`, `nombres`, `apellidos`, `email`, `password`, `created_at`, `status`) VALUES
(1, 'Abel', 'OS', 'abel@info.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '2023-01-13 23:27:34', 1),
(2, 'Carlos', 'Gonzalo', 'carlos@info.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '2023-01-13 23:28:28', 1),
(3, 'Empresa Uno', 'Empresa Uno', 'empresauno@gmail.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '2025-12-11 08:04:02', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `scope_jwt`
--

CREATE TABLE `scope_jwt` (
  `id_scope` bigint(20) NOT NULL,
  `scope` varchar(200) NOT NULL,
  `client_id` text NOT NULL,
  `key_secret` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `clientejwt_id` bigint(20) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `scope_jwt`
--

INSERT INTO `scope_jwt` (`id_scope`, `scope`, `client_id`, `key_secret`, `created_at`, `clientejwt_id`, `status`) VALUES
(1, 'Sistema clientes', '774dc814218a04279ccae9d6414752440a9598cdeae78d9b8ca512a146afba16-24afe0f47a2af90d39bcdf4b5c0e50ed6e09ba4e3a19aac277af311e9f72698b', '24afe0f47a2af90d39bcdf4b5c0e50ed6e09ba4e3a19aac277af311e9f72698b-774dc814218a04279ccae9d6414752440a9598cdeae78d9b8ca512a146afba16', '2023-01-14 02:09:16', 1, 1),
(2, 'Sistema clientes', 'ca16da40dd8744e0ceca54ffbb10922f4c5cb1791f66d6c9981fe79d9401f872-18f1b3372bafc1ebf758e5945aee0f1e6a6eaeed541497bb6f38cdb742bff7db', '18f1b3372bafc1ebf758e5945aee0f1e6a6eaeed541497bb6f38cdb742bff7db-ca16da40dd8744e0ceca54ffbb10922f4c5cb1791f66d6c9981fe79d9401f872', '2023-01-14 02:10:41', 2, 1),
(3, 'Sistema ventas', '774dc814218a04279ccae9d6414752440a9598cdeae78d9b8ca512a146afba16-2b9402f18218adee2563ee50aa169a60d9949b9dd687bf7b7c33620290840e78', '2b9402f18218adee2563ee50aa169a60d9949b9dd687bf7b7c33620290840e78-774dc814218a04279ccae9d6414752440a9598cdeae78d9b8ca512a146afba16', '2023-01-14 02:11:57', 1, 1),
(9, 'Empresa Uno', '6cbcb6d052547e5d560e8b8bf55ea170a0ade8e96f0534d67be256c8a617204c-08d4a98d34bec82c9c12b022b4cc2a611df2eda9da2f25f85f386197bbd9321f', '08d4a98d34bec82c9c12b022b4cc2a611df2eda9da2f25f85f386197bbd9321f-6cbcb6d052547e5d560e8b8bf55ea170a0ade8e96f0534d67be256c8a617204c', '2025-12-15 07:33:51', 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `token_jwt`
--

CREATE TABLE `token_jwt` (
  `id_tokenjwt` bigint(20) NOT NULL,
  `clientejwt_id` bigint(20) NOT NULL,
  `scope_id` bigint(20) NOT NULL,
  `access_token` text NOT NULL,
  `expirres_in` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `token_jwt`
--

INSERT INTO `token_jwt` (`id_tokenjwt`, `clientejwt_id`, `scope_id`, `access_token`, `expirres_in`, `created_at`, `status`) VALUES
(17, 3, 9, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6OSwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1wcmVzYXVub0BnbWFpbC5jb20iLCJpYXQiOjE3NjU4MDQ0NjgsImV4cCI6MTc2NTg5MDg2OH0.7twme8pnuP5LYyEQLFJeAMs0a8oxfLGQI7t_rxaX5k42E_m6sT4wBBEPya4fVhG0rp9mqlmH9xhcjYCTGVjoMQ', '1765890868', '2025-12-15 08:14:28', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cliente_jwt`
--
ALTER TABLE `cliente_jwt`
  ADD PRIMARY KEY (`id_clientejwt`);

--
-- Indices de la tabla `scope_jwt`
--
ALTER TABLE `scope_jwt`
  ADD PRIMARY KEY (`id_scope`),
  ADD KEY `clientejwt_id` (`clientejwt_id`);

--
-- Indices de la tabla `token_jwt`
--
ALTER TABLE `token_jwt`
  ADD PRIMARY KEY (`id_tokenjwt`),
  ADD KEY `clientejwt_id` (`clientejwt_id`),
  ADD KEY `scope_id` (`scope_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cliente_jwt`
--
ALTER TABLE `cliente_jwt`
  MODIFY `id_clientejwt` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `scope_jwt`
--
ALTER TABLE `scope_jwt`
  MODIFY `id_scope` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `token_jwt`
--
ALTER TABLE `token_jwt`
  MODIFY `id_tokenjwt` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `scope_jwt`
--
ALTER TABLE `scope_jwt`
  ADD CONSTRAINT `scope_jwt_ibfk_1` FOREIGN KEY (`clientejwt_id`) REFERENCES `cliente_jwt` (`id_clientejwt`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `token_jwt`
--
ALTER TABLE `token_jwt`
  ADD CONSTRAINT `token_jwt_ibfk_1` FOREIGN KEY (`scope_id`) REFERENCES `scope_jwt` (`id_scope`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
