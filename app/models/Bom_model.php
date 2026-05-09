<?php

class Bom_model {
    private $table = 'bom';
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllBOM($tenant_id) {
        $this->db->query('SELECT b.*, p.nama_barang as nama_produk 
                          FROM ' . $this->table . ' b 
                          JOIN master_persediaan p ON b.id_barang_jadi = p.id_barang AND p.tenant_id = b.tenant_id
                          WHERE b.tenant_id = :tenant_id');
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->resultSet();
    }

    public function getBOMById($id, $tenant_id) {
        $this->db->query('SELECT b.*, p.nama_barang as nama_produk, p.kode_barang 
                          FROM ' . $this->table . ' b 
                          JOIN master_persediaan p ON b.id_barang_jadi = p.id_barang AND p.tenant_id = b.tenant_id
                          WHERE b.id = :id AND b.tenant_id = :tenant_id');
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $bom = $this->db->single();
        
        if ($bom) {
            $this->db->query('SELECT d.*, p.nama_barang, p.kode_barang, p.satuan as satuan_barang 
                              FROM bom_detail d 
                              JOIN master_persediaan p ON d.id_bahan_baku = p.id_barang AND p.tenant_id = :tenant_id
                              WHERE d.id_bom = :id_bom');
            $this->db->bind('id_bom', $id);
            $this->db->bind('tenant_id', $tenant_id);
            $bom['details'] = $this->db->resultSet();
        }
        
        return $bom;
    }

    public function tambahBOM($data, $tenant_id) {
        $this->db->beginTransaction();
        try {
            $query = "INSERT INTO bom (tenant_id, id_barang_jadi, nama_bom, total_biaya_estimasi) 
                      VALUES (:tenant_id, :id_barang_jadi, :nama_bom, :total_biaya)";
            $this->db->query($query);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('id_barang_jadi', $data['id_barang_jadi']);
            $this->db->bind('nama_bom', $data['nama_bom']);
            $this->db->bind('total_biaya', $data['total_biaya']);
            $this->db->execute();
            $id_bom = $this->db->lastInsertId();

            foreach ($data['items'] as $item) {
                if (empty($item['id_barang'])) continue;
                
                $queryDetail = "INSERT INTO bom_detail (id_bom, id_bahan_baku, jumlah, satuan, biaya_satuan) 
                                VALUES (:id_bom, :id_bahan_baku, :jumlah, :satuan, :biaya)";
                $this->db->query($queryDetail);
                $this->db->bind('id_bom', $id_bom);
                $this->db->bind('id_bahan_baku', $item['id_barang']);
                $this->db->bind('jumlah', $item['jumlah']);
                $this->db->bind('satuan', $item['satuan']);
                $this->db->bind('biaya', $item['biaya_satuan']);
                $this->db->execute();
            }

            $this->db->commit();
            return $id_bom;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            return false;
        }
    }

    public function hapusBOM($id, $tenant_id) {
        $this->db->beginTransaction();
        try {
            // Cek apakah BOM digunakan di Produksi
            $this->db->query("SELECT 1 FROM produksi WHERE id_bom = :id AND tenant_id = :tenant_id LIMIT 1");
            $this->db->bind('id', $id);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->execute();
            if ($this->db->rowCount() > 0) {
                throw new Exception("BOM tidak dapat dihapus karena sudah digunakan dalam transaksi produksi.");
            }

            $this->db->query("DELETE FROM bom_detail WHERE id_bom = :id");
            $this->db->bind('id', $id);
            $this->db->execute();

            $this->db->query("DELETE FROM bom WHERE id = :id AND tenant_id = :tenant_id");
            $this->db->bind('id', $id);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->execute();

            $this->db->commit();
            return $this->db->rowCount();
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            Flash::setFlash($e->getMessage(), 'danger');
            return 0;
        }
    }
}
