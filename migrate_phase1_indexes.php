<?php
/**
 * Skrip Migrasi Indeks Performa - Tahap 1
 * SimpleAkunting v3.6
 *
 * Menambahkan indeks penting untuk mempercepat query laporan, Buku Besar,
 * Neraca Saldo, Neraca Lajur, dan transaksi harian.
 * Bersifat aman dan idempotent (dapat dijalankan berulang kali).
 */

require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/core/Database.php';

echo "========================================================\n";
echo "  SimpleAkunting v3.6 - Database Index Migration\n";
echo "========================================================\n\n";

try {
    $db = new Database();
    $dbh = $db->dbh;

    $indexes = [
        [
            'table'   => 'jurnal_detail',
            'name'    => 'idx_jd_kode_akun',
            'columns' => 'kode_akun',
            'desc'    => 'Index kode_akun pada jurnal_detail untuk Buku Besar & Neraca Lajur'
        ],
        [
            'table'   => 'jurnal_umum',
            'name'    => 'idx_ju_tenant_tanggal',
            'columns' => 'tenant_id, tanggal',
            'desc'    => 'Composite index tenant_id & tanggal pada jurnal_umum'
        ],
        [
            'table'   => 'penjualan',
            'name'    => 'idx_pj_tenant_tanggal',
            'columns' => 'tenant_id, tanggal_faktur',
            'desc'    => 'Composite index tenant_id & tanggal_faktur pada penjualan'
        ],
        [
            'table'   => 'pembelian',
            'name'    => 'idx_pb_tenant_tanggal',
            'columns' => 'tenant_id, tanggal_faktur',
            'desc'    => 'Composite index tenant_id & tanggal_faktur pada pembelian'
        ],
        [
            'table'   => 'master_persediaan',
            'name'    => 'idx_mp_tenant_id',
            'columns' => 'tenant_id',
            'desc'    => 'Index tenant_id pada master_persediaan'
        ],
        [
            'table'   => 'kas_transaksi',
            'name'    => 'idx_kt_tenant_tanggal',
            'columns' => 'tenant_id, tanggal',
            'desc'    => 'Composite index tenant_id & tanggal pada kas_transaksi'
        ],
    ];

    // 1. Periksa kolom kategori di master_persediaan
    $checkKategori = $dbh->query("SHOW COLUMNS FROM master_persediaan LIKE 'kategori'")->fetch();
    if (!$checkKategori) {
        echo "[+] Menambahkan kolom 'kategori' ke master_persediaan...\n";
        $dbh->exec("ALTER TABLE master_persediaan ADD COLUMN kategori VARCHAR(255) NULL AFTER nama_barang");
        echo "    -> Berhasil!\n";
    }

    // 2. Periksa tabel kartu_stok
    $checkKartu = $dbh->query("SHOW TABLES LIKE 'kartu_stok'")->fetch();
    if (!$checkKartu) {
        echo "[+] Membuat tabel 'kartu_stok'...\n";
        $dbh->exec("CREATE TABLE IF NOT EXISTS `kartu_stok` (
            `id_kartu` bigint unsigned NOT NULL AUTO_INCREMENT,
            `id_barang` bigint unsigned NOT NULL,
            `tipe_transaksi` enum('IN','OUT') NOT NULL,
            `kuantitas` decimal(15,2) NOT NULL,
            `keterangan` text,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id_kartu`),
            KEY `id_barang` (`id_barang`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "    -> Berhasil!\n";
    }

    $successCount = 0;
    $skippedCount = 0;

    foreach ($indexes as $item) {
        $table   = $item['table'];
        $name    = $item['name'];
        $columns = $item['columns'];
        $desc    = $item['desc'];

        // Cek apakah tabel ada
        $checkTable = $dbh->query("SHOW TABLES LIKE '$table'")->fetch();
        if (!$checkTable) {
            echo "[-] Tabel '$table' tidak ditemukan. Dilewati.\n";
            continue;
        }

        // Cek apakah index sudah ada
        $checkIndex = $dbh->query("SHOW INDEX FROM `$table` WHERE Key_name = '$name'")->fetch();
        if ($checkIndex) {
            echo "[=] Index '$name' pada tabel '$table' sudah ada. (Dilewati)\n";
            $skippedCount++;
        } else {
            echo "[+] Membuat index '$name' pada `$table` ($columns)...\n";
            $sql = "CREATE INDEX `$name` ON `$table` ($columns)";
            $dbh->exec($sql);
            echo "    -> Berhasil! ($desc)\n";
            $successCount++;
        }
    }

    echo "\n--------------------------------------------------------\n";
    echo "Migrasi Selesai!\n";
    echo "Indeks baru dibuat : $successCount\n";
    echo "Indeks sudah ada   : $skippedCount\n";
    echo "--------------------------------------------------------\n";

} catch (PDOException $e) {
    echo "\n[ERROR] Gagal menjalankan migrasi: " . $e->getMessage() . "\n";
    exit(1);
}
