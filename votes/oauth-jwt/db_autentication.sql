-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-03-2023 a las 08:18:57
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
-- Base de datos: `db_autentication`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_jwt`
--

CREATE TABLE `cliente_jwt` (
  `id_clientejwt` bigint(20) NOT NULL,
  `nombres` varchar(200) COLLATE utf8mb4_spanish_ci NOT NULL,
  `apellidos` varchar(200) COLLATE utf8mb4_spanish_ci NOT NULL,
  `email` varchar(200) COLLATE utf8mb4_spanish_ci NOT NULL,
  `password` varchar(200) COLLATE utf8mb4_spanish_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `cliente_jwt`
--

INSERT INTO `cliente_jwt` (`id_clientejwt`, `nombres`, `apellidos`, `email`, `password`, `created_at`, `status`) VALUES
(1, 'Abel', 'OS', 'abel@info.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '2023-01-13 23:27:34', 1),
(2, 'Carlos', 'Gonzalo', 'carlos@info.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '2023-01-13 23:28:28', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `scope_jwt`
--

CREATE TABLE `scope_jwt` (
  `id_scope` bigint(20) NOT NULL,
  `scope` varchar(200) COLLATE utf8mb4_spanish_ci NOT NULL,
  `client_id` text COLLATE utf8mb4_spanish_ci NOT NULL,
  `key_secret` text COLLATE utf8mb4_spanish_ci NOT NULL,
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
(3, 'Sistema ventas', '774dc814218a04279ccae9d6414752440a9598cdeae78d9b8ca512a146afba16-2b9402f18218adee2563ee50aa169a60d9949b9dd687bf7b7c33620290840e78', '2b9402f18218adee2563ee50aa169a60d9949b9dd687bf7b7c33620290840e78-774dc814218a04279ccae9d6414752440a9598cdeae78d9b8ca512a146afba16', '2023-01-14 02:11:57', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `token_jwt`
--

CREATE TABLE `token_jwt` (
  `id_tokenjwt` bigint(20) NOT NULL,
  `clientejwt_id` bigint(20) NOT NULL,
  `scope_id` bigint(20) NOT NULL,
  `access_token` text COLLATE utf8mb4_spanish_ci NOT NULL,
  `expirres_in` text COLLATE utf8mb4_spanish_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `token_jwt`
--

INSERT INTO `token_jwt` (`id_tokenjwt`, `clientejwt_id`, `scope_id`, `access_token`, `expirres_in`, `created_at`, `status`) VALUES
(1, 1, 3, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6Mywic2NvcGUiOiJTaXN0ZW1hIHZlbnRhcyIsImVtYWlsIjoiYWJlbEBpbmZvLmNvbSIsImlhdCI6MTY3NDExMjA2MSwiZXhwIjoxNjc0MTE1NjYxfQ.NzSn5k3aptRe4wqfCkzshaR-hNMNe0_ABQLXi2cA-xm26q5Da3SxlMdKVE4Z9-iLPV3L4Lm5b9eeUlZpn1RJEg', '1674115661', '2023-01-19 01:07:41', 1),
(2, 1, 3, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6Mywic2NvcGUiOiJTaXN0ZW1hIHZlbnRhcyIsImVtYWlsIjoiYWJlbEBpbmZvLmNvbSIsImlhdCI6MTY3NDExMjMzNywiZXhwIjoxNjc0MTE1OTM3fQ.CxOWS-TKmTBvyAS6kBF0s254T0eigKTmNa98pYkeOJARfMMeb30R0h_H0YXYacI_Qh05So4wvodQFfptfxBl1A', '1674115937', '2023-01-19 01:12:17', 1),
(3, 1, 3, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6Mywic2NvcGUiOiJTaXN0ZW1hIHZlbnRhcyIsImVtYWlsIjoiYWJlbEBpbmZvLmNvbSIsImlhdCI6MTY3NDExMjQwMCwiZXhwIjoxNjc0MTE2MDAwfQ.rYsXpQeF9u_aP-I1DLCL1frMEkUdClmaPhiEOjI5riz8u1s5Qk5JSaAelsAcBCpY3KA7eYBdpz_YNI9d0U8XwA', '1674116000', '2023-01-19 01:13:20', 1),
(4, 1, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6MSwic2NvcGUiOiJTaXN0ZW1hIGNsaWVudGVzIiwiZW1haWwiOiJhYmVsQGluZm8uY29tIiwiaWF0IjoxNjc0MTEyNTM0LCJleHAiOjE2NzQxMTYxMzR9.91dkPST-x2B2P1Lc3IWi4SWc7BF4Ik_MXy-0VFIUI1KebCtoH92pONwzE18GjhHoh5bTBsbGb7thmu-cKckX9A', '1674116134', '2023-01-19 01:15:34', 1),
(5, 1, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6MSwic2NvcGUiOiJTaXN0ZW1hIGNsaWVudGVzIiwiZW1haWwiOiJhYmVsQGluZm8uY29tIiwiaWF0IjoxNjc0ODAwODg3LCJleHAiOjE2NzQ4MDQ0ODd9.R6w_j3rV8bNgnoQN3pxDFYErEnJxBv0mjJW_SICMA1YnIUm414wk4OVRj8FXD7sa46B-QNTPkObOfKC-YfUizQ', '1674804487', '2023-01-27 00:28:07', 1),
(6, 1, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6MSwic2NvcGUiOiJTaXN0ZW1hIGNsaWVudGVzIiwiZW1haWwiOiJhYmVsQGluZm8uY29tIiwiaWF0IjoxNjc0ODA0Mjk3LCJleHAiOjE2NzQ4MDc4OTd9.qjLoANvdOts1tFWtcEHsVR0E9hTRYeHRbt1nAqvam1ZX6x7qY7denyCepOhkgCdQapfdGl08tmCKMdQjSF_lwA', '1674807897', '2023-01-27 01:24:57', 1),
(7, 1, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6MSwic2NvcGUiOiJTaXN0ZW1hIGNsaWVudGVzIiwiZW1haWwiOiJhYmVsQGluZm8uY29tIiwiaWF0IjoxNjc0ODc4NzU3LCJleHAiOjE2NzQ4ODIzNTd9.Mpg8PGvbfUKee7AIqyK1dgvhOMgQMtlrXP7XajGNXWdVSfl7jVGlBsm5pKMQDrO6Ps4qptx_VXPj0xXFv60zag', '1674882357', '2023-01-27 22:05:57', 1),
(8, 1, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6MSwic2NvcGUiOiJTaXN0ZW1hIGNsaWVudGVzIiwiZW1haWwiOiJhYmVsQGluZm8uY29tIiwiaWF0IjoxNjc2NTA4OTU1LCJleHAiOjE2NzY1MTI1NTV9.W9BxDRJTS5jjoylox-apSF3KfEEbZ9_sW_eR43S0EJFBD2RiLiRAxR8kaqwdihPagF_8-nutpay7Ygw3wrfz-A', '1676512555', '2023-02-15 18:55:55', 1),
(9, 1, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6MSwic2NvcGUiOiJTaXN0ZW1hIGNsaWVudGVzIiwiZW1haWwiOiJhYmVsQGluZm8uY29tIiwiaWF0IjoxNjc5MTE0OTQ4LCJleHAiOjE2NzkxMTg1NDh9.lQaP3RSEx48dodnHpTpFDEPQyDsjcmavVDy9as9CO2MMzM-b80vJvCUnazrK55KyQ9X1IuSMLgheB_AzzrYCzw', '1679118548', '2023-03-17 22:49:08', 1),
(10, 1, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6MSwic2NvcGUiOiJTaXN0ZW1hIGNsaWVudGVzIiwiZW1haWwiOiJhYmVsQGluZm8uY29tIiwiaWF0IjoxNjc5MTE5MjQ4LCJleHAiOjE2NzkxMjI4NDh9.oip45sAboR3D0Vxw5cuyRR5dZCMtcAVvg3tFitVFCQWQ1XitE7I2DOtBjBTfVQXtnezfLMMONNQeaKprBAxv5Q', '1679122848', '2023-03-18 00:00:48', 1),
(11, 1, 3, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6Mywic2NvcGUiOiJTaXN0ZW1hIHZlbnRhcyIsImVtYWlsIjoiYWJlbEBpbmZvLmNvbSIsImlhdCI6MTY3OTI5MTg1OSwiZXhwIjoxNjc5Mjk1NDU5fQ.f8PuWM86wYcahiap30074n_71G4RXHoCJJpONJnHWiH7jFvaErOQQ2AttLwU5r8UyZtve42Ltr_7x2RfKSIvJA', '1679295459', '2023-03-19 23:57:39', 1),
(12, 1, 3, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6Mywic2NvcGUiOiJTaXN0ZW1hIHZlbnRhcyIsImVtYWlsIjoiYWJlbEBpbmZvLmNvbSIsImlhdCI6MTY3OTI5NTkwOCwiZXhwIjoxNjc5Mjk5NTA4fQ.3n_eCspuvPbbH30UfRXdLHTVoi0yvMFePhAw9IbHNlNQ-lPnlpTz-6zOkp2E9zj5-u0SWqAwMWs3N2IObc9xBw', '1679299508', '2023-03-20 01:05:08', 1),
(13, 1, 3, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6Mywic2NvcGUiOiJTaXN0ZW1hIHZlbnRhcyIsImVtYWlsIjoiYWJlbEBpbmZvLmNvbSIsImlhdCI6MTY3OTI5NjMxNSwiZXhwIjoxNjc5MzgyNzE1fQ.1EXZ0BUU-Jzij8hEhV_fAwKSyZ7MqrAsON_60ohZo4U6ook6uhOsQfYthCpKSk9F4iBRQtDH2r7ygSx751zpFQ', '1679382715', '2023-03-20 01:11:55', 1),
(14, 1, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6MSwic2NvcGUiOiJTaXN0ZW1hIGNsaWVudGVzIiwiZW1haWwiOiJhYmVsQGluZm8uY29tIiwiaWF0IjoxNjc5Mjk4MDI5LCJleHAiOjE2NzkzMDE2Mjl9.Hg0luAJdAxQqj41yeE1o8itkbpTd820-IEpMJ2uRZiuPinaBbbTYuI3FduulRjJQ7TA0bnwczQ34mrwdZJwulA', '1679301629', '2023-03-20 01:40:29', 1);

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
  MODIFY `id_clientejwt` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `scope_jwt`
--
ALTER TABLE `scope_jwt`
  MODIFY `id_scope` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `token_jwt`
--
ALTER TABLE `token_jwt`
  MODIFY `id_tokenjwt` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
