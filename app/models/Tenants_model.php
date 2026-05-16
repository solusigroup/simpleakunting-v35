<?php

class Tenants_model {
    private $table = 'tenants';
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllTenants() {
        $this->db->query("SELECT * FROM " . $this->table . " ORDER BY created_at DESC");
        return $this->db->resultSet();
    }

    public function getTenantById($id) {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE id = :id");
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    public function tambahTenant($data) {
        $this->db->query("INSERT INTO " . $this->table . " (name, code, database_type, status) VALUES (:name, :code, :type, :status)");
        $this->db->bind('name', $data['name']);
        $this->db->bind('code', $data['code']);
        $this->db->bind('type', $data['database_type'] ?? 'single');
        $this->db->bind('status', 'active');
        $this->db->execute();
        return $this->db->lastInsertId();
    }
    public function ubahTenant($data) {
        $query = "UPDATE " . $this->table . " SET 
                    name = :name, 
                    code = :code, 
                    database_type = :type, 
                    status = :status 
                  WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('name', $data['name']);
        $this->db->bind('code', $data['code']);
        $this->db->bind('type', $data['database_type']);
        $this->db->bind('status', $data['status']);
        $this->db->bind('id', $data['id']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function hapusTenant($id) {
        $this->db->query("DELETE FROM " . $this->table . " WHERE id = :id");
        $this->db->bind('id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function getTenantByCode($code) {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE code = :code AND status = 'active'");
        $this->db->bind('code', $code);
        return $this->db->single();
    }

    public function getAllTenantsWithStats() {
        $this->db->query("SELECT t.*, 
                            (SELECT COUNT(*) FROM users u WHERE u.tenant_id = t.id) as user_count,
                            (SELECT SUM(total) FROM penjualan p WHERE p.tenant_id = t.id) as total_sales,
                            (SELECT SUM(total) FROM pembelian pb WHERE pb.tenant_id = t.id) as total_purchases
                          FROM " . $this->table . " t 
                          ORDER BY total_sales DESC");
        return $this->db->resultSet();
    }
}
