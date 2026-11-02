<?php

class Pemasok extends Controller {
    public function __construct() {
        parent::__construct();
        if (!Auth::isLoggedIn()) {
            Flash::setFlash('Silakan login terlebih dahulu.', 'warning');
            header('Location: ' . BASEURL . '/login');
            exit;
        }
        if (!Auth::hasPermission('master_pemasok') && !Auth::isAdmin() && !Auth::isManager()) {
            Flash::setFlash('Akses Ditolak', 'Anda tidak memiliki izin untuk mengakses data pemasok', 'danger');
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }
    }

    public function index() {
        $tenantId = $this->tenantId();
        if (empty($tenantId)) {
            if (Auth::isActuallySuperadmin()) {
                Flash::setFlash('Pilih Tenant Terlebih Dahulu', 'Silakan pilih tenant di Manajemen Tenant untuk mengelola Data Pemasok.', 'info');
                header('Location: ' . BASEURL . '/tenants');
                exit;
            } else {
                Flash::setFlash('Akses Ditolak', 'Tenant tidak valid.', 'danger');
                header('Location: ' . BASEURL . '/dashboard');
                exit;
            }
        }
        $data['judul'] = 'Data Pemasok';
        $pemasok = $this->model('Pemasok')->getAllPemasok($tenantId);
        $data['pemasok'] = is_array($pemasok) ? $pemasok : [];
        $this->view('templates/header', $data);
        $this->view('pemasok/index', $data);
        $this->view('templates/footer');
    }

    public function tambah() {
        $data['judul'] = 'Tambah Data Pemasok';
        $this->view('templates/header', $data);
        $this->view('pemasok/tambah');
        $this->view('templates/footer');
    }

    public function simpan() {
        if ($this->model('Pemasok')->tambahDataPemasok($_POST, $this->tenantId()) > 0) {
            Flash::setFlash('Data pemasok berhasil ditambahkan.', 'success');
        } else {
            Flash::setFlash('Gagal menambahkan data pemasok.', 'danger');
        }
        header('Location: ' . BASEURL . '/pemasok');
        exit;
    }

    public function edit($id) {
        $data['judul'] = 'Edit Data Pemasok';
        $data['pemasok'] = $this->model('Pemasok')->getPemasokById($id, $this->tenantId());
        $this->view('templates/header', $data);
        $this->view('pemasok/edit', $data);
        $this->view('templates/footer');
    }

    public function update() {
        if ($this->model('Pemasok')->ubahDataPemasok($_POST, $this->tenantId()) > 0) {
            Flash::setFlash('Data pemasok berhasil diubah.', 'success');
        } else {
            Flash::setFlash('Gagal mengubah data pemasok.', 'danger');
        }
        header('Location: ' . BASEURL . '/pemasok');
        exit;
    }

    public function hapus($id) {
        if (!Auth::isAdmin() && !Auth::isManager()) {
            Flash::setFlash('Anda tidak memiliki hak akses untuk tindakan ini.', 'danger');
            header('Location: ' . BASEURL . '/pemasok');
            exit;
        }
        
        if ($this->model('Pemasok')->hapusDataPemasok($id, $this->tenantId()) > 0) {
            Flash::setFlash('Data pemasok berhasil dihapus.', 'success');
        } else {
            Flash::setFlash('Gagal menghapus data pemasok.', 'danger');
        }
        header('Location: ' . BASEURL . '/pemasok');
        exit;
    }
}
