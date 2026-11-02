<?php

class Jurnal_model {
    private $db;
    private static $checked = false;

    public function __construct($db)
    {
        $this->db = $db;
        $this->ensureSchema();
    }

    private function ensureSchema()
    {
        if (self::$checked) return;
        self::$checked = true;

        try {
            $this->db->query("SHOW COLUMNS FROM jurnal_umum LIKE 'id_program'");
            if (!$this->db->single()) {
                $this->db->query("ALTER TABLE jurnal_umum ADD COLUMN id_program INT DEFAULT NULL AFTER sumber_jurnal");
                $this->db->execute();
            }
        } catch (Throwable $e) {}

        try {
            $this->db->query("SHOW COLUMNS FROM jurnal_umum LIKE 'id_unit'");
            if (!$this->db->single()) {
                $this->db->query("ALTER TABLE jurnal_umum ADD COLUMN id_unit INT DEFAULT NULL AFTER id_program");
                $this->db->execute();
            }
        } catch (Throwable $e) {}
    }

    public function getAllJurnal($tenant_id, $id_unit = null)
    {
        if (empty($tenant_id)) return [];

        try {
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
            
            $res = $this->db->resultSet();
            return is_array($res) ? $res : [];
        } catch (Throwable $e) {
            // Fallback jika id_program / id_unit belum ada pada tabel jurnal_umum
            try {
                $fallback = "SELECT 
                            ju.id_jurnal, ju.no_transaksi, ju.tanggal, ju.deskripsi, ju.sumber_jurnal, ju.is_locked,
                            COALESCE(SUM(jd.debit), 0) as total
                          FROM jurnal_umum ju
                          LEFT JOIN jurnal_detail jd ON ju.id_jurnal = jd.id_jurnal
                          WHERE ju.tenant_id = :tenant_id
                          GROUP BY ju.id_jurnal ORDER BY ju.tanggal DESC, ju.no_transaksi DESC";
                $this->db->query($fallback);
                $this->db->bind('tenant_id', $tenant_id);
                $res = $this->db->resultSet();
                return is_array($res) ? $res : [];
            } catch (Throwable $e2) {
                return [];
            }
        }
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
            // Validasi proteksi saldo kas: cegah pengeluaran kas melebihi saldo yang ada (saldo kas tidak boleh minus)
            $this->validateSaldoKas($data['details'] ?? [], $tenant_id);

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

        } catch (\Throwable $e) {
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
            // Validasi proteksi saldo kas saat update
            $this->validateSaldoKas($data['details'] ?? [], $tenant_id, $data['id_jurnal']);

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
        } catch (\Throwable $e) {
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
        $this->validateHapusJurnalSaldoKas($id, $tenant_id);
        $query = "DELETE FROM jurnal_umum WHERE id_jurnal = :id_jurnal AND tenant_id = :tenant_id";
        $this->db->query($query);
        $this->db->bind('id_jurnal', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    /**
     * Memeriksa apakah transaksi akan mengakibatkan saldo akun Kas & Setara Kas menjadi negatif (minus).
     * Sesuai prinsip akuntansi, saldo Kas dan Setara Kas tidak boleh negatif (kredit).
     *
     * @param array $details Array detail jurnal [['kode_akun' => ..., 'debit' => ..., 'kredit' => ...], ...]
     * @param int $tenant_id
     * @param int|null $exclude_jurnal_id ID jurnal yang dikecualikan (digunakan saat edit jurnal)
     * @throws Exception jika pengeluaran melebihi saldo kas yang tersedia
     */
    public function validateSaldoKas($details, $tenant_id, $exclude_jurnal_id = null)
    {
        if (empty($details) || !is_array($details)) return;

        // Kelompokkan mutasi bersih per kode_akun dalam transaksi ini
        $mutasiAkun = [];
        foreach ($details as $d) {
            $kode = trim($d['kode_akun'] ?? '');
            if (empty($kode)) continue;
            $debit = (float)($d['debit'] ?? 0);
            $kredit = (float)($d['kredit'] ?? 0);
            if (!isset($mutasiAkun[$kode])) {
                $mutasiAkun[$kode] = ['debit' => 0.0, 'kredit' => 0.0];
            }
            $mutasiAkun[$kode]['debit'] += $debit;
            $mutasiAkun[$kode]['kredit'] += $kredit;
        }

        foreach ($mutasiAkun as $kode => $trx) {
            // Hanya perlu divalidasi jika ada pengeluaran bersih (kredit > debit)
            $netKeluar = $trx['kredit'] - $trx['debit'];
            if ($netKeluar <= 0.0001) continue;

            // Cek apakah akun ini adalah akun Kas & Setara Kas (Aset kelompok 1 dengan kode 1-11% atau nama mengandung Kas/Bank)
            $this->db->query("SELECT kode_akun, nama_akun, tipe_akun, saldo_awal, posisi_saldo_normal 
                              FROM akun 
                              WHERE kode_akun = :kode 
                                AND tenant_id = :tenant_id 
                                AND tipe_akun != 'Header' 
                                AND SUBSTR(kode_akun, 1, 1) = '1'
                                AND (kode_akun LIKE '1-11%' OR nama_akun LIKE '%Kas%' OR nama_akun LIKE '%Bank%')");
            $this->db->bind('kode', $kode);
            $this->db->bind('tenant_id', $tenant_id);
            $akun = $this->db->single();

            if ($akun) {
                // Hitung saldo riil akun saat ini di database
                $sqlMutasi = "SELECT COALESCE(SUM(jd.debit), 0) - COALESCE(SUM(jd.kredit), 0) AS mutasi_db 
                              FROM jurnal_detail jd 
                              JOIN jurnal_umum ju ON jd.id_jurnal = ju.id_jurnal 
                              WHERE jd.kode_akun = :kode AND ju.tenant_id = :tenant_id";
                if ($exclude_jurnal_id) {
                    $sqlMutasi .= " AND ju.id_jurnal != :exclude_id";
                }
                $this->db->query($sqlMutasi);
                $this->db->bind('kode', $kode);
                $this->db->bind('tenant_id', $tenant_id);
                if ($exclude_jurnal_id) {
                    $this->db->bind('exclude_id', $exclude_jurnal_id);
                }
                $resMutasi = $this->db->single();
                $mutasiDb = (float)($resMutasi['mutasi_db'] ?? 0);

                $saldoAwal = (float)($akun['saldo_awal'] ?? 0);
                $saldoSaatIni = $saldoAwal + $mutasiDb;

                // Jika saldo setelah transaksi ini menjadi negatif
                $saldoProyeksi = $saldoSaatIni - $netKeluar;
                if ($saldoProyeksi < -0.0001) {
                    $namaAkun = $akun['nama_akun'];
                    $saldoStr = number_format($saldoSaatIni, 2, ',', '.');
                    $keluarStr = number_format($netKeluar, 2, ',', '.');
                    throw new Exception("Proteksi Saldo Kas: Transaksi ditolak! Saldo akun '{$namaAkun}' ({$kode}) tidak mencukupi (Saldo saat ini: Rp {$saldoStr}, Pengeluaran yang diminta: Rp {$keluarStr}). Saldo Kas dan Setara Kas tidak boleh negatif/minus. Silakan gunakan akun Hutang atau lakukan transaksi penerimaan/pengisian kas terlebih dahulu.");
                }
            }
        }
    }

    /**
     * Memeriksa apakah penghapusan jurnal akan mengakibatkan saldo akun Kas & Setara Kas menjadi minus.
     */
    public function validateHapusJurnalSaldoKas($id_jurnal, $tenant_id)
    {
        $this->db->query("SELECT jd.kode_akun, jd.debit, jd.kredit 
                          FROM jurnal_detail jd 
                          JOIN jurnal_umum ju ON jd.id_jurnal = ju.id_jurnal 
                          WHERE ju.id_jurnal = :id AND ju.tenant_id = :tenant_id");
        $this->db->bind('id', $id_jurnal);
        $this->db->bind('tenant_id', $tenant_id);
        $details = $this->db->resultSet();
        if (empty($details)) return;

        foreach ($details as $d) {
            $netMasuk = (float)$d['debit'] - (float)$d['kredit'];
            if ($netMasuk <= 0.0001) continue;

            $kode = $d['kode_akun'];
            $this->db->query("SELECT kode_akun, nama_akun, saldo_awal 
                              FROM akun 
                              WHERE kode_akun = :kode 
                                AND tenant_id = :tenant_id 
                                AND tipe_akun != 'Header' 
                                AND SUBSTR(kode_akun, 1, 1) = '1'
                                AND (kode_akun LIKE '1-11%' OR nama_akun LIKE '%Kas%' OR nama_akun LIKE '%Bank%')");
            $this->db->bind('kode', $kode);
            $this->db->bind('tenant_id', $tenant_id);
            $akun = $this->db->single();

            if ($akun) {
                $this->db->query("SELECT COALESCE(SUM(jd.debit), 0) - COALESCE(SUM(jd.kredit), 0) AS mutasi_db 
                                  FROM jurnal_detail jd 
                                  JOIN jurnal_umum ju ON jd.id_jurnal = ju.id_jurnal 
                                  WHERE jd.kode_akun = :kode AND ju.tenant_id = :tenant_id");
                $this->db->bind('kode', $kode);
                $this->db->bind('tenant_id', $tenant_id);
                $resMutasi = $this->db->single();
                $mutasiDb = (float)($resMutasi['mutasi_db'] ?? 0);
                $saldoSaatIni = (float)($akun['saldo_awal'] ?? 0) + $mutasiDb;

                if (($saldoSaatIni - $netMasuk) < -0.0001) {
                    $saldoStr = number_format($saldoSaatIni, 2, ',', '.');
                    throw new Exception("Proteksi Saldo Kas: Jurnal tidak dapat dihapus! Penghapusan penerimaan ini akan menyebabkan saldo Kas/Bank '{$akun['nama_akun']}' ({$kode}) menjadi negatif (Saldo saat ini: Rp {$saldoStr}).");
                }
            }
        }
    }

    public function getJurnalWithDetailsById($id, $tenant_id = null) {
        $sql = "SELECT ju.*, jd.kode_akun, jd.debit, jd.kredit, a.nama_akun
                FROM jurnal_umum ju
                LEFT JOIN jurnal_detail jd ON ju.id_jurnal = jd.id_jurnal
                LEFT JOIN akun a ON jd.kode_akun = a.kode_akun AND a.tenant_id = ju.tenant_id
                WHERE ju.id_jurnal = :id_jurnal";
        if (!empty($tenant_id)) {
            $sql .= " AND ju.tenant_id = :tenant_id";
        }
        $this->db->query($sql);
        $this->db->bind('id_jurnal', $id);
        if (!empty($tenant_id)) {
            $this->db->bind('tenant_id', $tenant_id);
        }
        $results = $this->db->resultSet();
        if(empty($results)){ return null; }
        $jurnal = [
            'id_jurnal' => $results[0]['id_jurnal'],
            'tenant_id' => $results[0]['tenant_id'] ?? null,
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
        if (empty($tenant_id)) return null;
        try {
            $this->db->query("SELECT id_unit FROM business_units WHERE tenant_id = :tid AND is_default = 1");
            $this->db->bind('tid', $tenant_id);
            $res = $this->db->single();
            return $res ? $res['id_unit'] : null;
        } catch (Throwable $e) {
            return null;
        }
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
                    $kode_awal = substr($akun['kode_akun'], 0, 1);
                    if ($kode_awal == '1') {
                        $total = (float)$akun['neraca_debit'] - (float)$akun['neraca_kredit'];
                        $result['aset'][] = [
                            'kode_akun' => $akun['kode_akun'],
                            'nama_akun' => $akun['nama_akun'],
                            'total' => $total
                        ];
                        $result['total_aset'] += $total;
                    } elseif ($kode_awal == '2') {
                        $total = (float)$akun['neraca_kredit'] - (float)$akun['neraca_debit'];
                        $result['kewajiban'][] = [
                            'kode_akun' => $akun['kode_akun'],
                            'nama_akun' => $akun['nama_akun'],
                            'total' => $total
                        ];
                        $result['total_kewajiban'] += $total;
                    } elseif ($kode_awal == '3') {
                        $total = (float)$akun['neraca_kredit'] - (float)$akun['neraca_debit'];
                        $result['modal'][] = [
                            'kode_akun' => $akun['kode_akun'],
                            'nama_akun' => $akun['nama_akun'],
                            'total' => $total
                        ];
                        $result['total_modal'] += $total;
                    }
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
        
        // Identifikasi semua akun Kas & Bank secara akurat
        $this->db->query("SELECT kode_akun, nama_akun FROM akun 
                          WHERE tenant_id = :tenant_id 
                            AND (tipe_akun = 'Kas & Bank' 
                                 OR kode_akun LIKE '1-100%' 
                                 OR kode_akun LIKE '1-11%' 
                                 OR LOWER(nama_akun) LIKE '%kas%' 
                                 OR LOWER(nama_akun) LIKE '%bank%') 
                            AND tipe_akun != 'Header'");
        $this->db->bind('tenant_id', $tenant_id);
        $kasRows = $this->db->resultSet();
        $kasAkunList = !empty($kasRows) ? array_column($kasRows, 'kode_akun') : ['1-10001'];

        $kasAwal = 0; 
        $kasAkhir = 0;
        foreach ($neracaSaldoAwal as $akun) { 
            if (in_array($akun['kode_akun'], $kasAkunList)) {
                $kasAwal += ((float)$akun['debit'] - (float)$akun['kredit']); 
            }
        }
        foreach ($neracaSaldoAkhir as $akun) { 
            if (in_array($akun['kode_akun'], $kasAkunList)) {
                $kasAkhir += ((float)$akun['debit'] - (float)$akun['kredit']); 
            }
        }

        if ($metode === 'direct') {
            // METODE LANGSUNG (DIRECT)
            $kasInClause = "'" . implode("','", array_map('addslashes', $kasAkunList)) . "'";
            
            $this->db->query("
                SELECT a.kode_akun, a.nama_akun, 
                       SUM(jd.debit) as total_debit, 
                       SUM(jd.kredit) as total_kredit
                FROM jurnal_detail jd
                JOIN jurnal_umum ju ON jd.id_jurnal = ju.id_jurnal
                JOIN akun a ON jd.kode_akun = a.kode_akun AND a.tenant_id = ju.tenant_id
                WHERE ju.tanggal BETWEEN :mulai AND :selesai 
                  AND ju.tenant_id = :tenant_id
                  " . ($id_unit ? " AND ju.id_unit = :id_unit " : "") . "
                  AND jd.id_jurnal IN (
                      SELECT DISTINCT id_jurnal FROM jurnal_detail WHERE kode_akun IN ($kasInClause)
                  )
                  AND jd.kode_akun NOT IN ($kasInClause)
                GROUP BY jd.kode_akun, a.nama_akun
                ORDER BY a.kode_akun ASC
            ");
            $this->db->bind('mulai', $tanggal_mulai);
            $this->db->bind('selesai', $tanggal_selesai);
            $this->db->bind('tenant_id', $tenant_id);
            if ($id_unit) $this->db->bind('id_unit', $id_unit);
            $transaksiDirect = $this->db->resultSet();
            
            $arusOperasi = [];
            $arusInvestasi = [];
            $arusPendanaan = [];
            $totalOperasi = 0;
            $totalInvestasi = 0;
            $totalPendanaan = 0;

            foreach ($transaksiDirect as $trx) {
                // Catatan Akuntansi pada akun lawan kas:
                // Kredit pada akun lawan = Kas didebit (Penerimaan Kas / Kas Masuk, tanda +)
                // Debit pada akun lawan = Kas dikredit (Pengeluaran Kas / Kas Keluar, tanda -)
                $netKas = (float)$trx['total_kredit'] - (float)$trx['total_debit'];
                if ($netKas == 0) continue;

                $kode = $trx['kode_akun'];
                $prefix1 = substr($kode, 0, 1);
                $prefix2 = substr($kode, 0, 3);
                $isAkumulasi = (stripos($trx['nama_akun'], 'akum') !== false || stripos($trx['nama_akun'], 'penyusutan') !== false);

                if ($prefix2 == '1-2' && !$isAkumulasi) {
                    // Aktivitas Investasi: Pembelian / Pelepasan Aset Tetap
                    $arusInvestasi[] = [
                        'kode_akun' => $kode,
                        'label' => $trx['nama_akun'],
                        'jumlah' => $netKas
                    ];
                    $totalInvestasi += $netKas;
                } elseif ($prefix1 == '3' || $prefix2 == '2-2') {
                    // Aktivitas Pendanaan: Modal, Prive, Utang Bank Jangka Panjang
                    $arusPendanaan[] = [
                        'kode_akun' => $kode,
                        'label' => $trx['nama_akun'],
                        'jumlah' => $netKas
                    ];
                    $totalPendanaan += $netKas;
                } else {
                    // Aktivitas Operasi: Penerimaan Penjualan/Piutang, Pembayaran Beban/Persediaan/Utang Usaha
                    $arusOperasi[] = [
                        'kode_akun' => $kode,
                        'label' => $trx['nama_akun'],
                        'jumlah' => $netKas
                    ];
                    $totalOperasi += $netKas;
                }
            }

            $kenaikanKasBersih = $totalOperasi + $totalInvestasi + $totalPendanaan;

            return [
                'metode' => 'Direct',
                'kas_awal' => $kasAwal,
                'kas_akhir' => $kasAkhir,
                'kenaikan_kas_bersih' => $kenaikanKasBersih,
                'laba_bersih' => $labaBersih,
                'penyesuaian' => $arusOperasi,
                'arus_operasi' => $arusOperasi,
                'total_operasi' => $totalOperasi,
                'arus_investasi' => $arusInvestasi,
                'total_investasi' => $totalInvestasi,
                'arus_pendanaan' => $arusPendanaan,
                'total_pendanaan' => $totalPendanaan
            ];
        } else {
            // METODE TIDAK LANGSUNG (INDIRECT)
            $saldoAwalMap = [];
            $saldoAkhirMap = [];
            $namaAkunMap = [];

            foreach ($neracaSaldoAwal as $akun) {
                $saldoAwalMap[$akun['kode_akun']] = [
                    'debit' => (float)$akun['debit'],
                    'kredit' => (float)$akun['kredit'],
                    'net_debit' => (float)$akun['debit'] - (float)$akun['kredit'],
                    'net_kredit' => (float)$akun['kredit'] - (float)$akun['debit']
                ];
                $namaAkunMap[$akun['kode_akun']] = $akun['nama_akun'];
            }

            foreach ($neracaSaldoAkhir as $akun) {
                $saldoAkhirMap[$akun['kode_akun']] = [
                    'debit' => (float)$akun['debit'],
                    'kredit' => (float)$akun['kredit'],
                    'net_debit' => (float)$akun['debit'] - (float)$akun['kredit'],
                    'net_kredit' => (float)$akun['kredit'] - (float)$akun['debit']
                ];
                $namaAkunMap[$akun['kode_akun']] = $akun['nama_akun'];
            }

            $allKodes = array_unique(array_merge(array_keys($saldoAwalMap), array_keys($saldoAkhirMap)));
            sort($allKodes);

            $penyesuaianOperasi = [];
            $arusInvestasi = [];
            $arusPendanaan = [];

            // 1. Beban Non-Kas (Penyusutan / Amortisasi dari Laba Rugi)
            $bebanPenyusutan = 0;
            if (!empty($labaRugiData['beban'])) {
                foreach ($labaRugiData['beban'] as $b) {
                    if (stripos($b['nama_akun'], 'penyusutan') !== false || stripos($b['nama_akun'], 'depresiasi') !== false || stripos($b['nama_akun'], 'amortisasi') !== false) {
                        $bebanPenyusutan += (float)$b['total_1'];
                    }
                }
            }
            if ($bebanPenyusutan != 0) {
                $penyesuaianOperasi[] = [
                    'label' => 'Beban Penyusutan & Amortisasi',
                    'jumlah' => $bebanPenyusutan
                ];
            }

            // 2. Evaluasi Perubahan Neraca
            foreach ($allKodes as $kode) {
                // Abaikan akun Kas & Bank
                if (in_array($kode, $kasAkunList)) continue;

                $nama = $namaAkunMap[$kode] ?? $kode;
                $prefix1 = substr($kode, 0, 1);
                $prefix2 = substr($kode, 0, 3);
                $isAkumulasi = (stripos($nama, 'akum') !== false || stripos($nama, 'penyusutan') !== false);

                $awalDebit = $saldoAwalMap[$kode]['net_debit'] ?? 0;
                $akhirDebit = $saldoAkhirMap[$kode]['net_debit'] ?? 0;
                $deltaDebit = $akhirDebit - $awalDebit; // Positif jika saldo aset bertambah

                $awalKredit = $saldoAwalMap[$kode]['net_kredit'] ?? 0;
                $akhirKredit = $saldoAkhirMap[$kode]['net_kredit'] ?? 0;
                $deltaKredit = $akhirKredit - $awalKredit; // Positif jika saldo kewajiban/modal bertambah

                // A. ASET
                if ($prefix1 == '1') {
                    if ($prefix2 == '1-2') {
                        // Aset Tetap
                        if ($isAkumulasi) {
                            // Akumulasi penyusutan sudah diakomodasi via addback Beban Penyusutan di Operasi
                            continue;
                        } else {
                            // Aset Tetap fisik (Investasi):
                            // Kenaikan aset tetap (deltaDebit > 0) = Pengeluaran kas / Pembelian (-)
                            // Penurunan aset tetap (deltaDebit < 0) = Penerimaan kas / Penjualan (+)
                            if ($deltaDebit != 0) {
                                $arusInvestasi[] = [
                                    'kode_akun' => $kode,
                                    'label' => ($deltaDebit > 0 ? 'Perolehan / Pembelian ' : 'Pelepasan / Penjualan ') . $nama,
                                    'jumlah' => -$deltaDebit
                                ];
                            }
                        }
                    } else {
                        // Aset Lancar Operasi (Piutang, Persediaan, Perlengkapan, dll.)
                        // Kenaikan aset lancar (deltaDebit > 0) = Arus kas berkurang (-)
                        // Penurunan aset lancar (deltaDebit < 0) = Arus kas bertambah (+)
                        if ($deltaDebit != 0) {
                            $penyesuaianOperasi[] = [
                                'kode_akun' => $kode,
                                'label' => ($deltaDebit > 0 ? 'Kenaikan ' : 'Penurunan ') . $nama,
                                'jumlah' => -$deltaDebit
                            ];
                        }
                    }
                }
                // B. KEWAJIBAN
                elseif ($prefix1 == '2') {
                    if ($prefix2 == '2-2') {
                        // Kewajiban Jangka Panjang (Pendanaan):
                        // Kenaikan pinjaman (deltaKredit > 0) = Kas Masuk (+)
                        // Pembayaran pinjaman (deltaKredit < 0) = Kas Keluar (-)
                        if ($deltaKredit != 0) {
                            $arusPendanaan[] = [
                                'kode_akun' => $kode,
                                'label' => ($deltaKredit > 0 ? 'Penerimaan ' : 'Pembayaran ') . $nama,
                                'jumlah' => $deltaKredit
                            ];
                        }
                    } else {
                        // Kewajiban Lancar Operasi (Utang Usaha, Utang Gaji, Utang Pajak, dll.)
                        // Kenaikan utang lancar (deltaKredit > 0) = Arus kas bertambah (+)
                        // Penurunan utang lancar (deltaKredit < 0) = Arus kas berkurang (-)
                        if ($deltaKredit != 0) {
                            $penyesuaianOperasi[] = [
                                'kode_akun' => $kode,
                                'label' => ($deltaKredit > 0 ? 'Kenaikan ' : 'Penurunan ') . $nama,
                                'jumlah' => $deltaKredit
                            ];
                        }
                    }
                }
                // C. EKUITAS
                elseif ($prefix1 == '3') {
                    // Abaikan akun Laba Ditahan / Laba Berjalan / Ikhtisar Laba Rugi karena laba bersih sudah ada di Operasi
                    if (stripos($nama, 'laba') !== false || stripos($nama, 'rugi') !== false || substr($kode, 0, 3) == '3-3') {
                        continue;
                    }

                    // Prive Pemilik (posisi normal Debit)
                    if (stripos($nama, 'prive') !== false || substr($kode, 0, 3) == '3-2') {
                        if ($deltaDebit != 0) {
                            // Penarikan prive = Arus kas keluar (-)
                            $arusPendanaan[] = [
                                'kode_akun' => $kode,
                                'label' => 'Penarikan ' . $nama,
                                'jumlah' => -$deltaDebit
                            ];
                        }
                    } else {
                        // Modal Pemilik / Modal Disetor (posisi normal Kredit)
                        // Kenaikan modal (deltaKredit > 0) = Setoran modal / Kas Masuk (+)
                        // Penurunan modal (deltaKredit < 0) = Pengurangan modal / Kas Keluar (-)
                        if ($deltaKredit != 0) {
                            $arusPendanaan[] = [
                                'kode_akun' => $kode,
                                'label' => ($deltaKredit > 0 ? 'Setoran ' : 'Pengurangan ') . $nama,
                                'jumlah' => $deltaKredit
                            ];
                        }
                    }
                }
            }

            $totalOperasi = $labaBersih;
            foreach ($penyesuaianOperasi as $p) {
                $totalOperasi += (float)$p['jumlah'];
            }

            $totalInvestasi = 0;
            foreach ($arusInvestasi as $i) {
                $totalInvestasi += (float)$i['jumlah'];
            }

            $totalPendanaan = 0;
            foreach ($arusPendanaan as $d) {
                $totalPendanaan += (float)$d['jumlah'];
            }

            $kenaikanKasBersih = $totalOperasi + $totalInvestasi + $totalPendanaan;

            return [
                'metode' => 'Indirect',
                'kas_awal' => $kasAwal,
                'kas_akhir' => $kasAkhir,
                'kenaikan_kas_bersih' => $kenaikanKasBersih,
                'laba_bersih' => $labaBersih,
                'penyesuaian' => $penyesuaianOperasi,
                'arus_operasi' => $penyesuaianOperasi,
                'total_operasi' => $totalOperasi,
                'arus_investasi' => $arusInvestasi,
                'total_investasi' => $totalInvestasi,
                'arus_pendanaan' => $arusPendanaan,
                'total_pendanaan' => $totalPendanaan
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
