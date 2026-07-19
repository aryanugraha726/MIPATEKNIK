-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 11, 2026 at 03:17 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mipateknik`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` varchar(7) NOT NULL,
  `nama_barang` varchar(20) DEFAULT NULL,
  `id_kategori` varchar(3) NOT NULL,
  `id_satuan` int(11) NOT NULL,
  `harga` int(11) DEFAULT NULL,
  `ket_barang` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `nama_barang`, `id_kategori`, `id_satuan`, `harga`, `ket_barang`) VALUES
('BC0001', 'SARUNG TANGAN KAIN', 'BC', 1, 3125, NULL),
('BC0002', 'SARUNG TANGAN KARET', 'BC', 1, 7000, NULL),
('BC0003', 'MATA GERINDA CUTTING', 'BC', 1, 3500, NULL),
('BC0004', 'FLAP DISC 4\" GERINDA', 'BC', 1, 65000, NULL),
('BC0005', 'FLAP DISC 4\" GERINDA', 'BC', 1, 65000, NULL),
('BC0006', 'MATA GERINDA POLES 4', 'BC', 1, 60000, NULL),
('BC0007', 'MATA GERINDA FLEXIBL', 'BC', 1, 15000, NULL),
('BC0008', 'ROLL LOCK DISC GRIT ', 'BC', 1, 15000, NULL),
('BC0009', 'ROLL LOCK DISC GRIT ', 'BC', 1, 15000, NULL),
('BC0010', 'ROLL LOCK DISC GRIT ', 'BC', 1, 15000, NULL),
('BC0011', 'ROLL LOCK DISC GRIT ', 'BC', 1, 15000, NULL),
('BC0012', 'FLAP WILL 25mm', 'BC', 1, 16000, NULL),
('BC0013', 'FLAP WILL P60 50mm', 'BC', 1, 50000, NULL),
('BC0014', 'FLAP WILL P120 50mm', 'BC', 1, 55000, NULL),
('BC0015', 'SCOTH BRITE MERAH', 'BC', 1, 16000, NULL),
('BC0016', 'SCOTH BRITE HIJAU', 'BC', 1, 16000, NULL),
('BC0017', 'AMPLAS 80', 'BC', 2, 8750, NULL),
('BC0018', 'AMPLAS 120', 'BC', 2, 8750, NULL),
('BC0019', 'AMPLAS 240', 'BC', 2, 8750, NULL),
('BC0020', 'AMPLAS 400', 'BC', 2, 8750, NULL),
('BC0021', 'AMPLAS DUCO 500', 'BC', 1, 10000, NULL),
('BC0022', 'AMPLAS DUCO 800', 'BC', 1, 10000, NULL),
('BC0023', 'AMPLAS DUCO 1000', 'BC', 1, 10000, NULL),
('BC0024', 'AMPLAS DUCO 2000', 'BC', 1, 10000, NULL),
('BC0025', 'KOAS 1 INCH', 'BC', 1, 3750, NULL),
('BC0026', 'KOAS 2 INCH', 'BC', 1, 7500, NULL),
('BC0027', 'MINI CUP BRUS WIRE 1', 'BC', 1, 19000, NULL),
('BC0028', 'MINI CUP BRUS WIRE 3', 'BC', 1, 22000, NULL),
('BC0029', 'MINI CUP BRUS WIRE 5', 'BC', 1, 24000, NULL),
('BC0030', 'CUP BRUSH 63mm', 'BC', 1, 26000, NULL),
('BC0031', 'MATA GERGAJI SANDFLE', 'BC', 1, 0, NULL),
('BC0032', 'MATA GERGAJI SANDFLE', 'BC', 1, 0, NULL),
('BC0033', 'BRIGHT GAS', 'BC', 3, 25000, NULL),
('BC0034', 'SODA API', 'BC', 3, 0, NULL),
('BC0035', 'LEM KOREA', 'BC', 1, 10000, NULL),
('BC0036', 'GERINDA POLES SPONGE', 'BC', 1, 0, NULL),
('BC0037', 'RED PENETRANT', 'BC', 1, 110000, NULL),
('BC0038', 'DEVELOPER', 'BC', 1, 110000, NULL),
('BC0039', 'CLEANER', 'BC', 1, 110000, NULL),
('BC0040', 'WD TAYO', 'BC', 3, 60000, NULL),
('BC0041', 'WD 40 33ML', 'BC', 3, 90000, NULL),
('CBD0001', 'Mur 5/8', 'CBD', 1, 0, NULL),
('CBD0002', 'BAUD L 5/8 P.115mm', 'CBD', 1, 0, NULL),
('CBD0003', 'BAUD L 5/8 P.105mm', 'CBD', 1, 0, NULL),
('CBD0004', 'BAUD L 5/8 P.90mm', 'CBD', 1, 0, NULL),
('CBD0005', 'BAUD L 5/8 P.65mm', 'CBD', 1, 0, NULL),
('CBD0006', 'BAUD L 5/8 P.50mm', 'CBD', 1, 0, NULL),
('CBD0007', 'BAUD HEXA 5/8 P.115m', 'CBD', 1, 0, NULL),
('CBD0008', 'BAUD HEXA 5/8 P.85mm', 'CBD', 1, 0, NULL),
('CBD0009', 'BAUD HEXA 5/8 P.65mm', 'CBD', 1, 0, NULL),
('CBD0010', 'BAUD L 3/4 P.150mm', 'CBD', 1, 0, NULL),
('CBD0011', 'BAUD L 3/4 P.95mm', 'CBD', 1, 0, NULL),
('CBD0012', 'BAUD L 3/4 P.70mm', 'CBD', 1, 0, NULL),
('CBD0013', 'BAUD HEXA 3/4 P.150m', 'CBD', 1, 0, NULL),
('CBD0014', 'BAUD HEXA 3/4 P.65mm', 'CBD', 1, 0, NULL),
('CBD0015', 'BAUD L 3/8 16G P.60m', 'CBD', 1, 0, NULL),
('CBD0016', 'BAUD HEXA 3/8 16G P.', 'CBD', 1, 0, NULL),
('CBD0017', 'BAUD INBUS 3/8 16G P', 'CBD', 1, 0, NULL),
('CBD0018', 'BAUD INBUS 7/16 P.50', 'CBD', 1, 0, NULL),
('CBD0019', 'BAUD INBUS 1/2 13G P', 'CBD', 1, 0, NULL),
('CBD0020', 'BAUD L 1/2 13G P.40m', 'CBD', 1, 0, NULL),
('CBD0021', 'BAUD L 7/8 P.70mm', 'CBD', 1, 0, NULL),
('CBD0022', 'MUR M16x2.0', 'CBD', 1, 0, NULL),
('CBD0023', 'BAUD INBUS M16 P.45m', 'CBD', 1, 0, NULL),
('CBD0024', 'BAUD L M16 P.115mm', 'CBD', 1, 0, NULL),
('CBD0025', 'BAUD HEXA M16 P.60mm', 'CBD', 1, 0, NULL),
('CBD0026', 'BAUD INBUS M3 P.20mm', 'CBD', 1, 0, NULL),
('CBD0027', 'BAUD L 40mm P.40mm', 'CBD', 1, 0, NULL),
('CBD0028', 'BAUD L M4 P.10mm', 'CBD', 1, 0, NULL),
('CBD0029', 'BAUD L M4 P.30mm', 'CBD', 1, 0, NULL),
('CBD0030', 'BAUD L M4 P.35mm', 'CBD', 1, 0, NULL),
('CBD0031', 'BAUD L M4 P.20mm', 'CBD', 1, 0, NULL),
('CBD0032', 'BAUD INBUS M4 P.20mm', 'CBD', 1, 0, NULL),
('CBD0033', 'BAUD INBUS M5 P.20mm', 'CBD', 1, 0, NULL),
('CBD0034', 'BAUD L M5 P.20mm', 'CBD', 1, 0, NULL),
('CBD0035', 'BAUD INBUS M6 P.10mm', 'CBD', 1, 0, NULL),
('CBD0036', 'BAUD INBUS M6 P.20mm', 'CBD', 1, 0, NULL),
('CBD0037', 'BAUD L M6 P.', 'CBD', 1, 0, NULL),
('CBD0038', 'BAUD HEXA M6 P.45mm', 'CBD', 1, 0, NULL),
('CBD0039', 'MUR M6', 'CBD', 1, 0, NULL),
('CBD0040', 'BAUD L M8 P.60mm', 'CBD', 1, 0, NULL),
('CBD0041', 'BAUD L M8 P.70mm', 'CBD', 1, 0, NULL),
('CBD0042', 'BAUD HEXA M8 P.55mm', 'CBD', 1, 0, NULL),
('CBD0043', 'BAUD HEXA M8 P.45mm', 'CBD', 1, 0, NULL),
('CBD0044', 'BAUD HEA M8 P.20mm', 'CBD', 1, 0, NULL),
('CBD0045', 'MUR M8', 'CBD', 1, 0, NULL),
('CBD0046', 'BAUD L M10 P.70mm', 'CBD', 1, 0, NULL),
('CBD0047', 'BAUD L M10 P.50mm', 'CBD', 1, 0, NULL),
('CBD0048', 'BAUD L M10 P.40mm', 'CBD', 1, 0, NULL),
('CBD0049', 'BAUD INBUS M10 P.530', 'CBD', 1, 0, NULL),
('CBD0050', 'BAUD HEXA M10 P.45mm', 'CBD', 1, 0, NULL),
('CBD0051', 'BAUD HEXA M10 P.20mm', 'CBD', 1, 0, NULL),
('CBD0052', 'MUR M10', 'CBD', 1, 0, NULL),
('CBD0053', 'MUR 1/4', 'CBD', 1, 0, NULL),
('CBD0054', 'BAUD L M12 P.20mm', 'CBD', 1, 0, NULL),
('CBD0055', 'BAUD INBUS M12 P.40m', 'CBD', 1, 0, NULL),
('CBD0056', 'BAUD INBUS M12 P.30m', 'CBD', 1, 0, NULL),
('CBD0057', 'BAUD L M12 P.15mm', 'CBD', 1, 0, NULL),
('CBD0058', 'BAUD HEXA M12 P.20mm', 'CBD', 1, 0, NULL),
('CCR0001', 'OLI BOR', 'CCR', 10, 30000, NULL),
('CCR0002', 'THINER', 'CCR', 10, 25000, NULL),
('CCR0003', 'SOLAR', 'CCR', 10, 14000, NULL),
('CCR0004', 'CAT KANGGURU', 'CCR', 3, 220000, NULL),
('CMG0001', 'INSERT VCMT 160404', 'CMG', 1, 9000, NULL),
('CMG0002', 'INSERT ALUR VBMT/VCM', 'CMG', 1, 42000, NULL),
('CMG0003', 'INSERT ALUR 2mm MGMN', 'CMG', 1, 0, NULL),
('CMG0004', 'INSERT ALUR 3mm MGMN', 'CMG', 1, 0, NULL),
('CMG0005', 'INSERT ALUR 4mm SGS ', 'CMG', 1, 0, NULL),
('CMG0006', 'INSERT RADIUS 5mm RP', 'CMG', 1, 0, NULL),
('CMG0007', 'INSERT DRAT DALAM 16', 'CMG', 1, 0, NULL),
('CMG0008', 'INSERT DRAT LUAR 16 ', 'CMG', 1, 0, NULL),
('CMG0009', 'INSERT DRAT LUAR 22 ', 'CMG', 1, 0, NULL),
('CMG0010', 'INSERT CCMT 060404 P', 'CMG', 1, 0, NULL),
('CMG0011', 'INSERT FACE MILL (MI', 'CMG', 1, 0, NULL),
('CMG0012', 'INSERT SDJCR 1010 H ', 'CMG', 1, 0, NULL),
('CMG0013', 'INSERT SDJCR 1010 H ', 'CMG', 1, 0, NULL),
('CMG0014', 'PISAU HSS 1/4\"x1/4\"x', 'CMG', 1, 75000, NULL),
('CMG0015', 'PISAU HSS 3/8\"x3/8\"x', 'CMG', 1, 105000, NULL),
('CMG0016', 'PISAU HSS 3/8\"x3/8\"x', 'CMG', 1, 0, NULL),
('CMG0017', 'PISAU HSS 1/2\"x1/2\"x', 'CMG', 1, 145000, NULL),
('CMG0018', 'PISAU CARBIDE ø14mm', 'CMG', 1, 0, NULL),
('CMG0019', 'INSERT CARBIDE TNG22', 'CMG', 1, 50000, NULL),
('CMG0020', 'WIDIA SEGITIGA YG66', 'CMG', 1, 0, NULL),
('CMG0021', 'WIDIA KANAN', 'CMG', 1, 0, NULL),
('CMG0022', 'BORAX', 'CMG', 9, 0, NULL),
('CMG0023', 'TAP MATIC', 'CMG', 3, 163760, NULL),
('CMK0001', 'GREASE', 'CMK', 1, 0, NULL),
('CMK0002', 'PERMATEX INDIAN HEAD', 'CMK', 1, 75000, NULL),
('CMK0003', 'EAR PLUG 3M', 'CMK', 1, 30000, NULL),
('CMK0004', 'SHIM PLAT 316 0,05 3', 'CMK', 6, 550000, NULL),
('CMK0005', 'SHIM PLAT 316 0,1 30', 'CMK', 6, 495000, NULL),
('CMK0006', 'SHIM PLAT 316 0,2 30', 'CMK', 6, 495000, NULL),
('CMK0007', 'SHIM PLAT 316 0,3 30', 'CMK', 6, 440000, NULL),
('CMK0008', 'SHIM PLAT 316 0,4 30', 'CMK', 6, 430000, NULL),
('CMK0009', 'SHIM PLAT 316 0,5 30', 'CMK', 6, 0, NULL),
('CMK0010', 'SHIM PLAT KUNINGAN 0', 'CMK', 6, 235000, NULL),
('CMK0011', 'SHIM PLAT KUNINGAN 0', 'CMK', 6, 235000, NULL),
('CMK0012', 'SHIM PLAT KUNINGAN 0', 'CMK', 6, 0, NULL),
('CMK0013', 'SHIM PLAT KUNINGAN 0', 'CMK', 6, 0, NULL),
('CMK0014', 'SHIM PLAT KUNINGAN 0', 'CMK', 6, 0, NULL),
('CMK0015', 'SHIM PLAT KUNINGAN 0', 'CMK', 6, 0, NULL),
('CMK0016', 'RED SILICON', 'CMK', 1, 65000, NULL),
('CMK0017', 'BLUE CHECK', 'CMK', 1, 186000, NULL),
('CMK0018', 'PLASTI GAUGE MERAH', 'CMK', 7, 175000, NULL),
('CMK0019', 'PLASTI GAUGE HIJAU', 'CMK', 7, 250000, NULL),
('CMK0020', 'MOLYKOTE', 'CMK', 3, 0, NULL),
('CMK0021', 'LIT WIRE', 'CMK', 8, 2500000, NULL),
('CMK0022', 'ANTI SIZE', 'CMK', 3, 615000, NULL),
('CMK0023', 'PERMATEX COPALTITE', 'CMK', 3, 0, NULL),
('CMK0024', 'ASBESTOS (ANTI PANAS', 'CMK', 2, 0, NULL),
('CMK0025', 'BLUE MARKER', 'CMK', 3, 475000, NULL),
('CPG0001', 'LAKBAN COKLAT', 'CPG', 1, 10350, NULL),
('CPG0002', 'LAKBAN KERTAS', 'CPG', 1, 7900, NULL),
('CPG0003', 'LAKBAN HITAM', 'CPG', 1, 15895, NULL),
('CPG0004', 'LAKBAN FRAGILE MERAH', 'CPG', 1, 16000, NULL),
('CPG0005', 'LAKBAN FRAGILE PUTIH', 'CPG', 1, 16000, NULL),
('CPG0006', 'LAKBAN BENING', 'CPG', 1, 10350, NULL),
('CPG0007', 'LAKBAN MERAH', 'CPG', 1, 15000, NULL),
('CPG0008', 'PLASTIK WRAPPING 5CM', 'CPG', 1, 10200, NULL),
('CPG0009', 'PLASTIK WRAPPING 10C', 'CPG', 1, 24900, NULL),
('CPG0010', 'PLASTIK WRAPPING 60C', 'CPG', 1, 65000, NULL),
('CPG0011', 'PLASTIK OBAT/CLIP 16', 'CPG', 7, 14000, NULL),
('CPG0012', 'PLASTIK OBAT/CLIP 10', 'CPG', 7, 10000, NULL),
('CPG0013', 'PLASTIK OBAT/CLIP 4x', 'CPG', 7, 3000, NULL),
('CPG0014', 'DOUBLETAPE 3M', 'CPG', 1, 12000, NULL),
('CPG0015', 'SOLASI LISTRIK', 'CPG', 1, 10000, NULL),
('CWL0001', 'TUNGSTEN 3,2 mm Mera', 'CWL', 1, 35000, NULL),
('CWL0002', 'TUNGSTEN 2,4 mm Mera', 'CWL', 1, 25000, NULL),
('CWL0003', 'TUNGSTEN 1,6 mm Mera', 'CWL', 1, 20000, NULL),
('CWL0004', 'TUNGSTEN 2,4 mm HIJA', 'CWL', 1, 40000, NULL),
('CWL0005', 'TUNGSTEN 1,6 mm HIJA', 'CWL', 1, 26000, NULL),
('CWL0006', 'FILLER ER 312 1,6mm', 'CWL', 4, 0, NULL),
('CWL0007', 'FILLER ER 312 2,4mm', 'CWL', 4, 0, NULL),
('CWL0008', 'FILLER ER 316 1,6mm', 'CWL', 4, 0, NULL),
('CWL0009', 'FILLER ER 316 2,4mm', 'CWL', 4, 0, NULL),
('CWL0010', 'FILLER ER 70 1,6mm', 'CWL', 4, 135000, NULL),
('CWL0011', 'FILLER ER 70 2,4mm', 'CWL', 4, 0, NULL),
('CWL0012', 'FILLER ER 410 1,4mm', 'CWL', 4, 0, NULL),
('CWL0013', 'FILLER ER 410 2,4mm', 'CWL', 4, 0, NULL),
('CWL0014', 'FILLER ER 90 1,6mm', 'CWL', 4, 0, NULL),
('CWL0015', 'FILLER ER 90 2,4mm', 'CWL', 4, 0, NULL),
('CWL0016', 'FILLER ALMUNIUM 5350', 'CWL', 4, 0, NULL),
('CWL0017', 'FILLER ALMUNIUM 4048', 'CWL', 4, 0, NULL),
('CWL0018', 'FILLER ALBRONZE', 'CWL', 4, 0, NULL),
('CWL0019', 'FILLER BRONZE', 'CWL', 4, 0, NULL),
('CWL0020', 'FILLER NUZZALLOY', 'CWL', 4, 0, NULL),
('CWL0021', 'FILLER BABBIT MEDCO', 'CWL', 4, 1800000, NULL),
('CWL0022', 'FILLER ZENNIT', 'CWL', 5, 2400000, NULL),
('CWL0023', 'TINNING JHONSON', 'CWL', 3, 2000000, NULL),
('CWL0024', 'COLLET ARGON 1,6mm', 'CWL', 1, 0, NULL),
('CWL0025', 'COLLET ARGON 2,4mm', 'CWL', 1, 0, NULL),
('CWL0026', 'COLLET ARGON 3,2mm', 'CWL', 1, 0, NULL),
('CWL0027', 'COLLET ARGON 2,4mm S', 'CWL', 1, 0, NULL),
('CWL0028', 'BODY COLLET ARGON 4m', 'CWL', 1, 0, NULL),
('CWL0029', 'BODY COLLET 1,6mm ', 'CWL', 1, 0, NULL),
('CWL0030', 'GASLENS COLLET BODY', 'CWL', 1, 0, NULL),
('CWL0031', 'SIKAT KAWAT BAJA UNI', 'CWL', 1, 45000, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `barang_keluar`
--

CREATE TABLE `barang_keluar` (
  `id_keluar` int(11) NOT NULL,
  `id_barang` varchar(7) NOT NULL,
  `tgl_keluar` date DEFAULT NULL,
  `project_id` int(11) NOT NULL,
  `jumlah_keluar` int(11) DEFAULT NULL,
  `ket_keluar` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barang_masuk`
--

CREATE TABLE `barang_masuk` (
  `id_masuk` int(11) NOT NULL,
  `id_barang` varchar(7) NOT NULL,
  `tgl_masuk` date DEFAULT NULL,
  `jml_masuk` int(11) DEFAULT NULL,
  `ket_masuk` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `divisi`
--

CREATE TABLE `divisi` (
  `id_divisi` int(11) NOT NULL,
  `nama_divisi` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `divisi`
--

INSERT INTO `divisi` (`id_divisi`, `nama_divisi`) VALUES
(1, 'PPIC'),
(2, 'PURCHASING'),
(3, 'MACHINING'),
(4, 'ENGINEERING'),
(5, 'MEKANIK');

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id_karyawan` int(11) NOT NULL,
  `nm_karyawan` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id_karyawan`, `nm_karyawan`) VALUES
(1, 'REDI HERDIANTO'),
(2, 'HILWAN NUL FIKRI'),
(3, 'M YUSUF'),
(4, 'HARRIS'),
(5, 'SLAMET JAYADI'),
(6, 'M RIDWAN'),
(7, 'ANDRI'),
(8, 'DZULKO'),
(9, 'ALDI'),
(10, 'RENDI'),
(11, 'HENDRA'),
(12, 'HERRY'),
(13, 'ALFI'),
(14, 'FATHURRAHMAN'),
(15, 'AZHAR ALFIAN');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_barang`
--

CREATE TABLE `kategori_barang` (
  `id_kategori` varchar(3) NOT NULL,
  `nama_kategori` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori_barang`
--

INSERT INTO `kategori_barang` (`id_kategori`, `nama_kategori`) VALUES
('BC', 'Barang Consumable'),
('C', 'CONSUMABLE'),
('CBD', 'Consumable Baud'),
('CCR', 'Consumable Cair'),
('CMG', 'Consumable Machining'),
('CMK', 'Consumable Mekanik'),
('CPG', 'Consumable Packing'),
('CWL', 'Consumable Welder/La'),
('S', 'SPAREPART');

-- --------------------------------------------------------

--
-- Table structure for table `management`
--

CREATE TABLE `management` (
  `management_id` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `id_divisi` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `management`
--

INSERT INTO `management` (`management_id`, `id_karyawan`, `id_divisi`) VALUES
(1, 1, 3),
(2, 2, 4),
(3, 14, 2),
(4, 15, 1);

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `project_id` int(11) NOT NULL,
  `nama_project` varchar(50) DEFAULT NULL,
  `prioritas` varchar(8) DEFAULT NULL,
  `start_project` date DEFAULT NULL,
  `target_project` date DEFAULT NULL,
  `management_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`project_id`, `nama_project`, `prioritas`, `start_project`, `target_project`, `management_id`) VALUES
(25024, 'PERBAIKAN TURBINE MURRAY PT. KTN', 'TINGGI', '2025-07-09', '2025-07-23', 2),
(25044, 'FABRICATION CARBON RING PT. IAS', 'SEDANG', '2025-07-15', '2025-08-14', 1),
(25046, 'REBABBIT BEARING GEARBOX TA.250', 'TINGGI', '2025-07-16', '2025-08-16', 1);

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `role_id` int(11) NOT NULL,
  `nama_role` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`role_id`, `nama_role`) VALUES
(1, 'ADMIN'),
(2, 'MANAGEMENT'),
(3, 'OPERATOR');

-- --------------------------------------------------------

--
-- Table structure for table `satuan`
--

CREATE TABLE `satuan` (
  `id_satuan` int(11) NOT NULL,
  `nama_satuan` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `satuan`
--

INSERT INTO `satuan` (`id_satuan`, `nama_satuan`) VALUES
(1, 'PCS'),
(2, 'M'),
(3, 'EA'),
(4, 'KG'),
(5, 'BOX'),
(6, 'LMBR'),
(7, 'PACK'),
(8, 'ROLL'),
(9, 'GR'),
(10, 'LITER');

-- --------------------------------------------------------

--
-- Table structure for table `stock_opname`
--

CREATE TABLE `stock_opname` (
  `id_stock` int(11) NOT NULL,
  `id_barang` varchar(7) NOT NULL,
  `stock_awal` decimal(10,2) DEFAULT NULL,
  `stock_masuk` decimal(10,2) DEFAULT NULL,
  `stock_keluar` decimal(10,2) DEFAULT NULL,
  `ket_stock` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_opname`
--

INSERT INTO `stock_opname` (`id_stock`, `id_barang`, `stock_awal`, `stock_masuk`, `stock_keluar`, `ket_stock`) VALUES
(1, 'BC0001', 43.00, NULL, NULL, NULL),
(2, 'BC0002', 24.00, NULL, NULL, NULL),
(3, 'BC0003', 46.00, NULL, NULL, NULL),
(4, 'BC0004', 12.00, NULL, NULL, NULL),
(5, 'BC0005', 9.00, NULL, NULL, NULL),
(6, 'BC0006', 24.00, NULL, NULL, NULL),
(7, 'BC0007', 16.00, NULL, NULL, NULL),
(8, 'BC0008', 33.00, NULL, NULL, NULL),
(9, 'BC0009', 47.00, NULL, NULL, NULL),
(10, 'BC0010', 58.00, NULL, NULL, NULL),
(11, 'BC0011', 72.00, NULL, NULL, NULL),
(12, 'BC0012', 0.00, NULL, NULL, NULL),
(13, 'BC0013', 2.00, NULL, NULL, NULL),
(14, 'BC0014', 3.00, NULL, NULL, NULL),
(15, 'BC0015', 14.00, NULL, NULL, NULL),
(16, 'BC0016', 7.00, NULL, NULL, NULL),
(17, 'BC0017', 46.00, NULL, NULL, NULL),
(18, 'BC0018', 60.00, NULL, NULL, NULL),
(19, 'BC0019', 35.00, NULL, NULL, NULL),
(20, 'BC0020', 50.00, NULL, NULL, NULL),
(21, 'BC0021', 0.00, NULL, NULL, NULL),
(22, 'BC0022', 0.00, NULL, NULL, NULL),
(23, 'BC0023', 3.00, NULL, NULL, NULL),
(24, 'BC0024', 0.00, NULL, NULL, NULL),
(25, 'BC0025', 27.00, NULL, NULL, NULL),
(26, 'BC0026', 19.00, NULL, NULL, NULL),
(27, 'BC0027', 1.00, NULL, NULL, NULL),
(28, 'BC0028', 1.00, NULL, NULL, NULL),
(29, 'BC0029', 2.00, NULL, NULL, NULL),
(30, 'BC0030', 1.00, NULL, NULL, NULL),
(31, 'BC0031', 4.00, NULL, NULL, NULL),
(32, 'BC0032', 4.00, NULL, NULL, NULL),
(33, 'BC0033', 2.00, NULL, NULL, NULL),
(34, 'BC0034', 0.00, NULL, NULL, NULL),
(35, 'BC0035', 8.00, NULL, NULL, NULL),
(36, 'BC0036', 4.00, NULL, NULL, NULL),
(37, 'BC0037', 6.00, NULL, NULL, NULL),
(38, 'BC0038', 8.00, NULL, NULL, NULL),
(39, 'BC0039', 8.00, NULL, NULL, NULL),
(40, 'BC0040', 5.00, NULL, NULL, NULL),
(41, 'BC0041', 3.00, NULL, NULL, NULL),
(42, 'CWL0001', 2.00, NULL, NULL, NULL),
(43, 'CWL0002', 13.00, NULL, NULL, NULL),
(44, 'CWL0003', 16.00, NULL, NULL, NULL),
(45, 'CWL0004', 4.00, NULL, NULL, NULL),
(46, 'CWL0005', 11.00, NULL, NULL, NULL),
(47, 'CWL0006', 4.50, NULL, NULL, NULL),
(48, 'CWL0007', 0.00, NULL, NULL, NULL),
(49, 'CWL0008', 0.60, NULL, NULL, NULL),
(50, 'CWL0009', 3.00, NULL, NULL, NULL),
(51, 'CWL0010', 3.40, NULL, NULL, NULL),
(52, 'CWL0011', 3.00, NULL, NULL, NULL),
(53, 'CWL0012', 3.50, NULL, NULL, NULL),
(54, 'CWL0013', 0.00, NULL, NULL, NULL),
(55, 'CWL0014', 5.60, NULL, NULL, NULL),
(56, 'CWL0015', 0.00, NULL, NULL, NULL),
(57, 'CWL0016', 0.80, NULL, NULL, NULL),
(58, 'CWL0017', 2.60, NULL, NULL, NULL),
(59, 'CWL0018', 1.30, NULL, NULL, NULL),
(60, 'CWL0019', 1.00, NULL, NULL, NULL),
(61, 'CWL0020', 1.00, NULL, NULL, NULL),
(62, 'CWL0021', 18.00, NULL, NULL, NULL),
(63, 'CWL0022', 2.00, NULL, NULL, NULL),
(64, 'CWL0023', 4.00, NULL, NULL, NULL),
(65, 'CWL0024', 10.00, NULL, NULL, NULL),
(66, 'CWL0025', 13.00, NULL, NULL, NULL),
(67, 'CWL0026', 3.00, NULL, NULL, NULL),
(68, 'CWL0027', 10.00, NULL, NULL, NULL),
(69, 'CWL0028', 9.00, NULL, NULL, NULL),
(70, 'CWL0029', 10.00, NULL, NULL, NULL),
(71, 'CWL0030', 10.00, NULL, NULL, NULL),
(72, 'CWL0031', 2.00, NULL, NULL, NULL),
(73, 'CMK0001', 3.00, NULL, NULL, NULL),
(74, 'CMK0002', 10.00, NULL, NULL, NULL),
(75, 'CMK0003', 10.00, NULL, NULL, NULL),
(76, 'CMK0004', 1.00, NULL, NULL, NULL),
(77, 'CMK0005', 1.00, NULL, NULL, NULL),
(78, 'CMK0006', 0.00, NULL, NULL, NULL),
(79, 'CMK0007', 1.00, NULL, NULL, NULL),
(80, 'CMK0008', 2.00, NULL, NULL, NULL),
(81, 'CMK0009', 0.00, NULL, NULL, NULL),
(82, 'CMK0010', 0.00, NULL, NULL, NULL),
(83, 'CMK0011', 0.00, NULL, NULL, NULL),
(84, 'CMK0012', 0.00, NULL, NULL, NULL),
(85, 'CMK0013', 0.00, NULL, NULL, NULL),
(86, 'CMK0014', 0.00, NULL, NULL, NULL),
(87, 'CMK0015', 0.00, NULL, NULL, NULL),
(88, 'CMK0016', 2.00, NULL, NULL, NULL),
(89, 'CMK0017', 10.00, NULL, NULL, NULL),
(90, 'CMK0018', 6.00, NULL, NULL, NULL),
(91, 'CMK0019', 6.00, NULL, NULL, NULL),
(92, 'CMK0020', 1.00, NULL, NULL, NULL),
(93, 'CMK0021', 1.00, NULL, NULL, NULL),
(94, 'CMK0022', 1.00, NULL, NULL, NULL),
(95, 'CMK0023', 1.00, NULL, NULL, NULL),
(96, 'CMK0024', 1.50, NULL, NULL, NULL),
(97, 'CMK0025', 1.00, NULL, NULL, NULL),
(98, 'CMG0001', 40.00, NULL, NULL, NULL),
(99, 'CMG0002', 22.00, NULL, NULL, NULL),
(100, 'CMG0003', 25.00, NULL, NULL, NULL),
(101, 'CMG0004', 17.00, NULL, NULL, NULL),
(102, 'CMG0005', 5.00, NULL, NULL, NULL),
(103, 'CMG0006', 12.00, NULL, NULL, NULL),
(104, 'CMG0007', 20.00, NULL, NULL, NULL),
(105, 'CMG0008', 5.00, NULL, NULL, NULL),
(106, 'CMG0009', 2.00, NULL, NULL, NULL),
(107, 'CMG0010', 5.00, NULL, NULL, NULL),
(108, 'CMG0011', 4.00, NULL, NULL, NULL),
(109, 'CMG0012', 50.00, NULL, NULL, NULL),
(110, 'CMG0013', 26.00, NULL, NULL, NULL),
(111, 'CMG0014', 8.00, NULL, NULL, NULL),
(112, 'CMG0015', 6.00, NULL, NULL, NULL),
(113, 'CMG0016', 2.00, NULL, NULL, NULL),
(114, 'CMG0017', 5.00, NULL, NULL, NULL),
(115, 'CMG0018', 1.00, NULL, NULL, NULL),
(116, 'CMG0019', 19.00, NULL, NULL, NULL),
(117, 'CMG0020', 5.00, NULL, NULL, NULL),
(118, 'CMG0021', 0.00, NULL, NULL, NULL),
(119, 'CMG0022', 50.00, NULL, NULL, NULL),
(120, 'CMG0023', 3.00, NULL, NULL, NULL),
(121, 'CCR0001', 5.00, NULL, NULL, NULL),
(122, 'CCR0002', 4.00, NULL, NULL, NULL),
(123, 'CCR0003', 0.00, NULL, NULL, NULL),
(124, 'CCR0004', 1.00, NULL, NULL, NULL),
(125, 'CPG0001', 7.00, NULL, NULL, NULL),
(126, 'CPG0002', 14.00, NULL, NULL, NULL),
(127, 'CPG0003', 5.00, NULL, NULL, NULL),
(128, 'CPG0004', 2.00, NULL, NULL, NULL),
(129, 'CPG0005', 3.00, NULL, NULL, NULL),
(130, 'CPG0006', 7.00, NULL, NULL, NULL),
(131, 'CPG0007', 2.00, NULL, NULL, NULL),
(132, 'CPG0008', 10.00, NULL, NULL, NULL),
(133, 'CPG0009', 0.00, NULL, NULL, NULL),
(134, 'CPG0010', 1.00, NULL, NULL, NULL),
(135, 'CPG0011', 2.00, NULL, NULL, NULL),
(136, 'CPG0012', 2.00, NULL, NULL, NULL),
(137, 'CPG0013', 2.00, NULL, NULL, NULL),
(138, 'CPG0014', 3.00, NULL, NULL, NULL),
(139, 'CPG0015', 9.00, NULL, NULL, NULL),
(140, 'CBD0001', 19.00, NULL, NULL, NULL),
(141, 'CBD0002', 15.00, NULL, NULL, NULL),
(142, 'CBD0003', 2.00, NULL, NULL, NULL),
(143, 'CBD0004', 8.00, NULL, NULL, NULL),
(144, 'CBD0005', 65.00, NULL, NULL, NULL),
(145, 'CBD0006', 8.00, NULL, NULL, NULL),
(146, 'CBD0007', 2.00, NULL, NULL, NULL),
(147, 'CBD0008', 21.00, NULL, NULL, NULL),
(148, 'CBD0009', 6.00, NULL, NULL, NULL),
(149, 'CBD0010', 2.00, NULL, NULL, NULL),
(150, 'CBD0011', 2.00, NULL, NULL, NULL),
(151, 'CBD0012', 4.00, NULL, NULL, NULL),
(152, 'CBD0013', 3.00, NULL, NULL, NULL),
(153, 'CBD0014', 2.00, NULL, NULL, NULL),
(154, 'CBD0015', 6.00, NULL, NULL, NULL),
(155, 'CBD0016', 8.00, NULL, NULL, NULL),
(156, 'CBD0017', 5.00, NULL, NULL, NULL),
(157, 'CBD0018', 6.00, NULL, NULL, NULL),
(158, 'CBD0019', 10.00, NULL, NULL, NULL),
(159, 'CBD0020', 6.00, NULL, NULL, NULL),
(160, 'CBD0021', 4.00, NULL, NULL, NULL),
(161, 'CBD0022', 4.00, NULL, NULL, NULL),
(162, 'CBD0023', 8.00, NULL, NULL, NULL),
(163, 'CBD0024', 6.00, NULL, NULL, NULL),
(164, 'CBD0025', 2.00, NULL, NULL, NULL),
(165, 'CBD0026', 19.00, NULL, NULL, NULL),
(166, 'CBD0027', 43.00, NULL, NULL, NULL),
(167, 'CBD0028', 19.00, NULL, NULL, NULL),
(168, 'CBD0029', 12.00, NULL, NULL, NULL),
(169, 'CBD0030', 17.00, NULL, NULL, NULL),
(170, 'CBD0031', 17.00, NULL, NULL, NULL),
(171, 'CBD0032', 13.00, NULL, NULL, NULL),
(172, 'CBD0033', 15.00, NULL, NULL, NULL),
(173, 'CBD0034', 8.00, NULL, NULL, NULL),
(174, 'CBD0035', 4.00, NULL, NULL, NULL),
(175, 'CBD0036', 50.00, NULL, NULL, NULL),
(176, 'CBD0037', 6.00, NULL, NULL, NULL),
(177, 'CBD0038', 16.00, NULL, NULL, NULL),
(178, 'CBD0039', 19.00, NULL, NULL, NULL),
(179, 'CBD0040', 9.00, NULL, NULL, NULL),
(180, 'CBD0041', 4.00, NULL, NULL, NULL),
(181, 'CBD0042', 15.00, NULL, NULL, NULL),
(182, 'CBD0043', 8.00, NULL, NULL, NULL),
(183, 'CBD0044', 3.00, NULL, NULL, NULL),
(184, 'CBD0045', 16.00, NULL, NULL, NULL),
(185, 'CBD0046', 5.00, NULL, NULL, NULL),
(186, 'CBD0047', 6.00, NULL, NULL, NULL),
(187, 'CBD0048', 9.00, NULL, NULL, NULL),
(188, 'CBD0049', 8.00, NULL, NULL, NULL),
(189, 'CBD0050', 17.00, NULL, NULL, NULL),
(190, 'CBD0051', 7.00, NULL, NULL, NULL),
(191, 'CBD0052', 10.00, NULL, NULL, NULL),
(192, 'CBD0053', 15.00, NULL, NULL, NULL),
(193, 'CBD0054', 6.00, NULL, NULL, NULL),
(194, 'CBD0055', 4.00, NULL, NULL, NULL),
(195, 'CBD0056', 3.00, NULL, NULL, NULL),
(196, 'CBD0057', 3.00, NULL, NULL, NULL),
(197, 'CBD0058', 8.00, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subproject`
--

CREATE TABLE `subproject` (
  `subproject_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `nama_subproject` varchar(50) DEFAULT NULL,
  `start_subproject` date DEFAULT NULL,
  `target_subproject` date DEFAULT NULL,
  `id_karyawan` int(11) NOT NULL,
  `management_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subproject`
--

INSERT INTO `subproject` (`subproject_id`, `project_id`, `nama_subproject`, `start_subproject`, `target_subproject`, `id_karyawan`, `management_id`) VALUES
(101, 25024, 'INCOMING INSPECTION', '2025-07-09', '2025-07-11', 9, 2),
(102, 25024, 'SPERPART TURBINE', '2025-07-09', '2025-07-13', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tugas`
--

CREATE TABLE `tugas` (
  `tugas_id` int(11) NOT NULL,
  `subproject_id` int(11) NOT NULL,
  `tugas` varchar(50) DEFAULT NULL,
  `start_tugas` date DEFAULT NULL,
  `target_tugas` date DEFAULT NULL,
  `id_vendor` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tugas`
--

INSERT INTO `tugas` (`tugas_id`, `subproject_id`, `tugas`, `start_tugas`, `target_tugas`, `id_vendor`, `id_karyawan`) VALUES
(1001, 102, 'MFG CARBON RING', '2025-07-09', '2025-08-09', 6, 1),
(1002, 101, 'INSPECTION VISUAL ROTOR', '2025-07-01', '2025-08-01', 99, 9);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` varchar(4) NOT NULL,
  `username` varchar(12) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `id_karyawan`, `role_id`) VALUES
('U001', 'redi', '$2y$10$simulasiHashPassword123', 1, 2),
('U002', 'hilwan', '$2y$10$simulasiHashPassword123', 2, 2),
('U003', 'aldi', '$2y$10$simulasiHashPassword123', 9, 3);

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

CREATE TABLE `vendor` (
  `id_vendor` int(11) NOT NULL,
  `nama_vendor` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor`
--

INSERT INTO `vendor` (`id_vendor`, `nama_vendor`) VALUES
(1, 'ANDRI VAI'),
(2, 'BU NANING'),
(3, 'HENDI'),
(4, 'IYAN TEKNIK'),
(5, 'KOSIM'),
(6, 'BARNOS'),
(7, 'ENDANG CNC BUBUT'),
(8, 'PT.HORIGUCHI'),
(9, 'BB METALINDO'),
(10, 'PA ALAN SANDBLASTING'),
(99, 'INTERNAL / NO VENDOR');

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_stock_aktual`
-- (See below for the actual view)
--
CREATE TABLE `view_stock_aktual` (
`id_stock` int(11)
,`id_barang` varchar(7)
,`nama_barang` varchar(20)
,`stock_awal` decimal(10,2)
,`id_satuan` int(11)
,`stock_masuk` decimal(10,2)
,`stock_keluar` decimal(10,2)
,`sisa_stock` decimal(12,2)
,`jumlah_nilai` decimal(22,2)
);

-- --------------------------------------------------------

--
-- Structure for view `view_stock_aktual`
--
DROP TABLE IF EXISTS `view_stock_aktual`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_stock_aktual`  AS SELECT `s`.`id_stock` AS `id_stock`, `s`.`id_barang` AS `id_barang`, `b`.`nama_barang` AS `nama_barang`, ifnull(`s`.`stock_awal`,0) AS `stock_awal`, `b`.`id_satuan` AS `id_satuan`, ifnull(`s`.`stock_masuk`,0) AS `stock_masuk`, ifnull(`s`.`stock_keluar`,0) AS `stock_keluar`, ifnull(`s`.`stock_awal`,0) + ifnull(`s`.`stock_masuk`,0) - ifnull(`s`.`stock_keluar`,0) AS `sisa_stock`, (ifnull(`s`.`stock_awal`,0) + ifnull(`s`.`stock_masuk`,0) - ifnull(`s`.`stock_keluar`,0)) * ifnull(`b`.`harga`,0) AS `jumlah_nilai` FROM (`stock_opname` `s` join `barang` `b` on(`s`.`id_barang` = `b`.`id_barang`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`),
  ADD KEY `barang_kategori_FK` (`id_kategori`),
  ADD KEY `barang_satuan_FK` (`id_satuan`);

--
-- Indexes for table `barang_keluar`
--
ALTER TABLE `barang_keluar`
  ADD PRIMARY KEY (`id_keluar`),
  ADD KEY `brg_keluar_barang_FK` (`id_barang`),
  ADD KEY `brg_keluar_project_FK` (`project_id`);

--
-- Indexes for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD PRIMARY KEY (`id_masuk`),
  ADD KEY `barang_masuk_barang_FK` (`id_barang`);

--
-- Indexes for table `divisi`
--
ALTER TABLE `divisi`
  ADD PRIMARY KEY (`id_divisi`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id_karyawan`);

--
-- Indexes for table `kategori_barang`
--
ALTER TABLE `kategori_barang`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `management`
--
ALTER TABLE `management`
  ADD PRIMARY KEY (`management_id`),
  ADD KEY `Management_divisi_FK` (`id_divisi`),
  ADD KEY `Management_Karyawan_FK` (`id_karyawan`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `project_Management_FK` (`management_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `satuan`
--
ALTER TABLE `satuan`
  ADD PRIMARY KEY (`id_satuan`);

--
-- Indexes for table `stock_opname`
--
ALTER TABLE `stock_opname`
  ADD PRIMARY KEY (`id_stock`),
  ADD KEY `stock_opname_barang_FK` (`id_barang`);

--
-- Indexes for table `subproject`
--
ALTER TABLE `subproject`
  ADD PRIMARY KEY (`subproject_id`),
  ADD KEY `subproject_Karyawan_FK` (`id_karyawan`),
  ADD KEY `subproject_Management_FK` (`management_id`),
  ADD KEY `subproject_project_FK` (`project_id`);

--
-- Indexes for table `tugas`
--
ALTER TABLE `tugas`
  ADD PRIMARY KEY (`tugas_id`),
  ADD KEY `tugas_subproject_FK` (`subproject_id`),
  ADD KEY `tugas_Karyawan_FK` (`id_karyawan`),
  ADD KEY `tugas_VENDOR_FK` (`id_vendor`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `user_Karyawan_FK` (`id_karyawan`),
  ADD KEY `user_role_FK` (`role_id`);

--
-- Indexes for table `vendor`
--
ALTER TABLE `vendor`
  ADD PRIMARY KEY (`id_vendor`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `satuan`
--
ALTER TABLE `satuan`
  MODIFY `id_satuan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_kategori_FK` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_barang` (`id_kategori`),
  ADD CONSTRAINT `barang_satuan_FK` FOREIGN KEY (`id_satuan`) REFERENCES `satuan` (`id_satuan`);

--
-- Constraints for table `barang_keluar`
--
ALTER TABLE `barang_keluar`
  ADD CONSTRAINT `brg_keluar_barang_FK` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`),
  ADD CONSTRAINT `brg_keluar_project_FK` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`);

--
-- Constraints for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD CONSTRAINT `barang_masuk_barang_FK` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`);

--
-- Constraints for table `management`
--
ALTER TABLE `management`
  ADD CONSTRAINT `Management_Karyawan_FK` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`),
  ADD CONSTRAINT `Management_divisi_FK` FOREIGN KEY (`id_divisi`) REFERENCES `divisi` (`id_divisi`);

--
-- Constraints for table `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `project_Management_FK` FOREIGN KEY (`management_id`) REFERENCES `management` (`management_id`);

--
-- Constraints for table `stock_opname`
--
ALTER TABLE `stock_opname`
  ADD CONSTRAINT `stock_opname_barang_FK` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`);

--
-- Constraints for table `subproject`
--
ALTER TABLE `subproject`
  ADD CONSTRAINT `subproject_Karyawan_FK` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`),
  ADD CONSTRAINT `subproject_Management_FK` FOREIGN KEY (`management_id`) REFERENCES `management` (`management_id`),
  ADD CONSTRAINT `subproject_project_FK` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`);

--
-- Constraints for table `tugas`
--
ALTER TABLE `tugas`
  ADD CONSTRAINT `tugas_Karyawan_FK` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`),
  ADD CONSTRAINT `tugas_VENDOR_FK` FOREIGN KEY (`id_vendor`) REFERENCES `vendor` (`id_vendor`),
  ADD CONSTRAINT `tugas_subproject_FK` FOREIGN KEY (`subproject_id`) REFERENCES `subproject` (`subproject_id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_Karyawan_FK` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`),
  ADD CONSTRAINT `user_role_FK` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
