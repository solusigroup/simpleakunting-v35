<?php

class Jurnal extends Controller {

    public function __construct() {
        parent::__construct();
        if (!Auth::isLoggedIn()) {
            Flash::setFlash('Silakan login terlebih dahulu.', 'warning');
            header('Location: ' . BASEURL . '/login');
            exit;
        }
        if (!Auth::hasPermission('fin_jurnal')) {
            Flash::setFlash('Anda tidak memiliki izin untuk mengakses Riwayat Jurnal.', 'danger');
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }
    }

    public function index()
    {
        $data['judul'] = 'Riwayat Jurnal';
        $tenantId = $this->tenantId();
        if (empty($tenantId)) {
            if (Auth::isActuallySuperadmin()) {
                Flash::setFlash('Pilih Tenant Terlebih Dahulu', 'Untuk melihat atau mencatat Riwayat Jurnal, silakan pilih tenant di Manajemen Tenant atau pantau melalui menu Agregat / Monitoring.', 'info');
                header('Location: ' . BASEURL . '/tenants');
                exit;
            } else {
                Flash::setFlash('Akses Ditolak', 'Tenant tidak valid atau belum dipilih.', 'danger');
                header('Location: ' . BASEURL . '/dashboard');
                exit;
            }
        }

        $id_unit = $_GET['id_unit'] ?? null;
        $jurnal = $this->model('Jurnal')->getAllJurnal($tenantId, $id_unit);
        $data['jurnal'] = is_array($jurnal) ? $jurnal : [];
        $units = $this->model('Unit')->getAllUnits($tenantId);
        $data['units'] = is_array($units) ? $units : [];
        $data['selected_unit'] = $id_unit;
        
        $this->view('templates/header', $data);
        $this->view('jurnal/index', $data);
        $this->view('templates/footer');
    }

    public function detail($id)
    {
        $data['judul'] = 'Detail Entri Jurnal';
        $data['jurnal'] = $this->model('Jurnal')->getJurnalWithDetailsById($id, $this->tenantId());
        
        if ($data['jurnal'] === null) {
            Flash::setFlash('Gagal! Entri jurnal tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/jurnal');
            exit;
        }

        $this->view('templates/header', $data);
        $this->view('jurnal/detail', $data);
        $this->view('templates/footer');
    }

    public function cetak($id)
    {
        $tenantId = $this->tenantId();
        $data['jurnal'] = $this->model('Jurnal')->getJurnalWithDetailsById($id, $tenantId);
        if ($data['jurnal'] === null) {
            Flash::setFlash('Gagal! Entri jurnal tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/jurnal');
            exit;
        }
        $perusahaanTenantId = !empty($data['jurnal']['tenant_id']) ? $data['jurnal']['tenant_id'] : $tenantId;
        $data['perusahaan'] = $this->model('Perusahaan')->getPerusahaan($perusahaanTenantId);
        $this->view('jurnal/cetak', $data);
    }



    public function tambah()
    {
        $tenantId = $this->tenantId();
        if (empty($tenantId)) {
            Flash::setFlash('Pilih tenant terlebih dahulu.', 'warning');
            header('Location: ' . BASEURL . '/tenants');
            exit;
        }

        $data['judul'] = 'Tambah Entri Jurnal';
        $data['akun'] = $this->model('Akun')->getAllAkun($this->tenantId());
        $data['programs'] = $this->model('Program')->getAllPrograms($this->tenantId());
        $units = $this->model('Unit')->getAllUnits($tenantId);
        $data['units'] = is_array($units) ? $units : [];
        $data['no_transaksi'] = $this->model('Jurnal')->generateNoTransaksi($this->tenantId());

        $this->view('templates/header', $data);
        $this->view('jurnal/tambah', $data);
        $this->view('templates/footer');
    }

    public function simpan()
    {
        $totalDebit = isset($_POST['details']['debit']) ? array_sum($_POST['details']['debit']) : 0;
        $totalKredit = isset($_POST['details']['kredit']) ? array_sum($_POST['details']['kredit']) : 0;

        if ($totalDebit !== $totalKredit || $totalDebit == 0) {
            Flash::setFlash('Gagal! Total Debit dan Kredit harus sama dan tidak boleh nol.', 'danger');
            header('Location: ' . BASEURL . '/jurnal/tambah');
            exit;
        }

        $formattedData = [
            'no_transaksi' => $_POST['no_transaksi'],
            'tanggal' => $_POST['tanggal'],
            'deskripsi' => $_POST['deskripsi'],
            'id_program' => !empty($_POST['id_program']) ? $_POST['id_program'] : null,
            'id_unit' => !empty($_POST['id_unit']) ? $_POST['id_unit'] : null,
            'details' => []
        ];

        if (isset($_POST['details']['kode_akun'])) {
            foreach ($_POST['details']['kode_akun'] as $index => $kode_akun) {
                $formattedData['details'][] = [
                    'kode_akun' => $kode_akun,
                    'debit' => $_POST['details']['debit'][$index] ?? 0,
                    'kredit' => $_POST['details']['kredit'][$index] ?? 0
                ];
            }
        }

        try {
            if ($this->model('Jurnal')->simpanJurnal($formattedData, $this->tenantId()) > 0) {
                Flash::setFlash('Entri jurnal berhasil disimpan.', 'success');
                header('Location: ' . BASEURL . '/jurnal');
                exit;
            } else {
                Flash::setFlash('Gagal menyimpan entri jurnal.', 'danger');
                header('Location: ' . BASEURL . '/jurnal/tambah');
                exit;
            }
        } catch (\Throwable $e) {
            Flash::setFlash($e->getMessage(), 'danger');
            header('Location: ' . BASEURL . '/jurnal/tambah');
            exit;
        }
    }

    /**
     * Menampilkan form untuk mengedit jurnal dengan logika yang lebih cerdas.
     */
    public function edit($id)
    {
        $tenantId = $this->tenantId();
        if (empty($tenantId)) {
            Flash::setFlash('Pilih tenant terlebih dahulu.', 'warning');
            header('Location: ' . BASEURL . '/tenants');
            exit;
        }

        $data['judul'] = 'Edit Entri Jurnal';
        $jurnal_model = $this->model('Jurnal');
        $data['jurnal'] = $jurnal_model->getJurnalWithDetailsById($id, $this->tenantId());
        $data['akun'] = $this->model('Akun')->getAllAkun($this->tenantId());
        $data['programs'] = $this->model('Program')->getAllPrograms($this->tenantId());
        $units = $this->model('Unit')->getAllUnits($tenantId);
        $data['units'] = is_array($units) ? $units : [];

        // PERBAIKAN: Cek apakah jurnal ditemukan SEBELUM melanjutkan
        if ($data['jurnal'] === null) {
            Flash::setFlash('Gagal! Entri jurnal dengan ID tersebut tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/jurnal');
            exit;
        }

        // Cek jika jurnal terkunci (logika ini sekarang aman)
        if ($data['jurnal']['is_locked']) {
            Flash::setFlash('Gagal! Jurnal ini terkunci dan tidak dapat diubah.', 'warning');
            header('Location: ' . BASEURL . '/jurnal');
            exit;
        }

        $this->view('templates/header', $data);
        $this->view('jurnal/edit', $data);
        $this->view('templates/footer');
    }

    public function update()
    {
        $totalDebit = isset($_POST['details']['debit']) ? array_sum($_POST['details']['debit']) : 0;
        $totalKredit = isset($_POST['details']['kredit']) ? array_sum($_POST['details']['kredit']) : 0;

        if ($totalDebit !== $totalKredit || $totalDebit == 0) {
            Flash::setFlash('Gagal! Total Debit dan Kredit harus sama dan tidak boleh nol.', 'danger');
            header('Location: ' . BASEURL . '/jurnal/edit/' . $_POST['id_jurnal']);
            exit;
        }
        
        $formattedData = [
            'id_jurnal' => $_POST['id_jurnal'],
            'no_transaksi' => $_POST['no_transaksi'],
            'tanggal' => $_POST['tanggal'],
            'deskripsi' => $_POST['deskripsi'],
            'id_program' => !empty($_POST['id_program']) ? $_POST['id_program'] : null,
            'id_unit' => !empty($_POST['id_unit']) ? $_POST['id_unit'] : null,
            'details' => []
        ];
        
        if (isset($_POST['details']['kode_akun'])) {
            foreach ($_POST['details']['kode_akun'] as $index => $kode_akun) {
                $formattedData['details'][] = [
                    'kode_akun' => $kode_akun,
                    'debit' => $_POST['details']['debit'][$index] ?? 0,
                    'kredit' => $_POST['details']['kredit'][$index] ?? 0
                ];
            }
        }

        try {
            $result = $this->model('Jurnal')->updateJurnal($formattedData, $this->tenantId());

            if ($result > 0) {
                Flash::setFlash('Entri jurnal berhasil diperbarui.', 'success');
            } elseif ($result < 0) {
                Flash::setFlash('Gagal! Jurnal ini terkunci dan tidak dapat diubah.', 'warning');
            } else {
                Flash::setFlash('Gagal memperbarui entri jurnal.', 'danger');
            }
            header('Location: ' . BASEURL . '/jurnal');
            exit;
        } catch (\Throwable $e) {
            Flash::setFlash($e->getMessage(), 'danger');
            header('Location: ' . BASEURL . '/jurnal/edit/' . $_POST['id_jurnal']);
            exit;
        }
    }

    public function hapus($id)
    {
        try {
            if ($this->model('Jurnal')->hapusJurnal($id, $this->tenantId()) > 0) {
                Flash::setFlash('Entri jurnal berhasil dihapus.', 'success');
            } else {
                Flash::setFlash('Gagal menghapus entri jurnal. Kemungkinan jurnal ini terkunci.', 'danger');
            }
        } catch (\Throwable $e) {
            Flash::setFlash($e->getMessage(), 'danger');
        }
        header('Location: ' . BASEURL . '/jurnal');
        exit;
    }
    public function isPeriodClosed($tanggal) {
        $tahun = date('Y', strtotime($tanggal));
        $bulan = date('m', strtotime($tanggal));

        $this->db->query("SELECT status FROM periode_akuntansi WHERE tahun = :tahun AND bulan = :bulan");
        $this->db->bind('tahun', $tahun);
        $this->db->bind('bulan', $bulan);
        
        $result = $this->db->single();
        
        return ($result && $result['status'] === 'Closed');
    }
}

