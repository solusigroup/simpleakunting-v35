<?php

class KlusterWilayah_model {
    private $table = 'kluster_wilayah';
    private $db;
    private static $checked = false;

    public function __construct($db) {
        $this->db = $db;
        $this->ensureTableExists();
    }

    /**
     * Memastikan tabel kluster_wilayah dan kolom relasinya ada di database secara otomatis
     */
    private function ensureTableExists() {
        if (self::$checked) return;
        self::$checked = true;

        try {
            $sqlTable = "CREATE TABLE IF NOT EXISTS `kluster_wilayah` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `nama_kabupaten` varchar(100) NOT NULL,
                `kode_kabupaten` varchar(10) NOT NULL UNIQUE,
                `provinsi` varchar(100) DEFAULT 'Jawa Timur',
                `status` enum('active', 'inactive') DEFAULT 'active',
                `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->query($sqlTable);
            $this->db->execute();
        } catch (Throwable $e) {}

        try {
            $this->db->query("ALTER TABLE `tenants` ADD COLUMN `kluster_wilayah_id` int(11) DEFAULT NULL AFTER `database_type`");
            $this->db->execute();
        } catch (Throwable $ignore) {}

        try {
            $this->db->query("ALTER TABLE `users` ADD COLUMN `kluster_wilayah_id` int(11) DEFAULT NULL AFTER `tenant_id`");
            $this->db->execute();
        } catch (Throwable $ignore) {}

        // Seeding 38 Kabupaten/Kota di Jawa Timur jika masih kosong
        try {
            $this->db->query("SELECT COUNT(*) as cnt FROM `kluster_wilayah`");
            $row = $this->db->single();
            if ($row && ($row['cnt'] ?? 0) == 0) {
                $wilayah = [
                    ['nama_kabupaten' => 'Kab. Bangkalan', 'kode_kabupaten' => '3526'],
                    ['nama_kabupaten' => 'Kab. Banyuwangi', 'kode_kabupaten' => '3510'],
                    ['nama_kabupaten' => 'Kab. Blitar', 'kode_kabupaten' => '3505'],
                    ['nama_kabupaten' => 'Kab. Bojonegoro', 'kode_kabupaten' => '3522'],
                    ['nama_kabupaten' => 'Kab. Bondowoso', 'kode_kabupaten' => '3511'],
                    ['nama_kabupaten' => 'Kab. Gresik', 'kode_kabupaten' => '3525'],
                    ['nama_kabupaten' => 'Kab. Jember', 'kode_kabupaten' => '3509'],
                    ['nama_kabupaten' => 'Kab. Jombang', 'kode_kabupaten' => '3517'],
                    ['nama_kabupaten' => 'Kab. Kediri', 'kode_kabupaten' => '3506'],
                    ['nama_kabupaten' => 'Kab. Lamongan', 'kode_kabupaten' => '3524'],
                    ['nama_kabupaten' => 'Kab. Lumajang', 'kode_kabupaten' => '3508'],
                    ['nama_kabupaten' => 'Kab. Madiun', 'kode_kabupaten' => '3519'],
                    ['nama_kabupaten' => 'Kab. Magetan', 'kode_kabupaten' => '3520'],
                    ['nama_kabupaten' => 'Kab. Malang', 'kode_kabupaten' => '3507'],
                    ['nama_kabupaten' => 'Kab. Mojokerto', 'kode_kabupaten' => '3516'],
                    ['nama_kabupaten' => 'Kab. Nganjuk', 'kode_kabupaten' => '3518'],
                    ['nama_kabupaten' => 'Kab. Ngawi', 'kode_kabupaten' => '3521'],
                    ['nama_kabupaten' => 'Kab. Pacitan', 'kode_kabupaten' => '3501'],
                    ['nama_kabupaten' => 'Kab. Pamekasan', 'kode_kabupaten' => '3528'],
                    ['nama_kabupaten' => 'Kab. Pasuruan', 'kode_kabupaten' => '3514'],
                    ['nama_kabupaten' => 'Kab. Ponorogo', 'kode_kabupaten' => '3502'],
                    ['nama_kabupaten' => 'Kab. Probolinggo', 'kode_kabupaten' => '3513'],
                    ['nama_kabupaten' => 'Kab. Sampang', 'kode_kabupaten' => '3527'],
                    ['nama_kabupaten' => 'Kab. Sidoarjo', 'kode_kabupaten' => '3515'],
                    ['nama_kabupaten' => 'Kab. Situbondo', 'kode_kabupaten' => '3512'],
                    ['nama_kabupaten' => 'Kab. Sumenep', 'kode_kabupaten' => '3529'],
                    ['nama_kabupaten' => 'Kab. Trenggalek', 'kode_kabupaten' => '3503'],
                    ['nama_kabupaten' => 'Kab. Tuban', 'kode_kabupaten' => '3523'],
                    ['nama_kabupaten' => 'Kab. Tulungagung', 'kode_kabupaten' => '3504'],
                    ['nama_kabupaten' => 'Kota Batu', 'kode_kabupaten' => '3579'],
                    ['nama_kabupaten' => 'Kota Blitar', 'kode_kabupaten' => '3572'],
                    ['nama_kabupaten' => 'Kota Kediri', 'kode_kabupaten' => '3571'],
                    ['nama_kabupaten' => 'Kota Madiun', 'kode_kabupaten' => '3577'],
                    ['nama_kabupaten' => 'Kota Malang', 'kode_kabupaten' => '3573'],
                    ['nama_kabupaten' => 'Kota Mojokerto', 'kode_kabupaten' => '3576'],
                    ['nama_kabupaten' => 'Kota Pasuruan', 'kode_kabupaten' => '3575'],
                    ['nama_kabupaten' => 'Kota Probolinggo', 'kode_kabupaten' => '3574'],
                    ['nama_kabupaten' => 'Kota Surabaya', 'kode_kabupaten' => '3578'],
                ];
                foreach ($wilayah as $w) {
                    try {
                        $this->db->query("INSERT IGNORE INTO `kluster_wilayah` (`nama_kabupaten`, `kode_kabupaten`, `provinsi`, `status`) 
                                          VALUES (:nama, :kode, 'Jawa Timur', 'active')");
                        $this->db->bind('nama', $w['nama_kabupaten']);
                        $this->db->bind('kode', $w['kode_kabupaten']);
                        $this->db->execute();
                    } catch (Throwable $eIgnore) {}
                }
            }
        } catch (Throwable $eSeed) {}
    }

    /**
     * Mengambil semua data kluster wilayah diurutkan berdasarkan provinsi dan nama kabupaten
     */
    public function getAllKluster() {
        try {
            $this->db->query("SELECT * FROM " . $this->table . " ORDER BY provinsi ASC, nama_kabupaten ASC");
            return $this->db->resultSet() ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Mengambil data satu kluster wilayah berdasarkan ID
     */
    public function getKlusterById($id) {
        if (!$id) return null;
        try {
            $this->db->query("SELECT * FROM " . $this->table . " WHERE id = :id");
            $this->db->bind('id', $id);
            return $this->db->single() ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Menambahkan data kluster wilayah baru
     */
    public function tambahKluster($data) {
        $provinsi = !empty($data['provinsi']) ? $data['provinsi'] : 'Jawa Timur';
        $status = !empty($data['status']) ? $data['status'] : 'active';

        try {
            $query = "INSERT INTO " . $this->table . " (nama_kabupaten, kode_kabupaten, provinsi, status) 
                      VALUES (:nama_kabupaten, :kode_kabupaten, :provinsi, :status)";
            $this->db->query($query);
            $this->db->bind('nama_kabupaten', $data['nama_kabupaten']);
            $this->db->bind('kode_kabupaten', $data['kode_kabupaten']);
            $this->db->bind('provinsi', $provinsi);
            $this->db->bind('status', $status);
            $this->db->execute();
            return $this->db->rowCount();
        } catch (Throwable $e) {
            return 0;
        }
    }

    /**
     * Mengubah data kluster wilayah berdasarkan ID
     */
    public function ubahKluster($data) {
        $provinsi = !empty($data['provinsi']) ? $data['provinsi'] : 'Jawa Timur';
        $status = !empty($data['status']) ? $data['status'] : 'active';

        try {
            $query = "UPDATE " . $this->table . " SET 
                        nama_kabupaten = :nama_kabupaten, 
                        kode_kabupaten = :kode_kabupaten, 
                        provinsi = :provinsi, 
                        status = :status 
                      WHERE id = :id";
            $this->db->query($query);
            $this->db->bind('nama_kabupaten', $data['nama_kabupaten']);
            $this->db->bind('kode_kabupaten', $data['kode_kabupaten']);
            $this->db->bind('provinsi', $provinsi);
            $this->db->bind('status', $status);
            $this->db->bind('id', $data['id']);
            $this->db->execute();
            return $this->db->rowCount();
        } catch (Throwable $e) {
            return 0;
        }
    }

    /**
     * Menghapus data kluster wilayah berdasarkan ID
     */
    public function hapusKluster($id) {
        try {
            $this->db->query("DELETE FROM " . $this->table . " WHERE id = :id");
            $this->db->bind('id', $id);
            $this->db->execute();
            return $this->db->rowCount();
        } catch (Throwable $e) {
            return 0;
        }
    }

    /**
     * Mengambil semua kluster wilayah yang berstatus aktif diurutkan berdasarkan nama kabupaten
     */
    public function getActiveKluster() {
        try {
            $this->db->query("SELECT * FROM " . $this->table . " WHERE status = 'active' ORDER BY nama_kabupaten ASC");
            return $this->db->resultSet() ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }
}
