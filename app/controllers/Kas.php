<?php

class Kas extends Controller {
    use PeriodLockTrait;

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['judul'] = 'Transaksi Kas & Bank';
        $data['transaksi'] = $this->model('Kas')->getAllTransaksi();
        $this->view('templates/header', $data);
        $this->view('kas/index', $data);
        $this->view('templates/footer');
    }

    /**
     * FUNGSI YANG DIPERBARUI: Sekarang lebih "tahan banting" terhadap nilai NULL.
     */
    public function tambah() {
        $data['judul'] = 'Tambah Transaksi Kas & Bank';
        
        $all_accounts = $this->model('Akun')->getAllAkun();
        $data['akun_kas_list'] = [];
        $grouped_accounts = [];

        foreach ($all_accounts as $akun) {
            if ($akun['tipe_akun'] != 'Header') {
                // Akun Kas & Bank biasanya diawali dengan 1
                if (substr($akun['kode_akun'], 0, 1) == '1') {
                    $data['akun_kas_list'][] = $akun;
                } else {
                    $firstDigit = substr($akun['kode_akun'], 0, 1);
                    $grupNames = [
                        '1' => 'Aset',
                        '2' => 'Kewajiban',
                        '3' => 'Ekuitas',
                        '4' => 'Pendapatan',
                        '5' => 'HPP',
                        '6' => 'Beban',
                        '7' => 'Beban',
                        '8' => 'Pendapatan Lainnya',
                        '9' => 'Beban Lainnya'
                    ];
                    $grup = $grupNames[$firstDigit] ?? 'Lain-lain';
                    $grouped_accounts[$grup][] = $akun;
                }
            }
        }
        $data['akun_lawan_list'] = $grouped_accounts;

        $this->view('templates/header', $data);
        $this->view('kas/tambah', $data);
        $this->view('templates/footer');
    }

    public function simpan() {
        $this->checkPeriodLock($_POST['tanggal'], BASEURL . '/kas');
        if ($this->model('Kas')->simpanTransaksi($_POST)) {
            Flash::setFlash('Transaksi kas berhasil disimpan.', 'success');
        }
        header('Location: ' . BASEURL . '/kas');
        exit;
    }
    
    /**
     * FUNGSI YANG DIPERBARUI: Menggunakan logika filter yang sama dengan tambah().
     */
    public function edit($id) {
        $data['judul'] = 'Edit Transaksi Kas & Bank';
        $data['transaksi'] = $this->model('Kas')->getTransaksiById($id);
        
        $all_accounts = $this->model('Akun')->getAllAkun();
        $data['akun_kas_list'] = [];
        $grouped_accounts = [];
        foreach ($all_accounts as $akun) {
            if ($akun['tipe_akun'] != 'Header') {
                if (substr($akun['kode_akun'], 0, 1) == '1') {
                    $data['akun_kas_list'][] = $akun;
                } else {
                    $firstDigit = substr($akun['kode_akun'], 0, 1);
                    $grupNames = [
                        '1' => 'Aset',
                        '2' => 'Kewajiban',
                        '3' => 'Ekuitas',
                        '4' => 'Pendapatan',
                        '5' => 'HPP',
                        '6' => 'Beban',
                        '7' => 'Beban',
                        '8' => 'Pendapatan Lainnya',
                        '9' => 'Beban Lainnya'
                    ];
                    $grup = $grupNames[$firstDigit] ?? 'Lain-lain';
                    $grouped_accounts[$grup][] = $akun;
                }
            }
        }
        $data['akun_lawan_list'] = $grouped_accounts;

        $this->view('templates/header', $data);
        $this->view('kas/edit', $data);
        $this->view('templates/footer');
    }

    public function update() {
        $this->_checkPeriodLock($_POST['tanggal']);
        if ($this->model('Kas')->updateTransaksi($_POST)) {
            Flash::setFlash('Transaksi kas berhasil diperbarui.', 'success');
        }
        header('Location: ' . BASEURL . '/kas');
        exit;
    }

    public function hapus($id) {
        if (!Auth::isAdmin() && !Auth::isManager()) {
            Flash::setFlash('Anda tidak memiliki hak akses untuk tindakan ini.', 'danger');
            header('Location: ' . BASEURL . '/kas');
            exit;
        }
        $transaksi = $this->model('Kas')->getTransaksiById($id);
        if ($transaksi) {
            $this->checkPeriodLock($transaksi['tanggal'], BASEURL . '/kas');
        }
        if ($this->model('Kas')->hapusTransaksi($id)) {
            Flash::setFlash('Transaksi kas berhasil dibatalkan.', 'success');
        }
        header('Location: ' . BASEURL . '/kas');
        exit;
    }
}

