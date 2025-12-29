<?php

class Penerimaan extends Controller {
    use PeriodLockTrait;

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['judul'] = 'Penerimaan Pelanggan';
        $data['penerimaan'] = $this->model('Penerimaan')->getAllPenerimaan();
        $this->view('templates/header', $data);
        $this->view('penerimaan/index', $data);
        $this->view('templates/footer');
    }

    /**
     * FUNGSI YANG DIPERBARUI: Sekarang menggunakan 'sub_grup_akun' untuk memfilter.
     */
    public function tambah() {
        $data['judul'] = 'Tambah Penerimaan Pelanggan';
        $data['pelanggan'] = $this->model('Pelanggan')->getAllPelanggan();
        
        // Ambil semua akun
        $all_accounts = $this->model('Akun')->getAllAkun();
        $data['akun_kas_list'] = [];
        
        // Lakukan pemfilteran di sini, di dalam Controller
        foreach ($all_accounts as $akun) {
            if ($akun['tipe_akun'] == 'Detail') {
                // PERBAIKAN: Bersihkan data dan abaikan huruf besar/kecil
                if (trim(strtolower($akun['sub_grup_akun'] ?? '')) == 'kas & bank') {
                    $data['akun_kas_list'][] = $akun;
                }
            }
        }

        $this->view('templates/header', $data);
        $this->view('penerimaan/tambah', $data);
        $this->view('templates/footer');
    }

    public function getFaktur($id_pelanggan) {
        header('Content-Type: application/json');
        $faktur = $this->model('Penerimaan')->getFakturBelumLunasByPelanggan($id_pelanggan);
        echo json_encode($faktur);
    }

    public function simpan() {
        $this->checkPeriodLock($_POST['tanggal'], BASEURL . '/penerimaan');
        $pelanggan = $this->model('Pelanggan')->getPelangganById($_POST['id_pelanggan']);
        $_POST['nama_pelanggan'] = $pelanggan['nama_pelanggan'];

        if ($this->model('Penerimaan')->simpanPenerimaan($_POST)) {
            Flash::setFlash('Penerimaan pembayaran berhasil disimpan.', 'success');
            header('Location: ' . BASEURL . '/penerimaan');
            exit;
        } else {
            header('Location: ' . BASEURL . '/penerimaan/tambah');
            exit;
        }
    }
    
    public function lihat($id) {
        $data['judul'] = 'Detail Penerimaan Pelanggan';
        $data['penerimaan'] = $this->model('Penerimaan')->getPenerimaanByIdWithDetails($id);
        if (!$data['penerimaan']) {
            Flash::setFlash('Bukti penerimaan tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/penerimaan');
            exit;
        }
        $this->view('templates/header', $data);
        $this->view('penerimaan/lihat', $data);
        $this->view('templates/footer');
    }
    
    public function hapus($id) {
        if (!Auth::isAdmin() && !Auth::isManager()) {
            Flash::setFlash('Anda tidak memiliki hak akses untuk tindakan ini.', 'danger');
            header('Location: ' . BASEURL . '/penerimaan');
            exit;
        }
        $penerimaan = $this->model('Penerimaan')->getPenerimaanByIdWithDetails($id);
        if ($penerimaan) {
            $this->checkPeriodLock($penerimaan['tanggal'], BASEURL . '/penerimaan');
        }
        
        // Panggil model untuk menghapus penerimaan
        if ($this->model('Penerimaan')->hapusPenerimaan($id)) {
            Flash::setFlash('Penerimaan berhasil dibatalkan.', 'success');
        } else {
            Flash::setFlash('Gagal membatalkan penerimaan.', 'danger');
        }
        header('Location: ' . BASEURL . '/penerimaan');
        exit;
    }
}