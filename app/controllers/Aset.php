<?php

class Aset extends Controller {

    public function __construct() {
        parent::__construct();
        if (!Auth::isLoggedIn()) {
            header('Location: ' . BASEURL . '/login');
            exit;
        }
    }

    public function index() {
        $data['judul'] = 'Daftar Aset Tetap';
        $data['aset'] = $this->model('Aset')->getAllAset($this->tenantId());
        
        $this->view('templates/header', $data);
        $this->view('aset/index', $data);
        $this->view('templates/footer');
    }

    public function tambah() {
        $data['judul'] = 'Tambah Aset Tetap';
        $data['akun'] = $this->model('Akun')->getAllAkun($this->tenantId());
        $data['kode_otomatis'] = $this->generateAutoNumber('AST', 'aset_tetap', 'kode_aset', $this->tenantId());
        
        // Ambil pengaturan default dari perusahaan
        $data['perusahaan'] = $this->model('Perusahaan')->getPerusahaan($this->tenantId());

        $this->view('templates/header', $data);
        $this->view('aset/tambah', $data);
        $this->view('templates/footer');
    }

    public function simpan() {
        if ($this->model('Aset')->simpanAset($_POST, $this->tenantId()) > 0) {
            Flash::setFlash('Aset tetap berhasil ditambahkan.', 'success');
        } else {
            Flash::setFlash('Gagal menambahkan aset tetap.', 'danger');
        }
        header('Location: ' . BASEURL . '/aset');
        exit;
    }

    public function edit($id) {
        $data['judul'] = 'Edit Aset Tetap';
        $data['aset'] = $this->model('Aset')->getAsetById($id, $this->tenantId());
        $data['akun'] = $this->model('Akun')->getAllAkun($this->tenantId());

        $this->view('templates/header', $data);
        $this->view('aset/edit', $data);
        $this->view('templates/footer');
    }

    public function update() {
        if ($this->model('Aset')->updateAset($_POST, $this->tenantId()) > 0) {
            Flash::setFlash('Aset tetap berhasil diperbarui.', 'success');
        }
        header('Location: ' . BASEURL . '/aset');
        exit;
    }

    public function hapus($id) {
        if ($this->model('Aset')->hapusAset($id, $this->tenantId()) > 0) {
            Flash::setFlash('Aset tetap berhasil dihapus.', 'success');
        }
        header('Location: ' . BASEURL . '/aset');
        exit;
    }

    public function susutkan() {
        $bulan = $_POST['bulan'] ?? date('m');
        $tahun = $_POST['tahun'] ?? date('Y');
        
        $count = $this->model('Aset')->prosesPenyusutan($bulan, $tahun, $this->tenantId());
        
        if ($count > 0) {
            Flash::setFlash($count . ' aset berhasil disusutkan untuk periode ' . $bulan . '/' . $tahun, 'success');
        } else {
            Flash::setFlash('Tidak ada aset yang perlu disusutkan atau semua aset sudah disusutkan untuk periode ini.', 'info');
        }
        header('Location: ' . BASEURL . '/aset');
        exit;
    }
}
