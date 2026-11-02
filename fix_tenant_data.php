<?php
/**
 * FIX SCRIPT FOR TENANT INITIALIZATION & USER PERMISSIONS
 * Run this script once to fix existing tenants with empty COA and users with missing role_id.
 */

require_once 'app/config.php';
require_once 'app/core/Database.php';
require_once 'app/models/Akun_model.php';

$db = new Database();
$akunModel = new Akun_model($db);

echo "--- FIXING TENANTS (Empty COA) ---\n";
$db->query("SELECT id, name FROM tenants");
$tenants = $db->resultSet();

foreach ($tenants as $t) {
    $db->query("SELECT COUNT(*) as count FROM akun WHERE tenant_id = :tid");
    $db->bind('tid', $t['id']);
    $res = $db->single();
    
    if ($res['count'] == 0) {
        echo "Initializing COA for tenant: " . $t['name'] . " (ID: " . $t['id'] . ")...\n";
        $count = $akunModel->generateFromCentral($t['id']);
        echo "Successfully added $count accounts.\n";
    } else {
        echo "Tenant " . $t['name'] . " already has " . $res['count'] . " accounts. Skipping.\n";
    }
}

echo "\n--- FIXING USERS (Missing Role ID) ---\n";
$db->query("SELECT id_user, nama_user, role, role_id FROM users WHERE role_id IS NULL OR role_id = 0");
$users = $db->resultSet();

foreach ($users as $u) {
    if ($u['role'] == 'Superadmin') continue; // Superadmin doesn't necessarily need a role_id in some logic
    
    echo "Processing user: " . $u['nama_user'] . " (Role: " . $u['role'] . ")...\n";
    
    $db->query("SELECT id FROM roles WHERE role_name = :rname");
    $db->bind('rname', $u['role']);
    $role_row = $db->single();
    
    if ($role_row) {
        $rid = $role_row['id'];
        $db->query("UPDATE users SET role_id = :rid WHERE id_user = :uid");
        $db->bind('rid', $rid);
        $db->bind('uid', $u['id_user']);
        $db->execute();
        echo "Updated role_id to $rid for user " . $u['nama_user'] . ".\n";
    } else {
        echo "Warning: Could not find role_id for role name '" . $u['role'] . "'.\n";
    }
}

echo "\n--- STANDAR SAK EP: UPDATING TERMINOLOGY AKTIVA -> ASET ---\n";
try {
    $db->query("UPDATE akun SET nama_akun = 'Aset' WHERE LOWER(nama_akun) = 'aktiva'");
    $db->execute();
    $db->query("UPDATE akun SET nama_akun = REPLACE(REPLACE(nama_akun, 'AKTIVA', 'Aset'), 'Aktiva', 'Aset') WHERE nama_akun LIKE '%aktiva%' OR nama_akun LIKE '%Aktiva%' OR nama_akun LIKE '%AKTIVA%'");
    $db->execute();
    $db->query("UPDATE central_akun SET nama_akun = 'Aset' WHERE LOWER(nama_akun) = 'aktiva'");
    $db->execute();
    $db->query("UPDATE central_akun SET nama_akun = REPLACE(REPLACE(nama_akun, 'AKTIVA', 'Aset'), 'Aktiva', 'Aset') WHERE nama_akun LIKE '%aktiva%' OR nama_akun LIKE '%Aktiva%' OR nama_akun LIKE '%AKTIVA%'");
    $db->execute();
    echo "✓ Terminologi akun berhasil disesuaikan dengan SAK EP (AKTIVA -> Aset).\n";
} catch (Throwable $e) {
    echo "⚠ Gagal memperbarui terminologi: " . $e->getMessage() . "\n";
}

echo "\nFixing complete! Please ask the user to logout and login again.\n";
