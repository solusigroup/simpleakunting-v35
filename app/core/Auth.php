<?php

class Auth {
    private static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Menyimpan data pengguna ke dalam session setelah login berhasil.
     * Pastikan kita konsisten menggunakan 'user_name' sebagai kunci sesi.
     */
    public static function setUser($user, $permissions = []) {
        self::startSession();
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['tenant_id'] = $user['tenant_id'] ?? null;
        $_SESSION['tenant_name'] = $user['tenant_name'] ?? null;
        $_SESSION['database_type'] = $user['database_type'] ?? 'dagang';
        $_SESSION['user_name'] = $user['nama_user'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_permissions'] = $permissions;
    }

    /**
     * Fitur Impersonation (Login sebagai user lain)
     */
    public static function impersonate($user, $permissions = []) {
        self::startSession();
        // Simpan identitas Superadmin asli agar bisa balik
        if (!isset($_SESSION['original_user'])) {
            $_SESSION['original_user'] = [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'],
                'role' => $_SESSION['user_role']
            ];
        }
        
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['tenant_id'] = $user['tenant_id'] ?? null;
        $_SESSION['tenant_name'] = $user['tenant_name'] ?? null;
        $_SESSION['database_type'] = $user['database_type'] ?? 'dagang';
        $_SESSION['user_name'] = $user['nama_user'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_permissions'] = $permissions;
        $_SESSION['impersonating'] = true;
    }

    public static function stopImpersonating() {
        self::startSession();
        if (isset($_SESSION['original_user'])) {
            $orig = $_SESSION['original_user'];
            $_SESSION['user_id'] = $orig['id'];
            $_SESSION['user_name'] = $orig['name'];
            $_SESSION['user_role'] = $orig['role'];
            $_SESSION['tenant_id'] = null; // Superadmin doesn't belong to a tenant
            $_SESSION['database_type'] = 'dagang';
            unset($_SESSION['original_user']);
            unset($_SESSION['impersonating']);
            unset($_SESSION['user_permissions']);
            return true;
        }
        return false;
    }

    public static function isLoggedIn() {
        self::startSession();
        return isset($_SESSION['user_id']);
    }

    public static function logout() {
        self::startSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * Mengambil data pengguna yang sedang login.
     * Pastikan kita membaca dari kunci 'user_name' yang sama.
     */
    public static function user() {
        self::startSession();
        if (self::isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'tenant_id' => $_SESSION['tenant_id'] ?? null,
                'tenant_name' => $_SESSION['tenant_name'] ?? null,
                'database_type' => $_SESSION['database_type'] ?? 'dagang',
                'name' => $_SESSION['user_name'], // Kunci 'user_name' dibaca di sini
                'role' => $_SESSION['user_role'],
                'impersonating' => $_SESSION['impersonating'] ?? false
            ];
        }
        return null;
    }

    public static function hasRole($role) {
        return self::isLoggedIn() && self::user()['role'] === $role;
    }

    public static function isAdmin() {
        return self::hasRole('Admin');
    }

    public static function isManager() {
        return self::hasRole('Manager');
    }

    public static function isActuallySuperadmin() {
        self::startSession();
        if (self::hasRole('Superadmin')) return true;
        if (isset($_SESSION['original_user']) && $_SESSION['original_user']['role'] === 'Superadmin') return true;
        return false;
    }

    public static function isStaff() {
        return self::hasRole('Staff');
    }

    public static function hasPermission($permission_key) {
        self::startSession();
        if (!self::isLoggedIn()) return false;
        
        // Hanya Superadmin yang membypass semua check (RBAC Global)
        if (self::hasRole('Superadmin')) return true;

        $permissions = $_SESSION['user_permissions'] ?? [];
        return in_array($permission_key, $permissions);
    }
}

