<?php

class Dashboard_model {
    private $db;

    /**
     * Constructor yang menerima koneksi database dari Controller.
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Mengambil ringkasan data utama (Total Piutang, Utang, dan Nilai Persediaan).
     * @param int|null $tenant_id Jika null, ambil semua data (agregat).
     * @return array Ringkasan data.
     */
    public function getSummary($tenant_id = null) {
        $where = $tenant_id ? " WHERE tenant_id = :tenant_id" : "";
        $this->db->query("SELECT 
            (SELECT SUM(saldo_terkini_piutang) FROM pelanggan $where) as total_piutang,
            (SELECT SUM(saldo_terkini_hutang) FROM pemasok $where) as total_utang,
            (SELECT SUM(stok_saat_ini * harga_beli) FROM master_persediaan $where) as nilai_persediaan,
            (SELECT COUNT(*) FROM produksi WHERE status = 'Draft' " . ($tenant_id ? "AND tenant_id = :tenant_id" : "") . ") as total_produksi_aktif
        ");
        if ($tenant_id) {
            $this->db->bind('tenant_id', $tenant_id);
        }
        return $this->db->single();
    }

    /**
     * Mengambil data tren penjualan vs pembelian untuk 6 bulan terakhir.
     * @param int|null $tenant_id Jika null, ambil semua data (agregat).
     * @return array Data tren bulanan.
     */
    public function getSalesPurchasesTrend($tenant_id = null) {
        $where = $tenant_id ? " AND tenant_id = :tenant_id" : "";
        $query = "
            SELECT 
                CONCAT(YEAR(tanggal), '-', LPAD(MONTH(tanggal), 2, '0')) as periode,
                SUM(CASE WHEN tipe = 'penjualan' THEN total ELSE 0 END) as total_penjualan,
                SUM(CASE WHEN tipe = 'pembelian' THEN total ELSE 0 END) as total_pembelian
            FROM (
                SELECT tanggal_faktur as tanggal, total, 'penjualan' as tipe FROM penjualan
                WHERE tanggal_faktur >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) $where
                UNION ALL
                SELECT tanggal_faktur as tanggal, total, 'pembelian' as tipe FROM pembelian
                WHERE tanggal_faktur >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) $where
            ) as transaksi
            GROUP BY periode
            ORDER BY periode ASC
        ";
        $this->db->query($query);
        if ($tenant_id) {
            $this->db->bind('tenant_id', $tenant_id);
        }
        return $this->db->resultSet();
    }

    /**
     * Mengambil data ringkasan untuk Superadmin (Central).
     */
    public function getCentralSummary() {
        $this->db->query("SELECT 
            (SELECT COUNT(*) FROM tenants WHERE status = 'active') as total_active_tenants,
            (SELECT SUM(total) FROM penjualan) as total_sales_all,
            (SELECT SUM(total) FROM pembelian) as total_purchases_all,
            (SELECT COUNT(*) FROM users) as total_users
        ");
        return $this->db->single();
    }
}

