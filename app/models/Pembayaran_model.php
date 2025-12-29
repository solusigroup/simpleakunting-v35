<?php

require_once 'Jurnal_model.php';

class Pembayaran_model {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllPembayaran() {
        $this->db->query("SELECT pp.*, p.nama_pemasok 
                         FROM pembayaran_pemasok pp
                         JOIN pemasok p ON pp.id_pemasok = p.id_pemasok
                         ORDER BY pp.tanggal DESC");
        return $this->db->resultSet();
    }
    
    /**
     * FUNGSI YANG DIPERBARUI: Sekarang menyertakan sisa Saldo Awal sebagai "faktur virtual".
     */
    public function getFakturBelumLunasByPemasok($id_pemasok) {
        // 1. Ambil semua faktur pembelian nyata yang belum lunas
        $this->db->query("SELECT id_pembelian, no_faktur_pembelian, tanggal_faktur, sisa_tagihan 
                         FROM pembelian 
                         WHERE id_pemasok = :id AND status_pembayaran != 'Lunas'
                         ORDER BY tanggal_faktur ASC");
        $this->db->bind('id', $id_pemasok);
        $fakturNyata = $this->db->resultSet();

        // 2. Ambil saldo terkini pemasok
        $this->db->query("SELECT saldo_terkini_hutang FROM pemasok WHERE id_pemasok = :id");
        $this->db->bind('id', $id_pemasok);
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
        if ($sisaSaldoAwal > 0.01) { // Toleransi untuk presisi float
            $fakturSaldoAwal = [
                'id_pembelian' => 'SA-' . $id_pemasok, // ID khusus untuk Saldo Awal
                'no_faktur_pembelian' => 'SALDO AWAL',
                'tanggal_faktur' => 'N/A',
                'sisa_tagihan' => $sisaSaldoAwal
            ];
            array_unshift($fakturNyata, $fakturSaldoAwal); // Tambahkan ke awal daftar
        }

        return $fakturNyata;
    }

    /**
     * FUNGSI YANG DIPERBARUI: Sekarang bisa memproses pembayaran untuk faktur nyata dan "faktur virtual".
     */
    public function simpanPembayaran($data) {
        $jurnalModel = new Jurnal_model($this->db);
        
        $this->db->beginTransaction();
        try {
            $totalDibayar = array_sum($data['details']['jumlah_bayar']);
            $this->db->query("SELECT akun_utang_default FROM perusahaan WHERE id = 1");
            $akun_utang_usaha = $this->db->single()['akun_utang_default'];
            if (empty($akun_utang_usaha)) throw new Exception("Akun Utang Usaha default belum diatur.");

            $jurnalData = [
                'no_transaksi' => $data['no_bukti'], 'tanggal' => $data['tanggal'],
                'deskripsi' => 'Pembayaran kepada ' . $data['nama_pemasok'], 'sumber_jurnal' => 'Pembelian',
                'details' => [
                    ['kode_akun' => $akun_utang_usaha, 'debit' => $totalDibayar, 'kredit' => 0],
                    ['kode_akun' => $data['akun_kas_bank'], 'debit' => 0, 'kredit' => $totalDibayar]
                ]
            ];
            $id_jurnal = $jurnalModel->simpanJurnal($jurnalData);
            if ($id_jurnal == 0) throw new Exception("Gagal menyimpan jurnal pembayaran.");
            $this->db->query("UPDATE jurnal_umum SET is_locked = 1 WHERE id_jurnal = :id");
            $this->db->bind('id', $id_jurnal);
            $this->db->execute();

            $queryHeader = "INSERT INTO pembayaran_pemasok (id_pemasok, id_jurnal, no_bukti, tanggal, akun_kas_bank, total_dibayar, keterangan) VALUES (:id_pemasok, :id_jurnal, :no_bukti, :tanggal, :akun_kas, :total, :ket)";
            $this->db->query($queryHeader);
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
                    // Jika ini BUKAN pembayaran untuk Saldo Awal, catat detail dan update faktur
                    if (strpos($id_pembelian, 'SA-') !== 0) {
                        $queryDetail = "INSERT INTO pembayaran_pemasok_detail (id_pembayaran, id_pembelian, jumlah_bayar) VALUES (:id_pembayaran, :id_pembelian, :jumlah)";
                        $this->db->query($queryDetail);
                        $this->db->bind('id_pembayaran', $id_pembayaran);
                        $this->db->bind('id_pembelian', $id_pembelian);
                        $this->db->bind('jumlah', $jumlah_bayar);
                        $this->db->execute();

                        $queryUpdateFaktur = "UPDATE pembelian SET sisa_tagihan = sisa_tagihan - :jumlah, status_pembayaran = IF(sisa_tagihan <= 0.01, 'Lunas', 'Lunas Sebagian') WHERE id_pembelian = :id_pembelian";
                        $this->db->query($queryUpdateFaktur);
                        $this->db->bind('jumlah', $jumlah_bayar);
                        $this->db->bind('id_pembelian', $id_pembelian);
                        $this->db->execute();
                    }
                }
            }

            // Update Saldo Terkini Pemasok (ini akan selalu benar, baik untuk faktur maupun saldo awal)
            $this->db->query("UPDATE pemasok SET saldo_terkini_hutang = saldo_terkini_hutang - :total WHERE id_pemasok = :id");
            $this->db->bind('total', $totalDibayar);
            $this->db->bind('id', $data['id_pemasok']);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            Flash::setFlash($e->getMessage(), 'danger');
            return false;
        }
    }

    /**
     * Menghapus transaksi pembayaran pemasok dan membalik semua efeknya.
     * @param int $id ID pembayaran yang akan dihapus
     * @return bool
     */
    public function hapusPembayaran($id) {
        $jurnalModel = new Jurnal_model($this->db);
        
        $this->db->beginTransaction();
        try {
            // 1. Ambil data header pembayaran
            $this->db->query("SELECT * FROM pembayaran_pemasok WHERE id_pembayaran = :id");
            $this->db->bind('id', $id);
            $pembayaran = $this->db->single();
            
            if (!$pembayaran) {
                throw new Exception("Data pembayaran tidak ditemukan.");
            }
            
            $id_pemasok = $pembayaran['id_pemasok'];
            $total_dibayar = (float)$pembayaran['total_dibayar'];
            $id_jurnal = $pembayaran['id_jurnal'];
            
            // 2. Ambil detail pembayaran dan kembalikan sisa tagihan faktur
            $this->db->query("SELECT * FROM pembayaran_pemasok_detail WHERE id_pembayaran = :id");
            $this->db->bind('id', $id);
            $details = $this->db->resultSet();
            
            foreach ($details as $detail) {
                $id_pembelian = $detail['id_pembelian'];
                $jumlah_bayar = (float)$detail['jumlah_bayar'];
                
                // Kembalikan sisa tagihan ke faktur pembelian
                $this->db->query("UPDATE pembelian SET sisa_tagihan = sisa_tagihan + :jumlah, 
                                  status_pembayaran = IF(sisa_tagihan + :jumlah2 >= total_tagihan, 'Belum Bayar', 
                                                         IF(sisa_tagihan + :jumlah3 > 0, 'Lunas Sebagian', status_pembayaran))
                                  WHERE id_pembelian = :id");
                $this->db->bind('jumlah', $jumlah_bayar);
                $this->db->bind('jumlah2', $jumlah_bayar);
                $this->db->bind('jumlah3', $jumlah_bayar);
                $this->db->bind('id', $id_pembelian);
                $this->db->execute();
            }
            
            // 3. Hapus detail pembayaran
            $this->db->query("DELETE FROM pembayaran_pemasok_detail WHERE id_pembayaran = :id");
            $this->db->bind('id', $id);
            $this->db->execute();
            
            // 4. Kembalikan saldo terkini pemasok
            $this->db->query("UPDATE pemasok SET saldo_terkini_hutang = saldo_terkini_hutang + :total WHERE id_pemasok = :id");
            $this->db->bind('total', $total_dibayar);
            $this->db->bind('id', $id_pemasok);
            $this->db->execute();
            
            // 5. Hapus header pembayaran
            $this->db->query("DELETE FROM pembayaran_pemasok WHERE id_pembayaran = :id");
            $this->db->bind('id', $id);
            $this->db->execute();
            
            // 6. Hapus jurnal terkait (dengan force = true untuk melewati lock check)
            if ($id_jurnal) {
                $jurnalModel->hapusJurnal($id_jurnal, true);
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            Flash::setFlash('Gagal menghapus pembayaran: ' . $e->getMessage(), 'danger');
            return false;
        }
    }

    /**
     * Mengambil data satu pembayaran lengkap dengan detailnya.
     * @param int $id ID pembayaran
     * @return array|false
     */
    public function getPembayaranByIdWithDetails($id) {
        // 1. Ambil data header pembayaran
        $this->db->query("SELECT pp.*, pm.nama_pemasok, pm.alamat as alamat_pemasok, ak.nama_akun as nama_akun_kas
                         FROM pembayaran_pemasok pp
                         JOIN pemasok pm ON pp.id_pemasok = pm.id_pemasok
                         JOIN akun ak ON pp.akun_kas_bank = ak.kode_akun
                         WHERE pp.id_pembayaran = :id");
        $this->db->bind('id', $id);
        $header = $this->db->single();

        if (!$header) {
            return false;
        }

        // 2. Ambil data detail (faktur yang dibayar)
        $this->db->query("SELECT ppd.*, pb.no_faktur_pembelian, pb.tanggal_faktur
                         FROM pembayaran_pemasok_detail ppd
                         LEFT JOIN pembelian pb ON ppd.id_pembelian = pb.id_pembelian
                         WHERE ppd.id_pembayaran = :id");
        $this->db->bind('id', $id);
        $details = $this->db->resultSet();
        
        // Menangani pembayaran Saldo Awal yang tidak memiliki detail faktur
        if(empty($details)) {
             $details[] = [
                'no_faktur_pembelian' => 'SALDO AWAL',
                'tanggal_faktur' => $header['tanggal'],
                'jumlah_bayar' => $header['total_dibayar']
             ];
        }

        $header['details'] = $details;
        return $header;
    }
}
