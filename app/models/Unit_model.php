<?php

class Unit_model {
    private $table = 'business_units';
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAllUnits($tenant_id)
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE tenant_id = :tenant_id ORDER BY is_default DESC, nama_unit ASC");
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->resultSet();
    }

    public function getUnitById($id, $tenant_id)
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE id_unit = :id AND tenant_id = :tenant_id");
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->single();
    }

    public function getDefaultUnit($tenant_id)
    {
        $this->db->query("SELECT id_unit FROM {$this->table} WHERE tenant_id = :tenant_id AND is_default = 1");
        $this->db->bind('tenant_id', $tenant_id);
        $res = $this->db->single();
        return $res ? $res['id_unit'] : null;
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
