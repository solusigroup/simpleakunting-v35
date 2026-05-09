<?php

require_once 'Jurnal_model.php';

class Pembayaran_model {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllPembayaran($tenant_id) {
        $this->db->query("SELECT pp.*, p.nama_pemasok 
                         FROM pembayaran_pemasok pp
                         JOIN pemasok p ON pp.id_pemasok = p.id_pemasok AND p.tenant_id = pp.tenant_id
                         WHERE pp.tenant_id = :tenant_id
                         ORDER BY pp.tanggal DESC");
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->resultSet();
    }
    
    public function getFakturBelumLunasByPemasok($id_pemasok, $tenant_id) {
        // 1. Ambil semua faktur pembelian nyata yang belum lunas
        $this->db->query("SELECT id_pembelian, no_faktur_pembelian, tanggal_faktur, sisa_tagihan 
                         FROM pembelian 
                         WHERE id_pemasok = :id AND status_pembayaran != 'Lunas' AND tenant_id = :tenant_id
                         ORDER BY tanggal_faktur ASC");
        $this->db->bind('id', $id_pemasok);
        $this->db->bind('tenant_id', $tenant_id);
        $fakturNyata = $this->db->resultSet();

        // 2. Ambil saldo terkini pemasok
        $this->db->query("SELECT saldo_terkini_hutang FROM pemasok WHERE id_pemasok = :id AND tenant_id = :tenant_id");
        $this->db->bind('id', $id_pemasok);
        $this->db->bind('tenant_id', $tenant_id);
        $pemasok = $this->db->single();
        $saldoTerkini = (float)($pemasok['saldo_terkini_hutang'] ?? 0);

        // 3. Hitung total sisa tagihan dari faktur nyata
        $totalSisaFaktur = 0;
        foreach ($fakturNyata as $faktur) {
            $totalSisaFaktur += (float)$faktur['sisa_tagihan'];
        }

        // 4. Sisa Saldo Awal adalah selisih antara saldo terkini dan total sisa faktur
        $sisaSaldoAwal = $saldoTerkini - $totalSisaFaktur;

        // 5. Jika ada sisa Saldo Awal, buat "faktur virtual"
        if ($sisaSaldoAwal > 0.01) {
            $fakturSaldoAwal = [
                'id_pembelian' => 'SA-' . $id_pemasok,
                'no_faktur_pembelian' => 'SALDO AWAL',
                'tanggal_faktur' => 'N/A',
                'sisa_tagihan' => $sisaSaldoAwal
            ];
            array_unshift($fakturNyata, $fakturSaldoAwal);
        }

        return $fakturNyata;
    }

    public function simpanPembayaran($data, $tenant_id) {
        $jurnalModel = new Jurnal_model($this->db);
        
        $this->db->beginTransaction();
        try {
            $totalDibayar = array_sum($data['details']['jumlah_bayar']);
            $this->db->query("SELECT akun_utang_default FROM perusahaan WHERE tenant_id = :tenant_id");
            $this->db->bind('tenant_id', $tenant_id);
            $perusahaan = $this->db->single();
            $akun_utang_usaha = $perusahaan['akun_utang_default'] ?? null;
            if (empty($akun_utang_usaha)) throw new Exception("Akun Utang Usaha default belum diatur.");

            $jurnalData = [
                'no_transaksi' => $data['no_bukti'], 'tanggal' => $data['tanggal'],
                'deskripsi' => 'Pembayaran kepada ' . $data['nama_pemasok'], 'sumber_jurnal' => 'Pembelian',
                'details' => [
                    ['kode_akun' => $akun_utang_usaha, 'debit' => $totalDibayar, 'kredit' => 0],
                    ['kode_akun' => $data['akun_kas_bank'], 'debit' => 0, 'kredit' => $totalDibayar]
                ]
            ];
            $id_jurnal = $jurnalModel->simpanJurnal($jurnalData, $tenant_id);
            if ($id_jurnal == 0) throw new Exception("Gagal menyimpan jurnal pembayaran.");
            
            $this->db->query("UPDATE jurnal_umum SET is_locked = 1 WHERE id_jurnal = :id AND tenant_id = :tenant_id");
            $this->db->bind('id', $id_jurnal);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->execute();

            $queryHeader = "INSERT INTO pembayaran_pemasok (tenant_id, id_pemasok, id_jurnal, no_bukti, tanggal, akun_kas_bank, total_dibayar, keterangan) VALUES (:tenant_id, :id_pemasok, :id_jurnal, :no_bukti, :tanggal, :akun_kas, :total, :ket)";
            $this->db->query($queryHeader);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('id_pemasok', $data['id_pemasok']);
            $this->db->bind('id_jurnal', $id_jurnal);
            $this->db->bind('no_bukti', $data['no_bukti']);
            $this->db->bind('tanggal', $data['tanggal']);
            $this->db->bind('akun_kas', $data['akun_kas_bank']);
            $this->db->bind('total', $totalDibayar);
            $this->db->bind('ket', $data['keterangan']);
            $this->db->execute();
            $id_pembayaran = $this->db->lastInsertId();

            foreach ($data['details']['id_pembelian'] as $index => $id_pembelian) {
                $jumlah_bayar = (float)$data['details']['jumlah_bayar'][$index];
                if ($jumlah_bayar > 0) {
                    if (strpos($id_pembelian, 'SA-') !== 0) {
                        $queryDetail = "INSERT INTO pembayaran_pemasok_detail (id_pembayaran, id_pembelian, jumlah_bayar) VALUES (:id_pembayaran, :id_pembelian, :jumlah)";
                        $this->db->query($queryDetail);
                        $this->db->bind('id_pembayaran', $id_pembayaran);
                        $this->db->bind('id_pembelian', $id_pembelian);
                        $this->db->bind('jumlah', $jumlah_bayar);
                        $this->db->execute();

                        $queryUpdateFaktur = "UPDATE pembelian SET sisa_tagihan = sisa_tagihan - :jumlah, status_pembayaran = IF(sisa_tagihan <= 0.01, 'Lunas', 'Lunas Sebagian') WHERE id_pembelian = :id_pembelian AND tenant_id = :tenant_id";
                        $this->db->query($queryUpdateFaktur);
                        $this->db->bind('jumlah', $jumlah_bayar);
                        $this->db->bind('id_pembelian', $id_pembelian);
                        $this->db->bind('tenant_id', $tenant_id);
                        $this->db->execute();
                    }
                }
            }

            $this->db->query("UPDATE pemasok SET saldo_terkini_hutang = saldo_terkini_hutang - :total WHERE id_pemasok = :id AND tenant_id = :tenant_id");
            $this->db->bind('total', $totalDibayar);
            $this->db->bind('id', $data['id_pemasok']);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            Flash::setFlash($e->getMessage(), 'danger');
            return false;
        }
    }

    public function hapusPembayaran($id, $tenant_id) {
        $jurnalModel = new Jurnal_model($this->db);
        
        $this->db->beginTransaction();
        try {
            $this->db->query("SELECT * FROM pembayaran_pemasok WHERE id_pembayaran = :id AND tenant_id = :tenant_id");
            $this->db->bind('id', $id);
            $this->db->bind('tenant_id', $tenant_id);
            $pembayaran = $this->db->single();
            
            if (!$pembayaran) throw new Exception("Data pembayaran tidak ditemukan.");
            
            $id_pemasok = $pembayaran['id_pemasok'];
            $total_dibayar = (float)$pembayaran['total_dibayar'];
            $id_jurnal = $pembayaran['id_jurnal'];
            
            $this->db->query("SELECT * FROM pembayaran_pemasok_detail WHERE id_pembayaran = :id");
            $this->db->bind('id', $id);
            $details = $this->db->resultSet();
            
            foreach ($details as $detail) {
                $id_pembelian = $detail['id_pembelian'];
                $jumlah_bayar = (float)$detail['jumlah_bayar'];
                
                $this->db->query("UPDATE pembelian SET sisa_tagihan = sisa_tagihan + :jumlah, 
                                  status_pembayaran = IF(sisa_tagihan + :jumlah2 >= total, 'Belum Bayar', 'Lunas Sebagian')
                                  WHERE id_pembelian = :id AND tenant_id = :tenant_id");
                $this->db->bind('jumlah', $jumlah_bayar);
                $this->db->bind('jumlah2', $jumlah_bayar);
                $this->db->bind('id', $id_pembelian);
                $this->db->bind('tenant_id', $tenant_id);
                $this->db->execute();
            }
            
            $this->db->query("DELETE FROM pembayaran_pemasok_detail WHERE id_pembayaran = :id");
            $this->db->bind('id', $id);
            $this->db->execute();
            
            $this->db->query("UPDATE pemasok SET saldo_terkini_hutang = saldo_terkini_hutang + :total WHERE id_pemasok = :id AND tenant_id = :tenant_id");
            $this->db->bind('total', $total_dibayar);
            $this->db->bind('id', $id_pemasok);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->execute();
            
            $this->db->query("DELETE FROM pembayaran_pemasok WHERE id_pembayaran = :id AND tenant_id = :tenant_id");
            $this->db->bind('id', $id);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->execute();
            
            if ($id_jurnal) {
                $jurnalModel->hapusJurnal($id_jurnal, $tenant_id, true);
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            Flash::setFlash('Gagal menghapus pembayaran: ' . $e->getMessage(), 'danger');
            return false;
        }
    }

    public function getPembayaranByIdWithDetails($id, $tenant_id) {
        $this->db->query("SELECT pp.*, pm.nama_pemasok, pm.alamat as alamat_pemasok, ak.nama_akun as nama_akun_kas
                         FROM pembayaran_pemasok pp
                         JOIN pemasok pm ON pp.id_pemasok = pm.id_pemasok AND pm.tenant_id = pp.tenant_id
                         JOIN akun ak ON pp.akun_kas_bank = ak.kode_akun AND ak.tenant_id = pp.tenant_id
                         WHERE pp.id_pembayaran = :id AND pp.tenant_id = :tenant_id");
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $header = $this->db->single();

        if (!$header) return false;

        $this->db->query("SELECT ppd.*, pb.no_faktur_pembelian, pb.tanggal_faktur
                         FROM pembayaran_pemasok_detail ppd
                         LEFT JOIN pembelian pb ON ppd.id_pembelian = pb.id_pembelian AND pb.tenant_id = :tenant_id
                         WHERE ppd.id_pembayaran = :id");
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $details = $this->db->resultSet();
        
        if(empty($details)) {
             $details[] = ['no_faktur_pembelian' => 'SALDO AWAL', 'tanggal_faktur' => $header['tanggal'], 'jumlah_bayar' => $header['total_dibayar']];
        }

        $header['details'] = $details;
        return $header;
    }
}
