<?php

class RFQ_model {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllRFQ($tenant_id) {
        $this->db->query("SELECT r.*, p.nama_pemasok 
                         FROM rfq r
                         LEFT JOIN pemasok p ON r.id_pemasok = p.id_pemasok AND p.tenant_id = r.tenant_id
                         WHERE r.tenant_id = :tenant_id
                         ORDER BY r.tanggal DESC");
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->resultSet();
    }

    public function getRFQById($id, $tenant_id) {
        $this->db->query("SELECT r.*, p.nama_pemasok, p.alamat as alamat_pemasok
                         FROM rfq r
                         LEFT JOIN pemasok p ON r.id_pemasok = p.id_pemasok AND p.tenant_id = r.tenant_id
                         WHERE r.id_rfq = :id AND r.tenant_id = :tenant_id");
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $header = $this->db->single();
        if (!$header) return false;

        $this->db->query("SELECT rd.*, ms.nama_barang, ms.kode_barang
                         FROM rfq_detail rd
                         JOIN master_persediaan ms ON rd.id_barang = ms.id_barang AND ms.tenant_id = :tenant_id
                         WHERE rd.id_rfq = :id");
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $header['details'] = $this->db->resultSet();
        return $header;
    }

    public function simpanRFQ($data, $tenant_id) {
        $this->db->beginTransaction();
        try {
            $totalEstimasi = array_sum($data['details']['subtotal']);

            $this->db->query("INSERT INTO rfq (tenant_id, id_pemasok, no_rfq, tanggal, tgl_kadaluarsa, total_estimasi, keterangan, status) 
                             VALUES (:tenant_id, :id_pemasok, :no_rfq, :tanggal, :tgl_exp, :total, :keterangan, 'Draft')");
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('id_pemasok', $data['id_pemasok']);
            $this->db->bind('no_rfq', $data['no_rfq']);
            $this->db->bind('tanggal', $data['tanggal']);
            $this->db->bind('tgl_exp', $data['tgl_kadaluarsa'] ?: null);
            $this->db->bind('total', $totalEstimasi);
            $this->db->bind('keterangan', $data['keterangan']);
            $this->db->execute();
            $id_rfq = $this->db->lastInsertId();

            foreach ($data['details']['id_barang'] as $index => $id_barang) {
                $this->db->query("INSERT INTO rfq_detail (id_rfq, id_barang, kuantitas, harga_estimasi, subtotal_estimasi) 
                                 VALUES (:id_rfq, :id_barang, :qty, :harga, :subtotal)");
                $this->db->bind('id_rfq', $id_rfq);
                $this->db->bind('id_barang', $id_barang);
                $this->db->bind('qty', $data['details']['kuantitas'][$index]);
                $this->db->bind('harga', $data['details']['harga'][$index]);
                $this->db->bind('subtotal', $data['details']['subtotal'][$index]);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateStatus($id, $status, $tenant_id) {
        $this->db->query("UPDATE rfq SET status = :status WHERE id_rfq = :id AND tenant_id = :tenant_id");
        $this->db->bind('status', $status);
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->execute();
    }
}
