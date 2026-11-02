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
        $tenantId = $this->tenantId();
        $data['judul'] = 'Daftar Aset Tetap';
        $data['aset'] = $this->model('Aset')->getAllAset($tenantId);
        
        $this->view('templates/header', $data);
        $this->view('aset/index', $data);
        $this->view('templates/footer');
    }

    public function tambah() {
        $tenantId = $this->tenantId();
        $data['judul'] = 'Tambah Aset Tetap';
        $data['akun'] = $this->model('Akun')->getAllAkun($tenantId);
        $data['kode_otomatis'] = $this->generateAutoNumber('AST', 'aset_tetap', 'kode_aset', $tenantId);
        
        // Ambil pengaturan default dari perusahaan
        $data['perusahaan'] = $this->model('Perusahaan')->getPerusahaan($tenantId);

        $this->view('templates/header', $data);
        $this->view('aset/tambah', $data);
        $this->view('templates/footer');
    }

    public function simpan() {
        try {
            $tenantId = $this->tenantId();
            if (!$tenantId) {
                $user = Auth::user();
                $tenantId = $user['tenant_id'] ?? 1;
            }

            if ($this->model('Aset')->simpanAset($_POST, $tenantId) > 0) {
                Flash::setFlash('Aset tetap berhasil ditambahkan.', 'success');
            } else {
                Flash::setFlash('Gagal menambahkan aset tetap.', 'danger');
            }
        } catch (Throwable $e) {
            error_log("Error Aset::simpan: " . $e->getMessage());
            Flash::setFlash('Terjadi kesalahan saat menyimpan aset: ' . $e->getMessage(), 'danger');
        }
        header('Location: ' . BASEURL . '/aset');
        exit;
    }

    public function edit($id) {
        $tenantId = $this->tenantId();
        $data['judul'] = 'Edit Aset Tetap';
        $data['aset'] = $this->model('Aset')->getAsetById($id, $tenantId);
        if (!$data['aset']) {
            Flash::setFlash('Data aset tetap tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/aset');
            exit;
        }
        $data['akun'] = $this->model('Akun')->getAllAkun($tenantId);

        $this->view('templates/header', $data);
        $this->view('aset/edit', $data);
        $this->view('templates/footer');
    }

    public function update() {
        try {
            $tenantId = $this->tenantId();
            if (!$tenantId) {
                $user = Auth::user();
                $tenantId = $user['tenant_id'] ?? 1;
            }

            if ($this->model('Aset')->updateAset($_POST, $tenantId) >= 0) {
                Flash::setFlash('Aset tetap berhasil diperbarui.', 'success');
            } else {
                Flash::setFlash('Gagal memperbarui aset tetap.', 'danger');
            }
        } catch (Throwable $e) {
            error_log("Error Aset::update: " . $e->getMessage());
            Flash::setFlash('Terjadi kesalahan saat memperbarui aset: ' . $e->getMessage(), 'danger');
        }
        header('Location: ' . BASEURL . '/aset');
        exit;
    }

    public function hapus($id) {
        try {
            $tenantId = $this->tenantId();
            if ($this->model('Aset')->hapusAset($id, $tenantId) > 0) {
                Flash::setFlash('Aset tetap berhasil dihapus.', 'success');
            } else {
                Flash::setFlash('Gagal menghapus aset tetap.', 'danger');
            }
        } catch (Throwable $e) {
            error_log("Error Aset::hapus: " . $e->getMessage());
            Flash::setFlash('Gagal menghapus aset: ' . $e->getMessage(), 'danger');
        }
        header('Location: ' . BASEURL . '/aset');
        exit;
    }

    public function susutkan() {
        try {
            $tenantId = $this->tenantId();
            if (!$tenantId) {
                $user = Auth::user();
                $tenantId = $user['tenant_id'] ?? 1;
            }

            $bulan = $_POST['bulan'] ?? date('m');
            $tahun = $_POST['tahun'] ?? date('Y');
            
            $count = $this->model('Aset')->prosesPenyusutan($bulan, $tahun, $tenantId);
            
            if ($count > 0) {
                Flash::setFlash($count . ' aset berhasil disusutkan untuk periode ' . $bulan . '/' . $tahun, 'success');
            } else {
                Flash::setFlash('Tidak ada aset yang perlu disusutkan atau semua aset sudah disusutkan untuk periode ini.', 'info');
            }
        } catch (Throwable $e) {
            error_log("Error Aset::susutkan: " . $e->getMessage());
            Flash::setFlash('Terjadi kesalahan saat memproses penyusutan: ' . $e->getMessage(), 'danger');
        }
        header('Location: ' . BASEURL . '/aset');
        exit;
    }
}
