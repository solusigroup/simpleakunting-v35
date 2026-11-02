<?php

require_once 'Jurnal_model.php';

class TutupBuku_model 
{
    private $db;
    private static $tableChecked = false;

    public function __construct($db) {
        $this->db = $db;
        $this->ensureTableExists();
    }

    /**
     * Memastikan tabel periode_akuntansi selalu tersedia
     */
    private function ensureTableExists() {
        if (self::$tableChecked) return;
        self::$tableChecked = true;

        try {
            $sql = "CREATE TABLE IF NOT EXISTS `periode_akuntansi` (
                `id_periode` INT AUTO_INCREMENT PRIMARY KEY,
                `tenant_id` INT DEFAULT NULL,
                `tahun` INT NOT NULL,
                `bulan` INT NOT NULL,
                `status` ENUM('Open', 'Closed') DEFAULT 'Open',
                `tipe_proses` VARCHAR(20) DEFAULT 'Bulanan',
                `tanggal_tutup` DATETIME DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY `uk_tenant_tahun_bulan` (`tenant_id`, `tahun`, `bulan`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $this->db->query($sql);
            $this->db->execute();
        } catch (Throwable $e) {}
    }

    public function getLatestClosedPeriod($tenant_id) {
        $this->ensureTableExists();
        if ($tenant_id === null) {
            $this->db->query("SELECT * FROM periode_akuntansi WHERE status = 'Closed' AND tipe_proses = 'Bulanan' AND tenant_id IS NULL ORDER BY tahun DESC, bulan DESC LIMIT 1");
        } else {
            $this->db->query("SELECT * FROM periode_akuntansi WHERE status = 'Closed' AND tipe_proses = 'Bulanan' AND tenant_id = :tenant_id ORDER BY tahun DESC, bulan DESC LIMIT 1");
            $this->db->bind('tenant_id', $tenant_id);
        }
        return $this->db->single();
    }

    public function getClosedPeriods($tenant_id, $limit = 36) {
        $this->ensureTableExists();
        if ($tenant_id === null) {
            $this->db->query("SELECT * FROM periode_akuntansi WHERE tenant_id IS NULL ORDER BY tahun DESC, bulan DESC LIMIT $limit");
        } else {
            $this->db->query("SELECT * FROM periode_akuntansi WHERE tenant_id = :tenant_id ORDER BY tahun DESC, bulan DESC LIMIT $limit");
            $this->db->bind('tenant_id', $tenant_id);
        }
        return $this->db->resultSet();
    }
    
    public function getClosingJournalPreview($periode, $tenant_id, $isTahunan = false) {
        $jurnalModel = new Jurnal_model($this->db);
        
        if ($isTahunan) {
            $tanggal_mulai = $periode . '-01-01';
            $tanggal_selesai = $periode . '-12-31';
        } else { // Bulanan
            $tanggal_mulai = $periode . '-01';
            $tanggal_selesai = date('Y-m-t', strtotime($tanggal_mulai));
        }
        
        return $jurnalModel->getLabaRugi($tanggal_mulai, $tanggal_selesai, null, null, $tenant_id);
    }

    public function prosesTutupBuku($periode, $tipe_proses, $tenant_id) {
        $this->ensureTableExists();
        $jurnalModel = new Jurnal_model($this->db);
        $isTahunan = ($tipe_proses == 'Tahunan');

        if ($isTahunan) {
            $tanggal_mulai = $periode . '-01-01';
            $tanggal_selesai = $periode . '-12-31';
        } else { // Bulanan
            $tanggal_mulai = $periode . '-01';
            $tanggal_selesai = date('Y-m-t', strtotime($tanggal_mulai));
        }

        $labaRugiData = $this->getClosingJournalPreview($periode, $tenant_id, $isTahunan);
        $totalPendapatan = (float)($labaRugiData['total_pendapatan_1'] ?? 0);
        $totalBeban = (float)($labaRugiData['total_beban_1'] ?? 0);
        $labaBersih = $totalPendapatan - $totalBeban;

        // Ambil akun pengaturan dari tenant atau fallback sistem
        $akun_ikhtisar_lr = '3-3000';
        $akun_laba_ditahan = '3-2000';

        try {
            $this->db->query("SELECT akun_laba_ditahan, akun_ikhtisar_lr FROM perusahaan WHERE tenant_id = :tenant_id");
            $this->db->bind('tenant_id', $tenant_id);
            $pengaturan = $this->db->single();
            if (!empty($pengaturan['akun_ikhtisar_lr'])) {
                $akun_ikhtisar_lr = $pengaturan['akun_ikhtisar_lr'];
            }
            if (!empty($pengaturan['akun_laba_ditahan'])) {
                $akun_laba_ditahan = $pengaturan['akun_laba_ditahan'];
            }
        } catch (Throwable $e) {}

        // Validasi keberadaan akun di tabel akun
        try {
            $this->db->query("SELECT kode_akun FROM akun WHERE (tenant_id = :tenant_id OR tenant_id IS NULL) AND (kode_akun = :kode OR nama_akun LIKE '%ikhtisar%') LIMIT 1");
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('kode', $akun_ikhtisar_lr);
            $foundIkhtisar = $this->db->single();
            if ($foundIkhtisar) {
                $akun_ikhtisar_lr = $foundIkhtisar['kode_akun'];
            }

            $this->db->query("SELECT kode_akun FROM akun WHERE (tenant_id = :tenant_id OR tenant_id IS NULL) AND (kode_akun = :kode OR nama_akun LIKE '%laba ditahan%') LIMIT 1");
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('kode', $akun_laba_ditahan);
            $foundLaba = $this->db->single();
            if ($foundLaba) {
                $akun_laba_ditahan = $foundLaba['kode_akun'];
            }
        } catch (Throwable $e) {}

        $this->db->beginTransaction();
        try {
            // Hanya buat jurnal penutup jika ada mutasi akun pendapatan / beban
            $hasEntries = (!empty($labaRugiData['pendapatan']) || !empty($labaRugiData['beban']));

            if ($hasEntries) {
                $jurnalData = [
                    'no_transaksi' => 'CL-' . ($isTahunan ? $periode : date('Ym', strtotime($tanggal_mulai))),
                    'tanggal' => $tanggal_selesai,
                    'deskripsi' => 'Jurnal Penutup ' . $tipe_proses . ' Periode ' . ($isTahunan ? 'Tahun '.$periode : date('F Y', strtotime($tanggal_mulai))),
                    'sumber_jurnal' => 'Adjustment',
                    'details' => []
                ];

                // 1. Tutup semua akun Pendapatan ke Ikhtisar L/R
                if (!empty($labaRugiData['pendapatan'])) {
                    foreach($labaRugiData['pendapatan'] as $akun) {
                        $nominal = (float)$akun['total_1'];
                        if ($nominal > 0) {
                            $jurnalData['details'][] = ['kode_akun' => $akun['kode_akun'], 'debit' => $nominal, 'kredit' => 0];
                        } elseif ($nominal < 0) {
                            $jurnalData['details'][] = ['kode_akun' => $akun['kode_akun'], 'debit' => 0, 'kredit' => abs($nominal)];
                        }
                    }
                    if ($totalPendapatan > 0) {
                        $jurnalData['details'][] = ['kode_akun' => $akun_ikhtisar_lr, 'debit' => 0, 'kredit' => $totalPendapatan];
                    } elseif ($totalPendapatan < 0) {
                        $jurnalData['details'][] = ['kode_akun' => $akun_ikhtisar_lr, 'debit' => abs($totalPendapatan), 'kredit' => 0];
                    }
                }

                // 2. Tutup semua akun Beban ke Ikhtisar L/R
                if (!empty($labaRugiData['beban'])) {
                    if ($totalBeban > 0) {
                        $jurnalData['details'][] = ['kode_akun' => $akun_ikhtisar_lr, 'debit' => $totalBeban, 'kredit' => 0];
                    } elseif ($totalBeban < 0) {
                        $jurnalData['details'][] = ['kode_akun' => $akun_ikhtisar_lr, 'debit' => 0, 'kredit' => abs($totalBeban)];
                    }
                    foreach($labaRugiData['beban'] as $akun) {
                        $nominal = (float)$akun['total_1'];
                        if ($nominal > 0) {
                            $jurnalData['details'][] = ['kode_akun' => $akun['kode_akun'], 'debit' => 0, 'kredit' => $nominal];
                        } elseif ($nominal < 0) {
                            $jurnalData['details'][] = ['kode_akun' => $akun['kode_akun'], 'debit' => abs($nominal), 'kredit' => 0];
                        }
                    }
                }
                
                // 3. Tutup Ikhtisar L/R ke Laba Ditahan
                if ($labaBersih > 0) {
                    $jurnalData['details'][] = ['kode_akun' => $akun_ikhtisar_lr, 'debit' => $labaBersih, 'kredit' => 0];
                    $jurnalData['details'][] = ['kode_akun' => $akun_laba_ditahan, 'debit' => 0, 'kredit' => $labaBersih];
                } elseif ($labaBersih < 0) {
                    $jurnalData['details'][] = ['kode_akun' => $akun_laba_ditahan, 'debit' => abs($labaBersih), 'kredit' => 0];
                    $jurnalData['details'][] = ['kode_akun' => $akun_ikhtisar_lr, 'debit' => 0, 'kredit' => abs($labaBersih)];
                }

                if (!empty($jurnalData['details'])) {
                    $id_jurnal = $jurnalModel->simpanJurnal($jurnalData, $tenant_id);
                    if ($id_jurnal > 0) {
                        $this->db->query("UPDATE jurnal_umum SET is_locked = 1 WHERE id_jurnal = :id");
                        $this->db->bind('id', $id_jurnal);
                        $this->db->execute();
                    }
                }
            }

            // 4. Tandai periode sebagai 'Closed' (Period Lock)
            if ($isTahunan) {
                for ($bulan = 1; $bulan <= 12; $bulan++) {
                    $this->db->query("INSERT INTO periode_akuntansi (tenant_id, tahun, bulan, status, tipe_proses, tanggal_tutup) 
                                      VALUES (:tenant_id, :tahun, :bulan, 'Closed', :tipe, NOW()) 
                                      ON DUPLICATE KEY UPDATE status = 'Closed', tipe_proses = :tipe, tanggal_tutup = NOW()");
                    $this->db->bind('tenant_id', $tenant_id);
                    $this->db->bind('tahun', (int)$periode);
                    $this->db->bind('bulan', $bulan);
                    $this->db->bind('tipe', $tipe_proses);
                    $this->db->execute();
                }
            } else {
                $tahun = (int)date('Y', strtotime($tanggal_mulai));
                $bulan = (int)date('m', strtotime($tanggal_mulai));
                $this->db->query("INSERT INTO periode_akuntansi (tenant_id, tahun, bulan, status, tipe_proses, tanggal_tutup) 
                                  VALUES (:tenant_id, :tahun, :bulan, 'Closed', :tipe, NOW()) 
                                  ON DUPLICATE KEY UPDATE status = 'Closed', tipe_proses = :tipe, tanggal_tutup = NOW()");
                $this->db->bind('tenant_id', $tenant_id);
                $this->db->bind('tahun', $tahun);
                $this->db->bind('bulan', $bulan);
                $this->db->bind('tipe', $tipe_proses);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            Flash::setFlash('Gagal melakukan tutup buku: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    public function isPeriodClosed($tanggal, $tenant_id) {
        $this->ensureTableExists();
        $tahun = (int)date('Y', strtotime($tanggal));
        $bulan = (int)date('m', strtotime($tanggal));

        if ($tenant_id === null) {
            $this->db->query("SELECT status FROM periode_akuntansi WHERE tahun = :tahun AND bulan = :bulan AND tenant_id IS NULL");
        } else {
            $this->db->query("SELECT status FROM periode_akuntansi WHERE tahun = :tahun AND bulan = :bulan AND tenant_id = :tenant_id");
            $this->db->bind('tenant_id', $tenant_id);
        }
        $this->db->bind('tahun', $tahun);
        $this->db->bind('bulan', $bulan);
        
        $result = $this->db->single();
        
        return ($result && $result['status'] === 'Closed');
    }

    public function reopenPeriod($id_periode, $tenant_id) {
        $this->ensureTableExists();
        if ($tenant_id === null) {
            $this->db->query("UPDATE periode_akuntansi SET status = 'Open' WHERE id_periode = :id AND tenant_id IS NULL");
        } else {
            $this->db->query("UPDATE periode_akuntansi SET status = 'Open' WHERE id_periode = :id AND tenant_id = :tenant_id");
            $this->db->bind('tenant_id', $tenant_id);
        }
        $this->db->bind('id', $id_periode);
        return $this->db->execute();
    }
}
