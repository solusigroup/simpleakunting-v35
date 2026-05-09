<?php

class Pemasok_model {
    private $table = 'pemasok';
    private $db;

    /**
     * Constructor baru yang menerima koneksi database dari Controller.
     */
    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllPemasok($tenant_id) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE tenant_id = :tenant_id ORDER BY nama_pemasok ASC');
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->resultSet();
    }

    public function getPemasokById($id, $tenant_id) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id_pemasok=:id AND tenant_id = :tenant_id');
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->single();
    }

    public function tambahDataPemasok($data, $tenant_id) {
        $query = "INSERT INTO {$this->table} (tenant_id, nama_pemasok, alamat, telepon, email, saldo_awal_hutang, saldo_terkini_hutang) 
                  VALUES (:tenant_id, :nama, :alamat, :telepon, :email, :saldo_awal, :saldo_terkini)";
        $this->db->query($query);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->bind('nama', $data['nama_pemasok']);
        $this->db->bind('alamat', $data['alamat']);
        $this->db->bind('telepon', $data['telepon']);
        $this->db->bind('email', $data['email']);
        $this->db->bind('saldo_awal', $data['saldo_awal_hutang']);
        $this->db->bind('saldo_terkini', $data['saldo_awal_hutang']); // Saldo terkini diinisialisasi sama dengan saldo awal
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function hapusDataPemasok($id, $tenant_id) {
        $query = "DELETE FROM {$this->table} WHERE id_pemasok = :id AND tenant_id = :tenant_id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function ubahDataPemasok($data, $tenant_id) {
        $query = "UPDATE {$this->table} SET 
                    nama_pemasok = :nama,
                    alamat = :alamat,
                    telepon = :telepon,
                    email = :email,
                    saldo_awal_hutang = :saldo_awal
                    -- Saldo terkini tidak diubah dari sini, melainkan melalui transaksi
                  WHERE id_pemasok = :id AND tenant_id = :tenant_id";
        $this->db->query($query);
        $this->db->bind('nama', $data['nama_pemasok']);
        $this->db->bind('alamat', $data['alamat']);
        $this->db->bind('telepon', $data['telepon']);
        $this->db->bind('email', $data['email']);
        $this->db->bind('saldo_awal', $data['saldo_awal_hutang']);
        $this->db->bind('id', $data['id_pemasok']);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->execute();
        return $this->db->rowCount();
    }
}

