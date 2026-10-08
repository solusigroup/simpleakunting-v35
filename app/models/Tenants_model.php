<?php

class Tenants_model {
    private $table = 'tenants';
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllTenants() {
        $this->db->query("SELECT t.*, kw.nama_kabupaten, kw.kode_kabupaten 
                          FROM " . $this->table . " t 
                          LEFT JOIN kluster_wilayah kw ON t.kluster_wilayah_id = kw.id 
                          ORDER BY t.created_at DESC");
        return $this->db->resultSet();
    }

    public function getTenantById($id) {
        $this->db->query("SELECT t.*, kw.nama_kabupaten 
                          FROM " . $this->table . " t 
                          LEFT JOIN kluster_wilayah kw ON t.kluster_wilayah_id = kw.id 
                          WHERE t.id = :id");
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    public function tambahTenant($data) {
        $kluster_id = !empty($data['kluster_wilayah_id']) ? $data['kluster_wilayah_id'] : null;
        $this->db->query("INSERT INTO " . $this->table . " (name, code, database_type, kluster_wilayah_id, status) VALUES (:name, :code, :type, :kluster, :status)");
        $this->db->bind('name', $data['name']);
        $this->db->bind('code', $data['code']);
        $this->db->bind('type', $data['database_type'] ?? 'single');
        $this->db->bind('kluster', $kluster_id);
        $this->db->bind('status', 'active');
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function ubahTenant($data) {
        $kluster_id = !empty($data['kluster_wilayah_id']) ? $data['kluster_wilayah_id'] : null;
        $query = "UPDATE " . $this->table . " SET 
                    name = :name, 
                    code = :code, 
                    database_type = :type, 
                    kluster_wilayah_id = :kluster,
                    status = :status 
                  WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('name', $data['name']);
        $this->db->bind('code', $data['code']);
        $this->db->bind('type', $data['database_type']);
        $this->db->bind('kluster', $kluster_id);
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

    /**
     * Ambil tenant berdasarkan kluster wilayah (untuk Penyelia Wilayah)
     */
    public function getTenantsByKluster($kluster_wilayah_id) {
        $this->db->query("SELECT t.*, kw.nama_kabupaten, kw.kode_kabupaten 
                          FROM " . $this->table . " t 
                          LEFT JOIN kluster_wilayah kw ON t.kluster_wilayah_id = kw.id 
                          WHERE t.kluster_wilayah_id = :kluster_id 
                          ORDER BY t.created_at DESC");
        $this->db->bind('kluster_id', $kluster_wilayah_id);
        return $this->db->resultSet();
    }

    public function getAllTenantsWithStats() {
        $this->db->query("SELECT t.*, kw.nama_kabupaten,
                            (SELECT COUNT(*) FROM users u WHERE u.tenant_id = t.id) as user_count,
                            (SELECT SUM(total) FROM penjualan p WHERE p.tenant_id = t.id) as total_sales,
                            (SELECT SUM(total) FROM pembelian pb WHERE pb.tenant_id = t.id) as total_purchases
                          FROM " . $this->table . " t 
                          LEFT JOIN kluster_wilayah kw ON t.kluster_wilayah_id = kw.id
                          ORDER BY total_sales DESC");
        return $this->db->resultSet();
    }
}
