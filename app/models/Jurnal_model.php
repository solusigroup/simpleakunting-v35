<?php

class Jurnal_model {
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAllJurnal($tenant_id, $id_unit = null)
    {
        $query = "SELECT 
                    ju.id_jurnal, ju.no_transaksi, ju.tanggal, ju.deskripsi, ju.sumber_jurnal, ju.is_locked, ju.id_program, ju.id_unit,
                    COALESCE(SUM(jd.debit), 0) as total
                  FROM jurnal_umum ju
                  LEFT JOIN jurnal_detail jd ON ju.id_jurnal = jd.id_jurnal
                  WHERE ju.tenant_id = :tenant_id";
        
        if ($id_unit) {
            $query .= " AND ju.id_unit = :id_unit";
        }

        $query .= " GROUP BY ju.id_jurnal ORDER BY ju.tanggal DESC, ju.no_transaksi DESC";
        
        $this->db->query($query);
        $this->db->bind('tenant_id', $tenant_id);
        if ($id_unit) $this->db->bind('id_unit', $id_unit);
        
        return $this->db->resultSet();
    }

    public function generateNoTransaksi($tenant_id) {
        $prefix = 'JU-' . date('ym') . '-';
        $this->db->query("SELECT no_transaksi FROM jurnal_umum WHERE tenant_id = :tenant_id AND no_transaksi LIKE :prefix ORDER BY CAST(SUBSTRING(no_transaksi, 9, 3) AS UNSIGNED) DESC LIMIT 1");
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->bind('prefix', $prefix . '%');
        $result = $this->db->single();

        if ($result) {
            $last_number = (int) substr($result['no_transaksi'], -3);
            $new_number = str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $new_number = '001';
        }

        return $prefix . $new_number;
    }

    public function simpanJurnal($data, $tenant_id)
    {
        $isTransactionActive = $this->db->inTransaction();
        if (!$isTransactionActive) {
            $this->db->beginTransaction();
        }
        
        try {
            $queryHeader = "INSERT INTO jurnal_umum (tenant_id, no_transaksi, tanggal, deskripsi, sumber_jurnal, id_program, id_unit) 
                            VALUES (:tenant_id, :no_transaksi, :tanggal, :deskripsi, :sumber_jurnal, :id_program, :id_unit)";
            $this->db->query($queryHeader);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('no_transaksi', $data['no_transaksi']);
            $this->db->bind('tanggal', $data['tanggal']);
            $this->db->bind('deskripsi', $data['deskripsi']);
            $this->db->bind('sumber_jurnal', $data['sumber_jurnal'] ?? 'Jurnal Umum');
            $this->db->bind('id_program', $data['id_program'] ?? null);
            $this->db->bind('id_unit', $data['id_unit'] ?? null);
            $this->db->execute();
            $id_jurnal = $this->db->lastInsertId();

            $queryDetail = "INSERT INTO jurnal_detail (id_jurnal, kode_akun, debit, kredit)
                            VALUES (:id_jurnal, :kode_akun, :debit, :kredit)";
            foreach ($data['details'] as $detail) {
                if(!empty($detail['kode_akun']) && (isset($detail['debit']) && $detail['debit'] > 0 || isset($detail['kredit']) && $detail['kredit'] > 0)){
                    $this->db->query($queryDetail);
                    $this->db->bind('id_jurnal', $id_jurnal);
                    $this->db->bind('kode_akun', $detail['kode_akun']);
                    $this->db->bind('debit', (float)($detail['debit'] ?? 0));
                    $this->db->bind('kredit', (float)($detail['kredit'] ?? 0));
                    $this->db->execute();
                }
            }
            
            if (!$isTransactionActive) {
                $this->db->commit();
            }
            return $id_jurnal;

        } catch (\PDOException $e) {
            if (!$isTransactionActive) {
                $this->db->rollBack();
            }
            throw $e; 
        }
    }
    
    public function updateJurnal($data, $tenant_id) {
        if ($this->isJurnalLocked($data['id_jurnal'])) {
            return -1;
        }
        $isTransactionActive = $this->db->inTransaction();
        if (!$isTransactionActive) {
            $this->db->beginTransaction();
        }
        try {
            $queryHeader = "UPDATE jurnal_umum SET no_transaksi = :no_transaksi, tanggal = :tanggal, deskripsi = :deskripsi, id_program = :id_program, id_unit = :id_unit WHERE id_jurnal = :id_jurnal AND tenant_id = :tenant_id";
            $this->db->query($queryHeader);
            $this->db->bind('no_transaksi', $data['no_transaksi']);
            $this->db->bind('tanggal', $data['tanggal']);
            $this->db->bind('deskripsi', $data['deskripsi']);
            $this->db->bind('id_program', $data['id_program'] ?? null);
            $this->db->bind('id_unit', $data['id_unit'] ?? null);
            $this->db->bind('id_jurnal', $data['id_jurnal']);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->execute();

            $this->db->query("DELETE FROM jurnal_detail WHERE id_jurnal = :id_jurnal");
            $this->db->bind('id_jurnal', $data['id_jurnal']);
            $this->db->execute();

            $queryDetail = "INSERT INTO jurnal_detail (id_jurnal, kode_akun, debit, kredit) VALUES (:id_jurnal, :kode_akun, :debit, :kredit)";
            foreach ($data['details'] as $detail) {
                if(!empty($detail['kode_akun']) && ($detail['debit'] > 0 || $detail['kredit'] > 0)){
                    $this->db->query($queryDetail);
                    $this->db->bind('id_jurnal', $data['id_jurnal']);
                    $this->db->bind('kode_akun', $detail['kode_akun']);
                    $this->db->bind('debit', (float)$detail['debit']);
                    $this->db->bind('kredit', (float)$detail['kredit']);
                    $this->db->execute();
                }
            }
            if (!$isTransactionActive) {
                $this->db->commit();
            }
            return 1;
        } catch (\PDOException $e) {
            if (!$isTransactionActive) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function hapusJurnal($id, $tenant_id, $force = false) {
        if ($this->isJurnalLocked($id) && !$force) {
            return 0;
        }
        $query = "DELETE FROM jurnal_umum WHERE id_jurnal = :id_jurnal AND tenant_id = :tenant_id";
        $this->db->query($query);
        $this->db->bind('id_jurnal', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function getJurnalWithDetailsById($id, $tenant_id) {
        $sql = "SELECT ju.*, jd.kode_akun, jd.debit, jd.kredit, a.nama_akun
                FROM jurnal_umum ju
                LEFT JOIN jurnal_detail jd ON ju.id_jurnal = jd.id_jurnal
                LEFT JOIN akun a ON jd.kode_akun = a.kode_akun AND a.tenant_id = ju.tenant_id
                WHERE ju.id_jurnal = :id_jurnal AND ju.tenant_id = :tenant_id";
        $this->db->query($sql);
        $this->db->bind('id_jurnal', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $results = $this->db->resultSet();
        if(empty($results)){ return null; }
        $jurnal = [
            'id_jurnal' => $results[0]['id_jurnal'],
            'no_transaksi' => $results[0]['no_transaksi'],
            'tanggal' => $results[0]['tanggal'],
            'deskripsi' => $results[0]['deskripsi'],
            'sumber_jurnal' => $results[0]['sumber_jurnal'],
            'is_locked' => $results[0]['is_locked'],
            'id_program' => $results[0]['id_program'],
            'id_unit' => $results[0]['id_unit'],
            'details' => []
        ];
        if (!is_null($results[0]['kode_akun'])) {
            foreach($results as $row){
                $jurnal['details'][] = [
                    'kode_akun' => $row['kode_akun'],
                    'nama_akun' => $row['nama_akun'],
                    'debit' => $row['debit'],
                    'kredit' => $row['kredit']
                ];
            }
        }
        return $jurnal;
    }
    
    public function isJurnalLocked($id) {
        $this->db->query("SELECT is_locked FROM jurnal_umum WHERE id_jurnal = :id");
        $this->db->bind('id', $id);
        $result = $this->db->single();
        return ($result && $result['is_locked'] == 1);
    }
    
    public function getBukuBesar($kode_akun, $tanggal_mulai, $tanggal_selesai, $tenant_id, $id_unit = null, $id_program = null) {
        $this->db->query("SELECT saldo_awal, posisi_saldo_normal FROM akun WHERE kode_akun = :kode_akun AND tenant_id = :tenant_id");
        $this->db->bind('kode_akun', $kode_akun);
        $this->db->bind('tenant_id', $tenant_id);
        $akunInfo = $this->db->single();
        if (!$akunInfo) {
            return ['saldo_awal_periode' => 0, 'posisi_saldo_normal' => 'Debit', 'transaksi' => []];
        }
        
        $querySaldoSebelum = "SELECT COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(kredit), 0) as total_kredit
                             FROM jurnal_detail jd
                             JOIN jurnal_umum ju ON jd.id_jurnal = ju.id_jurnal
                             WHERE jd.kode_akun = :kode_akun AND ju.tanggal < :tanggal_mulai AND ju.tenant_id = :tenant_id";
        if ($id_unit) $querySaldoSebelum .= " AND ju.id_unit = :id_unit";
        if ($id_program) $querySaldoSebelum .= " AND ju.id_program = :id_program";
        
        $this->db->query($querySaldoSebelum);
        $this->db->bind('kode_akun', $kode_akun);
        $this->db->bind('tanggal_mulai', $tanggal_mulai);
        $this->db->bind('tenant_id', $tenant_id);
        if ($id_unit) $this->db->bind('id_unit', $id_unit);
        if ($id_program) $this->db->bind('id_program', $id_program);
        $saldoSebelum = $this->db->single();

        // Saldo awal dari master akun hanya masuk ke unit Utama (jika id_unit ditentukan) 
        // atau masuk ke konsolidasi (jika id_unit NULL).
        // Untuk unit non-utama, saldo awal biasanya 0 kecuali ada transaksi modal antar unit.
        $saldo_awal_periode = (!$id_unit || $id_unit == $this->getDefaultUnit($tenant_id)) ? (float)$akunInfo['saldo_awal'] : 0;
        
        if ($akunInfo['posisi_saldo_normal'] == 'Debit') {
            $saldo_awal_periode += ((float)$saldoSebelum['total_debit'] - (float)$saldoSebelum['total_kredit']);
        } else {
            $saldo_awal_periode += ((float)$saldoSebelum['total_kredit'] - (float)$saldoSebelum['total_debit']);
        }

        $queryTransaksi = "SELECT ju.tanggal, ju.no_transaksi, ju.deskripsi, jd.debit, jd.kredit
                           FROM jurnal_detail jd
                           JOIN jurnal_umum ju ON jd.id_jurnal = ju.id_jurnal
                           WHERE jd.kode_akun = :kode_akun
                           AND ju.tanggal BETWEEN :tanggal_mulai AND :tanggal_selesai
                           AND ju.tenant_id = :tenant_id";
        if ($id_unit) $queryTransaksi .= " AND ju.id_unit = :id_unit";
        if ($id_program) $queryTransaksi .= " AND ju.id_program = :id_program";
        $queryTransaksi .= " ORDER BY ju.tanggal ASC, ju.id_jurnal ASC";

        $this->db->query($queryTransaksi);
        $this->db->bind('kode_akun', $kode_akun);
        $this->db->bind('tanggal_mulai', $tanggal_mulai);
        $this->db->bind('tanggal_selesai', $tanggal_selesai);
        $this->db->bind('tenant_id', $tenant_id);
        if ($id_unit) $this->db->bind('id_unit', $id_unit);
        if ($id_program) $this->db->bind('id_program', $id_program);
        $transaksi = $this->db->resultSet();

        return [
            'saldo_awal_periode' => $saldo_awal_periode,
            'posisi_saldo_normal' => $akunInfo['posisi_saldo_normal'],
            'transaksi' => $transaksi
        ];
    }

    private function getDefaultUnit($tenant_id) {
        $this->db->query("SELECT id_unit FROM business_units WHERE tenant_id = :tid AND is_default = 1");
        $this->db->bind('tid', $tenant_id);
        $res = $this->db->single();
        return $res ? $res['id_unit'] : null;
    }

    public function getNeracaSaldo($tanggal_selesai, $tenant_id, $id_unit = null)
    {
        $unitFilter = $id_unit ? " AND ju.id_unit = :id_unit " : "";
        
        $sql = "
            SELECT a.kode_akun, a.nama_akun, a.posisi_saldo_normal, a.saldo_awal,
                   COALESCE(trx.total_debit, 0) as total_debit,
                   COALESCE(trx.total_kredit, 0) as total_kredit
            FROM akun a
            LEFT JOIN (
                SELECT jd.kode_akun,
                       SUM(jd.debit) as total_debit,
                       SUM(jd.kredit) as total_kredit
                FROM jurnal_detail jd
                JOIN jurnal_umum ju ON jd.id_jurnal = ju.id_jurnal
                WHERE ju.tenant_id = :tenant_id AND ju.tanggal <= :tanggal_selesai $unitFilter
                GROUP BY jd.kode_akun
            ) trx ON a.kode_akun = trx.kode_akun
            WHERE (a.tipe_akun = 'Detail' OR a.tipe_akun != 'Header') AND a.tenant_id = :tenant_id
            ORDER BY a.kode_akun ASC";
            
        $this->db->query($sql);
        $this->db->bind('tanggal_selesai', $tanggal_selesai);
        $this->db->bind('tenant_id', $tenant_id);
        if ($id_unit) $this->db->bind('id_unit', $id_unit);
        
        $results = $this->db->resultSet();
        $neraca_saldo = [];
        $default_unit_id = $this->getDefaultUnit($tenant_id);

        foreach ($results as $row) {
            // Saldo awal hanya dihitung untuk konsolidasi atau unit utama
            $saldo_awal = (!$id_unit || $id_unit == $default_unit_id) ? (float)$row['saldo_awal'] : 0;
            $saldo_akhir = $saldo_awal;
            
            if ($row['posisi_saldo_normal'] == 'Debit') {
                $saldo_akhir += (float)$row['total_debit'] - (float)$row['total_kredit'];
            } else {
                $saldo_akhir += (float)$row['total_kredit'] - (float)$row['total_debit'];
            }
            $neraca_saldo[] = [
                'kode_akun' => $row['kode_akun'], 'nama_akun' => $row['nama_akun'],
                'debit' => ($row['posisi_saldo_normal'] == 'Debit' && $saldo_akhir >= 0) || ($row['posisi_saldo_normal'] == 'Kredit' && $saldo_akhir < 0) ? abs($saldo_akhir) : 0,
                'kredit' => ($row['posisi_saldo_normal'] == 'Kredit' && $saldo_akhir >= 0) || ($row['posisi_saldo_normal'] == 'Debit' && $saldo_akhir < 0) ? abs($saldo_akhir) : 0
            ];
        }
        return $neraca_saldo;
    }

    public function getLabaRugi($tgl_mulai_1, $tgl_selesai_1, $tgl_mulai_2, $tgl_selesai_2, $tenant_id, $id_unit = null) {
        $unitFilter = $id_unit ? " AND ju.id_unit = :id_unit " : "";
        
        $sql = "
            SELECT 
                a.kode_akun, a.nama_akun, a.posisi_saldo_normal,
                COALESCE(trx1.total_debit, 0) as total_debit_1, COALESCE(trx1.total_kredit, 0) as total_kredit_1,
                COALESCE(trx2.total_debit, 0) as total_debit_2, COALESCE(trx2.total_kredit, 0) as total_kredit_2
            FROM akun a
            LEFT JOIN (
                SELECT jd.kode_akun, SUM(jd.debit) AS total_debit, SUM(jd.kredit) AS total_kredit
                FROM jurnal_detail jd JOIN jurnal_umum ju ON jd.id_jurnal = ju.id_jurnal
                WHERE ju.tanggal BETWEEN :tgl_mulai_1 AND :tgl_selesai_1 AND ju.tenant_id = :tenant_id $unitFilter GROUP BY jd.kode_akun
            ) trx1 ON a.kode_akun = trx1.kode_akun
            LEFT JOIN (
                SELECT jd.kode_akun, SUM(jd.debit) AS total_debit, SUM(jd.kredit) AS total_kredit
                FROM jurnal_detail jd JOIN jurnal_umum ju ON jd.id_jurnal = ju.id_jurnal
                WHERE ju.tanggal BETWEEN :tgl_mulai_2 AND :tgl_selesai_2 AND ju.tenant_id = :tenant_id $unitFilter GROUP BY jd.kode_akun
            ) trx2 ON a.kode_akun = trx2.kode_akun
            WHERE a.tipe_akun = 'Detail' AND a.tenant_id = :tenant_id AND SUBSTR(a.kode_akun, 1, 1) IN ('4', '5', '6', '7', '8')
            ORDER BY a.kode_akun ASC";
        
        $this->db->query($sql);
        $this->db->bind('tgl_mulai_1', $tgl_mulai_1);
        $this->db->bind('tgl_selesai_1', $tgl_selesai_1);
        $this->db->bind('tgl_mulai_2', $tgl_mulai_2 ?? $tgl_mulai_1);
        $this->db->bind('tgl_selesai_2', $tgl_selesai_2 ?? $tgl_selesai_1);
        $this->db->bind('tenant_id', $tenant_id);
        if ($id_unit) $this->db->bind('id_unit', $id_unit);
        
        $results = $this->db->resultSet();

        $laporan = ['pendapatan' => [], 'beban' => [], 'total_pendapatan_1' => 0, 'total_beban_1' => 0, 'total_pendapatan_2' => 0, 'total_beban_2' => 0];
        foreach ($results as $row) {
            $saldo1 = ($row['posisi_saldo_normal'] == 'Kredit') ? ($row['total_kredit_1'] - $row['total_debit_1']) : ($row['total_debit_1'] - $row['total_kredit_1']);
            $saldo2 = ($row['posisi_saldo_normal'] == 'Kredit') ? ($row['total_kredit_2'] - $row['total_debit_2']) : ($row['total_debit_2'] - $row['total_kredit_2']);
            if ($saldo1 != 0 || $saldo2 != 0) {
                $item = ['kode_akun' => $row['kode_akun'], 'nama_akun' => $row['nama_akun'], 'total_1' => (float)$saldo1, 'total_2' => (float)$saldo2];
                if (substr($row['kode_akun'], 0, 1) == '4' || substr($row['kode_akun'], 0, 1) == '8') {
                    $laporan['pendapatan'][] = $item;
                    $laporan['total_pendapatan_1'] += $saldo1;
                    $laporan['total_pendapatan_2'] += $saldo2;
                } else {
                    $laporan['beban'][] = $item;
                    $laporan['total_beban_1'] += $saldo1;
                    $laporan['total_beban_2'] += $saldo2;
                }
            }
        }
        return $laporan;
    }

    public function getPosisiKeuangan($tgl_selesai_1, $tgl_selesai_2, $tenant_id, $id_unit = null)
    {
        $processPeriod = function($tanggal) use ($tenant_id, $id_unit) {
            $neracaLajur = $this->getNeracaLajur($tanggal, $tenant_id, $id_unit);
            $result = [
                'aset' => [], 'kewajiban' => [], 'modal' => [],
                'total_aset' => 0, 'total_kewajiban' => 0, 'total_modal' => 0,
            ];
            if (empty($neracaLajur['data'])) return $result;

            foreach ($neracaLajur['data'] as $akun) {
                if ($akun['neraca_debit'] > 0 || $akun['neraca_kredit'] > 0) {
                    $item = ['kode_akun' => $akun['kode_akun'], 'nama_akun' => $akun['nama_akun'], 'total' => ($akun['neraca_debit'] > 0) ? $akun['neraca_debit'] : $akun['neraca_kredit']];
                    $kode_awal = substr($akun['kode_akun'], 0, 1);
                    if ($kode_awal == '1') { $result['aset'][] = $item; $result['total_aset'] += ($akun['neraca_debit'] - $akun['neraca_kredit']); } 
                    elseif ($kode_awal == '2') { $result['kewajiban'][] = $item; $result['total_kewajiban'] += ($akun['neraca_kredit'] - $akun['neraca_debit']); } 
                    elseif ($kode_awal == '3') { $result['modal'][] = $item; $result['total_modal'] += ($akun['neraca_kredit'] - $akun['neraca_debit']); }
                }
            }
            $labaRugiBersih = $neracaLajur['laba_rugi_bersih'];
            $result['modal'][] = ['kode_akun' => '', 'nama_akun' => 'Laba (Rugi) Periode Berjalan', 'total' => $labaRugiBersih];
            $result['total_modal'] += $labaRugiBersih;
            return $result;
        };

        $laporan_1 = $processPeriod($tgl_selesai_1);
        $laporan_2 = $tgl_selesai_2 ? $processPeriod($tgl_selesai_2) : null;

        return ['periode_1' => $laporan_1, 'periode_2' => $laporan_2];
    }

    public function getPerubahanEkuitas($tgl_mulai_1, $tgl_selesai_1, $tgl_mulai_2, $tgl_selesai_2, $tenant_id) {
        $calculatePeriod = function($start_date, $end_date) use ($tenant_id) {
            $neracaSaldoAwal = $this->getNeracaSaldo(date('Y-m-d', strtotime($start_date . ' -1 day')), $tenant_id);
            $modalAwal = 0;
            foreach ($neracaSaldoAwal as $akun) {
                if (substr($akun['kode_akun'], 0, 1) == '3') {
                    $modalAwal += $akun['kredit'] - $akun['debit'];
                }
            }
            $labaRugiData = $this->getLabaRugi($start_date, $end_date, null, null, $tenant_id);
            $labaRugiPeriodeBerjalan = ($labaRugiData['total_pendapatan_1'] ?? 0) - ($labaRugiData['total_beban_1'] ?? 0);
            
            $queryPerubahanLangsung = "
                SELECT COALESCE(SUM(jd.kredit), 0) - COALESCE(SUM(jd.debit), 0) as net_change
                FROM jurnal_detail jd
                JOIN jurnal_umum ju ON jd.id_jurnal = ju.id_jurnal
                WHERE jd.kode_akun LIKE '3-%' AND ju.tanggal BETWEEN :start_date AND :end_date AND ju.tenant_id = :tenant_id";
            $this->db->query($queryPerubahanLangsung);
            $this->db->bind('start_date', $start_date);
            $this->db->bind('end_date', $end_date);
            $this->db->bind('tenant_id', $tenant_id);
            $netPerubahanModalLangsung = (float)$this->db->single()['net_change'];
            
            $modalAkhir = $modalAwal + $netPerubahanModalLangsung + $labaRugiPeriodeBerjalan;
            return [
                'modal_awal' => $modalAwal,
                'perubahan_modal_langsung' => $netPerubahanModalLangsung,
                'laba_rugi_periode_berjalan' => $labaRugiPeriodeBerjalan,
                'modal_akhir' => $modalAkhir
            ];
        };
        $laporan_1 = $calculatePeriod($tgl_mulai_1, $tgl_selesai_1);
        $laporan_2 = ($tgl_mulai_2 && $tgl_selesai_2) ? $calculatePeriod($tgl_mulai_2, $tgl_selesai_2) : null;
        return ['periode_1' => $laporan_1, 'periode_2' => $laporan_2];
    }

    public function getArusKas($tanggal_mulai, $tanggal_selesai, $metode, $tenant_id, $id_unit = null) {
        $labaRugiData = $this->getLabaRugi($tanggal_mulai, $tanggal_selesai, null, null, $tenant_id, $id_unit);
        $labaBersih = ($labaRugiData['total_pendapatan_1'] ?? 0) - ($labaRugiData['total_beban_1'] ?? 0);
        
        $neracaSaldoAwal = $this->getNeracaSaldo(date('Y-m-d', strtotime($tanggal_mulai . ' -1 day')), $tenant_id, $id_unit);
        $neracaSaldoAkhir = $this->getNeracaSaldo($tanggal_selesai, $tenant_id, $id_unit);
        
        $kasAwal = 0; $kasAkhir = 0;
        foreach ($neracaSaldoAwal as $akun) { if (substr($akun['kode_akun'], 0, 3) == '1-1') $kasAwal += $akun['debit'] - $akun['kredit']; }
        foreach ($neracaSaldoAkhir as $akun) { if (substr($akun['kode_akun'], 0, 3) == '1-1') $kasAkhir += $akun['debit'] - $akun['kredit']; }

        if ($metode === 'direct') {
            // Metode Langsung: Ambil transaksi yang melibatkan Kas & Bank (1-1xxx)
            $this->db->query("
                SELECT a.nama_akun, SUM(jd.debit) as penerimaan, SUM(jd.kredit) as pengeluaran
                FROM jurnal_detail jd
                JOIN jurnal_umum ju ON jd.id_jurnal = ju.id_jurnal
                JOIN akun a ON jd.kode_akun = a.kode_akun AND a.tenant_id = ju.tenant_id
                WHERE ju.tanggal BETWEEN :mulai AND :selesai AND ju.tenant_id = :tenant_id
                " . ($id_unit ? " AND ju.id_unit = :id_unit " : "") . "
                AND jd.id_jurnal IN (
                    SELECT id_jurnal FROM jurnal_detail WHERE kode_akun LIKE '1-1%'
                )
                AND jd.kode_akun NOT LIKE '1-1%'
                GROUP BY jd.kode_akun
            ");
            $this->db->bind('mulai', $tanggal_mulai);
            $this->db->bind('selesai', $tanggal_selesai);
            $this->db->bind('tenant_id', $tenant_id);
            if ($id_unit) $this->db->bind('id_unit', $id_unit);
            $transaksiDirect = $this->db->resultSet();
            
            $arusOperasi = [];
            foreach ($transaksiDirect as $trx) {
                $arusOperasi[] = ['label' => $trx['nama_akun'], 'jumlah' => $trx['penerimaan'] - $trx['pengeluaran']];
            }
            
            return [
                'metode' => 'Direct',
                'kas_awal' => $kasAwal, 'kas_akhir' => $kasAkhir,
                'arus_operasi' => $arusOperasi
            ];
        } else {
            // Metode Tidak Langsung (Indirect)
            $this->db->query("SELECT akun_piutang_default, akun_utang_default FROM perusahaan WHERE tenant_id = :tenant_id");
            $this->db->bind('tenant_id', $tenant_id);
            $pengaturan = $this->db->single();
            $akun_piutang = $pengaturan['akun_piutang_default'] ?? '1-10200';
            $akun_utang = $pengaturan['akun_utang_default'] ?? '2-10100';

            $saldoAkunAwal = []; $saldoAkunAkhir = [];
            foreach ($neracaSaldoAwal as $akun) { $saldoAkunAwal[$akun['kode_akun']] = $akun['debit'] - $akun['kredit']; }
            foreach ($neracaSaldoAkhir as $akun) { $saldoAkunAkhir[$akun['kode_akun']] = $akun['debit'] - $akun['kredit']; }
            
            $perubahanPiutang = ($saldoAkunAkhir[$akun_piutang] ?? 0) - ($saldoAkunAwal[$akun_piutang] ?? 0);
            $perubahanUtang = ($saldoAkunAkhir[$akun_utang] ?? 0) - ($saldoAkunAwal[$akun_utang] ?? 0);

            return [
                'metode' => 'Indirect',
                'kas_awal' => $kasAwal, 'kas_akhir' => $kasAkhir, 'laba_bersih' => $labaBersih,
                'penyesuaian' => [
                    ['label' => 'Kenaikan/Penurunan Piutang Usaha', 'jumlah' => -$perubahanPiutang],
                    ['label' => 'Kenaikan/Penurunan Utang Usaha', 'jumlah' => $perubahanUtang]
                ]
            ];
        }
    }

    public function getNeracaLajur($tanggal_selesai, $tenant_id, $id_unit = null) {
        $neracaSaldo = $this->getNeracaSaldo($tanggal_selesai, $tenant_id, $id_unit);
        $worksheet = [];
        $totals = ['ns_debit' => 0, 'ns_kredit' => 0, 'lr_debit' => 0, 'lr_kredit' => 0, 'neraca_debit' => 0, 'neraca_kredit' => 0];
        foreach ($neracaSaldo as $akun) {
            $row = ['kode_akun' => $akun['kode_akun'], 'nama_akun' => $akun['nama_akun'], 'ns_debit' => $akun['debit'], 'ns_kredit' => $akun['kredit'], 'lr_debit' => 0, 'lr_kredit' => 0, 'neraca_debit' => 0, 'neraca_kredit' => 0];
            $kode_awal = substr($akun['kode_akun'], 0, 1);
            if (in_array($kode_awal, ['4', '5', '6', '7', '8'])) {
                $row['lr_debit'] = $akun['debit'];
                $row['lr_kredit'] = $akun['kredit'];
            } else {
                $row['neraca_debit'] = $akun['debit'];
                $row['neraca_kredit'] = $akun['kredit'];
            }
            $worksheet[] = $row;
            foreach($totals as $key => &$total) { $total += $row[$key]; }
        }
        $labaRugi = $totals['lr_kredit'] - $totals['lr_debit'];
        return ['data' => $worksheet, 'totals' => $totals, 'laba_rugi_bersih' => $labaRugi];
    }
    
    public function getNeracaLajurLengkap($tanggal_mulai, $tanggal_selesai, $tenant_id, $id_unit = null)
    {
        $this->db->query("SELECT kode_akun, nama_akun, saldo_awal, posisi_saldo_normal FROM akun WHERE (tipe_akun = 'Detail' OR tipe_akun != 'Header') AND tenant_id = :tenant_id ORDER BY kode_akun ASC");
        $this->db->bind('tenant_id', $tenant_id);
        $akun_list = $this->db->resultSet();

        $unitFilter = $id_unit ? " AND ju.id_unit = :id_unit " : "";

        $sqlAgg = "
            SELECT 
                jd.kode_akun,
                COALESCE(SUM(CASE WHEN ju.tanggal < :tanggal_mulai THEN (jd.debit - jd.kredit) ELSE 0 END), 0) AS net_sebelum_periode,
                COALESCE(SUM(CASE WHEN ju.tanggal >= :tanggal_mulai AND ju.sumber_jurnal != 'Adjustment' THEN jd.debit ELSE 0 END), 0) AS mutasi_debit,
                COALESCE(SUM(CASE WHEN ju.tanggal >= :tanggal_mulai AND ju.sumber_jurnal != 'Adjustment' THEN jd.kredit ELSE 0 END), 0) AS mutasi_kredit,
                COALESCE(SUM(CASE WHEN ju.tanggal >= :tanggal_mulai AND ju.sumber_jurnal = 'Adjustment' THEN jd.debit ELSE 0 END), 0) AS penyesuaian_debit,
                COALESCE(SUM(CASE WHEN ju.tanggal >= :tanggal_mulai AND ju.sumber_jurnal = 'Adjustment' THEN jd.kredit ELSE 0 END), 0) AS penyesuaian_kredit
            FROM jurnal_detail jd
            JOIN jurnal_umum ju ON jd.id_jurnal = ju.id_jurnal
            WHERE ju.tenant_id = :tenant_id 
              AND ju.tanggal <= :tanggal_selesai
              $unitFilter
            GROUP BY jd.kode_akun
        ";

        $this->db->query($sqlAgg);
        $this->db->bind('tanggal_mulai', $tanggal_mulai);
        $this->db->bind('tanggal_selesai', $tanggal_selesai);
        $this->db->bind('tenant_id', $tenant_id);
        if ($id_unit) $this->db->bind('id_unit', $id_unit);
        $aggResults = $this->db->resultSet();

        $trxMap = [];
        foreach ($aggResults as $agg) {
            $trxMap[$agg['kode_akun']] = $agg;
        }

        $default_unit_id = $this->getDefaultUnit($tenant_id);
        $is_main_or_cons = (!$id_unit || $id_unit == $default_unit_id);

        $worksheet_data = [];
        foreach ($akun_list as $akun) {
            $kode = $akun['kode_akun'];
            $trx = $trxMap[$kode] ?? null;

            $net_sebelum = $trx ? (float)$trx['net_sebelum_periode'] : 0.0;
            $mutasi_debit = $trx ? (float)$trx['mutasi_debit'] : 0.0;
            $mutasi_kredit = $trx ? (float)$trx['mutasi_kredit'] : 0.0;
            $penyesuaian_debit = $trx ? (float)$trx['penyesuaian_debit'] : 0.0;
            $penyesuaian_kredit = $trx ? (float)$trx['penyesuaian_kredit'] : 0.0;

            $row = [
                'kode_akun' => $kode,
                'nama_akun' => $akun['nama_akun'],
                'sa_debit' => 0, 'sa_kredit' => 0,
                'mutasi_debit' => $mutasi_debit,
                'mutasi_kredit' => $mutasi_kredit,
                'ns_debit' => 0, 'ns_kredit' => 0,
                'penyesuaian_debit' => $penyesuaian_debit,
                'penyesuaian_kredit' => $penyesuaian_kredit,
                'nsd_debit' => 0, 'nsd_kredit' => 0,
                'lr_debit' => 0, 'lr_kredit' => 0,
                'poskeu_debit' => 0, 'poskeu_kredit' => 0,
            ];

            // Saldo awal hanya dihitung untuk unit utama atau konsolidasi
            $saldo_sebelum_periode = ($is_main_or_cons) ? (($akun['posisi_saldo_normal'] == 'Debit') ? (float)$akun['saldo_awal'] : -(float)$akun['saldo_awal']) : 0;
            $saldo_sebelum_periode += $net_sebelum;

            if ($saldo_sebelum_periode >= 0) {
                $row['sa_debit'] = $saldo_sebelum_periode;
            } else {
                $row['sa_kredit'] = abs($saldo_sebelum_periode);
            }

            $saldo_ns = $saldo_sebelum_periode + $row['mutasi_debit'] - $row['mutasi_kredit'];
            if ($saldo_ns >= 0) {
                $row['ns_debit'] = $saldo_ns;
            } else {
                $row['ns_kredit'] = abs($saldo_ns);
            }

            $saldo_nsd = $saldo_ns + $row['penyesuaian_debit'] - $row['penyesuaian_kredit'];
            if ($saldo_nsd >= 0) {
                $row['nsd_debit'] = $saldo_nsd;
            } else {
                $row['nsd_kredit'] = abs($saldo_nsd);
            }
            
            $kode_awal = substr($kode, 0, 1);
            if (in_array($kode_awal, ['4', '5', '6', '7', '8'])) {
                $row['lr_debit'] = $row['nsd_debit'];
                $row['lr_kredit'] = $row['nsd_kredit'];
            } else {
                $row['poskeu_debit'] = $row['nsd_debit'];
                $row['poskeu_kredit'] = $row['nsd_kredit'];
            }
            
            $worksheet_data[] = $row;
        }
        return $worksheet_data;
    }
}
