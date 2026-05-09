<?php

class Penjualan extends Controller {
    use PeriodLockTrait;

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['judul'] = 'Jurnal Penjualan';
        $data['penjualan'] = $this->model('Penjualan')->getAllPenjualan($this->tenantId());
        $this->view('templates/header', $data);
        $this->view('penjualan/index', $data);
        $this->view('templates/footer');
    }

    public function tambah() {
        $data['judul'] = 'Tambah Penjualan Baru';
        $data['pelanggan'] = $this->model('Pelanggan')->getAllPelanggan($this->tenantId());
        $data['barang'] = $this->model('Persediaan')->getAllBarang($this->tenantId());
        $data['akun_kas'] = $this->model('Akun')->getAkunKasBank($this->tenantId());
        $data['perusahaan'] = $this->model('Perusahaan')->getPerusahaan($this->tenantId());
        $data['no_faktur'] = $this->generateAutoNumber('PJ', 'penjualan', 'no_faktur', $this->tenantId());
        
        $this->view('templates/header', $data);
        $this->view('penjualan/tambah', $data);
        $this->view('templates/footer');
    }

    public function simpan() {
        // PERLINDUNGAN: Periksa tanggal transaksi sebelum menyimpan
        $this->checkPeriodLock($_POST['tanggal_faktur'], BASEURL . '/penjualan');
        
        $pelanggan = $this->model('Pelanggan')->getPelangganById($_POST['id_pelanggan'], $this->tenantId());
        $_POST['nama_pelanggan'] = $pelanggan['nama_pelanggan'];

        if ($this->model('Penjualan')->simpanPenjualan($_POST, $this->tenantId())) {
            Flash::setFlash('Transaksi penjualan berhasil disimpan dan dijurnal.', 'success');
            header('Location: ' . BASEURL . '/penjualan');
        } else {
            // Pesan error Flash (misalnya, stok tidak cukup) sudah di-set di dalam Model
            header('Location: ' . BASEURL . '/penjualan/tambah');
        }
        exit;
    }

    public function lihat($id) {
        $data['judul'] = 'Detail Faktur Penjualan';
        $data['penjualan'] = $this->model('Penjualan')->getPenjualanByIdWithDetails($id, $this->tenantId());
        if (!$data['penjualan']) {
            Flash::setFlash('Faktur penjualan tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/penjualan');
            exit;
        }
        $this->view('templates/header', $data);
        $this->view('penjualan/lihat', $data);
        $this->view('templates/footer');
    }

    public function hapus($id) {
        if (!Auth::isAdmin() && !Auth::isManager()) {
            Flash::setFlash('Anda tidak memiliki hak akses untuk tindakan ini.', 'danger');
            header('Location: ' . BASEURL . '/penjualan');
            exit;
        }
        
        // PERLINDUNGAN: Ambil data penjualan untuk mendapatkan tanggalnya, lalu periksa
        $penjualan = $this->model('Penjualan')->getPenjualanByIdWithDetails($id, $this->tenantId());
        if ($penjualan) {
            $this->checkPeriodLock($penjualan['tanggal_faktur'], BASEURL . '/penjualan');
        }

        if ($this->model('Penjualan')->hapusPenjualan($id, $this->tenantId())) {
            Flash::setFlash('Transaksi penjualan berhasil dibatalkan.', 'success');
        }
        header('Location: ' . BASEURL . '/penjualan');
        exit;
    }
}
