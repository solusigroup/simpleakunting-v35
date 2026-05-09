<?php

class Pelanggan_model {
    private $table = 'pelanggan';
    private $db;

    /**
     * Constructor baru yang menerima koneksi database dari Controller.
     */
    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllPelanggan($tenant_id) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE tenant_id = :tenant_id ORDER BY nama_pelanggan ASC');
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->resultSet();
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

