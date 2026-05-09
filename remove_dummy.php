<?php
require_once 'app/config.php';
$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    echo "Memulai penghapusan data dummy...\n";

    // 1. Jurnal Umum & Detail
    // Mengambil id_jurnal dari jurnal dummy
    $stmt = $pdo->query("SELECT id_jurnal FROM jurnal_umum WHERE no_transaksi = 'JU-001' AND deskripsi = 'Penyesuaian Persediaan Awal'");
    $jurnal_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (!empty($jurnal_ids)) {
        $inQuery = implode(',', $jurnal_ids);
        $pdo->exec("DELETE FROM jurnal_detail WHERE id_jurnal IN ($inQuery)");
        $pdo->exec("DELETE FROM jurnal_umum WHERE id_jurnal IN ($inQuery)");
        echo "- Data Jurnal Umum dummy dihapus.\n";
    }

    // 2. Kas Transaksi
    $pdo->exec("DELETE FROM kas_transaksi WHERE no_bukti IN ('KM-001', 'KK-001')");
    echo "- Data Kas Transaksi dummy dihapus.\n";

    // 3. BOM & BOM Detail
    $stmt = $pdo->query("SELECT id FROM bom WHERE nama_bom = 'Resep Roti Manis'");
    $bom_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (!empty($bom_ids)) {
        $inQuery = implode(',', $bom_ids);
        $pdo->exec("DELETE FROM bom_detail WHERE id_bom IN ($inQuery)");
        $pdo->exec("DELETE FROM bom WHERE id IN ($inQuery)");
        echo "- Data BOM dummy dihapus.\n";
    }

    // 4. Persediaan
    $pdo->exec("DELETE FROM master_persediaan WHERE kode_barang IN ('BB-01', 'BP-01', 'WP-01', 'BJ-01')");
    echo "- Data Persediaan dummy dihapus.\n";

    // 5. Aset Tetap
    $pdo->exec("DELETE FROM aset_tetap WHERE kode_aset = 'AST-001'");
    echo "- Data Aset Tetap dummy dihapus.\n";

    // 6. Pemasok
    $pdo->exec("DELETE FROM pemasok WHERE nama_pemasok LIKE 'PT Sumber Material %' OR nama_pemasok LIKE 'UD Lancar Barokah %'");
    echo "- Data Pemasok dummy dihapus.\n";

    // 7. Pelanggan
    $pdo->exec("DELETE FROM pelanggan WHERE nama_pelanggan LIKE 'Toko Maju Bersama %' OR nama_pelanggan LIKE 'CV Makmur Jaya %'");
    echo "- Data Pelanggan dummy dihapus.\n";

    echo "Selesai membersihkan semua data dummy!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
