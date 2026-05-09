<?php

class Kas extends Controller {
    use PeriodLockTrait;

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['judul'] = 'Transaksi Kas & Bank';
        $data['transaksi'] = $this->model('Kas')->getAllTransaksi($this->tenantId());
        $this->view('templates/header', $data);
        $this->view('kas/index', $data);
        $this->view('templates/footer');
    }

    public function tambah($tipe = 'Masuk') {
        $data['judul'] = 'Tambah Transaksi Kas ' . $tipe;
        $data['tipe'] = $tipe;
        
        $prefix = ($tipe == 'Masuk') ? 'KM' : 'KK';
        $data['no_bukti'] = $this->generateAutoNumber($prefix, 'kas_transaksi', 'no_bukti', $this->tenantId());

        $all_accounts = $this->model('Akun')->getAllAkun($this->tenantId());
        $data['akun_kas_list'] = [];
        $grouped_accounts = [];

        foreach ($all_accounts as $akun) {
            if ($akun['tipe_akun'] != 'Header') {
                if (substr($akun['kode_akun'], 0, 1) == '1') {
                    $data['akun_kas_list'][] = $akun;
                } else {
                    $firstDigit = substr($akun['kode_akun'], 0, 1);
                    $grupNames = ['1' => 'Aset', '2' => 'Kewajiban', '3' => 'Ekuitas', '4' => 'Pendapatan', '5' => 'HPP', '6' => 'Beban', '7' => 'Beban', '8' => 'Pendapatan Lainnya', '9' => 'Beban Lainnya'];
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
        if ($this->model('Kas')->simpanTransaksi($_POST, $this->tenantId())) {
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
        $data['transaksi'] = $this->model('Kas')->getTransaksiById($id, $this->tenantId());
        
        $all_accounts = $this->model('Akun')->getAllAkun($this->tenantId());
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
        if ($this->model('Kas')->updateTransaksi($_POST, $this->tenantId())) {
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
        $transaksi = $this->model('Kas')->getTransaksiById($id, $this->tenantId());
        if ($transaksi) {
            $this->checkPeriodLock($transaksi['tanggal'], BASEURL . '/kas');
        }
        if ($this->model('Kas')->hapusTransaksi($id, $this->tenantId())) {
            Flash::setFlash('Transaksi kas berhasil dibatalkan.', 'success');
        }
        header('Location: ' . BASEURL . '/kas');
        exit;
    }
}

