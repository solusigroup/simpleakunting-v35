<?php
// Aktifkan pelaporan error selama masa pengembangan (Otomatis deteksi localhost)
$isLocalhost = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1']) || ($_SERVER['HTTP_HOST'] ?? '') === 'localhost' || strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost:') === 0;
if ($isLocalhost) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. **MEMUAT AUTOLOADER COMPOSER (INI YANG PALING PENTING)**
// Path ini akan memuat semua library eksternal, termasuk PhpSpreadsheet.
require_once __DIR__ . '/../vendor/autoload.php';

// 2. Memuat file inisialisasi aplikasi kita (setelah library siap)
require_once '../app/init.php';

// --- OUTPUT BUFFERING UNTUK INJEKSI CSRF TOKEN SECARA OTOMATIS ---
ob_start(function($buffer) {
    // Hanya proses jika buffer berisi form HTML
    if (stripos($buffer, '<form') !== false) {
        $token = Auth::getCsrfToken();
        $input = "\n" . '<input type="hidden" name="csrf_token" value="' . $token . '">';
        
        // Ganti tag form post (case-insensitive) untuk menyisipkan input hidden csrf_token
        $buffer = preg_replace_callback('/(<form[^>]*method=["\']post["\'][^>]*>)/i', function($matches) use ($input) {
            return $matches[1] . $input;
        }, $buffer);
    }
    return $buffer;
});

// 3. Menjalankan aplikasi
$app = new App();

