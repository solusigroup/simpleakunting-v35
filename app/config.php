<?php

// URL Dasar Aplikasi (Otomatis mendeteksi localhost atau production)
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
define('BASEURL', $protocol . "://" . $host);

// Path Absolut Aplikasi
define('APPROOT', dirname(dirname(__FILE__)));

// Konfigurasi Database (pastikan ini sesuai dengan database di hosting Anda)
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'simpleak_v35');
