<?php

require_once 'Jurnal_model.php';

class Kas_model {
    private $table = 'kas_transaksi';
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllTransaksi($tenant_id) {
        $this->db->query("SELECT kt.*, ak.nama_akun as nama_akun_kas, al.nama_akun as nama_akun_lawan
                         FROM {$this->table} kt
                         JOIN akun ak ON kt.akun_kas_bank = ak.kode_akun AND ak.tenant_id = kt.tenant_id
                         JOIN akun al ON kt.akun_lawan = al.kode_akun AND al.tenant_id = kt.tenant_id
                         WHERE kt.tenant_id = :tenant_id
                         ORDER BY kt.tanggal DESC, kt.id_transaksi DESC");
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->resultSet();
    }

    public function getTransaksiById($id, $tenant_id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id_transaksi = :id AND tenant_id = :tenant_id");
        $this->db->bind('id', $id);
        $this->db->bind('tenant_id', $tenant_id);
        return $this->db->single();
    }

    public function simpanTransaksi($data, $tenant_id) {
        $jurnalModel = new Jurnal_model($this->db);
        
        $this->db->beginTransaction();
        try {
            $debit_akun = ($data['tipe_transaksi'] == 'Masuk') ? $data['akun_kas_bank'] : $data['akun_lawan'];
            $kredit_akun = ($data['tipe_transaksi'] == 'Masuk') ? $data['akun_lawan'] : $data['akun_kas_bank'];
            
            $jurnalData = [
                'no_transaksi' => $data['no_bukti'],
                'tanggal' => $data['tanggal'],
                'deskripsi' => $data['deskripsi'],
                'sumber_jurnal' => 'Kas & Bank',
                'details' => [
                    ['kode_akun' => $debit_akun, 'debit' => $data['jumlah'], 'kredit' => 0],
                    ['kode_akun' => $kredit_akun, 'debit' => 0, 'kredit' => $data['jumlah']]
                ]
            ];
            $id_jurnal = $jurnalModel->simpanJurnal($jurnalData, $tenant_id);
            if ($id_jurnal == 0) throw new Exception("Gagal menyimpan jurnal transaksi kas.");

            $this->db->query("UPDATE jurnal_umum SET is_locked = 1 WHERE id_jurnal = :id AND tenant_id = :tenant_id");
            $this->db->bind('id', $id_jurnal);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->execute();

            $query = "INSERT INTO {$this->table} (tenant_id, id_jurnal, tipe_transaksi, tanggal, no_bukti, akun_kas_bank, akun_lawan, jumlah, deskripsi) 
                      VALUES (:tenant_id, :id_jurnal, :tipe, :tgl, :no_bukti, :akun_kas, :akun_lawan, :jumlah, :deskripsi)";
            $this->db->query($query);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->bind('id_jurnal', $id_jurnal);
            $this->db->bind('tipe', $data['tipe_transaksi']);
            $this->db->bind('tgl', $data['tanggal']);
            $this->db->bind('no_bukti', $data['no_bukti']);
            $this->db->bind('akun_kas', $data['akun_kas_bank']);
            $this->db->bind('akun_lawan', $data['akun_lawan']);
            $this->db->bind('jumlah', $data['jumlah']);
            $this->db->bind('deskripsi', $data['deskripsi']);
            $this->db->execute();

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            Flash::setFlash($e->getMessage(), 'danger');
            return false;
        }
    }

    public function updateTransaksi($data, $tenant_id) {
        $jurnalModel = new Jurnal_model($this->db);
        $this->db->beginTransaction();
        try {
            $transaksiLama = $this->getTransaksiById($data['id_transaksi'], $tenant_id);
            if ($transaksiLama && $transaksiLama['id_jurnal']) {
                $jurnalModel->hapusJurnal($transaksiLama['id_jurnal'], $tenant_id, true);
            }

            $debit_akun = ($data['tipe_transaksi'] == 'Masuk') ? $data['akun_kas_bank'] : $data['akun_lawan'];
            $kredit_akun = ($data['tipe_transaksi'] == 'Masuk') ? $data['akun_lawan'] : $data['akun_kas_bank'];
            $jurnalData = [
                'no_transaksi' => $data['no_bukti'],
                'tanggal' => $data['tanggal'],
                'deskripsi' => $data['deskripsi'],
                'sumber_jurnal' => 'Kas & Bank',
                'details' => [
                    ['kode_akun' => $debit_akun, 'debit' => $data['jumlah'], 'kredit' => 0],
                    ['kode_akun' => $kredit_akun, 'debit' => 0, 'kredit' => $data['jumlah']]
                ]
            ];
            $id_jurnal_baru = $jurnalModel->simpanJurnal($jurnalData, $tenant_id);
            if ($id_jurnal_baru == 0) throw new Exception("Gagal membuat jurnal baru saat update.");

            $this->db->query("UPDATE jurnal_umum SET is_locked = 1 WHERE id_jurnal = :id AND tenant_id = :tenant_id");
            $this->db->bind('id', $id_jurnal_baru);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->execute();
            
            $query = "UPDATE {$this->table} SET 
                        id_jurnal = :id_jurnal, tipe_transaksi = :tipe, tanggal = :tgl, 
                        no_bukti = :no_bukti, akun_kas_bank = :akun_kas, akun_lawan = :akun_lawan, 
                        jumlah = :jumlah, deskripsi = :deskripsi
                      WHERE id_transaksi = :id_transaksi AND tenant_id = :tenant_id";
            $this->db->query($query);
            $this->db->bind('id_jurnal', $id_jurnal_baru);
            $this->db->bind('tipe', $data['tipe_transaksi']);
            $this->db->bind('tgl', $data['tanggal']);
            $this->db->bind('no_bukti', $data['no_bukti']);
            $this->db->bind('akun_kas', $data['akun_kas_bank']);
            $this->db->bind('akun_lawan', $data['akun_lawan']);
            $this->db->bind('jumlah', $data['jumlah']);
            $this->db->bind('deskripsi', $data['deskripsi']);
            $this->db->bind('id_transaksi', $data['id_transaksi']);
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

    public function hapusTransaksi($id, $tenant_id) {
        $this->db->beginTransaction();
        try {
            $transaksi = $this->getTransaksiById($id, $tenant_id);
            if (!$transaksi) throw new Exception('Transaksi kas tidak ditemukan.');

            $this->db->query("DELETE FROM {$this->table} WHERE id_transaksi = :id AND tenant_id = :tenant_id");
            $this->db->bind('id', $id);
            $this->db->bind('tenant_id', $tenant_id);
            $this->db->execute();

            if ($transaksi['id_jurnal']) {
                $jurnalModel = new Jurnal_model($this->db);
                $jurnalModel->hapusJurnal($transaksi['id_jurnal'], $tenant_id, true);
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
