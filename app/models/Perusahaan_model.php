<?php

class Perusahaan_model {
    private $table = 'perusahaan';
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getPerusahaan($tenant_id) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE tenant_id = :tenant_id');
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->single();
    }

    public function updatePerusahaan($data, $logo_path, $tenant_id) {
        // Sanitize IDs (convert empty strings to null for DB compatibility)
        $p1_id = !empty($data['penandatangan_1_id']) ? $data['penandatangan_1_id'] : null;
        $p2_id = !empty($data['penandatangan_2_id']) ? $data['penandatangan_2_id'] : null;
        $akun_piutang = !empty($data['akun_piutang_default']) ? $data['akun_piutang_default'] : null;
        $akun_utang = !empty($data['akun_utang_default']) ? $data['akun_utang_default'] : null;
        $akun_laba_ditahan = !empty($data['akun_laba_ditahan']) ? $data['akun_laba_ditahan'] : null;
        $akun_ikhtisar_lr = !empty($data['akun_ikhtisar_lr']) ? $data['akun_ikhtisar_lr'] : null;
        $akun_akumulasi = !empty($data['akun_akumulasi_depresiasi_default']) ? $data['akun_akumulasi_depresiasi_default'] : null;
        $akun_beban = !empty($data['akun_beban_depresiasi_default']) ? $data['akun_beban_depresiasi_default'] : null;
        $akun_tk = !empty($data['akun_tenaga_kerja_langsung']) ? $data['akun_tenaga_kerja_langsung'] : null;
        $akun_oh = !empty($data['akun_overhead_pabrik']) ? $data['akun_overhead_pabrik'] : null;
        $akun_pajak_penjualan = !empty($data['akun_pajak_penjualan']) ? $data['akun_pajak_penjualan'] : null;
        $akun_pajak_pembelian = !empty($data['akun_pajak_pembelian']) ? $data['akun_pajak_pembelian'] : null;
        $pajak_persen = !empty($data['persentase_pajak_default']) ? $data['persentase_pajak_default'] : 0;

        // Check if record exists for this tenant
        $this->db->query("SELECT 1 FROM {$this->table} WHERE tenant_id = :tenant_id");
        $this->db->bind('tenant_id', $tenant_id);
        $exists = $this->db->single();

        if (!$exists) {
            // INSERT logic
            $query = "INSERT INTO {$this->table} 
                      (tenant_id, nama_perusahaan, jenis_usaha, alamat, telepon, email, kota_laporan, 
                       penandatangan_1_id, penandatangan_2_id, akun_piutang_default, akun_utang_default,
                       akun_laba_ditahan, akun_ikhtisar_lr, akun_akumulasi_depresiasi_default,
                       akun_beban_depresiasi_default, akun_tenaga_kerja_langsung, akun_overhead_pabrik,
                       akun_pajak_penjualan, akun_pajak_pembelian, persentase_pajak_default" . ($logo_path ? ", path_logo" : "") . ")
                      VALUES 
                      (:tenant_id, :nama, :jenis_usaha, :alamat, :telepon, :email, :kota_laporan,
                       :p1_id, :p2_id, :akun_piutang, :akun_utang, :akun_laba_ditahan, :akun_ikhtisar_lr,
                       :akun_akumulasi, :akun_beban, :akun_tk, :akun_oh, :akun_pajak_penjualan, :akun_pajak_pembelian, :pajak_persen" . ($logo_path ? ", :path_logo" : "") . ")";
        } else {
            // UPDATE logic
            $query = "UPDATE {$this->table} SET 
                        nama_perusahaan = :nama, jenis_usaha = :jenis_usaha, alamat = :alamat, telepon = :telepon, email = :email,
                        kota_laporan = :kota_laporan,
                        penandatangan_1_id = :p1_id, penandatangan_2_id = :p2_id,
                        akun_piutang_default = :akun_piutang, akun_utang_default = :akun_utang,
                        akun_laba_ditahan = :akun_laba_ditahan, akun_ikhtisar_lr = :akun_ikhtisar_lr,
                        akun_akumulasi_depresiasi_default = :akun_akumulasi,
                        akun_beban_depresiasi_default = :akun_beban,
                        akun_tenaga_kerja_langsung = :akun_tk,
                        akun_overhead_pabrik = :akun_oh,
                        akun_pajak_penjualan = :akun_pajak_penjualan,
                        akun_pajak_pembelian = :akun_pajak_pembelian,
                        persentase_pajak_default = :pajak_persen";
            
            if ($logo_path) {
                $query .= ", path_logo = :path_logo";
            }

            $query .= " WHERE tenant_id = :tenant_id";
        }
                  
        $this->db->query($query);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->bind('nama', $data['nama_perusahaan']);
        $this->db->bind('jenis_usaha', $data['jenis_usaha'] ?? 'dagang');
        $this->db->bind('alamat', $data['alamat']);
        $this->db->bind('telepon', $data['telepon']);
        $this->db->bind('email', $data['email']);
        $this->db->bind('kota_laporan', $data['kota_laporan']);
        $this->db->bind('p1_id', $p1_id);
        $this->db->bind('p2_id', $p2_id);
        $this->db->bind('akun_piutang', $akun_piutang);
        $this->db->bind('akun_utang', $akun_utang);
        $this->db->bind('akun_laba_ditahan', $akun_laba_ditahan);
        $this->db->bind('akun_ikhtisar_lr', $akun_ikhtisar_lr);
        $this->db->bind('akun_akumulasi', $akun_akumulasi);
        $this->db->bind('akun_beban', $akun_beban);
        $this->db->bind('akun_tk', $akun_tk);
        $this->db->bind('akun_oh', $akun_oh);
        $this->db->bind('akun_pajak_penjualan', $akun_pajak_penjualan);
        $this->db->bind('akun_pajak_pembelian', $akun_pajak_pembelian);
        $this->db->bind('pajak_persen', $pajak_persen);
        
        if ($logo_path) {
            $this->db->bind('path_logo', $logo_path);
        }
        
        $this->db->execute();
        return $this->db->rowCount() > 0 || !$exists;
    }
}