<?php

/*
|--------------------------------------------------------------------------
| Kelas App (Router Utama)
|--------------------------------------------------------------------------
|
| Kelas ini adalah inti dari aplikasi. Ia membaca URL, menerapkan aturan
| keamanan (memaksa login), lalu menentukan controller, method, dan
| parameter apa yang harus dijalankan.
|
*/

class App {
    protected $controller = 'Dashboard';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->parseURL();

        // --- VALIDASI CSRF SECARA GLOBAL ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Bypass CSRF check untuk controller login (misal /login/process)
            $isLoginRoute = (!empty($url) && strtolower($url[0]) === 'login');
            if (!$isLoginRoute) {
                $csrf_token = $_POST['csrf_token'] ?? '';
                if (empty($csrf_token) || !hash_equals($_SESSION['csrf_token'] ?? '', $csrf_token)) {
                    Flash::setFlash('Akses Ditolak! Permintaan diblokir karena token keamanan (CSRF) tidak valid atau kedaluwarsa.', 'danger');
                    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? BASEURL));
                    exit;
                }
            }
        }

        // --- GERBANG KEAMANAN YANG DIPERBARUI ---

        // Cek apakah controller yang dituju adalah 'login'.
        $isLoginController = (!empty($url) && strtolower($url[0]) === 'login');
        
        // Cek apakah method yang dituju adalah 'index' (artinya, form login).
        // Jika method tidak ada, defaultnya adalah 'index'.
        $isLoginForm = ($isLoginController && (!isset($url[1]) || strtolower($url[1]) === 'index'));

        // Skenario 1: Pengguna BELUM login DAN TIDAK sedang mencoba mengakses controller login.
        if (!Auth::isLoggedIn() && !$isLoginController) {
            header('Location: ' . BASEURL . '/login');
            exit;
        }

        // Skenario 2: Pengguna SUDAH login TETAPI mencoba mengakses FORM login.
        if (Auth::isLoggedIn() && $isLoginForm) {
            // Ini tidak perlu, arahkan mereka ke halaman utama.
             header('Location: ' . BASEURL . '/dashboard');
             exit;
        }
        // --- AKHIR GERBANG KEAMANAN ---


        // --- Menentukan Controller ---
        if (!empty($url) && file_exists(APPROOT . '/app/controllers/' . ucfirst($url[0]) . '.php')) {
            $this->controller = ucfirst($url[0]);
            unset($url[0]);
        }

        require_once APPROOT . '/app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // --- Menentukan Method ---
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // --- Menentukan Parameter ---
        if (!empty($url)) {
            $this->params = array_values($url);
        }

        // --- PROTEKSI IMPERSONATION (READ-ONLY MODE) ---
        // Mencegah Superadmin yang sedang impersonasi untuk mengubah data di modul tenant.
        $user = Auth::user();
        if ($user && ($user['impersonating'] ?? false)) {
            // Dapatkan nama class controller (tanpa namespace/path)
            $controllerName = get_class($this->controller);
            
            // Kecuali modul Central dan Login, semua modul tenant diproteksi
            if ($controllerName !== 'Central' && $controllerName !== 'Login') {
                $writeMethods = ['simpan', 'update', 'hapus', 'delete', 'proses', 'tambah_data', 'ubah', 'set_active', 'import', 'export_save'];
                $currentMethod = strtolower($this->method);
                
                if (in_array($currentMethod, $writeMethods) || $_SERVER['REQUEST_METHOD'] === 'POST') {
                    Flash::setFlash('Akses Terbatas', 'Dalam mode Impersonation, Anda hanya diperbolehkan melihat data (Read-Only).', 'warning');
                    
                    // Redirect kembali ke halaman sebelumnya atau ke dashboard
                    $redirect = $_SERVER['HTTP_REFERER'] ?? BASEURL . '/dashboard';
                    header('Location: ' . $redirect);
                    exit;
                }
            }
        }

        // --- Jalankan Semuanya! ---
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseURL()
    {
        if (isset($_GET['url'])) {
            $url = trim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        
        // Fallback untuk PHP Built-in Server
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        
        // Remove script path from request URI if present (e.g. if installed in subdirectory)
        if ($scriptName !== '/' && strpos($requestUri, $scriptName) === 0) {
            $requestUri = substr($requestUri, strlen($scriptName));
        }
        
        $url = ltrim($requestUri, '/');
        if (!empty($url)) {
            $url = rtrim($url, '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }

        return [];
    }
}