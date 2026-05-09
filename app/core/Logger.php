<?php

class Logger {
    private static $db;

    public static function init($db) {
        self::$db = $db;
    }

    /**
     * Mencatat aktivitas ke dalam database.
     */
    public static function log($action, $module, $description = '') {
        if (!self::$db) return;

        $user = Auth::user();
        $tenant_id = $user['tenant_id'] ?? null;
        $user_id = $user['id'] ?? null;
        $user_name = $user['name'] ?? 'System';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        $query = "INSERT INTO activity_logs (tenant_id, user_id, user_name, action, module, description, ip_address) 
                  VALUES (:tenant_id, :user_id, :user_name, :action, :module, :description, :ip)";
        
        try {
            self::$db->query($query);
            self::$db->bind('tenant_id', $tenant_id);
            self::$db->bind('user_id', $user_id);
            self::$db->bind('user_name', $user_name);
            self::$db->bind('action', $action);
            self::$db->bind('module', $module);
            self::$db->bind('description', $description);
            self::$db->bind('ip', $ip);
            self::$db->execute();
        } catch (Exception $e) {
            // Silently fail if logging fails to avoid breaking main transaction
        }
    }
}
