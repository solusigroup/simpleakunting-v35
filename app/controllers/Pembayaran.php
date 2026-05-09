<?php

class Pembayaran extends Controller {
    use PeriodLockTrait;

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['judul'] = 'Pembayaran Pemasok';
        $data['pembayaran'] = $this->model('Pembayaran')->getAllPembayaran($this->tenantId());
        $this->view('templates/header', $data);
        $this->view('pembayaran/index', $data);
        $this->view('templates/footer');
    }

    public function tambah() {
        $data['judul'] = 'Tambah Pembayaran Pemasok';
        $data['pemasok'] = $this->model('Pemasok')->getAllPemasok($this->tenantId());
        
        // Ambil semua akun
        $all_accounts = $this->model('Akun')->getAllAkun($this->tenantId());
        $data['akun_kas_list'] = [];
        
        // STANDARDISASI: Menggunakan sub_grup_akun untuk konsistensi dengan controller lain
        // Lakukan pemfilteran di sini, di dalam Controller
        foreach ($all_accounts as $akun) {
            // Tampilkan semua akun Aset (Prefix 1) yang bukan Header
            if (substr($akun['kode_akun'], 0, 1) == '1' && $akun['tipe_akun'] != 'Header') {
                $data['akun_kas_list'][] = $akun;
            }
        }

        $this->view('templates/header', $data);
        $this->view('pembayaran/tambah', $data);
        $this->view('templates/footer');
    }

    public function getFaktur($id_pemasok) {
        header('Content-Type: application/json');
        $faktur = $this->model('Pembayaran')->getFakturBelumLunasByPemasok($id_pemasok, $this->tenantId());
        echo json_encode($faktur);
    }

    public function simpan() {
        $this->checkPeriodLock($_POST['tanggal'], BASEURL . '/pembayaran');
        $pemasok = $this->model('Pemasok')->getPemasokById($_POST['id_pemasok'], $this->tenantId());
        $_POST['nama_pemasok'] = $pemasok['nama_pemasok'];

        if ($this->model('Pembayaran')->simpanPembayaran($_POST, $this->tenantId())) {
            Flash::setFlash('Pembayaran kepada pemasok berhasil disimpan.', 'success');
            header('Location: ' . BASEURL . '/pembayaran');
            exit;
        } else {
            header('Location: ' . BASEURL . '/pembayaran/tambah');
            exit;
        }
    }

    public function lihat($id) {
        $data['judul'] = 'Detail Pembayaran Pemasok';
        $data['pembayaran'] = $this->model('Pembayaran')->getPembayaranByIdWithDetails($id, $this->tenantId());
        if (!$data['pembayaran']) {
            Flash::setFlash('Bukti pembayaran tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/pembayaran');
            exit;
        }
        $this->view('templates/header', $data);
        $this->view('pembayaran/lihat', $data);
        $this->view('templates/footer');
    }

    public function hapus($id) {
        if (!Auth::isAdmin() && !Auth::isManager()) {
            Flash::setFlash('Anda tidak memiliki hak akses untuk tindakan ini.', 'danger');
            header('Location: ' . BASEURL . '/pembayaran');
            exit;
        }
        
        $pembayaran = $this->model('Pembayaran')->getPembayaranByIdWithDetails($id, $this->tenantId());
        if ($pembayaran) {
            $this->checkPeriodLock($pembayaran['tanggal'], BASEURL . '/pembayaran');
        }
        
        if ($this->model('Pembayaran')->hapusPembayaran($id, $this->tenantId())) {
            Flash::setFlash('Pembayaran berhasil dibatalkan.', 'success');
        } else {
            Flash::setFlash('Gagal membatalkan pembayaran.', 'danger');
        }
        header('Location: ' . BASEURL . '/pembayaran');
        exit;
    }
}