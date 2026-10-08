<?php
$_SERVER['HTTP_HOST'] = '127.0.0.1';
require_once 'app/config.php';
require_once 'app/core/Database.php';

$db = new Database();

try {
    echo "Starting migration...\n";

    // 1. Create programs table
    $sql1 = "CREATE TABLE IF NOT EXISTS programs (
        id_program INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id INT NOT NULL,
        nama_program VARCHAR(255) NOT NULL,
        tipe ENUM('Pemerintah', 'CSR', 'Lainnya') DEFAULT 'Pemerintah',
        anggaran_total DECIMAL(15,2) DEFAULT 0.00,
        deskripsi TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX (tenant_id)
    ) ENGINE=InnoDB;";
    
    $db->query($sql1);
    $db->execute();
    echo "Table 'programs' created successfully.\n";

    // 2. Add id_program to jurnal_umum
    // Check if column exists first to avoid error
    $checkSql = "SHOW COLUMNS FROM jurnal_umum LIKE 'id_program'";
    $db->query($checkSql);
    $column = $db->single();

    if (!$column) {
        $sql2 = "ALTER TABLE jurnal_umum ADD COLUMN id_program INT DEFAULT NULL AFTER sumber_jurnal;";
        $db->query($sql2);
        $db->execute();
        
        $sql3 = "ALTER TABLE jurnal_umum ADD CONSTRAINT fk_jurnal_program FOREIGN KEY (id_program) REFERENCES programs(id_program) ON DELETE SET NULL;";
        $db->query($sql3);
        $db->execute();
        echo "Column 'id_program' added to 'jurnal_umum' successfully.\n";
    } else {
        echo "Column 'id_program' already exists in 'jurnal_umum'.\n";
    }

    // 3. Add permissions
    $perms = [
        ['permission_key' => 'menu_programs', 'display_name' => 'Menu Program & Sumber Dana', 'category' => 'Pembiayaan'],
        ['permission_key' => 'trx_programs_manage', 'display_name' => 'Kelola Data Program', 'category' => 'Pembiayaan'],
        ['permission_key' => 'rep_programs_realization', 'display_name' => 'Laporan Realisasi Program', 'category' => 'Laporan']
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
            echo "Permission '{$p['permission_key']}' added successfully.\n";
        }
    }

    echo "Migration completed successfully!\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
