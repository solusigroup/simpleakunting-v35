<?php

class Login extends Controller {
    /**
     * Constructor ini sangat penting.
     * Ia memastikan koneksi database ($this->db) dibuat dengan memanggil
     * constructor dari Controller induk sebelum method lain di kelas ini dijalankan.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Menampilkan halaman login.
     * Jika pengguna sudah login, ia akan diarahkan ke dashboard.
     */
    public function index() {
        if (Auth::isLoggedIn()) {
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }
        $data['judul'] = 'Login';
        $this->view('login/login', $data);
    }

    /**
     * Memproses data yang dikirim dari form login.
     */
    public function process() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/login');
            exit;
        }

        $login_type = $_POST['login_type'] ?? 'tenant';
        $nama_user = trim($_POST['nama_user'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($nama_user) || empty($password)) {
            Flash::setFlash('Nama pengguna dan kata sandi wajib diisi.', 'warning');
            header('Location: ' . BASEURL . '/login');
            exit;
        }

        // 1. Ambil data user
        $user = $this->model('User')->getUserByUsername($nama_user);

        // 2. Verifikasi Password
        if (!$user || !password_verify($password, $user['password_hash'])) {
            Flash::setFlash('Nama pengguna atau sandi salah.', 'danger');
            header('Location: ' . BASEURL . '/login');
            exit;
        }

        // 3. Verifikasi berdasarkan jenis login
        if ($login_type === 'central') {
            // Login Central harus role Superadmin atau Penyelia Wilayah
            if ($user['role'] !== 'Superadmin' && $user['role'] !== 'Penyelia Wilayah') {
                Flash::setFlash('Akses Ditolak! Akun Anda tidak memiliki otoritas Central.', 'danger');
                header('Location: ' . BASEURL . '/login');
                exit;
            }
            // Pastikan login central tidak membawa context tenant
            $user['tenant_id'] = null;
            $user['tenant_name'] = 'SYSTEM CENTRAL';
            $user['database_type'] = 'dagang'; // Default dashboard type for central
        } else {
            // Login Tenant harus menyertakan kode bisnis yang benar
            $tenant_code = $_POST['tenant_code'] ?? '';
            $tenant = $this->model('Tenants')->getTenantByCode($tenant_code);

            if (!$tenant) {
                Flash::setFlash('Kode Bisnis tidak ditemukan atau tidak aktif.', 'danger');
                header('Location: ' . BASEURL . '/login');
                exit;
            }

            if ($user['tenant_id'] != $tenant['id']) {
                Flash::setFlash('Pengguna tidak terdaftar di bisnis ' . $tenant['name'], 'danger');
                header('Location: ' . BASEURL . '/login');
                exit;
            }
            
            // Simpan info tenant ke user session array
            $user['tenant_name'] = $tenant['name'];
            $user['database_type'] = $tenant['database_type'];
        }

        // 4. Ambil Izin Akses jika ada custom role
        $permissions = [];
        if (!empty($user['role_id'])) {
            $roleModel = $this->model('Role');
            $permissions = $roleModel->getRolePermissions($user['role_id']);
        }

        // Ambil info nama kluster wilayah jika Penyelia Wilayah
        if ($user['role'] === 'Penyelia Wilayah' && !empty($user['kluster_wilayah_id'])) {
            $kw = $this->model('KlusterWilayah')->getKlusterById($user['kluster_wilayah_id']);
            $user['kluster_wilayah_nama'] = $kw['nama_kabupaten'] ?? null;
        }

        // Jika semua lolos, atur sesi
        Auth::setUser($user, $permissions);
        Logger::log('LOGIN', 'Authentication', 'User successfully logged in.');
        header('Location: ' . BASEURL . '/dashboard');
        exit;
    }

    /**
     * Menangani proses logout.
     */
    public function logout() {
        Auth::logout();
        header('Location: ' . BASEURL);
        exit;
    }
}

