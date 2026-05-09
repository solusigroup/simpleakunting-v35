<?php
require_once 'app/config.php';
$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    // Ambil semua tenant
    $stmt = $pdo->query("SELECT id, name FROM tenants");
    $tenants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($tenants as $t) {
        $tid = $t['id'];
        $nama = $t['name'];
        echo "Seeding untuk Tenant: $nama (ID: $tid)...\n";

        // 1. Pelanggan
        $pdo->exec("INSERT IGNORE INTO pelanggan (tenant_id, nama_pelanggan, email, telepon, alamat) VALUES 
            ($tid, 'Toko Maju Bersama ($nama)', 'maju@example.com', '081234567890', 'Jl. Merdeka No. 1'),
            ($tid, 'CV Makmur Jaya ($nama)', 'makmur@example.com', '081987654321', 'Jl. Sudirman No. 2')");

        // 2. Pemasok
        $pdo->exec("INSERT IGNORE INTO pemasok (tenant_id, nama_pemasok, email, telepon, alamat) VALUES 
            ($tid, 'PT Sumber Material ($nama)', 'sumber@example.com', '085512341234', 'Kawasan Industri 1'),
            ($tid, 'UD Lancar Barokah ($nama)', 'lancar@example.com', '087799998888', 'Pasar Induk')");

        // 3. Aset Tetap
        $pdo->exec("INSERT IGNORE INTO aset_tetap (tenant_id, kode_aset, nama_aset, tanggal_perolehan, harga_perolehan, umur_ekonomis, nilai_residu, metode_penyusutan, akun_aset, akun_akumulasi, akun_beban) VALUES 
            ($tid, 'AST-001', 'Mesin Produksi Alpha ($nama)', '2025-01-01', 50000000.00, 60, 5000000.00, 'garis_lurus', '1-2100', '1-2110', '6-1100')");

        // 4. Persediaan
        $pdo->exec("INSERT IGNORE INTO master_persediaan (tenant_id, kode_barang, nama_barang, kategori, satuan, stok_awal, stok_saat_ini, harga_beli, harga_jual, akun_persediaan, akun_hpp, akun_penjualan) VALUES 
            ($tid, 'BB-01', 'Tepung Terigu Premium', 'Persediaan Bahan Baku', 'Kg', 100, 100, 10000, 0, '1-1400', '5-1000', '4-1000'),
            ($tid, 'BP-01', 'Plastik Kemasan', 'Persediaan Bahan Penolong', 'Pcs', 500, 500, 500, 0, '1-1400', '5-1000', '4-1000'),
            ($tid, 'WP-01', 'Adonan Setengah Jadi', 'Persediaan WIP', 'Kg', 0, 0, 15000, 0, '1-1400', '5-1000', '4-1000'),
            ($tid, 'BJ-01', 'Roti Manis Spesial', 'Persediaan Barang Jadi/Barang Dagangan', 'Pcs', 50, 50, 2000, 5000, '1-1400', '5-1000', '4-1000')");
        
        $id_bahan_baku = $pdo->query("SELECT id_barang FROM master_persediaan WHERE tenant_id = $tid AND kode_barang = 'BB-01'")->fetchColumn();
        $id_barang_jadi = $pdo->query("SELECT id_barang FROM master_persediaan WHERE tenant_id = $tid AND kode_barang = 'BJ-01'")->fetchColumn();

        // 5. BOM
        if ($id_bahan_baku && $id_barang_jadi) {
            $pdo->exec("INSERT IGNORE INTO bom (tenant_id, nama_bom, id_barang_jadi, total_biaya_estimasi) VALUES ($tid, 'Resep Roti Manis', $id_barang_jadi, 2000)");
            $id_bom = $pdo->lastInsertId();
            if ($id_bom) {
                $pdo->exec("INSERT IGNORE INTO bom_detail (id_bom, id_bahan_baku, jumlah, satuan, biaya_satuan) VALUES ($id_bom, $id_bahan_baku, 0.2, 'Kg', 2000)");
            }
        }

        // 6. Kas Transaksi
        $pdo->exec("INSERT IGNORE INTO kas_transaksi (tenant_id, tipe_transaksi, tanggal, no_bukti, akun_kas_bank, akun_lawan, jumlah, deskripsi) VALUES 
            ($tid, 'Masuk', '2026-05-01', 'KM-001', '1-1100', '3-1000', 10000000.00, 'Setoran Modal Awal'),
            ($tid, 'Keluar', '2026-05-02', 'KK-001', '1-1100', '6-1000', 500000.00, 'Membayar Listrik dan Air')");

        // 7. Jurnal Umum
        $pdo->exec("INSERT IGNORE INTO jurnal_umum (tenant_id, no_transaksi, tanggal, deskripsi, sumber_jurnal) VALUES 
            ($tid, 'JU-001', '2026-05-03', 'Penyesuaian Persediaan Awal', 'Adjustment')");
        $id_jurnal = $pdo->lastInsertId();
        if ($id_jurnal) {
            $pdo->exec("INSERT IGNORE INTO jurnal_detail (id_jurnal, kode_akun, debit, kredit) VALUES 
                ($id_jurnal, '1-1400', 1000000.00, 0.00),
                ($id_jurnal, '3-1000', 0.00, 1000000.00)");
        }
    }
    
    echo "Selesai menambahkan data dummy untuk semua tenant!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
