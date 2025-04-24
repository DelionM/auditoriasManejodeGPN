-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-04-2025 a las 18:51:59
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
-- Base de datos: `auditoria`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditorias`
--

CREATE TABLE `auditorias` (
  `id_auditoria` int(11) NOT NULL,
  `numero_empleado` varchar(50) DEFAULT NULL,
  `nombre_auditor` text DEFAULT NULL,
  `cliente` text DEFAULT NULL,
  `proceso_auditado` text DEFAULT NULL,
  `parte_auditada` text DEFAULT NULL,
  `operacion_auditada` text DEFAULT NULL,
  `nave` text DEFAULT NULL,
  `unidad` text DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `fecha_inicio_proceso` timestamp NULL DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `acciones` text DEFAULT NULL,
  `idProblemasUnoUno` varchar(150) DEFAULT NULL,
  `estatus` text DEFAULT NULL,
  `fecha_fila` date DEFAULT NULL,
  `nombre_archivo` text DEFAULT NULL,
  `ruta_archivo` text DEFAULT NULL,
  `fecha_subida` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesUnoDos` text DEFAULT NULL,
  `accionesUnoDos` text DEFAULT NULL,
  `idProblemasUnoDos` varchar(120) DEFAULT NULL,
  `estatusUnoDos` text DEFAULT NULL,
  `fecha_filaUnoDos` date DEFAULT NULL,
  `nombre_archivoUnoDos` text DEFAULT NULL,
  `ruta_archivoUnoDos` text DEFAULT NULL,
  `fecha_subidaUnoDos` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesUnoTres` text DEFAULT NULL,
  `accionesUnoTres` text DEFAULT NULL,
  `idProblemasUnoTres` varchar(120) DEFAULT NULL,
  `estatusUnoTres` text DEFAULT NULL,
  `fecha_filaUnoTres` date DEFAULT NULL,
  `nombre_archivoUnoTres` text DEFAULT NULL,
  `ruta_archivoUnoTres` text DEFAULT NULL,
  `fecha_subidaUnoTres` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesDosUno` text DEFAULT NULL,
  `accionesDosUno` text DEFAULT NULL,
  `idProblemasDosUno` varchar(120) DEFAULT NULL,
  `estatusDosUno` text DEFAULT NULL,
  `fecha_filaDosUno` date DEFAULT NULL,
  `nombre_archivoDosUno` text DEFAULT NULL,
  `ruta_archivoDosUno` text DEFAULT NULL,
  `fecha_subidaDosUno` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesDosDos` text DEFAULT NULL,
  `accionesDosDos` text DEFAULT NULL,
  `idProblemasDosDos` varchar(120) DEFAULT NULL,
  `estatusDosDos` text DEFAULT NULL,
  `fecha_filaDosDos` date DEFAULT NULL,
  `nombre_archivoDosDos` text DEFAULT NULL,
  `ruta_archivoDosDos` text DEFAULT NULL,
  `fecha_subidaDosDos` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesDosTres` text DEFAULT NULL,
  `accionesDosTres` text DEFAULT NULL,
  `idProblemasDosTres` varchar(120) DEFAULT NULL,
  `estatusDosTres` text DEFAULT NULL,
  `fecha_filaDosTres` date DEFAULT NULL,
  `nombre_archivoDosTres` text DEFAULT NULL,
  `ruta_archivoDosTres` text DEFAULT NULL,
  `fecha_subidaDosTres` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesDosCuatro` text DEFAULT NULL,
  `accionesDosCuatro` text DEFAULT NULL,
  `idProblemasDosCuatro` varchar(120) DEFAULT NULL,
  `estatusDosCuatro` text DEFAULT NULL,
  `fecha_filaDosCuatro` date DEFAULT NULL,
  `nombre_archivoDosCuatro` text DEFAULT NULL,
  `ruta_archivoDosCuatro` text DEFAULT NULL,
  `fecha_subidaDosCuatro` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesDosCinco` text DEFAULT NULL,
  `accionesDosCinco` text DEFAULT NULL,
  `idProblemasDosCinco` varchar(120) DEFAULT NULL,
  `estatusDosCinco` text DEFAULT NULL,
  `fecha_filaDosCinco` date DEFAULT NULL,
  `nombre_archivoDosCinco` text DEFAULT NULL,
  `ruta_archivoDosCinco` text DEFAULT NULL,
  `fecha_subidaDosCinco` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesDosSeis` text DEFAULT NULL,
  `accionesDosSeis` text DEFAULT NULL,
  `idProblemasDosSeis` varchar(120) DEFAULT NULL,
  `estatusDosSeis` text DEFAULT NULL,
  `fecha_filaDosSeis` date DEFAULT NULL,
  `nombre_archivoDosSeis` text DEFAULT NULL,
  `ruta_archivoDosSeis` text DEFAULT NULL,
  `fecha_subidaDosSeis` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesTresUno` text DEFAULT NULL,
  `accionesTresUno` text DEFAULT NULL,
  `idProblemasTresUno` varchar(120) DEFAULT NULL,
  `estatusTresUno` text DEFAULT NULL,
  `fecha_filaTresUno` date DEFAULT NULL,
  `nombre_archivoTresUno` text DEFAULT NULL,
  `ruta_archivoTresUno` text DEFAULT NULL,
  `fecha_subidaTresUno` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesCuatroUno` text DEFAULT NULL,
  `accionesCuatroUno` text DEFAULT NULL,
  `idProblemasCuatroUno` varchar(120) DEFAULT NULL,
  `estatusCuatroUno` text DEFAULT NULL,
  `fecha_filaCuatroUno` date DEFAULT NULL,
  `nombre_archivoCuatroUno` text DEFAULT NULL,
  `ruta_archivoCuatroUno` text DEFAULT NULL,
  `fecha_subidaCuatroUno` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesCuatroDos` text DEFAULT NULL,
  `accionesCuatroDos` text DEFAULT NULL,
  `idProblemasCuatroDos` varchar(120) DEFAULT NULL,
  `estatusCuatroDos` text DEFAULT NULL,
  `fecha_filaCuatroDos` date DEFAULT NULL,
  `nombre_archivoCuatroDos` text DEFAULT NULL,
  `ruta_archivoCuatroDos` text DEFAULT NULL,
  `fecha_subidaCuatroDos` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesCuatroTres` text DEFAULT NULL,
  `accionesCuatroTres` text DEFAULT NULL,
  `idProblemasCuatroTres` varchar(120) DEFAULT NULL,
  `estatusCuatroTres` text DEFAULT NULL,
  `fecha_filaCuatroTres` date DEFAULT NULL,
  `nombre_archivoCuatroTres` text DEFAULT NULL,
  `ruta_archivoCuatroTres` text DEFAULT NULL,
  `fecha_subidaCuatroTres` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesCincoUno` text DEFAULT NULL,
  `accionesCincoUno` text DEFAULT NULL,
  `idProblemasCincoUno` varchar(120) DEFAULT NULL,
  `estatusCincoUno` text DEFAULT NULL,
  `fecha_filaCincoUno` date DEFAULT NULL,
  `nombre_archivoCincoUno` text DEFAULT NULL,
  `ruta_archivoCincoUno` text DEFAULT NULL,
  `fecha_subidaCincoUno` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesCincoDos` text DEFAULT NULL,
  `accionesCincoDos` text DEFAULT NULL,
  `idProblemasCincoDos` varchar(120) DEFAULT NULL,
  `estatusCincoDos` text DEFAULT NULL,
  `fecha_filaCincoDos` date DEFAULT NULL,
  `nombre_archivoCincoDos` text DEFAULT NULL,
  `ruta_archivoCincoDos` text DEFAULT NULL,
  `fecha_subidaCincoDos` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesCincoTres` text DEFAULT NULL,
  `accionesCincoTres` text DEFAULT NULL,
  `idProblemasCincoTres` varchar(120) DEFAULT NULL,
  `estatusCincoTres` text DEFAULT NULL,
  `fecha_filaCincoTres` date DEFAULT NULL,
  `nombre_archivoCincoTres` text DEFAULT NULL,
  `ruta_archivoCincoTres` text DEFAULT NULL,
  `fecha_subidaCincoTres` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesCincoCuatro` text DEFAULT NULL,
  `accionesCincoCuatro` text DEFAULT NULL,
  `idProblemasCincoCuatro` varchar(120) DEFAULT NULL,
  `estatusCincoCuatro` text DEFAULT NULL,
  `fecha_filaCincoCuatro` date DEFAULT NULL,
  `nombre_archivoCincoCuatro` text DEFAULT NULL,
  `ruta_archivoCincoCuatro` text DEFAULT NULL,
  `fecha_subidaCincoCuatro` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesCincoCinco` text DEFAULT NULL,
  `accionesCincoCinco` text DEFAULT NULL,
  `idProblemasCincoCinco` varchar(120) DEFAULT NULL,
  `estatusCincoCinco` text DEFAULT NULL,
  `fecha_filaCincoCinco` date DEFAULT NULL,
  `nombre_archivoCincoCinco` text DEFAULT NULL,
  `ruta_archivoCincoCinco` text DEFAULT NULL,
  `fecha_subidaCincoCinco` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesCincoSeis` text DEFAULT NULL,
  `accionesCincoSeis` text DEFAULT NULL,
  `idProblemasCincoSeis` varchar(120) DEFAULT NULL,
  `estatusCincoSeis` text DEFAULT NULL,
  `fecha_filaCincoSeis` date DEFAULT NULL,
  `nombre_archivoCincoSeis` text DEFAULT NULL,
  `ruta_archivoCincoSeis` text DEFAULT NULL,
  `fecha_subidaCincoSeis` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesCincoSiete` text DEFAULT NULL,
  `accionesCincoSiete` text DEFAULT NULL,
  `idProblemasCincoSiete` varchar(120) DEFAULT NULL,
  `estatusCincoSiete` text DEFAULT NULL,
  `fecha_filaCincoSiete` date DEFAULT NULL,
  `nombre_archivoCincoSiete` text DEFAULT NULL,
  `ruta_archivoCincoSiete` text DEFAULT NULL,
  `fecha_subidaCincoSiete` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesCincoOcho` text DEFAULT NULL,
  `accionesCincoOcho` text DEFAULT NULL,
  `idProblemasCincoOcho` varchar(120) DEFAULT NULL,
  `estatusCincoOcho` text DEFAULT NULL,
  `fecha_filaCincoOcho` date DEFAULT NULL,
  `nombre_archivoCincoOcho` text DEFAULT NULL,
  `ruta_archivoCincoOcho` text DEFAULT NULL,
  `fecha_subidaCincoOcho` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesSeisUno` text DEFAULT NULL,
  `accionesSeisUno` text DEFAULT NULL,
  `idProblemasSeisUno` varchar(120) DEFAULT NULL,
  `estatusSeisUno` text DEFAULT NULL,
  `fecha_filaSeisUno` date DEFAULT NULL,
  `nombre_archivoSeisUno` text DEFAULT NULL,
  `ruta_archivoSeisUno` text DEFAULT NULL,
  `fecha_subidaSeisUno` timestamp NOT NULL DEFAULT current_timestamp(),
  `estatus_cierre` enum('Abierto','Cerrado') DEFAULT 'Abierto',
  `idNombreAuditor2` varchar(80) DEFAULT NULL,
  `idNombreSupervisor` varchar(80) DEFAULT NULL,
  `idNombreOperador` varchar(80) DEFAULT NULL,
  `fecha_cierre` varchar(20) DEFAULT NULL,
  `tuvo_nok` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditorias`
--

INSERT INTO `auditorias` (`id_auditoria`, `numero_empleado`, `nombre_auditor`, `cliente`, `proceso_auditado`, `parte_auditada`, `operacion_auditada`, `nave`, `unidad`, `fecha`, `fecha_inicio_proceso`, `observaciones`, `acciones`, `idProblemasUnoUno`, `estatus`, `fecha_fila`, `nombre_archivo`, `ruta_archivo`, `fecha_subida`, `observacionesUnoDos`, `accionesUnoDos`, `idProblemasUnoDos`, `estatusUnoDos`, `fecha_filaUnoDos`, `nombre_archivoUnoDos`, `ruta_archivoUnoDos`, `fecha_subidaUnoDos`, `observacionesUnoTres`, `accionesUnoTres`, `idProblemasUnoTres`, `estatusUnoTres`, `fecha_filaUnoTres`, `nombre_archivoUnoTres`, `ruta_archivoUnoTres`, `fecha_subidaUnoTres`, `observacionesDosUno`, `accionesDosUno`, `idProblemasDosUno`, `estatusDosUno`, `fecha_filaDosUno`, `nombre_archivoDosUno`, `ruta_archivoDosUno`, `fecha_subidaDosUno`, `observacionesDosDos`, `accionesDosDos`, `idProblemasDosDos`, `estatusDosDos`, `fecha_filaDosDos`, `nombre_archivoDosDos`, `ruta_archivoDosDos`, `fecha_subidaDosDos`, `observacionesDosTres`, `accionesDosTres`, `idProblemasDosTres`, `estatusDosTres`, `fecha_filaDosTres`, `nombre_archivoDosTres`, `ruta_archivoDosTres`, `fecha_subidaDosTres`, `observacionesDosCuatro`, `accionesDosCuatro`, `idProblemasDosCuatro`, `estatusDosCuatro`, `fecha_filaDosCuatro`, `nombre_archivoDosCuatro`, `ruta_archivoDosCuatro`, `fecha_subidaDosCuatro`, `observacionesDosCinco`, `accionesDosCinco`, `idProblemasDosCinco`, `estatusDosCinco`, `fecha_filaDosCinco`, `nombre_archivoDosCinco`, `ruta_archivoDosCinco`, `fecha_subidaDosCinco`, `observacionesDosSeis`, `accionesDosSeis`, `idProblemasDosSeis`, `estatusDosSeis`, `fecha_filaDosSeis`, `nombre_archivoDosSeis`, `ruta_archivoDosSeis`, `fecha_subidaDosSeis`, `observacionesTresUno`, `accionesTresUno`, `idProblemasTresUno`, `estatusTresUno`, `fecha_filaTresUno`, `nombre_archivoTresUno`, `ruta_archivoTresUno`, `fecha_subidaTresUno`, `observacionesCuatroUno`, `accionesCuatroUno`, `idProblemasCuatroUno`, `estatusCuatroUno`, `fecha_filaCuatroUno`, `nombre_archivoCuatroUno`, `ruta_archivoCuatroUno`, `fecha_subidaCuatroUno`, `observacionesCuatroDos`, `accionesCuatroDos`, `idProblemasCuatroDos`, `estatusCuatroDos`, `fecha_filaCuatroDos`, `nombre_archivoCuatroDos`, `ruta_archivoCuatroDos`, `fecha_subidaCuatroDos`, `observacionesCuatroTres`, `accionesCuatroTres`, `idProblemasCuatroTres`, `estatusCuatroTres`, `fecha_filaCuatroTres`, `nombre_archivoCuatroTres`, `ruta_archivoCuatroTres`, `fecha_subidaCuatroTres`, `observacionesCincoUno`, `accionesCincoUno`, `idProblemasCincoUno`, `estatusCincoUno`, `fecha_filaCincoUno`, `nombre_archivoCincoUno`, `ruta_archivoCincoUno`, `fecha_subidaCincoUno`, `observacionesCincoDos`, `accionesCincoDos`, `idProblemasCincoDos`, `estatusCincoDos`, `fecha_filaCincoDos`, `nombre_archivoCincoDos`, `ruta_archivoCincoDos`, `fecha_subidaCincoDos`, `observacionesCincoTres`, `accionesCincoTres`, `idProblemasCincoTres`, `estatusCincoTres`, `fecha_filaCincoTres`, `nombre_archivoCincoTres`, `ruta_archivoCincoTres`, `fecha_subidaCincoTres`, `observacionesCincoCuatro`, `accionesCincoCuatro`, `idProblemasCincoCuatro`, `estatusCincoCuatro`, `fecha_filaCincoCuatro`, `nombre_archivoCincoCuatro`, `ruta_archivoCincoCuatro`, `fecha_subidaCincoCuatro`, `observacionesCincoCinco`, `accionesCincoCinco`, `idProblemasCincoCinco`, `estatusCincoCinco`, `fecha_filaCincoCinco`, `nombre_archivoCincoCinco`, `ruta_archivoCincoCinco`, `fecha_subidaCincoCinco`, `observacionesCincoSeis`, `accionesCincoSeis`, `idProblemasCincoSeis`, `estatusCincoSeis`, `fecha_filaCincoSeis`, `nombre_archivoCincoSeis`, `ruta_archivoCincoSeis`, `fecha_subidaCincoSeis`, `observacionesCincoSiete`, `accionesCincoSiete`, `idProblemasCincoSiete`, `estatusCincoSiete`, `fecha_filaCincoSiete`, `nombre_archivoCincoSiete`, `ruta_archivoCincoSiete`, `fecha_subidaCincoSiete`, `observacionesCincoOcho`, `accionesCincoOcho`, `idProblemasCincoOcho`, `estatusCincoOcho`, `fecha_filaCincoOcho`, `nombre_archivoCincoOcho`, `ruta_archivoCincoOcho`, `fecha_subidaCincoOcho`, `observacionesSeisUno`, `accionesSeisUno`, `idProblemasSeisUno`, `estatusSeisUno`, `fecha_filaSeisUno`, `nombre_archivoSeisUno`, `ruta_archivoSeisUno`, `fecha_subidaSeisUno`, `estatus_cierre`, `idNombreAuditor2`, `idNombreSupervisor`, `idNombreOperador`, `fecha_cierre`, `tuvo_nok`) VALUES
(208, '1093', 'Delion', 'Audi Mexico, S.A. de C.V.', 'Partes Plásticas', 'fdefdssdffd', 'Partes Plásticas', 'Nave 3', 'Unidad 2', '2025-04-09', '2025-04-22 21:15:54', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-22 21:15:54', 'fedsa', 'dfs', 'Liberación de primera pieza', 'NOK', '2025-04-05', 'Capturadepantalla2024-06-12155957_1745356573.png', 'uploads/Capturadepantalla2024-06-12155957_1745356573.png', '2025-04-22 21:16:13', '', '', '', 'NOK', '0000-00-00', NULL, NULL, '2025-04-22 21:15:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 21:15:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 21:15:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 21:15:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 21:15:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 21:15:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 21:15:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 21:15:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 21:15:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 21:15:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 21:15:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 21:15:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 21:15:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 21:15:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 21:15:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 21:15:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 21:15:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 21:15:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 21:15:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 21:15:54', 'Abierto', NULL, NULL, NULL, NULL, 1),
(210, '1093', 'Delion', 'Audi Mexico, S.A. de C.V.', 'Partes Plásticas', 'zzzzzzzzzzzzzzzzzzzz', 'Partes Plásticas', 'Nave 3', 'Unidad 2', '2025-04-17', '2025-04-23 14:08:29', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 14:08:29', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 14:08:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:29', 'Cerrado', NULL, NULL, NULL, NULL, 0),
(211, '1093', 'Delion', 'Audi Mexico, S.A. de C.V.', 'Partes Plásticas', 'saasa', 'Partes Plásticas', 'Nave 2', 'Unidad 2', '2025-04-22', '2025-04-23 14:07:18', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 14:07:47', '', '', '', 'N/A', '0000-00-00', NULL, NULL, '2025-04-23 14:07:52', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 14:07:55', '', '', '', 'N/A', '0000-00-00', NULL, NULL, '2025-04-23 14:07:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:07:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:07:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:07:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:07:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:07:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:07:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:07:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:07:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:07:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:07:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:07:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:07:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:07:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:07:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:07:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:07:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:07:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:07:18', 'Cerrado', 'Delion', 'aaaaaaaaaaa', 'aaaaaaaa', '2025-04-23 08:11:45', 0),
(219, '1093', 'Delion', 'Mazda Motor Manufacturing de Mexico', 'Insulators', 'saassadsdasda', 'Insulators', 'Nave 9', 'Unidad 2', '2025-04-07', '2025-04-23 14:08:47', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 14:08:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:08:47', 'Abierto', NULL, NULL, NULL, NULL, 0),
(223, '1093', 'Delion', 'Mazda Motor Manufacturing de Mexico', 'Asfaltos', ' DG7V56151', 'Asfaltos', 'Nave 8', 'Unidad 2', '2025-04-23', '2025-04-23 14:25:59', 'xsssssssssssssss', 'aaaaaaaaaaaaa', 'Matriz de EPP', 'NOK', '2025-04-14', 'c_1745418360.png', 'uploads/c_1745418360.png', '2025-04-23 14:26:00', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 14:26:02', 'aaaaaaaaa', 'QQQQQQQED', 'Matriz de EPP', 'NOK', '2025-04-11', 'c_1745418378.png', 'uploads/c_1745418378.png', '2025-04-23 14:26:18', 'DQ', 'QW', 'Matriz de Habilidades', 'NOK', '2025-04-09', 'Capturadepantalla2024-06-24094749_1745418391.png', 'uploads/Capturadepantalla2024-06-24094749_1745418391.png', '2025-04-23 14:26:31', 'DW', 'WTR', 'Matriz de EPP', 'NOK', '2025-04-02', 'c_1745418403.png', 'uploads/c_1745418403.png', '2025-04-23 14:26:43', 'RT', 'TG', 'Matriz de EPP', 'NOK', '2025-04-05', 'Capturadepantalla2024-06-25133120_1745418464.png', 'uploads/Capturadepantalla2024-06-25133120_1745418464.png', '2025-04-23 14:27:44', 'DWQ', 'QDW', 'sss', 'NOK', '2025-04-04', 'baseDatos1_1745418489.png', 'uploads/baseDatos1_1745418489.png', '2025-04-23 14:28:09', 'WQD', 'WQ', 'Dispositivo de control / Instructivo /etiqueta de verificacion', 'NOK', '2025-04-14', 'Capturadepantalla2024-06-20163715_1745418503.png', 'uploads/Capturadepantalla2024-06-20163715_1745418503.png', '2025-04-23 14:28:23', 'DW', 'FR', 'Norma de empaque', 'NOK', '2025-04-15', 'Capturadepantalla2024-06-24094435_1745418519.png', 'uploads/Capturadepantalla2024-06-24094435_1745418519.png', '2025-04-23 14:28:39', 'RF', 'RE', 'Hoja de procesos', 'NOK', '2025-04-10', 'Capturadepantalla2024-06-24094749_1745418533.png', 'uploads/Capturadepantalla2024-06-24094749_1745418533.png', '2025-04-23 14:28:53', 'FE', 'DE', 'Norma de empaque', 'NOK', '2025-04-11', 'c_1745418548.png', 'uploads/c_1745418548.png', '2025-04-23 14:29:08', 'AD', 'AD', 'Norma de empaque', 'NOK', '2025-04-21', 'Capturadepantalla2024-06-24094403_1745418565.png', 'uploads/Capturadepantalla2024-06-24094403_1745418565.png', '2025-04-23 14:29:25', 'C', 'C', 'Hoja de procesos', 'NOK', '2025-05-01', 'Capturadepantalla2024-06-25133120_1745418581.png', 'uploads/Capturadepantalla2024-06-25133120_1745418581.png', '2025-04-23 14:25:59', 'CD', 'CD', 'Lay-Out', 'NOK', '2025-04-09', 'Capturadepantalla2024-06-27110602_1745418594.png', 'uploads/Capturadepantalla2024-06-27110602_1745418594.png', '2025-04-23 14:29:54', 'CD', 'CD', 'Lay-Out', 'NOK', '2025-04-24', 'Capturadepantalla2024-06-24100926_1745418612.png', 'uploads/Capturadepantalla2024-06-24100926_1745418612.png', '2025-04-23 14:30:12', 'CD', 'CD', 'Dispositivo de control / Instructivo /etiqueta de verificacion', 'NOK', '2025-04-16', 'Capturadepantalla2024-06-25111216_1745418624.png', 'uploads/Capturadepantalla2024-06-25111216_1745418624.png', '2025-04-23 14:30:24', 'CD', 'CDC', 'Plan de Control', 'NOK', '2025-05-03', 'Capturadepantalla2024-06-14110936_1745418639.png', 'uploads/Capturadepantalla2024-06-14110936_1745418639.png', '2025-04-23 14:30:39', 'DC', 'CD', 'Dispositivo de control / Instructivo /etiqueta de verificacion', 'NOK', '2025-04-01', 'Capturadepantalla2024-07-11144106_1745418655.png', 'uploads/Capturadepantalla2024-07-11144106_1745418655.png', '2025-04-23 14:30:55', 'CD', 'S', 'Dispositivo de control / Instructivo /etiqueta de verificacion', 'NOK', '2025-04-03', 'Capturadepantalla2024-06-17145422_1745418670.png', 'uploads/Capturadepantalla2024-06-17145422_1745418670.png', '2025-04-23 14:31:10', 'CD', 'HRT', 'Dispositivo de control / Instructivo /etiqueta de verificacion', 'NOK', '2025-04-10', 'Capturadepantalla2024-06-24085329_1745418684.png', 'uploads/Capturadepantalla2024-06-24085329_1745418684.png', '2025-04-23 14:31:25', 'TGH', 'TR', 'Registro de parametros', 'NOK', '2025-04-25', 'Capturadepantalla2024-06-24105536_1745418700.png', 'uploads/Capturadepantalla2024-06-24105536_1745418700.png', '2025-04-23 14:31:40', 'SSSSSSSSSSSS3', '33333', 'Catalogo de NO conformidades', 'NOK', '2025-04-22', 'Capturadepantalla2024-06-25133608_1745418722.png', 'uploads/Capturadepantalla2024-06-25133608_1745418722.png', '2025-04-23 14:32:02', 'Abierto', NULL, NULL, NULL, NULL, 1),
(224, '1093', 'Delion', 'PROMA AUTOMOTIVE DE MEXICO', 'Respaldo', ' 36185', 'Respaldo', 'Nave 9', 'Unidad 3', '2025-04-22', '2025-04-23 16:19:59', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:19:59', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:20:01', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:20:12', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:20:16', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:20:19', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:20:29', '', '', NULL, 'OK', NULL, NULL, NULL, '2025-04-23 16:20:31', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:20:35', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:20:37', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:20:39', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:20:44', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:20:47', '', '', '', 'N/A', '0000-00-00', NULL, NULL, '2025-04-23 16:19:59', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:20:52', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:20:53', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:20:56', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:20:58', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:21:02', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:21:04', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:21:06', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:21:08', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:21:10', 'Cerrado', 'Delion', 'ddddddddddddd', 'dddddddddddddd', '2025-04-23 10:22:33', 0);

--
-- Disparadores `auditorias`
--
DELIMITER $$
CREATE TRIGGER `update_tuvo_nok_auditorias` BEFORE UPDATE ON `auditorias` FOR EACH ROW BEGIN
    IF NEW.estatus = 'NOK' OR 
       NEW.estatusUnoDos = 'NOK' OR 
       NEW.estatusUnoTres = 'NOK' OR 
       NEW.estatusDosUno = 'NOK' OR 
       NEW.estatusDosDos = 'NOK' OR 
       NEW.estatusDosTres = 'NOK' OR 
       NEW.estatusDosCuatro = 'NOK' OR 
       NEW.estatusDosCinco = 'NOK' OR 
       NEW.estatusDosSeis = 'NOK' OR 
       NEW.estatusTresUno = 'NOK' OR 
       NEW.estatusCuatroUno = 'NOK' OR 
       NEW.estatusCuatroDos = 'NOK' OR 
       NEW.estatusCuatroTres = 'NOK' OR 
       NEW.estatusCincoUno = 'NOK' OR 
       NEW.estatusCincoDos = 'NOK' OR 
       NEW.estatusCincoTres = 'NOK' OR 
       NEW.estatusCincoCuatro = 'NOK' OR 
       NEW.estatusCincoCinco = 'NOK' OR 
       NEW.estatusCincoSeis = 'NOK' OR 
       NEW.estatusCincoSiete = 'NOK' OR 
       NEW.estatusCincoOcho = 'NOK' OR 
       NEW.estatusSeisUno = 'NOK' THEN
        SET NEW.tuvo_nok = 1;
    ELSE
        SET NEW.tuvo_nok = OLD.tuvo_nok; -- Mantiene el valor anterior si no hay NOK
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_proceso`
--

CREATE TABLE `auditoria_proceso` (
  `id` int(11) NOT NULL,
  `id_auditoria` int(11) NOT NULL,
  `numero_empleado` varchar(50) DEFAULT NULL,
  `nombre_auditor` text DEFAULT NULL,
  `cliente` text DEFAULT NULL,
  `proceso_auditado` text DEFAULT NULL,
  `parte_auditada` text DEFAULT NULL,
  `operacion_auditada` varchar(50) DEFAULT NULL,
  `nivelIngenieria` text DEFAULT NULL,
  `nave` text DEFAULT NULL,
  `supervisor` text DEFAULT NULL,
  `unidad` text DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `fecha_inicio_proceso` timestamp NULL DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `acciones` text DEFAULT NULL,
  `idProblemasUno` varchar(120) DEFAULT NULL,
  `estatusUno` text DEFAULT NULL,
  `fecha_filaUno` date DEFAULT NULL,
  `nombre_archivoUno` text DEFAULT NULL,
  `ruta_archivoUno` text DEFAULT NULL,
  `fecha_subidaUno` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesDos` text DEFAULT NULL,
  `accionesDos` text DEFAULT NULL,
  `idProblemasDos` varchar(120) DEFAULT NULL,
  `estatusDos` text DEFAULT NULL,
  `fecha_filaDos` date DEFAULT NULL,
  `nombre_archivoDos` text DEFAULT NULL,
  `ruta_archivoDos` text DEFAULT NULL,
  `fecha_subidaDos` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesTres` text DEFAULT NULL,
  `accionesTres` text DEFAULT NULL,
  `idProblemasTres` varchar(120) DEFAULT NULL,
  `estatusTres` text DEFAULT NULL,
  `fecha_filaTres` date DEFAULT NULL,
  `nombre_archivoTres` text DEFAULT NULL,
  `ruta_archivoTres` text DEFAULT NULL,
  `fecha_subidaTres` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesCuatro` text DEFAULT NULL,
  `accionesCuatro` text DEFAULT NULL,
  `idProblemasCuatro` varchar(120) DEFAULT NULL,
  `estatusCuatro` text DEFAULT NULL,
  `fecha_filaCuatro` date DEFAULT NULL,
  `nombre_archivoCuatro` text DEFAULT NULL,
  `ruta_archivoCuatro` text DEFAULT NULL,
  `fecha_subidaCuatro` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesCinco` text DEFAULT NULL,
  `accionesCinco` text DEFAULT NULL,
  `idProblemasCinco` varchar(120) DEFAULT NULL,
  `estatusCinco` text DEFAULT NULL,
  `fecha_filaCinco` date DEFAULT NULL,
  `nombre_archivoCinco` text DEFAULT NULL,
  `ruta_archivoCinco` text DEFAULT NULL,
  `fecha_subidaCinco` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesSeis` text DEFAULT NULL,
  `accionesSeis` text DEFAULT NULL,
  `idProblemasSeis` varchar(120) DEFAULT NULL,
  `estatusSeis` text DEFAULT NULL,
  `fecha_filaSeis` date DEFAULT NULL,
  `nombre_archivoSeis` text DEFAULT NULL,
  `ruta_archivoSeis` text DEFAULT NULL,
  `fecha_subidaSeis` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesSiete` text DEFAULT NULL,
  `accionesSiete` text DEFAULT NULL,
  `idProblemasSiete` varchar(120) DEFAULT NULL,
  `estatusSiete` text DEFAULT NULL,
  `fecha_filaSiete` date DEFAULT NULL,
  `nombre_archivoSiete` text DEFAULT NULL,
  `ruta_archivoSiete` text DEFAULT NULL,
  `fecha_subidaSiete` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesOcho` text DEFAULT NULL,
  `accionesOcho` text DEFAULT NULL,
  `idProblemasOcho` varchar(120) DEFAULT NULL,
  `estatusOcho` text DEFAULT NULL,
  `fecha_filaOcho` date DEFAULT NULL,
  `nombre_archivoOcho` text DEFAULT NULL,
  `ruta_archivoOcho` text DEFAULT NULL,
  `fecha_subidaOcho` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesNueve` text DEFAULT NULL,
  `accionesNueve` text DEFAULT NULL,
  `idProblemasNueve` varchar(120) DEFAULT NULL,
  `estatusNueve` text DEFAULT NULL,
  `fecha_filaNueve` date DEFAULT NULL,
  `nombre_archivoNueve` text DEFAULT NULL,
  `ruta_archivoNueve` text DEFAULT NULL,
  `fecha_subidaNueve` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesDiez` text DEFAULT NULL,
  `accionesDiez` text DEFAULT NULL,
  `idProblemasDiez` varchar(120) DEFAULT NULL,
  `estatusDiez` text DEFAULT NULL,
  `fecha_filaDiez` date DEFAULT NULL,
  `nombre_archivoDiez` text DEFAULT NULL,
  `ruta_archivoDiez` text DEFAULT NULL,
  `fecha_subidaDiez` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesOnce` text DEFAULT NULL,
  `accionesOnce` text DEFAULT NULL,
  `idProblemasOnce` varchar(120) DEFAULT NULL,
  `estatusOnce` text DEFAULT NULL,
  `fecha_filaOnce` date DEFAULT NULL,
  `nombre_archivoOnce` text DEFAULT NULL,
  `ruta_archivoOnce` text DEFAULT NULL,
  `fecha_subidaOnce` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesDoce` text DEFAULT NULL,
  `accionesDoce` text DEFAULT NULL,
  `idProblemasDoce` varchar(120) DEFAULT NULL,
  `estatusDoce` text DEFAULT NULL,
  `fecha_filaDoce` date DEFAULT NULL,
  `nombre_archivoDoce` text DEFAULT NULL,
  `ruta_archivoDoce` text DEFAULT NULL,
  `fecha_subidaDoce` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesTrece` text DEFAULT NULL,
  `accionesTrece` text DEFAULT NULL,
  `idProblemasTrece` varchar(120) DEFAULT NULL,
  `estatusTrece` text DEFAULT NULL,
  `fecha_filaTrece` date DEFAULT NULL,
  `nombre_archivoTrece` text DEFAULT NULL,
  `ruta_archivoTrece` text DEFAULT NULL,
  `fecha_subidaTrece` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesCatorce` text DEFAULT NULL,
  `accionesCatorce` text DEFAULT NULL,
  `idProblemasCatorce` varchar(120) DEFAULT NULL,
  `estatusCatorce` text DEFAULT NULL,
  `fecha_filaCatorce` date DEFAULT NULL,
  `nombre_archivoCatorce` text DEFAULT NULL,
  `ruta_archivoCatorce` text DEFAULT NULL,
  `fecha_subidaCatorce` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesQuince` text DEFAULT NULL,
  `accionesQuince` text DEFAULT NULL,
  `idProblemasQuince` varchar(120) DEFAULT NULL,
  `estatusQuince` text DEFAULT NULL,
  `fecha_filaQuince` date DEFAULT NULL,
  `nombre_archivoQuince` text DEFAULT NULL,
  `ruta_archivoQuince` text DEFAULT NULL,
  `fecha_subidaQuince` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesDieciseis` text DEFAULT NULL,
  `accionesDieciseis` text DEFAULT NULL,
  `idProblemasDieciseis` varchar(120) DEFAULT NULL,
  `estatusDieciseis` text DEFAULT NULL,
  `fecha_filaDieciseis` date DEFAULT NULL,
  `nombre_archivoDieciseis` text DEFAULT NULL,
  `ruta_archivoDieciseis` text DEFAULT NULL,
  `fecha_subidaDieciseis` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesDiecisiete` text DEFAULT NULL,
  `accionesDiecisiete` text DEFAULT NULL,
  `idProblemasDiecisiete` varchar(120) DEFAULT NULL,
  `estatusDiecisiete` text DEFAULT NULL,
  `fecha_filaDiecisiete` date DEFAULT NULL,
  `nombre_archivoDiecisiete` text DEFAULT NULL,
  `ruta_archivoDiecisiete` text DEFAULT NULL,
  `fecha_subidaDiecisiete` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesDieciocho` text DEFAULT NULL,
  `accionesDieciocho` text DEFAULT NULL,
  `idProblemasDieciocho` varchar(120) DEFAULT NULL,
  `estatusDieciocho` text DEFAULT NULL,
  `fecha_filaDieciocho` date DEFAULT NULL,
  `nombre_archivoDieciocho` text DEFAULT NULL,
  `ruta_archivoDieciocho` text DEFAULT NULL,
  `fecha_subidaDieciocho` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesDiecinueve` text DEFAULT NULL,
  `accionesDiecinueve` text DEFAULT NULL,
  `idProblemasDiecinueve` varchar(120) DEFAULT NULL,
  `estatusDiecinueve` text DEFAULT NULL,
  `fecha_filaDiecinueve` date DEFAULT NULL,
  `nombre_archivoDiecinueve` text DEFAULT NULL,
  `ruta_archivoDiecinueve` text DEFAULT NULL,
  `fecha_subidaDiecinueve` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesVeinte` text DEFAULT NULL,
  `accionesVeinte` text DEFAULT NULL,
  `idProblemasVeinte` varchar(120) DEFAULT NULL,
  `estatusVeinte` text DEFAULT NULL,
  `fecha_filaVeinte` date DEFAULT NULL,
  `nombre_archivoVeinte` text DEFAULT NULL,
  `ruta_archivoVeinte` text DEFAULT NULL,
  `fecha_subidaVeinte` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesVeintiuno` text DEFAULT NULL,
  `accionesVeintiuno` text DEFAULT NULL,
  `idProblemasVeintiuno` varchar(120) DEFAULT NULL,
  `estatusVeintiuno` text DEFAULT NULL,
  `fecha_filaVeintiuno` date DEFAULT NULL,
  `nombre_archivoVeintiuno` text DEFAULT NULL,
  `ruta_archivoVeintiuno` text DEFAULT NULL,
  `fecha_subidaVeintiuno` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesVeintidos` text DEFAULT NULL,
  `accionesVeintidos` text DEFAULT NULL,
  `idProblemasVeintidos` varchar(120) DEFAULT NULL,
  `estatusVeintidos` text DEFAULT NULL,
  `fecha_filaVeintidos` date DEFAULT NULL,
  `nombre_archivoVeintidos` text DEFAULT NULL,
  `ruta_archivoVeintidos` text DEFAULT NULL,
  `fecha_subidaVeintidos` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesVeintitres` text DEFAULT NULL,
  `accionesVeintitres` text DEFAULT NULL,
  `idProblemasVeintitres` varchar(120) DEFAULT NULL,
  `estatusVeintitres` text DEFAULT NULL,
  `fecha_filaVeintitres` date DEFAULT NULL,
  `nombre_archivoVeintitres` text DEFAULT NULL,
  `ruta_archivoVeintitres` text DEFAULT NULL,
  `fecha_subidaVeintitres` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesVeinticuatro` text DEFAULT NULL,
  `accionesVeinticuatro` text DEFAULT NULL,
  `idProblemasVeinticuatro` varchar(120) DEFAULT NULL,
  `estatusVeinticuatro` text DEFAULT NULL,
  `fecha_filaVeinticuatro` date DEFAULT NULL,
  `nombre_archivoVeinticuatro` text DEFAULT NULL,
  `ruta_archivoVeinticuatro` text DEFAULT NULL,
  `fecha_subidaVeinticuatro` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacionesVeinticinco` text DEFAULT NULL,
  `accionesVeinticinco` text DEFAULT NULL,
  `idProblemasVeinticinco` varchar(120) DEFAULT NULL,
  `estatusVeinticinco` text DEFAULT NULL,
  `fecha_filaVeinticinco` date DEFAULT NULL,
  `nombre_archivoVeinticinco` text DEFAULT NULL,
  `ruta_archivoVeinticinco` text DEFAULT NULL,
  `fecha_subidaVeinticinco` timestamp NOT NULL DEFAULT current_timestamp(),
  `estatus_cierre` enum('Abierto','Cerrado') DEFAULT 'Abierto',
  `idNombreAuditor2` varchar(80) DEFAULT NULL,
  `idNombreSupervisor` varchar(80) DEFAULT NULL,
  `idNombreOperador` varchar(80) DEFAULT NULL,
  `fecha_cierre` varchar(20) DEFAULT NULL,
  `tuvo_nok` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditoria_proceso`
--

INSERT INTO `auditoria_proceso` (`id`, `id_auditoria`, `numero_empleado`, `nombre_auditor`, `cliente`, `proceso_auditado`, `parte_auditada`, `operacion_auditada`, `nivelIngenieria`, `nave`, `supervisor`, `unidad`, `fecha`, `fecha_inicio_proceso`, `observaciones`, `acciones`, `idProblemasUno`, `estatusUno`, `fecha_filaUno`, `nombre_archivoUno`, `ruta_archivoUno`, `fecha_subidaUno`, `observacionesDos`, `accionesDos`, `idProblemasDos`, `estatusDos`, `fecha_filaDos`, `nombre_archivoDos`, `ruta_archivoDos`, `fecha_subidaDos`, `observacionesTres`, `accionesTres`, `idProblemasTres`, `estatusTres`, `fecha_filaTres`, `nombre_archivoTres`, `ruta_archivoTres`, `fecha_subidaTres`, `observacionesCuatro`, `accionesCuatro`, `idProblemasCuatro`, `estatusCuatro`, `fecha_filaCuatro`, `nombre_archivoCuatro`, `ruta_archivoCuatro`, `fecha_subidaCuatro`, `observacionesCinco`, `accionesCinco`, `idProblemasCinco`, `estatusCinco`, `fecha_filaCinco`, `nombre_archivoCinco`, `ruta_archivoCinco`, `fecha_subidaCinco`, `observacionesSeis`, `accionesSeis`, `idProblemasSeis`, `estatusSeis`, `fecha_filaSeis`, `nombre_archivoSeis`, `ruta_archivoSeis`, `fecha_subidaSeis`, `observacionesSiete`, `accionesSiete`, `idProblemasSiete`, `estatusSiete`, `fecha_filaSiete`, `nombre_archivoSiete`, `ruta_archivoSiete`, `fecha_subidaSiete`, `observacionesOcho`, `accionesOcho`, `idProblemasOcho`, `estatusOcho`, `fecha_filaOcho`, `nombre_archivoOcho`, `ruta_archivoOcho`, `fecha_subidaOcho`, `observacionesNueve`, `accionesNueve`, `idProblemasNueve`, `estatusNueve`, `fecha_filaNueve`, `nombre_archivoNueve`, `ruta_archivoNueve`, `fecha_subidaNueve`, `observacionesDiez`, `accionesDiez`, `idProblemasDiez`, `estatusDiez`, `fecha_filaDiez`, `nombre_archivoDiez`, `ruta_archivoDiez`, `fecha_subidaDiez`, `observacionesOnce`, `accionesOnce`, `idProblemasOnce`, `estatusOnce`, `fecha_filaOnce`, `nombre_archivoOnce`, `ruta_archivoOnce`, `fecha_subidaOnce`, `observacionesDoce`, `accionesDoce`, `idProblemasDoce`, `estatusDoce`, `fecha_filaDoce`, `nombre_archivoDoce`, `ruta_archivoDoce`, `fecha_subidaDoce`, `observacionesTrece`, `accionesTrece`, `idProblemasTrece`, `estatusTrece`, `fecha_filaTrece`, `nombre_archivoTrece`, `ruta_archivoTrece`, `fecha_subidaTrece`, `observacionesCatorce`, `accionesCatorce`, `idProblemasCatorce`, `estatusCatorce`, `fecha_filaCatorce`, `nombre_archivoCatorce`, `ruta_archivoCatorce`, `fecha_subidaCatorce`, `observacionesQuince`, `accionesQuince`, `idProblemasQuince`, `estatusQuince`, `fecha_filaQuince`, `nombre_archivoQuince`, `ruta_archivoQuince`, `fecha_subidaQuince`, `observacionesDieciseis`, `accionesDieciseis`, `idProblemasDieciseis`, `estatusDieciseis`, `fecha_filaDieciseis`, `nombre_archivoDieciseis`, `ruta_archivoDieciseis`, `fecha_subidaDieciseis`, `observacionesDiecisiete`, `accionesDiecisiete`, `idProblemasDiecisiete`, `estatusDiecisiete`, `fecha_filaDiecisiete`, `nombre_archivoDiecisiete`, `ruta_archivoDiecisiete`, `fecha_subidaDiecisiete`, `observacionesDieciocho`, `accionesDieciocho`, `idProblemasDieciocho`, `estatusDieciocho`, `fecha_filaDieciocho`, `nombre_archivoDieciocho`, `ruta_archivoDieciocho`, `fecha_subidaDieciocho`, `observacionesDiecinueve`, `accionesDiecinueve`, `idProblemasDiecinueve`, `estatusDiecinueve`, `fecha_filaDiecinueve`, `nombre_archivoDiecinueve`, `ruta_archivoDiecinueve`, `fecha_subidaDiecinueve`, `observacionesVeinte`, `accionesVeinte`, `idProblemasVeinte`, `estatusVeinte`, `fecha_filaVeinte`, `nombre_archivoVeinte`, `ruta_archivoVeinte`, `fecha_subidaVeinte`, `observacionesVeintiuno`, `accionesVeintiuno`, `idProblemasVeintiuno`, `estatusVeintiuno`, `fecha_filaVeintiuno`, `nombre_archivoVeintiuno`, `ruta_archivoVeintiuno`, `fecha_subidaVeintiuno`, `observacionesVeintidos`, `accionesVeintidos`, `idProblemasVeintidos`, `estatusVeintidos`, `fecha_filaVeintidos`, `nombre_archivoVeintidos`, `ruta_archivoVeintidos`, `fecha_subidaVeintidos`, `observacionesVeintitres`, `accionesVeintitres`, `idProblemasVeintitres`, `estatusVeintitres`, `fecha_filaVeintitres`, `nombre_archivoVeintitres`, `ruta_archivoVeintitres`, `fecha_subidaVeintitres`, `observacionesVeinticuatro`, `accionesVeinticuatro`, `idProblemasVeinticuatro`, `estatusVeinticuatro`, `fecha_filaVeinticuatro`, `nombre_archivoVeinticuatro`, `ruta_archivoVeinticuatro`, `fecha_subidaVeinticuatro`, `observacionesVeinticinco`, `accionesVeinticinco`, `idProblemasVeinticinco`, `estatusVeinticinco`, `fecha_filaVeinticinco`, `nombre_archivoVeinticinco`, `ruta_archivoVeinticinco`, `fecha_subidaVeinticinco`, `estatus_cierre`, `idNombreAuditor2`, `idNombreSupervisor`, `idNombreOperador`, `fecha_cierre`, `tuvo_nok`) VALUES
(44, 214, '1093', 'Delion', 'Audi Mexico, S.A. de C.V.', 'Partes Plásticas', 'fsd', NULL, 'sfd', 'Nave 3', 'ddddd', 'Unidad 1', '2025-04-13', '2025-04-22 23:49:37', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-22 23:49:37', 'fds', 'fds', 'Catalogo de NO conformidades', 'NOK', '2025-04-09', 'basedeDatosTablaEncabezado.png', '../uploads/1745365791_basedeDatosTablaEncabezado.png', '2025-04-22 23:49:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-22 23:49:37', 'Abierto', NULL, NULL, NULL, NULL, 1),
(45, 215, '1093', 'Delion', 'Audi Mexico, S.A. de C.V.', 'Partes Plásticas', 'xs', NULL, 'sa', 'Nave 3', 'as', 'Unidad 2', '2025-04-09', '2025-04-23 14:09:26', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 14:09:26', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 14:09:29', '', '', '', 'N/A', '0000-00-00', NULL, NULL, '2025-04-23 14:09:32', 'xsa', 'sa', 'Matriz de EPP', 'OK', '2025-04-18', 'Captura de pantalla 2024-06-17 145422.png', '../uploads/1745417388_Captura de pantalla 2024-06-17 145422.png', '2025-04-23 14:09:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:09:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:09:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:09:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:09:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:09:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:09:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:09:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:09:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:09:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:09:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:09:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:09:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:09:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:09:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:09:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:09:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:09:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:09:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:09:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:09:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-23 14:09:26', 'Abierto', NULL, NULL, NULL, NULL, 1),
(46, 222, '1093', 'Delion', 'Audi Mexico, S.A. de C.V.', 'Partes Plásticas', '80A 861 529 A 4PK', NULL, 'de', 'Nave 9', 'das', 'Unidad 3', '2025-04-28', '2025-04-23 14:57:29', 'sa', 's', 'Calibración/Verificación de equipos vigentes', 'NOK', '2025-04-19', NULL, NULL, '2025-04-23 14:57:29', 'r', 'f', 'Alerta de calidad', 'NOK', '2025-04-19', 'c.png', '../uploads/1745420277_c.png', '2025-04-23 14:57:57', 'fr', 'r', 'Identificación de materiales', 'NOK', '2025-04-01', 'Captura de pantalla 2024-06-24 105623.png', '../uploads/1745420293_Captura de pantalla 2024-06-24 105623.png', '2025-04-23 14:58:13', 'frew', 'wfer', 'Matriz de EPP', 'NOK', '2025-04-26', 'Captura de pantalla 2024-06-24 105536.png', '../uploads/1745420308_Captura de pantalla 2024-06-24 105536.png', '2025-04-23 14:58:28', 'fw', 'fffeeee', 'Medios de seguridad para contención', 'NOK', '2025-04-02', 'Captura de pantalla 2024-06-25 133120.png', '../uploads/1745420325_Captura de pantalla 2024-06-25 133120.png', '2025-04-23 14:58:45', 'fe', 'wfe', 'Conocimiento al plan de reaccion', 'NOK', '2025-04-16', 'Captura de pantalla 2024-06-28 171422.png', '../uploads/1745420343_Captura de pantalla 2024-06-28 171422.png', '2025-04-23 14:59:03', 'eewewrf23r', '23e', 'Calibración/Verificación de equipos vigentes', 'NOK', '2025-05-03', 'Captura de pantalla 2024-06-25 111216.png', '../uploads/1745420359_Captura de pantalla 2024-06-25 111216.png', '2025-04-23 14:59:19', 'fdsa', 'fsdxc  2312', 'Ejecución de 5´s', 'NOK', '2025-04-18', 'Captura de pantalla 2024-06-17 145422.png', '../uploads/1745420379_Captura de pantalla 2024-06-17 145422.png', '2025-04-23 14:59:39', '76u', 'h6', 'Medios de seguridad para contención', 'NOK', '2025-04-12', 'Captura de pantalla 2024-07-04 171823.png', '../uploads/1745420398_Captura de pantalla 2024-07-04 171823.png', '2025-04-23 14:59:58', 'u76u76', 'tyh6', 'Matriz de Habilidades', 'NOK', '2025-04-10', 'Captura de pantalla 2024-06-28 130824.png', '../uploads/1745420415_Captura de pantalla 2024-06-28 130824.png', '2025-04-23 15:00:15', 'r4', '2', 'Identificación de materiales', 'NOK', '2025-05-02', 'Captura de pantalla 2024-06-28 130604.png', '../uploads/1745420431_Captura de pantalla 2024-06-28 130604.png', '2025-04-23 15:00:31', 'Observaciones ', 'Observaciones Observaciones ', 'Dispositivo de control / Instructivo /etiqueta de verificacion', 'NOK', '2025-04-18', 'Captura de pantalla 2024-06-20 163730.png', '../uploads/1745420449_Captura de pantalla 2024-06-20 163730.png', '2025-04-23 15:00:49', 'saccsdaz', '212', 'Manejo de materiales peligrosos', 'NOK', '2025-04-25', 'Captura de pantalla 2024-06-24 085329.png', '../uploads/1745420465_Captura de pantalla 2024-06-24 085329.png', '2025-04-23 15:01:05', 'd3', 'e3', 'Conocimiento al plan de reaccion', 'NOK', '2025-04-26', 'Captura de pantalla 2024-06-24 100926.png', '../uploads/1745420491_Captura de pantalla 2024-06-24 100926.png', '2025-04-23 15:01:31', 'dfeqad321', '212', 'Matriz de EPP', 'NOK', '2025-04-19', 'Captura de pantalla 2024-06-24 105536.png', '../uploads/1745420506_Captura de pantalla 2024-06-24 105536.png', '2025-04-23 15:01:46', 'reeqw', 'eq21', 'Calibración/Verificación de equipos vigentes', 'NOK', '2025-04-26', 'Captura de pantalla 2024-06-25 132959.png', '../uploads/1745420519_Captura de pantalla 2024-06-25 132959.png', '2025-04-23 15:01:59', 'e231', '2e1', 'Medios de seguridad para contención', 'NOK', '2025-04-12', 'basedeDatosTablaEncabezado.png', '../uploads/1745420533_basedeDatosTablaEncabezado.png', '2025-04-23 15:02:13', 'e3', 'wqd ', 'Matriz de Habilidades', 'NOK', '2025-04-05', 'Captura de pantalla 2024-07-04 145716.png', '../uploads/1745420550_Captura de pantalla 2024-07-04 145716.png', '2025-04-23 15:02:30', ' cccccccccccccccccccccccccc', 'cccccccccccccccccccccc', 'Área de trabajo segura', 'NOK', '2025-05-02', 'Captura de pantalla 2024-06-24 085329.png', '../uploads/1745420568_Captura de pantalla 2024-06-24 085329.png', '2025-04-23 15:02:48', 'ki', 'uyi', 'Medios de seguridad para contención', 'NOK', '2025-04-26', 'Captura de pantalla 2024-06-27 153632.png', '../uploads/1745420585_Captura de pantalla 2024-06-27 153632.png', '2025-04-23 15:03:05', 'kkkkkkkkkkkk777777777777', '7757hjjj', 'Medios de seguridad para contención', 'NOK', '2025-05-01', 'Captura de pantalla 2024-06-25 111213.png', '../uploads/1745420605_Captura de pantalla 2024-06-25 111213.png', '2025-04-23 15:03:25', 'jh', 'jjjjjjjjjjjjjjjj44444444444', 'Auditoria de capas', 'NOK', '2025-04-25', 'Captura de pantalla 2024-06-24 105623.png', '../uploads/1745420626_Captura de pantalla 2024-06-24 105623.png', '2025-04-23 15:03:46', '4gre15', 'gert4s5', 'Plan de Control', 'NOK', '2025-05-03', 'Captura de pantalla 2024-06-28 171435.png', '../uploads/1745420648_Captura de pantalla 2024-06-28 171435.png', '2025-04-23 15:04:08', 'gre', 'dsf', 'Medios de seguridad para contención', 'NOK', '2025-04-26', 'Captura de pantalla 2024-06-24 105536.png', '../uploads/1745420661_Captura de pantalla 2024-06-24 105536.png', '2025-04-23 15:04:21', 'AccionesAcciones', 'AccionesAccionesAccionesAccionesAcciones', 'Calibración/Verificación de equipos vigentes', 'NOK', '2025-04-11', 'Captura de pantalla 2024-06-25 133608.png', '../uploads/1745420681_Captura de pantalla 2024-06-25 133608.png', '2025-04-23 15:04:41', 'Abierto', NULL, NULL, NULL, NULL, 1),
(47, 225, '1093', 'Delion', 'Audi Mexico, S.A. de C.V.', 'Partes Plásticas', '80A 861 529 K 4PK', NULL, 'dgfsdfs', 'Nave 7A', 'dsfdfs', 'Unidad 1', '2025-04-17', '2025-04-23 16:27:55', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:27:55', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:27:58', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:28:00', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:28:02', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:28:04', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:28:06', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:28:09', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:28:11', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:28:14', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:28:18', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:28:20', '', '', '', 'N/A', '0000-00-00', NULL, NULL, '2025-04-23 16:28:23', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:28:45', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:28:49', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:28:51', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:28:54', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:28:57', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:29:01', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:29:03', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:29:05', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:29:07', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:29:09', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:29:12', '', '', '', 'OK', '0000-00-00', NULL, NULL, '2025-04-23 16:29:14', '', '', '', 'N/A', '0000-00-00', NULL, NULL, '2025-04-23 16:29:16', 'Cerrado', 'Delion', 'dsfdfs', 'ddddddddddd', '2025-04-23 10:30:28', 0);

--
-- Disparadores `auditoria_proceso`
--
DELIMITER $$
CREATE TRIGGER `update_tuvo_nok_auditoria_proceso` BEFORE UPDATE ON `auditoria_proceso` FOR EACH ROW BEGIN
    IF NEW.estatusUno = 'NOK' OR 
       NEW.estatusDos = 'NOK' OR 
       NEW.estatusTres = 'NOK' OR 
       NEW.estatusCuatro = 'NOK' OR 
       NEW.estatusCinco = 'NOK' OR 
       NEW.estatusSeis = 'NOK' OR 
       NEW.estatusSiete = 'NOK' OR 
       NEW.estatusOcho = 'NOK' OR 
       NEW.estatusNueve = 'NOK' OR 
       NEW.estatusDiez = 'NOK' OR 
       NEW.estatusOnce = 'NOK' OR 
       NEW.estatusDoce = 'NOK' OR 
       NEW.estatusTrece = 'NOK' OR 
       NEW.estatusCatorce = 'NOK' OR 
       NEW.estatusQuince = 'NOK' OR 
       NEW.estatusDieciseis = 'NOK' OR 
       NEW.estatusDiecisiete = 'NOK' OR 
       NEW.estatusDieciocho = 'NOK' OR 
       NEW.estatusDiecinueve = 'NOK' OR 
       NEW.estatusVeinte = 'NOK' OR 
       NEW.estatusVeintiuno = 'NOK' OR
       NEW.estatusVeintidos = 'NOK' OR
       NEW.estatusVeintitres = 'NOK' OR
       NEW.estatusVeinticuatro = 'NOK' OR 
       NEW.estatusVeinticinco = 'NOK' THEN
        SET NEW.tuvo_nok = 1;
    ELSE
        SET NEW.tuvo_nok = OLD.tuvo_nok; -- Mantiene el valor anterior si no hay NOK
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `numero_empleado` varchar(50) NOT NULL,
  `nombre` text DEFAULT NULL,
  `contraseña` text DEFAULT NULL,
  `tipo` enum('superadmin','admin','auditor','usuario') NOT NULL DEFAULT 'usuario',
  `correo` varchar(59) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`numero_empleado`, `nombre`, `contraseña`, `tipo`, `correo`, `estado`) VALUES
('1', '1', '$2y$10$dhzFbDLQ/mr0W2jhO6nVQezAexbAu0t0DATbppkeZKjgqyhBl.1y6', 'superadmin', 'brayan.delion@adlerpelzer.com', 1),
('1000', 'Karina Gomez de Lucio', '$2y$10$nBPxr2xtyaLksXe0au1txuzzgCJcND8S6XwKep0aJWNOuuidRNCtG', 'usuario', 'karina.gomez@adlerpelzer.com', 1),
('1002', 'Joselin Rojas', '$2y$10$NQe/kLMQjcqN26Bb4MyY9.5bESEsseMTM0PDZQdjsqET7wkgPr7Xu', 'usuario', 'Jocelyn.Rojas@adlerpelzer.com', 1),
('1004', 'Jose Mendez', '$2y$10$G1/sYYUxHr7KlyTfFErFgewii2PpJoMGkjm4d07Dggb/FYDIxqbuK', 'usuario', 'juan.mendez@adlerpelzer.com', 1),
('1006', 'Luis Garcia', '$2y$10$mcynsVpW8MPEW6zhKBf5VOHkGdfE42jyb1MNpRWStTdAjcwVAOz/i', 'usuario', 'eduardo.garcia@adlerpelzer.com', 1),
('1039', 'Alan Correa', '$2y$10$oOiOsc5jScHmDgrffbNESe1tN3p3JhaWSD7l9kYahfLI6IiXHaaX.', 'usuario', 'alan.correa@adlerpelzer.com', 1),
('1055', 'Lucrecia Ruano', '$2y$10$StiQtbLGmnQl6bO8kZP5iuQiXmlcFEjFWANCG49WvLAdEdOn8XR7y', 'usuario', 'lucrecia.ruano@adlerpelzer.com', 1),
('1057', 'Aldo Olmos', '$2y$10$3Rc80GFIHYgSiKUZFf21O.v2sap3gYTzUaNd/GCGewPiP1V5Ito46', 'usuario', 'ruben.olmos@adlerpelzer.com', 1),
('1058', 'Eduardo Bautista', '$2y$10$S9HDKaz7RHvMurBt5EuLF.9ll8ZB52f.ugPhh1Y2mUov9kii1DGoW', 'usuario', 'eduardo.bautista@adlerpelzer.com', 1),
('1059', 'Carlos Gonzalez', '$2y$10$2EkB/gtl.F5pDb/1QH.cgueX4Z6gDm5Ou3.fYFpi3yoNWlQY2GbSO', 'usuario', 'carlos.gonzalez@adlerpelzer.com', 1),
('1063', 'Jocelyn Canto', '$2y$10$KnObITcE4P5h32IMVKtOreAlZwIDH9.3EHE6K6Mwy0RrMO/gvcWmm', 'auditor', 'jocelyn.canto@adlerpelzer.com', 1),
('1065', 'Claudia Casarreal', '$2y$10$sBVX3Fo5DfON5z3oSf2SNeQdytBb3JfJ60RLZXhRCI50Yz3DJGVpS', 'usuario', 'claudia.casarreal@adlerpelzer.com', 1),
('1068', 'Janet Jaimes', '$2y$10$PtAsXDkNx3hA4a5rHzPE6eH8yQiMyzzyRcg/opUlcN0wZxLAZ4Ylm', 'usuario', 'perla.jaimes@adlerpelzer.com', 1),
('1089', 'Mario Vazquez', '$2y$10$.Rdmg3goMPazLZutxOTfpOjf7Z50Gk2NMERAnVD1ub5UrTh4y3342', 'usuario', 'mario.vazquez@adlerpelzer.com', 1),
('1090', 'Omar Gonzalez Bravo', '$2y$10$u9Gpu7lh7ET1FZQrCUYBne2RamL9kt3X1mMr5gDtMqt2HpR6gctam', 'usuario', 'n@adlerpelzer.com', 1),
('1091', 'Lorena Cruz', '$2y$10$TUJvZHV9HvWOsk8paeFRyeTnrXkBFhlMa9nvINaCVKMhs5.HGKyuK', 'usuario', 'lorena.cruz@adlerpelzer.com', 1),
('1093', 'Delion', '$2y$10$dl4BKKBKBrlydJA3awRBjuLRdgwUljBLMnjz4vA.oTRMHVckRdnSW', 'superadmin', 'brayan.delion@adlerpelzer.com', 1),
('10932', 'Eduardo Rivero', '$2y$10$iWZrma1djUSYqHWpfxcBPerOdsgZNq0BjlfBj1EaneV0VYjNGEv2e', 'usuario', 'eduardo.rivero@adlerpelzer.com', 1),
('11112', 'Rufino Nava', '$2y$10$1UihLM.TfFZa3udWKXDm1.PBQxwHiI62C2HoGOk9Ilg.eCGg23IkC', 'usuario', 'rufino.nava@adlerpelzer.com', 1),
('11422', 'Raul Ramirez G.', '$2y$10$u3HQmhqUaGnu0f3fTpE2Pu7euGACWI2oxd4Gy2zxggh52ZMaThf.6', 'usuario', 'raul.ramirez@adlerpelzer.com', 1),
('11455', 'Isidro Bravo', '$2y$10$awpNdbANosPGlOYanE1ZNeDKFEiAQ4/2GJB41bH0f0ZEM4yjPS4BO', 'usuario', 'isidro.bravo@adlerpelzer.com', 1),
('12', 'Martin', '$2y$10$Oi1DsNTHbK3XS6vgReOyL.9ZsLp2cBCo/3szuVcMGQ4d1oGfWuWp.', 'admin', NULL, 1),
('12029', 'Raul Sosa', '$2y$10$ts8mNzohFaO0FG/lpIQwyuMlDuJkRs0PxUVoLCuBAm/21ee8QK5Ly', 'usuario', 'raul.sosa@adlerpelzer.com', 1),
('12503', 'Oscar Ortiz', '$2y$10$o3Wz8cTwvZQarYVyV4jKROeO8zXzyMum2DGvNWS.fNah5SpFWFPqS', 'usuario', 'oscar.ortiz@adlerpelzer.com', 1),
('12621', 'Emmanuel Flores', '$2y$10$JzRVf7denoCSCDAAyaY45.HXcOlkmniLWt8HfxtvOSt39FwXU23eS', 'usuario', 'emmanuel.flores@adlerpelzer.com', 1),
('131', 'Victor Torres', '$2y$10$7GsyMS0tfXotZpyrJBTCLuCPAL8kEcu/fAwHpyBapoE64/Tl71JRm', 'usuario', 'victor.torres@adlerpelzer.com', 1),
('13221', 'Raul Ramirez S.', '$2y$10$VPSGd.X1lCPCwovFaSNVLunNrs9Ot59bR4t65sBE/zefk7f7itGQa', 'usuario', 'raul.ramirez@adlerpelzer.com', 1),
('13341', 'Margarita Hernandez', '$2y$10$oC8O9miWZ5KXSZ9saTQwQ.kJrNFnQ385fLiKKACpDgHMES/08E/iq', 'auditor', 'albina.hernandez@adlerpelzer.com', 1),
('14154', 'Enrique Ramirez', '$2y$10$ol.uoS/PcrRv.Lpx6oKfruUhfYJL0EkdSdKGCY.s8fDyenbkvApmK', 'usuario', 'enrique.ramirez@adlerpelzer.com', 1),
('14198', 'Juan C. Aguilar', '$2y$10$VN6f6de2L1xXBF7ItyagteidcLIbdKMghHe1yo19/5Qh.5SU1uwH.', 'usuario', 'juan.aguilar@adlerpelzer.com', 1),
('14512', 'Martin Dominguez', '$2y$10$SxYQ1JN48k4uET1thdQXGew0w.PwalsmYDsI5xxyw0DsspbppWjpG', 'usuario', 'martin.dominguez@adlerpelzer.com', 1),
('14548', 'J. Luis Rivera', '$2y$10$EzVsLJDOTobuTg74C8kVV.dg1EYwBe.EdWg6PTN8Y2JE5JEynHCdO', 'usuario', 'luis.rivera@adlerpelzer.com', 1),
('14792', 'Erick Rodriguez', '$2y$10$ZG5CiayaRdNV7pVJ2.WK8ei5X1xs3LB7B/ofOLk4SwOGiM632RO7a', 'usuario', 'erick.rodriguez@adlerpelzer.com', 1),
('1516', '1516', '$2y$10$JI6WtxhSbUepMuXwQyo3ReZIK8YAdpp1/zsbbtWoCyzLk4mLGYF.W', 'usuario', 'n@adlerpelzer.com', 0),
('15570', 'Salustia Rojo', '$2y$10$ISuUUG1J9/EZ/b2ix0aRju07Gl.5Zgonx/JoT7bX2Lmmc93INCaLq', 'usuario', 'salustia.rojo@adlerpelzer.com', 1),
('15649', 'Minerva Vite', '$2y$10$w54cvfQKbJjTQpFXif0Pa.Y0FEc3KgWadmo.qJC/T0Zoni6tPOU5a', 'usuario', 'minerva.vite@adlerpelzer.com', 1),
('15735', NULL, '$2y$10$uZf5RQq6qKdztiXLh1nt/eB/O.jZeIf3D5ePO/ReHNnkN7Q5iCUcS', '', NULL, NULL),
('15885', 'Juan Flores', '$2y$10$7h1b/LIGK1AhwbJj9siivuuviKAUxpExPqji1TK66NUEZ7qZxpb0q', 'usuario', 'juan.flores@adlerpelzer.com', 1),
('159', 'Lourdes Castillo', '$2y$10$Xt6nmlXjBbrVoa606KkK7uAO.p.xPl6p5zbyTQ1XYGsL41jpcjwVS', 'usuario', 'lourdes.castillo@adlerpelzer.com', 1),
('16118', 'Anastacio Aparicio', '$2y$10$D71jvqorbHLIdTDS9wV4reJf6c7Joa.gWrK/hbN9xinbNpsBLyLou', 'usuario', 'anastacio.aparicio@adlerpelzer.com', 1),
('16210', 'J.L Melendez', '$2y$10$b8OwKklmyA78.ry5ztU6yuk25bHfNT6A0nPgk46ytxrg0D5Xw5UP.', 'usuario', 'n@adlerpelzer.com', 1),
('16256', 'Rene Cardoza', '$2y$10$J5R5X4SIaaCiq/./Zus7X.fkuesA22k4Xq6AUUSijP3CluK8TWem2', 'usuario', 'rene.cardoza@adlerpelzer.com', 1),
('16327', 'Emiliano Hernandez', '$2y$10$kloHI6uWIAur/9BuU2Qvw.PRfSNy5kZAjJjARJPXfGetjltlV7l6G', 'usuario', 'emiliano.hernandez@adlerpelzer.com', 1),
('16339', 'Juan Raymundo', '$2y$10$1JlmgJNiekab3wujf6mIa.vh7CYnW7rnr5.60Lsw0bfB1o5JReiV6', 'usuario', 'juan.raymundo@adlerpelzer.com', 1),
('16341', 'Rolando Delgadillo', '$2y$10$BQFGc.mfeACO6uLU8fYonuK7HVNKcwZ/JmepBpLFIi/ckHhrsT/qS', 'usuario', 'rolando.delgadillo@adlerpelzer.com', 1),
('16507', 'Pedro Ayala', '$2y$10$s908suMsigv4Na9M7SnCzeZjlYbfSbzxmJ1UAEkopmjZ2S8kW88Ze', 'usuario', 'pedro.ayala@adlerpelzer.com', 1),
('16554', 'Miguel A. Fernández', '$2y$10$eYRUjtJCBbVyfa.0mNTGYOShI3opR9oEfajPByF7n.CbTPie/hDMC', 'usuario', 'miguel.fernandez@adlerpelzer.com', 1),
('16570', 'Miguel A. Soto', '$2y$10$C2Txy3nPW9Zuqa7j/DM6PuruSSVGmOwbsb6y0/JUh5I5isAuwGh7y', 'usuario', 'miguel.soto@adlerpelzer.com', 1),
('16747', 'Adrian Ortega', '$2y$10$Ic3.zX5BtjktphGR3hoFsOvuB3PQoaYQ.reSwE0avlAFnxct.rudK', 'usuario', 'adrian.ortega@adlerpelzer.com', 1),
('16754', 'Paola Garcia', '$2y$10$3YCy2CzqpvuR9.FcqCWZEuSS7jvyRcVGvBQa7hXqNO2fBEX7kRTGi', 'usuario', 'paola.garcia@adlerpelzer.com', 1),
('16872', 'Francisco Chavez', '$2y$10$vyKX9qa/DtgmC2wOVDIj9OSS1vAPXHjuKaBIDIr85lNXztdOFDWgO', 'usuario', 'francisco.chavez@adlerpelzer.com', 1),
('16908', 'Emilio Flores', '$2y$10$OjYzZoWz8Uu9d8x3dMwWtOOX7C.2AGalEilOxwRGIq.jQrdoRbBZa', 'usuario', 'emilio.flores@adlerpelzer.com', 1),
('17011', 'Apolinar Diego', '$2y$10$CtGsZRihsf73Pg9u4PN9cuDY7qQD8F.NAsDiCYHZotLO7naCWuTR6', 'usuario', 'apolinar.diego@adlerpelzer.com', 1),
('17025', 'Gabriela Mendez', '$2y$10$DD/YAXH9Gr69qa1QejpA5.m3CpfwL9FRWTvSxhhuMOhIle2S.tATe', 'usuario', 'gabriela.mendez@adlerpelzer.com', 1),
('17058', 'Javier Mujica', '$2y$10$vrqr2lexvsi3vxnk9PCCz.f/V9COZAuS46M7ld2rTvKkgDMoasJYm', 'usuario', 'javier.mujica@adlerpelzer.com', 1),
('17169', 'Jonathan Miros', '$2y$10$dgyUhSE0QxBzTWZsJyuWhOgppIsN0IoKwpyYOEqsVIKmaM6acoRM2', 'usuario', 'jonathan.miros@adlerpelzer.com', 1),
('17296', 'Vanesa Hernandez', '$2y$10$tN9Z.kCn70sd8SRvZd1hL.Gep40byxfN39lQJazzAd7JWqO56qeFy', 'usuario', 'vanesa.hernandez@adlerpelzer.com', 1),
('17454', 'Fabiola Oropeza', '$2y$10$eycdm7ooqOmW5u7dJprLH.hiFad9ivv.7jkOEO2C71yGD7WSK17Pm', 'usuario', 'n@adlerpelzer.com', 0),
('17497', 'Isidro Lopez', '$2y$10$zA.6w/fFRZ38imLxzpA89OUYhRhxpz3U6YGgDaW5X5tzYIdS/3icy', 'usuario', 'isidro.lopez@adlerpelzer.com', 1),
('17538', 'Dannae Gonzalez', '$2y$10$fr6KFQztYKL9XWYHhtr4mOlvnku2EWSy6iy.Q4HLk5SA9lgVQVs6i', 'usuario', 'dannae.gonzalez@adlerpelzer.com', 1),
('17545', 'Ignacio Ortega', '$2y$10$RIG2P0bg9V5WfF43wQwVe.w7wIuBQINgF0pyLjtY4AplJVf7E3Qe.', 'usuario', 'ignacio.ortega@adlerpelzer.com', 1),
('17547', 'Omar Temoxtle', '$2y$10$VLCxxQDY4SGwRmZ1Zw9i9.NzWEvGCLyNlBoSCTKYD65jm2bVdQUQ.', 'usuario', 'omar.temoxtle@adlerpelzer.com', 1),
('17685', 'Juan Alvarado', '$2y$10$026NWMnCmM2TV.mtlmlUh.NgIyP511DeSqj03Z9wD2sVHEXL.miPi', 'usuario', 'juan.alvarado@adlerpelzer.com', 1),
('17733', 'Gamaliel Meneses', '$2y$10$jrq5pXlB/18xFA3MHG1XHuW/mr5H1WXRTmZ869.a8qohxVQwdtkEq', 'usuario', 'gamaliel.meneses@adlerpelzer.com', 1),
('17734', 'Paulina Perez Tinoco', '$2y$10$/1ZI7l8fzzhzWLyjTMm3dO4B6SE2uivZn8ZHO9tijYf8cOG2wT/RS', 'usuario', 'n@adlerpelzer.com', 1),
('17844', 'A. Veronica Ramirez M.', '$2y$10$b0PHzLzePKB3RoTOVuUal.yrwJ/5GoCxn0pJfk8vg.ssFSdRi0T0u', 'usuario', 'a@adlerpelzer.com', 1),
('18044', 'Jose Trinidad Morales', '$2y$10$kSFtaGz.ymo6jSdAz7dn0.hPesn/8.Sh8PiElUJlcbzjwOApwaYC6', 'usuario', 'jose.trinidad@adlerpelzer.com', 1),
('18068', 'Kevin Rojas', '$2y$10$ZxiGk7U957.Tlo5v8R3XeuZKfMSDzP6MW.WmoWGY/IGehSZGnwthy', 'usuario', 'kevin.rojas@adlerpelzer.com', 1),
('2', '2', '$2y$10$84I42eROA6WQd03x3hK8l.EExvep7g0T7RKZ/GasA4hWX2VRoVSLu', 'admin', 'brayan.delion@adlerpelzer.com', 1),
('202', '202', '$2y$10$u2RmDzFRM1HN2lqq2Rs3i.ybkY2/.YPKattSi8hbao7RhitN0TWE.', 'usuario', 'brayan.delion@adlerpelzer.com', 1),
('203', '203', '$2y$10$1OEMiiRa2C6vg2OgysPet.h0nHGKQ6d2QrOYvEresQAjzDdbM82A.', 'auditor', 'brayan.delion@adlerpelzer.com', 1),
('205', '205', '$2y$10$24U/k12lwkNajNTder9iOOKIGauDcbI3ru4uJWuLQViLByWELUaQK', 'superadmin', 'brayan.delion@adlerpelzer.com', 1),
('206', '206', '$2y$10$ZxM0ntq4ckbWCnznU28Txe8aoXUeHjyiP.gFPvycegWefP6iUGDUi', 'admin', 'brayan.delion@adlerpelzer.com', 1),
('260', 'Juan M. Hdz Tellez', '$2y$10$dnbD0wY1R3tzIoixeFo3heNH75/zUfH640XeQjtoEwW9QCN3X3w.6', 'usuario', 'n@adlerpelzer.com', 1),
('275', 'Merari Gutierrez', '$2y$10$N10qPmB1MH1WAJgld8yFbu/JjTYNETSq6TH9bN4ooC6AqPJAgut9.', 'usuario', 'merari.gutierrez@adlerpelzer.com', 1),
('3', '3', '$2y$10$E4dKD4Obvgh3Svte4lADZOafje95q2lbr1SstNABG9/ShBv01.GGa', 'auditor', 'brayan.delion@adlerpelzer.com', 1),
('301', 'Francisco Bravo', '$2y$10$SYSUVjfiMA8zktzpQgRKAOuaRd9ek.NyAOZIYj5WJ3S1exnm363Zi', 'usuario', 'francisco.bravo@adlerpelzer.com', 1),
('383', 'Mauricio Castro', '$2y$10$lxNUCDXrPnU7o96Ab7VP6uq/T4CUVZKZKibVx77qSVzs4u3AwvQDS', 'usuario', 'mauricio.castro@adlerpelzer.com', 1),
('397', 'Marcela Mosqueda', '$2y$10$kIfqHPuRAOflRRBtySf8Lu1dpfj2WXZuwpDG3b2vgPfNvt4tPnCG2', 'admin', 'marcela.mosqueda@adlerpelzer.com', 1),
('4', '4', '$2y$10$kZtBq8J9MVfyOaVAWV17ouJYlJ5e/Bkrd1e3Rrs9HIH2MAMgx0ft2', 'usuario', 'brayan.delion@adlerpelzer.com', 1),
('404', 'Carolina Alvarado', '$2y$10$e8.f7Ze82RFyjsMYuu.aV.xF/3dW4Tk3VOAMEs8BMOLRkG46CmgLm', 'usuario', 'n@adlerpelzer.com', 0),
('410', 'Cesar Espejel', '$2y$10$2Tzy0Xx/GDwxmElPo/Ot5O5U0mypG.eZbSmTc4NfxQx7Un7LyFdBm', 'usuario', 'cesar.espejel@adlerpelzer.com', 1),
('413', 'Marisol Pineda', '$2y$10$UJvpxOXQc/4zWHdzpzUIVe9kEKiyqf3ISWBD0Ra1nu8SbFOolnVc.', 'usuario', 'marisol.pineda@adlerpelzer.com', 1),
('415', 'Marlyn Valdez', '$2y$10$4pjfvEc0wPh.p3ZShe6FJeFNWzdK8z2//KeH26ARzq1TxTJ1ANWme', 'usuario', 'n@adlerpelzer.com', 0),
('470', 'Monica Arriaga', '$2y$10$L0KlIbssC1AC9iouX09LoOlb7tf1uJuMRsCgKCcMctmhKxbo.Xan2', 'usuario', 'monica.arriaga@adlerpelzer.com', 1),
('618', 'Jesus Vargas', '$2y$10$yi3v3r0ztPfk.zwBiNT8IubYH10igqh3NQlIestMnbBQEJYKZe6RG', 'usuario', 'jesus.vargas@adlerpelzer.com', 1),
('719', 'Pedro Buendia', '$2y$10$dZevLHO1ZcoulO8oF6LSIO/oRHFI3LypfR8nlB6fIMN8fg2VhoQ/u', 'usuario', 'pedro.buendia@adlerpelzer.com', 1),
('738', 'Raziel Garcia', '$2y$10$fm231OmAJ5SB7AcOrlT1I.jLg6THdQ0RM1tepB9BK75mwEcnBhw6O', 'usuario', 'raziel.garcia@adlerpelzer.com', 1),
('741', 'Jose L. Dominguez', '$2y$10$yEo/8D/DS3YaxTFCttZMWOI7KAy0Izcts16GhN6wfvMTrSSMGu0r.', 'usuario', 'jose.dominguez@adlerpelzer.com', 1),
('779', 'José Martin Juarez ', '$2y$10$b1RxVEB2Dz4y5r/3HaARwOxxJ7tG15tVyVBPsyAQUjLWgNpa2Kfx.', 'superadmin', 'martin.juarez@adlerpelzer.com', 1),
('7791', 'Jose Martin Juarez', '$2y$10$luaUFO4.YqWPxK4rGwQa0.NTzN.jSIg0keMSMNAayhQTAT9.rdtVq', 'usuario', 'martin.juarez@adlerpelzer.com', 1),
('784', 'Mauricio Loera', '$2y$10$c0atfVH.Qj5PXjIpMxM2puhy4xe7LX3yH/VsE/hknS9xWHGGK40Zi', 'usuario', 'mauricio.loera@adlerpelzer.com', 1),
('789', 'Edgar Hernandez', '$2y$10$31znWErOtVBRt7ndtj0P/OOl80Iv5SWCfRfZahEWeN38teMt0mm2S', 'usuario', 'edgar.hernandez@adlerpelzer.com', 1),
('804', 'Nayeli Mendez', '$2y$10$2syhXrNA4BHqRHyLv7piCeztD83VvkaNMa4..DH/ZNguvg/h/D3pi', 'usuario', 'nayeli.mendez@adlerpelzer.com', 1),
('807', 'Juan M. Torres', '$2y$10$z3GU7h0./xeNCdcAEIE.retynUohvOvlYJ3.ApAFJrD1qW4DhDWlK', 'usuario', 'juan.torres@adlerpelzer.com', 1),
('844', 'Daniel Ocampo', '$2y$10$6e00aSt3jD16GgiS86a85uWBt9Z5gYnlfJpHV6fQP.9VEjZXUzYNS', 'admin', 'daniel.ocampo@adlerpelzer.com', 1),
('848', 'Sergio Camero', '$2y$10$p6wm8yfJTIqKGGmtS/HUIul5241DenLjy/rRVqdkHbyZ703hHKiRq', 'usuario', 'sergio.camero@adlerpelzer.com', 1),
('852', 'V. Alfonso Hernandez', '$2y$10$d/j1Mif2.ZOX4UUGJSZ73eRwiqANhTit1Jd.ZQlaD9780yCYsSpDm', 'usuario', 'victor.hernandez@adlerpelzer.com', 1),
('855', 'Monica Ortiz', '$2y$10$8dCwdm1IJqq6sGLA5u0XRO.mRPQVidG2MRJKKgflBtbuFNUZGd00i', 'usuario', 'monica.ortiz@adlerpelzer.com', 1),
('871', 'Miguel Uribe', '$2y$10$/K2BOToLgFy67GG2xTc4PuvZYz0du76qw0FZG46Z.T4BgShZl5X6y', 'usuario', 'miguel.uribe@adlerpelzer.com', 1),
('872', 'Bruno Oswaldo', '$2y$10$FHac6mmw3Bq5nRYCSKLgO.3GYClWz88pyy1v7R2ZafL5b/h2uM/76', 'usuario', 'metrologia@adlerpelzer.com', 1),
('885', 'German Hernandez', '$2y$10$7Qpi2dojsdBKgQ0BdMy/EO0nrT7Z/rBET3BUyaZy6U9Z5NxSW5HhS', 'usuario', 'german.hernandez@adlerpelzer.com', 1),
('886', 'Francisco Rodriguez', '$2y$10$ap8XzX1z4/h56Yb/Y0WhOORPX/NQOTeOJcnZW2M6hoexb11wjK8O6', 'usuario', 'franciscojavier.rodriguez@adlerpelzer.com', 1),
('9090', 'Rogelio Rivera', '$2y$10$UuJ9cKoe0OsUq4WzvLccy.DC9jFUjK6fijggOWd9KSJx1sar7hzk2', 'usuario', 'rogelio.rivera@adlerpelzer.com', 1),
('9091', 'Rosalio Méndez G', '$2y$10$HJmVyEy.SO7or0YTr5ItFOxEagoWayIqUnCOpJLzYV6C7kkyG.e02', 'admin', 'rosalio.mendez@adlerpelzer.com', 1),
('9092', 'Rodrigo Napoles', '$2y$10$9PAtjcGVYy2ShbsqcUYm2uHqsdIUKNzKFs892RbWayK7BWXxpCOAu', 'usuario', 'rodrigo.napoles@adlerpelzer.com', 1),
('921', 'Humberto Carrillo', '$2y$10$aQxp9.yXuwj1C.GObTes6.yguIluUe14tMi.stxjlCApFotuyd8ki', 'usuario', 'n@adlerpelzer.com', 0),
('946', 'Maria de Jesus Miranda', '$2y$10$xYAKRVcpqjZLh2ItQhSaSeJZ7/AtSBP0dkOeL8TTZc0KmkDSqBfqG', 'usuario', 'mdejesus.miranda@adlerpelzer.com', 1),
('962', 'Cristian Aguiñaga', '$2y$10$9JXBSGB1zpfm9TN.xZcSYe8ioiTTYQctSG0TvYHbce8C9cMb/tyYy', 'usuario', 'crhistian.aguinaga@adlerpelzer.com', 1),
('967', 'Fenanda Ruiz', '$2y$10$VFoTaeQniuRIezD53s3CEuee1j3IqfqxMPJWWQVnOmIPBh6cZbq3W', 'usuario', 'fernanda.ruiz@adlerpelzer.com', 1),
('969', 'Evaristo Lira', '$2y$10$d2AwWmObY4rd7LQxFuwjWeuORIUEzSrqENArMtbXsmt4Qt/Bo5yhK', 'usuario', 'evaristo.lira@adlerpelzer.com', 1),
('973', 'Julio Cesar Perez', '$2y$10$VD6HMrryQWEUDoOxlTDWyOEkubJT84x0fkN.sAcN2Vy86k1PGnCd6', 'usuario', 'juliocesar.perez@adlerpelzer.com', 1),
('980', 'Aimee Quesada', '$2y$10$XwYg500SKMDIIpflA9GgD.aGCCHQFL.PITR4AvY9xbU8.oEFGAphK', 'usuario', 'aimee.quesada@adlerpelzer.com', 1),
('988', 'Hector Montiel', '$2y$10$6VJ6W/4iDHRzC3zm93cDfuQKGEQvWgUZ2mudir7bye1gK8j8RLanC', 'usuario', 'hector.montiel@adlerpelzer.com', 1),
('992', 'Cristian O. Jimenez', '$2y$10$bmaSjwwC2QESjiNtL6WosuNIhK1cJADvzGrFuglGWwt31ct0g49Q6', 'usuario', 'cristhian.jimenez@adlerpelzer.com', 1),
('997', 'Alejandra Ortiz', '$2y$10$AAtjA.rOsFGU35giiq3d1.hlMtSa2l8lOyaeTIgg9snOgHttkwt7.', 'usuario', 'alejandra.ortiz@adlerpelzer.com', 1),
('998', 'Laura Karen Sanches', '$2y$10$/oeb.7vW5.eKAn.Gu.SAH.TYIiOIGgJtRsWWgUp6dB4/aVNFPoytW', 'usuario', 'LauraKaren.sanchez@adlerpelzer.com', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programar_auditoria`
--

CREATE TABLE `programar_auditoria` (
  `id_auditoria` int(11) NOT NULL,
  `numero_empleado` varchar(50) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `nave` varchar(50) NOT NULL,
  `descripcion` text NOT NULL,
  `proyecto` varchar(200) NOT NULL,
  `cliente` varchar(100) NOT NULL,
  `responsable` varchar(100) NOT NULL,
  `tipo_auditoria` enum('auditoria por Capas','auditoria por Procesos') NOT NULL,
  `semana` varchar(10) NOT NULL,
  `fecha_programada` timestamp NOT NULL DEFAULT current_timestamp(),
  `correo` varchar(110) NOT NULL,
  `estatus` enum('Asignada','Proceso','Realizada','Cerrada') NOT NULL,
  `gpn` varchar(100) DEFAULT NULL,
  `numero_parte` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `programar_auditoria`
--

INSERT INTO `programar_auditoria` (`id_auditoria`, `numero_empleado`, `nombre`, `nave`, `descripcion`, `proyecto`, `cliente`, `responsable`, `tipo_auditoria`, `semana`, `fecha_programada`, `correo`, `estatus`, `gpn`, `numero_parte`) VALUES
(208, '1093', 'Delion', 'Nave 3', 'Partes Plásticas', 'Q5 Filling part load rack', 'Audi Mexico, S.A. de C.V.', '', 'auditoria por Capas', '2025-W17', '2025-04-22 19:08:48', 'brayan.delion@adlerpelzer.com', 'Proceso', NULL, NULL),
(209, '1093', 'Delion', 'Nave 3', 'Partes Plásticas', 'Q5 Filling part load rack', 'Audi Mexico, S.A. de C.V.', '', 'auditoria por Capas', '2025-W17', '2025-04-22 19:11:09', 'brayan.delion@adlerpelzer.com', 'Asignada', NULL, NULL),
(210, '1093', 'Delion', 'Nave 3', 'Partes Plásticas', 'Q5 Filling part load rack', 'Audi Mexico, S.A. de C.V.', '', 'auditoria por Capas', '2025-W17', '2025-04-22 19:11:15', 'brayan.delion@adlerpelzer.com', 'Cerrada', NULL, NULL),
(211, '1093', 'Delion', 'Nave 3', 'Partes Plásticas', 'Q5 Filling part load rack', 'Audi Mexico, S.A. de C.V.', '', 'auditoria por Capas', '2025-W17', '2025-04-22 19:15:59', 'brayan.delion@adlerpelzer.com', 'Cerrada', NULL, NULL),
(212, '1093', 'Delion', 'Nave 3', 'Partes Plásticas', 'Q5 Filling part load rack', 'Audi Mexico, S.A. de C.V.', '', 'auditoria por Capas', '2025-W17', '2025-04-22 19:16:35', 'brayan.delion@adlerpelzer.com', 'Asignada', NULL, NULL),
(213, '205', '205', 'Nave 4', 'Lodera', 'Q5 WAL rear left', 'Audi Mexico, S.A. de C.V.', '', 'auditoria por Procesos', '2025-W18', '2025-04-22 19:17:21', 'brayan.delion@adlerpelzer.com', 'Asignada', NULL, NULL),
(214, '1093', 'Delion', 'Nave 3', 'Partes Plásticas', 'Q5 Filling part', 'Audi Mexico, S.A. de C.V.', '', 'auditoria por Procesos', '2025-W18', '2025-04-22 19:18:36', 'brayan.delion@adlerpelzer.com', 'Proceso', NULL, NULL),
(215, '1093', 'Delion', 'Nave 3', 'Partes Plásticas', 'Q5 Filling part', 'Audi Mexico, S.A. de C.V.', '', 'auditoria por Procesos', '2025-W18', '2025-04-22 19:22:56', 'brayan.delion@adlerpelzer.com', 'Proceso', NULL, NULL),
(216, '1093', 'Delion', 'Nave 2', 'Insulators', 'J03   Insulator heat', 'Mazda Motor Manufacturing de Mexico', '', 'auditoria por Procesos', '2025-W19', '2025-04-22 19:23:29', 'brayan.delion@adlerpelzer.com', 'Asignada', NULL, NULL),
(217, '1093', 'Delion', 'Nave 2', 'Insulators', 'J03   Insulator heat', 'Mazda Motor Manufacturing de Mexico', '', 'auditoria por Procesos', '2025-W19', '2025-04-22 19:28:15', 'brayan.delion@adlerpelzer.com', 'Asignada', NULL, NULL),
(219, '1093', 'Delion', 'Nave 9', 'Insulators', 'J03   Heat insulator Dash.RH', 'Mazda Motor Manufacturing de Mexico', '', 'auditoria por Capas', '2025-W19', '2025-04-22 19:40:13', 'brayan.delion@adlerpelzer.com', 'Proceso', NULL, NULL),
(220, '1093', 'Delion', 'Nave 2', 'Insulators', 'J03   Insulator Bonnet', 'Mazda Motor Manufacturing de Mexico', '', 'auditoria por Capas', '2025-W15', '2025-04-22 22:52:26', 'brayan.delion@adlerpelzer.com', 'Asignada', '4111101001.01', 'DA6C56681'),
(221, '1093', 'Delion', 'Nave 1', 'Insulators', 'J03   Insulator heat', 'Mazda Motor Manufacturing de Mexico', '', 'auditoria por Procesos', '2025-W16', '2025-04-22 22:52:57', 'brayan.delion@adlerpelzer.com', 'Asignada', '4111102001.01', 'DA6A56453'),
(222, '1093', 'Delion', 'Nave 9', 'Partes Plásticas', 'LADEBODEN', 'Audi Mexico, S.A. de C.V.', '', 'auditoria por Procesos', '2025-W19', '2025-04-22 23:39:31', 'brayan.delion@adlerpelzer.com', 'Proceso', '1718301009.01', '80A 861 529 A 4PK'),
(223, '1093', 'Delion', 'Nave 8', 'Asfaltos', 'J03    Sheet-Damping, A', 'Mazda Motor Manufacturing de Mexico', '', 'auditoria por Capas', '2025-W19', '2025-04-23 14:04:49', 'brayan.delion@adlerpelzer.com', 'Proceso', '4111205012.01', 'DG7V56151'),
(224, '1093', 'Delion', 'Nave 9', 'Respaldo', 'Lining Rear Panel', 'PROMA AUTOMOTIVE DE MEXICO', '', 'auditoria por Capas', '2025-W19', '2025-04-23 16:19:16', 'brayan.delion@adlerpelzer.com', 'Realizada', '16B5212002', '36185'),
(225, '1093', 'Delion', 'Nave 7A', 'Partes Plásticas', 'Q5     Load Floor front Hyb', 'Audi Mexico, S.A. de C.V.', '', 'auditoria por Procesos', '2025-W21', '2025-04-23 16:19:40', 'brayan.delion@adlerpelzer.com', 'Cerrada', '1718301017.0', '80A 861 529 K 4PK');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos`
--

CREATE TABLE `proyectos` (
  `id` int(11) NOT NULL,
  `gpn` varchar(100) DEFAULT NULL,
  `numero_parte` varchar(100) DEFAULT NULL,
  `cliente` varchar(100) DEFAULT NULL,
  `proyecto` varchar(200) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `nave` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proyectos`
--

INSERT INTO `proyectos` (`id`, `gpn`, `numero_parte`, `cliente`, `proyecto`, `descripcion`, `nave`) VALUES
(67, '1718301001.03', '80A 861 531 G GW8', 'Audi Mexico, S.A. de C.V.', 'Q5 Load Floor rear', 'Porta Equipaje', NULL),
(68, '1718301002.02', '80A 861 529 G GW8', 'Audi Mexico, S.A. de C.V.', 'Q5 Load Floor front', 'Partes Plásticas', NULL),
(69, '1718301003.02', '80A 861 529 H GW8', 'Audi Mexico, S.A. de C.V.', 'Q5 Load Floor front', 'Partes Plásticas', NULL),
(70, '1718312002.02', '80A 861 827 C GW8', 'Audi Mexico, S.A. de C.V.', 'Q5 Filling part', 'Partes Plásticas', NULL),
(71, '1718312003.02', '80A 861 828 C GW8', 'Audi Mexico, S.A. de C.V.', 'Q5 Filling part', 'Partes Plásticas', NULL),
(72, '1718312007.01', '80A 861 835 4PK', 'Audi Mexico, S.A. de C.V.', 'Q5 Filling part load rack', 'Partes Plásticas', NULL),
(73, '1718312008.01', '80A 861 836 4PK', 'Audi Mexico, S.A. de C.V.', 'Q5 Filling part load rack', 'Partes Plásticas', NULL),
(74, '1718402001.02', '80A 810 171 A', 'Audi Mexico, S.A. de C.V.', 'Q5 WAL rear left', 'Lodera', NULL),
(75, '1718402002.03', NULL, 'Audi Mexico, S.A. de C.V.', 'Q5 WAL front right ZWK', 'Lodera', NULL),
(76, '1718402003.03', '80A 821 172 G', 'Audi Mexico, S.A. de C.V.', 'Q5 WAL front right', 'Q5 WAL front right', NULL),
(77, '1718402004.03', '80A 821 171 F', 'Audi Mexico, S.A. de C.V.', 'Q5 WAL front left ZWK', 'Lodera', NULL),
(78, '1718402005.03', '80A 821 171 G', 'Audi Mexico, S.A. de C.V.', 'Q5 WAL front left', 'Lodera', NULL),
(79, '1718402006.02', '80A 810 172 A', 'Audi Mexico, S.A. de C.V.', 'Q5  WAL rear right', 'Lodera', NULL),
(80, '1718405002.02', '80A 863 545 D', 'Audi Mexico, S.A. de C.V.', 'Dammung Rucksitzbank LOW', 'Underseat', NULL),
(81, '1718301009.01', '80A 861 529 A 4PK', 'Audi Mexico, S.A. de C.V.', 'LADEBODEN', 'Partes Plásticas', NULL),
(82, '1718301015.01', '80A 861 529 J 4PK', 'Audi Mexico, S.A. de C.V.', 'Q5 Load Floor front', 'Partes Plásticas', NULL),
(83, '1718301017.0', '80A 861 529 K 4PK', 'Audi Mexico, S.A. de C.V.', 'Q5     Load Floor front Hyb', 'Partes Plásticas', NULL),
(84, '1718301021.01', '80A 861 531 D GW8', 'Audi Mexico, S.A. de C.V.', 'Q5     Load Floor rear Hyb', 'Porta Equipaje', NULL),
(85, '1718301023.01', '80A 861 531 E GW8', 'Audi Mexico, S.A. de C.V.', 'Q5     Load Floor rear Hyb', 'Porta Equipaje', NULL),
(86, '1718301025.01', '80A 861 531 F GW8', 'Audi Mexico, S.A. de C.V.', 'Q5     Load Floor rear Hyb', 'Porta Equipaje', NULL),
(87, '1718312013.01', '80A 861 835 C 4PK', 'Audi Mexico, S.A. de C.V.', 'Q5     Filling part load Hyb', 'Partes Plásticas', NULL),
(88, '1718312014.01', '80A 861 836 A 4PK', 'Audi Mexico, S.A. de C.V.', 'Q5 Filling part load Hyb', 'Partes Plásticas', NULL),
(89, '1718312015.01', '80A 861 827 B GW8', 'Audi Mexico, S.A. de C.V.', 'Q5     Filling part Hyb', 'Partes Plásticas', NULL),
(90, '1718312016.01', '80A 861 828 B GW8', 'Audi Mexico, S.A. de C.V.', 'Q5     Filling part Hyb', 'Partes Plásticas', NULL),
(91, '1718213004.02', '80A 864 785 A', 'Audi Mexico, S.A. de C.V.', 'INSULATION BELT TENSIONER LH', 'Espumitas', NULL),
(92, '1718213003.02', '80A 864 786 A', 'Audi Mexico, S.A. de C.V.', 'INSULATION BELT TENSIONER RH', 'Espumitas', NULL),
(93, '1674213001', '8MA 863 813 A', 'Audi Mexico, S.A. de C.V.', 'WHEEL HOUSING', 'Lodera Interna', NULL),
(94, '1674213002', '8MA 863 814 A', 'Audi Mexico, S.A. de C.V.', 'WHEEL HOUSING', 'Lodera Interna', NULL),
(95, '1674213005', '8MA 864 785', 'Audi Mexico, S.A. de C.V.', 'Insulation', 'Espumitas', NULL),
(96, '1674213006', '8MA 864 785', 'Audi Mexico, S.A. de C.V.', 'Insulation', 'Espumitas', NULL),
(97, '1674213006', '8MA 864 786', 'Audi Mexico, S.A. de C.V.', 'Insulation', 'Espumitas', NULL),
(98, '1674213007', '8MS 864 785', 'Audi Mexico, S.A. de C.V.', 'Insulation', 'Espumitas', NULL),
(99, '1674213008', '8MS 864 786', 'Audi Mexico, S.A. de C.V.', 'Insulation', 'Espumitas', NULL),
(100, '1674402003', '8MA 821 171 D', 'Audi Mexico, S.A. de C.V.', 'WHEEL HOUSING LI', 'Lodera', NULL),
(101, '1674402004', '8MA 821 172 D', 'Audi Mexico, S.A. de C.V.', 'WHEEL HOUSING RE', 'Lodera', NULL),
(102, '1674402001', '8MA 810 171 A', 'Audi Mexico, S.A. de C.V.', 'WHEEL HOUSING LI', 'Lodera', NULL),
(103, '1674402002', '8MA 810 172 A', 'Audi Mexico, S.A. de C.V.', 'WHEEL HOUSING RE', 'Lodera', NULL),
(104, '1674213004.02', '8MA 863 545 B', 'Audi Mexico, S.A. de C.V.', 'INSULATING MAT', 'Underseat', NULL),
(105, '1674213003.02', '8MA 863 545 C', 'Audi Mexico, S.A. de C.V.', 'INSULATING MAT', 'Underseat', NULL),
(106, '2220101001.02', '68188633AD', 'Stellantis', 'VF-Duc Hood Silencer', 'Insulators', NULL),
(107, '2220102001.04', '68163837AI', 'Stellantis', 'VF-Duc Insulation Dash Engine', 'Insulators', NULL),
(108, '2220104002', '68631952AB', 'Stellantis', 'Insulation LWR', 'Insulators', NULL),
(109, '2220104001', '68164224AG', 'Stellantis', 'Insulation LWR', 'Insulators', NULL),
(110, '2220203001.04', '68164217AF', 'Stellantis', 'VF  Inner Dash Assy Panel', 'Insulators', NULL),
(111, '6408201005.02', '6408201005.02', 'Stellantis', 'Carpet Assy-Floor Frt LT', 'Alfombra de Piso', NULL),
(112, '6408201003.02', '7JZ94TX7AB', 'Stellantis', 'Carpet Assy-Floor Frt RT', 'Alfombra de Piso', NULL),
(113, '6408201001.02', '7KZ38TX7AB', 'Stellantis', 'Carpet Assy-Floor RR', 'Alfombra de Piso', NULL),
(114, '6406201003.03', '7MC84TX7AB', 'Stellantis', 'Carpet Assy Floor FRT RT', 'Alfombra de Piso', NULL),
(115, '6406201004.03', '7MC85TX7AB', 'Stellantis', 'Carpet Assy Floor FRT LT', 'Alfombra de Piso', NULL),
(116, '6406201005.02', '7ME36TX7AA', 'Stellantis', 'Carpet Assy Floor RR', 'Alfombra de Piso', NULL),
(117, '6408201002.01', '\'7LK96TX7AA', 'Stellantis', 'Carpet Assy Floor RR', 'Alfombra de Piso', NULL),
(118, '863205002', '84114-H9000', 'KIA Motors Mexico', 'Anti PAD-CTR Floor RR', 'Asfaltos', NULL),
(119, '863205010', '84155-BC000', 'KIA Motors Mexico', 'ANTI PAD - CTR FLOOR FR SIDE, LH', 'Asfaltos', NULL),
(120, '863205008', '84156-BC000', 'KIA Motors Mexico', 'ANTI PAD - CTR FLOOR RR SIDE, LH', 'Asfaltos', NULL),
(121, '863205009', '84158-BC000', 'KIA Motors Mexico', 'ANTI PAD - RR FLOOR CTR SIDE, LH', 'Asfaltos', NULL),
(122, '863306001', '84178-BC000', 'KIA Motors Mexico', 'ANTI PAD - SPARE TIRE WELL', 'Asfaltos', NULL),
(123, '863205013', '84185-BC000', 'KIA Motors Mexico', 'ANTI PAD - RR FLOOR RR SIDE, RH', 'Asfaltos', NULL),
(124, '881205001', '84135-GG000', 'KIA Motors Mexico', 'ANTI PAD - FR FLOOR SIDE, LH', 'Asfaltos', NULL),
(125, '881205003', '84156-GG000', 'KIA Motors Mexico', 'ANTI PAD - CTR FLOOR RR SIDE, LH', 'Asfaltos', NULL),
(126, '881205004', '84175-GG000', 'KIA Motors Mexico', 'ANTI PAD - RR FLOOR FR SIDE, LH', 'Asfaltos', NULL),
(127, '881306001', '84178-GG000', 'KIA Motors Mexico', 'ANTI PAD - SPARE TIRE WELL', 'Asfaltos', NULL),
(128, '881205005', '84186-GG000', 'KIA Motors Mexico', 'ANTI PAD - RR FLR SIDE CTR, LH', 'Asfaltos', NULL),
(129, '881205002', '84155-GG000', 'KIA Motors Mexico', 'ANTI PAD - CTR FLOOR FR SIDE, LH', 'Asfaltos', NULL),
(130, '881205006', '84165-GG000', 'KIA Motors Mexico', 'ANTI PAD - CTR FLOOR FR SIDE, RH', 'Asfaltos', NULL),
(131, '08A6301001', '85715 CW000 NNB', 'Hyundai Motor', 'Board Assy Luggage', 'Porta Equipaje', NULL),
(132, '4111101001.01', 'DA6C56681', 'Mazda Motor Manufacturing de Mexico', 'J03   Insulator Bonnet', 'Insulators', NULL),
(133, '4111102001.01', 'DA6A56453', 'Mazda Motor Manufacturing de Mexico', 'J03   Insulator heat', 'Insulators', NULL),
(134, '4111102002.01', 'DA6A564F3', 'Mazda Motor Manufacturing de Mexico', 'J03   Heat insulator Dash.RH', 'Insulators', NULL),
(135, '4111201013.01', 'DG7T68670', 'Mazda Motor Manufacturing de Mexico', 'J03 Mat Assy Floor', 'Alfombra de Piso', NULL),
(136, '4111201014.01', 'DHM568670', 'Mazda Motor Manufacturing de Mexico', 'J03 Mat Assy Floor', 'Alfombra de Piso', NULL),
(137, '4111201015.01', 'DHM668670', 'Mazda Motor Manufacturing de Mexico', 'J03 Mat Assy Floor', 'Alfombra de Piso', NULL),
(138, '4111201016.01', 'DHM368670', 'Mazda Motor Manufacturing de Mexico', 'J03 Mat Assy Floor', 'Alfombra de Piso', NULL),
(139, '4111201017.01', 'DADT68670', 'Mazda Motor Manufacturing de Mexico', 'J03 Mat Assy Floor', 'Alfombra de Piso', NULL),
(140, '4111201018.01', 'DADV68670', 'Mazda Motor Manufacturing de Mexico', 'J03 Mat Assy Floor', 'Alfombra de Piso', NULL),
(141, '4111201006.01', 'DG7M68670', 'Mazda Motor Manufacturing de Mexico', 'J03    Mat Assy Floor', 'Alfombra de Piso', NULL),
(142, '4111201007.01', 'DG7P68670', 'Mazda Motor Manufacturing de Mexico', 'J03    Mat Assy Floor', 'Alfombra de Piso', NULL),
(143, '4111201007.01', 'DG7P68670', 'Mazda Motor Manufacturing de Mexico', 'J03    Mat Assy Floor', 'Alfombra de Piso', NULL),
(144, '4111201008.01', 'DG7S68670', 'Mazda Motor Manufacturing de Mexico', 'J03    Mat Assy Floor', 'Alfombra de Piso', NULL),
(145, '4111201009.01', 'DG8F68670', 'Mazda Motor Manufacturing de Mexico', 'J03    Mat Assy Floor', 'Alfombra de Piso', NULL),
(146, '4111205002.03', 'DA6A56152', 'Mazda Motor Manufacturing de Mexico', 'J03   Sheet-Damping, B', 'Asfaltos', NULL),
(147, '4111205004.03', 'DA6A56155B', 'Mazda Motor Manufacturing de Mexico', 'J03   Sheet- Damping, F', 'Asfaltos', NULL),
(148, '4111205005.03', 'DA6A56156B', 'Mazda Motor Manufacturing de Mexico', 'J03   Sheet -Damping, D', 'Asfaltos', NULL),
(149, '4111205006.01', 'DA6A56159', 'Mazda Motor Manufacturing de Mexico', 'J03   Sheet-Damping,R,Side', 'Asfaltos Magneticos', NULL),
(150, '4111205012.01', 'DG7V56151', 'Mazda Motor Manufacturing de Mexico', 'J03    Sheet-Damping, A', 'Asfaltos', NULL),
(151, '4111205013.01', 'DG7V56154', 'Mazda Motor Manufacturing de Mexico', 'J03    Sheet-Damping, C', 'Asfaltos', NULL),
(152, '4111301001.01', 'DB1R6881X', 'Mazda Motor Manufacturing de Mexico', 'J03 Mat Assy Trunk', 'Porta Equipaje', NULL),
(153, '4111301002.01', 'DB5K6881X', 'Mazda Motor Manufacturing de Mexico', 'J03 Mat Assy Trunk', 'Porta Equipaje', NULL),
(154, '4111301002.01', 'DB5K6881X', 'Mazda Motor Manufacturing de Mexico', 'J03 Mat Assy Trunk', 'Porta Equipaje', NULL),
(155, '4111301005.01', 'DB1N6881X', 'Mazda Motor Manufacturing de Mexico', 'J03 Mat Assy-Trunk', 'Porta Equipaje', NULL),
(156, '4111306002.03', 'DA6C56157B', 'Mazda Motor Manufacturing de Mexico', 'J03    Sheet-Damping, Trunk', 'Asfaltos', NULL),
(157, '4111306003.03', 'DA6C56177B', 'Mazda Motor Manufacturing de Mexico', 'J03    Sheet-Damping, Trunk', 'Asfaltos', NULL),
(158, '4111306004.03', 'DA6C56178B', 'Mazda Motor Manufacturing de Mexico', 'J03    Sheet-Damping, Trunk', 'Asfaltos', NULL),
(159, '4111306004.03', 'DA6C56178B', 'Mazda Motor Manufacturing de Mexico', 'J03    Sheet-Damping, Trunk', 'Asfaltos', NULL),
(161, '4111405001.01', 'DA6A56436', 'Mazda Motor Manufacturing de Mexico', 'J03   Tunnel insulator', 'Insulators', NULL),
(162, '4111306006.01', 'DHM156165', 'Mazda Motor Manufacturing de Mexico', 'Sht-Damp, Whl House', 'Asfaltos Magneticos', NULL),
(163, '4111301005.01', 'DB1N6881X', 'Mazda Motor Manufacturing de Mexico', 'Mat Assy Trunk', 'Porta Equipaje', NULL),
(164, '4111301001.01', 'DB1R6881X', 'Mazda Motor Manufacturing de Mexico', 'Mat Assy Trunk', 'Porta Equipaje', NULL),
(165, '4111301002.01', 'DB5K6881X', 'Mazda Motor Manufacturing de Mexico', 'Mat Asst Trunk', 'Porta Equipaje', NULL),
(166, '4111301007.01', 'DNGH6881X', 'Mazda Motor Manufacturing de Mexico', 'Mat Assy Trunk', 'Porta Equipaje', NULL),
(167, '4116205003.01', 'DLSB56159', 'Mazda Motor Manufacturing de Mexico', 'Sheet -Damping R Side', 'Asfaltos Magneticos', NULL),
(168, '4116205001.01', 'DLSB 56156', 'Mazda Motor Manufacturing de Mexico', 'Sheet -Damping D', 'Asfaltos', NULL),
(169, '4116306001.01', 'DLSB56157', 'Mazda Motor Manufacturing de Mexico', 'Sheet -Damping Trunk', 'Asfaltos', NULL),
(170, '4116205002.01', 'DLSB56158', 'Mazda Motor Manufacturing de Mexico', 'Sheet -Damping CTR', 'Asfaltos', NULL),
(171, '4116205004.01', 'DLSB56161', 'Mazda Motor Manufacturing de Mexico', 'Sheet -Damping R', 'Asfaltos', NULL),
(172, '4116205005.01', 'DLSB56177', 'Mazda Motor Manufacturing de Mexico', 'Sheet -Damping', 'Asfaltos', NULL),
(173, '4116205006.01', 'DLSB56178', 'Mazda Motor Manufacturing de Mexico', 'Sheet -Damping', 'Asfaltos', NULL),
(174, '4116201004.01', 'DNJF68670', 'Mazda Motor Manufacturing de Mexico', 'MAT ASSY FLOOR', 'Alfombra de Piso', NULL),
(175, '4116301001.01', 'DNJK6881X', 'Mazda Motor Manufacturing de Mexico', 'Mat Assy - Trunk', 'Porta Equipaje', NULL),
(176, '4114205001.02', 'BDTS56151A', 'Mazda Motor Manufacturing de Mexico', 'Sheet Damping A', 'Asfaltos Magneticos', NULL),
(177, '4114205002.02', 'BDTS56152A', 'Mazda Motor Manufacturing de Mexico', 'Sheet Damping B', 'Asfaltos Magneticos', NULL),
(178, '4114205007.03', 'BDTS56159B', 'Mazda Motor Manufacturing de Mexico', 'Sheet-Damping, R Side', 'Asfaltos Magneticos', NULL),
(179, '4114205003.02', 'BDTS56154A', 'Mazda Motor Manufacturing de Mexico', 'Sheet-Damping, C', 'Asfaltos', NULL),
(180, '4114205004.02', 'BDTS56155A', 'Mazda Motor Manufacturing de Mexico', 'Sheet-Damping, F', 'Asfaltos', NULL),
(181, '4114306002.02', 'BDTS56157A', 'Mazda Motor Manufacturing de Mexico', 'Sheet-Damping, Trunk', 'Asfaltos', NULL),
(182, '4114205006.02', 'BDTS56158A', 'Mazda Motor Manufacturing de Mexico', 'Sht Damp CTR', 'Asfaltos', NULL),
(183, '4114205009.02', 'BDTS56162A', 'Mazda Motor Manufacturing de Mexico', 'Sheet-Damping, Dash', 'Asfaltos', NULL),
(184, '4114205010.02', 'BDTS56163A', 'Mazda Motor Manufacturing de Mexico', 'Sheet-Damping, Dash UP', 'Asfaltos', NULL),
(185, '4114205005.03', 'BDTS56156B', 'Mazda Motor Manufacturing de Mexico', 'Sheet-Damping, D', 'Asfaltos', NULL),
(186, '4114404003.02', 'BDTS56391A', 'Mazda Motor Manufacturing de Mexico', 'Plate-Seal RH', 'Fender', NULL),
(187, '4114404004.02', 'BDTS56396A', 'Mazda Motor Manufacturing de Mexico', 'Plate-Seal LH', 'Fender', NULL),
(188, '4114404001.02', 'BDTS56J12A', 'Mazda Motor Manufacturing de Mexico', 'Seal-B RH', 'Fender', NULL),
(189, '4114404002.02', 'BDTS56J13A', 'Mazda Motor Manufacturing de Mexico', 'Seal-B LH', 'Fender', NULL),
(190, '4114301001.02', 'BDTS6881X', 'Mazda Motor Manufacturing de Mexico', 'Mat Assy Trunk', 'Porta Equipaje', NULL),
(191, '4114201001.07', 'BDTT68670E', 'Mazda Motor Manufacturing de Mexico', 'J03    Mat Assy Floor', 'Alfombra de Piso', NULL),
(192, '4114201002.07', 'BDTS68670E', 'Mazda Motor Manufacturing de Mexico', 'MAT ASSY FLOOR', 'Alfombra de Piso', NULL),
(193, '4114201007.06', 'BDWK68670D', 'Mazda Motor Manufacturing de Mexico', 'MAT ASSY FLOOR', 'Alfombra de Piso', NULL),
(194, '4114201008.06', 'BDWL68670D', 'Mazda Motor Manufacturing de Mexico', 'MAT ASSY FLOOR', 'Alfombra de Piso', NULL),
(195, '4114201009.06', 'BDWR68670D', 'Mazda Motor Manufacturing de Mexico', 'MAT ASSY FLOOR', 'Alfombra de Piso', NULL),
(196, '4114201010.05', 'BDWP68670D', 'Mazda Motor Manufacturing de Mexico', 'MAT ASSY FLOOR', 'Alfombra de Piso', NULL),
(197, '4114201011.05', 'BEKC68670D', 'Mazda Motor Manufacturing de Mexico', 'MAT ASSY FLOOR', 'Alfombra de Piso', NULL),
(198, '4114201012.05', 'BEKD68670D', 'Mazda Motor Manufacturing de Mexico', 'MAT ASSY FLOOR', 'Alfombra de Piso', NULL),
(199, '4114104001.01', 'BEKC42744', 'Mazda Motor Manufacturing de Mexico', 'Insulator', 'Insulators', NULL),
(200, '4114104002.01', 'BEKC42745', 'Mazda Motor Manufacturing de Mexico', 'Insulator', 'Insulators', NULL),
(201, '4114306003.01', 'DGH956153', 'Mazda Motor Manufacturing de Mexico', 'SHT-Damp Tunnel ,R', 'Asfaltos', NULL),
(202, '4114205011.01', 'DGH956155', 'Mazda Motor Manufacturing de Mexico', 'Sheet-Damping, F', 'Asfaltos', NULL),
(203, '4114306004.01', 'DGH956157', 'Mazda Motor Manufacturing de Mexico', 'SHT-Damp Trunk', 'Asfaltos', NULL),
(204, '4114205012.01', 'DGH956158', 'Mazda Motor Manufacturing de Mexico', 'SHT-Damp CTR', 'Asfaltos', NULL),
(205, '4114404007.02', 'DGH956391A', 'Mazda Motor Manufacturing de Mexico', 'Plate-Seal RH', 'Fender', NULL),
(206, '4114404008.02', '4114404008.02', 'Mazda Motor Manufacturing de Mexico', 'Plate-Seal LH', 'Fender', NULL),
(207, '4114404005.02', 'DGH956J12A', 'Mazda Motor Manufacturing de Mexico', 'Seal-B RH', 'Fender', NULL),
(208, '4114404006.02', 'DGH956J13A', 'Mazda Motor Manufacturing de Mexico', 'Seal-B LH', 'Fender', NULL),
(209, '4114402009.01', 'DHSW561H1', 'Mazda Motor Manufacturing de Mexico', 'GUARD-MUD R RH', 'Lodera', NULL),
(210, '4114402010.01', 'DHSW561J1', 'Mazda Motor Manufacturing de Mexico', 'GUARD-MUD R LH', 'Lodera', NULL),
(211, '4114301004.02', 'DGH96881X', 'Mazda Motor Manufacturing de Mexico', 'Mat Assy Trunk', 'Porta Equipaje', NULL),
(212, '4114301005.02', 'DGS96881X', 'Mazda Motor Manufacturing de Mexico', 'Mat Assy Trunk', 'Porta Equipaje', NULL),
(213, '4114301006.02', 'DGV46881X', 'Mazda Motor Manufacturing de Mexico', 'Mat Assy Trunk', 'Porta Equipaje', NULL),
(214, '4114301007.02', 'DGK96881X', 'Mazda Motor Manufacturing de Mexico', 'Mat Assy Trunk', 'Porta Equipaje', NULL),
(215, '4114301008.02', 'DGL26881X', 'Mazda Motor Manufacturing de Mexico', 'Mat Assy Trunk', 'Porta Equipaje', NULL),
(216, '4114201013.05', 'DGK968670C', 'Mazda Motor Manufacturing de Mexico', 'MAT ASSY FLOOR', 'Alfombra de Piso', NULL),
(217, '4114201014.05', 'DGH968670C', 'Mazda Motor Manufacturing de Mexico', 'MAT ASSY FLOOR', 'Alfombra de Piso', NULL),
(218, '4114201019.05', 'DGJ468670C', 'Mazda Motor Manufacturing de Mexico', 'MAT ASSY FLOOR', 'Alfombra de Piso', NULL),
(219, '4114201020.05', 'DGL468670C', 'Mazda Motor Manufacturing de Mexico', 'MAT ASSY FLOOR', 'Alfombra de Piso', NULL),
(220, '4114311004.01', 'BGVN56135', 'Mazda Motor Manufacturing de Mexico', 'Insulator R', 'Insulators', NULL),
(221, '4114104004.01', 'DGJ442745', 'Mazda Motor Manufacturing de Mexico', 'Insulator', 'Insulators', NULL),
(222, '4114104003.01', 'DGJ442744', 'Mazda Motor Manufacturing de Mexico', 'Insulator', 'Insulators', NULL),
(223, '4114402007.01', 'BGVN561H1', 'Mazda Motor Manufacturing de Mexico', 'GUARD-MUD R RH', 'Lodera', NULL),
(224, '4114402008.01', 'BGVN561J1', 'Mazda Motor Manufacturing de Mexico', 'GUARD-MUD R LH', 'Lodera', NULL),
(225, '1517205001.02', 'DB7P56157', 'Mazda Motor Manufacturing de Mexico', 'J03G Sheet Damping Trunk', 'Asfaltos', NULL),
(226, '1517301001.02', 'DD1B6881XA', 'Mazda Motor Manufacturing de Mexico', 'J03G Mat Assy-Trunk T Temp', 'Porta Equipaje', NULL),
(227, '4111301006.01', 'DNGD6881X', 'Mazda Motor Manufacturing de Mexico', 'Mat Assy Trunk', 'Porta Equipaje', NULL),
(228, '1207205003.01', '748855RB0A', 'Nissan Mexicana, S.A. de C.V.', 'P02F Insul --FR Floor, RR', 'Asfaltos', NULL),
(229, '1207205004.01', '748875RB0A', 'Nissan Mexicana, S.A. de C.V.', 'P02F Insul --RR Floor, RR', 'Asfaltos', NULL),
(230, '1208205002.01', '74881EL010', 'Nissan Mexicana, S.A. de C.V.', 'B/V C Insul fr Floor fr ctr', 'Insulators', NULL),
(231, '1208205002.01', '74881EL010', 'Nissan Mexicana, S.A. de C.V.', 'B/V C Insul fr Floor fr ctr', 'Insulators', NULL),
(232, '1208205003.01', '74881EL020', 'Nissan Mexicana, S.A. de C.V.', 'B/V C Insul fr Floor  ctr', 'Insulators', NULL),
(233, '1225102002.01', '678961HL0A', 'Nissan Mexicana, S.A. de C.V.', 'B/V Insul Dash Lwr', 'Insulators', NULL),
(234, '1225306001.01', '748823AN0A', 'Nissan Mexicana, S.A. de C.V.', 'L02B Insul - Fr Floor, Fr', 'Asfaltos', NULL),
(235, '1208205001.01', '74881EL000', 'Nissan Mexicana, S.A. de C.V.', 'X11C/L12F Insul fr Floor fr ct', 'Asfaltos', NULL),
(236, '1227205003.01', '673569AM0C', 'Nissan Mexicana, S.A. de C.V.', 'L12F Insul-Fusible, Dash Lwr', 'Insulators', NULL),
(237, '1227205004.01', '673569AM0D', 'Nissan Mexicana, S.A. de C.V.', 'L12F Insul-Fusible, Dash Lwr', 'Insulators', NULL),
(238, '1207205001.01', '748825RB0A', 'Nissan Mexicana, S.A. de C.V.', 'P02F Insul-Fr Floor, Fr', 'Insulators', NULL),
(239, '1207205002.01', '748665RB0A', 'Nissan Mexicana, S.A. de C.V.', 'P02F   Insul-Fr Floor, Ctr', 'Insulators', NULL),
(240, '1224205004.01', '748554KH1A', 'Nissan Mexicana, S.A. de C.V.', 'H60A Insul Floor Fr Fr Si', 'Asfaltos', NULL),
(241, '1224205006.01', '748854KH0A', 'Nissan Mexicana, S.A. de C.V.', 'H60A Insul Fr Floor Rr Rh', 'Asfaltos', NULL),
(242, '1228205001.01', '748823LM0C', 'Nissan Mexicana, S.A. de C.V.', 'X11M Insul Fr Floor Fr', 'Asfaltos', NULL),
(243, '1242402001.01', '767487LG0A', 'Nissan Mexicana, S.A. de C.V.', 'PROTR-RR WH RH', 'Lodera', NULL),
(244, '1242402002.01', '767497LG0A', 'Nissan Mexicana, S.A. de C.V.', 'PROTR-RR WH LH', 'Lodera', NULL),
(245, '1242402003.01', '767497LG1A', 'Nissan Mexicana, S.A. de C.V.', 'PROTR-RR WH LH', 'Lodera', NULL),
(246, '1233402001.02', '767485RL0A', 'Nissan Mexicana, S.A. de C.V.', 'Prot RR-WH RH', 'Lodera', NULL),
(247, '1233402002.02', '767495RL0A', 'Nissan Mexicana, S.A. de C.V.', 'Protr-RR WH, LH', 'Lodera', NULL),
(248, '1233402003.8', '767495RY0A', 'Nissan Mexicana, S.A. de C.V.', 'PROTR-RR WH,LH', 'Lodera', NULL),
(249, '1233402004.8', '767485RY1A', 'Nissan Mexicana, S.A. de C.V.', 'PROTR-RR WH, RH', 'Lodera', NULL),
(250, '1233402005.8', '767495RY1A', 'Nissan Mexicana, S.A. de C.V.', 'Protr-RR WH, LH', 'Lodera', NULL),
(251, '8702102001.01', '678109MM0A', 'Nissan Mexicana, S.A. de C.V.', 'INSUL-DASH LWR,FR', 'Insulators', NULL),
(252, '8702102002.01', '678109MM1A', 'Nissan Mexicana, S.A. de C.V.', 'INSUL-DASH LWR,FR', 'Insulators', NULL),
(253, '8702102003.01', '678119MM1A', 'Nissan Mexicana, S.A. de C.V.', 'INSUL-DASH LWR,FR UPR', 'Insulators', NULL),
(254, '8702102004.01', '678969MM0A', 'Nissan Mexicana, S.A. de C.V.', 'INSUL-DASH LWR, RH', 'Insulators', NULL),
(255, '8702101001.01', '658409MM0A', 'Nissan Mexicana, S.A. de C.V.', 'INSUL-HOOD', 'Insulators', NULL),
(256, '8702101002.01', '658409MM1A', 'Nissan Mexicana, S.A. de C.V.', 'INSUL-HOOD', 'Insulators', NULL),
(257, '1221206001.01', '748819KZ0A', 'Nissan Mexicana, S.A. de C.V.', 'Insul FR Floor FR CTR', 'Asfaltos Magneticos', NULL),
(258, '1207205012.01', '673565RW0A', 'Nissan Mexicana, S.A. de C.V.', 'Insul-Fusible, Dash L WR', 'Asfaltos Magneticos', NULL),
(259, '1207101002.03', '658405EF0A', 'Nissan Mexicana, S.A. de C.V.', 'INSUL-HOOD', 'Insulators', NULL),
(260, '1207402001.02', '767485EF0B', 'Nissan Mexicana, S.A. de C.V.', 'PROTR-RR WH RH', 'Lodera', NULL),
(261, '1207402002.02', '767495EF0B', 'Nissan Mexicana, S.A. de C.V.', 'PROTR-RR WH LH', 'Lodera', NULL),
(262, '1213402001.01', '767487SF0A', 'Nissan Mexicana, S.A. de C.V.', 'PROTR-RR WH, RH', 'Lodera', NULL),
(263, '1213402002.01', '767497SF0A', 'Nissan Mexicana, S.A. de C.V.', 'Protr-RR WH, LH', 'Lodera', NULL),
(264, '1213101001.01', '658407SF0A', 'Nissan Mexicana, S.A. de C.V.', 'INSUL-HOOD', 'Insulators', NULL),
(265, '1224205009.01', '748868AM0A', 'Nissan Mexicana, S.A. de C.V.', 'INSUL-FR FLOOR RR LH', 'Asfaltos', NULL),
(266, '1207306001.01', '748895RB0A', 'Nissan Mexicana, S.A. de C.V.', 'P02F Insul --RR Floor, RR RH', 'Asfaltos', NULL),
(267, '1208205005.01', '74882EL000', 'Nissan Mexicana, S.A. de C.V.', 'B/V C Insul fr floor fr', 'Asfaltos', NULL),
(268, '1225307001.01', '849669KS0A', 'Nissan Mexicana, S.A. de C.V.', 'L02B  Fin--trunk Lid', 'Porta Equipaje', NULL),
(269, '1225307002.01', '849669KS1A', 'Nissan Mexicana, S.A. de C.V.', 'L02B  Fin--trunk Lid', 'Porta Equipaje', NULL),
(270, '1208302003.01', '74868EL00A', 'Nissan Mexicana, S.A. de C.V.', 'X11C/L12F Insul rr Floor Side1', 'Asfaltos', NULL),
(271, '1208302004.01', '74868EL000', 'Nissan Mexicana, S.A. de C.V.', 'X11C/L12F Insul fr Floor fr', 'Asfaltos', NULL),
(272, '1227205001.01', '673569AM0A', 'Nissan Mexicana, S.A. de C.V.', 'L12F Insul-Fusible, Dash Lwr', 'Asfaltos Magneticos', NULL),
(273, '1227205002.01', '673569AM0B', 'Nissan Mexicana, S.A. de C.V.', 'L12F Insul-Fusible, Dash Lwr', 'Asfaltos Magneticos', NULL),
(274, '1207205005.01', '673565RL0A', 'Nissan Mexicana, S.A. de C.V.', 'P02F Insul- Dash', 'Asfaltos Magneticos', NULL),
(275, '1233306001.01', '748895RL0A', 'Nissan Mexicana, S.A. de C.V.', 'Insul-RR Floor,RR LH', 'Asfaltos', NULL),
(276, '1224205003.01', '748554KH0A', 'Nissan Mexicana, S.A. de C.V.', 'H60A Insul Floor Fr Fr Si', 'Asfaltos', NULL),
(277, '1224205005.01', '748864KH0A', 'Nissan Mexicana, S.A. de C.V.', 'H60A Insul Fr Floor Rr Lh', 'Asfaltos', NULL),
(278, '1224205007.01', '748864KH1A', 'Nissan Mexicana, S.A. de C.V.', 'H60A Insul Fr Floor Rr Rh', 'Asfaltos', NULL),
(279, '1224212001.01', '769964KH2B', 'Nissan Mexicana, S.A. de C.V.', 'H60A- Lid Assy  RH', 'Interiores', NULL),
(280, '1228205002.01', '748853LM0C', 'Nissan Mexicana, S.A. de C.V.', 'X11M Insul Fr Floor Ctr Rh', 'Asfaltos', NULL),
(281, '1228205003.01', '748883LN0B', 'Nissan Mexicana, S.A. de C.V.', 'X11M Insul Rr Floor Ctr', 'Asfaltos', NULL),
(282, '1228205004.01', '748883LN0A', 'Nissan Mexicana, S.A. de C.V.', 'X11M Insul Rr Floor Ctr', 'Asfaltos', NULL),
(283, '3702213020.01', '1050419-00-A', 'Tesla Motors Netherlands B.V.', 'MOD/S Fender Insulator LH', 'Fender', NULL),
(284, '3702213021.01', '1050420-00-A', 'Tesla Motors Netherlands B.V.', 'MOD/S Fender Insulator RH', 'Fender', NULL),
(285, '3702213021.01', '1050420-00-A', 'Tesla Motors Netherlands B.V.', 'MOD/S Fender Insulator RH', 'Fender', NULL),
(286, '3702403001.01', '1037731-00-B', 'Tesla Motors Netherlands B.V.', 'FENDER BAFFLE LH MX', 'Fender', NULL),
(287, '3702403002.01', '1037732-00-B', 'Tesla Motors Netherlands B.V.', 'FENDER BAFFLE RH MX', 'Fender', NULL),
(288, '1606312001.02', '86269 20 001 01', 'Nissan Mexicana, S.A. de C.V.', 'VW37X   Rear Pnl linging 753', 'Respaldo', NULL),
(289, '1606312001.02', '86269 20 001 01', 'VRK Automotive Systems SA de CV', 'VW37X   Rear Pnl linging 753', 'Respaldo', NULL),
(290, '1606312004.02', '86271 20 001 01', 'VRK Automotive Systems SA de CV', 'VW37X   Rear Pnl linging 754', 'Respaldo', NULL),
(291, '16B5212002', '36185', 'PROMA AUTOMOTIVE DE MEXICO', 'Lining Rear Panel', 'Respaldo', NULL),
(292, '16B5212001', '36186', 'PROMA AUTOMOTIVE DE MEXICO', 'Lining Rear Panel', 'Respaldo', NULL),
(293, '482403001.02', 'M1PB S16E560 AB', 'Ford Motor Company S.A.de C.V.', 'INS FRT FNDR RR SDN', 'Fender', NULL),
(294, '482403002.02', 'M1PB S16E561 AB', 'Ford Motor Company S.A.de C.V.', 'INS FRT FNDR RR SDN', 'Fender', NULL),
(295, '484403001.01', 'NZ6B E16E561 AA', 'Ford Motor Company S.A.de C.V.', 'INS FRT FNDR RR SDN', 'Fender', NULL),
(296, '484403002.01', 'NZ6B E16E560 AA', 'Ford Motor Company S.A.de C.V.', 'INS FRT FNDR RR SDN', 'Fender', NULL),
(297, '445403001.01', 'LJ6B S16E560 AA', 'Ford Motor Company S.A.de C.V.', 'INS FRT FNDR RR SND RH', 'Fender', NULL),
(298, '445403002.01', 'LJ6B S16E561 AA', 'Ford Motor Company S.A.de C.V.', 'INS FRT FNDR RR SND LH', 'Fender', NULL),
(299, '445403003.01', '445403003.01', 'Ford Motor Company S.A.de C.V.', 'INS FRT FNDR RR SND LH', 'Fender', NULL),
(300, '445403004.01', 'LJ7B S16E560 AA', 'Ford Motor Company S.A.de C.V.', 'INS FRT FNDR RR SND RH', 'Fender', NULL),
(301, '481405009.03', 'LJ8B 111E66 AC', 'Ford Motor Company S.A.de C.V.', 'DEFL RR FLR PAN AIR', 'Insulators', NULL),
(302, '481405010.03', 'LJ8B 111E66 BC', 'Ford Motor Company S.A.de C.V.', 'DEFL RR FLR PAN AIR', 'Insulators', NULL),
(303, '481403001.04', 'LJ8B R16E560 BA', 'Ford Motor Company S.A.de C.V.', 'INS FRT FNDR RR SND', 'Fender', NULL),
(304, '481403002.04', 'LJ8B R16E561 BA', 'Ford Motor Company S.A.de C.V.', 'INS FRT FNDR RR SND LH', 'Fender', NULL),
(305, '481405003.07', 'LJ8B-11782-DA', 'Ford Motor Company S.A.de C.V.', 'DEFL ASY RR AIR', 'Insulators', NULL),
(306, '481405004.08', 'LJ8B 11782 DB', 'Ford Motor Company S.A.de C.V.', 'DEFL ASY RR AIR', 'Insulators', NULL),
(307, '481103001.02', 'LJ9B 6B629 AB', 'Ford Motor Company S.A.de C.V.', 'SHLD ASY ENG SPLS', 'Insulators', NULL),
(308, '481103003.02', 'LK9B 6B629 BB', 'Ford Motor Company S.A.de C.V.', 'TITLE SHLD ASY ENG SPLS', 'Insulators', NULL),
(309, '481402001.05', 'LJ8B 278B50 AD', 'Ford Motor Company S.A.de C.V.', 'FILL QTR WHL/HS RH', 'Lodera', NULL),
(310, '481402004.05', 'LJ8B 278B51 AD', 'Ford Motor Company S.A.de C.V.', 'FILL QTR WHL/HS LH', 'Lodera', NULL),
(311, '481405001.01', 'LJ9B 11779 AA', 'Ford Motor Company S.A.de C.V.', 'DELF RR AIR', 'Insulators', NULL),
(312, '481405002.01', 'LJ9B 11778 AA', 'Ford Motor Company S.A.de C.V.', 'DELF RR AIR', 'Insulators', NULL),
(313, '481402003.04', 'LJ8B 16034 AD', 'Ford Motor Company S.A.de C.V.', 'SHLD ASY FRT FNDR APR SPLS', 'Lodera', NULL),
(314, '481402002.04', 'LJ8B 16035 AD', 'Ford Motor Company S.A.de C.V.', 'SHLD ASY FRT FNDR APR SPLS LH', 'Lodera', NULL),
(315, '237402014.02', 'A 2476901703', 'Daimler - Mercedes Benz', 'ASSY Wheel House Covering RR LH', 'Lodera', NULL),
(316, '237402016.02', 'A 2476901803', 'Daimler - Mercedes Benz', 'ASSY Wheel House Covering RR RH', 'Lodera', NULL),
(317, '1698201001.03', '17B 863 367 C', 'Volkswagen de Mexico, S.A. de C.V.', 'Floor Covering', 'Alfombra de Piso', NULL),
(318, '1698201006.01', '17B 863 367 D', 'Volkswagen de Mexico, S.A. de C.V.', 'Assy Floor Covering', 'Alfombra de Piso', NULL),
(319, '1698402006.02', '17A 810 971 A', 'Volkswagen de Mexico, S.A. de C.V.', 'Wheel Housing Liner Rear LH', 'Lodera', NULL),
(320, '1698402007.03', '17A 810 972 B', 'Volkswagen de Mexico, S.A. de C.V.', 'Wheel Housing, Rear RH', 'Lodera', NULL),
(321, '1665402007.04', '2GJ 810 971 B', 'Volkswagen de Mexico, S.A. de C.V.', 'Wheel housing liner, rear', 'Lodera', NULL),
(322, '1665402008.03', '1665402008.03', 'Volkswagen de Mexico, S.A. de C.V.', 'Wheel housing liner, rear', 'Lodera', NULL),
(323, '16B5402001', '57N 810 971', 'Volkswagen de Mexico, S.A. de C.V.', 'Wheel Housing Liner Rear LH', 'Lodera', NULL),
(324, '16B5402002', '57N 810 972', 'Volkswagen de Mexico, S.A. de C.V.', 'Wheel Housing liner rear RH', 'Lodera', NULL),
(325, '16B5214007', '57N 863 011 A', 'Volkswagen de Mexico, S.A. de C.V.', 'luggage compartm (Set)', 'Tapetes', NULL),
(326, '1655311001.01', '3CM 863 007 B', 'Volkswagen Group of America', 'Carpet (Spare Wheel)', 'Cajuela', NULL),
(327, '1655311002.01', '3CM 863 007 C', 'Volkswagen Group of America', 'Carpet (Spare Wheel)', 'Cajuela', NULL),
(328, '16B3311016', '11K 863 544 B', 'Volkswagen Group of America', 'Trim Spare Wheel Well', 'Cajuela', NULL),
(329, '16B3311017', '11K 863 544 C', 'Volkswagen Group of America', 'Trim Spare Wheel Well', 'Cajuela', NULL),
(330, '3306103002.02', '32414699', 'Volvo', 'ENGINE UNDERSHIELD', 'Insulators', NULL),
(331, '3306402001.01', '32130902', 'Volvo', 'WHEEL HOUSING INNER REAR LEFT', 'Lodera', NULL),
(332, '3306402002.01', '32130903', 'Volvo', 'WHEEL HOUSING INNER REAR RIGHT', 'Lodera', NULL),
(333, '3317402002.01', '32417109', 'Volvo', 'LINER WHEELARCH REAR RIGHT', 'Lodera', NULL),
(334, '3317402001.01', '32417108', 'Volvo', 'LINER WHEELARCH REAR LEFT', 'Lodera', NULL),
(335, '3306103001.01', '32130536', 'Volvo', 'UNDERBODY PANEL CENTRE', 'Insulators', NULL),
(336, '3306102001.01', '32244676', 'Volvo', 'SOUND ABSORBER FIREWALL LOWER', 'Insulators', NULL),
(337, '3306109001.01', '32132816', 'Volvo', 'SOUND ABSORBER NVH WALL LOWER', 'Insulators', NULL),
(338, '6502303001.01', 'P11-N06600-04', 'Lucid Motors', 'Trunk Side Pocket', 'Cajuela', NULL),
(339, '6502303005.01', 'P11-NT09GN-04', 'Lucid Motors', 'Trunk Rear End Panel', 'Cajuela', NULL),
(340, '6502303007.01', 'P11-E753GN-06', 'Lucid Motors', 'Assy Front Carpet Frunk', 'Cajuela', NULL),
(341, '6502311005.02', 'P11-NT07GN-07', 'Lucid Motors', 'Trunk Upper Panel', 'Cajuela', NULL),
(342, '6502303002.01', 'P11-NT6300-06', 'Lucid Motors', 'Trunk Rear Floor', 'Cajuela', NULL),
(343, '6502303003.02', 'P11-NT05GN-07', 'Lucid Motors', 'Trunk Side Panel LH', 'Cajuela', NULL),
(344, '6502303004.02', 'P11-NT04GN-06', 'Lucid Motors', 'Trunk Side Panel RH', 'Cajuela', NULL),
(345, '6502303008.01', 'P11-E754GN-07', 'Lucid Motors', 'Assy Rear Carpet Frunk', 'Cajuela', NULL),
(346, '6502402002.03', 'P11-E725L0-05', 'Lucid Motors', 'Wheel Liner Rear LH', 'Lodera', NULL),
(347, '6502402001.03', 'P11-E725R0-05', 'Lucid Motors', 'Wheel Liner Rear RH', 'Lodera', NULL),
(348, '6502307002.01', 'P11-NT21GN-03', 'Lucid Motors', 'Service Lid', 'Cajuela', NULL),
(349, '6502312003.01', 'P11-N16100-01', 'Lucid Motors', 'Trunk Gap Hider', 'Cajuela', NULL),
(350, '6502312003.01', 'P11-N16100-01', 'Lucid Motors', 'Trunk Gap Hider', 'Cajuela', NULL),
(351, '6502303006.03', 'P11-NT01GN-08', 'Lucid Motors', 'Trunk Floor With Support DR5856', 'Cajuela', NULL),
(352, '6502307001.02', 'P11-NT03GN-06', 'Lucid Motors', 'Trunk Deck Lid', 'Cajuela', NULL),
(353, '6502311001.01', 'P11-NN0800-02', 'Lucid Motors', 'Trunk Insulator, Rear', 'Cajuela', NULL),
(354, '6502311004.01', 'P11-NN1100-05', 'Lucid Motors', 'Trunk Floor Insulator', 'Cajuela', NULL),
(355, '6502311002.01', 'P11-NN0900-03', 'Lucid Motors', 'Trunk Side Insulator L', 'Cajuela', NULL),
(356, '6502311003.01', 'P11-NN1000-03', 'Lucid Motors', 'Trunk Side Insulator R', 'Cajuela', NULL),
(357, '6502402003.01', 'P1R-E72520-00', 'Lucid Motors', 'Wheel Liner Rear RH', 'Lodera', NULL),
(358, '6502402004.01', 'P1L-E72520-00', 'Lucid Motors', 'Wheel Liner Rear LH', 'Lodera', NULL),
(359, '6501311001.02', 'P21-NN0500-02', 'Lucid Motors', 'TRUNK INSULATOR LH', 'Cajuela', NULL),
(360, '6501311002.02', 'P21-NN0600-02', 'Lucid Motors', 'TRUNK INSULATOR RH', 'Cajuela', NULL),
(361, '6501104001.01', 'P21-NN0200-01', 'Lucid Motors', 'TUNNEL INSULATOR', 'Insulators', NULL),
(362, '6501108001.01', 'P21-WH0100-01', 'Lucid Motors', 'FDU JACKET', 'Insulators', NULL),
(363, '6501107001.02', 'P21-NN0700-02', 'Lucid Motors', 'MOTORBAY INNER INSULATOR 5 SEATER', 'Insulators', NULL),
(364, '6501107003.02', 'P21-NN0900-02', 'Lucid Motors', 'MOTORBAY INNER INSULATOR 6/7 SEATER', 'Insulators', NULL),
(365, '6501304001.02', 'P21-NN0300-02', 'Lucid Motors', 'WHEELHOUSE INSULATOR LH', 'Insulators', NULL),
(366, '6501304002.02', 'P21-NN0400-02', 'Lucid Motors', 'WHEELHOUSE INSULATOR RH', 'Insulators', NULL),
(367, '6501203001.03', 'P21-NN0100-03', 'Lucid Motors', 'DASHMAT INNER INSULATOR', 'Insulators', NULL),
(368, '6501102001.02', 'P21-WE0100-02', 'Lucid Motors', 'DASHMAT OUTER', 'Insulators', NULL),
(369, '6501107002.02', 'P21-WE0200-02', 'Lucid Motors', 'MOTOR BAY OUTER', 'Insulators', NULL),
(370, '6501311003.01', 'P21-NN0800-01', 'Lucid Motors', 'TRUNK FLOOR REAR INSULATOR', 'Cajuela', NULL),
(371, '1405501169.01', '1405501169.01', 'Pelzer de México SA de CV (Saltillo)', 'Alf. Backing Luggage Covering Carpet BL7', 'Alfombra', NULL),
(372, '1402503011.01', '1402503011.01', 'Pelzer de México SA de CV (Saltillo)', 'DJ MCA Fieltro + Alf. Nowoven CPN Bin L.', 'Insulators', NULL),
(373, '1405501164.1', '1405501164.1', 'Pelzer de México SA de CV (Saltillo)', 'Alfombra Nonwoven 100% PET VW TAOS', 'Alfombra', NULL),
(374, '1405501168', '1405501168', 'Pelzer de México SA de CV (Saltillo)', 'Nonwoven dilour carpet TDV 62U Schwarz 640 g/m²', 'Alfombra', NULL),
(375, '1405501168', '1405501168', 'Pelzer de México SA de CV (Saltillo)', 'Nonwoven dilour carpet 720 g/m² Soul TDV 8B5', 'Alfombra', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguimientos`
--

CREATE TABLE `seguimientos` (
  `id_seguimiento` int(11) NOT NULL,
  `id_auditoria` int(11) NOT NULL,
  `fila` varchar(20) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `acciones` text DEFAULT NULL,
  `fecha_seguimiento` date DEFAULT NULL,
  `nombre_archivo` text DEFAULT NULL,
  `ruta_archivo` text DEFAULT NULL,
  `fecha_subida` timestamp NOT NULL DEFAULT current_timestamp(),
  `responsable` varchar(100) DEFAULT NULL,
  `estatus_seguimiento` enum('Pendiente','En Proceso','Cerrado') DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguimiento_proceso`
--

CREATE TABLE `seguimiento_proceso` (
  `id_seguimiento` int(11) NOT NULL,
  `id_auditoria` int(11) NOT NULL,
  `fila` varchar(20) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `acciones` text DEFAULT NULL,
  `fecha_seguimiento` date DEFAULT NULL,
  `nombre_archivo` text DEFAULT NULL,
  `ruta_archivo` text DEFAULT NULL,
  `fecha_subida` timestamp NOT NULL DEFAULT current_timestamp(),
  `responsable` varchar(100) DEFAULT NULL,
  `estatus_seguimiento` enum('Pendiente','En Proceso','Cerrado') DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditorias`
--
ALTER TABLE `auditorias`
  ADD PRIMARY KEY (`id_auditoria`),
  ADD KEY `numero_empleado` (`numero_empleado`);

--
-- Indices de la tabla `auditoria_proceso`
--
ALTER TABLE `auditoria_proceso`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_auditoria_empleado` (`id_auditoria`,`numero_empleado`),
  ADD KEY `numero_empleado` (`numero_empleado`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`numero_empleado`);

--
-- Indices de la tabla `programar_auditoria`
--
ALTER TABLE `programar_auditoria`
  ADD PRIMARY KEY (`id_auditoria`),
  ADD KEY `fk_numero_empleado` (`numero_empleado`);

--
-- Indices de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `seguimientos`
--
ALTER TABLE `seguimientos`
  ADD PRIMARY KEY (`id_seguimiento`),
  ADD KEY `id_auditoria` (`id_auditoria`);

--
-- Indices de la tabla `seguimiento_proceso`
--
ALTER TABLE `seguimiento_proceso`
  ADD PRIMARY KEY (`id_seguimiento`),
  ADD KEY `id_auditoria` (`id_auditoria`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditoria_proceso`
--
ALTER TABLE `auditoria_proceso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT de la tabla `programar_auditoria`
--
ALTER TABLE `programar_auditoria`
  MODIFY `id_auditoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=226;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=376;

--
-- AUTO_INCREMENT de la tabla `seguimientos`
--
ALTER TABLE `seguimientos`
  MODIFY `id_seguimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `seguimiento_proceso`
--
ALTER TABLE `seguimiento_proceso`
  MODIFY `id_seguimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `auditorias`
--
ALTER TABLE `auditorias`
  ADD CONSTRAINT `auditorias_ibfk_1` FOREIGN KEY (`numero_empleado`) REFERENCES `empleados` (`numero_empleado`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_auditorias_programar_auditoria` FOREIGN KEY (`id_auditoria`) REFERENCES `programar_auditoria` (`id_auditoria`);

--
-- Filtros para la tabla `auditoria_proceso`
--
ALTER TABLE `auditoria_proceso`
  ADD CONSTRAINT `auditoria_proceso_ibfk_1` FOREIGN KEY (`numero_empleado`) REFERENCES `empleados` (`numero_empleado`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_auditoria_proceso_programar_auditoria` FOREIGN KEY (`id_auditoria`) REFERENCES `programar_auditoria` (`id_auditoria`);

--
-- Filtros para la tabla `programar_auditoria`
--
ALTER TABLE `programar_auditoria`
  ADD CONSTRAINT `fk_numero_empleado` FOREIGN KEY (`numero_empleado`) REFERENCES `empleados` (`numero_empleado`);

--
-- Filtros para la tabla `seguimientos`
--
ALTER TABLE `seguimientos`
  ADD CONSTRAINT `seguimientos_ibfk_1` FOREIGN KEY (`id_auditoria`) REFERENCES `auditorias` (`id_auditoria`) ON DELETE CASCADE;

--
-- Filtros para la tabla `seguimiento_proceso`
--
ALTER TABLE `seguimiento_proceso`
  ADD CONSTRAINT `seguimiento_proceso_ibfk_1` FOREIGN KEY (`id_auditoria`) REFERENCES `auditoria_proceso` (`id_auditoria`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
