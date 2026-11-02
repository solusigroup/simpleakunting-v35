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
        if (!Auth::hasPermission('menu_programs') && !Auth::isAdmin() && !Auth::isManager()) {
            Flash::setFlash('Akses Ditolak', 'Anda tidak memiliki izin untuk mengakses menu ini', 'danger');
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }

        $tenantId = $this->tenantId();
        if (empty($tenantId)) {
            if (Auth::isActuallySuperadmin()) {
                Flash::setFlash('Pilih Tenant Terlebih Dahulu', 'Silakan pilih tenant di Manajemen Tenant untuk mengelola data Program & Dana.', 'info');
                header('Location: ' . BASEURL . '/tenants');
                exit;
            } else {
                Flash::setFlash('Akses Ditolak', 'Tenant tidak valid.', 'danger');
                header('Location: ' . BASEURL . '/dashboard');
                exit;
            }
        }

        $data['judul'] = 'Manajemen Program & Sumber Dana';
        $programs = $this->model('Program')->getAllPrograms($tenantId);
        $data['programs'] = is_array($programs) ? $programs : [];

        $this->view('templates/header', $data);
        $this->view('programs/index', $data);
        $this->view('templates/footer');
    }

    public function tambah()
    {
        if (!Auth::hasPermission('trx_programs_manage') && !Auth::isAdmin() && !Auth::isManager()) {
            Flash::setFlash('Akses Ditolak', 'Anda tidak memiliki izin untuk mengelola data program', 'danger');
            header('Location: ' . BASEURL . '/programs');
            exit;
        }
        $tenantId = $this->tenantId();
        if (empty($tenantId)) {
            Flash::setFlash('Tenant tidak valid', 'Pilih tenant terlebih dahulu', 'danger');
            header('Location: ' . BASEURL . '/programs');
            exit;
        }
        if ($this->model('Program')->tambahProgram($_POST, $tenantId) > 0) {
            Flash::setFlash('Berhasil', 'Program baru telah ditambahkan', 'success');
        } else {
            Flash::setFlash('Gagal', 'Terjadi kesalahan saat menambah program', 'danger');
        }
        header('Location: ' . BASEURL . '/programs');
    }

    public function ubah()
    {
        if (!Auth::hasPermission('trx_programs_manage') && !Auth::isAdmin() && !Auth::isManager()) {
            Flash::setFlash('Akses Ditolak', 'Anda tidak memiliki izin untuk mengelola data program', 'danger');
            header('Location: ' . BASEURL . '/programs');
            exit;
        }
        $tenantId = $this->tenantId();
        if (empty($tenantId)) {
            Flash::setFlash('Tenant tidak valid', 'Pilih tenant terlebih dahulu', 'danger');
            header('Location: ' . BASEURL . '/programs');
            exit;
        }
        if ($this->model('Program')->ubahProgram($_POST, $tenantId) > 0) {
            Flash::setFlash('Berhasil', 'Data program telah diperbarui', 'success');
        } else {
            Flash::setFlash('Gagal', 'Tidak ada perubahan data', 'info');
        }
        header('Location: ' . BASEURL . '/programs');
    }

    public function hapus($id)
    {
        if (!Auth::hasPermission('trx_programs_manage') && !Auth::isAdmin() && !Auth::isManager()) {
            Flash::setFlash('Akses Ditolak', 'Anda tidak memiliki izin untuk mengelola data program', 'danger');
            header('Location: ' . BASEURL . '/programs');
            exit;
        }
        $tenantId = $this->tenantId();
        if (empty($tenantId)) {
            Flash::setFlash('Tenant tidak valid', 'Pilih tenant terlebih dahulu', 'danger');
            header('Location: ' . BASEURL . '/programs');
            exit;
        }
        if ($this->model('Program')->hapusProgram($id, $tenantId) > 0) {
            Flash::setFlash('Berhasil', 'Program telah dihapus', 'success');
        } else {
            Flash::setFlash('Gagal', 'Gagal menghapus program', 'danger');
        }
        header('Location: ' . BASEURL . '/programs');
    }

    public function realisasi($id)
    {
        if (!Auth::hasPermission('rep_programs_realization') && !Auth::isAdmin() && !Auth::isManager()) {
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
