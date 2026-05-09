<?php

class Controller {
    /**
     * @var Database Properti untuk menyimpan satu instance koneksi database.
     */
    protected $db;

    /**
     * Constructor ini berjalan secara otomatis untuk setiap controller.
     * Ia membuat satu koneksi database yang akan digunakan bersama.
     */
    public function __construct()
    {
        $this->db = new Database;
        Logger::init($this->db);
    }

    /**
     * Metode untuk memuat dan menampilkan file view.
     * @param string $view Path ke file view dari folder 'views'.
     * @param array $data Data yang akan diekstrak menjadi variabel di dalam view.
     */
    public function view($view, $data = [])
    {
        $viewFile = APPROOT . '/app/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die('Error: File view tidak ditemukan di: ' . $viewFile);
        }
    }

    /**
     * Metode untuk memuat file model dan menyuntikkan koneksi database.
     * @param string $model Nama file model (tanpa _model.php).
     * @return object Instance dari model yang diminta, yang sudah memiliki koneksi database.
     */
    public function model($model)
    {
        // Muat file model
        require_once APPROOT . '/app/models/' . $model . '_model.php';
        
        // Buat instance dari kelas modelnya
        $modelName = $model . '_model';
        // Berikan koneksi database ($this->db) ke constructor model
        return new $modelName($this->db);
    }

    /**
     * Menghasilkan nomor transaksi otomatis berdasarkan prefix dan tanggal.
     * Format: PREFIX/YYYYMMDD/SEQUENTIAL (e.g. PJ/20240509/001)
     */
    protected function generateAutoNumber($prefix, $table, $column, $tenant_id) {
        $date = date('Ymd');
        $pattern = "$prefix/$date/%";
        
        $this->db->query("SELECT MAX($column) as last_no FROM $table WHERE $column LIKE :pattern AND tenant_id = :tenant_id");
        $this->db->bind('pattern', $pattern);
        $this->db->bind('tenant_id', $tenant_id);
        $result = $this->db->single();
        
        $last_no = $result['last_no'];
        $sequence = 1;
        
        if ($last_no) {
            $parts = explode('/', $last_no);
            $sequence = (int)end($parts) + 1;
        }
        
        return "$prefix/$date/" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Mendapatkan tenant_id dari user yang sedang login.
     * Jika superadmin, bisa mengembalikan null atau ID tertentu tergantung konteks.
     */
    protected function tenantId()
    {
        $user = Auth::user();
        if (!$user) return null;
        
        // Jika ada tenant_id di session (bisa dari login normal atau impersonasi)
        if (isset($user['tenant_id']) && !empty($user['tenant_id'])) {
            return $user['tenant_id'];
        }
        
        // Default untuk Superadmin murni (melihat agregat)
        return null;
    }
}

