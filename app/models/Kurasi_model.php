<?php

class Kurasi_model {
    private $table = 'kurasi_pengajuan';
    private $db;
    private static $checked = false;

    public function __construct($db) {
        $this->db = $db;
        $this->ensureTableExists();
    }

    private function ensureTableExists() {
        if (self::$checked) return;
        self::$checked = true;

        try {
            $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
              `id_pengajuan` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `tenant_id` int(11) DEFAULT NULL,
              `nama_usaha` varchar(255) NOT NULL,
              `nama_pemilik` varchar(255) NOT NULL,
              `telepon` varchar(50) NOT NULL,
              `kota` varchar(100) NOT NULL,
              `sektor` varchar(100) NOT NULL,
              `omset` decimal(15,2) NOT NULL DEFAULT 0.00,
              `laba_bersih` decimal(15,2) NOT NULL DEFAULT 0.00,
              `aset_usaha` decimal(15,2) NOT NULL DEFAULT 0.00,
              `lama_usaha` int(11) DEFAULT 1,
              `metode_pembukuan` varchar(50) DEFAULT 'manual',
              `deskripsi` text DEFAULT NULL,
              `skor_kelayakan` int(11) NOT NULL DEFAULT 60,
              `grade` varchar(10) NOT NULL DEFAULT 'B',
              `status` varchar(50) NOT NULL DEFAULT 'Menunggu Kurasi',
              `rekomendasi_pembiayaan` text DEFAULT NULL,
              `catatan_kurator` text DEFAULT NULL,
              `agree_nda` tinyint(1) NOT NULL DEFAULT 0,
              `agree_spk` tinyint(1) NOT NULL DEFAULT 0,
              `agree_pakta` tinyint(1) NOT NULL DEFAULT 0,
              `agree_hardcopy` tinyint(1) NOT NULL DEFAULT 0,
              `agree_share_profit` tinyint(1) NOT NULL DEFAULT 0,
              `alamat_hardcopy` varchar(255) DEFAULT 'Jln. Suromulang Barat VI/20 Mojokerto Jatim',
              `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id_pengajuan`),
              KEY `status` (`status`),
              KEY `grade` (`grade`),
              KEY `kota` (`kota`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
            $this->db->query($sql);
            $this->db->execute();
        } catch (Throwable $e) {}

        // Pastikan kolom persetujuan baru ada di tabel yang sudah dibuat sebelumnya
        $columnsToAdd = [
            'agree_nda' => "TINYINT(1) NOT NULL DEFAULT 0",
            'agree_spk' => "TINYINT(1) NOT NULL DEFAULT 0",
            'agree_pakta' => "TINYINT(1) NOT NULL DEFAULT 0",
            'agree_hardcopy' => "TINYINT(1) NOT NULL DEFAULT 0",
            'agree_share_profit' => "TINYINT(1) NOT NULL DEFAULT 0",
            'alamat_hardcopy' => "VARCHAR(255) DEFAULT 'Jln. Suromulang Barat VI/20 Mojokerto Jatim'"
        ];

        foreach ($columnsToAdd as $col => $type) {
            try {
                $this->db->query("SHOW COLUMNS FROM `{$this->table}` LIKE '{$col}'");
                if (!$this->db->single()) {
                    $this->db->query("ALTER TABLE `{$this->table}` ADD COLUMN `{$col}` {$type}");
                    $this->db->execute();
                }
            } catch (Throwable $e) {}
        }
    }

    public function simpanPengajuan($data, $tenant_id = null) {
        $nama_usaha = trim($data['nama_usaha'] ?? '');
        $nama_pemilik = trim($data['nama_pemilik'] ?? '');
        $telepon = trim($data['telepon'] ?? '');
        $kota = trim($data['kota'] ?? '');
        $sektor = trim($data['sektor'] ?? '');
        $omset = (float)($data['omset'] ?? 0);
        $laba = (float)($data['laba_bersih'] ?? ($omset * 0.2));
        $aset = (float)($data['aset_usaha'] ?? ($omset * 0.8));
        $lama_usaha = (int)($data['lama_usaha'] ?? 2);
        $metode = trim($data['metode_pembukuan'] ?? 'simpleakunting');
        $deskripsi = trim($data['deskripsi'] ?? '');

        // Persetujuan legalitas
        $agree_nda = !empty($data['agree_nda']) ? 1 : 0;
        $agree_spk = !empty($data['agree_spk']) ? 1 : 0;
        $agree_pakta = !empty($data['agree_pakta']) ? 1 : 0;
        $agree_hardcopy = !empty($data['agree_hardcopy']) ? 1 : 0;
        $agree_share_profit = !empty($data['agree_share_profit']) ? 1 : 0;
        $alamat_hardcopy = 'Jln. Suromulang Barat VI/20 Mojokerto Jatim';

        // Hitung estimasi skor awal
        $skor = 50;
        if ($omset >= 50000000) $skor += 20;
        elseif ($omset >= 20000000) $skor += 15;
        elseif ($omset >= 5000000) $skor += 10;

        if ($lama_usaha >= 3) $skor += 15;
        elseif ($lama_usaha >= 1) $skor += 10;

        if ($metode === 'simpleakunting') $skor += 15;
        else $skor += 8;

        // Bonus skor jika menyetujui seluruh pakta & komitmen
        if ($agree_nda && $agree_spk && $agree_pakta && $agree_share_profit) {
            $skor += 5;
        }

        $skor = min(100, max(40, $skor));
        $grade = ($skor >= 85) ? 'A' : (($skor >= 70) ? 'B+' : 'B');
        
        $rekomendasi = ($grade === 'A') 
            ? 'KUR Komersial Bank Jatim / BRI s/d Rp 250 Juta & Mitra CSR'
            : (($grade === 'B+') ? 'KUR Mikro s/d Rp 50 Juta & Inkubasi BUMDes' : 'KUR Super Mikro s/d Rp 10 Juta & Pelatihan SAK EP');

        try {
            $query = "INSERT INTO {$this->table} 
                        (tenant_id, nama_usaha, nama_pemilik, telepon, kota, sektor, omset, laba_bersih, aset_usaha, lama_usaha, metode_pembukuan, deskripsi, skor_kelayakan, grade, status, rekomendasi_pembiayaan, agree_nda, agree_spk, agree_pakta, agree_hardcopy, agree_share_profit, alamat_hardcopy)
                      VALUES 
                        (:tenant_id, :nama_usaha, :nama_pemilik, :telepon, :kota, :sektor, :omset, :laba, :aset, :lama, :metode, :deskripsi, :skor, :grade, 'Menunggu Kurasi', :rekomendasi, :agree_nda, :agree_spk, :agree_pakta, :agree_hardcopy, :agree_share_profit, :alamat_hardcopy)";
            $this->db->query($query);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('nama_usaha', $nama_usaha);
            $this->db->bind('nama_pemilik', $nama_pemilik);
            $this->db->bind('telepon', $telepon);
            $this->db->bind('kota', $kota);
            $this->db->bind('sektor', $sektor);
            $this->db->bind('omset', $omset);
            $this->db->bind('laba', $laba);
            $this->db->bind('aset', $aset);
            $this->db->bind('lama', $lama_usaha);
            $this->db->bind('metode', $metode);
            $this->db->bind('deskripsi', $deskripsi);
            $this->db->bind('skor', $skor);
            $this->db->bind('grade', $grade);
            $this->db->bind('rekomendasi', $rekomendasi);
            $this->db->bind('agree_nda', $agree_nda);
            $this->db->bind('agree_spk', $agree_spk);
            $this->db->bind('agree_pakta', $agree_pakta);
            $this->db->bind('agree_hardcopy', $agree_hardcopy);
            $this->db->bind('agree_share_profit', $agree_share_profit);
            $this->db->bind('alamat_hardcopy', $alamat_hardcopy);
            $this->db->execute();
            return $this->db->lastInsertId();
        } catch (Throwable $e) {
            return false;
        }
    }

    public function getAllPengajuan($status = null) {
        try {
            $query = "SELECT * FROM {$this->table}";
            if (!empty($status) && $status !== 'Semua') {
                $query .= " WHERE status = :status";
            }
            $query .= " ORDER BY id_pengajuan DESC";
            $this->db->query($query);
            if (!empty($status) && $status !== 'Semua') {
                $this->db->bind('status', $status);
            }
            $res = $this->db->resultSet();
            return is_array($res) ? $res : [];
        } catch (Throwable $e) {
            return [];
        }
    }

    public function getPengajuanById($id) {
        try {
            $this->db->query("SELECT * FROM {$this->table} WHERE id_pengajuan = :id");
            $this->db->bind('id', $id);
            return $this->db->single();
        } catch (Throwable $e) {
            return null;
        }
    }

    public function updateStatusKurasi($id, $status, $grade, $skor, $rekomendasi, $catatan) {
        try {
            $query = "UPDATE {$this->table} SET 
                        status = :status, 
                        grade = :grade, 
                        skor_kelayakan = :skor, 
                        rekomendasi_pembiayaan = :rekomendasi, 
                        catatan_kurator = :catatan 
                      WHERE id_pengajuan = :id";
            $this->db->query($query);
            $this->db->bind('status', $status);
            $this->db->bind('grade', $grade);
            $this->db->bind('skor', $skor);
            $this->db->bind('rekomendasi', $rekomendasi);
            $this->db->bind('catatan', $catatan);
            $this->db->bind('id', $id);
            return $this->db->execute();
        } catch (Throwable $e) {
            return false;
        }
    }

    public function hapusPengajuan($id) {
        try {
            $this->db->query("DELETE FROM {$this->table} WHERE id_pengajuan = :id");
            $this->db->bind('id', $id);
            return $this->db->execute();
        } catch (Throwable $e) {
            return false;
        }
    }

    public function countPending() {
        try {
            $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE status = 'Menunggu Kurasi'");
            $res = $this->db->single();
            return (int)($res['total'] ?? 0);
        } catch (Throwable $e) {
            return 0;
        }
    }

    public function getSummaryStats() {
        try {
            $this->db->query("SELECT 
                                COUNT(*) as total,
                                SUM(CASE WHEN status = 'Disetujui' OR status = 'Bankable' THEN 1 ELSE 0 END) as total_approved,
                                SUM(CASE WHEN status = 'Menunggu Kurasi' THEN 1 ELSE 0 END) as total_pending,
                                SUM(CASE WHEN grade = 'A' THEN 1 ELSE 0 END) as total_grade_a
                              FROM {$this->table}");
            $res = $this->db->single();
            return $res ?: ['total' => 0, 'total_approved' => 0, 'total_pending' => 0, 'total_grade_a' => 0];
        } catch (Throwable $e) {
            return ['total' => 0, 'total_approved' => 0, 'total_pending' => 0, 'total_grade_a' => 0];
        }
    }
}
