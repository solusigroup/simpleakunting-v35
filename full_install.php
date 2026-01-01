<?php
/**
 * Script Instalasi Pertama Kali (Full Install)
 * Menghapus tabel lama jika ada dan membuat struktur database baru yang lengkap.
 */
require_once 'app/config.php';
require_once 'app/core/Database.php';

$db = new Database();

$sql = "
SET FOREIGN_KEY_CHECKS = 0;

-- Drop existing tables to start fresh
DROP TABLE IF EXISTS `akun`, `cache`, `cache_locks`, `failed_jobs`, `job_batches`, `jobs`, `jurnal_detail`, `jurnal_umum`, `kartu_stok`, `master_persediaan`, `migrations`, `pelanggan`, `pemasok`, `pembayaran_pemasok`, `pembayaran_pemasok_detail`, `pembelian`, `pembelian_detail`, `penerimaan_pelanggan`, `penerimaan_pelanggan_detail`, `penjualan`, `penjualan_detail`, `perusahaan`, `users`;

-- Table: akun
CREATE TABLE `akun` (
  `kode_akun` varchar(20) NOT NULL,
  `nama_akun` varchar(255) NOT NULL,
  `tipe_akun` varchar(255) NOT NULL,
  `saldo_normal` varchar(10) NOT NULL,
  `saldo_awal` decimal(15,2) DEFAULT '0.00',
  `posisi_saldo_normal` varchar(20) DEFAULT 'Debit',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_akun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: jurnal_umum
CREATE TABLE `jurnal_umum` (
  `id_jurnal` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_transaksi` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `deskripsi` text,
  `sumber_jurnal` varchar(255) NOT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_jurnal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: jurnal_detail
CREATE TABLE `jurnal_detail` (
  `id_detail` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_jurnal` bigint unsigned NOT NULL,
  `kode_akun` varchar(255) NOT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `kredit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `id_jurnal` (`id_jurnal`),
  CONSTRAINT `jurnal_detail_ibfk_1` FOREIGN KEY (`id_jurnal`) REFERENCES `jurnal_umum` (`id_jurnal`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: pelanggan
CREATE TABLE `pelanggan` (
  `id_pelanggan` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_pelanggan` varchar(255) NOT NULL,
  `alamat` text,
  `telepon` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `saldo_awal_piutang` decimal(15,2) NOT NULL DEFAULT '0.00',
  `saldo_terkini_piutang` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pelanggan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: pemasok
CREATE TABLE `pemasok` (
  `id_pemasok` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_pemasok` varchar(255) NOT NULL,
  `alamat` text,
  `telepon` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `saldo_awal_hutang` decimal(15,2) NOT NULL DEFAULT '0.00',
  `saldo_terkini_hutang` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pemasok`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: master_persediaan
CREATE TABLE `master_persediaan` (
  `id_barang` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(255) NOT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `satuan` varchar(255) NOT NULL,
  `stok_awal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stok_saat_ini` decimal(10,2) NOT NULL DEFAULT '0.00',
  `harga_beli` decimal(15,2) NOT NULL DEFAULT '0.00',
  `harga_jual` decimal(15,2) NOT NULL DEFAULT '0.00',
  `akun_persediaan` varchar(255) DEFAULT NULL,
  `akun_hpp` varchar(255) DEFAULT NULL,
  `akun_penjualan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_barang`),
  UNIQUE KEY `kode_barang` (`kode_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: penjualan
CREATE TABLE `penjualan` (
  `id_penjualan` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_pelanggan` bigint unsigned NOT NULL,
  `id_jurnal` bigint unsigned DEFAULT NULL,
  `no_faktur` varchar(255) NOT NULL,
  `tanggal_faktur` date NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `keterangan` text,
  `metode_pembayaran` varchar(255) NOT NULL,
  `akun_kas_bank` varchar(255) DEFAULT NULL,
  `sisa_tagihan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status_pembayaran` varchar(255) NOT NULL DEFAULT 'Belum Lunas',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_penjualan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: pembelian
CREATE TABLE `pembelian` (
  `id_pembelian` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_pemasok` bigint unsigned NOT NULL,
  `id_jurnal` bigint unsigned DEFAULT NULL,
  `no_faktur_pembelian` varchar(255) NOT NULL,
  `tanggal_faktur` date NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `keterangan` text,
  `metode_pembayaran` varchar(255) NOT NULL,
  `akun_kas_bank` varchar(255) DEFAULT NULL,
  `sisa_tagihan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status_pembayaran` varchar(255) NOT NULL DEFAULT 'Belum Lunas',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pembelian`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: penerimaan_pelanggan
CREATE TABLE `penerimaan_pelanggan` (
  `id_penerimaan` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_pelanggan` bigint unsigned NOT NULL,
  `id_jurnal` bigint unsigned NOT NULL,
  `no_bukti` varchar(50) NOT NULL,
  `tanggal` date NOT NULL,
  `akun_kas_bank` varchar(20) NOT NULL,
  `total_diterima` decimal(15,2) NOT NULL DEFAULT '0.00',
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_penerimaan`),
  CONSTRAINT `fk_penerimaan_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`),
  CONSTRAINT `fk_penerimaan_jurnal` FOREIGN KEY (`id_jurnal`) REFERENCES `jurnal_umum` (`id_jurnal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: penerimaan_pelanggan_detail
CREATE TABLE `penerimaan_pelanggan_detail` (
  `id_detail` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_penerimaan` bigint unsigned NOT NULL,
  `id_penjualan` varchar(50) NOT NULL,
  `jumlah_bayar` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_detail`),
  CONSTRAINT `fk_penerimaan_detail_header` FOREIGN KEY (`id_penerimaan`) REFERENCES `penerimaan_pelanggan` (`id_penerimaan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: pembayaran_pemasok
CREATE TABLE `pembayaran_pemasok` (
  `id_pembayaran` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_pemasok` bigint unsigned NOT NULL,
  `id_jurnal` bigint unsigned NOT NULL,
  `no_bukti` varchar(50) NOT NULL,
  `tanggal` date NOT NULL,
  `akun_kas_bank` varchar(20) NOT NULL,
  `total_dibayar` decimal(15,2) NOT NULL DEFAULT '0.00',
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pembayaran`),
  CONSTRAINT `fk_pembayaran_pemasok` FOREIGN KEY (`id_pemasok`) REFERENCES `pemasok` (`id_pemasok`),
  CONSTRAINT `fk_pembayaran_jurnal` FOREIGN KEY (`id_jurnal`) REFERENCES `jurnal_umum` (`id_jurnal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: pembayaran_pemasok_detail
CREATE TABLE `pembayaran_pemasok_detail` (
  `id_detail` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_pembayaran` bigint unsigned NOT NULL,
  `id_pembelian` varchar(50) NOT NULL,
  `jumlah_bayar` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_detail`),
  CONSTRAINT `fk_pembayaran_detail_header` FOREIGN KEY (`id_pembayaran`) REFERENCES `pembayaran_pemasok` (`id_pembayaran`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: perusahaan
CREATE TABLE `perusahaan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_perusahaan` varchar(255) NOT NULL,
  `alamat` text,
  `telepon` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `akun_piutang_default` varchar(255) DEFAULT NULL,
  `akun_utang_default` varchar(255) DEFAULT NULL,
  `nama_direktur` varchar(255) DEFAULT NULL,
  `nama_akuntan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: users
CREATE TABLE `users` (
  `id_user` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_user` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `jabatan` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `nama_user` (`nama_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- SEED DATA
INSERT INTO `users` (`id_user`, `nama_user`, `password_hash`, `role`, `jabatan`, `created_at`) VALUES 
(1, 'kurniawan', '$2y$12$NjXttksnY5fGOO/weIxUxOxoHZESXiI5RpOcXwkha7lpvN3nr9yee', 'Admin', 'Direktur', NOW());

INSERT INTO `perusahaan` (`id`, `nama_perusahaan`, `nama_direktur`, `nama_akuntan`, `updated_at`) VALUES 
(1, 'KOPERASI KELURAHAN MERAH PUTIH SURODINAWAN', 'Wawan', 'Kurniawan', NOW());

INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('1-10001', 'Kas Kecil', 'Kas & Bank', 'Debit', 0.00, 'Debit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('1-10002', 'Bank BCA', 'Kas & Bank', 'Debit', 0.00, 'Debit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('1-10003', 'Bank Mandiri', 'Kas & Bank', 'Debit', 0.00, 'Debit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('1-10100', 'Piutang Usaha', 'Piutang', 'Debit', 0.00, 'Debit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('1-10200', 'Persediaan Barang Dagang', 'Persediaan', 'Debit', 0.00, 'Debit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('1-10300', 'Perlengkapan', 'Aset Lancar Lainnya', 'Debit', 0.00, 'Debit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('1-10400', 'Sewa Dibayar Dimuka', 'Aset Lancar Lainnya', 'Debit', 0.00, 'Debit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('1-20100', 'Peralatan Kantor', 'Aset Tetap', 'Debit', 0.00, 'Debit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('1-20101', 'Akum. Peny. Peralatan', 'Aset Tetap', 'Kredit', 0.00, 'Kredit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('1-20200', 'Kendaraan', 'Aset Tetap', 'Debit', 0.00, 'Debit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('1-20201', 'Akum. Peny. Kendaraan', 'Aset Tetap', 'Kredit', 0.00, 'Kredit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('2-10100', 'Utang Usaha', 'Utang Usaha', 'Kredit', 0.00, 'Kredit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('2-10200', 'Utang Gaji', 'Kewajiban Lancar Lainnya', 'Kredit', 0.00, 'Kredit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('2-10300', 'Utang Pajak', 'Kewajiban Lancar Lainnya', 'Kredit', 0.00, 'Kredit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('2-20100', 'Utang Bank Jangka Panjang', 'Kewajiban Jangka Panjang', 'Kredit', 0.00, 'Kredit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('3-10000', 'Modal Pemilik', 'Ekuitas', 'Kredit', 0.00, 'Kredit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('3-20000', 'Prive Pemilik', 'Ekuitas', 'Debit', 0.00, 'Debit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('3-30000', 'Laba Ditahan', 'Ekuitas', 'Kredit', 0.00, 'Kredit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('4-10000', 'Penjualan Barang', 'Pendapatan', 'Kredit', 0.00, 'Kredit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('4-20000', 'Pendapatan Jasa', 'Pendapatan', 'Kredit', 0.00, 'Kredit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('4-30000', 'Retur Penjualan', 'Pendapatan', 'Debit', 0.00, 'Debit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('4-40000', 'Potongan Penjualan', 'Pendapatan', 'Debit', 0.00, 'Debit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('5-10000', 'Harga Pokok Penjualan', 'HPP', 'Debit', 0.00, 'Debit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('6-10001', 'Beban Gaji', 'Beban', 'Debit', 0.00, 'Debit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('6-10002', 'Beban Sewa', 'Beban', 'Debit', 0.00, 'Debit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('6-10003', 'Beban Listrik, Air & Telp', 'Beban', 'Debit', 0.00, 'Debit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('6-10004', 'Beban Perlengkapan', 'Beban', 'Debit', 0.00, 'Debit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('6-10005', 'Beban Penyusutan', 'Beban', 'Debit', 0.00, 'Debit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('6-10006', 'Beban Pemasaran', 'Beban', 'Debit', 0.00, 'Debit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('6-10007', 'Beban Lain-lain', 'Beban', 'Debit', 0.00, 'Debit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('8-10000', 'Pendapatan Bunga', 'Pendapatan Lainnya', 'Kredit', 0.00, 'Kredit');
INSERT INTO akun (kode_akun, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) VALUES ('9-10000', 'Beban Administrasi Bank', 'Beban Lainnya', 'Debit', 0.00, 'Debit');
";

try {
    echo "Memulai instalasi database baru...\n";
    // PDO::exec can execute multiple queries if the driver supports it
    // Our Database class uses PDO, but execute() might only do one query.
    // We'll use the raw PDO connection for this.
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec($sql);
    echo "✅ Instalasi Database Berhasil!\n";
    echo "Default User: kurniawan / password: user (silakan segera ganti)\n";
} catch (Exception $e) {
    echo "❌ Error Instalasi: " . $e->getMessage() . "\n";
}
