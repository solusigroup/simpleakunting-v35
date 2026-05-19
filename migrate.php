<?php
/**
 * Script untuk update database production
 */
require_once 'app/config.php';
require_once 'app/core/Database.php';

$db = new Database();

$sql = "
-- 1. Update Tabel Akun
ALTER TABLE akun ADD COLUMN IF NOT EXISTS saldo_awal DECIMAL(15,2) DEFAULT 0.00;
ALTER TABLE akun ADD COLUMN IF NOT EXISTS posisi_saldo_normal VARCHAR(20) DEFAULT 'Debit';

-- 2. Tabel Penerimaan Pelanggan
CREATE TABLE IF NOT EXISTS penerimaan_pelanggan (
    id_penerimaan BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_pelanggan BIGINT UNSIGNED NOT NULL,
    id_jurnal BIGINT UNSIGNED NOT NULL,
    no_bukti VARCHAR(50) NOT NULL,
    tanggal DATE NOT NULL,
    akun_kas_bank VARCHAR(20) NOT NULL,
    total_diterima DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan),
    FOREIGN KEY (id_jurnal) REFERENCES jurnal_umum(id_jurnal)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS penerimaan_pelanggan_detail (
    id_detail BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_penerimaan BIGINT UNSIGNED NOT NULL,
    id_penjualan VARCHAR(50) NOT NULL,
    jumlah_bayar DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_penerimaan) REFERENCES penerimaan_pelanggan(id_penerimaan) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. Tabel Pembayaran Pemasok
CREATE TABLE IF NOT EXISTS pembayaran_pemasok (
    id_pembayaran BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_pemasok BIGINT UNSIGNED NOT NULL,
    id_jurnal BIGINT UNSIGNED NOT NULL,
    no_bukti VARCHAR(50) NOT NULL,
    tanggal DATE NOT NULL,
    akun_kas_bank VARCHAR(20) NOT NULL,
    total_dibayar DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pemasok) REFERENCES pemasok(id_pemasok),
    FOREIGN KEY (id_jurnal) REFERENCES jurnal_umum(id_jurnal)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pembayaran_pemasok_detail (
    id_detail BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_pembayaran BIGINT UNSIGNED NOT NULL,
    id_pembelian VARCHAR(50) NOT NULL,
    jumlah_bayar DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pembayaran) REFERENCES pembayaran_pemasok(id_pembayaran) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Tabel Kartu Stok
CREATE TABLE IF NOT EXISTS kartu_stok (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_barang BIGINT UNSIGNED NOT NULL,
    tipe_transaksi ENUM('IN','OUT') NOT NULL,
    kuantitas DECIMAL(10,2) NOT NULL,
    keterangan VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Tambah Kolom Kategori di Master Persediaan
ALTER TABLE master_persediaan ADD COLUMN IF NOT EXISTS kategori ENUM('Persediaan Bahan Baku','Persediaan Bahan Penolong','Persediaan WIP','Persediaan Barang Jadi/Barang Dagangan') NOT NULL DEFAULT 'Persediaan Bahan Baku' AFTER nama_barang;

-- 6. Ubah Unique Key master_persediaan agar Unik per Tenant
ALTER TABLE master_persediaan DROP INDEX kode_barang;
ALTER TABLE master_persediaan ADD UNIQUE KEY kode_barang (tenant_id, kode_barang);
";

try {
    echo "Memulai migrasi database...\n";
    // PDO exec bisa menjalankan multiple query sekaligus
    $db->query($sql);
    $db->execute();
    echo "✅ Database berhasil diperbarui!\n";
} catch (Exception $e) {
    echo "❌ Error migrasi: " . $e->getMessage() . "\n";
}
