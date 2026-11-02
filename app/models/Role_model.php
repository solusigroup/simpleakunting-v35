<?php

class Role_model {
    private $db;
    private static $permsChecked = false;

    public function __construct($db) {
        $this->db = $db;
        $this->ensureDefaultPermissions();
    }

    /**
     * Memastikan seluruh permission esensial dan assignment ke role terdaftar otomatis di database
     */
    private function ensureDefaultPermissions() {
        if (self::$permsChecked) return;
        self::$permsChecked = true;

        try {
            $requiredPermissions = [
                ['master_pelanggan', 'Manage Pelanggan', 'Master Data'],
                ['master_pemasok', 'Manage Pemasok', 'Master Data'],
                ['menu_programs', 'Menu Program & Sumber Dana', 'Pembiayaan'],
                ['trx_programs_manage', 'Kelola Data Program', 'Pembiayaan'],
                ['rep_programs_realization', 'Laporan Realisasi Program', 'Laporan'],
                ['menu_units', 'Menu Unit Usaha', 'Pembiayaan'],
                ['trx_units_manage', 'Kelola Data Unit Usaha', 'Pembiayaan'],
                ['trx_pos', 'Point of Sales (Kasir)', 'Operasional']
            ];

            foreach ($requiredPermissions as $p) {
                $this->db->query("SELECT id FROM permissions WHERE permission_key = :k");
                $this->db->bind('k', $p[0]);
                $row = $this->db->single();
                $permId = $row ? $row['id'] : null;

                if (!$permId) {
                    $this->db->query("INSERT INTO permissions (permission_key, display_name, category) VALUES (:k, :d, :c)");
                    $this->db->bind('k', $p[0]);
                    $this->db->bind('d', $p[1]);
                    $this->db->bind('c', $p[2]);
                    $this->db->execute();
                    $permId = $this->db->lastInsertId();
                }

                if ($permId) {
                    // Berikan akses otomatis ke Superadmin (1), Admin (2), Manager (3)
                    foreach ([1, 2, 3] as $rid) {
                        $this->db->query("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (:rid, :pid)");
                        $this->db->bind('rid', $rid);
                        $this->db->bind('pid', $permId);
                        $this->db->execute();
                    }
                }
            }
        } catch (Throwable $e) {}
    }

    public function getAllRoles() {
        $this->db->query("SELECT * FROM roles ORDER BY id ASC");
        $roles = $this->db->resultSet();
        
        foreach ($roles as &$role) {
            $role['permissions'] = $this->getRolePermissions($role['id']);
        }
        
        return $roles;
    }

    public function getRoleById($id) {
        $this->db->query("SELECT * FROM roles WHERE id = :id");
        $this->db->bind('id', $id);
        $role = $this->db->single();
        
        if ($role) {
            $role['permissions'] = $this->getRolePermissions($id);
        }
        return $role;
    }

    public function getRolePermissions($role_id) {
        $this->db->query("SELECT p.permission_key 
                          FROM role_permissions rp 
                          JOIN permissions p ON rp.permission_id = p.id 
                          WHERE rp.role_id = :role_id");
        $this->db->bind('role_id', $role_id);
        return array_column($this->db->resultSet(), 'permission_key');
    }

    public function getAllPermissions() {
        $this->db->query("SELECT * FROM permissions ORDER BY category, display_name");
        return $this->db->resultSet();
    }

    public function tambahRole($data) {
        $this->db->beginTransaction();
        try {
            $this->db->query("INSERT INTO roles (role_name, description) VALUES (:name, :desc)");
            $this->db->bind('name', $data['role_name']);
            $this->db->bind('desc', $data['description']);
            $this->db->execute();
            $role_id = $this->db->lastInsertId();

            if (!empty($data['permissions'])) {
                foreach ($data['permissions'] as $perm_id) {
                    $this->db->query("INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :perm_id)");
                    $this->db->bind('role_id', $role_id);
                    $this->db->bind('perm_id', $perm_id);
                    $this->db->execute();
                }
            }

            $this->db->commit();
            return $role_id;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateRole($data) {
        $this->db->beginTransaction();
        try {
            $this->db->query("UPDATE roles SET role_name = :name, description = :desc WHERE id = :id");
            $this->db->bind('id', $data['id']);
            $this->db->bind('name', $data['role_name']);
            $this->db->bind('desc', $data['description']);
            $this->db->execute();

            // Clear old permissions
            $this->db->query("DELETE FROM role_permissions WHERE role_id = :id");
            $this->db->bind('id', $data['id']);
            $this->db->execute();

            // Insert new permissions
            if (!empty($data['permissions'])) {
                foreach ($data['permissions'] as $perm_id) {
                    $this->db->query("INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :perm_id)");
                    $this->db->bind('role_id', $data['id']);
                    $this->db->bind('perm_id', $perm_id);
                    $this->db->execute();
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function hapusRole($id) {
        $this->db->query("DELETE FROM roles WHERE id = :id");
        $this->db->bind('id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }
}
