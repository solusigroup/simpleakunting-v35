<?php
/**
 * POS Model for SimpleAkunting v3.5
 * Handles database operations for Point of Sales (POS)
 */
require_once APPROOT . '/app/models/Penjualan_model.php';

class Pos_model {
    private $db;
    private static $checked = false;

    public function __construct($db) {
        $this->db = $db;
        $this->ensureTableExists();
    }

    /**
     * Memastikan tabel pos_transactions dan permission terkait ada di database secara otomatis
     */
    private function ensureTableExists() {
        if (self::$checked) return;
        self::$checked = true;

        try {
            $sqlTable = "CREATE TABLE IF NOT EXISTS `pos_transactions` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `tenant_id` int(11) NOT NULL,
              `id_penjualan` bigint(20) unsigned NOT NULL COMMENT 'FK ke tabel penjualan',
              `no_receipt` varchar(50) NOT NULL,
              `kasir_id` bigint(20) unsigned NOT NULL,
              `kasir_name` varchar(255) DEFAULT NULL,
              `total` decimal(15,2) NOT NULL,
              `bayar` decimal(15,2) NOT NULL DEFAULT 0.00,
              `kembalian` decimal(15,2) NOT NULL DEFAULT 0.00,
              `metode_pembayaran` varchar(50) DEFAULT 'Tunai',
              `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `tenant_id` (`tenant_id`),
              KEY `id_penjualan` (`id_penjualan`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
            $this->db->query($sqlTable);
            $this->db->execute();
        } catch (Throwable $e) {}

        // Pastikan permissions untuk POS terdaftar
        try {
            $permissions = [
                ['trx_pos', 'Point of Sales (Kasir)', 'Operasional'],
                ['master_pelanggan', 'Manage Pelanggan', 'Master Data'],
                ['master_pemasok', 'Manage Pemasok', 'Master Data']
            ];
            foreach ($permissions as $p) {
                try {
                    $this->db->query("SELECT id FROM permissions WHERE permission_key = :key");
                    $this->db->bind('key', $p[0]);
                    $row = $this->db->single();
                    if (!$row) {
                        $this->db->query("INSERT INTO permissions (permission_key, display_name, category) VALUES (:key, :name, :cat)");
                        $this->db->bind('key', $p[0]);
                        $this->db->bind('name', $p[1]);
                        $this->db->bind('cat', $p[2]);
                        $this->db->execute();
                    }
                } catch (Throwable $eP) {}
            }

            // Berikan izin trx_pos ke role bawaan (1=Superadmin, 2=Admin, 3=Manager, 4=Staff)
            try {
                $this->db->query("SELECT id FROM permissions WHERE permission_key = 'trx_pos'");
                $pRow = $this->db->single();
                if ($pRow && !empty($pRow['id'])) {
                    $pId = $pRow['id'];
                    foreach ([1, 2, 3, 4] as $roleId) {
                        $this->db->query("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (:rid, :pid)");
                        $this->db->bind('rid', $roleId);
                        $this->db->bind('pid', $pId);
                        $this->db->execute();
                    }
                }
            } catch (Throwable $eRp) {}
        } catch (Throwable $e) {}
    }

    // Search active products by keyword (name, code, barcode)
    public function cariBarang($keyword, $tenant_id) {
        if (!$tenant_id) return [];
        try {
            $kw = '%' . $keyword . '%';
            $this->db->query("SELECT id_barang, kode_barang, barcode, nama_barang, satuan, stok_saat_ini, harga_jual, harga_beli
                              FROM master_persediaan 
                              WHERE tenant_id = :tenant_id 
                                AND (nama_barang LIKE :kw OR kode_barang LIKE :kw OR barcode LIKE :kw)
                                AND stok_saat_ini > 0
                              LIMIT 25");
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('kw', $kw);
            return $this->db->resultSet() ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }

    // Get all active inventory products for the tenant
    public function getAllBarangAktif($tenant_id) {
        if (!$tenant_id) return [];
        try {
            $this->db->query("SELECT id_barang, kode_barang, barcode, nama_barang, satuan, stok_saat_ini, harga_jual, harga_beli
                              FROM master_persediaan 
                              WHERE tenant_id = :tenant_id 
                              ORDER BY nama_barang ASC");
            $this->db->bind('tenant_id', $tenant_id);
            return $this->db->resultSet() ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }

    // Generate unique receipt number: POS/YYYYMMDD/001
    public function generateReceiptNumber($tenant_id) {
        $date = date('Ymd');
        $prefix = "POS/" . $date . "/";
        $pattern = $prefix . "%";

        try {
            $this->db->query("SELECT MAX(no_receipt) as max_receipt 
                              FROM pos_transactions 
                              WHERE tenant_id = :tenant_id AND no_receipt LIKE :pattern");
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('pattern', $pattern);
            $row = $this->db->single();

            if ($row && !empty($row['max_receipt'])) {
                $lastNum = (int)substr($row['max_receipt'], strlen($prefix));
                $nextNum = $lastNum + 1;
            } else {
                $nextNum = 1;
            }
        } catch (Throwable $e) {
            $nextNum = 1;
        }

        return $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }

    // Save POS specific transaction data
    public function simpanTransaksiPos($data, $tenant_id) {
        try {
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
        } catch (Throwable $e) {
            return false;
        }
    }

    // Get today's POS transactions
    public function getTransaksiHariIni($tenant_id) {
        if (!$tenant_id) return [];
        try {
            $this->db->query("SELECT pt.*, p.no_faktur, p.tanggal_faktur 
                              FROM pos_transactions pt
                              JOIN penjualan p ON pt.id_penjualan = p.id_penjualan AND p.tenant_id = pt.tenant_id
                              WHERE pt.tenant_id = :tenant_id AND DATE(pt.created_at) = CURDATE()
                              ORDER BY pt.created_at DESC");
            $this->db->bind('tenant_id', $tenant_id);
            return $this->db->resultSet() ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }

    // Get POS transactions by date range
    public function getTransaksiByPeriode($tenant_id, $dari = null, $sampai = null) {
        if (!$tenant_id) return [];
        if (empty($dari)) $dari = date('Y-m-d');
        if (empty($sampai)) $sampai = date('Y-m-d');

        try {
            $this->db->query("SELECT pt.*, p.no_faktur, p.tanggal_faktur 
                              FROM pos_transactions pt
                              JOIN penjualan p ON pt.id_penjualan = p.id_penjualan AND p.tenant_id = pt.tenant_id
                              WHERE pt.tenant_id = :tenant_id 
                                AND DATE(pt.created_at) BETWEEN :dari AND :sampai
                              ORDER BY pt.created_at DESC");
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('dari', $dari);
            $this->db->bind('sampai', $sampai);
            return $this->db->resultSet() ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }

    // Get single transaction details
    public function getTransaksiById($id, $tenant_id) {
        if (!$tenant_id || !$id) return false;
        try {
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
            $tx['details'] = $this->db->resultSet() ?: [];

            return $tx;
        } catch (Throwable $e) {
            return false;
        }
    }

    // Get default Walk-in Customer (auto create if not present for tenant)
    public function getWalkInCustomer($tenant_id) {
        if (!$tenant_id) return null;
        try {
            $this->db->query("SELECT * FROM pelanggan WHERE tenant_id = :tenant_id AND nama_pelanggan = 'Walk-in Customer' LIMIT 1");
            $this->db->bind('tenant_id', $tenant_id);
            $walkIn = $this->db->single();

            if (!$walkIn) {
                // Auto create walk-in customer for this tenant
                $this->db->query("INSERT INTO pelanggan (tenant_id, nama_pelanggan, alamat, telepon, email, saldo_awal_piutang, saldo_terkini_piutang) 
                                  VALUES (:tenant_id, 'Walk-in Customer', 'Pelanggan Langsung (Kasir)', '-', '', 0, 0)");
                $this->db->bind('tenant_id', $tenant_id);
                $this->db->execute();

                $this->db->query("SELECT * FROM pelanggan WHERE tenant_id = :tenant_id AND nama_pelanggan = 'Walk-in Customer' LIMIT 1");
                $this->db->bind('tenant_id', $tenant_id);
                $walkIn = $this->db->single();
            }
            return $walkIn ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    // Get statistics for today
    public function getStatistikHariIni($tenant_id) {
        if (!$tenant_id) return ['jumlah_transaksi' => 0, 'total_penjualan' => 0];
        try {
            $this->db->query("SELECT COUNT(id) as jumlah_transaksi, COALESCE(SUM(total), 0) as total_penjualan 
                              FROM pos_transactions 
                              WHERE tenant_id = :tenant_id AND DATE(created_at) = CURDATE()");
            $this->db->bind('tenant_id', $tenant_id);
            return $this->db->single() ?: ['jumlah_transaksi' => 0, 'total_penjualan' => 0];
        } catch (Throwable $e) {
            return ['jumlah_transaksi' => 0, 'total_penjualan' => 0];
        }
    }
}
