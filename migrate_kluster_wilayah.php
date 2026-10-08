<?php
/**
 * Migration script for Kluster Wilayah & Seeding Jawa Timur
 * SimpleAkunting v3.6
 */

if (!defined('DB_HOST')) {
    require_once __DIR__ . '/app/config.php';
}
require_once __DIR__ . '/app/core/Database.php';

$db = new Database();

echo "========================================================\n";
echo "  SimpleAkunting v3.6 - Migrasi Kluster Wilayah\n";
echo "========================================================\n\n";

// 1. Create table kluster_wilayah
try {
    echo "1. Membuat tabel 'kluster_wilayah'...\n";
    $sqlTable = "CREATE TABLE IF NOT EXISTS `kluster_wilayah` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `nama_kabupaten` varchar(100) NOT NULL,
        `kode_kabupaten` varchar(10) NOT NULL UNIQUE,
        `provinsi` varchar(100) DEFAULT 'Jawa Timur',
        `status` enum('active', 'inactive') DEFAULT 'active',
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $db->query($sqlTable);
    $db->execute();
    echo "   ✓ Tabel 'kluster_wilayah' berhasil dibuat atau sudah ada.\n";
} catch (Exception $e) {
    echo "   ⚠ Error pada tabel 'kluster_wilayah': " . $e->getMessage() . "\n";
}

// 2. Add column kluster_wilayah_id to tenants table
try {
    echo "2. Menambahkan kolom 'kluster_wilayah_id' ke tabel 'tenants'...\n";
    $sqlTenants = "ALTER TABLE `tenants` ADD COLUMN `kluster_wilayah_id` int(11) DEFAULT NULL AFTER `database_type`;";
    $db->query($sqlTenants);
    $db->execute();
    echo "   ✓ Kolom 'kluster_wilayah_id' berhasil ditambahkan ke tabel 'tenants'.\n";
} catch (Exception $e) {
    echo "   ⚠ Info 'tenants': " . $e->getMessage() . "\n";
}

// 3. Add column kluster_wilayah_id to users table
try {
    echo "3. Menambahkan kolom 'kluster_wilayah_id' ke tabel 'users'...\n";
    $sqlUsers = "ALTER TABLE `users` ADD COLUMN `kluster_wilayah_id` int(11) DEFAULT NULL AFTER `tenant_id`;";
    $db->query($sqlUsers);
    $db->execute();
    echo "   ✓ Kolom 'kluster_wilayah_id' berhasil ditambahkan ke tabel 'users'.\n";
} catch (Exception $e) {
    echo "   ⚠ Info 'users': " . $e->getMessage() . "\n";
}

// 4. Seed ALL 38 Kabupaten/Kota in Jawa Timur
echo "4. Melakukan seeding 38 Kabupaten/Kota di Jawa Timur...\n";

$wilayah = [
    ['nama_kabupaten' => 'Kab. Bangkalan', 'kode_kabupaten' => '3526'],
    ['nama_kabupaten' => 'Kab. Banyuwangi', 'kode_kabupaten' => '3510'],
    ['nama_kabupaten' => 'Kab. Blitar', 'kode_kabupaten' => '3505'],
    ['nama_kabupaten' => 'Kab. Bojonegoro', 'kode_kabupaten' => '3522'],
    ['nama_kabupaten' => 'Kab. Bondowoso', 'kode_kabupaten' => '3511'],
    ['nama_kabupaten' => 'Kab. Gresik', 'kode_kabupaten' => '3525'],
    ['nama_kabupaten' => 'Kab. Jember', 'kode_kabupaten' => '3509'],
    ['nama_kabupaten' => 'Kab. Jombang', 'kode_kabupaten' => '3517'],
    ['nama_kabupaten' => 'Kab. Kediri', 'kode_kabupaten' => '3506'],
    ['nama_kabupaten' => 'Kab. Lamongan', 'kode_kabupaten' => '3524'],
    ['nama_kabupaten' => 'Kab. Lumajang', 'kode_kabupaten' => '3508'],
    ['nama_kabupaten' => 'Kab. Madiun', 'kode_kabupaten' => '3519'],
    ['nama_kabupaten' => 'Kab. Magetan', 'kode_kabupaten' => '3520'],
    ['nama_kabupaten' => 'Kab. Malang', 'kode_kabupaten' => '3507'],
    ['nama_kabupaten' => 'Kab. Mojokerto', 'kode_kabupaten' => '3516'],
    ['nama_kabupaten' => 'Kab. Nganjuk', 'kode_kabupaten' => '3518'],
    ['nama_kabupaten' => 'Kab. Ngawi', 'kode_kabupaten' => '3521'],
    ['nama_kabupaten' => 'Kab. Pacitan', 'kode_kabupaten' => '3501'],
    ['nama_kabupaten' => 'Kab. Pamekasan', 'kode_kabupaten' => '3528'],
    ['nama_kabupaten' => 'Kab. Pasuruan', 'kode_kabupaten' => '3514'],
    ['nama_kabupaten' => 'Kab. Ponorogo', 'kode_kabupaten' => '3502'],
    ['nama_kabupaten' => 'Kab. Probolinggo', 'kode_kabupaten' => '3513'],
    ['nama_kabupaten' => 'Kab. Sampang', 'kode_kabupaten' => '3527'],
    ['nama_kabupaten' => 'Kab. Sidoarjo', 'kode_kabupaten' => '3515'],
    ['nama_kabupaten' => 'Kab. Situbondo', 'kode_kabupaten' => '3512'],
    ['nama_kabupaten' => 'Kab. Sumenep', 'kode_kabupaten' => '3529'],
    ['nama_kabupaten' => 'Kab. Trenggalek', 'kode_kabupaten' => '3503'],
    ['nama_kabupaten' => 'Kab. Tuban', 'kode_kabupaten' => '3523'],
    ['nama_kabupaten' => 'Kab. Tulungagung', 'kode_kabupaten' => '3504'],
    ['nama_kabupaten' => 'Kota Batu', 'kode_kabupaten' => '3579'],
    ['nama_kabupaten' => 'Kota Blitar', 'kode_kabupaten' => '3572'],
    ['nama_kabupaten' => 'Kota Kediri', 'kode_kabupaten' => '3571'],
    ['nama_kabupaten' => 'Kota Madiun', 'kode_kabupaten' => '3577'],
    ['nama_kabupaten' => 'Kota Malang', 'kode_kabupaten' => '3573'],
    ['nama_kabupaten' => 'Kota Mojokerto', 'kode_kabupaten' => '3576'],
    ['nama_kabupaten' => 'Kota Pasuruan', 'kode_kabupaten' => '3575'],
    ['nama_kabupaten' => 'Kota Probolinggo', 'kode_kabupaten' => '3574'],
    ['nama_kabupaten' => 'Kota Surabaya', 'kode_kabupaten' => '3578'],
];

$successCount = 0;
foreach ($wilayah as $w) {
    try {
        $sqlSeed = "INSERT INTO `kluster_wilayah` (`nama_kabupaten`, `kode_kabupaten`, `provinsi`, `status`) 
                    VALUES (:nama, :kode, 'Jawa Timur', 'active') 
                    ON DUPLICATE KEY UPDATE `nama_kabupaten` = VALUES(`nama_kabupaten`), `provinsi` = VALUES(`provinsi`), `status` = VALUES(`status`);";
        $db->query($sqlSeed);
        $db->bind('nama', $w['nama_kabupaten']);
        $db->bind('kode', $w['kode_kabupaten']);
        $db->execute();
        $successCount++;
    } catch (Exception $e) {
        echo "   ⚠ Gagal seeding {$w['nama_kabupaten']} ({$w['kode_kabupaten']}): " . $e->getMessage() . "\n";
    }
}

echo "   ✓ Berhasil memproses {$successCount} dari " . count($wilayah) . " Kabupaten/Kota.\n\n";
echo "Migrasi kluster wilayah selesai!\n";
