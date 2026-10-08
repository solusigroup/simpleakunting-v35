<?php

class Backup extends Controller {
    public function __construct() {
        parent::__construct();
        if (!Auth::isLoggedIn() || (!Auth::isAdmin() && !Auth::isActuallySuperadmin())) {
            Flash::setFlash('Akses Ditolak', 'Hanya Administrator yang dapat melakukan backup database.', 'danger');
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }
    }

    public function index() {
        $isSuperadmin = Auth::isActuallySuperadmin();
        $tenant_id = $this->tenantId();

        // Nama file backup
        if ($isSuperadmin && empty($tenant_id)) {
            $filename = 'backup_system_full_' . date('Y-m-d_H-i-s') . '.sql';
        } else {
            $user = Auth::user();
            $rawName = $user['tenant_name'] ?? ('tenant_' . $tenant_id);
            $safeTenantName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $rawName);
            $filename = 'backup_' . $safeTenantName . '_' . date('Y-m-d_H-i-s') . '.sql';
        }
        
        // Header untuk download file
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Nonaktifkan buffering untuk streaming langsung ke output
        while (ob_get_level()) {
            ob_end_clean();
        }

        echo "-- SimpleAkunting v3.6 Database Backup\n";
        echo "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        echo "-- Mode: " . ($isSuperadmin && empty($tenant_id) ? "Full System" : "Tenant ID: " . $tenant_id) . "\n\n";
        echo "SET FOREIGN_KEY_CHECKS=0;\n";
        echo "SET NAMES utf8mb4;\n\n";

        // Ambil semua tabel
        $queryTables = $this->db->dbh->query("SHOW TABLES");
        $allTables = $queryTables->fetchAll(PDO::FETCH_COLUMN);

        // Definisi query filter per tabel untuk tenant non-superadmin
        $subordinateFilters = [
            'jurnal_detail'               => "WHERE id_jurnal IN (SELECT id_jurnal FROM jurnal_umum WHERE tenant_id = $tenant_id)",
            'penjualan_detail'            => "WHERE id_penjualan IN (SELECT id_penjualan FROM penjualan WHERE tenant_id = $tenant_id)",
            'pembelian_detail'            => "WHERE id_pembelian IN (SELECT id_pembelian FROM pembelian WHERE tenant_id = $tenant_id)",
            'pembayaran_pemasok_detail'   => "WHERE id_pembayaran IN (SELECT id_pembayaran FROM pembayaran_pemasok WHERE tenant_id = $tenant_id)",
            'penerimaan_pelanggan_detail' => "WHERE id_penerimaan IN (SELECT id_penerimaan FROM penerimaan_pelanggan WHERE tenant_id = $tenant_id)",
            'penawaran_detail'            => "WHERE id_penawaran IN (SELECT id_penawaran FROM penawaran WHERE tenant_id = $tenant_id)",
            'rfq_detail'                  => "WHERE id_rfq IN (SELECT id_rfq FROM rfq WHERE tenant_id = $tenant_id)",
            'bom_detail'                  => "WHERE id_bom IN (SELECT id_bom FROM bom WHERE tenant_id = $tenant_id)",
            'kartu_stok'                  => "WHERE id_barang IN (SELECT id_barang FROM master_persediaan WHERE tenant_id = $tenant_id)",
        ];

        // Tabel global yang tidak memiliki tenant_id tetapi boleh disertakan (read-only RBAC)
        $globalSharedTables = ['roles', 'permissions', 'role_permissions'];

        foreach ($allTables as $tableName) {
            // Tentukan klausa WHERE jika dibatasi per tenant
            $whereClause = "";
            if (!$isSuperadmin || !empty($tenant_id)) {
                // Cek kolom tabel
                $colQuery = $this->db->dbh->query("SHOW COLUMNS FROM `$tableName` LIKE 'tenant_id'");
                $hasTenantIdCol = ($colQuery->rowCount() > 0);

                if ($hasTenantIdCol) {
                    $whereClause = "WHERE tenant_id = " . intval($tenant_id);
                } elseif (isset($subordinateFilters[$tableName])) {
                    $whereClause = $subordinateFilters[$tableName];
                } elseif (in_array($tableName, $globalSharedTables)) {
                    $whereClause = ""; // Boleh diikutsertakan
                } else {
                    // Tabel yang tidak relevan atau milik tenant lain (misal tabel tenants, activity_logs global)
                    continue;
                }
            }

            // 1. SHOW CREATE TABLE
            $queryCreate = $this->db->dbh->query("SHOW CREATE TABLE `$tableName` ");
            $createTable = $queryCreate->fetch(PDO::FETCH_ASSOC);
            if (!$createTable) continue;

            echo "-- Structure for table `$tableName` --\n";
            echo "DROP TABLE IF EXISTS `$tableName`;\n";
            echo $createTable['Create Table'] . ";\n\n";

            // 2. Data - Streaming baris per baris tanpa memuat seluruh tabel ke memori
            $sqlData = "SELECT * FROM `$tableName` $whereClause";
            $queryData = $this->db->dbh->query($sqlData);

            $firstRow = true;
            $keys = [];
            $batchValues = [];
            $batchSize = 50;

            while ($row = $queryData->fetch(PDO::FETCH_ASSOC)) {
                if ($firstRow) {
                    echo "-- Dumping data for table `$tableName` --\n";
                    $keys = array_keys($row);
                    $firstRow = false;
                }

                $escapedValues = [];
                foreach ($row as $val) {
                    if (is_null($val)) {
                        $escapedValues[] = "NULL";
                    } else {
                        $escapedValues[] = $this->db->dbh->quote($val);
                    }
                }
                $batchValues[] = "(" . implode(", ", $escapedValues) . ")";

                if (count($batchValues) >= $batchSize) {
                    echo "INSERT INTO `$tableName` (`" . implode("`, `", $keys) . "`) VALUES\n" . implode(",\n", $batchValues) . ";\n";
                    $batchValues = [];
                    flush();
                }
            }

            if (!empty($batchValues)) {
                echo "INSERT INTO `$tableName` (`" . implode("`, `", $keys) . "`) VALUES\n" . implode(",\n", $batchValues) . ";\n\n";
            } elseif (!$firstRow) {
                echo "\n";
            }
            flush();
        }

        echo "SET FOREIGN_KEY_CHECKS=1;\n";
        exit;
    }
}
