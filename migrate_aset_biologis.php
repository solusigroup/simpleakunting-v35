<?php
/**
 * Migration script for Biological Assets (Aset Biologis) module
 * Conforms to PSAK 241 / IAS 41 Agriculture
 * SimpleAkunting v3.6
 */
require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/core/Database.php';

$db = new Database();

$sql = "
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `aset_biologis` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `kode_aset` VARCHAR(50) NOT NULL,
    `nama_aset` VARCHAR(255) NOT NULL,
    `kategori` ENUM('Hewan Konsumsi', 'Hewan Produktif', 'Tanaman Konsumsi', 'Tanaman Produktif', 'Hasil pada Tanaman') NOT NULL,
    `jenis` VARCHAR(100) DEFAULT NULL COMMENT 'Spesies / ras / varietas',
    `status_kematangan` ENUM('Belum Menghasilkan', 'Menghasilkan') NOT NULL DEFAULT 'Belum Menghasilkan',
    `metode_pengukuran` ENUM('Nilai Wajar', 'Biaya Perolehan') NOT NULL DEFAULT 'Nilai Wajar',
    `tanggal_perolehan` DATE NOT NULL,
    `biaya_perolehan` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    `biaya_jual` DECIMAL(18,2) NOT NULL DEFAULT 0.00 COMMENT 'Estimasi biaya untuk menjual',
    `nilai_wajar` DECIMAL(18,2) NOT NULL DEFAULT 0.00 COMMENT 'Nilai wajar pasar sebelum biaya jual',
    `nilai_tercatat` DECIMAL(18,2) NOT NULL DEFAULT 0.00 COMMENT 'Carrying amount = Nilai Wajar - Biaya Jual',
    `kuantitas` DECIMAL(18,4) NOT NULL DEFAULT 0.0000 COMMENT 'Jumlah fisik: ekor, pohon, ha, kg',
    `satuan` VARCHAR(50) DEFAULT 'Ekor' COMMENT 'Ekor, Pohon, Ha, Kg, Ton, Liter',
    `lokasi` VARCHAR(255) DEFAULT NULL,
    `umur_ekonomis` INT DEFAULT NULL COMMENT 'Bulan, khusus model biaya',
    `akun_aset` VARCHAR(20) NOT NULL COMMENT 'Akun neraca aset biologis',
    `akun_keuntungan_fv` VARCHAR(20) DEFAULT NULL COMMENT 'Akun laba rugi keuntungan nilai wajar',
    `akun_kerugian_fv` VARCHAR(20) DEFAULT NULL COMMENT 'Akun laba rugi kerugian nilai wajar',
    `akun_beban_pemeliharaan` VARCHAR(20) DEFAULT NULL COMMENT 'Akun beban pakan / perawatan',
    `akun_hasil_panen` VARCHAR(20) DEFAULT NULL COMMENT 'Akun persediaan hasil panen agrikultur',
    `akun_keuntungan_panen` VARCHAR(20) DEFAULT NULL COMMENT 'Akun laba rugi keuntungan panen',
    `status` ENUM('Aktif', 'Dipanen', 'Dijual', 'Mati/Hapus') NOT NULL DEFAULT 'Aktif',
    `keterangan` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_ab_tenant` (`tenant_id`),
    INDEX `idx_ab_kategori` (`tenant_id`, `kategori`),
    INDEX `idx_ab_status` (`tenant_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `aset_biologis_penyesuaian` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `id_aset_biologis` INT NOT NULL,
    `id_jurnal` INT DEFAULT NULL,
    `tanggal` DATE NOT NULL,
    `tipe_penyesuaian` ENUM('Nilai Wajar', 'Penyusutan', 'Reklasifikasi') NOT NULL,
    `nilai_tercatat_lama` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    `nilai_tercatat_baru` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    `selisih` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    `kuantitas_lama` DECIMAL(18,4) DEFAULT 0.0000,
    `kuantitas_baru` DECIMAL(18,4) DEFAULT 0.0000,
    `keterangan` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_abp_tenant_aset` (`tenant_id`, `id_aset_biologis`),
    CONSTRAINT `fk_abp_aset` FOREIGN KEY (`id_aset_biologis`) REFERENCES `aset_biologis` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `aset_biologis_panen` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `id_aset_biologis` INT NOT NULL,
    `id_jurnal` INT DEFAULT NULL,
    `tanggal` DATE NOT NULL,
    `nama_hasil` VARCHAR(255) NOT NULL,
    `kuantitas` DECIMAL(18,4) NOT NULL,
    `satuan` VARCHAR(50) NOT NULL,
    `nilai_wajar_panen` DECIMAL(18,2) NOT NULL,
    `biaya_jual_panen` DECIMAL(18,2) DEFAULT 0.00,
    `akun_persediaan` VARCHAR(20) NOT NULL,
    `keterangan` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_abpanen_tenant_aset` (`tenant_id`, `id_aset_biologis`),
    CONSTRAINT `fk_abpanen_aset` FOREIGN KEY (`id_aset_biologis`) REFERENCES `aset_biologis` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `aset_biologis_pelepasan` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `id_aset_biologis` INT NOT NULL,
    `id_jurnal` INT DEFAULT NULL,
    `tanggal` DATE NOT NULL,
    `tipe_pelepasan` ENUM('Penjualan', 'Kematian', 'Hapus Buku') NOT NULL,
    `kuantitas_dilepas` DECIMAL(18,4) NOT NULL DEFAULT 0.0000,
    `nilai_tercatat_dilepas` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    `nilai_jual` DECIMAL(18,2) DEFAULT 0.00,
    `laba_rugi` DECIMAL(18,2) DEFAULT 0.00,
    `keterangan` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_ablepas_tenant_aset` (`tenant_id`, `id_aset_biologis`),
    CONSTRAINT `fk_ablepas_aset` FOREIGN KEY (`id_aset_biologis`) REFERENCES `aset_biologis` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
";

$accounts = [
    ['1-14100', 'Aset Biologis', 'Header', 'Debit'],
    ['1-14101', 'Aset Biologis - Hewan Konsumsi', 'Detail', 'Debit'],
    ['1-14102', 'Aset Biologis - Hewan Produktif', 'Detail', 'Debit'],
    ['1-14103', 'Aset Biologis - Tanaman Konsumsi', 'Detail', 'Debit'],
    ['1-14104', 'Aset Biologis - Tanaman Produktif', 'Detail', 'Debit'],
    ['1-14105', 'Aset Biologis - Hasil pada Tanaman', 'Detail', 'Debit'],
    ['1-14200', 'Persediaan Produk Agrikultur', 'Detail', 'Debit'],
    ['4-40300', 'Keuntungan Nilai Wajar Aset Biologis', 'Detail', 'Kredit'],
    ['4-40400', 'Keuntungan Panen Produk Agrikultur', 'Detail', 'Kredit'],
    ['4-40500', 'Pendapatan Penjualan Aset Biologis', 'Detail', 'Kredit'],
    ['5-50300', 'Beban Pokok Aset Biologis Terjual', 'Detail', 'Debit'],
    ['6-60400', 'Kerugian Penurunan Nilai Wajar Aset Biologis', 'Detail', 'Debit'],
    ['6-60500', 'Beban Pemeliharaan Aset Biologis', 'Detail', 'Debit'],
    ['6-60600', 'Kerugian Kematian/Hapus Aset Biologis', 'Detail', 'Debit'],
    ['6-60700', 'Beban Panen Agrikultur', 'Detail', 'Debit']
];

try {
    echo "Running Biological Assets (Aset Biologis - PSAK 241) Migration...\n";
    
    // 1. Create Tables
    $db->query($sql);
    $db->execute();
    echo "✅ Tables created successfully: aset_biologis, aset_biologis_penyesuaian, aset_biologis_panen, aset_biologis_pelepasan\n";
    
    // 2. Fetch all tenants
    $db->query("SELECT id FROM tenants");
    $tenants = $db->resultSet();
    if (empty($tenants)) {
        // Fallback tenant 1 if no tenants table records
        $tenants = [['id' => 1]];
    }
    
    // 3. Migrate old 1-107xx and 1-10300 accounts to 1-14xxx if any exist
    $oldToNew = [
        '1-10700' => '1-14100',
        '1-10701' => '1-14101',
        '1-10702' => '1-14102',
        '1-10703' => '1-14103',
        '1-10704' => '1-14104',
        '1-10705' => '1-14105',
        '1-10300' => '1-14200'
    ];

    foreach ($oldToNew as $oldK => $newK) {
        $db->query("UPDATE aset_biologis SET akun_aset = :newK WHERE akun_aset = :oldK");
        $db->bind('newK', $newK);
        $db->bind('oldK', $oldK);
        $db->execute();

        $db->query("UPDATE aset_biologis SET akun_hasil_panen = :newK WHERE akun_hasil_panen = :oldK");
        $db->bind('newK', $newK);
        $db->bind('oldK', $oldK);
        $db->execute();

        $db->query("UPDATE jurnal_detail SET kode_akun = :newK WHERE kode_akun = :oldK");
        $db->bind('newK', $newK);
        $db->bind('oldK', $oldK);
        $db->execute();

        if ($oldK === '1-10300') {
            $db->query("DELETE FROM akun WHERE kode_akun = '1-10300' AND nama_akun LIKE '%Agrikultur%'");
            $db->execute();
        } else {
            $db->query("DELETE FROM akun WHERE kode_akun = :oldK");
            $db->bind('oldK', $oldK);
            $db->execute();
        }
    }
    echo "✅ Cleaned up old 1-107xx accounts and updated references to 1-14xxx.\n";

    // 4. Insert COA entries for each tenant
    $coa_inserted = 0;
    foreach ($tenants as $t) {
        $tenant_id = (int)$t['id'];
        
        foreach ($accounts as $acc) {
            $checkSql = "SELECT kode_akun FROM akun WHERE kode_akun = :kode AND tenant_id = :tenant";
            $db->query($checkSql);
            $db->bind('kode', $acc[0]);
            $db->bind('tenant', $tenant_id);
            if (!$db->single()) {
                $insertSql = "INSERT INTO akun (kode_akun, tenant_id, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) 
                              VALUES (:kode, :tenant, :nama, :tipe, :saldo_normal, 0, :posisi)";
                $db->query($insertSql);
                $db->bind('kode', $acc[0]);
                $db->bind('tenant', $tenant_id);
                $db->bind('nama', $acc[1]);
                $db->bind('tipe', $acc[2]);
                $db->bind('saldo_normal', $acc[3]);
                $db->bind('posisi', $acc[3]);
                $db->execute();
                $coa_inserted++;
            }
        }
    }
    
    echo "✅ Added {$coa_inserted} Chart of Accounts entries for PSAK 241 (1-14xxx) across " . count($tenants) . " tenant(s).\n";
    echo "✅ Migration Completed Successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Migration Error: " . $e->getMessage() . "\n";
}
