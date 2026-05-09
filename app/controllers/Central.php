<?php

class Central extends Controller {
    public function __construct() {
        parent::__construct();
        if (!Auth::isLoggedIn() || !Auth::isActuallySuperadmin()) {
            Flash::setFlash('Akses Ditolak', 'Hanya Superadmin yang bisa mengakses modul Central', 'danger');
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }
    }

    public function index() {
        header('Location: ' . BASEURL . '/dashboard');
        exit;
    }

    /**
     * Manajemen Pengguna Global
     */
    public function users() {
        $data['judul'] = 'Global User Management';
        $data['users'] = $this->model('User')->getAllUsers(); // Ambil semua tanpa tenant_id
        $data['tenants'] = $this->model('Tenants')->getAllTenants();
        
        $this->view('templates/header', $data);
        $this->view('central/users', $data);
        $this->view('templates/footer');
    }

    public function user_simpan() {
        if ($this->model('User')->tambahDataUser($_POST, $_POST['tenant_id']) > 0) {
            Flash::setFlash('Berhasil', 'User baru telah ditambahkan ke sistem', 'success');
        } else {
            Flash::setFlash('Gagal', 'Gagal menambahkan user baru', 'danger');
        }
        header('Location: ' . BASEURL . '/central/users');
        exit;
    }

    public function user_update() {
        if ($this->model('User')->ubahDataUser($_POST, $_POST['tenant_id']) > 0) {
            Flash::setFlash('Berhasil', 'Data user berhasil diperbarui', 'success');
        } else {
            Flash::setFlash('Gagal', 'Tidak ada perubahan data', 'info');
        }
        header('Location: ' . BASEURL . '/central/users');
        exit;
    }

    public function user_hapus($id) {
        // Jangan hapus diri sendiri
        if ($id == Auth::user()['id']) {
            Flash::setFlash('Gagal', 'Anda tidak bisa menghapus akun Anda sendiri', 'danger');
            header('Location: ' . BASEURL . '/central/users');
            exit;
        }

        // Cari user dulu untuk dapat tenant_id-nya
        $user = $this->model('User')->getUserById($id);
        if ($this->model('User')->hapusDataUser($id, $user['tenant_id']) > 0) {
            Flash::setFlash('Berhasil', 'User telah dihapus dari sistem', 'success');
        } else {
            Flash::setFlash('Gagal', 'Gagal menghapus user', 'danger');
        }
        header('Location: ' . BASEURL . '/central/users');
        exit;
    }

    public function user_impersonate($id) {
        $user = $this->model('User')->getUserById($id);
        if (!$user) {
            Flash::setFlash('Gagal', 'User tidak ditemukan', 'danger');
            header('Location: ' . BASEURL . '/central/users');
            exit;
        }

        // Jangan impersonate diri sendiri atau sesama superadmin
        if ($user['role'] == 'Superadmin') {
            Flash::setFlash('Gagal', 'Tidak bisa impersonate Superadmin', 'warning');
            header('Location: ' . BASEURL . '/central/users');
            exit;
        }

        Auth::impersonate($user);
        Flash::setFlash('Impersonation', 'Anda sekarang login sebagai ' . $user['nama_user'], 'info');
        header('Location: ' . BASEURL . '/dashboard');
        exit;
    }

    public function user_stop_impersonate() {
        if (Auth::stopImpersonating()) {
            Flash::setFlash('Berhasil', 'Anda telah kembali ke akun Superadmin', 'success');
        }
        header('Location: ' . BASEURL . '/central/users');
        exit;
    }

    /**
     * Manajemen Role Terpusat (RBAC)
     */
    public function roles() {
        $data['judul'] = 'Role Management';
        $data['roles'] = $this->model('Role')->getAllRoles();
        $data['permissions'] = $this->model('Role')->getAllPermissions();
        
        $this->view('templates/header', $data);
        $this->view('central/roles', $data);
        $this->view('templates/footer');
    }

    public function role_simpan() {
        if ($this->model('Role')->tambahRole($_POST)) {
            Flash::setFlash('Berhasil', 'Role baru telah ditambahkan', 'success');
        } else {
            Flash::setFlash('Gagal', 'Gagal menambahkan role', 'danger');
        }
        header('Location: ' . BASEURL . '/central/roles');
        exit;
    }

    public function role_update() {
        if ($this->model('Role')->updateRole($_POST)) {
            Flash::setFlash('Berhasil', 'Data role berhasil diperbarui', 'success');
        } else {
            Flash::setFlash('Gagal', 'Gagal memperbarui role', 'danger');
        }
        header('Location: ' . BASEURL . '/central/roles');
        exit;
    }

    public function role_hapus($id) {
        if ($this->model('Role')->hapusRole($id) > 0) {
            Flash::setFlash('Berhasil', 'Role telah dihapus', 'success');
        } else {
            Flash::setFlash('Gagal', 'Gagal menghapus role', 'danger');
        }
        header('Location: ' . BASEURL . '/central/roles');
        exit;
    }

    /**
     * Monitoring Transaksi Global (Semua Tenant)
     */
    public function monitoring() {
        $data['judul'] = 'Global Transaction Monitoring';
        
        // Ambil 50 transaksi terbaru dari semua tenant
        $this->db->query("SELECT ju.*, t.name as tenant_name, 
                            COALESCE(SUM(jd.debit), 0) as total
                          FROM jurnal_umum ju
                          JOIN tenants t ON ju.tenant_id = t.id
                          LEFT JOIN jurnal_detail jd ON ju.id_jurnal = jd.id_jurnal
                          GROUP BY ju.id_jurnal
                          ORDER BY ju.created_at DESC 
                          LIMIT 50");
        $data['transactions'] = $this->db->resultSet();
        
        $this->view('templates/header', $data);
        $this->view('central/monitoring', $data);
        $this->view('templates/footer');
    }

    /**
     * Laporan Agregat Global
     */
    public function agregat() {
        $data['judul'] = 'Global Aggregate Report';
        $dashboardModel = $this->model('Dashboard');
        
        $data['summary'] = $dashboardModel->getCentralSummary();
        $data['tenants_report'] = $this->model('Tenants')->getAllTenantsWithStats();
        
        $this->view('templates/header', $data);
        $this->view('central/agregat', $data);
        $this->view('templates/footer');
    }
}
