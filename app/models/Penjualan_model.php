<?php

// Muat semua model yang dibutuhkan secara manual
require_once 'Jurnal_model.php';
require_once 'Persediaan_model.php';

class Penjualan_model {
    private $db;
    private static $schemaChecked = false;

    /**
     * Constructor baru yang menerima koneksi database dari Controller.
     */
    public function __construct($db) {
        $this->db = $db;
        $this->ensureTableSchema();
    }

    /**
     * Memastikan struktur tabel penjualan dan penjualan_detail lengkap dan mutakhir (Self-healing).
     */
    public function ensureTableSchema() {
        if (self::$schemaChecked) {
            return;
        }

        try {
            // 1. Pastikan tabel penjualan ada
            $this->db->query("CREATE TABLE IF NOT EXISTS `penjualan` (
                `id_penjualan` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `tenant_id` int(11) DEFAULT 1,
                `id_pelanggan` bigint(20) unsigned NOT NULL,
                `id_jurnal` bigint(20) unsigned DEFAULT NULL,
                `no_faktur` varchar(255) NOT NULL,
                `tanggal_faktur` date NOT NULL,
                `total` decimal(15,2) NOT NULL DEFAULT 0.00,
                `pajak` decimal(15,2) DEFAULT 0.00,
                `diskon` decimal(15,2) DEFAULT 0.00,
                `keterangan` text DEFAULT NULL,
                `metode_pembayaran` varchar(255) NOT NULL,
                `akun_kas_bank` varchar(255) DEFAULT NULL,
                `sisa_tagihan` decimal(15,2) NOT NULL DEFAULT 0.00,
                `status_pembayaran` varchar(255) NOT NULL DEFAULT 'Belum Lunas',
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id_penjualan`),
                KEY `idx_tenant_penjualan` (`tenant_id`, `no_faktur`),
                KEY `idx_pelanggan` (`id_pelanggan`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
            $this->db->execute();

            // 2. Pastikan tabel penjualan_detail ada
            $this->db->query("CREATE TABLE IF NOT EXISTS `penjualan_detail` (
                `id_detail` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `id_penjualan` bigint(20) unsigned NOT NULL,
                `id_barang` bigint(20) unsigned NOT NULL,
                `kuantitas` decimal(15,2) NOT NULL DEFAULT 0.00,
                `harga` decimal(15,2) NOT NULL DEFAULT 0.00,
                `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
                `akun_pendapatan` varchar(50) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id_detail`),
                KEY `idx_penjualan_detail_penjualan` (`id_penjualan`),
                KEY `idx_penjualan_detail_barang` (`id_barang`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
            $this->db->execute();

            // 3. Verifikasi kolom pada tabel penjualan jika tabel sudah ada sebelumnya
            $this->db->query("SHOW COLUMNS FROM `penjualan`");
            $cols = $this->db->resultSet();
            $fields = array_column($cols, 'Field');

            if (!in_array('tenant_id', $fields)) {
                $this->db->query("ALTER TABLE `penjualan` ADD COLUMN `tenant_id` int(11) DEFAULT 1 AFTER `id_penjualan`");
                $this->db->execute();
            }
            if (!in_array('pajak', $fields)) {
                $this->db->query("ALTER TABLE `penjualan` ADD COLUMN `pajak` decimal(15,2) DEFAULT 0.00 AFTER `total`");
                $this->db->execute();
            }
            if (!in_array('diskon', $fields)) {
                $this->db->query("ALTER TABLE `penjualan` ADD COLUMN `diskon` decimal(15,2) DEFAULT 0.00 AFTER `pajak`");
                $this->db->execute();
            }
            if (!in_array('sisa_tagihan', $fields)) {
                $this->db->query("ALTER TABLE `penjualan` ADD COLUMN `sisa_tagihan` decimal(15,2) NOT NULL DEFAULT 0.00 AFTER `akun_kas_bank`");
                $this->db->execute();
            }
            if (!in_array('status_pembayaran', $fields)) {
                $this->db->query("ALTER TABLE `penjualan` ADD COLUMN `status_pembayaran` varchar(255) NOT NULL DEFAULT 'Belum Lunas' AFTER `sisa_tagihan`");
                $this->db->execute();
            }

            self::$schemaChecked = true;
        } catch (Throwable $e) {
            error_log("Penjualan_model::ensureTableSchema error: " . $e->getMessage());
        }
    }

    public function getAllPenjualan($tenant_id) {
        $this->ensureTableSchema();
        $this->db->query("SELECT p.*, pl.nama_pelanggan 
                         FROM penjualan p
                         LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan AND pl.tenant_id = p.tenant_id
                         WHERE p.tenant_id = :tenant_id
                         ORDER BY p.tanggal_faktur DESC");
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->resultSet();
    }
    
    public function getPenjualanByIdWithDetails($id, $tenant_id) {
        $this->ensureTableSchema();
        $this->db->query("SELECT p.*, pl.nama_pelanggan, pl.alamat as alamat_pelanggan
                         FROM penjualan p
                         LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan AND pl.tenant_id = p.tenant_id
                         WHERE p.id_penjualan = :id AND p.tenant_id = :tenant_id");
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $header = $this->db->single();
        if (!$header) return false;

        $this->db->query("SELECT pd.*, a.nama_akun, ms.kode_barang, ms.nama_barang, ms.id_barang, pd.kuantitas
                         FROM penjualan_detail pd
                         JOIN master_persediaan ms ON pd.id_barang = ms.id_barang AND ms.tenant_id = :tenant_id
                         JOIN akun a ON ms.akun_penjualan = a.kode_akun AND a.tenant_id = :tenant_id
                         WHERE pd.id_penjualan = :id");
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $header['details'] = $this->db->resultSet();
        return $header;
    }
    
    public function simpanPenjualan($data, $tenant_id) {
        $this->ensureTableSchema();
        $jurnalModel = new Jurnal_model($this->db);
        $persediaanModel = new Persediaan_model($this->db);
        
        $this->db->beginTransaction();
        try {
            // Ambil akun piutang, pajak, dan jenis usaha dari pengaturan tenant
            $this->db->query("SELECT akun_piutang_default, akun_pajak_penjualan, jenis_usaha FROM perusahaan WHERE tenant_id = :tenant_id");
            $this->db->bind('tenant_id', $tenant_id);
            $perusahaan = $this->db->single();
            $akun_piutang_usaha = $perusahaan['akun_piutang_default'] ?? null;
            $akun_pajak_penjualan = $perusahaan['akun_pajak_penjualan'] ?? null;
            $jenisUsaha = $perusahaan['jenis_usaha'] ?? 'dagang';
            $isJasa = ($jenisUsaha === 'jasa');
            
            if (empty($akun_piutang_usaha)) {
                throw new Exception("Akun Piutang Usaha default belum diatur di Pengaturan Perusahaan.");
            }

            // Validasi Stok (hanya untuk perusahaan Dagang/Manufaktur)
            if (!$isJasa) {
                foreach ($data['details']['id_barang'] as $index => $id_barang) {
                    $barang = $persediaanModel->getBarangById($id_barang, $tenant_id);
                    if (!$barang) throw new Exception("Data barang tidak ditemukan.");
                    $qty_dijual = (float)$data['details']['kuantitas'][$index];
                    if ($barang['stok_saat_ini'] < $qty_dijual) {
                        throw new Exception("Stok untuk '{$barang['nama_barang']}' tidak cukup (tersisa: {$barang['stok_saat_ini']}).");
                    }
                }
            }

            $totalSubtotal = array_sum($data['details']['subtotal']);
            $totalDiskon = (float)($data['total_diskon'] ?? 0);
            $totalPajak = (float)($data['total_pajak'] ?? 0);
            $totalPenjualan = $totalSubtotal - $totalDiskon + $totalPajak;

            $jurnalData = [
                'no_transaksi' => $data['no_faktur'],
                'tanggal' => $data['tanggal_faktur'],
                'deskripsi' => 'Penjualan kpd ' . $data['nama_pelanggan'] . ' (Faktur #' . $data['no_faktur'] . ')',
                'sumber_jurnal' => 'Penjualan',
                'details' => []
            ];

            // Sisi Debit: Kas/Bank atau Piutang
            if ($data['metode_pembayaran'] === 'Tunai') {
                $jurnalData['details'][] = ['kode_akun' => $data['akun_kas_bank'], 'debit' => $totalPenjualan, 'kredit' => 0];
            } else {
                $jurnalData['details'][] = ['kode_akun' => $akun_piutang_usaha, 'debit' => $totalPenjualan, 'kredit' => 0];
            }

            // Sisi Kredit: Pendapatan & Pajak
            $pendapatanGrouped = []; $hppGroupedDebit = []; $hppGroupedKredit = [];
            foreach ($data['details']['id_barang'] as $index => $id_barang) {
                $barang = $persediaanModel->getBarangById($id_barang, $tenant_id);
                $pendapatanGrouped[$barang['akun_penjualan']] = ($pendapatanGrouped[$barang['akun_penjualan']] ?? 0) + $data['details']['subtotal'][$index];
                
                if (!$isJasa && !empty($barang['akun_hpp']) && !empty($barang['akun_persediaan'])) {
                    $subtotal_hpp = (float)$data['details']['kuantitas'][$index] * (float)$barang['harga_beli'];
                    $hppGroupedDebit[$barang['akun_hpp']] = ($hppGroupedDebit[$barang['akun_hpp']] ?? 0) + $subtotal_hpp;
                    $hppGroupedKredit[$barang['akun_persediaan']] = ($hppGroupedKredit[$barang['akun_persediaan']] ?? 0) + $subtotal_hpp;
                }
            }

            foreach ($pendapatanGrouped as $akun => $total) { 
                // Jika ada diskon, kurangi secara proporsional atau biarkan akun diskon terpisah?
                // Untuk kesederhanaan, kita kurangi pendapatan langsung atau pakai akun diskon.
                // Disini kita biarkan pendapatan utuh dan tambahkan baris diskon di debit jika ada.
                $jurnalData['details'][] = ['kode_akun' => $akun, 'debit' => 0, 'kredit' => $total]; 
            }

            if ($totalDiskon > 0) {
                // Gunakan akun diskon jika ada, atau kurangi pendapatan. Disini asumsikan 4102 atau semacamnya.
                // Untuk sementara kurangi di sisi Debit (Beban Diskon/Kontra-Pendapatan)
                $jurnalData['details'][] = ['kode_akun' => '4102', 'debit' => $totalDiskon, 'kredit' => 0];
            }

            if ($totalPajak > 0 && !empty($akun_pajak_penjualan)) {
                $jurnalData['details'][] = ['kode_akun' => $akun_pajak_penjualan, 'debit' => 0, 'kredit' => $totalPajak];
            }

            // Sisi Jurnal HPP & Persediaan
            if (!$isJasa) {
                foreach ($hppGroupedDebit as $akun => $total) { $jurnalData['details'][] = ['kode_akun' => $akun, 'debit' => $total, 'kredit' => 0]; }
                foreach ($hppGroupedKredit as $akun => $total) { $jurnalData['details'][] = ['kode_akun' => $akun, 'debit' => 0, 'kredit' => $total]; }
            }
            
            $id_jurnal = $jurnalModel->simpanJurnal($jurnalData, $tenant_id);
            if ($id_jurnal == 0) throw new Exception("Gagal menyimpan jurnal penjualan.");
            
            $this->db->query("UPDATE jurnal_umum SET is_locked = 1 WHERE id_jurnal = :id AND tenant_id = :tenant_id");
            $this->db->bind('id', $id_jurnal);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->execute();
            
            $sisa_tagihan = ($data['metode_pembayaran'] === 'Kredit') ? $totalPenjualan : 0.00;
            $status_pembayaran = ($data['metode_pembayaran'] === 'Kredit') ? 'Belum Lunas' : 'Lunas';

            $queryPenjualan = "INSERT INTO penjualan (tenant_id, id_pelanggan, id_jurnal, no_faktur, tanggal_faktur, total, pajak, diskon, keterangan, metode_pembayaran, akun_kas_bank, sisa_tagihan, status_pembayaran) 
                               VALUES (:tenant_id, :id_pelanggan, :id_jurnal, :no_faktur, :tanggal, :total, :pajak, :diskon, :keterangan, :metode, :akun_kas, :sisa_tagihan, :status)";
            $this->db->query($queryPenjualan);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('id_pelanggan', $data['id_pelanggan']);
            $this->db->bind('id_jurnal', $id_jurnal);
            $this->db->bind('no_faktur', $data['no_faktur']);
            $this->db->bind('tanggal', $data['tanggal_faktur']);
            $this->db->bind('total', $totalPenjualan);
            $this->db->bind('pajak', $totalPajak);
            $this->db->bind('diskon', $totalDiskon);
            $this->db->bind('keterangan', $data['keterangan']);
            $this->db->bind('metode', $data['metode_pembayaran']);
            $this->db->bind('akun_kas', ($data['metode_pembayaran'] === 'Tunai') ? $data['akun_kas_bank'] : null);
            $this->db->bind('sisa_tagihan', $sisa_tagihan);
            $this->db->bind('status', $status_pembayaran);
            $this->db->execute();
            $id_penjualan = $this->db->lastInsertId();

            $queryDetail = "INSERT INTO penjualan_detail (id_penjualan, id_barang, kuantitas, harga, subtotal, akun_pendapatan) VALUES (:id_penjualan, :id_barang, :qty, :harga, :subtotal, :akun)";
            $queryUpdateStok = "UPDATE master_persediaan SET stok_saat_ini = stok_saat_ini - :qty WHERE id_barang = :id_barang AND tenant_id = :tenant_id";
            
            foreach ($data['details']['id_barang'] as $index => $id_barang) {
                $barang = $persediaanModel->getBarangById($id_barang, $tenant_id);
                $qty = $data['details']['kuantitas'][$index];
                
                $this->db->query($queryDetail);
                $this->db->bind('id_penjualan', $id_penjualan);
                $this->db->bind('id_barang', $id_barang);
                $this->db->bind('qty', $qty);
                $this->db->bind('harga', $data['details']['harga'][$index]);
                $this->db->bind('subtotal', $data['details']['subtotal'][$index]);
                $this->db->bind('akun', $barang['akun_penjualan']);
                $this->db->execute();
                
                if (!$isJasa) {
                    $this->db->query($queryUpdateStok);
                    $this->db->bind('qty', $qty);
                    $this->db->bind('id_barang', $id_barang);
                    $this->db->bind('tenant_id', $tenant_id);
                    $this->db->execute();
                }
            }

            if ($data['metode_pembayaran'] === 'Kredit') {
                $this->db->query("UPDATE pelanggan SET saldo_terkini_piutang = saldo_terkini_piutang + :total WHERE id_pelanggan = :id AND tenant_id = :tenant_id");
                $this->db->bind('total', $totalPenjualan);
                $this->db->bind('id', $data['id_pelanggan']);
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

    public function hapusPenjualan($id_penjualan, $tenant_id) {
        $jurnalModel = new Jurnal_model($this->db);

        $this->db->beginTransaction();
        try {
            $penjualan = $this->getPenjualanByIdWithDetails($id_penjualan, $tenant_id);
            if (!$penjualan) throw new Exception('Faktur penjualan tidak ditemukan.');

            $queryUpdateStok = "UPDATE master_persediaan SET stok_saat_ini = stok_saat_ini + :qty WHERE id_barang = :id_barang AND tenant_id = :tenant_id";
            foreach ($penjualan['details'] as $item) {
                $this->db->query($queryUpdateStok);
                $this->db->bind('qty', (float)$item['kuantitas']);
                $this->db->bind('id_barang', $item['id_barang']);
                $this->db->bind('tenant_id', $tenant_id);
                $this->db->execute();
            }

            $this->db->query("DELETE FROM penjualan_detail WHERE id_penjualan = :id");
            $this->db->bind('id', $id_penjualan);
            $this->db->execute();

            $this->db->query("DELETE FROM penjualan WHERE id_penjualan = :id AND tenant_id = :tenant_id");
            $this->db->bind('id', $id_penjualan);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->execute();

            if ($penjualan['id_jurnal']) {
                $jurnalModel->hapusJurnal($penjualan['id_jurnal'], $tenant_id, true);
            }
            
            if ($penjualan['metode_pembayaran'] === 'Kredit') {
                $this->db->query("UPDATE pelanggan SET saldo_terkini_piutang = saldo_terkini_piutang - :total WHERE id_pelanggan = :id AND tenant_id = :tenant_id");
                $this->db->bind('total', (float)$penjualan['total']);
                $this->db->bind('id', $penjualan['id_pelanggan']);
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
