<?php

require_once 'Jurnal_model.php';
require_once 'Persediaan_model.php';
require_once 'Bom_model.php';
require_once 'Perusahaan_model.php';

class Produksi_model {
    private $table = 'produksi';
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllProduksi($tenant_id) {
        $this->db->query('SELECT p.*, b.nama_bom, pr.nama_barang as nama_produk 
                          FROM ' . $this->table . ' p 
                          JOIN bom b ON p.id_bom = b.id AND b.tenant_id = p.tenant_id
                          JOIN master_persediaan pr ON b.id_barang_jadi = pr.id_barang AND pr.tenant_id = p.tenant_id
                          WHERE p.tenant_id = :tenant_id 
                          ORDER BY p.tanggal DESC, p.id DESC');
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->resultSet();
    }

    public function getProduksiById($id, $tenant_id) {
        $this->db->query('SELECT p.*, b.nama_bom, b.id_barang_jadi, pr.nama_barang as nama_produk, pr.kode_barang as kode_produk
                          FROM ' . $this->table . ' p 
                          JOIN bom b ON p.id_bom = b.id AND b.tenant_id = p.tenant_id
                          JOIN master_persediaan pr ON b.id_barang_jadi = pr.id_barang AND pr.tenant_id = p.tenant_id
                          WHERE p.id = :id AND p.tenant_id = :tenant_id');
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->single();
    }

    public function tambahProduksi($data, $tenant_id) {
        $query = "INSERT INTO produksi (tenant_id, no_produksi, tanggal, id_bom, jumlah_target, status) 
                  VALUES (:tenant_id, :no_produksi, :tanggal, :id_bom, :jumlah, :status)";
        $this->db->query($query);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->bind('no_produksi', $data['no_produksi']);
        $this->db->bind('tanggal', $data['tanggal']);
        $this->db->bind('id_bom', $data['id_bom']);
        $this->db->bind('jumlah', $data['jumlah_target']);
        $this->db->bind('status', 'Draft');
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function selesaikanProduksi($id, $postData, $tenant_id) {
        $jurnalModel = new Jurnal_model($this->db);
        $persediaanModel = new Persediaan_model($this->db);
        $bomModel = new Bom_model($this->db);
        $perusahaanModel = new Perusahaan_model($this->db);

        $this->db->beginTransaction();
        try {
            // 1. Ambil data produksi
            $produksi = $this->getProduksiById($id, $tenant_id);
            if (!$produksi) throw new Exception("Data produksi tidak ditemukan.");
            if ($produksi['status'] === 'Selesai') throw new Exception("Produksi ini sudah selesai.");

            // 2. Ambil detail BOM
            $bom = $bomModel->getBOMById($produksi['id_bom'], $tenant_id);
            if (!$bom) throw new Exception("Data BOM tidak ditemukan.");

            $totalMaterialCost = 0;
            $qty_produksi = (float)$produksi['jumlah_target'];

            // 3. Validasi & Kurangi Stok Bahan Baku
            foreach ($bom['details'] as $bahan) {
                $qty_dibutuhkan = (float)$bahan['jumlah'] * $qty_produksi;
                $barang = $persediaanModel->getBarangById($bahan['id_bahan_baku'], $tenant_id);
                
                if ($barang['stok_saat_ini'] < $qty_dibutuhkan) {
                    throw new Exception("Stok bahan baku '{$barang['nama_barang']}' tidak cukup. Dibutuhkan: {$qty_dibutuhkan}, Tersedia: {$barang['stok_saat_ini']}");
                }

                // Update Stok Bahan Baku (OUT)
                $this->db->query("UPDATE master_persediaan SET stok_saat_ini = stok_saat_ini - :qty WHERE id_barang = :id AND tenant_id = :tenant_id");
                $this->db->bind('qty', $qty_dibutuhkan);
                $this->db->bind('id', $bahan['id_bahan_baku']);
                $this->db->bind('tenant_id', $tenant_id);
                $this->db->execute();

                // Catat Kartu Stok (OUT)
                $this->db->query("INSERT INTO kartu_stok (id_barang, tipe_transaksi, kuantitas, keterangan) VALUES (:id, 'OUT', :qty, :ket)");
                $this->db->bind('id', $bahan['id_bahan_baku']);
                $this->db->bind('qty', $qty_dibutuhkan);
                $this->db->bind('ket', "Pemakaian Produksi #{$produksi['no_produksi']}");
                $this->db->execute();

                $totalMaterialCost += ($qty_dibutuhkan * (float)$barang['harga_beli']);
            }

            $laborCost = (float)($postData['biaya_tenaga_kerja'] ?? 0);
            $overheadCost = (float)($postData['biaya_overhead'] ?? 0);
            $totalBiayaProduksi = $totalMaterialCost + $laborCost + $overheadCost;

            // 4. Tambah Stok Barang Jadi (IN)
            $this->db->query("UPDATE master_persediaan SET stok_saat_ini = stok_saat_ini + :qty WHERE id_barang = :id AND tenant_id = :tenant_id");
            $this->db->bind('qty', $qty_produksi);
            $this->db->bind('id', $bom['id_barang_jadi']);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->execute();

            // Catat Kartu Stok (IN)
            $this->db->query("INSERT INTO kartu_stok (id_barang, tipe_transaksi, kuantitas, keterangan) VALUES (:id, 'IN', :qty, :ket)");
            $this->db->bind('id', $bom['id_barang_jadi']);
            $this->db->bind('qty', $qty_produksi);
            $this->db->bind('ket', "Hasil Produksi #{$produksi['no_produksi']}");
            $this->db->execute();

            // 5. Buat Jurnal Umum
            $perusahaan = $perusahaanModel->getPerusahaan($tenant_id);
            $produkJadi = $persediaanModel->getBarangById($bom['id_barang_jadi'], $tenant_id);
            $jurnalData = [
                'no_transaksi' => $produksi['no_produksi'],
                'tanggal' => date('Y-m-d'),
                'deskripsi' => "Penyelesaian Produksi #{$produksi['no_produksi']} ({$produkJadi['nama_barang']})",
                'sumber_jurnal' => 'Produksi',
                'details' => []
            ];

            // Sisi Debit: Barang Jadi
            $jurnalData['details'][] = ['kode_akun' => $produkJadi['akun_persediaan'], 'debit' => $totalBiayaProduksi, 'kredit' => 0];

            // Sisi Kredit: Bahan Baku (Dikelompokkan per akun persediaan)
            $kreditGrouped = [];
            foreach ($bom['details'] as $bahan) {
                $barangBahan = $persediaanModel->getBarangById($bahan['id_bahan_baku'], $tenant_id);
                $biayaBahan = (float)$bahan['jumlah'] * $qty_produksi * (float)$barangBahan['harga_beli'];
                $akunBahan = $barangBahan['akun_persediaan'];
                $kreditGrouped[$akunBahan] = ($kreditGrouped[$akunBahan] ?? 0) + $biayaBahan;
            }

            foreach ($kreditGrouped as $akun => $nilai) {
                $jurnalData['details'][] = ['kode_akun' => $akun, 'debit' => 0, 'kredit' => $nilai];
            }

            // Sisi Kredit: Tenaga Kerja Langsung
            if ($laborCost > 0) {
                $akunTK = $perusahaan['akun_tenaga_kerja_langsung'] ?? '5001'; // Fallback
                $jurnalData['details'][] = ['kode_akun' => $akunTK, 'debit' => 0, 'kredit' => $laborCost];
            }

            // Sisi Kredit: Overhead Pabrik
            if ($overheadCost > 0) {
                $akunOH = $perusahaan['akun_overhead_pabrik'] ?? '5002'; // Fallback
                $jurnalData['details'][] = ['kode_akun' => $akunOH, 'debit' => 0, 'kredit' => $overheadCost];
            }

            $id_jurnal = $jurnalModel->simpanJurnal($jurnalData, $tenant_id);
            if ($id_jurnal == 0) throw new Exception("Gagal menyimpan jurnal produksi.");

            $this->db->query("UPDATE jurnal_umum SET is_locked = 1 WHERE id_jurnal = :id AND tenant_id = :tenant_id");
            $this->db->bind('id', $id_jurnal);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->execute();

            // 6. Update Status Produksi
            $this->db->query("UPDATE produksi SET status = 'Selesai', id_jurnal = :id_jurnal, total_biaya_aktual = :total, biaya_tenaga_kerja = :tk, biaya_overhead = :oh WHERE id = :id AND tenant_id = :tenant_id");
            $this->db->bind('id_jurnal', $id_jurnal);
            $this->db->bind('total', $totalBiayaProduksi);
            $this->db->bind('tk', $laborCost);
            $this->db->bind('oh', $overheadCost);
            $this->db->bind('id', $id);
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

    public function getLaporanProduksi($tgl_mulai, $tgl_selesai, $tenant_id) {
        $this->db->query('SELECT p.*, b.nama_bom, pr.nama_barang as nama_produk, pr.kode_barang as kode_produk
                          FROM ' . $this->table . ' p 
                          JOIN bom b ON p.id_bom = b.id AND b.tenant_id = p.tenant_id
                          JOIN master_persediaan pr ON b.id_barang_jadi = pr.id_barang AND pr.tenant_id = p.tenant_id
                          WHERE p.tenant_id = :tenant_id 
                          AND p.tanggal BETWEEN :tgl_mulai AND :tgl_selesai
                          ORDER BY p.tanggal ASC');
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->bind('tgl_mulai', $tgl_mulai);
        $this->db->bind('tgl_selesai', $tgl_selesai);
        return $this->db->resultSet();
    }

    public function getLaporanPemakaianBahan($tgl_mulai, $tgl_selesai, $tenant_id) {
        // Karena detail pemakaian bahan ada di kartu_stok dengan keterangan tertentu
        // Atau kita bisa join produksi, bom_detail, dan master_persediaan
        $this->db->query('SELECT p.no_produksi, p.tanggal, bd.id_bahan_baku, pr.nama_barang as nama_bahan, pr.kode_barang, 
                                 (bd.jumlah * p.jumlah_target) as qty_dipakai, pr.satuan, 
                                 (bd.jumlah * p.jumlah_target * pr.harga_beli) as total_biaya
                          FROM produksi p
                          JOIN bom_detail bd ON p.id_bom = bd.id_bom
                          JOIN master_persediaan pr ON bd.id_bahan_baku = pr.id_barang AND pr.tenant_id = p.tenant_id
                          WHERE p.tenant_id = :tenant_id 
                          AND p.status = "Selesai"
                          AND p.tanggal BETWEEN :tgl_mulai AND :tgl_selesai
                          ORDER BY p.tanggal ASC, p.no_produksi ASC');
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->bind('tgl_mulai', $tgl_mulai);
        $this->db->bind('tgl_selesai', $tgl_selesai);
        return $this->db->resultSet();
    }

    public function hapusProduksi($id, $tenant_id) {
        $this->db->query("SELECT status FROM produksi WHERE id = :id AND tenant_id = :tenant_id");
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $res = $this->db->single();

        if ($res && $res['status'] === 'Selesai') {
            Flash::setFlash("Produksi yang sudah selesai tidak dapat dihapus.", "danger");
            return 0;
        }

        $this->db->query("DELETE FROM produksi WHERE id = :id AND tenant_id = :tenant_id");
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->execute();
        return $this->db->rowCount();
    }
}
