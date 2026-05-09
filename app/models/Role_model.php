<?php

class Role_model {
    private $db;

    public function __construct($db) {
        $this->db = $db;
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
