<?php

class Penawaran_model {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllPenawaran($tenant_id) {
        $this->db->query("SELECT p.*, pl.nama_pelanggan 
                         FROM penawaran p
                         LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan AND pl.tenant_id = p.tenant_id
                         WHERE p.tenant_id = :tenant_id
                         ORDER BY p.tanggal DESC");
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->resultSet();
    }

    public function getPenawaranById($id, $tenant_id) {
        $this->db->query("SELECT p.*, pl.nama_pelanggan, pl.alamat as alamat_pelanggan
                         FROM penawaran p
                         LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan AND pl.tenant_id = p.tenant_id
                         WHERE p.id_penawaran = :id AND p.tenant_id = :tenant_id");
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $header = $this->db->single();
        if (!$header) return false;

        $this->db->query("SELECT pd.*, ms.nama_barang, ms.kode_barang
                         FROM penawaran_detail pd
                         JOIN master_persediaan ms ON pd.id_barang = ms.id_barang AND ms.tenant_id = :tenant_id
                         WHERE pd.id_penawaran = :id");
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $header['details'] = $this->db->resultSet();
        return $header;
    }

    public function simpanPenawaran($data, $tenant_id) {
        $this->db->beginTransaction();
        try {
            $totalSubtotal = array_sum($data['details']['subtotal']);
            $totalDiskon = (float)($data['total_diskon'] ?? 0);
            $totalPajak = (float)($data['total_pajak'] ?? 0);
            $total = $totalSubtotal - $totalDiskon + $totalPajak;

            $this->db->query("INSERT INTO penawaran (tenant_id, id_pelanggan, no_penawaran, tanggal, tgl_kadaluarsa, total, pajak, diskon, keterangan, status) 
                             VALUES (:tenant_id, :id_pelanggan, :no_penawaran, :tanggal, :tgl_exp, :total, :pajak, :diskon, :keterangan, 'Draft')");
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('id_pelanggan', $data['id_pelanggan']);
            $this->db->bind('no_penawaran', $data['no_penawaran']);
            $this->db->bind('tanggal', $data['tanggal']);
            $this->db->bind('tgl_exp', $data['tgl_kadaluarsa'] ?: null);
            $this->db->bind('total', $total);
            $this->db->bind('pajak', $totalPajak);
            $this->db->bind('diskon', $totalDiskon);
            $this->db->bind('keterangan', $data['keterangan']);
            $this->db->execute();
            $id_penawaran = $this->db->lastInsertId();

            foreach ($data['details']['id_barang'] as $index => $id_barang) {
                $this->db->query("INSERT INTO penawaran_detail (id_penawaran, id_barang, kuantitas, harga, subtotal) 
                                 VALUES (:id_penawaran, :id_barang, :qty, :harga, :subtotal)");
                $this->db->bind('id_penawaran', $id_penawaran);
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
        $this->db->query("UPDATE penawaran SET status = :status WHERE id_penawaran = :id AND tenant_id = :tenant_id");
        $this->db->bind('status', $status);
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->execute();
    }
}
