-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 14, 2026 at 06:12 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `api-h2omanager`
--

-- --------------------------------------------------------

--
-- Table structure for table `abono_cxc`
--

CREATE TABLE `abono_cxc` (
  `id` bigint UNSIGNED NOT NULL,
  `cxc_id` bigint UNSIGNED NOT NULL,
  `pago_id` bigint UNSIGNED NOT NULL,
  `monto` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `abono_cxc`
--

INSERT INTO `abono_cxc` (`id`, `cxc_id`, `pago_id`, `monto`, `created_at`, `updated_at`) VALUES
(1, 2, 5, 25.00, '2026-03-15 14:00:00', '2026-03-15 14:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `asiento`
--

CREATE TABLE `asiento` (
  `id` bigint UNSIGNED NOT NULL,
  `fecha` datetime NOT NULL,
  `origen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'venta, compra, pago, ajuste',
  `referencia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sucursal_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `asiento`
--

INSERT INTO `asiento` (`id`, `fecha`, `origen`, `referencia`, `sucursal_id`, `created_at`, `updated_at`) VALUES
(1, '2026-03-01 23:59:59', 'venta', 'Cierre Diario Ventas 01-03', 1, '2026-03-02 03:59:59', '2026-03-02 03:59:59'),
(2, '2026-03-02 23:59:59', 'venta', 'Cierre Diario Ventas 02-03', 1, '2026-03-03 03:59:59', '2026-03-03 03:59:59');

-- --------------------------------------------------------

--
-- Table structure for table `asiento_detalle`
--

CREATE TABLE `asiento_detalle` (
  `id` bigint UNSIGNED NOT NULL,
  `asiento_id` bigint UNSIGNED NOT NULL,
  `cuenta_id` bigint UNSIGNED NOT NULL,
  `debe` decimal(15,2) NOT NULL DEFAULT '0.00',
  `haber` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `asiento_detalle`
--

INSERT INTO `asiento_detalle` (`id`, `asiento_id`, `cuenta_id`, `debe`, `haber`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2.00, 0.00, '2026-03-02 03:59:59', '2026-03-02 03:59:59'),
(2, 1, 2, 3.00, 0.00, '2026-03-02 03:59:59', '2026-03-02 03:59:59'),
(3, 1, 6, 0.00, 5.00, '2026-03-02 03:59:59', '2026-03-02 03:59:59'),
(4, 2, 3, 20.00, 0.00, '2026-03-03 03:59:59', '2026-03-03 03:59:59'),
(5, 2, 6, 0.00, 20.00, '2026-03-03 03:59:59', '2026-03-03 03:59:59');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cliente`
--

CREATE TABLE `cliente` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre_razon_social` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rif_ci` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Natural/Jurídico',
  `limite_credito` decimal(15,2) NOT NULL DEFAULT '0.00',
  `dias_credito` int NOT NULL DEFAULT '0',
  `saldo` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cliente`
--

INSERT INTO `cliente` (`id`, `nombre_razon_social`, `rif_ci`, `telefono`, `direccion`, `tipo`, `limite_credito`, `dias_credito`, `saldo`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Consumidor Final', 'V-00000000', 'N/A', 'N/A', 'Natural', 0.00, 0, 0.00, '2026-01-01 14:00:00', '2026-01-01 14:00:00', NULL),
(2, 'Juan Pérez', 'V-18123456', '0414-9876543', 'Urb. El Parral', 'Natural', 0.00, 0, 0.00, '2026-01-05 14:00:00', '2026-01-05 14:00:00', NULL),
(3, 'María Rodríguez', 'V-20111222', '0424-1234567', 'Cabudare Centro', 'Natural', 0.00, 0, 0.00, '2026-01-06 15:00:00', '2026-01-06 15:00:00', NULL),
(4, 'Panadería El Trigo C.A.', 'J-30111222-1', '0251-2334455', 'Av. Lara con Leones', 'Jurídico', 500.00, 15, 120.50, '2026-01-10 13:00:00', '2026-02-15 18:00:00', NULL),
(5, 'Clínica Salud Total', 'J-40555666-2', '0251-4445566', 'Av. Vargas', 'Jurídico', 1000.00, 30, 0.00, '2026-01-12 12:30:00', '2026-01-12 12:30:00', NULL),
(6, 'Gimnasio Fitness Pro', 'J-50111999-3', '0412-9998877', 'CC Las Trinitarias', 'Jurídico', 200.00, 7, 50.00, '2026-01-15 19:00:00', '2026-03-01 14:00:00', NULL),
(7, 'Tecnologías Avanzadas C.A. (Editado)', 'J-12345678-9', '+58 412-5551234', 'Dirección Fiscal 2.0', 'Jurídico', 6000.00, 45, 150.25, '2026-03-23 00:13:15', '2026-03-23 00:14:51', '2026-03-23 00:14:51');

-- --------------------------------------------------------

--
-- Table structure for table `cuenta_contable`
--

CREATE TABLE `cuenta_contable` (
  `id` bigint UNSIGNED NOT NULL,
  `codigo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Activo, Pasivo, Patrimonio, Ingreso, Egreso',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cuenta_contable`
--

INSERT INTO `cuenta_contable` (`id`, `codigo`, `nombre`, `tipo`, `created_at`, `updated_at`) VALUES
(1, '1.1.01', 'Caja General', 'Activo', '2026-01-01 12:00:00', '2026-01-01 12:00:00'),
(2, '1.1.02', 'Bancos Nacionales', 'Activo', '2026-01-01 12:00:00', '2026-01-01 12:00:00'),
(3, '1.1.03', 'Cuentas por Cobrar Clientes', 'Activo', '2026-01-01 12:00:00', '2026-01-01 12:00:00'),
(4, '1.1.04', 'Inventario de Mercancía', 'Activo', '2026-01-01 12:00:00', '2026-01-01 12:00:00'),
(5, '2.1.01', 'Cuentas por Pagar Proveedores', 'Pasivo', '2026-01-01 12:00:00', '2026-01-01 12:00:00'),
(6, '4.1.01', 'Ventas de Recarga de Agua', 'Ingreso', '2026-01-01 12:00:00', '2026-01-01 12:00:00'),
(7, '4.1.02', 'Ventas de Productos', 'Ingreso', '2026-01-01 12:00:00', '2026-01-01 12:00:00'),
(8, '5.1.01', 'Costo de Ventas', 'Egreso', '2026-01-01 12:00:00', '2026-01-01 12:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `cuenta_por_cobrar`
--

CREATE TABLE `cuenta_por_cobrar` (
  `id` bigint UNSIGNED NOT NULL,
  `cliente_id` bigint UNSIGNED NOT NULL,
  `doc_id` bigint UNSIGNED NOT NULL,
  `fecha` datetime NOT NULL,
  `vencimiento` datetime NOT NULL,
  `saldo` decimal(15,2) NOT NULL DEFAULT '0.00',
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cuenta_por_cobrar`
--

INSERT INTO `cuenta_por_cobrar` (`id`, `cliente_id`, `doc_id`, `fecha`, `vencimiento`, `saldo`, `estado`, `created_at`, `updated_at`) VALUES
(1, 4, 3, '2026-03-02 11:00:00', '2026-03-17 11:00:00', 20.00, 'PENDIENTE', '2026-03-02 15:00:00', '2026-03-02 15:00:00'),
(2, 5, 5, '2026-03-05 08:45:00', '2026-04-05 08:45:00', 25.00, 'PARCIAL', '2026-03-05 12:45:00', '2026-03-15 14:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `documento_detalle`
--

CREATE TABLE `documento_detalle` (
  `id` bigint UNSIGNED NOT NULL,
  `doc_id` bigint UNSIGNED NOT NULL,
  `item_id` bigint UNSIGNED NOT NULL,
  `tamano_id` bigint UNSIGNED DEFAULT NULL,
  `cantidad` decimal(15,2) NOT NULL,
  `precio_unit` decimal(15,2) NOT NULL,
  `iva_monto` decimal(15,2) NOT NULL,
  `total_linea` decimal(15,2) NOT NULL,
  `costo_estimado` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documento_detalle`
--

INSERT INTO `documento_detalle` (`id`, `doc_id`, `item_id`, `tamano_id`, `cantidad`, `precio_unit`, `iva_monto`, `total_linea`, `costo_estimado`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 2.00, 1.00, 0.00, 2.00, 0.15, '2026-03-01 13:15:00', '2026-03-01 13:15:00'),
(2, 2, 1, 1, 3.00, 1.00, 0.00, 3.00, 0.22, '2026-03-01 14:30:00', '2026-03-01 14:30:00'),
(3, 3, 1, 2, 20.00, 1.00, 0.00, 20.00, 1.50, '2026-03-02 15:00:00', '2026-03-02 15:00:00'),
(4, 4, 1, 2, 1.00, 1.20, 0.00, 1.20, 0.08, '2026-03-02 18:20:00', '2026-03-02 18:20:00'),
(5, 5, 1, 1, 50.00, 1.00, 0.00, 50.00, 3.50, '2026-03-05 12:45:00', '2026-03-05 12:45:00'),
(6, 6, 4, NULL, 1.00, 5.00, 0.80, 5.80, 3.00, '2026-03-10 20:00:00', '2026-03-10 20:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `documento_fiscal`
--

CREATE TABLE `documento_fiscal` (
  `id` bigint UNSIGNED NOT NULL,
  `sucursal_id` bigint UNSIGNED NOT NULL,
  `tipo_doc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Factura, Nota de Crédito',
  `serie_correlativo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` datetime NOT NULL,
  `cliente_id` bigint UNSIGNED NOT NULL,
  `condiciones_pago` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `iva` decimal(15,2) NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documento_fiscal`
--

INSERT INTO `documento_fiscal` (`id`, `sucursal_id`, `tipo_doc`, `serie_correlativo`, `fecha`, `cliente_id`, `condiciones_pago`, `subtotal`, `iva`, `total`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 'Factura', 'F01-00000001', '2026-03-01 09:15:00', 1, 'Contado', 2.00, 0.00, 2.00, 'PAGADA', '2026-03-01 13:15:00', '2026-03-01 13:15:00'),
(2, 1, 'Factura', 'F01-00000002', '2026-03-01 10:30:00', 2, 'Contado', 3.00, 0.00, 3.00, 'PAGADA', '2026-03-01 14:30:00', '2026-03-01 14:30:00'),
(3, 1, 'Factura', 'F01-00000003', '2026-03-02 11:00:00', 4, 'Crédito', 20.00, 0.00, 20.00, 'PENDIENTE', '2026-03-02 15:00:00', '2026-03-02 15:00:00'),
(4, 2, 'Factura', 'F02-00000001', '2026-03-02 14:20:00', 3, 'Contado', 1.20, 0.00, 1.20, 'PAGADA', '2026-03-02 18:20:00', '2026-03-02 18:20:00'),
(5, 1, 'Factura', 'F01-00000004', '2026-03-05 08:45:00', 5, 'Crédito', 50.00, 0.00, 50.00, 'PENDIENTE', '2026-03-05 12:45:00', '2026-03-05 12:45:00'),
(6, 1, 'Factura', 'F01-00000005', '2026-03-10 16:00:00', 1, 'Contado', 5.00, 0.80, 5.80, 'PAGADA', '2026-03-10 20:00:00', '2026-03-10 20:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `inventario_existencia`
--

CREATE TABLE `inventario_existencia` (
  `sucursal_id` bigint UNSIGNED NOT NULL,
  `item_id` bigint UNSIGNED NOT NULL,
  `cantidad_actual` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventario_existencia`
--

INSERT INTO `inventario_existencia` (`sucursal_id`, `item_id`, `cantidad_actual`) VALUES
(1, 1, 150.50),
(1, 2, 5000.00),
(1, 3, 5000.00),
(1, 4, 150.00),
(1, 5, 20.00),
(2, 2, 2000.00),
(2, 3, 2000.00),
(2, 4, 50.00),
(3, 2, 1500.00),
(3, 3, 1500.00);

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `id` bigint UNSIGNED NOT NULL,
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'PRODUCTO, SERVICIO, INSUMO',
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unidad_medida` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grava_iva` tinyint(1) NOT NULL DEFAULT '1',
  `stock_minimo` decimal(15,2) DEFAULT '0.00',
  `precio_sugerido` decimal(15,2) DEFAULT '0.00',
  `proveedor_id` bigint UNSIGNED NOT NULL,
  `cuenta_contable_venta_id` bigint UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`id`, `tipo`, `nombre`, `sku`, `unidad_medida`, `grava_iva`, `stock_minimo`, `precio_sugerido`, `proveedor_id`, `cuenta_contable_venta_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'SERVICIO', 'Recarga de Agua Purificada', 'SRV-REC-001', 'Litros', 0, 0.00, 5.00, 3, 6, NULL, '2026-01-01 13:00:00', '2026-01-01 13:00:00'),
(2, 'INSUMO', 'Tapa Plástica 55mm Azul', 'INS-TAP-001', 'Unidad', 1, 0.00, 3.00, 1, 7, NULL, '2026-01-01 13:00:00', '2026-01-01 13:00:00'),
(3, 'INSUMO', 'Sello Térmico de Seguridad', 'INS-SEL-001', 'Unidad', 1, 0.00, 4.50, 1, 7, NULL, '2026-01-01 13:00:00', '2026-01-01 13:00:00'),
(4, 'PRODUCTO', 'Botellón PET 18 Litros Nuevo', 'PRO-BOT-18L', 'Unidad', 1, 10.00, 2.22, 1, 7, NULL, '2026-01-01 13:00:00', '2026-01-01 13:00:00'),
(5, 'PRODUCTO', 'Dispensador de Agua Bomba Manual', 'PRO-DIS-MAN', 'Unidad', 1, 10.00, 3.50, 3, 7, NULL, '2026-01-01 13:00:00', '2026-01-01 13:00:00'),
(6, 'INSUMO', 'Filtro de Sedimentos 5 Micras', 'INS-FIL-005', 'Unidad', 1, 0.00, 1.25, 2, 7, NULL, '2026-01-01 13:00:00', '2026-01-01 13:00:00'),
(7, 'PRODUCTO', 'Recarga de Agua 20 Litros', 'AGUA-20L-RECARGA', 'BOT', 0, 200.00, 4.00, 3, 6, '2026-04-06 01:31:24', '2026-04-06 01:20:55', '2026-04-06 01:31:24');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_personal_access_tokens_table', 1),
(3, '2026_03_01_000001_create_sucursal_table', 1),
(4, '2026_03_01_000002_create_cliente_table', 1),
(5, '2026_03_01_000003_create_proveedor_table', 1),
(6, '2026_03_01_000004_create_cuenta_contable_table', 1),
(7, '2026_03_01_000005_create_rol_table', 1),
(8, '2026_03_01_000005_create_tamano_recarga_table', 1),
(9, '2026_03_01_000006_create_usuario_table', 1),
(10, '2026_03_01_000007_create_item_table', 1),
(11, '2026_03_01_000008_create_asiento_table', 1),
(12, '2026_03_01_000009_create_documento_fiscal_table', 1),
(13, '2026_03_01_000010_create_tarifa_recarga_table', 1),
(14, '2026_03_01_000011_create_inventario_existencia_table', 1),
(15, '2026_03_01_000012_create_movimiento_inventario_table', 1),
(16, '2026_03_01_000013_create_pago_table', 1),
(17, '2026_03_01_000014_create_cuenta_por_cobrar_table', 1),
(18, '2026_03_01_000015_create_asiento_detalle_table', 1),
(19, '2026_03_01_000017_create_documento_detalle_table', 1),
(20, '2026_03_01_000018_create_abono_cxc_table copy', 1);

-- --------------------------------------------------------

--
-- Table structure for table `movimiento_inventario`
--

CREATE TABLE `movimiento_inventario` (
  `id` bigint UNSIGNED NOT NULL,
  `fecha` datetime NOT NULL,
  `sucursal_id` bigint UNSIGNED NOT NULL,
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'compra, venta, ajuste, traslado, merma',
  `referencia_doc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usuario_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `movimiento_inventario`
--

INSERT INTO `movimiento_inventario` (`id`, `fecha`, `sucursal_id`, `tipo`, `referencia_doc`, `usuario_id`, `created_at`, `updated_at`) VALUES
(1, '2026-01-02 08:00:00', 1, 'compra', 'FAC-PROV-998', 1, '2026-01-02 12:00:00', '2026-01-02 12:00:00'),
(2, '2026-01-05 14:00:00', 2, 'traslado', 'TR-001', 2, '2026-01-05 18:00:00', '2026-01-05 18:00:00'),
(3, '2026-04-07 10:30:00', 1, 'compra', 'FACT-INV-2026-001', 1, '2026-04-08 02:53:17', '2026-04-08 02:53:17'),
(4, '2026-04-07 11:15:22', 1, 'venta', 'REF-CORREGIDA-99', 2, '2026-04-08 02:53:32', '2026-04-08 02:55:01'),
(5, '2026-04-07 11:15:22', 1, 'venta', 'TICKET-000452', 2, '2026-04-08 02:54:14', '2026-04-08 02:54:14'),
(6, '2026-04-07 16:00:00', 2, 'ajuste', NULL, 1, '2026-04-08 02:54:26', '2026-04-08 02:54:26');

-- --------------------------------------------------------

--
-- Table structure for table `movimiento_inventario_detalle`
--

CREATE TABLE `movimiento_inventario_detalle` (
  `id` bigint UNSIGNED NOT NULL,
  `mov_id` bigint UNSIGNED NOT NULL,
  `item_id` bigint UNSIGNED NOT NULL,
  `cantidad` decimal(15,2) NOT NULL,
  `costo_unitario` decimal(15,2) NOT NULL,
  `signo` int NOT NULL COMMENT '+ o -',
  `motivo` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `movimiento_inventario_detalle`
--

INSERT INTO `movimiento_inventario_detalle` (`id`, `mov_id`, `item_id`, `cantidad`, `costo_unitario`, `signo`, `motivo`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 5000.00, 0.05, 1, 'Compra inicial', '2026-01-02 12:00:00', '2026-01-02 12:00:00'),
(2, 1, 3, 5000.00, 0.02, 1, 'Compra inicial', '2026-01-02 12:00:00', '2026-01-02 12:00:00'),
(3, 2, 2, 2000.00, 0.05, 1, 'Recepción de principal', '2026-01-05 18:00:00', '2026-01-05 18:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `pago`
--

CREATE TABLE `pago` (
  `id` bigint UNSIGNED NOT NULL,
  `doc_id` bigint UNSIGNED NOT NULL,
  `fecha` datetime NOT NULL,
  `metodo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto` decimal(15,2) NOT NULL,
  `referencia_bancaria` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banco` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pago`
--

INSERT INTO `pago` (`id`, `doc_id`, `fecha`, `metodo`, `monto`, `referencia_bancaria`, `banco`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-03-01 09:15:00', 'Efectivo', 2.00, NULL, 'Caja', '2026-03-01 13:15:00', '2026-03-01 13:15:00'),
(2, 2, '2026-03-01 10:30:00', 'Pago Móvil', 3.00, '12345678', 'Banesco', '2026-03-01 14:30:00', '2026-03-01 14:30:00'),
(3, 4, '2026-03-02 14:20:00', 'Punto de Venta', 1.20, '009988', 'Mercantil', '2026-03-02 18:20:00', '2026-03-02 18:20:00'),
(4, 6, '2026-03-10 16:00:00', 'Efectivo USD', 5.80, NULL, 'Caja Fuerte', '2026-03-10 20:00:00', '2026-03-10 20:00:00'),
(5, 5, '2026-03-15 10:00:00', 'Transferencia', 25.00, 'TR-99998888', 'Provincial', '2026-03-15 14:00:00', '2026-03-15 14:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proveedor`
--

CREATE TABLE `proveedor` (
  `id` bigint UNSIGNED NOT NULL,
  `razon_social` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rif` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contacto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `proveedor`
--

INSERT INTO `proveedor` (`id`, `razon_social`, `rif`, `contacto`, `telefono`, `direccion`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Plásticos Nacionales C.A.', 'J-40001111-1', 'Carlos Ruiz', '0414-1112233', 'Zona Industrial Carabobo', NULL, '2026-01-01 13:00:00', '2026-01-01 13:00:00'),
(2, 'Químicos y Filtros S.A.', 'J-40002222-2', 'Ana Gómez', '0412-3334455', 'Av. Miranda, Caracas', NULL, '2026-01-01 13:00:00', '2026-01-01 13:00:00'),
(3, 'Suministros H2O', 'J-40003333-3', 'Pedro Pérez', '0424-5556677', 'Centro de Barquisimeto', NULL, '2026-01-01 13:00:00', '2026-01-01 13:00:00'),
(6, 'Distribuidora de Plásticos Occidente, C.A.', 'J-29405831-0', 'Ing. Ricardo Méndez', '0251-4423100', 'Zona Industrial II, Edificio H2O, Barquisimeto', '2026-04-05 01:03:34', '2026-04-05 01:03:24', '2026-04-05 01:03:34');

-- --------------------------------------------------------

--
-- Table structure for table `rol`
--

CREATE TABLE `rol` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rol`
--

INSERT INTO `rol` (`id`, `nombre`) VALUES
(1, 'Administrador'),
(2, 'Gerente de Sucursal'),
(3, 'Cajero'),
(4, 'Operario de Planta');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sucursal`
--

CREATE TABLE `sucursal` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rif` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `correlativos_doc` json NOT NULL COMMENT 'Almacena series y números actuales',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sucursal`
--

INSERT INTO `sucursal` (`id`, `nombre`, `rif`, `direccion`, `correlativos_doc`, `created_at`, `updated_at`) VALUES
(1, 'Sede Principal', 'J-12345678-0', 'Av. Centro, Edif. Agua Pura', '{\"Factura\": {\"serie\": \"F01\", \"numero\": 1500}, \"Nota de Crédito\": {\"serie\": \"NC01\", \"numero\": 50}}', '2026-01-01 12:00:00', '2026-01-01 12:00:00'),
(2, 'Sucursal Este', 'J-12345678-1', 'CC El Este, Nivel PB', '{\"Factura\": {\"serie\": \"F02\", \"numero\": 800}, \"Nota de Crédito\": {\"serie\": \"NC02\", \"numero\": 10}}', '2026-01-01 12:00:00', '2026-01-01 12:00:00'),
(3, 'Sucursal Oeste', 'J-12345678-2', 'Zona Industrial II, Galpón 4', '{\"Factura\": {\"serie\": \"F03\", \"numero\": 200}, \"Nota de Crédito\": {\"serie\": \"NC03\", \"numero\": 5}}', '2026-01-01 12:00:00', '2026-01-01 12:00:00'),
(4, 'Sucursal Norte - C.C. Sambil', 'J-50006666-1', 'Calle modificada 123', '{\"Factura\": {\"serie\": \"F04\", \"numero\": 1}, \"Nota de Crédito\": {\"serie\": \"NC04\", \"numero\": 1}}', '2026-03-23 01:16:39', '2026-03-23 01:41:09');

-- --------------------------------------------------------

--
-- Table structure for table `tamano_recarga`
--

CREATE TABLE `tamano_recarga` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ejemplo: 5L, 10L, 20L',
  `factor_consumo_agua` decimal(15,2) NOT NULL DEFAULT '0.00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tamano_recarga`
--

INSERT INTO `tamano_recarga` (`id`, `nombre`, `factor_consumo_agua`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Botellón 18 Litros', 18.00, NULL, '2026-01-01 14:00:00', '2026-01-01 14:00:00'),
(2, 'Botellón 20 Litros', 20.00, NULL, '2026-01-01 14:00:00', '2026-01-01 14:00:00'),
(3, 'Botellón 5 Litros', 5.00, NULL, '2026-01-01 14:00:00', '2026-01-01 14:00:00'),
(4, 'Termo / Vaso (1 Litro)', 1.00, NULL, '2026-01-01 14:00:00', '2026-01-01 14:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `tarifa_recarga`
--

CREATE TABLE `tarifa_recarga` (
  `id` bigint UNSIGNED NOT NULL,
  `tamano_id` bigint UNSIGNED NOT NULL,
  `precio` decimal(15,2) NOT NULL,
  `sucursal_id` bigint UNSIGNED DEFAULT NULL,
  `creado_por` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tarifa_recarga`
--

INSERT INTO `tarifa_recarga` (`id`, `tamano_id`, `precio`, `sucursal_id`, `creado_por`, `created_at`, `updated_at`) VALUES
(1, 1, 1.00, 1, 1, '2026-01-01 14:30:00', '2026-01-01 14:30:00'),
(2, 2, 1.20, 1, 1, '2026-01-01 14:30:00', '2026-01-01 14:30:00'),
(3, 3, 0.50, 1, 1, '2026-01-01 14:30:00', '2026-01-01 14:30:00'),
(4, 1, 1.00, 2, 1, '2026-01-01 14:30:00', '2026-01-01 14:30:00'),
(5, 2, 1.20, 2, 1, '2026-01-01 14:30:00', '2026-01-01 14:30:00'),
(6, 1, 0.80, 3, 1, '2026-01-01 14:30:00', '2026-01-01 14:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `usuario`
--

CREATE TABLE `usuario` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cedula` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol_id` bigint UNSIGNED NOT NULL,
  `sucursal_id` bigint UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `usuario`
--

INSERT INTO `usuario` (`id`, `nombre`, `email`, `cedula`, `password`, `rol_id`, `sucursal_id`, `deleted_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'admin@h2o.com', 'V-10000000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, NULL, NULL, '2026-01-01 12:00:00', '2026-01-01 12:00:00'),
(2, 'Gerente Principal', 'gerente1@h2o.com', 'V-12345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 1, NULL, NULL, '2026-01-01 12:00:00', '2026-01-01 12:00:00'),
(3, 'Cajero Principal', 'caja1@h2o.com', 'V-20123123', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 1, NULL, NULL, '2026-01-01 12:00:00', '2026-01-01 12:00:00'),
(4, 'Cajero Este', 'caja2@h2o.com', 'V-22333444', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 2, NULL, NULL, '2026-01-01 12:00:00', '2026-01-01 12:00:00'),
(5, 'Cajero Oeste', 'caja3@h2o.com', 'V-25666777', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 3, NULL, NULL, '2026-01-01 12:00:00', '2026-01-01 12:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abono_cxc`
--
ALTER TABLE `abono_cxc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `abono_cxc_cxc_id_foreign` (`cxc_id`),
  ADD KEY `abono_cxc_pago_id_foreign` (`pago_id`);

--
-- Indexes for table `asiento`
--
ALTER TABLE `asiento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asiento_sucursal_id_foreign` (`sucursal_id`);

--
-- Indexes for table `asiento_detalle`
--
ALTER TABLE `asiento_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asiento_detalle_asiento_id_foreign` (`asiento_id`),
  ADD KEY `asiento_detalle_cuenta_id_foreign` (`cuenta_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cliente_rif_ci_unique` (`rif_ci`);

--
-- Indexes for table `cuenta_contable`
--
ALTER TABLE `cuenta_contable`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cuenta_contable_codigo_unique` (`codigo`);

--
-- Indexes for table `cuenta_por_cobrar`
--
ALTER TABLE `cuenta_por_cobrar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cuenta_por_cobrar_cliente_id_foreign` (`cliente_id`),
  ADD KEY `cuenta_por_cobrar_doc_id_foreign` (`doc_id`);

--
-- Indexes for table `documento_detalle`
--
ALTER TABLE `documento_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `documento_detalle_doc_id_foreign` (`doc_id`),
  ADD KEY `documento_detalle_item_id_foreign` (`item_id`),
  ADD KEY `documento_detalle_tamano_id_foreign` (`tamano_id`);

--
-- Indexes for table `documento_fiscal`
--
ALTER TABLE `documento_fiscal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `documento_fiscal_sucursal_id_foreign` (`sucursal_id`),
  ADD KEY `documento_fiscal_cliente_id_foreign` (`cliente_id`);

--
-- Indexes for table `inventario_existencia`
--
ALTER TABLE `inventario_existencia`
  ADD PRIMARY KEY (`sucursal_id`,`item_id`),
  ADD KEY `inventario_existencia_item_id_foreign` (`item_id`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `item_sku_unique` (`sku`),
  ADD KEY `item_proveedor_id_foreign` (`proveedor_id`),
  ADD KEY `item_cuenta_contable_venta_id_foreign` (`cuenta_contable_venta_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movimiento_inventario`
--
ALTER TABLE `movimiento_inventario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movimiento_inventario_sucursal_id_foreign` (`sucursal_id`),
  ADD KEY `movimiento_inventario_usuario_id_foreign` (`usuario_id`);

--
-- Indexes for table `movimiento_inventario_detalle`
--
ALTER TABLE `movimiento_inventario_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movimiento_inventario_detalle_mov_id_foreign` (`mov_id`),
  ADD KEY `movimiento_inventario_detalle_item_id_foreign` (`item_id`);

--
-- Indexes for table `pago`
--
ALTER TABLE `pago`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pago_doc_id_foreign` (`doc_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `sucursal`
--
ALTER TABLE `sucursal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tamano_recarga`
--
ALTER TABLE `tamano_recarga`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tarifa_recarga`
--
ALTER TABLE `tarifa_recarga`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tarifa_recarga_tamano_id_foreign` (`tamano_id`),
  ADD KEY `tarifa_recarga_sucursal_id_foreign` (`sucursal_id`),
  ADD KEY `tarifa_recarga_creado_por_foreign` (`creado_por`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_cedula_unique` (`cedula`),
  ADD KEY `usuario_rol_id_foreign` (`rol_id`),
  ADD KEY `usuario_sucursal_id_foreign` (`sucursal_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `abono_cxc`
--
ALTER TABLE `abono_cxc`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `asiento`
--
ALTER TABLE `asiento`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `asiento_detalle`
--
ALTER TABLE `asiento_detalle`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cuenta_contable`
--
ALTER TABLE `cuenta_contable`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `cuenta_por_cobrar`
--
ALTER TABLE `cuenta_por_cobrar`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `documento_detalle`
--
ALTER TABLE `documento_detalle`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `documento_fiscal`
--
ALTER TABLE `documento_fiscal`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `movimiento_inventario`
--
ALTER TABLE `movimiento_inventario`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `movimiento_inventario_detalle`
--
ALTER TABLE `movimiento_inventario_detalle`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pago`
--
ALTER TABLE `pago`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rol`
--
ALTER TABLE `rol`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sucursal`
--
ALTER TABLE `sucursal`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tamano_recarga`
--
ALTER TABLE `tamano_recarga`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tarifa_recarga`
--
ALTER TABLE `tarifa_recarga`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `abono_cxc`
--
ALTER TABLE `abono_cxc`
  ADD CONSTRAINT `abono_cxc_cxc_id_foreign` FOREIGN KEY (`cxc_id`) REFERENCES `cuenta_por_cobrar` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `abono_cxc_pago_id_foreign` FOREIGN KEY (`pago_id`) REFERENCES `pago` (`id`);

--
-- Constraints for table `asiento`
--
ALTER TABLE `asiento`
  ADD CONSTRAINT `asiento_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursal` (`id`);

--
-- Constraints for table `asiento_detalle`
--
ALTER TABLE `asiento_detalle`
  ADD CONSTRAINT `asiento_detalle_asiento_id_foreign` FOREIGN KEY (`asiento_id`) REFERENCES `asiento` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asiento_detalle_cuenta_id_foreign` FOREIGN KEY (`cuenta_id`) REFERENCES `cuenta_contable` (`id`);

--
-- Constraints for table `cuenta_por_cobrar`
--
ALTER TABLE `cuenta_por_cobrar`
  ADD CONSTRAINT `cuenta_por_cobrar_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `cuenta_por_cobrar_doc_id_foreign` FOREIGN KEY (`doc_id`) REFERENCES `documento_fiscal` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `documento_detalle`
--
ALTER TABLE `documento_detalle`
  ADD CONSTRAINT `documento_detalle_doc_id_foreign` FOREIGN KEY (`doc_id`) REFERENCES `documento_fiscal` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documento_detalle_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`),
  ADD CONSTRAINT `documento_detalle_tamano_id_foreign` FOREIGN KEY (`tamano_id`) REFERENCES `tamano_recarga` (`id`);

--
-- Constraints for table `documento_fiscal`
--
ALTER TABLE `documento_fiscal`
  ADD CONSTRAINT `documento_fiscal_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`),
  ADD CONSTRAINT `documento_fiscal_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursal` (`id`);

--
-- Constraints for table `inventario_existencia`
--
ALTER TABLE `inventario_existencia`
  ADD CONSTRAINT `inventario_existencia_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`),
  ADD CONSTRAINT `inventario_existencia_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursal` (`id`);

--
-- Constraints for table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `item_cuenta_contable_venta_id_foreign` FOREIGN KEY (`cuenta_contable_venta_id`) REFERENCES `cuenta_contable` (`id`),
  ADD CONSTRAINT `item_proveedor_id_foreign` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedor` (`id`);

--
-- Constraints for table `movimiento_inventario`
--
ALTER TABLE `movimiento_inventario`
  ADD CONSTRAINT `movimiento_inventario_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursal` (`id`),
  ADD CONSTRAINT `movimiento_inventario_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`);

--
-- Constraints for table `movimiento_inventario_detalle`
--
ALTER TABLE `movimiento_inventario_detalle`
  ADD CONSTRAINT `movimiento_inventario_detalle_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`),
  ADD CONSTRAINT `movimiento_inventario_detalle_mov_id_foreign` FOREIGN KEY (`mov_id`) REFERENCES `movimiento_inventario` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pago`
--
ALTER TABLE `pago`
  ADD CONSTRAINT `pago_doc_id_foreign` FOREIGN KEY (`doc_id`) REFERENCES `documento_fiscal` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `tarifa_recarga`
--
ALTER TABLE `tarifa_recarga`
  ADD CONSTRAINT `tarifa_recarga_creado_por_foreign` FOREIGN KEY (`creado_por`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `tarifa_recarga_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursal` (`id`),
  ADD CONSTRAINT `tarifa_recarga_tamano_id_foreign` FOREIGN KEY (`tamano_id`) REFERENCES `tamano_recarga` (`id`);

--
-- Constraints for table `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_rol_id_foreign` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`id`),
  ADD CONSTRAINT `usuario_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursal` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
