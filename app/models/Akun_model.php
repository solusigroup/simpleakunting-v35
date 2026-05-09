<?php

class Akun_model {
    private $table = 'akun';
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAllAkun($tenant_id)
    {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE tenant_id = :tenant_id ORDER BY kode_akun ASC');
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->resultSet();
    }

    public function tambahDataAkun($data, $tenant_id)
    {
        // Keamanan: Saldo awal untuk Header selalu 0
        $saldo_awal = ($data['tipe_akun'] == 'Header') ? 0.00 : ($data['saldo_awal'] ?? 0.00);

        $query = "INSERT INTO {$this->table} (tenant_id, kode_akun, nama_akun, tipe_akun, saldo_normal, posisi_saldo_normal, saldo_awal) 
                  VALUES (:tenant_id, :kode_akun, :nama_akun, :tipe_akun, :saldo_normal, :posisi_saldo_normal, :saldo_awal)";
        
        $this->db->query($query);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->bind('kode_akun', $data['kode_akun']);
        $this->db->bind('nama_akun', $data['nama_akun']);
        $this->db->bind('tipe_akun', $data['tipe_akun']);
        $this->db->bind('saldo_normal', $data['posisi_saldo_normal']); // Use the same value for compatibility
        $this->db->bind('posisi_saldo_normal', $data['posisi_saldo_normal']);
        $this->db->bind('saldo_awal', $saldo_awal);
        
        $this->db->execute();
        return $this->db->rowCount();
    }

    /**
     * FUNGSI YANG DIPERBARUI dengan logika keamanan untuk Saldo Awal.
     */
    public function ubahDataAkun($data, $tenant_id)
    {
        // **LOGIKA BARU: Pastikan saldo awal untuk Header selalu 0 di sisi server**
        $saldo_awal = ($data['tipe_akun'] == 'Header') ? 0.00 : ($data['saldo_awal'] ?? 0.00);

        $query = "UPDATE {$this->table} SET 
                    nama_akun = :nama_akun,
                    tipe_akun = :tipe_akun,
                    saldo_normal = :saldo_normal,
                    posisi_saldo_normal = :posisi_saldo_normal,
                    saldo_awal = :saldo_awal
                  WHERE kode_akun = :kode_akun AND tenant_id = :tenant_id";
        
        $this->db->query($query);
        $this->db->bind('nama_akun', $data['nama_akun']);
        $this->db->bind('tipe_akun', $data['tipe_akun']);
        $this->db->bind('saldo_normal', $data['posisi_saldo_normal']);
        $this->db->bind('posisi_saldo_normal', $data['posisi_saldo_normal']);
        $this->db->bind('saldo_awal', $saldo_awal); 
        $this->db->bind('kode_akun', $data['kode_akun']);
        $this->db->bind('tenant_id', $tenant_id);
        
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function importFromExcel($data, $tenant_id)
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS=0; DELETE FROM ' . $this->table . ' WHERE tenant_id = :tenant_id; SET FOREIGN_KEY_CHECKS=1;');
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->execute();

        $rowCount = 0;
        foreach ($data as $akun) {
            if (!empty($akun['kode_akun'])) {
                $this->tambahDataAkun($akun, $tenant_id);
                $rowCount++;
            }
        }
        return $rowCount;
    }

    public function generateFromCentral($tenant_id)
    {
        // 1. Bersihkan akun lama (Opsional, tapi biasanya untuk setup awal)
        $this->db->query('SET FOREIGN_KEY_CHECKS=0; DELETE FROM ' . $this->table . ' WHERE tenant_id = :tenant_id; SET FOREIGN_KEY_CHECKS=1;');
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->execute();

        // 2. Ambil dari central_akun
        $this->db->query("SELECT * FROM central_akun ORDER BY kode_akun ASC");
        $central = $this->db->resultSet();

        // 3. Masukkan ke tabel akun tenant
        $count = 0;
        foreach ($central as $acc) {
            $data = [
                'kode_akun' => $acc['kode_akun'],
                'nama_akun' => $acc['nama_akun'],
                'tipe_akun' => $acc['tipe_akun'],
                'posisi_saldo_normal' => $acc['posisi_saldo_normal'],
                'saldo_awal' => 0
            ];
            $this->tambahDataAkun($data, $tenant_id);
            $count++;
        }
        return $count;
    }

    public function hapusDataAkun($kode, $tenant_id)
    {
        $query = "DELETE FROM {$this->table} WHERE kode_akun = :kode_akun AND tenant_id = :tenant_id";
        $this->db->query($query);
        $this->db->bind('kode_akun', $kode);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function getAkunByKode($kode, $tenant_id)
    {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE kode_akun=:kode_akun AND tenant_id = :tenant_id');
        $this->db->bind('kode_akun', $kode);
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->single();
    }
    
    public function isKodeAkunExists($kode, $tenant_id)
    {
        $this->db->query('SELECT 1 FROM ' . $this->table . ' WHERE kode_akun = :kode_akun AND tenant_id = :tenant_id');
        $this->db->bind('kode_akun', $kode);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    public function getAkunKasBank($tenant_id)
    {
        $this->db->query("SELECT * FROM " . $this->table . " 
                          WHERE (kode_akun LIKE '1-11%' OR nama_akun LIKE '%Kas%' OR nama_akun LIKE '%Bank%') 
                          AND tipe_akun != 'Header' 
                          AND tenant_id = :tenant_id 
                          ORDER BY kode_akun ASC");
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->resultSet();
    }
}