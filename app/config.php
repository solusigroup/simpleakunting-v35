<?php

// URL Dasar Aplikasi (Otomatis mendeteksi localhost atau production)
// URL Dasar Aplikasi (Otomatis mendeteksi localhost atau production)
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? "https" : "http";

$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
$script_name = $_SERVER['SCRIPT_NAME'] ?? '';
$base_dir = str_replace('\\', '/', dirname($script_name));
if ($base_dir === '/' || $base_dir === '.')
    $base_dir = '';
define('BASEURL', $protocol . "://" . $host . $base_dir);


// Path Absolut Aplikasi
define('APPROOT', dirname(dirname(__FILE__)));

// Konfigurasi Database (Otomatis mendeteksi localhost atau production server)
if (php_sapi_name() === 'cli') {
    // Jika dijalankan lewat terminal/CLI, anggap localhost hanya jika OS adalah Windows (lingkungan dev lokal)
    $isLocalhost = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
} else {
    $isLocalhost = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1']) || $host === 'localhost' || strpos($host, 'localhost:') === 0;
}

if ($isLocalhost) {
    define('DB_HOST', '127.0.0.1');
    define('DB_USER', 'root');
    define('DB_PASS', 'root');
    define('DB_NAME', 'simpleak_v35');
} else {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'simkopde_umkm');
    define('DB_PASS', '#5@8@12Yaa');
    define('DB_NAME', 'simkopde_umkm35');
}
