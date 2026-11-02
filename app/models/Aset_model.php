<?php

class Aset_model {
    private $table = 'aset_tetap';
    private $db;
    private static $schemaChecked = false;

    public function __construct($db) {
        $this->db = $db;
        $this->ensureTableSchema();
    }

    /**
     * Memastikan struktur tabel aset_tetap dan penyusutan_aset lengkap dan mutakhir (Self-healing).
     */
    public function ensureTableSchema() {
        if (self::$schemaChecked) {
            return;
        }

        try {
            // 1. Pastikan tabel aset_tetap ada
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `tenant_id` int(11) NOT NULL,
                `kode_aset` varchar(50) NOT NULL,
                `nama_aset` varchar(255) NOT NULL,
                `tanggal_perolehan` date NOT NULL,
                `harga_perolehan` decimal(15,2) NOT NULL DEFAULT 0.00,
                `nilai_residu` decimal(15,2) DEFAULT 0.00,
                `umur_ekonomis` int(11) NOT NULL DEFAULT 1 COMMENT 'dalam bulan',
                `metode_penyusutan` varchar(50) DEFAULT 'garis_lurus',
                `akun_aset` varchar(20) DEFAULT NULL,
                `akun_akumulasi` varchar(20) DEFAULT NULL,
                `akun_beban` varchar(20) DEFAULT NULL,
                `status` varchar(20) DEFAULT 'Aktif',
                `keterangan` text DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_tenant_aset` (`tenant_id`, `kode_aset`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
            $this->db->execute();

            // 2. Periksa kolom-kolom yang berpotensi belum ada pada tabel lama
            $this->db->query("SHOW COLUMNS FROM `{$this->table}`");
            $existingCols = $this->db->resultSet();
            $fieldMap = [];
            foreach ($existingCols as $col) {
                $fieldMap[$col['Field']] = $col;
            }

            if (!isset($fieldMap['status'])) {
                $this->db->query("ALTER TABLE `{$this->table}` ADD COLUMN `status` VARCHAR(20) DEFAULT 'Aktif' AFTER `akun_beban`");
                $this->db->execute();
            }

            if (!isset($fieldMap['keterangan'])) {
                $this->db->query("ALTER TABLE `{$this->table}` ADD COLUMN `keterangan` TEXT DEFAULT NULL AFTER `status`");
                $this->db->execute();
            }

            if (!isset($fieldMap['updated_at'])) {
                $this->db->query("ALTER TABLE `{$this->table}` ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`");
                $this->db->execute();
            }

            // Jika metode_penyusutan masih ENUM strict, ubah ke VARCHAR(50) agar menerima variasi format
            if (isset($fieldMap['metode_penyusutan']) && stripos($fieldMap['metode_penyusutan']['Type'], 'enum') !== false) {
                $this->db->query("ALTER TABLE `{$this->table}` MODIFY COLUMN `metode_penyusutan` VARCHAR(50) DEFAULT 'garis_lurus'");
                $this->db->execute();
            }

            // 3. Pastikan tabel penyusutan_aset ada
            $this->db->query("CREATE TABLE IF NOT EXISTS `penyusutan_aset` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `tenant_id` int(11) NOT NULL,
                `id_aset` bigint(20) unsigned NOT NULL,
                `id_jurnal` int(11) DEFAULT NULL,
                `bulan` tinyint(4) NOT NULL,
                `tahun` smallint(6) NOT NULL,
                `jumlah` decimal(15,2) NOT NULL DEFAULT 0.00,
                `tanggal_proses` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_penyusutan_aset` (`tenant_id`, `id_aset`, `bulan`, `tahun`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
            $this->db->execute();

            self::$schemaChecked = true;
        } catch (Throwable $e) {
            error_log("Aset_model::ensureTableSchema error: " . $e->getMessage());
        }
    }

    public function getAllAset($tenant_id) {
        $this->ensureTableSchema();
        $this->db->query("SELECT * FROM {$this->table} WHERE tenant_id = :tenant_id ORDER BY created_at DESC");
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->resultSet();
    }

    public function getAsetById($id, $tenant_id) {
        $this->ensureTableSchema();
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND tenant_id = :tenant_id");
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->single();
    }

    private function parseCurrency($value) {
        if (is_numeric($value)) {
            return (float)$value;
        }
        $val = str_replace(' ', '', str_replace('Rp', '', (string)$value));
        if (strpos($val, '.') !== false && strpos($val, ',') !== false) {
            $val = str_replace('.', '', $val);
            $val = str_replace(',', '.', $val);
        } else if (strpos($val, ',') !== false) {
            $val = str_replace(',', '.', $val);
        }
        return (float)$val;
    }

    public function simpanAset($data, $tenant_id) {
        $this->ensureTableSchema();

        $kode = trim($data['kode_aset'] ?? '');
        $nama = trim($data['nama_aset'] ?? '');
        $tanggal = !empty($data['tanggal_perolehan']) ? $data['tanggal_perolehan'] : date('Y-m-d');
        
        $harga = $this->parseCurrency($data['harga_perolehan'] ?? 0);
        $residu = $this->parseCurrency($data['nilai_residu'] ?? 0);
        $umur = !empty($data['umur_ekonomis']) ? (int)$data['umur_ekonomis'] : 1;

        $metodeRaw = trim($data['metode_penyusutan'] ?? 'garis_lurus');
        $metode = (stripos($metodeRaw, 'saldo') !== false) ? 'saldo_menurun' : 'garis_lurus';

        $akun_aset = !empty($data['akun_aset']) ? trim($data['akun_aset']) : null;
        $akun_akumulasi = !empty($data['akun_akumulasi']) ? trim($data['akun_akumulasi']) : null;
        $akun_beban = !empty($data['akun_beban']) ? trim($data['akun_beban']) : null;
        $keterangan = trim($data['keterangan'] ?? '');
        $status = !empty($data['status']) ? trim($data['status']) : 'Aktif';

        $query = "INSERT INTO {$this->table} (
                    tenant_id, kode_aset, nama_aset, tanggal_perolehan, harga_perolehan, 
                    nilai_residu, umur_ekonomis, metode_penyusutan, akun_aset, 
                    akun_akumulasi, akun_beban, status, keterangan
                  ) VALUES (
                    :tenant_id, :kode, :nama, :tanggal, :harga, 
                    :residu, :umur, :metode, :akun_aset, 
                    :akun_akumulasi, :akun_beban, :status, :keterangan
                  )";

        $this->db->query($query);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->bind('kode', $kode);
        $this->db->bind('nama', $nama);
        $this->db->bind('tanggal', $tanggal);
        $this->db->bind('harga', $harga);
        $this->db->bind('residu', $residu);
        $this->db->bind('umur', $umur);
        $this->db->bind('metode', $metode);
        $this->db->bind('akun_aset', $akun_aset);
        $this->db->bind('akun_akumulasi', $akun_akumulasi);
        $this->db->bind('akun_beban', $akun_beban);
        $this->db->bind('status', $status);
        $this->db->bind('keterangan', $keterangan);
        $this->db->execute();

        return $this->db->rowCount();
    }

    public function updateAset($data, $tenant_id) {
        $this->ensureTableSchema();

        $id = $data['id'] ?? $data['id_aset'] ?? 0;
        $kode = trim($data['kode_aset'] ?? '');
        $nama = trim($data['nama_aset'] ?? '');
        $tanggal = !empty($data['tanggal_perolehan']) ? $data['tanggal_perolehan'] : date('Y-m-d');
        
        $harga = $this->parseCurrency($data['harga_perolehan'] ?? 0);
        $residu = $this->parseCurrency($data['nilai_residu'] ?? 0);
        $umur = !empty($data['umur_ekonomis']) ? (int)$data['umur_ekonomis'] : (!empty($data['masa_manfaat']) ? (int)$data['masa_manfaat'] : 1);

        $metodeRaw = trim($data['metode_penyusutan'] ?? 'garis_lurus');
        $metode = (stripos($metodeRaw, 'saldo') !== false) ? 'saldo_menurun' : 'garis_lurus';

        $akun_aset = !empty($data['akun_aset']) ? trim($data['akun_aset']) : null;
        $akun_akumulasi = !empty($data['akun_akumulasi']) ? trim($data['akun_akumulasi']) : (!empty($data['akun_akumulasi_depresiasi']) ? trim($data['akun_akumulasi_depresiasi']) : null);
        $akun_beban = !empty($data['akun_beban']) ? trim($data['akun_beban']) : (!empty($data['akun_beban_depresiasi']) ? trim($data['akun_beban_depresiasi']) : null);
        $status = !empty($data['status']) ? trim($data['status']) : 'Aktif';
        $keterangan = trim($data['keterangan'] ?? '');

        $query = "UPDATE {$this->table} SET 
                    kode_aset = :kode, nama_aset = :nama, tanggal_perolehan = :tanggal, harga_perolehan = :harga, 
                    nilai_residu = :residu, umur_ekonomis = :umur, metode_penyusutan = :metode, 
                    akun_aset = :akun_aset, akun_akumulasi = :akun_akumulasi, akun_beban = :akun_beban, 
                    status = :status, keterangan = :keterangan
                  WHERE id = :id AND tenant_id = :tenant_id";

        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->bind('kode', $kode);
        $this->db->bind('nama', $nama);
        $this->db->bind('tanggal', $tanggal);
        $this->db->bind('harga', $harga);
        $this->db->bind('residu', $residu);
        $this->db->bind('umur', $umur);
        $this->db->bind('metode', $metode);
        $this->db->bind('akun_aset', $akun_aset);
        $this->db->bind('akun_akumulasi', $akun_akumulasi);
        $this->db->bind('akun_beban', $akun_beban);
        $this->db->bind('status', $status);
        $this->db->bind('keterangan', $keterangan);
        $this->db->execute();

        return $this->db->rowCount();
    }

    public function hapusAset($id, $tenant_id) {
        $this->ensureTableSchema();
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND tenant_id = :tenant_id");
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function prosesPenyusutan($bulan, $tahun, $tenant_id) {
        $this->ensureTableSchema();
        $tanggal_akhir = date("Y-m-t", strtotime("$tahun-$bulan-01"));
        $this->db->query("SELECT * FROM {$this->table} WHERE (status = 'Aktif' OR status IS NULL) AND tenant_id = :tenant_id AND tanggal_perolehan <= :tgl_akhir");
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->bind('tgl_akhir', $tanggal_akhir);
        $assets = $this->db->resultSet();
        
        $count = 0;
        require_once APPROOT . '/app/models/Jurnal_model.php';
        $jurnalModel = new Jurnal_model($this->db);

        foreach ($assets as $aset) {
            $this->db->query("SELECT id FROM penyusutan_aset WHERE id_aset = :id_aset AND bulan = :bulan AND tahun = :tahun");
            $this->db->bind('id_aset', $aset['id']);
            $this->db->bind('bulan', (int)$bulan);
            $this->db->bind('tahun', (int)$tahun);
            if ($this->db->single()) continue; 
            
            $umur = max(1, (int)$aset['umur_ekonomis']);
            $monthly = round(($aset['harga_perolehan'] - $aset['nilai_residu']) / $umur, 2);
            if ($monthly <= 0) continue;
            
            if (empty($aset['akun_beban']) || empty($aset['akun_akumulasi'])) {
                continue;
            }

            $jurnalData = [
                'no_transaksi' => "DEP/" . str_replace('/', '', $aset['kode_aset']) . "/$tahun$bulan",
                'tanggal' => $tanggal_akhir,
                'deskripsi' => "Penyusutan Aset: {$aset['nama_aset']} ($bulan/$tahun)",
                'sumber_jurnal' => 'Penyusutan',
                'details' => [
                    ['kode_akun' => $aset['akun_beban'], 'debit' => $monthly, 'kredit' => 0],
                    ['kode_akun' => $aset['akun_akumulasi'], 'debit' => 0, 'kredit' => $monthly]
                ]
            ];
            
            $this->db->beginTransaction();
            try {
                $id_jurnal = $jurnalModel->simpanJurnal($jurnalData, $tenant_id);
                $this->db->query("UPDATE jurnal_umum SET is_locked = 1 WHERE id_jurnal = :id");
                $this->db->bind('id', $id_jurnal);
                $this->db->execute();

                $this->db->query("INSERT INTO penyusutan_aset (tenant_id, id_aset, id_jurnal, bulan, tahun, jumlah) VALUES (:tenant_id, :id_aset, :id_jurnal, :bulan, :tahun, :jumlah)");
                $this->db->bind('tenant_id', $tenant_id);
                $this->db->bind('id_aset', $aset['id']);
                $this->db->bind('id_jurnal', $id_jurnal);
                $this->db->bind('bulan', (int)$bulan);
                $this->db->bind('tahun', (int)$tahun);
                $this->db->bind('jumlah', $monthly);
                $this->db->execute();
                
                $this->db->commit();
                $count++;
            } catch (Throwable $e) {
                if ($this->db->inTransaction()) $this->db->rollBack();
                error_log("Gagal menyusutkan aset ID {$aset['id']}: " . $e->getMessage());
            }
        }
        return $count;
    }
}
