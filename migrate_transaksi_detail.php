<?php
/**
 * Migrasi Struktur Tabel Transaksi Detail (Pembelian & Penjualan)
 * SimpleAkunting v3.5
 */
require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/core/Database.php';

$db = new Database();

echo "Memeriksa dan memperbarui tabel transaksi dan detail (Pembelian & Penjualan)...\n";

try {
    // 1. Pastikan tabel pembelian ada
    $db->query("CREATE TABLE IF NOT EXISTS `pembelian` (
        `id_pembelian` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `tenant_id` int(11) DEFAULT 1,
        `id_pemasok` bigint(20) unsigned NOT NULL,
        `id_jurnal` bigint(20) unsigned DEFAULT NULL,
        `no_faktur_pembelian` varchar(255) NOT NULL,
        `tanggal_faktur` date NOT NULL,
        `total` decimal(15,2) NOT NULL DEFAULT 0.00,
        `pajak` decimal(15,2) DEFAULT 0.00,
        `diskon` decimal(15,2) DEFAULT 0.00,
        `keterangan` text DEFAULT NULL,
        `metode_pembayaran` varchar(255) NOT NULL,
        `akun_kas_bank` varchar(255) DEFAULT NULL,
        `sisa_tagihan` decimal(15,2) NOT NULL DEFAULT 0.00,
        `status_pembayaran` varchar(255) NOT NULL DEFAULT 'Belum Lunas',
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id_pembelian`),
        KEY `idx_tenant_pembelian` (`tenant_id`, `no_faktur_pembelian`),
        KEY `idx_pemasok` (`id_pemasok`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
    $db->execute();

    // Verifikasi kolom tabel pembelian
    $db->query("SHOW COLUMNS FROM `pembelian`");
    $colsPembelian = array_column($db->resultSet(), 'Field');
    if (!in_array('tenant_id', $colsPembelian)) {
        echo "- Menambahkan kolom 'tenant_id' ke tabel pembelian...\n";
        $db->query("ALTER TABLE `pembelian` ADD COLUMN `tenant_id` int(11) DEFAULT 1 AFTER `id_pembelian`");
        $db->execute();
    }
    if (!in_array('pajak', $colsPembelian)) {
        echo "- Menambahkan kolom 'pajak' ke tabel pembelian...\n";
        $db->query("ALTER TABLE `pembelian` ADD COLUMN `pajak` decimal(15,2) DEFAULT 0.00 AFTER `total`");
        $db->execute();
    }
    if (!in_array('diskon', $colsPembelian)) {
        echo "- Menambahkan kolom 'diskon' ke tabel pembelian...\n";
        $db->query("ALTER TABLE `pembelian` ADD COLUMN `diskon` decimal(15,2) DEFAULT 0.00 AFTER `pajak`");
        $db->execute();
    }
    if (!in_array('sisa_tagihan', $colsPembelian)) {
        echo "- Menambahkan kolom 'sisa_tagihan' ke tabel pembelian...\n";
        $db->query("ALTER TABLE `pembelian` ADD COLUMN `sisa_tagihan` decimal(15,2) NOT NULL DEFAULT 0.00 AFTER `akun_kas_bank`");
        $db->execute();
    }
    if (!in_array('status_pembayaran', $colsPembelian)) {
        echo "- Menambahkan kolom 'status_pembayaran' ke tabel pembelian...\n";
        $db->query("ALTER TABLE `pembelian` ADD COLUMN `status_pembayaran` varchar(255) NOT NULL DEFAULT 'Belum Lunas' AFTER `sisa_tagihan`");
        $db->execute();
    }

    // 2. Pastikan tabel pembelian_detail ada
    $db->query("CREATE TABLE IF NOT EXISTS `pembelian_detail` (
        `id_detail` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `id_pembelian` bigint(20) unsigned NOT NULL,
        `id_barang` bigint(20) unsigned NOT NULL,
        `kuantitas` decimal(15,2) NOT NULL DEFAULT 0.00,
        `harga` decimal(15,2) NOT NULL DEFAULT 0.00,
        `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
        `akun_beban_persediaan` varchar(50) DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id_detail`),
        KEY `idx_pembelian_detail_pembelian` (`id_pembelian`),
        KEY `idx_pembelian_detail_barang` (`id_barang`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
    $db->execute();

    // 3. Pastikan tabel penjualan ada
    $db->query("CREATE TABLE IF NOT EXISTS `penjualan` (
        `id_penjualan` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `tenant_id` int(11) DEFAULT 1,
        `id_pelanggan` bigint(20) unsigned NOT NULL,
        `id_jurnal` bigint(20) unsigned DEFAULT NULL,
        `no_faktur` varchar(255) NOT NULL,
        `tanggal_faktur` date NOT NULL,
        `total` decimal(15,2) NOT NULL DEFAULT 0.00,
        `pajak` decimal(15,2) DEFAULT 0.00,
        `diskon` decimal(15,2) DEFAULT 0.00,
        `keterangan` text DEFAULT NULL,
        `metode_pembayaran` varchar(255) NOT NULL,
        `akun_kas_bank` varchar(255) DEFAULT NULL,
        `sisa_tagihan` decimal(15,2) NOT NULL DEFAULT 0.00,
        `status_pembayaran` varchar(255) NOT NULL DEFAULT 'Belum Lunas',
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id_penjualan`),
        KEY `idx_tenant_penjualan` (`tenant_id`, `no_faktur`),
        KEY `idx_pelanggan` (`id_pelanggan`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
    $db->execute();

    // Verifikasi kolom tabel penjualan
    $db->query("SHOW COLUMNS FROM `penjualan`");
    $colsPenjualan = array_column($db->resultSet(), 'Field');
    if (!in_array('tenant_id', $colsPenjualan)) {
        echo "- Menambahkan kolom 'tenant_id' ke tabel penjualan...\n";
        $db->query("ALTER TABLE `penjualan` ADD COLUMN `tenant_id` int(11) DEFAULT 1 AFTER `id_penjualan`");
        $db->execute();
    }
    if (!in_array('pajak', $colsPenjualan)) {
        echo "- Menambahkan kolom 'pajak' ke tabel penjualan...\n";
        $db->query("ALTER TABLE `penjualan` ADD COLUMN `pajak` decimal(15,2) DEFAULT 0.00 AFTER `total`");
        $db->execute();
    }
    if (!in_array('diskon', $colsPenjualan)) {
        echo "- Menambahkan kolom 'diskon' ke tabel penjualan...\n";
        $db->query("ALTER TABLE `penjualan` ADD COLUMN `diskon` decimal(15,2) DEFAULT 0.00 AFTER `pajak`");
        $db->execute();
    }
    if (!in_array('sisa_tagihan', $colsPenjualan)) {
        echo "- Menambahkan kolom 'sisa_tagihan' ke tabel penjualan...\n";
        $db->query("ALTER TABLE `penjualan` ADD COLUMN `sisa_tagihan` decimal(15,2) NOT NULL DEFAULT 0.00 AFTER `akun_kas_bank`");
        $db->execute();
    }
    if (!in_array('status_pembayaran', $colsPenjualan)) {
        echo "- Menambahkan kolom 'status_pembayaran' ke tabel penjualan...\n";
        $db->query("ALTER TABLE `penjualan` ADD COLUMN `status_pembayaran` varchar(255) NOT NULL DEFAULT 'Belum Lunas' AFTER `sisa_tagihan`");
        $db->execute();
    }

    // 4. Pastikan tabel penjualan_detail ada
    $db->query("CREATE TABLE IF NOT EXISTS `penjualan_detail` (
        `id_detail` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `id_penjualan` bigint(20) unsigned NOT NULL,
        `id_barang` bigint(20) unsigned NOT NULL,
        `kuantitas` decimal(15,2) NOT NULL DEFAULT 0.00,
        `harga` decimal(15,2) NOT NULL DEFAULT 0.00,
        `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
        `akun_pendapatan` varchar(50) DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id_detail`),
        KEY `idx_penjualan_detail_penjualan` (`id_penjualan`),
        KEY `idx_penjualan_detail_barang` (`id_barang`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
    $db->execute();

    echo "✅ Tabel pembelian, pembelian_detail, penjualan, dan penjualan_detail berhasil dimigrasi & diverifikasi!\n";
} catch (Throwable $e) {
    echo "❌ Error migrasi detail transaksi: " . $e->getMessage() . "\n";
}
