<?php

class Units extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!Auth::isLoggedIn()) {
            header('Location: ' . BASEURL . '/login');
            exit;
        }
    }

    public function index()
    {
        if (!Auth::hasPermission('menu_units')) {
            Flash::setFlash('Akses Ditolak', 'Anda tidak memiliki izin untuk mengakses menu ini', 'danger');
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }
        $data['judul'] = 'Manajemen Unit Usaha';
        $data['units'] = $this->model('Unit')->getAllUnits($this->tenantId());

        $this->view('templates/header', $data);
        $this->view('units/index', $data);
        $this->view('templates/footer');
    }

    public function tambah()
    {
        if (!Auth::hasPermission('trx_units_manage')) {
            Flash::setFlash('Akses Ditolak', 'Anda tidak memiliki izin untuk mengelola data unit usaha', 'danger');
            header('Location: ' . BASEURL . '/units');
            exit;
        }
        if ($this->model('Unit')->tambahUnit($_POST, $this->tenantId()) > 0) {
            Flash::setFlash('Berhasil', 'Unit usaha baru telah ditambahkan', 'success');
        } else {
            Flash::setFlash('Gagal', 'Terjadi kesalahan saat menambah unit usaha', 'danger');
        }
        header('Location: ' . BASEURL . '/units');
    }

    public function ubah()
    {
        if (!Auth::hasPermission('trx_units_manage')) {
            Flash::setFlash('Akses Ditolak', 'Anda tidak memiliki izin untuk mengelola data unit usaha', 'danger');
            header('Location: ' . BASEURL . '/units');
            exit;
        }
        if ($this->model('Unit')->ubahUnit($_POST, $this->tenantId()) > 0) {
            Flash::setFlash('Berhasil', 'Data unit usaha telah diperbarui', 'success');
        } else {
            Flash::setFlash('Gagal', 'Tidak ada perubahan data', 'info');
        }
        header('Location: ' . BASEURL . '/units');
    }

    public function hapus($id)
    {
        if (!Auth::hasPermission('trx_units_manage')) {
            Flash::setFlash('Akses Ditolak', 'Anda tidak memiliki izin untuk mengelola data unit usaha', 'danger');
            header('Location: ' . BASEURL . '/units');
            exit;
        }
        if ($this->model('Unit')->hapusUnit($id, $this->tenantId()) > 0) {
            Flash::setFlash('Berhasil', 'Unit usaha telah dihapus', 'success');
        } else {
            Flash::setFlash('Gagal', 'Gagal menghapus unit usaha. Unit utama tidak dapat dihapus.', 'danger');
        }
        header('Location: ' . BASEURL . '/units');
    }
}
