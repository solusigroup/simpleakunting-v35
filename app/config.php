<?php

// URL Dasar Aplikasi (Otomatis mendeteksi localhost atau production)
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
define('BASEURL', $protocol . "://" . $host);

// Path Absolut Aplikasi
define('APPROOT', dirname(dirname(__FILE__)));

// Konfigurasi Database (pastikan ini sesuai dengan database di hosting Anda)
define('DB_HOST', 'localhost');
define('DB_USER', 'simpleak_user_simple');
define('DB_PASS', '5@8@12Yaa');
define('DB_NAME', 'simpleak_db_akunting');
