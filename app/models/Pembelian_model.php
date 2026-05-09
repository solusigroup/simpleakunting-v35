<?php

// Muat semua model yang dibutuhkan secara manual
require_once 'Jurnal_model.php';
require_once 'Persediaan_model.php';

class Pembelian_model {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllPembelian($tenant_id) {
        $this->db->query("SELECT p.*, pm.nama_pemasok 
                         FROM pembelian p
                         LEFT JOIN pemasok pm ON p.id_pemasok = pm.id_pemasok AND pm.tenant_id = p.tenant_id
                         WHERE p.tenant_id = :tenant_id
                         ORDER BY p.tanggal_faktur DESC");
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->resultSet();
    }
    
    public function getPembelianByIdWithDetails($id, $tenant_id) {
        $this->db->query("SELECT p.*, pm.nama_pemasok, pm.alamat as alamat_pemasok
                         FROM pembelian p
                         LEFT JOIN pemasok pm ON p.id_pemasok = pm.id_pemasok AND pm.tenant_id = p.tenant_id
                         WHERE p.id_pembelian = :id AND p.tenant_id = :tenant_id");
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $header = $this->db->single();
        if (!$header) return false;

        $this->db->query("SELECT pd.*, ms.id_barang, ms.kode_barang, ms.nama_barang, pd.kuantitas
                         FROM pembelian_detail pd
                         JOIN master_persediaan ms ON pd.id_barang = ms.id_barang AND ms.tenant_id = :tenant_id
                         WHERE pd.id_pembelian = :id");
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $header['details'] = $this->db->resultSet();
        return $header;
    }
    
    public function simpanPembelian($data, $tenant_id) {
        $jurnalModel = new Jurnal_model($this->db);
        $persediaanModel = new Persediaan_model($this->db);
        
        $this->db->beginTransaction();
        try {
            // Ambil akun utang dan pajak dari pengaturan tenant
            $this->db->query("SELECT akun_utang_default, akun_pajak_pembelian FROM perusahaan WHERE tenant_id = :tenant_id");
            $this->db->bind('tenant_id', $tenant_id);
            $perusahaan = $this->db->single();
            $akun_utang_usaha = $perusahaan['akun_utang_default'] ?? null;
            $akun_pajak_pembelian = $perusahaan['akun_pajak_pembelian'] ?? null;
            
            if (empty($akun_utang_usaha)) throw new Exception("Akun Utang Usaha default belum diatur di Pengaturan Perusahaan.");

            $totalSubtotal = array_sum($data['details']['subtotal']);
            $totalDiskon = (float)($data['total_diskon'] ?? 0);
            $totalPajak = (float)($data['total_pajak'] ?? 0);
            $totalPembelian = $totalSubtotal - $totalDiskon + $totalPajak;

            $jurnalData = [
                'no_transaksi' => $data['no_faktur_pembelian'],
                'tanggal' => $data['tanggal_faktur'],
                'deskripsi' => 'Pembelian dari ' . $data['nama_pemasok'],
                'sumber_jurnal' => 'Pembelian',
                'details' => []
            ];

            // Sisi Kredit: Kas/Bank atau Utang
            if ($data['metode_pembayaran'] === 'Tunai') {
                $jurnalData['details'][] = ['kode_akun' => $data['akun_kas_bank'], 'debit' => 0, 'kredit' => $totalPembelian];
            } else {
                $jurnalData['details'][] = ['kode_akun' => $akun_utang_usaha, 'debit' => 0, 'kredit' => $totalPembelian];
            }

            // Sisi Debit: Persediaan, Pajak, dan Kredit: Diskon
            $bebanGrouped = [];
            foreach ($data['details']['id_barang'] as $index => $id_barang) {
                $barang = $persediaanModel->getBarangById($id_barang, $tenant_id);
                if (!$barang) throw new Exception("Data barang tidak ditemukan.");
                $akun_beban_persediaan = $barang['akun_persediaan'];
                $subtotal = $data['details']['subtotal'][$index];
                $bebanGrouped[$akun_beban_persediaan] = ($bebanGrouped[$akun_beban_persediaan] ?? 0) + $subtotal;
            }

            foreach ($bebanGrouped as $akun => $subtotal) {
                $jurnalData['details'][] = ['kode_akun' => $akun, 'debit' => $subtotal, 'kredit' => 0];
            }

            if ($totalPajak > 0 && !empty($akun_pajak_pembelian)) {
                $jurnalData['details'][] = ['kode_akun' => $akun_pajak_pembelian, 'debit' => $totalPajak, 'kredit' => 0];
            }

            if ($totalDiskon > 0) {
                // Akun Diskon Pembelian (Potongan Harga) - 5102 atau semacamnya
                $jurnalData['details'][] = ['kode_akun' => '5102', 'debit' => 0, 'kredit' => $totalDiskon];
            }
            
            $id_jurnal = $jurnalModel->simpanJurnal($jurnalData, $tenant_id);
            if ($id_jurnal == 0) throw new Exception("Gagal menyimpan entri jurnal.");
            
            $this->db->query("UPDATE jurnal_umum SET is_locked = 1 WHERE id_jurnal = :id AND tenant_id = :tenant_id");
            $this->db->bind('id', $id_jurnal);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->execute();

            $sisa_tagihan = ($data['metode_pembayaran'] === 'Kredit') ? $totalPembelian : 0.00;
            $status_pembayaran = ($data['metode_pembayaran'] === 'Kredit') ? 'Belum Lunas' : 'Lunas';

            $queryPembelian = "INSERT INTO pembelian (tenant_id, id_pemasok, id_jurnal, no_faktur_pembelian, tanggal_faktur, total, pajak, diskon, keterangan, metode_pembayaran, akun_kas_bank, sisa_tagihan, status_pembayaran)
                               VALUES (:tenant_id, :id_pemasok, :id_jurnal, :no_faktur, :tanggal, :total, :pajak, :diskon, :keterangan, :metode, :akun_kas, :sisa_tagihan, :status)";
            $this->db->query($queryPembelian);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('id_pemasok', $data['id_pemasok']);
            $this->db->bind('id_jurnal', $id_jurnal);
            $this->db->bind('no_faktur', $data['no_faktur_pembelian']);
            $this->db->bind('tanggal', $data['tanggal_faktur']);
            $this->db->bind('total', $totalPembelian);
            $this->db->bind('pajak', $totalPajak);
            $this->db->bind('diskon', $totalDiskon);
            $this->db->bind('keterangan', $data['keterangan']);
            $this->db->bind('metode', $data['metode_pembayaran']);
            $this->db->bind('akun_kas', ($data['metode_pembayaran'] === 'Tunai') ? $data['akun_kas_bank'] : null);
            $this->db->bind('sisa_tagihan', $sisa_tagihan);
            $this->db->bind('status', $status_pembayaran);
            $this->db->execute();
            $id_pembelian = $this->db->lastInsertId();

            $queryDetail = "INSERT INTO pembelian_detail (id_pembelian, id_barang, kuantitas, harga, subtotal, akun_beban_persediaan)
                            VALUES (:id_pembelian, :id_barang, :qty, :harga, :subtotal, :akun)";
            $queryUpdateStok = "UPDATE master_persediaan SET stok_saat_ini = stok_saat_ini + :qty WHERE id_barang = :id_barang AND tenant_id = :tenant_id";
            $queryKartuStok = "INSERT INTO kartu_stok (id_barang, tipe_transaksi, kuantitas, keterangan) VALUES (:id_barang, 'IN', :qty, :keterangan)";

            foreach ($data['details']['id_barang'] as $index => $id_barang) {
                $qty = $data['details']['kuantitas'][$index];
                $barang = $persediaanModel->getBarangById($id_barang, $tenant_id);

                $this->db->query($queryDetail);
                $this->db->bind('id_pembelian', $id_pembelian);
                $this->db->bind('id_barang', $id_barang);
                $this->db->bind('qty', $qty);
                $this->db->bind('harga', $data['details']['harga'][$index]);
                $this->db->bind('subtotal', $data['details']['subtotal'][$index]);
                $this->db->bind('akun', $barang['akun_persediaan']);
                $this->db->execute();
                
                $this->db->query($queryUpdateStok);
                $this->db->bind('qty', $qty);
                $this->db->bind('id_barang', $id_barang);
                $this->db->bind('tenant_id', $tenant_id);
                $this->db->execute();

                $this->db->query($queryKartuStok);
                $this->db->bind('id_barang', $id_barang);
                $this->db->bind('qty', $qty);
                $this->db->bind('keterangan', 'Faktur Pembelian: ' . $data['no_faktur_pembelian']);
                $this->db->execute();
            }

            if ($data['metode_pembayaran'] === 'Kredit') {
                $this->db->query("UPDATE pemasok SET saldo_terkini_hutang = saldo_terkini_hutang + :total WHERE id_pemasok = :id AND tenant_id = :tenant_id");
                $this->db->bind('total', $totalPembelian);
                $this->db->bind('id', $data['id_pemasok']);
                $this->db->bind('tenant_id', $tenant_id);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            Flash::setFlash($e->getMessage(), 'danger');
            return false;
        }
    }
    
    public function hapusPembelian($id_pembelian, $tenant_id) {
        $jurnalModel = new Jurnal_model($this->db);

        $this->db->beginTransaction();
        try {
            $pembelian = $this->getPembelianByIdWithDetails($id_pembelian, $tenant_id);
            if (!$pembelian) throw new Exception('Faktur pembelian tidak ditemukan.');

            $queryUpdateStok = "UPDATE master_persediaan SET stok_saat_ini = stok_saat_ini - :qty WHERE id_barang = :id_barang AND tenant_id = :tenant_id";
            foreach ($pembelian['details'] as $item) {
                $this->db->query($queryUpdateStok);
                $this->db->bind('qty', (float)$item['kuantitas']);
                $this->db->bind('id_barang', $item['id_barang']);
                $this->db->bind('tenant_id', $tenant_id);
                $this->db->execute();
            }

            $this->db->query("DELETE FROM pembelian WHERE id_pembelian = :id AND tenant_id = :tenant_id");
            $this->db->bind('id', $id_pembelian);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->execute();

            if ($pembelian['id_jurnal']) {
                $jurnalModel->hapusJurnal($pembelian['id_jurnal'], $tenant_id, true);
            }
            
            if ($pembelian['metode_pembayaran'] === 'Kredit') {
                $this->db->query("UPDATE pemasok SET saldo_terkini_hutang = saldo_terkini_hutang - :total WHERE id_pemasok = :id AND tenant_id = :tenant_id");
                $this->db->bind('total', (float)$pembelian['total']);
                $this->db->bind('id', $pembelian['id_pemasok']);
                $this->db->bind('tenant_id', $tenant_id);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            Flash::setFlash($e->getMessage(), 'danger');
            return false;
        }
    }
}
