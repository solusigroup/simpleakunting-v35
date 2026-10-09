<?php
/**
 * RESET & ENSURE SUPERADMIN USER
 * SimpleAkunting v3.6
 *
 * Jalankan via Browser: https://domain-anda.com/reset_superadmin.php
 * Jalankan via CLI: php reset_superadmin.php
 */

require_once 'app/config.php';
require_once 'app/core/Database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
unset($_SESSION['flash']);

$isWeb = (php_sapi_name() !== 'cli');

if ($isWeb) {
    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Reset User Superadmin</title>";
    echo "<style>body{font-family:system-ui,-apple-system,sans-serif;background:#f0fdf4;color:#1e293b;padding:2rem;}";
    echo ".card{max-width:550px;margin:2rem auto;background:#fff;border-radius:16px;box-shadow:0 10px 25px -5px rgba(0,0,0,0.1);padding:2rem;}";
    echo "h2{color:#065f46;margin-top:0;}";
    echo ".info-box{background:#f8fafc;border-left:4px solid #059669;padding:1rem;margin:1.5rem 0;border-radius:6px;font-family:monospace;font-size:1rem;}";
    echo ".btn{display:inline-block;padding:0.75rem 1.5rem;background:#059669;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;}";
    echo "</style></head><body><div class='card'>";
}

$db = new Database();

$username = 'superadmin';
$password = $_REQUEST['pass'] ?? 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo $isWeb ? "<h2>🔑 Reset Akun Superadmin</h2>" : "=== RESET SUPERADMIN ===\n";

// 1. Cek apakah user superadmin sudah ada di database
$db->query("SELECT * FROM users WHERE nama_user = :user");
$db->bind('user', $username);
$existing = $db->single();

if ($existing) {
    // Update password dan pastikan role adalah Superadmin
    $db->query("UPDATE users SET password_hash = :hash, role = 'Superadmin', jabatan = 'Developer / Superadmin' WHERE id_user = :id");
    $db->bind('hash', $hash);
    $db->bind('id', $existing['id_user']);
    $db->execute();
    
    $msg = "Akun '{$username}' berhasil diperbarui!";
} else {
    // Buat baru jika belum ada
    $db->query("INSERT INTO users (nama_user, nama_lengkap, password_hash, role, jabatan, tenant_id) 
                VALUES (:user, 'Super Administrator', :hash, 'Superadmin', 'System Superadmin', NULL)");
    $db->bind('user', $username);
    $db->bind('hash', $hash);
    $db->execute();
    
    $msg = "Akun '{$username}' baru berhasil dibuat!";
}

if ($isWeb) {
    echo "<p>{$msg}</p>";
    echo "<div class='info-box'>";
    echo "<strong>Username:</strong> {$username}<br>";
    echo "<strong>Password:</strong> {$password}<br>";
    echo "<strong>Role:</strong> Superadmin<br>";
    echo "<strong>Akses:</strong> Tab 'Central' atau Tab 'Tenant'";
    echo "</div>";
    echo "<a href='" . BASEURL . "/login' class='btn'>👉 Pergi ke Halaman Login</a>";
    echo "</div></body></html>";
} else {
    echo "✓ {$msg}\n";
    echo "Username : {$username}\n";
    echo "Password : {$password}\n";
    echo "Role     : Superadmin\n\n";
}
