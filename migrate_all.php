<?php
/**
 * MASTER MIGRATION & REPAIR RUNNER
 * SimpleAkunting v3.6
 *
 * Jalankan file ini melalui:
 * - Browser: https://domain-anda.com/migrate_all.php
 * - Terminal: php migrate_all.php
 */

if (php_sapi_name() !== 'cli') {
    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Migrasi Database SimpleAkunting</title>";
    echo "<style>body{font-family:system-ui,-apple-system,sans-serif;background:#f8fafc;color:#1e293b;padding:2rem;}";
    echo ".box{max-width:800px;margin:0 auto;background:#fff;border-radius:12px;box-shadow:0 4px 6px -1px rgb(0 0 0 / 0.1);padding:2rem;}";
    echo "pre{background:#0f172a;color:#38bdf8;padding:1rem;border-radius:8px;overflow-x:auto;font-size:0.875rem;}";
    echo ".btn{display:inline-block;padding:0.75rem 1.5rem;background:#059669;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;margin-top:1rem;}";
    echo "</style></head><body><div class='box'><h2>🚀 SimpleAkunting v3.6 - Master Database Migration</h2><pre>";
}

echo "Memulai migrasi seluruh database...\n\n";

require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/core/Database.php';

$scripts = [
    'migrate_kluster_wilayah.php' => '1. Migrasi Kluster Wilayah & Seeding Jawa Timur',
    'migrate_pos.php'             => '2. Migrasi Point of Sales (Kasir POS)',
    'migrate_programs.php'        => '3. Migrasi Program & Sumber Dana',
    'migrate_business_units.php'   => '4. Migrasi Unit Usaha / Cabang',
    'migrate_aset_biologis.php'   => '5. Migrasi Aset Biologis PSAK 241',
    'migrate_phase1_indexes.php'  => '6. Migrasi Indeks Performa Transaksi',
    'fix_tenant_data.php'         => '7. Inisialisasi COA Tenant Kosong & Role User'
];

foreach ($scripts as $file => $title) {
    echo "========================================================\n";
    echo " " . $title . "\n";
    echo "========================================================\n";
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        try {
            // Buffer dan jalankan skrip
            include $fullPath;
            echo "\n";
        } catch (Throwable $e) {
            echo "⚠ Terjadi error pada {$file}: " . $e->getMessage() . "\n\n";
        }
    } else {
        echo "⚠ File {$file} tidak ditemukan, dilewati.\n\n";
    }
}

echo "========================================================\n";
echo "🎉 SELURUH MIGRASI & PERBAIKAN SELESAI!\n";
echo "========================================================\n";

if (php_sapi_name() !== 'cli') {
    echo "</pre><a href='" . BASEURL . "/dashboard' class='btn'>Kembali ke Dashboard</a></div></body></html>";
}
