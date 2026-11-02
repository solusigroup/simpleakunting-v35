<?php

class Pelanggan_model {
    private $table = 'pelanggan';
    private $db;
    private static $checked = false;

    public function __construct($db) {
        $this->db = $db;
        $this->ensureTableExists();
    }

    private function ensureTableExists() {
        if (self::$checked) return;
        self::$checked = true;

        try {
            $sql = "CREATE TABLE IF NOT EXISTS `pelanggan` (
              `id_pelanggan` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `tenant_id` int(11) DEFAULT 1,
              `nama_pelanggan` varchar(255) NOT NULL,
              `alamat` text DEFAULT NULL,
              `telepon` varchar(255) DEFAULT NULL,
              `email` varchar(255) DEFAULT NULL,
              `saldo_awal_piutang` decimal(15,2) NOT NULL DEFAULT 0.00,
              `saldo_terkini_piutang` decimal(15,2) NOT NULL DEFAULT 0.00,
              `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id_pelanggan`),
              KEY `tenant_id` (`tenant_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
            $this->db->query($sql);
            $this->db->execute();
        } catch (Throwable $e) {}
    }

    public function getAllPelanggan($tenant_id) {
        if (empty($tenant_id)) return [];
        try {
            $this->db->query('SELECT * FROM ' . $this->table . ' WHERE tenant_id = :tenant_id ORDER BY nama_pelanggan ASC');
            $this->db->bind('tenant_id', $tenant_id);
            $res = $this->db->resultSet();
            return is_array($res) ? $res : [];
        } catch (Throwable $e) {
            return [];
        }
    }

    public function getPelangganById($id, $tenant_id) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id_pelanggan=:id AND tenant_id = :tenant_id');
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->single();
    }

    public function tambahDataPelanggan($data, $tenant_id) {
        $query = "INSERT INTO {$this->table} (tenant_id, nama_pelanggan, alamat, telepon, email, saldo_awal_piutang, saldo_terkini_piutang) 
                  VALUES (:tenant_id, :nama, :alamat, :telepon, :email, :saldo_awal, :saldo_terkini)";
        $this->db->query($query);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->bind('nama', $data['nama_pelanggan']);
        $this->db->bind('alamat', $data['alamat']);
        $this->db->bind('telepon', $data['telepon']);
        $this->db->bind('email', $data['email']);
        $this->db->bind('saldo_awal', $data['saldo_awal_piutang']);
        $this->db->bind('saldo_terkini', $data['saldo_awal_piutang']); // Saldo terkini diinisialisasi sama dengan saldo awal
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function hapusDataPelanggan($id, $tenant_id) {
        $query = "DELETE FROM {$this->table} WHERE id_pelanggan = :id AND tenant_id = :tenant_id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function ubahDataPelanggan($data, $tenant_id) {
        $query = "UPDATE {$this->table} SET 
                    nama_pelanggan = :nama,
                    alamat = :alamat,
                    telepon = :telepon,
                    email = :email,
                    saldo_awal_piutang = :saldo_awal
                    -- Saldo terkini tidak diubah dari sini
                  WHERE id_pelanggan = :id AND tenant_id = :tenant_id";
        $this->db->query($query);
        $this->db->bind('nama', $data['nama_pelanggan']);
        $this->db->bind('alamat', $data['alamat']);
        $this->db->bind('telepon', $data['telepon']);
        $this->db->bind('email', $data['email']);
        $this->db->bind('saldo_awal', $data['saldo_awal_piutang']);
        $this->db->bind('id', $data['id_pelanggan']);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->execute();
        return $this->db->rowCount();
    }
}

