<?php

class Unit_model {
    private $table = 'business_units';
    private $db;
    private static $checked = false;

    public function __construct($db)
    {
        $this->db = $db;
        $this->ensureTableExists();
    }

    private function ensureTableExists()
    {
        if (self::$checked) return;
        self::$checked = true;

        try {
            $sql = "CREATE TABLE IF NOT EXISTS business_units (
                id_unit INT AUTO_INCREMENT PRIMARY KEY,
                tenant_id INT NOT NULL,
                nama_unit VARCHAR(255) NOT NULL,
                kode_unit VARCHAR(50) NOT NULL,
                deskripsi TEXT,
                is_default TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (tenant_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
            $this->db->query($sql);
            $this->db->execute();
        } catch (Throwable $e) {}
    }

    public function getAllUnits($tenant_id)
    {
        if (empty($tenant_id)) return [];
        try {
            $this->db->query("SELECT * FROM {$this->table} WHERE tenant_id = :tenant_id ORDER BY is_default DESC, nama_unit ASC");
            $this->db->bind('tenant_id', $tenant_id);
            $res = $this->db->resultSet();
            return is_array($res) ? $res : [];
        } catch (Throwable $e) {
            return [];
        }
    }

    public function getUnitById($id, $tenant_id)
    {
        if (empty($tenant_id)) return null;
        try {
            $this->db->query("SELECT * FROM {$this->table} WHERE id_unit = :id AND tenant_id = :tenant_id");
            $this->db->bind('id', $id);
            $this->db->bind('tenant_id', $tenant_id);
            return $this->db->single();
        } catch (Throwable $e) {
            return null;
        }
    }

    public function getDefaultUnit($tenant_id)
    {
        if (empty($tenant_id)) return null;
        try {
            $this->db->query("SELECT id_unit FROM {$this->table} WHERE tenant_id = :tenant_id AND is_default = 1");
            $this->db->bind('tenant_id', $tenant_id);
            $res = $this->db->single();
            return $res ? $res['id_unit'] : null;
        } catch (Throwable $e) {
            return null;
        }
    }

    public function tambahUnit($data, $tenant_id)
    {
        $query = "INSERT INTO {$this->table} (tenant_id, nama_unit, kode_unit, deskripsi, is_default) 
                  VALUES (:tenant_id, :nama_unit, :kode_unit, :deskripsi, :is_default)";
        
        $this->db->query($query);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->bind('nama_unit', $data['nama_unit']);
        $this->db->bind('kode_unit', $data['kode_unit']);
        $this->db->bind('deskripsi', $data['deskripsi'] ?? '');
        $this->db->bind('is_default', $data['is_default'] ?? 0);
        
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function ubahUnit($data, $tenant_id)
    {
        $query = "UPDATE {$this->table} SET 
                    nama_unit = :nama_unit,
                    kode_unit = :kode_unit,
                    deskripsi = :deskripsi,
                    is_default = :is_default
                  WHERE id_unit = :id AND tenant_id = :tenant_id";
        
        $this->db->query($query);
        $this->db->bind('nama_unit', $data['nama_unit']);
        $this->db->bind('kode_unit', $data['kode_unit']);
        $this->db->bind('deskripsi', $data['deskripsi']);
        $this->db->bind('is_default', $data['is_default']);
        $this->db->bind('id', $data['id_unit']);
        $this->db->bind('tenant_id', $tenant_id);
        
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function hapusUnit($id, $tenant_id)
    {
        // Prevent deleting default unit
        $unit = $this->getUnitById($id, $tenant_id);
        if ($unit && $unit['is_default'] == 1) return 0;

        $query = "DELETE FROM {$this->table} WHERE id_unit = :id AND tenant_id = :tenant_id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->execute();
        return $this->db->rowCount();
    }
}
