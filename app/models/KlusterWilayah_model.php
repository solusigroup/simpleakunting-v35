<?php

class KlusterWilayah_model {
    private $table = 'kluster_wilayah';
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Mengambil semua data kluster wilayah diurutkan berdasarkan provinsi dan nama kabupaten
     */
    public function getAllKluster() {
        $this->db->query("SELECT * FROM " . $this->table . " ORDER BY provinsi ASC, nama_kabupaten ASC");
        return $this->db->resultSet();
    }

    /**
     * Mengambil data satu kluster wilayah berdasarkan ID
     */
    public function getKlusterById($id) {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE id = :id");
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    /**
     * Menambahkan data kluster wilayah baru
     */
    public function tambahKluster($data) {
        $provinsi = !empty($data['provinsi']) ? $data['provinsi'] : 'Jawa Timur';
        $status = !empty($data['status']) ? $data['status'] : 'active';

        $query = "INSERT INTO " . $this->table . " (nama_kabupaten, kode_kabupaten, provinsi, status) 
                  VALUES (:nama_kabupaten, :kode_kabupaten, :provinsi, :status)";
        $this->db->query($query);
        $this->db->bind('nama_kabupaten', $data['nama_kabupaten']);
        $this->db->bind('kode_kabupaten', $data['kode_kabupaten']);
        $this->db->bind('provinsi', $provinsi);
        $this->db->bind('status', $status);
        $this->db->execute();
        return $this->db->rowCount();
    }

    /**
     * Mengubah data kluster wilayah berdasarkan ID
     */
    public function ubahKluster($data) {
        $provinsi = !empty($data['provinsi']) ? $data['provinsi'] : 'Jawa Timur';
        $status = !empty($data['status']) ? $data['status'] : 'active';

        $query = "UPDATE " . $this->table . " SET 
                    nama_kabupaten = :nama_kabupaten, 
                    kode_kabupaten = :kode_kabupaten, 
                    provinsi = :provinsi, 
                    status = :status 
                  WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('nama_kabupaten', $data['nama_kabupaten']);
        $this->db->bind('kode_kabupaten', $data['kode_kabupaten']);
        $this->db->bind('provinsi', $provinsi);
        $this->db->bind('status', $status);
        $this->db->bind('id', $data['id']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    /**
     * Menghapus data kluster wilayah berdasarkan ID
     */
    public function hapusKluster($id) {
        $this->db->query("DELETE FROM " . $this->table . " WHERE id = :id");
        $this->db->bind('id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    /**
     * Mengambil semua kluster wilayah yang berstatus aktif diurutkan berdasarkan nama kabupaten
     */
    public function getActiveKluster() {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE status = 'active' ORDER BY nama_kabupaten ASC");
        return $this->db->resultSet();
    }
}
