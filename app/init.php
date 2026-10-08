<?php

// Memuat file-file inti (core) dari aplikasi
require_once 'config.php';
require_once 'core/App.php';
require_once 'core/Controller.php';
require_once 'core/PeriodLockTrait.php';
require_once 'core/Database.php';

// **TAMBAHKAN ATAU PASTIKAN BARIS INI ADA**
require_once 'core/Flash.php';


// **TAMBAHKAN BARIS INI**
require_once 'core/Auth.php';
require_once 'core/Logger.php';
require_once 'core/Helpers.php';

// Autoloader untuk Model jika dipanggil langsung via new Class_model()
spl_autoload_register(function ($class) {
    $modelFile = APPROOT . '/app/models/' . $class . '.php';
    if (file_exists($modelFile)) {
        require_once $modelFile;
    }
});

