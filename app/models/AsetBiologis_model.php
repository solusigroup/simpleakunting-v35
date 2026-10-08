<?php

class AsetBiologis_model {
    private $table = 'aset_biologis';
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->ensureTablesExist();
    }

    public function ensureTablesExist($tenant_id = null) {
        try {
            $this->db->query("SHOW TABLES LIKE '{$this->table}'");
            $tableExists = $this->db->single();
            if (!$tableExists) {
                $this->runAutoMigration($tenant_id);
            } else {
                $this->migrateOldAccountsTo14xxx();
            }
        } catch (Exception $e) {
            // Silently handle
        }
    }

    public function migrateOldAccountsTo14xxx() {
        try {
            $oldToNew = [
                '1-10700' => ['1-14100', 'Aset Biologis', 'Header'],
                '1-10701' => ['1-14101', 'Aset Biologis - Hewan Konsumsi', 'Detail'],
                '1-10702' => ['1-14102', 'Aset Biologis - Hewan Produktif', 'Detail'],
                '1-10703' => ['1-14103', 'Aset Biologis - Tanaman Konsumsi', 'Detail'],
                '1-10704' => ['1-14104', 'Aset Biologis - Tanaman Produktif', 'Detail'],
                '1-10705' => ['1-14105', 'Aset Biologis - Hasil pada Tanaman', 'Detail'],
                '1-10300' => ['1-14200', 'Persediaan Produk Agrikultur', 'Detail'],
            ];

            foreach ($oldToNew as $oldK => $target) {
                $newK = $target[0];
                $nama = $target[1];
                $tipe = $target[2];

                if ($oldK === '1-10300') {
                    $this->db->query("SELECT tenant_id FROM akun WHERE kode_akun = '1-10300' AND nama_akun LIKE '%Agrikultur%'");
                } else {
                    $this->db->query("SELECT tenant_id FROM akun WHERE kode_akun = :oldK");
                    $this->db->bind('oldK', $oldK);
                }
                $tenantsWithOld = $this->db->resultSet();

                foreach ($tenantsWithOld as $row) {
                    $tid = (int)$row['tenant_id'];

                    $this->db->query("SELECT kode_akun FROM akun WHERE kode_akun = :newK AND tenant_id = :tenant");
                    $this->db->bind('newK', $newK);
                    $this->db->bind('tenant', $tid);
                    if (!$this->db->single()) {
                        $this->db->query("INSERT INTO akun (kode_akun, tenant_id, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) 
                                          VALUES (:kode, :tenant, :nama, :tipe, 'Debit', 0, 'Debit')");
                        $this->db->bind('kode', $newK);
                        $this->db->bind('tenant', $tid);
                        $this->db->bind('nama', $nama);
                        $this->db->bind('tipe', $tipe);
                        $this->db->execute();
                    }

                    $this->db->query("UPDATE jurnal_detail jd 
                                      JOIN jurnal_umum ju ON jd.id_jurnal = ju.id_jurnal 
                                      SET jd.kode_akun = :newK 
                                      WHERE jd.kode_akun = :oldK AND ju.tenant_id = :tenant");
                    $this->db->bind('newK', $newK);
                    $this->db->bind('oldK', $oldK);
                    $this->db->bind('tenant', $tid);
                    $this->db->execute();

                    if ($oldK === '1-10300') {
                        $this->db->query("UPDATE aset_biologis SET akun_hasil_panen = :newK WHERE akun_hasil_panen = :oldK AND tenant_id = :tenant");
                    } else {
                        $this->db->query("UPDATE aset_biologis SET akun_aset = :newK WHERE akun_aset = :oldK AND tenant_id = :tenant");
                    }
                    $this->db->bind('newK', $newK);
                    $this->db->bind('oldK', $oldK);
                    $this->db->bind('tenant', $tid);
                    $this->db->execute();

                    if ($oldK === '1-10300') {
                        $this->db->query("DELETE FROM akun WHERE kode_akun = '1-10300' AND tenant_id = :tenant AND nama_akun LIKE '%Agrikultur%'");
                    } else {
                        $this->db->query("DELETE FROM akun WHERE kode_akun = :oldK AND tenant_id = :tenant");
                        $this->db->bind('oldK', $oldK);
                    }
                    $this->db->bind('tenant', $tid);
                    $this->db->execute();
                }
            }

            $this->seedBaganAkun();
        } catch (Exception $e) {
            // Silently handle
        }
    }

    public function runAutoMigration($tenant_id = null) {
        try {
            $queries = [
                "CREATE TABLE IF NOT EXISTS `aset_biologis` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `tenant_id` INT NOT NULL,
                    `kode_aset` VARCHAR(50) NOT NULL,
                    `nama_aset` VARCHAR(255) NOT NULL,
                    `kategori` ENUM('Hewan Konsumsi', 'Hewan Produktif', 'Tanaman Konsumsi', 'Tanaman Produktif', 'Hasil pada Tanaman') NOT NULL,
                    `jenis` VARCHAR(100) DEFAULT NULL COMMENT 'Spesies / ras / varietas',
                    `status_kematangan` ENUM('Belum Menghasilkan', 'Menghasilkan') NOT NULL DEFAULT 'Belum Menghasilkan',
                    `metode_pengukuran` ENUM('Nilai Wajar', 'Biaya Perolehan') NOT NULL DEFAULT 'Nilai Wajar',
                    `tanggal_perolehan` DATE NOT NULL,
                    `biaya_perolehan` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
                    `biaya_jual` DECIMAL(18,2) NOT NULL DEFAULT 0.00 COMMENT 'Estimasi biaya untuk menjual',
                    `nilai_wajar` DECIMAL(18,2) NOT NULL DEFAULT 0.00 COMMENT 'Nilai wajar pasar sebelum biaya jual',
                    `nilai_tercatat` DECIMAL(18,2) NOT NULL DEFAULT 0.00 COMMENT 'Carrying amount = Nilai Wajar - Biaya Jual',
                    `kuantitas` DECIMAL(18,4) NOT NULL DEFAULT 0.0000 COMMENT 'Jumlah fisik: ekor, pohon, ha, kg',
                    `satuan` VARCHAR(50) DEFAULT 'Ekor' COMMENT 'Ekor, Pohon, Ha, Kg, Ton, Liter',
                    `lokasi` VARCHAR(255) DEFAULT NULL,
                    `umur_ekonomis` INT DEFAULT NULL COMMENT 'Bulan, khusus model biaya',
                    `akun_aset` VARCHAR(20) NOT NULL COMMENT 'Akun neraca aset biologis',
                    `akun_keuntungan_fv` VARCHAR(20) DEFAULT NULL COMMENT 'Akun laba rugi keuntungan nilai wajar',
                    `akun_kerugian_fv` VARCHAR(20) DEFAULT NULL COMMENT 'Akun laba rugi kerugian nilai wajar',
                    `akun_beban_pemeliharaan` VARCHAR(20) DEFAULT NULL COMMENT 'Akun beban pakan / perawatan',
                    `akun_hasil_panen` VARCHAR(20) DEFAULT NULL COMMENT 'Akun persediaan hasil panen agrikultur',
                    `akun_keuntungan_panen` VARCHAR(20) DEFAULT NULL COMMENT 'Akun laba rugi keuntungan panen',
                    `status` ENUM('Aktif', 'Dipanen', 'Dijual', 'Mati/Hapus') NOT NULL DEFAULT 'Aktif',
                    `keterangan` TEXT DEFAULT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX `idx_ab_tenant` (`tenant_id`),
                    INDEX `idx_ab_kategori` (`tenant_id`, `kategori`),
                    INDEX `idx_ab_status` (`tenant_id`, `status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

                "CREATE TABLE IF NOT EXISTS `aset_biologis_penyesuaian` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `tenant_id` INT NOT NULL,
                    `id_aset_biologis` INT NOT NULL,
                    `id_jurnal` INT DEFAULT NULL,
                    `tanggal` DATE NOT NULL,
                    `tipe_penyesuaian` ENUM('Nilai Wajar', 'Penyusutan', 'Reklasifikasi') NOT NULL,
                    `nilai_tercatat_lama` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
                    `nilai_tercatat_baru` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
                    `selisih` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
                    `kuantitas_lama` DECIMAL(18,4) DEFAULT 0.0000,
                    `kuantitas_baru` DECIMAL(18,4) DEFAULT 0.0000,
                    `keterangan` TEXT DEFAULT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX `idx_abp_tenant_aset` (`tenant_id`, `id_aset_biologis`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

                "CREATE TABLE IF NOT EXISTS `aset_biologis_panen` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `tenant_id` INT NOT NULL,
                    `id_aset_biologis` INT NOT NULL,
                    `id_jurnal` INT DEFAULT NULL,
                    `tanggal` DATE NOT NULL,
                    `nama_hasil` VARCHAR(255) NOT NULL,
                    `kuantitas` DECIMAL(18,4) NOT NULL,
                    `satuan` VARCHAR(50) NOT NULL,
                    `nilai_wajar_panen` DECIMAL(18,2) NOT NULL,
                    `biaya_jual_panen` DECIMAL(18,2) DEFAULT 0.00,
                    `akun_persediaan` VARCHAR(20) NOT NULL,
                    `keterangan` TEXT DEFAULT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX `idx_abpanen_tenant_aset` (`tenant_id`, `id_aset_biologis`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

                "CREATE TABLE IF NOT EXISTS `aset_biologis_pelepasan` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `tenant_id` INT NOT NULL,
                    `id_aset_biologis` INT NOT NULL,
                    `id_jurnal` INT DEFAULT NULL,
                    `tanggal` DATE NOT NULL,
                    `tipe_pelepasan` ENUM('Penjualan', 'Kematian', 'Hapus Buku') NOT NULL,
                    `kuantitas_dilepas` DECIMAL(18,4) NOT NULL DEFAULT 0.0000,
                    `nilai_tercatat_dilepas` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
                    `nilai_jual` DECIMAL(18,2) DEFAULT 0.00,
                    `laba_rugi` DECIMAL(18,2) DEFAULT 0.00,
                    `keterangan` TEXT DEFAULT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX `idx_ablepas_tenant_aset` (`tenant_id`, `id_aset_biologis`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
            ];

            foreach ($queries as $q) {
                $this->db->query($q);
                $this->db->execute();
            }

            $this->seedBaganAkun($tenant_id);
            $this->migrateOldAccountsTo14xxx();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function seedBaganAkun($specific_tenant_id = null) {
        try {
            $this->db->query("SELECT id FROM tenants");
            $tenants = $this->db->resultSet();
            if (empty($tenants)) {
                $tenants = [];
            }

            if ($specific_tenant_id) {
                $found = false;
                foreach ($tenants as $t) {
                    if ((int)$t['id'] === (int)$specific_tenant_id) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $tenants[] = ['id' => (int)$specific_tenant_id];
                }
            }

            if (empty($tenants)) {
                $tenants = [['id' => 1]];
            }

            $accounts = [
                ['1-14100', 'Aset Biologis', 'Header', 'Debit'],
                ['1-14101', 'Aset Biologis - Hewan Konsumsi', 'Detail', 'Debit'],
                ['1-14102', 'Aset Biologis - Hewan Produktif', 'Detail', 'Debit'],
                ['1-14103', 'Aset Biologis - Tanaman Konsumsi', 'Detail', 'Debit'],
                ['1-14104', 'Aset Biologis - Tanaman Produktif', 'Detail', 'Debit'],
                ['1-14105', 'Aset Biologis - Hasil pada Tanaman', 'Detail', 'Debit'],
                ['1-14200', 'Persediaan Produk Agrikultur', 'Detail', 'Debit'],
                ['4-40300', 'Keuntungan Nilai Wajar Aset Biologis', 'Detail', 'Kredit'],
                ['4-40400', 'Keuntungan Panen Produk Agrikultur', 'Detail', 'Kredit'],
                ['4-40500', 'Pendapatan Penjualan Aset Biologis', 'Detail', 'Kredit'],
                ['5-50300', 'Beban Pokok Aset Biologis Terjual', 'Detail', 'Debit'],
                ['6-60400', 'Kerugian Penurunan Nilai Wajar Aset Biologis', 'Detail', 'Debit'],
                ['6-60500', 'Beban Pemeliharaan Aset Biologis', 'Detail', 'Debit'],
                ['6-60600', 'Kerugian Kematian/Hapus Aset Biologis', 'Detail', 'Debit'],
                ['6-60700', 'Beban Panen Agrikultur', 'Detail', 'Debit']
            ];

            foreach ($tenants as $t) {
                $tid = (int)$t['id'];
                foreach ($accounts as $acc) {
                    $this->db->query("SELECT kode_akun FROM akun WHERE kode_akun = :kode AND tenant_id = :tenant");
                    $this->db->bind('kode', $acc[0]);
                    $this->db->bind('tenant', $tid);
                    if (!$this->db->single()) {
                        $this->db->query("INSERT INTO akun (kode_akun, tenant_id, nama_akun, tipe_akun, saldo_normal, saldo_awal, posisi_saldo_normal) 
                                          VALUES (:kode, :tenant, :nama, :tipe, :saldo_normal, 0, :posisi)");
                        $this->db->bind('kode', $acc[0]);
                        $this->db->bind('tenant', $tid);
                        $this->db->bind('nama', $acc[1]);
                        $this->db->bind('tipe', $acc[2]);
                        $this->db->bind('saldo_normal', $acc[3]);
                        $this->db->bind('posisi', $acc[3]);
                        $this->db->execute();
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getAllAsetBiologis($tenant_id, $filters = []) {
        $query = "SELECT * FROM {$this->table} WHERE tenant_id = :tenant_id";
        $params = ['tenant_id' => $tenant_id];

        if (!empty($filters['kategori'])) {
            $query .= " AND kategori = :kategori";
            $params['kategori'] = $filters['kategori'];
        }
        if (!empty($filters['status'])) {
            $query .= " AND status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['status_kematangan'])) {
            $query .= " AND status_kematangan = :status_kematangan";
            $params['status_kematangan'] = $filters['status_kematangan'];
        }
        
        $query .= " ORDER BY created_at DESC";
        $this->db->query($query);
        
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        
        return $this->db->resultSet();
    }

    public function getAsetBiologisById($id, $tenant_id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND tenant_id = :tenant_id");
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->single();
    }

    public function simpanAsetBiologis($data, $tenant_id) {
        $metode = $data['metode_pengukuran'] ?? 'Nilai Wajar';
        $nilai_wajar = (float)($data['nilai_wajar'] ?? 0);
        $biaya_jual = (float)($data['biaya_jual'] ?? 0);
        $biaya_perolehan = (float)($data['biaya_perolehan'] ?? 0);

        if ($metode === 'Nilai Wajar') {
            $nilai_tercatat = max(0, $nilai_wajar - $biaya_jual);
        } else {
            $nilai_tercatat = $biaya_perolehan;
        }

        $this->db->beginTransaction();
        try {
            $query = "INSERT INTO {$this->table} (
                        tenant_id, kode_aset, nama_aset, kategori, jenis, status_kematangan, 
                        metode_pengukuran, tanggal_perolehan, biaya_perolehan, biaya_jual, 
                        nilai_wajar, nilai_tercatat, kuantitas, satuan, lokasi, umur_ekonomis, 
                        akun_aset, akun_keuntungan_fv, akun_kerugian_fv, akun_beban_pemeliharaan, 
                        akun_hasil_panen, akun_keuntungan_panen, status, keterangan
                      ) VALUES (
                        :tenant_id, :kode_aset, :nama_aset, :kategori, :jenis, :status_kematangan, 
                        :metode_pengukuran, :tanggal_perolehan, :biaya_perolehan, :biaya_jual, 
                        :nilai_wajar, :nilai_tercatat, :kuantitas, :satuan, :lokasi, :umur_ekonomis, 
                        :akun_aset, :akun_keuntungan_fv, :akun_kerugian_fv, :akun_beban_pemeliharaan, 
                        :akun_hasil_panen, :akun_keuntungan_panen, 'Aktif', :keterangan
                      )";
            
            $this->db->query($query);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('kode_aset', $data['kode_aset']);
            $this->db->bind('nama_aset', $data['nama_aset']);
            $this->db->bind('kategori', $data['kategori']);
            $this->db->bind('jenis', $data['jenis'] ?? null);
            $this->db->bind('status_kematangan', $data['status_kematangan']);
            $this->db->bind('metode_pengukuran', $metode);
            $this->db->bind('tanggal_perolehan', $data['tanggal_perolehan']);
            $this->db->bind('biaya_perolehan', $biaya_perolehan);
            $this->db->bind('biaya_jual', $biaya_jual);
            $this->db->bind('nilai_wajar', $nilai_wajar);
            $this->db->bind('nilai_tercatat', $nilai_tercatat);
            $this->db->bind('kuantitas', (float)($data['kuantitas'] ?? 0));
            $this->db->bind('satuan', $data['satuan'] ?? 'Ekor');
            $this->db->bind('lokasi', $data['lokasi'] ?? null);
            $this->db->bind('umur_ekonomis', !empty($data['umur_ekonomis']) ? (int)$data['umur_ekonomis'] : null);
            $this->db->bind('akun_aset', $data['akun_aset']);
            $this->db->bind('akun_keuntungan_fv', $data['akun_keuntungan_fv'] ?? '4-40300');
            $this->db->bind('akun_kerugian_fv', $data['akun_kerugian_fv'] ?? '6-60400');
            $this->db->bind('akun_beban_pemeliharaan', $data['akun_beban_pemeliharaan'] ?? '6-60500');
            $this->db->bind('akun_hasil_panen', $data['akun_hasil_panen'] ?? '1-14200');
            $this->db->bind('akun_keuntungan_panen', $data['akun_keuntungan_panen'] ?? '4-40400');
            $this->db->bind('keterangan', $data['keterangan'] ?? null);
            $this->db->execute();
            
            $id_aset = $this->db->lastInsertId();

            // Otomatis Jurnal Pengakuan Awal (Initial Recognition) jika nilai tercatat > 0
            if ($nilai_tercatat > 0) {
                require_once APPROOT . '/app/models/Jurnal_model.php';
                $jurnalModel = new Jurnal_model($this->db);
                
                $tanggal = $data['tanggal_perolehan'] ?? date('Y-m-d');
                $no_transaksi = "ABG/" . date('Ymd', strtotime($tanggal)) . "/" . sprintf('%03d', rand(1, 999));
                $akun_sumber = $data['akun_sumber'] ?? '1-10001'; // Default Kas/Bank
                
                // Cek jika akun_sumber ada di Bagan Akun, fallback ke akun Kas terdekat
                $checkKas = "SELECT kode_akun FROM akun WHERE kode_akun = :akun AND tenant_id = :tenant";
                $this->db->query($checkKas);
                $this->db->bind('akun', $akun_sumber);
                $this->db->bind('tenant', $tenant_id);
                if (!$this->db->single()) {
                    $this->db->query("SELECT kode_akun FROM akun WHERE kode_akun LIKE '1-100%' AND tenant_id = :tenant LIMIT 1");
                    $this->db->bind('tenant', $tenant_id);
                    $firstKas = $this->db->single();
                    $akun_sumber = $firstKas ? $firstKas['kode_akun'] : '1-10001';
                }

                $jurnalData = [
                    'no_transaksi' => $no_transaksi,
                    'tanggal' => $tanggal,
                    'deskripsi' => "Pengakuan Awal Aset Biologis: {$data['nama_aset']} ({$data['kode_aset']})",
                    'sumber_jurnal' => 'Aset Biologis',
                    'details' => [
                        ['kode_akun' => $data['akun_aset'], 'debit' => $nilai_tercatat, 'kredit' => 0],
                        ['kode_akun' => $akun_sumber, 'debit' => 0, 'kredit' => $nilai_tercatat]
                    ]
                ];
                
                $id_jurnal = $jurnalModel->simpanJurnal($jurnalData, $tenant_id);
                $this->db->query("UPDATE jurnal_umum SET is_locked = 1 WHERE id_jurnal = :id");
                $this->db->bind('id', $id_jurnal);
                $this->db->execute();
            }

            $this->db->commit();
            return $id_aset;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            return false;
        }
    }

    public function updateAsetBiologis($data, $tenant_id) {
        $metode = $data['metode_pengukuran'] ?? 'Nilai Wajar';
        $nilai_wajar = (float)($data['nilai_wajar'] ?? 0);
        $biaya_jual = (float)($data['biaya_jual'] ?? 0);
        $biaya_perolehan = (float)($data['biaya_perolehan'] ?? 0);

        if ($metode === 'Nilai Wajar') {
            $nilai_tercatat = max(0, $nilai_wajar - $biaya_jual);
        } else {
            $nilai_tercatat = $biaya_perolehan;
        }

        $query = "UPDATE {$this->table} SET 
                    kode_aset = :kode_aset, nama_aset = :nama_aset, kategori = :kategori, 
                    jenis = :jenis, status_kematangan = :status_kematangan, metode_pengukuran = :metode_pengukuran,
                    tanggal_perolehan = :tanggal_perolehan, biaya_perolehan = :biaya_perolehan, biaya_jual = :biaya_jual,
                    nilai_wajar = :nilai_wajar, nilai_tercatat = :nilai_tercatat, kuantitas = :kuantitas, 
                    satuan = :satuan, lokasi = :lokasi, umur_ekonomis = :umur_ekonomis, akun_aset = :akun_aset, 
                    akun_keuntungan_fv = :akun_keuntungan_fv, akun_kerugian_fv = :akun_kerugian_fv, 
                    akun_beban_pemeliharaan = :akun_beban_pemeliharaan, akun_hasil_panen = :akun_hasil_panen, 
                    akun_keuntungan_panen = :akun_keuntungan_panen, keterangan = :keterangan
                  WHERE id = :id AND tenant_id = :tenant_id";
        
        $this->db->query($query);
        $this->db->bind('id', $data['id']);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->bind('kode_aset', $data['kode_aset']);
        $this->db->bind('nama_aset', $data['nama_aset']);
        $this->db->bind('kategori', $data['kategori']);
        $this->db->bind('jenis', $data['jenis'] ?? null);
        $this->db->bind('status_kematangan', $data['status_kematangan']);
        $this->db->bind('metode_pengukuran', $metode);
        $this->db->bind('tanggal_perolehan', $data['tanggal_perolehan']);
        $this->db->bind('biaya_perolehan', $biaya_perolehan);
        $this->db->bind('biaya_jual', $biaya_jual);
        $this->db->bind('nilai_wajar', $nilai_wajar);
        $this->db->bind('nilai_tercatat', $nilai_tercatat);
        $this->db->bind('kuantitas', (float)($data['kuantitas'] ?? 0));
        $this->db->bind('satuan', $data['satuan'] ?? 'Ekor');
        $this->db->bind('lokasi', $data['lokasi'] ?? null);
        $this->db->bind('umur_ekonomis', !empty($data['umur_ekonomis']) ? (int)$data['umur_ekonomis'] : null);
        $this->db->bind('akun_aset', $data['akun_aset']);
        $this->db->bind('akun_keuntungan_fv', $data['akun_keuntungan_fv'] ?? '4-40300');
        $this->db->bind('akun_kerugian_fv', $data['akun_kerugian_fv'] ?? '6-60400');
        $this->db->bind('akun_beban_pemeliharaan', $data['akun_beban_pemeliharaan'] ?? '6-60500');
        $this->db->bind('akun_hasil_panen', $data['akun_hasil_panen'] ?? '1-14200');
        $this->db->bind('akun_keuntungan_panen', $data['akun_keuntungan_panen'] ?? '4-40400');
        $this->db->bind('keterangan', $data['keterangan'] ?? null);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function hapusAsetBiologis($id, $tenant_id) {
        $aset = $this->getAsetBiologisById($id, $tenant_id);
        if (!$aset) {
            return false;
        }

        // Cek jika sudah ada riwayat transaksi
        $this->db->query("SELECT id FROM aset_biologis_penyesuaian WHERE id_aset_biologis = :id AND tenant_id = :tenant LIMIT 1");
        $this->db->bind('id', $id);
        $this->db->bind('tenant', $tenant_id);
        if ($this->db->single()) return false;

        $this->db->query("SELECT id FROM aset_biologis_panen WHERE id_aset_biologis = :id AND tenant_id = :tenant LIMIT 1");
        $this->db->bind('id', $id);
        $this->db->bind('tenant', $tenant_id);
        if ($this->db->single()) return false;

        $this->db->query("SELECT id FROM aset_biologis_pelepasan WHERE id_aset_biologis = :id AND tenant_id = :tenant LIMIT 1");
        $this->db->bind('id', $id);
        $this->db->bind('tenant', $tenant_id);
        if ($this->db->single()) return false;

        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND tenant_id = :tenant_id");
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function prosesPenyesuaianBatch($data, $tenant_id) {
        require_once APPROOT . '/app/models/Jurnal_model.php';
        $jurnalModel = new Jurnal_model($this->db);
        
        $tanggal = $data['tanggal_penyesuaian'] ?? date('Y-m-d');
        $items = $data['penyesuaian'] ?? [];
        $count = 0;

        foreach ($items as $item) {
            if (empty($item['id_aset'])) continue;
            $id_aset = (int)$item['id_aset'];
            
            // Skip jika input kosong
            if (!isset($item['nilai_wajar_baru']) || $item['nilai_wajar_baru'] === '') continue;
            
            $nw_baru = (float)$item['nilai_wajar_baru'];
            $bj_baru = isset($item['biaya_jual_baru']) && $item['biaya_jual_baru'] !== '' ? (float)$item['biaya_jual_baru'] : 0.0;
            $nt_baru = max(0, $nw_baru - $bj_baru);

            $aset = $this->getAsetBiologisById($id_aset, $tenant_id);
            if (!$aset || $aset['status'] !== 'Aktif') continue;

            $nt_lama = (float)$aset['nilai_tercatat'];
            $selisih = $nt_baru - $nt_lama;
            if (abs($selisih) < 0.01) continue; // Tidak ada perubahan

            $this->db->beginTransaction();
            try {
                $no_transaksi = "ABG/" . date('Ymd', strtotime($tanggal)) . "/" . sprintf('%03d', rand(1, 999));
                $jurnalData = [
                    'no_transaksi' => $no_transaksi,
                    'tanggal' => $tanggal,
                    'deskripsi' => "Penyesuaian Nilai Wajar PSAK 241: {$aset['nama_aset']} ({$aset['kode_aset']})",
                    'sumber_jurnal' => 'Aset Biologis',
                    'details' => []
                ];

                if ($selisih > 0) {
                    // Kenaikan Nilai Wajar: Dr. Aset Biologis, Cr. Keuntungan Nilai Wajar (P&L)
                    $akun_untung = !empty($aset['akun_keuntungan_fv']) ? $aset['akun_keuntungan_fv'] : '4-40300';
                    $jurnalData['details'] = [
                        ['kode_akun' => $aset['akun_aset'], 'debit' => $selisih, 'kredit' => 0],
                        ['kode_akun' => $akun_untung, 'debit' => 0, 'kredit' => $selisih]
                    ];
                } else {
                    // Penurunan Nilai Wajar: Dr. Kerugian Nilai Wajar (P&L), Cr. Aset Biologis
                    $abs_selisih = abs($selisih);
                    $akun_rugi = !empty($aset['akun_kerugian_fv']) ? $aset['akun_kerugian_fv'] : '6-60400';
                    $jurnalData['details'] = [
                        ['kode_akun' => $akun_rugi, 'debit' => $abs_selisih, 'kredit' => 0],
                        ['kode_akun' => $aset['akun_aset'], 'debit' => 0, 'kredit' => $abs_selisih]
                    ];
                }

                $id_jurnal = $jurnalModel->simpanJurnal($jurnalData, $tenant_id);
                $this->db->query("UPDATE jurnal_umum SET is_locked = 1 WHERE id_jurnal = :id");
                $this->db->bind('id', $id_jurnal);
                $this->db->execute();

                // Catat ke tabel aset_biologis_penyesuaian
                $this->db->query("INSERT INTO aset_biologis_penyesuaian (
                                    tenant_id, id_aset_biologis, id_jurnal, tanggal, tipe_penyesuaian, 
                                    nilai_tercatat_lama, nilai_tercatat_baru, selisih, kuantitas_lama, kuantitas_baru, keterangan
                                  ) VALUES (
                                    :tenant_id, :id_aset, :id_jurnal, :tanggal, 'Nilai Wajar', 
                                    :nt_lama, :nt_baru, :selisih, :kuantitas, :kuantitas, :keterangan
                                  )");
                $this->db->bind('tenant_id', $tenant_id);
                $this->db->bind('id_aset', $id_aset);
                $this->db->bind('id_jurnal', $id_jurnal);
                $this->db->bind('tanggal', $tanggal);
                $this->db->bind('nt_lama', $nt_lama);
                $this->db->bind('nt_baru', $nt_baru);
                $this->db->bind('selisih', $selisih);
                $this->db->bind('kuantitas', $aset['kuantitas']);
                $this->db->bind('keterangan', "Penyesuaian Nilai Wajar Periode " . date('d/m/Y', strtotime($tanggal)));
                $this->db->execute();

                // Update saldo aset
                $this->db->query("UPDATE {$this->table} SET 
                                    nilai_wajar = :nw_baru, biaya_jual = :bj_baru, nilai_tercatat = :nt_baru 
                                  WHERE id = :id AND tenant_id = :tenant_id");
                $this->db->bind('nw_baru', $nw_baru);
                $this->db->bind('bj_baru', $bj_baru);
                $this->db->bind('nt_baru', $nt_baru);
                $this->db->bind('id', $id_aset);
                $this->db->bind('tenant_id', $tenant_id);
                $this->db->execute();

                $this->db->commit();
                $count++;
            } catch (Exception $e) {
                if ($this->db->inTransaction()) $this->db->rollBack();
            }
        }

        return $count;
    }

    public function prosesPanen($data, $tenant_id) {
        require_once APPROOT . '/app/models/Jurnal_model.php';
        $jurnalModel = new Jurnal_model($this->db);

        $id_aset = (int)$data['id_aset'];
        $aset = $this->getAsetBiologisById($id_aset, $tenant_id);
        if (!$aset || $aset['status'] !== 'Aktif') return false;

        $tanggal = $data['tanggal'] ?? date('Y-m-d');
        $nama_hasil = $data['nama_hasil'];
        $kuantitas_panen = (float)($data['kuantitas'] ?? 0);
        $satuan_panen = $data['satuan'] ?? 'Kg';
        $nilai_wajar_panen = (float)($data['nilai_wajar'] ?? 0);
        $biaya_panen = (float)($data['biaya_panen'] ?? 0);
        $akun_persediaan = $data['akun_persediaan'] ?? (!empty($aset['akun_hasil_panen']) ? $aset['akun_hasil_panen'] : '1-14200');
        $akun_untung_panen = !empty($aset['akun_keuntungan_panen']) ? $aset['akun_keuntungan_panen'] : '4-40400';

        // Nilai hasil agrikultur pada titik panen = Nilai wajar dikurangi biaya pelepasan (IAS 41.13)
        $nilai_bersih_panen = max(0, $nilai_wajar_panen - $biaya_panen);
        if ($nilai_bersih_panen <= 0) return false;

        $this->db->beginTransaction();
        try {
            $no_transaksi = "ABG/" . date('Ymd', strtotime($tanggal)) . "/" . sprintf('%03d', rand(1, 999));
            $jurnalData = [
                'no_transaksi' => $no_transaksi,
                'tanggal' => $tanggal,
                'deskripsi' => "Panen Agrikultur PSAK 241: {$nama_hasil} dari {$aset['nama_aset']}",
                'sumber_jurnal' => 'Panen',
                'details' => [
                    ['kode_akun' => $akun_persediaan, 'debit' => $nilai_bersih_panen, 'kredit' => 0],
                    ['kode_akun' => $akun_untung_panen, 'debit' => 0, 'kredit' => $nilai_bersih_panen]
                ]
            ];

            // Jika tanaman/hewan adalah aset konsumsi yang habis saat dipanen (Tanaman Konsumsi, Hasil pada Tanaman)
            $is_consumable = in_array($aset['kategori'], ['Tanaman Konsumsi', 'Hasil pada Tanaman']);
            $pengurangan_nilai_aset = 0;
            if ($is_consumable) {
                // Tentukan porsi kuantitas yang dipanen
                $ratio = ($aset['kuantitas'] > 0) ? min(1, $kuantitas_panen / $aset['kuantitas']) : 1;
                $pengurangan_nilai_aset = round($aset['nilai_tercatat'] * $ratio, 2);
                
                if ($pengurangan_nilai_aset > 0) {
                    // Derecognize biological asset carrying value
                    $akun_beban_panen = !empty($aset['akun_beban_pemeliharaan']) ? $aset['akun_beban_pemeliharaan'] : '6-60700';
                    $jurnalData['details'][] = ['kode_akun' => $akun_beban_panen, 'debit' => $pengurangan_nilai_aset, 'kredit' => 0];
                    $jurnalData['details'][] = ['kode_akun' => $aset['akun_aset'], 'debit' => 0, 'kredit' => $pengurangan_nilai_aset];
                }
            }

            $id_jurnal = $jurnalModel->simpanJurnal($jurnalData, $tenant_id);
            $this->db->query("UPDATE jurnal_umum SET is_locked = 1 WHERE id_jurnal = :id");
            $this->db->bind('id', $id_jurnal);
            $this->db->execute();

            // Insert ke aset_biologis_panen
            $this->db->query("INSERT INTO aset_biologis_panen (
                                tenant_id, id_aset_biologis, id_jurnal, tanggal, nama_hasil, 
                                kuantitas, satuan, nilai_wajar_panen, biaya_jual_panen, akun_persediaan, keterangan
                              ) VALUES (
                                :tenant_id, :id_aset, :id_jurnal, :tanggal, :nama_hasil, 
                                :kuantitas, :satuan, :nw_panen, :bj_panen, :akun_persediaan, :keterangan
                              )");
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('id_aset', $id_aset);
            $this->db->bind('id_jurnal', $id_jurnal);
            $this->db->bind('tanggal', $tanggal);
            $this->db->bind('nama_hasil', $nama_hasil);
            $this->db->bind('kuantitas', $kuantitas_panen);
            $this->db->bind('satuan', $satuan_panen);
            $this->db->bind('nw_panen', $nilai_wajar_panen);
            $this->db->bind('bj_panen', $biaya_panen);
            $this->db->bind('akun_persediaan', $akun_persediaan);
            $this->db->bind('keterangan', $data['keterangan'] ?? "Panen {$nama_hasil} ({$kuantitas_panen} {$satuan_panen})");
            $this->db->execute();

            // Update sisa aset jika aset konsumsi
            if ($is_consumable) {
                $sisa_kuantitas = max(0, $aset['kuantitas'] - $kuantitas_panen);
                $sisa_nilai_tercatat = max(0, $aset['nilai_tercatat'] - $pengurangan_nilai_aset);
                $status_baru = ($sisa_kuantitas <= 0) ? 'Dipanen' : 'Aktif';

                $this->db->query("UPDATE {$this->table} SET 
                                    kuantitas = :sisa_q, nilai_tercatat = :sisa_nt, nilai_wajar = :sisa_nt, status = :status 
                                  WHERE id = :id AND tenant_id = :tenant_id");
                $this->db->bind('sisa_q', $sisa_kuantitas);
                $this->db->bind('sisa_nt', $sisa_nilai_tercatat);
                $this->db->bind('status', $status_baru);
                $this->db->bind('id', $id_aset);
                $this->db->bind('tenant_id', $tenant_id);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            return false;
        }
    }

    public function prosesPelepasan($data, $tenant_id) {
        require_once APPROOT . '/app/models/Jurnal_model.php';
        $jurnalModel = new Jurnal_model($this->db);

        $id_aset = (int)$data['id_aset'];
        $aset = $this->getAsetBiologisById($id_aset, $tenant_id);
        if (!$aset || $aset['status'] !== 'Aktif') return false;

        $tipe = $data['tipe_pelepasan'] ?? 'Penjualan'; // Penjualan, Kematian, Hapus Buku
        $tanggal = $data['tanggal'] ?? date('Y-m-d');
        $kuantitas_dilepas = (float)($data['kuantitas'] ?? 0);
        if ($kuantitas_dilepas <= 0) return false;

        // Proporsi pelepasan
        $ratio = ($aset['kuantitas'] > 0) ? min(1, $kuantitas_dilepas / $aset['kuantitas']) : 1;
        $nilai_tercatat_dilepas = round($aset['nilai_tercatat'] * $ratio, 2);
        $nilai_jual = ($tipe === 'Penjualan') ? (float)($data['nilai_jual'] ?? 0) : 0.0;
        $laba_rugi = ($tipe === 'Penjualan') ? ($nilai_jual - $nilai_tercatat_dilepas) : (-$nilai_tercatat_dilepas);

        $this->db->beginTransaction();
        try {
            $no_transaksi = "ABG/" . date('Ymd', strtotime($tanggal)) . "/" . sprintf('%03d', rand(1, 999));
            $jurnalData = [
                'no_transaksi' => $no_transaksi,
                'tanggal' => $tanggal,
                'deskripsi' => "Pelepasan Aset Biologis ({$tipe}): {$aset['nama_aset']} ({$aset['kode_aset']})",
                'sumber_jurnal' => 'Pelepasan',
                'details' => []
            ];

            if ($tipe === 'Penjualan') {
                $akun_kas = $data['akun_kas'] ?? '1-10001';
                $jurnalData['details'][] = ['kode_akun' => $akun_kas, 'debit' => $nilai_jual, 'kredit' => 0];
                $jurnalData['details'][] = ['kode_akun' => $aset['akun_aset'], 'debit' => 0, 'kredit' => $nilai_tercatat_dilepas];

                if ($laba_rugi > 0) {
                    // Keuntungan Pelepasan / Penjualan
                    $jurnalData['details'][] = ['kode_akun' => '4-40500', 'debit' => 0, 'kredit' => $laba_rugi];
                } elseif ($laba_rugi < 0) {
                    // Kerugian Penjualan Aset Biologis
                    $jurnalData['details'][] = ['kode_akun' => '5-50300', 'debit' => abs($laba_rugi), 'kredit' => 0];
                }
            } else {
                // Kematian / Hapus Buku
                $jurnalData['details'][] = ['kode_akun' => '6-60600', 'debit' => $nilai_tercatat_dilepas, 'kredit' => 0];
                $jurnalData['details'][] = ['kode_akun' => $aset['akun_aset'], 'debit' => 0, 'kredit' => $nilai_tercatat_dilepas];
            }

            $id_jurnal = $jurnalModel->simpanJurnal($jurnalData, $tenant_id);
            $this->db->query("UPDATE jurnal_umum SET is_locked = 1 WHERE id_jurnal = :id");
            $this->db->bind('id', $id_jurnal);
            $this->db->execute();

            // Insert ke aset_biologis_pelepasan
            $this->db->query("INSERT INTO aset_biologis_pelepasan (
                                tenant_id, id_aset_biologis, id_jurnal, tanggal, tipe_pelepasan, 
                                kuantitas_dilepas, nilai_tercatat_dilepas, nilai_jual, laba_rugi, keterangan
                              ) VALUES (
                                :tenant_id, :id_aset, :id_jurnal, :tanggal, :tipe, 
                                :kuantitas, :nt_dilepas, :nj, :laba_rugi, :keterangan
                              )");
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('id_aset', $id_aset);
            $this->db->bind('id_jurnal', $id_jurnal);
            $this->db->bind('tanggal', $tanggal);
            $this->db->bind('tipe', $tipe);
            $this->db->bind('kuantitas', $kuantitas_dilepas);
            $this->db->bind('nt_dilepas', $nilai_tercatat_dilepas);
            $this->db->bind('nj', $nilai_jual);
            $this->db->bind('laba_rugi', $laba_rugi);
            $this->db->bind('keterangan', $data['keterangan'] ?? "{$tipe} {$kuantitas_dilepas} {$aset['satuan']}");
            $this->db->execute();

            // Update sisa aset
            $sisa_kuantitas = max(0, $aset['kuantitas'] - $kuantitas_dilepas);
            $sisa_nilai_tercatat = max(0, $aset['nilai_tercatat'] - $nilai_tercatat_dilepas);
            $status_baru = ($sisa_kuantitas <= 0) ? ($tipe === 'Penjualan' ? 'Dijual' : 'Mati/Hapus') : 'Aktif';

            $this->db->query("UPDATE {$this->table} SET 
                                kuantitas = :sisa_q, nilai_tercatat = :sisa_nt, nilai_wajar = :sisa_nt, status = :status 
                              WHERE id = :id AND tenant_id = :tenant_id");
            $this->db->bind('sisa_q', $sisa_kuantitas);
            $this->db->bind('sisa_nt', $sisa_nilai_tercatat);
            $this->db->bind('status', $status_baru);
            $this->db->bind('id', $id_aset);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            return false;
        }
    }

    public function reklasifikasi($id, $tenant_id) {
        $aset = $this->getAsetBiologisById($id, $tenant_id);
        if (!$aset || $aset['status_kematangan'] === 'Menghasilkan') {
            return false;
        }

        $this->db->beginTransaction();
        try {
            $tanggal = date('Y-m-d');
            
            $this->db->query("UPDATE {$this->table} SET status_kematangan = 'Menghasilkan' WHERE id = :id AND tenant_id = :tenant_id");
            $this->db->bind('id', $id);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->execute();

            // Catat log penyesuaian reklasifikasi
            $this->db->query("INSERT INTO aset_biologis_penyesuaian (
                                tenant_id, id_aset_biologis, tanggal, tipe_penyesuaian, 
                                nilai_tercatat_lama, nilai_tercatat_baru, selisih, kuantitas_lama, kuantitas_baru, keterangan
                              ) VALUES (
                                :tenant_id, :id_aset, :tanggal, 'Reklasifikasi', 
                                :nt, :nt, 0, :kuantitas, :kuantitas, 'Reklasifikasi dari Belum Menghasilkan ke Menghasilkan (Dewasa/Matang)'
                              )");
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('id_aset', $id);
            $this->db->bind('tanggal', $tanggal);
            $this->db->bind('nt', $aset['nilai_tercatat']);
            $this->db->bind('kuantitas', $aset['kuantitas']);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            return false;
        }
    }

    public function getRekonsiliasiNilaiTercatat($tanggal_mulai, $tanggal_selesai, $tenant_id) {
        $kategoris = ['Hewan Konsumsi', 'Hewan Produktif', 'Tanaman Konsumsi', 'Tanaman Produktif', 'Hasil pada Tanaman'];
        $rekonsiliasi = [];

        foreach ($kategoris as $kat) {
            // 1. Saldo Awal: Nilai tercatat aset aktif yang diperoleh sebelum tanggal mulai
            $this->db->query("SELECT COALESCE(SUM(nilai_tercatat), 0) as total 
                              FROM {$this->table} 
                              WHERE tenant_id = :tenant_id AND kategori = :kategori AND tanggal_perolehan < :tgl_mulai");
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('kategori', $kat);
            $this->db->bind('tgl_mulai', $tanggal_mulai);
            $saldo_awal = (float)$this->db->single()['total'];

            // 2. Pembelian dalam periode (biaya_perolehan > 0)
            $this->db->query("SELECT COALESCE(SUM(biaya_perolehan), 0) as total 
                              FROM {$this->table} 
                              WHERE tenant_id = :tenant_id AND kategori = :kategori 
                              AND tanggal_perolehan BETWEEN :tgl_mulai AND :tgl_selesai AND biaya_perolehan > 0");
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('kategori', $kat);
            $this->db->bind('tgl_mulai', $tanggal_mulai);
            $this->db->bind('tgl_selesai', $tanggal_selesai);
            $pembelian = (float)$this->db->single()['total'];

            // 3. Kelahiran / Prokreasi dalam periode (biaya_perolehan == 0)
            $this->db->query("SELECT COALESCE(SUM(nilai_tercatat), 0) as total 
                              FROM {$this->table} 
                              WHERE tenant_id = :tenant_id AND kategori = :kategori 
                              AND tanggal_perolehan BETWEEN :tgl_mulai AND :tgl_selesai AND biaya_perolehan = 0");
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('kategori', $kat);
            $this->db->bind('tgl_mulai', $tanggal_mulai);
            $this->db->bind('tgl_selesai', $tanggal_selesai);
            $kelahiran = (float)$this->db->single()['total'];

            // 4. Keuntungan Nilai Wajar dalam periode
            $this->db->query("SELECT COALESCE(SUM(p.selisih), 0) as total 
                              FROM aset_biologis_penyesuaian p 
                              JOIN {$this->table} a ON p.id_aset_biologis = a.id 
                              WHERE p.tenant_id = :tenant_id AND a.kategori = :kategori 
                              AND p.tanggal BETWEEN :tgl_mulai AND :tgl_selesai AND p.selisih > 0");
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('kategori', $kat);
            $this->db->bind('tgl_mulai', $tanggal_mulai);
            $this->db->bind('tgl_selesai', $tanggal_selesai);
            $keuntungan_fv = (float)$this->db->single()['total'];

            // 5. Kerugian Nilai Wajar dalam periode
            $this->db->query("SELECT COALESCE(SUM(ABS(p.selisih)), 0) as total 
                              FROM aset_biologis_penyesuaian p 
                              JOIN {$this->table} a ON p.id_aset_biologis = a.id 
                              WHERE p.tenant_id = :tenant_id AND a.kategori = :kategori 
                              AND p.tanggal BETWEEN :tgl_mulai AND :tgl_selesai AND p.selisih < 0");
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('kategori', $kat);
            $this->db->bind('tgl_mulai', $tanggal_mulai);
            $this->db->bind('tgl_selesai', $tanggal_selesai);
            $kerugian_fv = (float)$this->db->single()['total'];

            // 6. Pengurangan akibat Panen dalam periode
            $this->db->query("SELECT COALESCE(SUM(pan.nilai_wajar_panen - pan.biaya_jual_panen), 0) as total 
                              FROM aset_biologis_panen pan 
                              JOIN {$this->table} a ON pan.id_aset_biologis = a.id 
                              WHERE pan.tenant_id = :tenant_id AND a.kategori = :kategori 
                              AND pan.tanggal BETWEEN :tgl_mulai AND :tgl_selesai");
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('kategori', $kat);
            $this->db->bind('tgl_mulai', $tanggal_mulai);
            $this->db->bind('tgl_selesai', $tanggal_selesai);
            $panen = (float)$this->db->single()['total'];

            // 7. Pengurangan akibat Penjualan
            $this->db->query("SELECT COALESCE(SUM(pel.nilai_tercatat_dilepas), 0) as total 
                              FROM aset_biologis_pelepasan pel 
                              JOIN {$this->table} a ON pel.id_aset_biologis = a.id 
                              WHERE pel.tenant_id = :tenant_id AND a.kategori = :kategori 
                              AND pel.tipe_pelepasan = 'Penjualan' AND pel.tanggal BETWEEN :tgl_mulai AND :tgl_selesai");
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('kategori', $kat);
            $this->db->bind('tgl_mulai', $tanggal_mulai);
            $this->db->bind('tgl_selesai', $tanggal_selesai);
            $penjualan = (float)$this->db->single()['total'];

            // 8. Pengurangan akibat Kematian / Hapus Buku
            $this->db->query("SELECT COALESCE(SUM(pel.nilai_tercatat_dilepas), 0) as total 
                              FROM aset_biologis_pelepasan pel 
                              JOIN {$this->table} a ON pel.id_aset_biologis = a.id 
                              WHERE pel.tenant_id = :tenant_id AND a.kategori = :kategori 
                              AND pel.tipe_pelepasan != 'Penjualan' AND pel.tanggal BETWEEN :tgl_mulai AND :tgl_selesai");
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('kategori', $kat);
            $this->db->bind('tgl_mulai', $tanggal_mulai);
            $this->db->bind('tgl_selesai', $tanggal_selesai);
            $kematian = (float)$this->db->single()['total'];

            // 9. Saldo Akhir saat ini
            $this->db->query("SELECT COALESCE(SUM(nilai_tercatat), 0) as total 
                              FROM {$this->table} 
                              WHERE tenant_id = :tenant_id AND kategori = :kategori AND status = 'Aktif'");
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('kategori', $kat);
            $saldo_akhir = (float)$this->db->single()['total'];

            $rekonsiliasi[$kat] = [
                'saldo_awal' => $saldo_awal,
                'pembelian' => $pembelian,
                'kelahiran' => $kelahiran,
                'keuntungan_fv' => $keuntungan_fv,
                'kerugian_fv' => $kerugian_fv,
                'panen' => $panen,
                'penjualan' => $penjualan,
                'kematian' => $kematian,
                'saldo_akhir' => $saldo_akhir
            ];
        }

        return $rekonsiliasi;
    }

    public function getRingkasanKlasifikasi($tenant_id) {
        $this->db->query("SELECT kategori, status_kematangan, COUNT(*) as jumlah, COALESCE(SUM(nilai_tercatat), 0) as total_nilai 
                          FROM {$this->table} 
                          WHERE tenant_id = :tenant_id AND status = 'Aktif' 
                          GROUP BY kategori, status_kematangan 
                          ORDER BY kategori ASC, status_kematangan DESC");
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->resultSet();
    }

    public function getRiwayatPenyesuaian($id_aset, $tenant_id) {
        $this->db->query("SELECT * FROM aset_biologis_penyesuaian 
                          WHERE id_aset_biologis = :id_aset AND tenant_id = :tenant_id 
                          ORDER BY tanggal DESC, id DESC");
        $this->db->bind('id_aset', $id_aset);
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->resultSet();
    }

    public function getRiwayatPanen($id_aset, $tenant_id) {
        $this->db->query("SELECT * FROM aset_biologis_panen 
                          WHERE id_aset_biologis = :id_aset AND tenant_id = :tenant_id 
                          ORDER BY tanggal DESC, id DESC");
        $this->db->bind('id_aset', $id_aset);
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->resultSet();
    }

    public function getRiwayatPelepasan($id_aset, $tenant_id) {
        $this->db->query("SELECT * FROM aset_biologis_pelepasan 
                          WHERE id_aset_biologis = :id_aset AND tenant_id = :tenant_id 
                          ORDER BY tanggal DESC, id DESC");
        $this->db->bind('id_aset', $id_aset);
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->resultSet();
    }
}
