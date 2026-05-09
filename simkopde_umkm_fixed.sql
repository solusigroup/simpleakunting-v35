-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versi server:                 12.2.2-MariaDB - MariaDB Server
-- OS Server:                    Win64
-- HeidiSQL Versi:               12.15.0.7171
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- membuang struktur untuk table simpleak_v35.activity_logs
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `action` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `module` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `tenant_id` (`tenant_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.activity_logs: ~26 rows (lebih kurang)
INSERT INTO `activity_logs` (`id`, `tenant_id`, `user_id`, `user_name`, `action`, `module`, `description`, `ip_address`, `created_at`) VALUES
	(1, 2, 5, 'manajerjaya', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 08:13:05'),
	(2, 1, 2, 'superadmin', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 08:17:04'),
	(3, 2, 5, 'manajerjaya', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 08:26:13'),
	(4, 1, 2, 'superadmin', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 08:47:56'),
	(5, 2, 5, 'manajerjaya', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 09:14:00'),
	(6, 1, 2, 'superadmin', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 09:40:00'),
	(7, 1, 2, 'superadmin', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 09:47:06'),
	(8, 1, 2, 'superadmin', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 09:53:01'),
	(9, 1, 2, 'superadmin', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 09:54:22'),
	(10, 3, 6, 'penjahit', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 09:55:14'),
	(11, 1, 2, 'superadmin', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 09:59:36'),
	(12, 2, 5, 'manajerjaya', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 10:04:53'),
	(13, 1, 2, 'superadmin', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 10:30:45'),
	(14, 1, 2, 'superadmin', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 10:31:56'),
	(15, NULL, 2, 'superadmin', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 10:34:04'),
	(16, NULL, 2, 'superadmin', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 12:30:16'),
	(17, NULL, 2, 'superadmin', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 13:04:13'),
	(18, 2, 5, 'manajerjaya', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 13:28:45'),
	(19, NULL, 2, 'superadmin', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 13:29:14'),
	(20, NULL, 2, 'superadmin', 'UPDATE_ROLE', 'Security', 'Updated Role: Manager', '127.0.0.1', '2026-05-09 13:29:32'),
	(21, 2, 5, 'manajerjaya', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 13:52:02'),
	(22, NULL, 2, 'superadmin', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 14:10:47'),
	(23, NULL, 2, 'superadmin', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 14:29:26'),
	(24, 2, 5, 'manajerjaya', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 14:39:16'),
	(25, NULL, 2, 'superadmin', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 14:40:52'),
	(26, NULL, 2, 'superadmin', 'LOGIN', 'Authentication', 'User successfully logged in.', '127.0.0.1', '2026-05-09 16:37:54');

-- membuang struktur untuk table simpleak_v35.akun
CREATE TABLE IF NOT EXISTS `akun` (
  `kode_akun` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tenant_id` int(11) NOT NULL DEFAULT 1,
  `nama_akun` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tipe_akun` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `saldo_normal` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `saldo_awal` decimal(15,2) DEFAULT 0.00,
  `posisi_saldo_normal` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Debit',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_akun`,`tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.akun: ~106 rows (lebih kurang)
INSERT INTO `akun` (`kode_akun`, `tenant_id`, `nama_akun`, `tipe_akun`, `saldo_normal`, `saldo_awal`, `posisi_saldo_normal`, `created_at`, `updated_at`) VALUES
	('1-0000', 2, 'AKTIVA', 'Header', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-0000', 3, 'AKTIVA', 'Header', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-1000', 2, 'AKTIVA LANCAR', 'Header', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-1000', 3, 'AKTIVA LANCAR', 'Header', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-10001', 1, 'Kas Kecil', 'Kas & Bank', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-10002', 1, 'Bank BCA', 'Kas & Bank', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-10003', 1, 'Bank Mandiri', 'Kas & Bank', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-10100', 1, 'Piutang Usaha', 'Piutang', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-10200', 1, 'Persediaan Barang Dagang', 'Persediaan', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-10300', 1, 'Perlengkapan', 'Aset Lancar Lainnya', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-10400', 1, 'Sewa Dibayar Dimuka', 'Aset Lancar Lainnya', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-1100', 2, 'Kas & Bank', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-1100', 3, 'Kas & Bank', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-1101', 2, 'Kas Kecil', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-1101', 3, 'Kas Kecil', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-1102', 2, 'Bank BCA', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-1102', 3, 'Bank BCA', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-1200', 2, 'Piutang Usaha', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-1200', 3, 'Piutang Usaha', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-1300', 2, 'Persediaan Barang', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-1300', 3, 'Persediaan Barang', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-1400', 2, 'Pajak Dibayar Dimuka (PPN)', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-1400', 3, 'Pajak Dibayar Dimuka (PPN)', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-2000', 2, 'AKTIVA TETAP', 'Header', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-2000', 3, 'AKTIVA TETAP', 'Header', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-20100', 1, 'Peralatan Kantor', 'Aset Tetap', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-20101', 1, 'Akum. Peny. Peralatan', 'Aset Tetap', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('1-20200', 1, 'Kendaraan', 'Aset Tetap', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-20201', 1, 'Akum. Peny. Kendaraan', 'Aset Tetap', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('1-2100', 2, 'Tanah', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-2100', 3, 'Tanah', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-2200', 2, 'Bangunan', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-2200', 3, 'Bangunan', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-2201', 2, 'Akum. Penyusutan Bangunan', 'Detail', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('1-2201', 3, 'Akum. Penyusutan Bangunan', 'Detail', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('1-2300', 2, 'Kendaraan', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-2300', 3, 'Kendaraan', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('1-2301', 2, 'Akum. Penyusutan Kendaraan', 'Detail', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('1-2301', 3, 'Akum. Penyusutan Kendaraan', 'Detail', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('2-0000', 2, 'KEWAJIBAN', 'Header', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('2-0000', 3, 'KEWAJIBAN', 'Header', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('2-1000', 2, 'KEWAJIBAN LANCAR', 'Header', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('2-1000', 3, 'KEWAJIBAN LANCAR', 'Header', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('2-10100', 1, 'Utang Usaha', 'Utang Usaha', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('2-10200', 1, 'Utang Gaji', 'Kewajiban Lancar Lainnya', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('2-10300', 1, 'Utang Pajak', 'Kewajiban Lancar Lainnya', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('2-1100', 2, 'Hutang Usaha', 'Detail', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('2-1100', 3, 'Hutang Usaha', 'Detail', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('2-1200', 2, 'Hutang Pajak (PPN)', 'Detail', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('2-1200', 3, 'Hutang Pajak (PPN)', 'Detail', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('2-1300', 2, 'Hutang Gaji', 'Detail', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('2-1300', 3, 'Hutang Gaji', 'Detail', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('2-2000', 2, 'KEWAJIBAN JANGKA PANJANG', 'Header', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('2-2000', 3, 'KEWAJIBAN JANGKA PANJANG', 'Header', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('2-20100', 1, 'Utang Bank Jangka Panjang', 'Kewajiban Jangka Panjang', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('2-2100', 2, 'Hutang Bank', 'Detail', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('2-2100', 3, 'Hutang Bank', 'Detail', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('3-0000', 2, 'EKUITAS', 'Header', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('3-0000', 3, 'EKUITAS', 'Header', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('3-1000', 2, 'Modal Disetor', 'Detail', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('3-1000', 3, 'Modal Disetor', 'Detail', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('3-10000', 1, 'Modal Pemilik', 'Ekuitas', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('3-2000', 2, 'Laba Ditahan', 'Detail', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('3-2000', 3, 'Laba Ditahan', 'Detail', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('3-20000', 1, 'Prive Pemilik', 'Ekuitas', 'Debit', 0.00, 'Debit', NULL, NULL),
	('3-3000', 2, 'Ikhtisar Laba Rugi', 'Detail', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('3-3000', 3, 'Ikhtisar Laba Rugi', 'Detail', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('3-30000', 1, 'Laba Ditahan', 'Ekuitas', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('4-0000', 2, 'PENDAPATAN', 'Header', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('4-0000', 3, 'PENDAPATAN', 'Header', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('4-1000', 2, 'Pendapatan Usaha', 'Detail', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('4-1000', 3, 'Pendapatan Usaha', 'Detail', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('4-10000', 1, 'Penjualan Barang', 'Pendapatan', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('4-2000', 2, 'Pendapatan Lain-lain', 'Detail', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('4-2000', 3, 'Pendapatan Lain-lain', 'Detail', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('4-20000', 1, 'Pendapatan Jasa', 'Pendapatan', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('4-30000', 1, 'Retur Penjualan', 'Pendapatan', 'Debit', 0.00, 'Debit', NULL, NULL),
	('4-40000', 1, 'Potongan Penjualan', 'Pendapatan', 'Debit', 0.00, 'Debit', NULL, NULL),
	('5-0000', 2, 'HARGA POKOK PENJUALAN', 'Header', 'Debit', 0.00, 'Debit', NULL, NULL),
	('5-0000', 3, 'HARGA POKOK PENJUALAN', 'Header', 'Debit', 0.00, 'Debit', NULL, NULL),
	('5-1000', 2, 'HPP Barang Dagangan', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('5-1000', 3, 'HPP Barang Dagangan', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('5-10000', 1, 'Harga Pokok Penjualan', 'HPP', 'Debit', 0.00, 'Debit', NULL, NULL),
	('6-0000', 2, 'BEBAN OPERASIONAL', 'Header', 'Debit', 0.00, 'Debit', NULL, NULL),
	('6-0000', 3, 'BEBAN OPERASIONAL', 'Header', 'Debit', 0.00, 'Debit', NULL, NULL),
	('6-1000', 2, 'Beban Gaji & Upah', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('6-1000', 3, 'Beban Gaji & Upah', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('6-10001', 1, 'Beban Gaji', 'Beban', 'Debit', 0.00, 'Debit', NULL, NULL),
	('6-10002', 1, 'Beban Sewa', 'Beban', 'Debit', 0.00, 'Debit', NULL, NULL),
	('6-10003', 1, 'Beban Listrik, Air & Telp', 'Beban', 'Debit', 0.00, 'Debit', NULL, NULL),
	('6-10004', 1, 'Beban Perlengkapan', 'Beban', 'Debit', 0.00, 'Debit', NULL, NULL),
	('6-10005', 1, 'Beban Penyusutan', 'Beban', 'Debit', 0.00, 'Debit', NULL, NULL),
	('6-10006', 1, 'Beban Pemasaran', 'Beban', 'Debit', 0.00, 'Debit', NULL, NULL),
	('6-10007', 1, 'Beban Lain-lain', 'Beban', 'Debit', 0.00, 'Debit', NULL, NULL),
	('6-2000', 2, 'Beban Listrik, Air & Telp', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('6-2000', 3, 'Beban Listrik, Air & Telp', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('6-3000', 2, 'Beban Sewa', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('6-3000', 3, 'Beban Sewa', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('6-4000', 2, 'Beban Perlengkapan Kantor', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('6-4000', 3, 'Beban Perlengkapan Kantor', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('6-5000', 2, 'Beban Penyusutan Aset', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('6-5000', 3, 'Beban Penyusutan Aset', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('6-9000', 2, 'Beban Lain-lain', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('6-9000', 3, 'Beban Lain-lain', 'Detail', 'Debit', 0.00, 'Debit', NULL, NULL),
	('8-10000', 1, 'Pendapatan Bunga', 'Pendapatan Lainnya', 'Kredit', 0.00, 'Kredit', NULL, NULL),
	('9-10000', 1, 'Beban Administrasi Bank', 'Beban Lainnya', 'Debit', 0.00, 'Debit', NULL, NULL);

-- membuang struktur untuk table simpleak_v35.aset_tetap
CREATE TABLE IF NOT EXISTS `aset_tetap` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `kode_aset` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_aset` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_perolehan` date NOT NULL,
  `harga_perolehan` decimal(15,2) NOT NULL,
  `nilai_residu` decimal(15,2) DEFAULT 0.00,
  `umur_ekonomis` int(11) NOT NULL COMMENT 'dalam bulan',
  `metode_penyusutan` enum('garis_lurus','saldo_menurun') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'garis_lurus',
  `akun_aset` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `akun_akumulasi` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `akun_beban` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.aset_tetap: ~0 rows (lebih kurang)

-- membuang struktur untuk table simpleak_v35.bom
CREATE TABLE IF NOT EXISTS `bom` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `id_barang_jadi` bigint(20) unsigned NOT NULL,
  `nama_bom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `total_biaya_estimasi` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.bom: ~0 rows (lebih kurang)

-- membuang struktur untuk table simpleak_v35.bom_detail
CREATE TABLE IF NOT EXISTS `bom_detail` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_bom` bigint(20) unsigned NOT NULL,
  `id_bahan_baku` bigint(20) unsigned NOT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `satuan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `biaya_satuan` decimal(15,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `id_bom` (`id_bom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.bom_detail: ~0 rows (lebih kurang)

-- membuang struktur untuk table simpleak_v35.central_akun
CREATE TABLE IF NOT EXISTS `central_akun` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_akun` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_akun` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tipe_akun` enum('Header','Detail') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `posisi_saldo_normal` enum('Debit','Kredit') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.central_akun: ~37 rows (lebih kurang)
INSERT INTO `central_akun` (`id`, `kode_akun`, `nama_akun`, `tipe_akun`, `posisi_saldo_normal`) VALUES
	(1, '1-0000', 'AKTIVA', 'Header', 'Debit'),
	(2, '1-1000', 'AKTIVA LANCAR', 'Header', 'Debit'),
	(3, '1-1100', 'Kas & Bank', 'Detail', 'Debit'),
	(4, '1-1101', 'Kas Kecil', 'Detail', 'Debit'),
	(5, '1-1102', 'Bank BCA', 'Detail', 'Debit'),
	(6, '1-1200', 'Piutang Usaha', 'Detail', 'Debit'),
	(7, '1-1300', 'Persediaan Barang', 'Detail', 'Debit'),
	(8, '1-1400', 'Pajak Dibayar Dimuka (PPN)', 'Detail', 'Debit'),
	(9, '1-2000', 'AKTIVA TETAP', 'Header', 'Debit'),
	(10, '1-2100', 'Tanah', 'Detail', 'Debit'),
	(11, '1-2200', 'Bangunan', 'Detail', 'Debit'),
	(12, '1-2201', 'Akum. Penyusutan Bangunan', 'Detail', 'Kredit'),
	(13, '1-2300', 'Kendaraan', 'Detail', 'Debit'),
	(14, '1-2301', 'Akum. Penyusutan Kendaraan', 'Detail', 'Kredit'),
	(15, '2-0000', 'KEWAJIBAN', 'Header', 'Kredit'),
	(16, '2-1000', 'KEWAJIBAN LANCAR', 'Header', 'Kredit'),
	(17, '2-1100', 'Hutang Usaha', 'Detail', 'Kredit'),
	(18, '2-1200', 'Hutang Pajak (PPN)', 'Detail', 'Kredit'),
	(19, '2-1300', 'Hutang Gaji', 'Detail', 'Kredit'),
	(20, '2-2000', 'KEWAJIBAN JANGKA PANJANG', 'Header', 'Kredit'),
	(21, '2-2100', 'Hutang Bank', 'Detail', 'Kredit'),
	(22, '3-0000', 'EKUITAS', 'Header', 'Kredit'),
	(23, '3-1000', 'Modal Disetor', 'Detail', 'Kredit'),
	(24, '3-2000', 'Laba Ditahan', 'Detail', 'Kredit'),
	(25, '3-3000', 'Ikhtisar Laba Rugi', 'Detail', 'Kredit'),
	(26, '4-0000', 'PENDAPATAN', 'Header', 'Kredit'),
	(27, '4-1000', 'Pendapatan Usaha', 'Detail', 'Kredit'),
	(28, '4-2000', 'Pendapatan Lain-lain', 'Detail', 'Kredit'),
	(29, '5-0000', 'HARGA POKOK PENJUALAN', 'Header', 'Debit'),
	(30, '5-1000', 'HPP Barang Dagangan', 'Detail', 'Debit'),
	(31, '6-0000', 'BEBAN OPERASIONAL', 'Header', 'Debit'),
	(32, '6-1000', 'Beban Gaji & Upah', 'Detail', 'Debit'),
	(33, '6-2000', 'Beban Listrik, Air & Telp', 'Detail', 'Debit'),
	(34, '6-3000', 'Beban Sewa', 'Detail', 'Debit'),
	(35, '6-4000', 'Beban Perlengkapan Kantor', 'Detail', 'Debit'),
	(36, '6-5000', 'Beban Penyusutan Aset', 'Detail', 'Debit'),
	(37, '6-9000', 'Beban Lain-lain', 'Detail', 'Debit');

-- membuang struktur untuk table simpleak_v35.jurnal_detail
CREATE TABLE IF NOT EXISTS `jurnal_detail` (
  `id_detail` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_jurnal` bigint(20) unsigned NOT NULL,
  `kode_akun` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `kredit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `id_jurnal` (`id_jurnal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.jurnal_detail: ~0 rows (lebih kurang)

-- membuang struktur untuk table simpleak_v35.jurnal_umum
CREATE TABLE IF NOT EXISTS `jurnal_umum` (
  `id_jurnal` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) DEFAULT 1,
  `no_transaksi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` date NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sumber_jurnal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_jurnal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.jurnal_umum: ~0 rows (lebih kurang)

-- membuang struktur untuk table simpleak_v35.kas_transaksi
CREATE TABLE IF NOT EXISTS `kas_transaksi` (
  `id_transaksi` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `id_jurnal` int(11) DEFAULT NULL,
  `tipe_transaksi` enum('Masuk','Keluar') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` date NOT NULL,
  `no_bukti` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `akun_kas_bank` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `akun_lawan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_transaksi`),
  KEY `tenant_id` (`tenant_id`),
  KEY `tanggal` (`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.kas_transaksi: ~0 rows (lebih kurang)

-- membuang struktur untuk table simpleak_v35.master_persediaan
CREATE TABLE IF NOT EXISTS `master_persediaan` (
  `id_barang` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) DEFAULT 1,
  `kode_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `barcode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `satuan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `stok_awal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stok_saat_ini` decimal(10,2) NOT NULL DEFAULT 0.00,
  `harga_beli` decimal(15,2) NOT NULL DEFAULT 0.00,
  `harga_jual` decimal(15,2) NOT NULL DEFAULT 0.00,
  `akun_persediaan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `akun_hpp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `akun_penjualan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_barang`),
  UNIQUE KEY `kode_barang` (`kode_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.master_persediaan: ~0 rows (lebih kurang)

-- membuang struktur untuk table simpleak_v35.pelanggan
CREATE TABLE IF NOT EXISTS `pelanggan` (
  `id_pelanggan` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) DEFAULT 1,
  `nama_pelanggan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telepon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `saldo_awal_piutang` decimal(15,2) NOT NULL DEFAULT 0.00,
  `saldo_terkini_piutang` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pelanggan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.pelanggan: ~0 rows (lebih kurang)

-- membuang struktur untuk table simpleak_v35.pemasok
CREATE TABLE IF NOT EXISTS `pemasok` (
  `id_pemasok` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) DEFAULT 1,
  `nama_pemasok` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telepon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `saldo_awal_hutang` decimal(15,2) NOT NULL DEFAULT 0.00,
  `saldo_terkini_hutang` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pemasok`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.pemasok: ~0 rows (lebih kurang)

-- membuang struktur untuk table simpleak_v35.pembayaran_pemasok
CREATE TABLE IF NOT EXISTS `pembayaran_pemasok` (
  `id_pembayaran` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) DEFAULT 1,
  `id_pemasok` bigint(20) unsigned NOT NULL,
  `id_jurnal` bigint(20) unsigned NOT NULL,
  `no_bukti` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` date NOT NULL,
  `akun_kas_bank` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `total_dibayar` decimal(15,2) NOT NULL DEFAULT 0.00,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_pembayaran`),
  KEY `fk_pembayaran_pemasok` (`id_pemasok`),
  KEY `fk_pembayaran_jurnal` (`id_jurnal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.pembayaran_pemasok: ~0 rows (lebih kurang)

-- membuang struktur untuk table simpleak_v35.pembayaran_pemasok_detail
CREATE TABLE IF NOT EXISTS `pembayaran_pemasok_detail` (
  `id_detail` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_pembayaran` bigint(20) unsigned NOT NULL,
  `id_pembelian` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jumlah_bayar` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_detail`),
  KEY `fk_pembayaran_detail_header` (`id_pembayaran`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.pembayaran_pemasok_detail: ~0 rows (lebih kurang)

-- membuang struktur untuk table simpleak_v35.pembelian
CREATE TABLE IF NOT EXISTS `pembelian` (
  `id_pembelian` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) DEFAULT 1,
  `id_pemasok` bigint(20) unsigned NOT NULL,
  `id_jurnal` bigint(20) unsigned DEFAULT NULL,
  `no_faktur_pembelian` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_faktur` date NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `pajak` decimal(15,2) DEFAULT 0.00,
  `diskon` decimal(15,2) DEFAULT 0.00,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `metode_pembayaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `akun_kas_bank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sisa_tagihan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status_pembayaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Belum Lunas',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pembelian`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.pembelian: ~0 rows (lebih kurang)

-- membuang struktur untuk table simpleak_v35.penawaran
CREATE TABLE IF NOT EXISTS `penawaran` (
  `id_penawaran` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `no_penawaran` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` date NOT NULL,
  `tgl_kadaluarsa` date DEFAULT NULL,
  `total` decimal(15,2) DEFAULT 0.00,
  `pajak` decimal(15,2) DEFAULT 0.00,
  `diskon` decimal(15,2) DEFAULT 0.00,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('Draft','Sent','Accepted','Rejected','Invoiced') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Draft',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_penawaran`),
  KEY `tenant_id` (`tenant_id`),
  KEY `no_penawaran` (`no_penawaran`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.penawaran: ~0 rows (lebih kurang)

-- membuang struktur untuk table simpleak_v35.penawaran_detail
CREATE TABLE IF NOT EXISTS `penawaran_detail` (
  `id_detail` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_penawaran` bigint(20) unsigned NOT NULL,
  `id_barang` int(11) NOT NULL,
  `kuantitas` decimal(15,2) NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `id_penawaran` (`id_penawaran`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.penawaran_detail: ~0 rows (lebih kurang)

-- membuang struktur untuk table simpleak_v35.penerimaan_pelanggan
CREATE TABLE IF NOT EXISTS `penerimaan_pelanggan` (
  `id_penerimaan` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) DEFAULT 1,
  `id_pelanggan` bigint(20) unsigned NOT NULL,
  `id_jurnal` bigint(20) unsigned NOT NULL,
  `no_bukti` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` date NOT NULL,
  `akun_kas_bank` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `total_diterima` decimal(15,2) NOT NULL DEFAULT 0.00,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_penerimaan`),
  KEY `fk_penerimaan_pelanggan` (`id_pelanggan`),
  KEY `fk_penerimaan_jurnal` (`id_jurnal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.penerimaan_pelanggan: ~0 rows (lebih kurang)

-- membuang struktur untuk table simpleak_v35.penerimaan_pelanggan_detail
CREATE TABLE IF NOT EXISTS `penerimaan_pelanggan_detail` (
  `id_detail` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_penerimaan` bigint(20) unsigned NOT NULL,
  `id_penjualan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jumlah_bayar` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_detail`),
  KEY `fk_penerimaan_detail_header` (`id_penerimaan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.penerimaan_pelanggan_detail: ~0 rows (lebih kurang)

-- membuang struktur untuk table simpleak_v35.penjualan
CREATE TABLE IF NOT EXISTS `penjualan` (
  `id_penjualan` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) DEFAULT 1,
  `id_pelanggan` bigint(20) unsigned NOT NULL,
  `id_jurnal` bigint(20) unsigned DEFAULT NULL,
  `no_faktur` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_faktur` date NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `pajak` decimal(15,2) DEFAULT 0.00,
  `diskon` decimal(15,2) DEFAULT 0.00,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `metode_pembayaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `akun_kas_bank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sisa_tagihan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status_pembayaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Belum Lunas',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_penjualan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.penjualan: ~0 rows (lebih kurang)

-- membuang struktur untuk table simpleak_v35.penyusutan_aset
CREATE TABLE IF NOT EXISTS `penyusutan_aset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `id_aset` bigint(20) unsigned NOT NULL,
  `id_jurnal` int(11) DEFAULT NULL,
  `bulan` tinyint(4) NOT NULL,
  `tahun` smallint(6) NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `tanggal_proses` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_aset` (`id_aset`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.penyusutan_aset: ~0 rows (lebih kurang)

-- membuang struktur untuk table simpleak_v35.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `display_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `category` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permission_key` (`permission_key`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.permissions: ~13 rows (lebih kurang)
INSERT INTO `permissions` (`id`, `permission_key`, `display_name`, `category`) VALUES
	(1, 'sys_user_management', 'Manage Users & Roles', 'System'),
	(2, 'master_akun', 'Manage Bagan Akun', 'Master Data'),
	(3, 'master_persediaan', 'Manage Persediaan', 'Master Data'),
	(4, 'master_aset', 'Manage Aset Tetap', 'Master Data'),
	(5, 'trx_penjualan', 'Transaksi Penjualan', 'Operasional'),
	(6, 'trx_penerimaan', 'Penerimaan Piutang', 'Operasional'),
	(7, 'trx_pembelian', 'Transaksi Pembelian', 'Operasional'),
	(8, 'trx_pembayaran', 'Pembayaran Utang', 'Operasional'),
	(9, 'trx_bom', 'Bill of Materials', 'Manufaktur'),
	(10, 'trx_produksi', 'Perintah Produksi', 'Manufaktur'),
	(11, 'trx_kas', 'Kas & Bank', 'Keuangan'),
	(12, 'fin_jurnal', 'Jurnal Umum', 'Keuangan'),
	(13, 'fin_laporan', 'Laporan Keuangan', 'Keuangan');

-- membuang struktur untuk table simpleak_v35.perusahaan
CREATE TABLE IF NOT EXISTS `perusahaan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) DEFAULT 1,
  `nama_perusahaan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_usaha` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telepon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `penandatangan_1_id` int(11) DEFAULT NULL,
  `penandatangan_2_id` int(11) DEFAULT NULL,
  `kota_laporan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `path_logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `akun_piutang_default` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `akun_utang_default` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `akun_laba_ditahan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `akun_ikhtisar_lr` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `akun_akumulasi_depresiasi_default` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `akun_beban_depresiasi_default` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `akun_pajak_penjualan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `akun_pajak_pembelian` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `persentase_pajak_default` decimal(5,2) DEFAULT 11.00,
  `akun_tenaga_kerja_langsung` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `akun_overhead_pabrik` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_direktur` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_akuntan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.perusahaan: ~3 rows (lebih kurang)
INSERT INTO `perusahaan` (`id`, `tenant_id`, `nama_perusahaan`, `jenis_usaha`, `alamat`, `telepon`, `email`, `penandatangan_1_id`, `penandatangan_2_id`, `kota_laporan`, `path_logo`, `akun_piutang_default`, `akun_utang_default`, `akun_laba_ditahan`, `akun_ikhtisar_lr`, `akun_akumulasi_depresiasi_default`, `akun_beban_depresiasi_default`, `akun_pajak_penjualan`, `akun_pajak_pembelian`, `persentase_pajak_default`, `akun_tenaga_kerja_langsung`, `akun_overhead_pabrik`, `nama_direktur`, `nama_akuntan`, `created_at`, `updated_at`) VALUES
	(1, 1, 'KOPERASI KELURAHAN MERAH PUTIH SURODINAWAN', 'manufaktur', '', '', '', 1, 3, '', 'img/logos/69feec521dedd_favicon-SimpleAkunting.png', '1-10001', '2-10100', '3-10000', '3-10000', '1-20201', '6-10005', NULL, NULL, 11.00, NULL, NULL, 'Wawan', 'Kurniawan', NULL, '2026-05-09 04:54:52'),
	(2, 2, 'PT Maju Jaya', 'manufaktur', 'Jl Suromulang no 20', '0888399799777', 'majujaya@gmail.com', 5, 5, 'PASURUAN', 'img/logos/69fefef89e1a2_favicon-SimpleAkunting.png', '1-1100', '2-1100', '3-1000', '3-1000', '1-2301', '6-5000', '2-1200', '1-1400', 11.00, '6-1000', '6-2000', NULL, NULL, NULL, NULL),
	(3, 3, 'Penjahit Modern', 'jasa', 'Jl Suromulang Barat VI no 20', '0895399799777', 'kurniawan.se@gmail.com', 6, 6, 'KOTA MOJOKERTO', 'img/logos/69ff050a79c48_favicon-SimpleAkunting.png', '1-1100', '2-1100', '3-1000', '3-1000', NULL, NULL, NULL, NULL, 11.00, NULL, NULL, NULL, NULL, NULL, NULL);

-- membuang struktur untuk table simpleak_v35.produksi
CREATE TABLE IF NOT EXISTS `produksi` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `no_produksi` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` date NOT NULL,
  `id_bom` bigint(20) unsigned NOT NULL,
  `id_jurnal` int(11) DEFAULT NULL,
  `jumlah_target` decimal(10,2) NOT NULL,
  `total_biaya_aktual` decimal(15,2) DEFAULT NULL,
  `biaya_tenaga_kerja` decimal(15,2) DEFAULT 0.00,
  `biaya_overhead` decimal(15,2) DEFAULT 0.00,
  `status` enum('Draft','Selesai','Batal') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Draft',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.produksi: ~0 rows (lebih kurang)

-- membuang struktur untuk table simpleak_v35.rfq
CREATE TABLE IF NOT EXISTS `rfq` (
  `id_rfq` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `id_pemasok` int(11) NOT NULL,
  `no_rfq` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` date NOT NULL,
  `tgl_kadaluarsa` date DEFAULT NULL,
  `total_estimasi` decimal(15,2) DEFAULT 0.00,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('Draft','Sent','Ordered','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Draft',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_rfq`),
  KEY `tenant_id` (`tenant_id`),
  KEY `no_rfq` (`no_rfq`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.rfq: ~0 rows (lebih kurang)

-- membuang struktur untuk table simpleak_v35.rfq_detail
CREATE TABLE IF NOT EXISTS `rfq_detail` (
  `id_detail` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_rfq` bigint(20) unsigned NOT NULL,
  `id_barang` int(11) NOT NULL,
  `kuantitas` decimal(15,2) NOT NULL,
  `harga_estimasi` decimal(15,2) NOT NULL,
  `subtotal_estimasi` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `id_rfq` (`id_rfq`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.rfq_detail: ~0 rows (lebih kurang)

-- membuang struktur untuk table simpleak_v35.role_permissions
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.role_permissions: ~47 rows (lebih kurang)
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
	(1, 1),
	(1, 2),
	(1, 3),
	(1, 4),
	(1, 5),
	(1, 6),
	(1, 7),
	(1, 8),
	(1, 9),
	(1, 10),
	(1, 11),
	(1, 12),
	(1, 13),
	(2, 1),
	(2, 2),
	(2, 3),
	(2, 4),
	(2, 5),
	(2, 6),
	(2, 7),
	(2, 8),
	(2, 9),
	(2, 10),
	(2, 11),
	(2, 12),
	(2, 13),
	(3, 2),
	(3, 3),
	(3, 4),
	(3, 5),
	(3, 6),
	(3, 7),
	(3, 8),
	(3, 9),
	(3, 10),
	(3, 11),
	(3, 12),
	(3, 13),
	(4, 3),
	(4, 5),
	(4, 6),
	(4, 7),
	(4, 8),
	(4, 9),
	(4, 10),
	(4, 11),
	(4, 12);

-- membuang struktur untuk table simpleak_v35.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.roles: ~4 rows (lebih kurang)
INSERT INTO `roles` (`id`, `role_name`, `description`) VALUES
	(1, 'Superadmin', 'Akses penuh ke seluruh sistem dan seluruh tenant.'),
	(2, 'Admin', 'Akses penuh ke data satu tenant spesifik.'),
	(3, 'Manager', 'Akses operasional dan laporan keuangan tenant.'),
	(4, 'Staff', 'Akses input data transaksi dasar.');

-- membuang struktur untuk table simpleak_v35.tenants
CREATE TABLE IF NOT EXISTS `tenants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'active',
  `database_type` enum('jasa','dagang','manufaktur') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'dagang',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.tenants: ~3 rows (lebih kurang)
INSERT INTO `tenants` (`id`, `name`, `code`, `status`, `database_type`, `created_at`, `updated_at`) VALUES
	(1, 'UD USAHA DAGANG', 'DAGANGJAYA', 'active', 'dagang', '2026-05-09 04:54:37', '2026-05-09 05:46:23'),
	(2, 'PT Maju Jaya', 'MAJUJAYA', 'active', 'manufaktur', '2026-05-09 05:37:36', '2026-05-09 05:43:54'),
	(3, 'Penjahit Modern', 'penjahitmodern', 'active', 'jasa', '2026-05-09 09:42:33', '2026-05-09 09:42:33');

-- membuang struktur untuk table simpleak_v35.users
CREATE TABLE IF NOT EXISTS `users` (
  `id_user` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) DEFAULT 1,
  `nama_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_lengkap` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `jabatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `nama_user` (`nama_user`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Membuang data untuk tabel simpleak_v35.users: ~6 rows (lebih kurang)
INSERT INTO `users` (`id_user`, `tenant_id`, `nama_user`, `nama_lengkap`, `password_hash`, `role`, `role_id`, `jabatan`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 1, 'kurniawan', NULL, '$2y$12/weIxUxOxoHZESXiI5RpOcXwkha7lpvN3nr9yee', 'Admin', 2, 'Direktur', NULL, '2026-05-09 04:54:52', NULL),
	(2, 1, 'superadmin', NULL, '$2y$12$7yC4ASJfY4eOmVVfmHzosesPzo3J9A/ZGHkb63iVTGlUdeRUdj0Gy', 'Superadmin', 1, 'CEO', NULL, NULL, NULL),
	(3, 1, 'manajer', NULL, '$2y$12$gZ7erYKkzrUHsCMoz3jm6e5.8bwSu/pjhGoYOIFFT1AJLCK1Fx62W', 'Manager', 3, 'Finance Manager', NULL, NULL, NULL),
	(4, 1, 'staff', NULL, '$2y$12$hwrdWK1G9AilbZ12yiVUtO4MwwHuEUsRhNQQqYgfpOxz1lyt.08OO', 'Staff', 4, 'Accounting Staff', NULL, NULL, NULL),
	(5, 2, 'manajerjaya', 'Anton Hilman', '$2y$12$gZ7erYKkzrUHsCMoz3jm6e5.8bwSu/pjhGoYOIFFT1AJLCK1Fx62W', 'Manager', 3, 'Finance Manager', NULL, NULL, NULL),
	(6, 3, 'penjahit', 'Budi Santoso', '$2y$12$vu8P1MrJk2tKcbYHv32vcOyQnehp7m9y3JHJlGJOZLf6LgWsOrOc6', 'Admin', 2, 'Pemilik', NULL, NULL, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

