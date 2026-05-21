<?php
/**
 * POS Model for SimpleAkunting v3.5
 * Handles database operations for Point of Sales (POS)
 */
require_once 'Penjualan_model.php';

class Pos_model {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Search active products by keyword (name, code, barcode)
    public function cariBarang($keyword, $tenant_id) {
        $kw = '%' . $keyword . '%';
        $this->db->query("SELECT id_barang, kode_barang, barcode, nama_barang, satuan, stok_saat_ini, harga_jual, harga_beli
                          FROM master_persediaan 
                          WHERE tenant_id = :tenant_id 
                            AND (nama_barang LIKE :kw OR kode_barang LIKE :kw OR barcode LIKE :kw)
                            AND stok_saat_ini > 0
                          LIMIT 25");
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->bind('kw', $kw);
        return $this->db->resultSet();
    }

    // Get all active inventory products for the tenant
    public function getAllBarangAktif($tenant_id) {
        $this->db->query("SELECT id_barang, kode_barang, barcode, nama_barang, satuan, stok_saat_ini, harga_jual, harga_beli
                          FROM master_persediaan 
                          WHERE tenant_id = :tenant_id 
                          ORDER BY nama_barang ASC");
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->resultSet();
    }

    // Generate unique receipt number: POS/YYYYMMDD/001
    public function generateReceiptNumber($tenant_id) {
        $date = date('Ymd');
        $prefix = "POS/" . $date . "/";
        $pattern = $prefix . "%";

        $this->db->query("SELECT MAX(no_receipt) as max_receipt 
                          FROM pos_transactions 
                          WHERE tenant_id = :tenant_id AND no_receipt LIKE :pattern");
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->bind('pattern', $pattern);
        $row = $db_row = $this->db->single();

        if ($row && !empty($row['max_receipt'])) {
            $lastNum = (int)substr($row['max_receipt'], strlen($prefix));
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        return $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }

    // Save POS specific transaction data
    public function simpanTransaksiPos($data, $tenant_id) {
        $this->db->query("INSERT INTO pos_transactions 
                          (tenant_id, id_penjualan, no_receipt, kasir_id, kasir_name, total, bayar, kembalian, metode_pembayaran, created_at) 
                          VALUES 
                          (:tenant_id, :id_penjualan, :no_receipt, :kasir_id, :kasir_name, :total, :bayar, :kembalian, :metode_pembayaran, CURRENT_TIMESTAMP)");
        
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->bind('id_penjualan', $data['id_penjualan']);
        $this->db->bind('no_receipt', $data['no_receipt']);
        $this->db->bind('kasir_id', $data['kasir_id']);
        $this->db->bind('kasir_name', $data['kasir_name']);
        $this->db->bind('total', $data['total']);
        $this->db->bind('bayar', $data['bayar']);
        $this->db->bind('kembalian', $data['kembalian']);
        $this->db->bind('metode_pembayaran', $data['metode_pembayaran'] ?? 'Tunai');

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    // Get today's POS transactions
    public function getTransaksiHariIni($tenant_id) {
        $this->db->query("SELECT pt.*, p.no_faktur, p.tanggal_faktur 
                          FROM pos_transactions pt
                          JOIN penjualan p ON pt.id_penjualan = p.id_penjualan AND p.tenant_id = pt.tenant_id
                          WHERE pt.tenant_id = :tenant_id AND DATE(pt.created_at) = CURDATE()
                          ORDER BY pt.created_at DESC");
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->resultSet();
    }

    // Get POS transactions by date range
    public function getTransaksiByPeriode($tenant_id, $dari = null, $sampai = null) {
        if (empty($dari)) $dari = date('Y-m-d');
        if (empty($sampai)) $sampai = date('Y-m-d');

        $this->db->query("SELECT pt.*, p.no_faktur, p.tanggal_faktur 
                          FROM pos_transactions pt
                          JOIN penjualan p ON pt.id_penjualan = p.id_penjualan AND p.tenant_id = pt.tenant_id
                          WHERE pt.tenant_id = :tenant_id 
                            AND DATE(pt.created_at) BETWEEN :dari AND :sampai
                          ORDER BY pt.created_at DESC");
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->bind('dari', $dari);
        $this->db->bind('sampai', $sampai);
        return $this->db->resultSet();
    }

    // Get single transaction details
    public function getTransaksiById($id, $tenant_id) {
        $this->db->query("SELECT pt.*, p.id_pelanggan, p.no_faktur, p.tanggal_faktur, p.pajak as total_pajak, p.diskon as total_diskon,
                                 pl.nama_pelanggan, pr.nama_perusahaan, pr.alamat as alamat_perusahaan, pr.telepon as telepon_perusahaan
                          FROM pos_transactions pt
                          JOIN penjualan p ON pt.id_penjualan = p.id_penjualan AND p.tenant_id = pt.tenant_id
                          LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan AND pl.tenant_id = pt.tenant_id
                          LEFT JOIN perusahaan pr ON pr.tenant_id = pt.tenant_id
                          WHERE pt.id = :id AND pt.tenant_id = :tenant_id");
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $tx = $this->db->single();

        if (!$tx) return false;

        // Fetch details (items)
        $this->db->query("SELECT pd.*, ms.kode_barang, ms.nama_barang, ms.satuan 
                          FROM penjualan_detail pd
                          JOIN master_persediaan ms ON pd.id_barang = ms.id_barang AND ms.tenant_id = :tenant_id
                          WHERE pd.id_penjualan = :id_penjualan");
        $this->db->bind('id_penjualan', $tx['id_penjualan']);
        $this->db->bind('tenant_id', $tenant_id);
        $tx['details'] = $this->db->resultSet();

        return $tx;
    }

    // Get default Walk-in Customer
    public function getWalkInCustomer($tenant_id) {
        $this->db->query("SELECT * FROM pelanggan WHERE tenant_id = :tenant_id AND nama_pelanggan = 'Walk-in Customer' LIMIT 1");
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->single();
    }

    // Get statistics for today
    public function getStatistikHariIni($tenant_id) {
        $this->db->query("SELECT COUNT(id) as jumlah_transaksi, COALESCE(SUM(total), 0) as total_penjualan 
                          FROM pos_transactions 
                          WHERE tenant_id = :tenant_id AND DATE(created_at) = CURDATE()");
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->single();
    }
}
