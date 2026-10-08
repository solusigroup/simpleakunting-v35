<?php
$_SERVER['HTTP_HOST'] = '127.0.0.1';
require_once 'app/config.php';
require_once 'app/core/Database.php';

$db = new Database();

try {
    echo "Starting business_units migration...\n";

    // 1. Create business_units table
    $sql1 = "CREATE TABLE IF NOT EXISTS business_units (
        id_unit INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id INT NOT NULL,
        nama_unit VARCHAR(255) NOT NULL,
        kode_unit VARCHAR(50) NOT NULL,
        deskripsi TEXT,
        is_default TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX (tenant_id)
    ) ENGINE=InnoDB;";
    
    $db->query($sql1);
    $db->execute();
    echo "✓ Table 'business_units' created successfully.\n";

    // 2. Add id_unit to jurnal_umum if not exists
    $checkSql = "SHOW COLUMNS FROM jurnal_umum LIKE 'id_unit'";
    $db->query($checkSql);
    $column = $db->single();

    if (!$column) {
        $sql2 = "ALTER TABLE jurnal_umum ADD COLUMN id_unit INT DEFAULT NULL AFTER id_program;";
        $db->query($sql2);
        $db->execute();
        echo "✓ Column 'id_unit' added to 'jurnal_umum' successfully.\n";
    } else {
        echo "✓ Column 'id_unit' already exists in 'jurnal_umum'.\n";
    }

    // 3. Add permissions for units
    $perms = [
        ['permission_key' => 'menu_units', 'display_name' => 'Menu Unit Usaha', 'category' => 'Pembiayaan'],
        ['permission_key' => 'trx_units_manage', 'display_name' => 'Kelola Data Unit Usaha', 'category' => 'Pembiayaan']
    ];

    foreach ($perms as $p) {
        $checkPerm = "SELECT id FROM permissions WHERE permission_key = :key";
        $db->query($checkPerm);
        $db->bind('key', $p['permission_key']);
        if (!$db->single()) {
            $sqlPerm = "INSERT INTO permissions (permission_key, display_name, category) VALUES (:key, :name, :cat)";
            $db->query($sqlPerm);
            $db->bind('key', $p['permission_key']);
            $db->bind('name', $p['display_name']);
            $db->bind('cat', $p['category']);
            $db->execute();
            echo "✓ Permission '{$p['permission_key']}' added.\n";
        }
    }

    // 4. Ensure default unit exists for each tenant
    $db->query("SELECT id FROM tenants");
    $tenants = $db->resultSet();
    foreach ($tenants as $t) {
        $tid = $t['id'];
        $db->query("SELECT id_unit FROM business_units WHERE tenant_id = :tid");
        $db->bind('tid', $tid);
        if (!$db->single()) {
            $db->query("INSERT INTO business_units (tenant_id, nama_unit, kode_unit, deskripsi, is_default) VALUES (:tid, 'Kantor Pusat / Utama', 'HQ', 'Unit bisnis utama', 1)");
            $db->bind('tid', $tid);
            $db->execute();
            echo "✓ Default unit created for tenant {$tid}.\n";
        }
    }

    echo "Migration for business_units completed successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
