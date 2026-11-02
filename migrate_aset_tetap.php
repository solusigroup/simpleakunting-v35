<?php
/**
 * Migrasi Struktur Tabel Aset Tetap & Penyusutan
 * SimpleAkunting v3.5
 */
require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/core/Database.php';

$db = new Database();

echo "Memeriksa dan memperbarui tabel aset_tetap...\n";

try {
    // 1. Pastikan tabel aset_tetap ada
    $db->query("CREATE TABLE IF NOT EXISTS `aset_tetap` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `tenant_id` int(11) NOT NULL,
        `kode_aset` varchar(50) NOT NULL,
        `nama_aset` varchar(255) NOT NULL,
        `tanggal_perolehan` date NOT NULL,
        `harga_perolehan` decimal(15,2) NOT NULL DEFAULT 0.00,
        `nilai_residu` decimal(15,2) DEFAULT 0.00,
        `umur_ekonomis` int(11) NOT NULL DEFAULT 1 COMMENT 'dalam bulan',
        `metode_penyusutan` varchar(50) DEFAULT 'garis_lurus',
        `akun_aset` varchar(20) DEFAULT NULL,
        `akun_akumulasi` varchar(20) DEFAULT NULL,
        `akun_beban` varchar(20) DEFAULT NULL,
        `status` varchar(20) DEFAULT 'Aktif',
        `keterangan` text DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_tenant_aset` (`tenant_id`, `kode_aset`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
    $db->execute();

    // 2. Periksa dan tambahkan kolom jika belum ada
    $db->query("SHOW COLUMNS FROM `aset_tetap`");
    $existingCols = $db->resultSet();
    $fieldMap = [];
    foreach ($existingCols as $col) {
        $fieldMap[$col['Field']] = $col;
    }

    if (!isset($fieldMap['status'])) {
        echo "- Menambahkan kolom 'status' ke aset_tetap...\n";
        $db->query("ALTER TABLE `aset_tetap` ADD COLUMN `status` VARCHAR(20) DEFAULT 'Aktif' AFTER `akun_beban`");
        $db->execute();
    }

    if (!isset($fieldMap['keterangan'])) {
        echo "- Menambahkan kolom 'keterangan' ke aset_tetap...\n";
        $db->query("ALTER TABLE `aset_tetap` ADD COLUMN `keterangan` TEXT DEFAULT NULL AFTER `status`");
        $db->execute();
    }

    if (!isset($fieldMap['updated_at'])) {
        echo "- Menambahkan kolom 'updated_at' ke aset_tetap...\n";
        $db->query("ALTER TABLE `aset_tetap` ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`");
        $db->execute();
    }

    if (isset($fieldMap['metode_penyusutan']) && stripos($fieldMap['metode_penyusutan']['Type'], 'enum') !== false) {
        echo "- Mengubah tipe 'metode_penyusutan' menjadi VARCHAR(50)...\n";
        $db->query("ALTER TABLE `aset_tetap` MODIFY COLUMN `metode_penyusutan` VARCHAR(50) DEFAULT 'garis_lurus'");
        $db->execute();
    }

    // 3. Pastikan tabel penyusutan_aset ada
    $db->query("CREATE TABLE IF NOT EXISTS `penyusutan_aset` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `tenant_id` int(11) NOT NULL,
        `id_aset` bigint(20) unsigned NOT NULL,
        `id_jurnal` int(11) DEFAULT NULL,
        `bulan` tinyint(4) NOT NULL,
        `tahun` smallint(6) NOT NULL,
        `jumlah` decimal(15,2) NOT NULL DEFAULT 0.00,
        `tanggal_proses` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_penyusutan_aset` (`tenant_id`, `id_aset`, `bulan`, `tahun`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
    $db->execute();

    echo "✅ Tabel aset_tetap dan penyusutan_aset berhasil dimigrasi & diverifikasi!\n";
} catch (Throwable $e) {
    echo "❌ Error migrasi aset tetap: " . $e->getMessage() . "\n";
}
