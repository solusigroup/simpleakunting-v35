<?php
/**
 * Tinker-like script to reset user password
 * Run this via CLI: php scratch/tinker_reset_password.php
 */

require_once 'app/config.php';
require_once 'app/core/Database.php';

echo "--- TINKER PASSWORD RESET ---\n";

$db = new Database();

$id_user = 7;
$nama_user = 'penyelia';
$new_password = 'Gaspol2026!@#$%';

// 1. Cek apakah user ada
$db->query("SELECT * FROM users WHERE id_user = :id AND nama_user = :nama");
$db->bind('id', $id_user);
$db->bind('nama', $nama_user);
$user = $db->single();

if (!$user) {
    echo "ERROR: User dengan ID {$id_user} dan Username '{$nama_user}' tidak ditemukan.\n";
    exit;
}

echo "User ditemukan: " . $user['nama_user'] . " (ID: " . $user['id_user'] . ")\n";

// 2. Generate Hash
$hash = password_hash($new_password, PASSWORD_DEFAULT);

// 3. Update Password
$db->query("UPDATE users SET password_hash = :hash WHERE id_user = :id");
$db->bind('hash', $hash);
$db->bind('id', $id_user);

try {
    $db->execute();
    echo "SUCCESS: Password untuk user '{$nama_user}' berhasil diubah menjadi '{$new_password}'.\n";
} catch (Exception $e) {
    echo "ERROR: Gagal memperbarui password. " . $e->getMessage() . "\n";
}
