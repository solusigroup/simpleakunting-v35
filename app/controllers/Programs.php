<?php

class Programs extends Controller
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
        if (!Auth::hasPermission('menu_programs')) {
            Flash::setFlash('Akses Ditolak', 'Anda tidak memiliki izin untuk mengakses menu ini', 'danger');
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }
        $data['judul'] = 'Manajemen Program & Sumber Dana';
        $data['programs'] = $this->model('Program')->getAllPrograms($this->tenantId());

        $this->view('templates/header', $data);
        $this->view('programs/index', $data);
        $this->view('templates/footer');
    }

    public function tambah()
    {
        if (!Auth::hasPermission('trx_programs_manage')) {
            Flash::setFlash('Akses Ditolak', 'Anda tidak memiliki izin untuk mengelola data program', 'danger');
            header('Location: ' . BASEURL . '/programs');
            exit;
        }
        if ($this->model('Program')->tambahProgram($_POST, $this->tenantId()) > 0) {
            Flash::setFlash('Berhasil', 'Program baru telah ditambahkan', 'success');
        } else {
            Flash::setFlash('Gagal', 'Terjadi kesalahan saat menambah program', 'danger');
        }
        header('Location: ' . BASEURL . '/programs');
    }

    public function ubah()
    {
        if (!Auth::hasPermission('trx_programs_manage')) {
            Flash::setFlash('Akses Ditolak', 'Anda tidak memiliki izin untuk mengelola data program', 'danger');
            header('Location: ' . BASEURL . '/programs');
            exit;
        }
        if ($this->model('Program')->ubahProgram($_POST, $this->tenantId()) > 0) {
            Flash::setFlash('Berhasil', 'Data program telah diperbarui', 'success');
        } else {
            Flash::setFlash('Gagal', 'Tidak ada perubahan data', 'info');
        }
        header('Location: ' . BASEURL . '/programs');
    }

    public function hapus($id)
    {
        if (!Auth::hasPermission('trx_programs_manage')) {
            Flash::setFlash('Akses Ditolak', 'Anda tidak memiliki izin untuk mengelola data program', 'danger');
            header('Location: ' . BASEURL . '/programs');
            exit;
        }
        if ($this->model('Program')->hapusProgram($id, $this->tenantId()) > 0) {
            Flash::setFlash('Berhasil', 'Program telah dihapus', 'success');
        } else {
            Flash::setFlash('Gagal', 'Gagal menghapus program', 'danger');
        }
        header('Location: ' . BASEURL . '/programs');
    }

    public function realisasi($id)
    {
        if (!Auth::hasPermission('rep_programs_realization')) {
            Flash::setFlash('Akses Ditolak', 'Anda tidak memiliki izin untuk melihat laporan realisasi program', 'danger');
            header('Location: ' . BASEURL . '/programs');
            exit;
        }
        $data['judul'] = 'Laporan Realisasi Penggunaan Dana';
        $data['realisasi'] = $this->model('Program')->getProgramRealization($id, $this->tenantId());

        if (!$data['realisasi']) {
            Flash::setFlash('Gagal', 'Program tidak ditemukan', 'danger');
            header('Location: ' . BASEURL . '/programs');
            exit;
        }

        $data['perusahaan'] = $this->model('Perusahaan')->getPerusahaan($this->tenantId());

        $this->view('templates/header', $data);
        $this->view('programs/realisasi', $data);
        $this->view('templates/footer');
    }
}
