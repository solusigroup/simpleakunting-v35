<?php
/**
 * Migration script for SimpleAkunting v3.5
 * Adding Point of Sales (POS) tables and Role-based permissions
 */
require_once 'app/config.php';
require_once 'app/core/Database.php';

$db = new Database();

// 1. Create pos_transactions table
$sqlTable = "
CREATE TABLE IF NOT EXISTS `pos_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `id_penjualan` bigint(20) unsigned NOT NULL COMMENT 'FK ke tabel penjualan',
  `no_receipt` varchar(50) NOT NULL,
  `kasir_id` bigint(20) unsigned NOT NULL,
  `kasir_name` varchar(255) DEFAULT NULL,
  `total` decimal(15,2) NOT NULL,
  `bayar` decimal(15,2) NOT NULL DEFAULT 0.00,
  `kembalian` decimal(15,2) NOT NULL DEFAULT 0.00,
  `metode_pembayaran` varchar(50) DEFAULT 'Tunai',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tenant_id` (`tenant_id`),
  KEY `id_penjualan` (`id_penjualan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";

try {
    echo "Creating pos_transactions table...\n";
    $db->query($sqlTable);
    $db->execute();
    echo "✅ Table pos_transactions created or already exists.\n\n";

    // 2. Insert new permissions (idempotent)
    $permissions = [
        ['trx_pos', 'Point of Sales (Kasir)', 'Operasional'],
        ['master_pelanggan', 'Manage Pelanggan', 'Master Data'],
        ['master_pemasok', 'Manage Pemasok', 'Master Data']
    ];

    echo "Inserting permissions...\n";
    foreach ($permissions as $p) {
        $db->query("SELECT id FROM permissions WHERE permission_key = :key");
        $db->bind('key', $p[0]);
        $row = $db->single();

        if (!$row) {
            $db->query("INSERT INTO permissions (permission_key, display_name, category) VALUES (:key, :name, :cat)");
            $db->bind('key', $p[0]);
            $db->bind('name', $p[1]);
            $db->bind('cat', $p[2]);
            $db->execute();
            echo "✨ Added permission: {$p[0]}\n";
        } else {
            echo "   Permission {$p[0]} already exists.\n";
        }
    }
    echo "✅ Permissions check complete.\n\n";

    // 3. Assign permissions to roles
    // Get permission IDs
    $permIds = [];
    foreach ($permissions as $p) {
        $db->query("SELECT id FROM permissions WHERE permission_key = :key");
        $db->bind('key', $p[0]);
        $row = $db->single();
        if ($row) {
            $permIds[$p[0]] = $row['id'];
        }
    }

    echo "Assigning permissions to roles...\n";
    // Superadmin (role_id=1): gets all 3 (automatically because they get all, but let's record it explicitly just in case)
    // Admin (role_id=2): gets all 3
    // Manager (role_id=3): gets all 3
    // Staff (role_id=4): gets trx_pos, master_pelanggan, master_pemasok (Staff needs cash register and contacts management!)
    
    $roleAssignments = [
        1 => ['trx_pos', 'master_pelanggan', 'master_pemasok'],
        2 => ['trx_pos', 'master_pelanggan', 'master_pemasok'],
        3 => ['trx_pos', 'master_pelanggan', 'master_pemasok'],
        4 => ['trx_pos', 'master_pelanggan', 'master_pemasok']
    ];

    foreach ($roleAssignments as $roleId => $keys) {
        foreach ($keys as $key) {
            if (isset($permIds[$key])) {
                $permId = $permIds[$key];
                
                // Check if already assigned
                $db->query("SELECT * FROM role_permissions WHERE role_id = :role_id AND permission_id = :perm_id");
                $db->bind('role_id', $roleId);
                $db->bind('perm_id', $permId);
                $exists = $db->single();

                if (!$exists) {
                    $db->query("INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :perm_id)");
                    $db->bind('role_id', $roleId);
                    $db->bind('perm_id', $permId);
                    $db->execute();
                    echo "✨ Assigned permission ID {$permId} ({$key}) to role ID {$roleId}\n";
                }
            }
        }
    }
    echo "✅ Role permissions mapping complete.\n\n";

    // 4. Check & insert 'Walk-in Customer' for every tenant
    echo "Checking default Walk-in Customer for all tenants...\n";
    
    // Get all tenants
    $db->query("SELECT id FROM tenants");
    $tenants = $db->resultSet();

    foreach ($tenants as $t) {
        $tenantId = $t['id'];
        
        // Check if Walk-in Customer already exists for this tenant
        $db->query("SELECT id_pelanggan FROM pelanggan WHERE tenant_id = :tid AND nama_pelanggan = 'Walk-in Customer'");
        $db->bind('tid', $tenantId);
        $cust = $db->single();

        if (!$cust) {
            $db->query("INSERT INTO pelanggan (tenant_id, nama_pelanggan, alamat, telepon, email) VALUES (:tid, 'Walk-in Customer', 'Cash / POS Client', '-', '-')");
            $db->bind('tid', $tenantId);
            $db->execute();
            echo "✨ Created default 'Walk-in Customer' for tenant ID {$tenantId}\n";
        } else {
            echo "   Walk-in Customer already exists for tenant ID {$tenantId}\n";
        }
    }
    echo "✅ Default customers check complete.\n\n";

    echo "🎉 POS Migration Complete!\n";

} catch (Exception $e) {
    echo "❌ Migration Error: " . $e->getMessage() . "\n";
}
