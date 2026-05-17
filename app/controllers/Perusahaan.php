<?php

class Perusahaan extends Controller {

    public function __construct()
    {
        parent::__construct();
        $user = Auth::user();
        if ($user && $user['role'] === 'Superadmin') {
            Flash::setFlash('Superadmin tidak melakukan setting perusahaan', 'warning');
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }
    }

    /**
     * Menampilkan halaman utama Pengaturan Perusahaan.
     * Versi ini juga mengambil daftar pengguna dan daftar akun untuk dropdown.
     */
    public function index() {
        $data['judul'] = 'Pengaturan Perusahaan';
        // Ambil data perusahaan saat ini
        $data['perusahaan'] = $this->model('Perusahaan')->getPerusahaan($this->tenantId());
        // Ambil SEMUA pengguna untuk mengisi pilihan penandatangan
        $data['users'] = $this->model('User')->getAllUsers($this->tenantId());
        // **PERUBAHAN: Ambil SEMUA akun untuk mengisi pilihan akun kontrol**
        $data['akun'] = $this->model('Akun')->getAllAkun($this->tenantId());
        
        $this->view('templates/header', $data);
        $this->view('perusahaan/index', $data);
        $this->view('templates/footer');
    }

    /**
     * Memproses pembaruan data perusahaan, termasuk upload logo.
     */
    public function update() {
        $logo_path = null;
        
        // Cek apakah ada file logo baru yang diunggah dan valid
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['logo'];
            
            // 1. Validasi Ekstensi Gambar
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            // 2. Validasi MIME Type Gambar Asli (MIME Sniffing)
            $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($file_ext, $allowed_extensions) || !in_array($mime_type, $allowed_mimes)) {
                Flash::setFlash('Gagal! Format file logo tidak didukung. Hanya gambar (JPG, PNG, GIF, SVG) yang diperbolehkan.', 'danger');
                header('Location: ' . BASEURL . '/perusahaan');
                exit;
            }
            
            // 3. Gunakan Path Absolut Berbasis APPROOT agar stabil di semua environment
            $target_dir = APPROOT . "/public/img/logos/";
            // Bersihkan nama file dari karakter berbahaya
            $clean_filename = preg_replace('/[^a-zA-Z0-9_.-]/', '_', basename($file["name"]));
            $unique_name = uniqid() . '_' . $clean_filename;
            $target_file = $target_dir . $unique_name;

            // Pastikan direktori tujuan ada, jika tidak, buat
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            // Pindahkan file yang diunggah ke direktori tujuan
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                // Simpan path relatif ke public web root agar sesuai dengan database dan view
                $logo_path = "img/logos/" . $unique_name;
            } else {
                Flash::setFlash('Gagal memindahkan file logo ke direktori penyimpanan.', 'danger');
                header('Location: ' . BASEURL . '/perusahaan');
                exit;
            }
        }

        // Panggil model untuk memperbarui data di database
        if ($this->model('Perusahaan')->updatePerusahaan($_POST, $logo_path, $this->tenantId()) > 0) {
            Flash::setFlash('Data perusahaan berhasil diperbarui.', 'success');
        } else {
            // Jika tidak ada baris yang terpengaruh, berarti tidak ada perubahan data
            Flash::setFlash('Tidak ada perubahan data yang disimpan.', 'info');
        }
        header('Location: ' . BASEURL . '/perusahaan');
        exit;
    }
}

