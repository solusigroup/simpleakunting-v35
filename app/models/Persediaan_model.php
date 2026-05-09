<?php

class Persediaan_model {
    private $table = 'master_persediaan';
    private $db;

    /**
     * Constructor baru yang menerima koneksi database dari Controller.
     */
    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllBarang($tenant_id) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE tenant_id = :tenant_id ORDER BY nama_barang ASC');
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->resultSet();
    }
    
    public function getBarangById($id_barang, $tenant_id) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id_barang = :id AND tenant_id = :tenant_id');
        $this->db->bind('id', $id_barang);
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->single();
    }
    
    public function isKodeBarangExists($kode_barang, $tenant_id) {
        $this->db->query('SELECT 1 FROM ' . $this->table . ' WHERE kode_barang = :kode AND tenant_id = :tenant_id');
        $this->db->bind('kode', $kode_barang);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    public function tambahDataBarang($data, $tenant_id) {
        $query = "INSERT INTO {$this->table} 
                    (tenant_id, kode_barang, nama_barang, kategori, satuan, stok_awal, stok_saat_ini, harga_beli, harga_jual, akun_persediaan, akun_hpp, akun_penjualan) 
                  VALUES 
                    (:tenant_id, :kode, :nama, :kategori, :satuan, :stok_awal, :stok_saat_ini, :harga_beli, :harga_jual, :akun_persediaan, :akun_hpp, :akun_penjualan)";
        
        $this->db->beginTransaction();
        try {
            $this->db->query($query);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('kode', $data['kode_barang']);
            $this->db->bind('nama', $data['nama_barang']);
            $this->db->bind('kategori', $data['kategori']);
            $this->db->bind('satuan', $data['satuan']);
            $this->db->bind('stok_awal', $data['stok_awal']);
            $this->db->bind('stok_saat_ini', $data['stok_awal']);
            $this->db->bind('harga_beli', $data['harga_beli']);
            $this->db->bind('harga_jual', $data['harga_jual']);
            $this->db->bind('akun_persediaan', $data['akun_persediaan']);
            $this->db->bind('akun_hpp', $data['akun_hpp']);
            $this->db->bind('akun_penjualan', $data['akun_penjualan']);
            $this->db->execute();
            $id_barang = $this->db->lastInsertId();

            if ((float)$data['stok_awal'] > 0) {
                $queryKartuStok = "INSERT INTO kartu_stok (id_barang, tipe_transaksi, kuantitas, keterangan) VALUES (:id_barang, 'IN', :qty, :keterangan)";
                $this->db->query($queryKartuStok);
                $this->db->bind('id_barang', $id_barang);
                $this->db->bind('qty', $data['stok_awal']);
                $this->db->bind('keterangan', 'Stok Awal');
                $this->db->execute();
            }
            
            $this->db->commit();
            return $this->db->rowCount();
        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            die("<strong>ERROR DATABASE SAAT TAMBAH BARANG:</strong> <br>" . $e->getMessage());
            return 0;
        }
    }

    public function ubahDataBarang($data, $tenant_id) {
        $this->db->beginTransaction();
        try {
            $barangLama = $this->getBarangById($data['id_barang'], $tenant_id);
            if (!$barangLama) {
                throw new Exception("Barang tidak ditemukan.");
            }

            $stokAwalLama = (float)$barangLama['stok_awal'];
            $stokAwalBaru = (float)$data['stok_awal'];
            $selisihStokAwal = $stokAwalBaru - $stokAwalLama;
            $stokTerkiniBaru = (float)$barangLama['stok_saat_ini'] + $selisihStokAwal;

            $query = "UPDATE {$this->table} SET 
                        kode_barang = :kode,
                        nama_barang = :nama,
                        kategori = :kategori,
                        satuan = :satuan,
                        stok_awal = :stok_awal,
                        stok_saat_ini = :stok_terkini,
                        harga_beli = :harga_beli,
                        harga_jual = :harga_jual,
                        akun_persediaan = :akun_persediaan,
                        akun_hpp = :akun_hpp,
                        akun_penjualan = :akun_penjualan
                      WHERE id_barang = :id AND tenant_id = :tenant_id";
            $this->db->query($query);
            $this->db->bind('kode', $data['kode_barang']);
            $this->db->bind('nama', $data['nama_barang']);
            $this->db->bind('kategori', $data['kategori']);
            $this->db->bind('satuan', $data['satuan']);
            $this->db->bind('stok_awal', $stokAwalBaru);
            $this->db->bind('stok_terkini', $stokTerkiniBaru);
            $this->db->bind('harga_beli', $data['harga_beli']);
            $this->db->bind('harga_jual', $data['harga_jual']);
            $this->db->bind('akun_persediaan', $data['akun_persediaan']);
            $this->db->bind('akun_hpp', $data['akun_hpp']);
            $this->db->bind('akun_penjualan', $data['akun_penjualan']);
            $this->db->bind('id', $data['id_barang']);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->execute();
            $rowCount = $this->db->rowCount();

            if ($selisihStokAwal != 0) {
                $queryKartuStok = "INSERT INTO kartu_stok (id_barang, tipe_transaksi, kuantitas, keterangan) VALUES (:id_barang, :tipe, :qty, :keterangan)";
                $this->db->query($queryKartuStok);
                $this->db->bind('id_barang', $data['id_barang']);
                $this->db->bind('tipe', ($selisihStokAwal > 0) ? 'IN' : 'OUT');
                $this->db->bind('qty', abs($selisihStokAwal));
                $this->db->bind('keterangan', 'Penyesuaian Stok Awal');
                $this->db->execute();
            }

            $this->db->commit();
            return $rowCount;

        } catch (\Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            return 0;
        }
    }

    public function hapusDataBarang($id, $tenant_id) {
        $query = "DELETE FROM {$this->table} WHERE id_barang = :id AND tenant_id = :tenant_id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        $this->db->execute();
        return $this->db->rowCount();
    }
    
    public function importFromExcel($data, $tenant_id) {
        $this->db->beginTransaction();
        try {
            $this->db->query('DELETE FROM ' . $this->table . ' WHERE tenant_id = :tenant_id');
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->execute();

            $rowCount = 0;
            foreach ($data as $barang) {
                if (!empty($barang['kode_barang'])) {
                    $this->tambahDataBarang($barang, $tenant_id);
                    $rowCount++;
                }
            }
            $this->db->commit();
            return $rowCount;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            return 0;
        }
    }
}