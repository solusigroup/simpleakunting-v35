<?php

class TutupBuku extends Controller {
    public function __construct() {
        parent::__construct();
        if (!Auth::isLoggedIn() || (!Auth::isAdmin() && !Auth::isManager() && !Auth::isActuallySuperadmin())) {
            Flash::setFlash('Hanya Admin, Manajer, atau Superadmin yang dapat mengakses fitur Tutup Buku.', 'danger');
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }
    }

    public function index() {
        $tenantId = $this->tenantId();
        $tutupBukuModel = $this->model('TutupBuku');

        $data['judul'] = 'Tutup Buku & Period Lock - SimpleAkunting v3.5';
        $latestClosed = $tutupBukuModel->getLatestClosedPeriod($tenantId);
        $data['latest_closed'] = $latestClosed;
        
        $nextMonthPeriod = $latestClosed ? date('Y-m', strtotime($latestClosed['tahun'] . '-' . str_pad($latestClosed['bulan'], 2, '0', STR_PAD_LEFT) . '-01 +1 month')) : date('Y-m', strtotime('first day of last month'));
        $data['periode_bulanan_next'] = $nextMonthPeriod;
        
        $data['history_closed'] = $tutupBukuModel->getClosedPeriods($tenantId);
        $data['preview'] = null;

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tipe_proses'])) {
            $data['tipe_proses'] = $_POST['tipe_proses'];
            $data['periode_raw'] = $_POST['periode']; // YYYY-MM untuk bulanan, YYYY untuk tahunan

            if ($data['tipe_proses'] == 'Bulanan') {
                $data['periode_label'] = date('F Y', strtotime($data['periode_raw'] . '-01'));
                $data['preview'] = $tutupBukuModel->getClosingJournalPreview($data['periode_raw'], $tenantId, false);
            } else { // Tahunan
                $data['periode_label'] = 'Tahun ' . $data['periode_raw'];
                $data['preview'] = $tutupBukuModel->getClosingJournalPreview($data['periode_raw'], $tenantId, true);
            }
        }
        
        $this->view('templates/header', $data);
        $this->view('tutupbuku/index', $data);
        $this->view('templates/footer');
    }
    
    public function proses() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['periode'])) {
            $tenantId = $this->tenantId();
            $tutupBukuModel = $this->model('TutupBuku');
            $periode = $_POST['periode'];
            $tipe = $_POST['tipe_proses'] ?? 'Bulanan';

            if ($tutupBukuModel->prosesTutupBuku($periode, $tipe, $tenantId)) {
                Flash::setFlash("Proses tutup buku ($tipe: $periode) dan penguncian periode transaksi berhasil disimpan.", 'success');
            }
        }
        header('Location: ' . BASEURL . '/tutupBuku');
        exit;
    }

    public function buka_kembali($id_periode) {
        $tenantId = $this->tenantId();
        $tutupBukuModel = $this->model('TutupBuku');

        if ($tutupBukuModel->reopenPeriod((int)$id_periode, $tenantId)) {
            Flash::setFlash('Periode akuntansi berhasil dibuka kembali (Open). Transaksi pada periode ini dapat disesuaikan kembali.', 'warning');
        } else {
            Flash::setFlash('Gagal membuka kembali periode akuntansi.', 'danger');
        }

        header('Location: ' . BASEURL . '/tutupBuku');
        exit;
    }
}
