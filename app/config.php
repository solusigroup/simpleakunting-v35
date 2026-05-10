<?php

// URL Dasar Aplikasi (Otomatis mendeteksi localhost atau production)
// URL Dasar Aplikasi (Otomatis mendeteksi localhost atau production)
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? "https" : "http";

$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
$script_name = $_SERVER['SCRIPT_NAME'] ?? '';
$base_dir = str_replace('\\', '/', dirname($script_name));
if ($base_dir === DIRECTORY_SEPARATOR || $base_dir === '.') $base_dir = '';
define('BASEURL', $protocol . "://" . $host . $base_dir);


// Path Absolut Aplikasi
define('APPROOT', dirname(dirname(__FILE__)));

// Konfigurasi Database (pastikan ini sesuai dengan database di hosting Anda)
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'simpleak_v35');
