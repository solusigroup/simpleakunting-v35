<?php

class AsetBiologis extends Controller {

    public function __construct() {
        parent::__construct();
        if (!Auth::isLoggedIn()) {
            header('Location: ' . BASEURL . '/login');
            exit;
        }
    }

    public function index() {
        $data['judul'] = 'Manajemen Aset Biologis (PSAK 241)';
        
        $model = $this->model('AsetBiologis');
        $model->ensureTablesExist($this->tenantId());
        
        $filters = [];
        if (!empty($_GET['kategori'])) $filters['kategori'] = $_GET['kategori'];
        if (!empty($_GET['status'])) $filters['status'] = $_GET['status'];
        
        $data['aset'] = $model->getAllAsetBiologis($this->tenantId(), $filters);
        $data['akun'] = $this->model('Akun')->getAllAkun($this->tenantId());
        $data['ringkasan'] = $model->getRingkasanKlasifikasi($this->tenantId());
        
        $this->view('templates/header', $data);
        $this->view('asetbiologis/index', $data);
        $this->view('templates/footer');
    }

    public function migrate() {
        $model = $this->model('AsetBiologis');
        $model->runAutoMigration($this->tenantId());
        $model->seedBaganAkun($this->tenantId());
        Flash::setFlash('Tabel dan Bagan Akun Aset Biologis (PSAK 241) berhasil diinisialisasi!', 'success');
        header('Location: ' . BASEURL . '/asetbiologis');
        exit;
    }

    public function tambah() {
        $data['judul'] = 'Tambah Aset Biologis (PSAK 241)';
        $data['akun'] = $this->model('Akun')->getAllAkun($this->tenantId());
        $data['kode_otomatis'] = $this->generateAutoNumber('ABG', 'aset_biologis', 'kode_aset', $this->tenantId());
        
        $this->view('templates/header', $data);
        $this->view('asetbiologis/tambah', $data);
        $this->view('templates/footer');
    }

    public function detail($id) {
        $data['judul'] = 'Detail Aset Biologis';
        $data['aset'] = $this->model('AsetBiologis')->getAsetBiologisById($id, $this->tenantId());
        
        if (!$data['aset']) {
            Flash::setFlash('Data aset biologis tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/asetbiologis');
            exit;
        }
        
        $data['penyesuaian'] = $this->model('AsetBiologis')->getRiwayatPenyesuaian($id, $this->tenantId());
        $data['panen'] = $this->model('AsetBiologis')->getRiwayatPanen($id, $this->tenantId());
        $data['pelepasan'] = $this->model('AsetBiologis')->getRiwayatPelepasan($id, $this->tenantId());

        $this->view('templates/header', $data);
        $this->view('asetbiologis/detail', $data);
        $this->view('templates/footer');
    }

    public function edit($id) {
        $data['judul'] = 'Edit Aset Biologis';
        $data['aset'] = $this->model('AsetBiologis')->getAsetBiologisById($id, $this->tenantId());
        
        if (!$data['aset']) {
            Flash::setFlash('Data aset biologis tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/asetbiologis');
            exit;
        }

        $data['akun'] = $this->model('Akun')->getAllAkun($this->tenantId());

        $this->view('templates/header', $data);
        $this->view('asetbiologis/edit', $data);
        $this->view('templates/footer');
    }

    public function laporan() {
        $data = $this->_prepareLaporanData();
        $data['judul'] = 'Laporan Aset Biologis (PSAK 241)';

        if (isset($_GET['cetak']) && $_GET['cetak'] == '1') {
            $this->view('asetbiologis/cetak', $data);
            return;
        }

        $this->view('templates/header', $data);
        $this->view('asetbiologis/laporan', $data);
        $this->view('templates/footer');
    }

    public function cetak() {
        $data = $this->_prepareLaporanData();
        $data['judul'] = 'Laporan Aset Biologis (PSAK 241)';
        $this->view('asetbiologis/cetak', $data);
    }

    public function cetakLaporan() {
        $this->cetak();
    }

    private function _prepareLaporanData() {
        $tanggal_mulai = $_GET['mulai'] ?? $_GET['tanggal_mulai'] ?? $_POST['tanggal_mulai'] ?? $_POST['mulai'] ?? date('Y-01-01');
        $tanggal_selesai = $_GET['selesai'] ?? $_GET['tanggal_selesai'] ?? $_POST['tanggal_selesai'] ?? $_POST['selesai'] ?? date('Y-m-t');

        $tenant_id = $this->tenantId();
        $model = $this->model('AsetBiologis');

        $data['tanggal_mulai'] = $tanggal_mulai;
        $data['tanggal_selesai'] = $tanggal_selesai;
        $data['periode_1'] = date('d/m/Y', strtotime($tanggal_mulai)) . ' - ' . date('d/m/Y', strtotime($tanggal_selesai));

        $data['rekonsiliasi'] = $model->getRekonsiliasiNilaiTercatat(
            $tanggal_mulai, $tanggal_selesai, $tenant_id
        );
        $data['ringkasan'] = $model->getRingkasanKlasifikasi($tenant_id);

        $data['perusahaan'] = $this->model('Perusahaan')->getPerusahaan($tenant_id);
        if (!$data['perusahaan']) {
            $data['perusahaan'] = [
                'nama_perusahaan' => '(Perusahaan Belum Diatur)',
                'alamat' => '',
                'telepon' => '',
                'email' => '',
                'kota_laporan' => 'Mojokerto',
                'penandatangan_1_id' => null,
                'penandatangan_2_id' => null
            ];
        }

        $userModel = $this->model('User');
        $p1_id = $data['perusahaan']['penandatangan_1_id'] ?? null;
        $user1 = $p1_id ? $userModel->getUserById($p1_id, $tenant_id) : ['nama_user' => '(Belum Diatur)', 'jabatan' => 'Pimpinan'];
        if (!empty($user1['nama_lengkap'])) $user1['nama_user'] = $user1['nama_lengkap'];
        $data['penandatangan_1'] = $user1;

        $p2_id = $data['perusahaan']['penandatangan_2_id'] ?? null;
        $user2 = $p2_id ? $userModel->getUserById($p2_id, $tenant_id) : ['nama_user' => '(Belum Diatur)', 'jabatan' => 'Pengelola Aset / Akuntan'];
        if (!empty($user2['nama_lengkap'])) $user2['nama_user'] = $user2['nama_lengkap'];
        $data['penandatangan_2'] = $user2;

        $data['kota_laporan'] = $data['perusahaan']['kota_laporan'] ?? 'Mojokerto';

        return $data;
    }

    // === Action Methods ===

    public function simpan() {
        $result = $this->model('AsetBiologis')->simpanAsetBiologis($_POST, $this->tenantId());
        if ($result) {
            Flash::setFlash('Aset biologis berhasil ditambahkan dengan jurnal pengakuan awal otomatis (PSAK 241).', 'success');
        } else {
            Flash::setFlash('Gagal menambahkan aset biologis.', 'danger');
        }
        header('Location: ' . BASEURL . '/asetbiologis');
        exit;
    }

    public function update() {
        if ($this->model('AsetBiologis')->updateAsetBiologis($_POST, $this->tenantId()) > 0) {
            Flash::setFlash('Data aset biologis berhasil diperbarui.', 'success');
        } else {
            Flash::setFlash('Tidak ada perubahan pada data aset biologis.', 'info');
        }
        header('Location: ' . BASEURL . '/asetbiologis');
        exit;
    }

    public function hapus($id) {
        $result = $this->model('AsetBiologis')->hapusAsetBiologis($id, $this->tenantId());
        if ($result) {
            Flash::setFlash('Aset biologis berhasil dihapus.', 'success');
        } else {
            Flash::setFlash('Gagal menghapus aset. Aset memiliki riwayat transaksi (penyesuaian/panen/pelepasan).', 'danger');
        }
        header('Location: ' . BASEURL . '/asetbiologis');
        exit;
    }

    public function sesuaikan_batch() {
        $count = $this->model('AsetBiologis')->prosesPenyesuaianBatch($_POST, $this->tenantId());
        if ($count > 0) {
            Flash::setFlash($count . ' aset biologis berhasil disesuaikan nilai wajarnya dengan jurnal otomatis ke Laba Rugi.', 'success');
        } else {
            Flash::setFlash('Tidak ada perubahan nilai wajar yang diproses.', 'info');
        }
        header('Location: ' . BASEURL . '/asetbiologis');
        exit;
    }

    // Alias for sesuaikan_batch
    public function penyesuaianNilaiWajar() {
        $this->sesuaikan_batch();
    }

    public function panen() {
        $result = $this->model('AsetBiologis')->prosesPanen($_POST, $this->tenantId());
        if ($result) {
            Flash::setFlash('Panen produk agrikultur berhasil dicatat dengan jurnal otomatis ke akun persediaan (IAS 41 / PSAK 241).', 'success');
        } else {
            Flash::setFlash('Gagal mencatat panen. Periksa kembali kuantitas dan nilai wajar.', 'danger');
        }
        header('Location: ' . BASEURL . '/asetbiologis');
        exit;
    }

    public function pelepasan() {
        $result = $this->model('AsetBiologis')->prosesPelepasan($_POST, $this->tenantId());
        if ($result) {
            Flash::setFlash('Pelepasan aset biologis berhasil dicatat dengan jurnal otomatis.', 'success');
        } else {
            Flash::setFlash('Gagal mencatat pelepasan aset biologis.', 'danger');
        }
        header('Location: ' . BASEURL . '/asetbiologis');
        exit;
    }

    // Alias for pelepasan
    public function lepas() {
        $this->pelepasan();
    }

    public function reklasifikasi($id = null) {
        $targetId = $id ?? $_POST['id_aset'] ?? null;
        if (!$targetId) {
            header('Location: ' . BASEURL . '/asetbiologis');
            exit;
        }

        $result = $this->model('AsetBiologis')->reklasifikasi($targetId, $this->tenantId());
        if ($result) {
            Flash::setFlash('Aset biologis berhasil direklasifikasi menjadi status Menghasilkan (Matang).', 'success');
        } else {
            Flash::setFlash('Gagal mereklasifikasi aset biologis.', 'danger');
        }
        header('Location: ' . BASEURL . '/asetbiologis/detail/' . $targetId);
        exit;
    }
}
