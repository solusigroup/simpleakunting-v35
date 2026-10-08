<?php

class Tenants extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // Izinkan Superadmin dan Penyelia Wilayah
        if (!Auth::isLoggedIn() || !Auth::isActuallySuperadmin()) {
            Flash::setFlash('Akses Ditolak', 'Hanya Superadmin/Penyelia Wilayah yang bisa mengakses halaman ini', 'danger');
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }
    }

    public function index()
    {
        $data['judul'] = 'Manajemen Tenant';
        
        // Penyelia Wilayah hanya melihat tenant di klusternya
        if (Auth::isPenyeliaWilayah()) {
            $klusterId = Auth::getKlusterWilayahId();
            $data['tenants'] = $this->model('Tenants')->getTenantsByKluster($klusterId);
            $data['kluster_info'] = $this->model('KlusterWilayah')->getKlusterById($klusterId);
        } else {
            $data['tenants'] = $this->model('Tenants')->getAllTenants();
        }
        
        // Data kluster untuk dropdown
        $data['klusters'] = $this->model('KlusterWilayah')->getActiveKluster();
        $data['is_penyelia'] = Auth::isPenyeliaWilayah();
        $data['penyelia_kluster_id'] = Auth::getKlusterWilayahId();

        $this->view('templates/header', $data);
        $this->view('tenants/index', $data);
        $this->view('templates/footer');
    }

    public function tambah()
    {
        // Penyelia Wilayah: auto-set kluster_wilayah_id
        if (Auth::isPenyeliaWilayah()) {
            $_POST['kluster_wilayah_id'] = Auth::getKlusterWilayahId();
        }

        if ($this->model('Tenants')->tambahTenant($_POST) > 0) {
            Flash::setFlash('Berhasil', 'Tenant baru telah ditambahkan', 'success');
        } else {
            Flash::setFlash('Gagal', 'Terjadi kesalahan saat menambah tenant', 'danger');
        }
        header('Location: ' . BASEURL . '/tenants');
    }

    public function ubah()
    {
        // Penyelia Wilayah: pastikan tenant yang diedit ada di klusternya
        if (Auth::isPenyeliaWilayah()) {
            $tenant = $this->model('Tenants')->getTenantById($_POST['id']);
            if (!$tenant || $tenant['kluster_wilayah_id'] != Auth::getKlusterWilayahId()) {
                Flash::setFlash('Akses Ditolak', 'Anda hanya bisa mengedit tenant di kluster wilayah Anda', 'danger');
                header('Location: ' . BASEURL . '/tenants');
                return;
            }
            $_POST['kluster_wilayah_id'] = Auth::getKlusterWilayahId();
        }

        if ($this->model('Tenants')->ubahTenant($_POST) > 0) {
            Flash::setFlash('Berhasil', 'Data tenant telah diperbarui', 'success');
        } else {
            Flash::setFlash('Gagal', 'Tidak ada perubahan data', 'info');
        }
        header('Location: ' . BASEURL . '/tenants');
    }

    public function hapus($id)
    {
        // Penyelia Wilayah: pastikan tenant yang dihapus ada di klusternya
        if (Auth::isPenyeliaWilayah()) {
            $tenant = $this->model('Tenants')->getTenantById($id);
            if (!$tenant || $tenant['kluster_wilayah_id'] != Auth::getKlusterWilayahId()) {
                Flash::setFlash('Akses Ditolak', 'Anda hanya bisa menghapus tenant di kluster wilayah Anda', 'danger');
                header('Location: ' . BASEURL . '/tenants');
                return;
            }
        }

        if ($this->model('Tenants')->hapusTenant($id) > 0) {
            Flash::setFlash('Berhasil', 'Tenant telah dihapus', 'success');
        } else {
            Flash::setFlash('Gagal', 'Gagal menghapus tenant', 'danger');
        }
        header('Location: ' . BASEURL . '/tenants');
    }

    /**
     * Fitur impersonasi untuk Superadmin/Penyelia Wilayah agar bisa masuk ke dashboard tenant spesifik.
     */
    public function switch($id)
    {
        $tenant = $this->model('Tenants')->getTenantById($id);
        
        // Penyelia Wilayah: pastikan tenant ada di klusternya
        if (Auth::isPenyeliaWilayah() && $tenant) {
            if ($tenant['kluster_wilayah_id'] != Auth::getKlusterWilayahId()) {
                Flash::setFlash('Akses Ditolak', 'Tenant ini bukan di kluster wilayah Anda', 'danger');
                header('Location: ' . BASEURL . '/tenants');
                exit;
            }
        }

        if ($tenant) {
            // Simpan identitas asli agar bisa balik
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
