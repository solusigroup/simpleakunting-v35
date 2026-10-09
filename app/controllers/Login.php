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
            // Login Tenant
            $tenant_code = trim($_POST['tenant_code'] ?? '');
            
            // Kemudahan Superadmin: Jika Superadmin login di tab Tenant tanpa kode bisnis, otomatis masuk Central
            if (($user['role'] === 'Superadmin' || $user['role'] === 'Penyelia Wilayah') && empty($tenant_code)) {
                $user['tenant_id'] = null;
                $user['tenant_name'] = 'SYSTEM CENTRAL';
                $user['database_type'] = 'dagang';
            } else {
                $tenant = $this->model('Tenants')->getTenantByCode($tenant_code);

                if (!$tenant) {
                    Flash::setFlash('Kode Bisnis tidak ditemukan atau tidak aktif.', 'danger');
                    header('Location: ' . BASEURL . '/login');
                    exit;
                }

                // Superadmin memiliki hak akses universal ke semua tenant
                if ($user['role'] !== 'Superadmin' && $user['tenant_id'] != $tenant['id']) {
                    Flash::setFlash('Pengguna tidak terdaftar di bisnis ' . $tenant['name'], 'danger');
                    header('Location: ' . BASEURL . '/login');
                    exit;
                }
                
                // Simpan info tenant ke user session array
                $user['tenant_id'] = $tenant['id'];
                $user['tenant_name'] = $tenant['name'];
                $user['database_type'] = $tenant['database_type'];
            }
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

        // Bersihkan flash error sisa percobaan login yang gagal sebelumnya
        unset($_SESSION['flash']);

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

    /**
     * Endpoint Darurat: Reset & Pastikan Akun Superadmin
     * URL: /login/reset_superadmin
     */
    public function reset_superadmin() {
        unset($_SESSION['flash']); // Bersihkan notifikasi error lama
        $username = 'superadmin';
        $password = $_REQUEST['pass'] ?? 'admin123';
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $this->db->query("SELECT * FROM users WHERE nama_user = :user");
        $this->db->bind('user', $username);
        $existing = $this->db->single();

        if ($existing) {
            $this->db->query("UPDATE users SET password_hash = :hash, role = 'Superadmin', jabatan = 'Developer / Superadmin' WHERE id_user = :id");
            $this->db->bind('hash', $hash);
            $this->db->bind('id', $existing['id_user']);
            $this->db->execute();
            $status = "Akun '{$username}' berhasil diperbarui!";
        } else {
            $this->db->query("INSERT INTO users (nama_user, nama_lengkap, password_hash, role, jabatan, tenant_id) 
                              VALUES (:user, 'Super Administrator', :hash, 'Superadmin', 'System Superadmin', NULL)");
            $this->db->bind('user', $username);
            $this->db->bind('hash', $hash);
            $this->db->execute();
            $status = "Akun '{$username}' baru berhasil dibuat!";
        }

        echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Reset Superadmin</title>";
        echo "<style>body{font-family:system-ui,-apple-system,sans-serif;background:#f0fdf4;color:#1e293b;padding:2rem;}";
        echo ".card{max-width:550px;margin:2rem auto;background:#fff;border-radius:16px;box-shadow:0 10px 25px -5px rgba(0,0,0,0.1);padding:2rem;}";
        echo "h2{color:#065f46;margin-top:0;}";
        echo ".info-box{background:#f8fafc;border-left:4px solid #059669;padding:1rem;margin:1.5rem 0;border-radius:6px;font-family:monospace;font-size:1rem;}";
        echo ".btn{display:inline-block;padding:0.75rem 1.5rem;background:#059669;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;}";
        echo "</style></head><body><div class='card'>";
        echo "<h2>🔑 {$status}</h2>";
        echo "<p>Kredensial login Anda telah siap digunakan:</p>";
        echo "<div class='info-box'>";
        echo "<strong>Username:</strong> {$username}<br>";
        echo "<strong>Password:</strong> {$password}<br>";
        echo "<strong>Role:</strong> Superadmin<br>";
        echo "<strong>Cara Login:</strong> Bisa di tab 'Central' atau tab 'Tenant'";
        echo "</div>";
        echo "<a href='" . BASEURL . "/login' class='btn'>👉 Masuk ke Halaman Login</a>";
        echo "</div></body></html>";
        exit;
    }

    /**
     * Endpoint Darurat: Jalankan Semua Migrasi Database
     * URL: /login/migrate
     */
    public function migrate() {
        require_once APPROOT . '/migrate_all.php';
        exit;
    }
}

