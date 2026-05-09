<?php

class User_model {
    private $table = 'users';
    private $db;

    /**
     * Constructor baru yang menerima koneksi database dari Controller.
     */
    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllUsers($tenant_id = null) {
        $query = 'SELECT * FROM ' . $this->table;
        if ($tenant_id) {
            $query .= ' WHERE tenant_id = :tenant_id';
        }
        $query .= ' ORDER BY nama_user ASC';
        
        $this->db->query($query);
        if ($tenant_id) {
            $this->db->bind('tenant_id', $tenant_id);
        }
        return $this->db->resultSet();
    }

    public function getUserById($id, $tenant_id = null) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id_user=:id';
        if ($tenant_id) {
            $query .= ' AND tenant_id = :tenant_id';
        }
        $this->db->query($query);
        $this->db->bind('id', $id);
        if ($tenant_id) {
            $this->db->bind('tenant_id', $tenant_id);
        }
        return $this->db->single();
    }
    
    public function getUserByUsername($nama_user) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE nama_user = :nama_user');
        $this->db->bind('nama_user', $nama_user);
        return $this->db->single();
    }

    public function tambahDataUser($data, $tenant_id) {
        $role_id = !empty($data['role_id']) ? $data['role_id'] : null;
        $query = "INSERT INTO {$this->table} (tenant_id, nama_user, nama_lengkap, password_hash, role, role_id, jabatan) 
                  VALUES (:tenant_id, :nama, :nama_lengkap, :password, :role, :role_id, :jabatan)";
        $this->db->query($query);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->bind('nama', $data['nama_user']);
        $this->db->bind('nama_lengkap', $data['nama_lengkap']);
        $this->db->bind('password', password_hash($data['password'], PASSWORD_DEFAULT));
        $this->db->bind('role', $data['role'] ?? 'Staff');
        $this->db->bind('role_id', $role_id);
        $this->db->bind('jabatan', $data['jabatan']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function hapusDataUser($id, $tenant_id) {
        $query = "DELETE FROM {$this->table} WHERE id_user = :id AND tenant_id = :tenant_id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function ubahDataUser($data, $tenant_id) {
        $role_id = !empty($data['role_id']) ? $data['role_id'] : null;
        if (!empty($data['password'])) {
            $query = "UPDATE {$this->table} SET 
                        nama_user = :nama,
                        nama_lengkap = :nama_lengkap,
                        password_hash = :password,
                        role = :role,
                        role_id = :role_id,
                        jabatan = :jabatan
                      WHERE id_user = :id AND tenant_id = :tenant_id";
            $this->db->query($query);
            $this->db->bind('password', password_hash($data['password'], PASSWORD_DEFAULT));
        } else {
            $query = "UPDATE {$this->table} SET 
                        nama_user = :nama,
                        nama_lengkap = :nama_lengkap,
                        role = :role,
                        role_id = :role_id,
                        jabatan = :jabatan
                      WHERE id_user = :id AND tenant_id = :tenant_id";
            $this->db->query($query);
        }
        
        $this->db->bind('nama', $data['nama_user']);
        $this->db->bind('nama_lengkap', $data['nama_lengkap']);
        $this->db->bind('role', $data['role'] ?? 'Staff');
        $this->db->bind('role_id', $role_id);
        $this->db->bind('jabatan', $data['jabatan']);
        $this->db->bind('id', $data['id_user']);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->execute();
        return $this->db->rowCount();
    }
}
