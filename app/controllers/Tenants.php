<?php

class Tenants extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!Auth::isLoggedIn() || !Auth::isActuallySuperadmin()) {
            Flash::setFlash('Akses Ditolak', 'Hanya Superadmin yang bisa mengakses halaman ini', 'danger');
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }
    }

    public function index()
    {
        $data['judul'] = 'Manajemen Tenant';
        $data['tenants'] = $this->model('Tenants')->getAllTenants();

        $this->view('templates/header', $data);
        $this->view('tenants/index', $data);
        $this->view('templates/footer');
    }

    public function tambah()
    {
        $tenant_id = $this->model('Tenants')->tambahTenant($_POST);
        if ($tenant_id > 0) {
            // Auto-generate COA from Central Template
            $this->model('Akun_model')->generateFromCentral($tenant_id);
            
            Flash::setFlash('Berhasil', 'Tenant baru telah ditambahkan dan COA telah diinisialisasi.', 'success');
        } else {
            Flash::setFlash('Gagal', 'Terjadi kesalahan saat menambah tenant', 'danger');
        }
        header('Location: ' . BASEURL . '/tenants');
    }

    public function ubah()
    {
        if ($this->model('Tenants')->ubahTenant($_POST) > 0) {
            Flash::setFlash('Berhasil', 'Data tenant telah diperbarui', 'success');
        } else {
            Flash::setFlash('Gagal', 'Tidak ada perubahan data', 'info');
        }
        header('Location: ' . BASEURL . '/tenants');
    }

    public function hapus($id)
    {
        if ($this->model('Tenants')->hapusTenant($id) > 0) {
            Flash::setFlash('Berhasil', 'Tenant telah dihapus', 'success');
        } else {
            Flash::setFlash('Gagal', 'Gagal menghapus tenant', 'danger');
        }
        header('Location: ' . BASEURL . '/tenants');
    }

    /**
     * Fitur impersonasi untuk Superadmin agar bisa masuk ke dashboard tenant spesifik.
     */
    public function switch($id)
    {
        $tenant = $this->model('Tenants')->getTenantById($id);
        if ($tenant) {
            // Simpan identitas Superadmin asli agar bisa balik (Pola yang sama dengan Auth::impersonate)
            if (!isset($_SESSION['original_user'])) {
                $_SESSION['original_user'] = [
                    'id' => $_SESSION['user_id'],
                    'name' => $_SESSION['user_name'],
                    'role' => $_SESSION['user_role']
                ];
            }

            $_SESSION['tenant_id'] = $tenant['id'];
            $_SESSION['tenant_name'] = $tenant['name'];
            $_SESSION['database_type'] = $tenant['database_type'];
            $_SESSION['impersonating'] = true;

            Flash::setFlash('Berhasil', 'Anda sekarang berada di dashboard ' . $tenant['name'], 'success');
            header('Location: ' . BASEURL . '/dashboard');
        } else {
            Flash::setFlash('Gagal', 'Tenant tidak ditemukan', 'danger');
            header('Location: ' . BASEURL . '/tenants');
        }
        exit;
    }

    public function central()
    {
        Auth::stopImpersonating();

        Flash::setFlash('Berhasil', 'Kembali ke Dashboard Central', 'info');
        header('Location: ' . BASEURL . '/dashboard');
        exit;
    }
}
