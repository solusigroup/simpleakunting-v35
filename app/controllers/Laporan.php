<?php

// Impor kelas-kelas yang dibutuhkan dari library eksternal
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Dompdf\Dompdf;
use Dompdf\Options;

class Laporan extends Controller {

    public function __construct() {
        parent::__construct();
        if (!Auth::isLoggedIn()) {
            Flash::setFlash('Anda harus login untuk mengakses halaman ini.', 'warning');
            header('Location: ' . BASEURL . '/login');
            exit;
        }
    }

    public function index() {
        $data['judul'] = 'Pusat Laporan';
        $this->view('templates/header', $data);
        $this->view('laporan/index', $data);
        $this->view('templates/footer');
    }

    // --- VIEW METHODS ---

    public function bukuBesar() {
        $data['judul'] = 'Buku Besar';
        $data['akun'] = $this->model('Akun')->getAllAkun($this->tenantId());
        $data['laporan'] = null;
        $data['kode_akun_terpilih'] = $_POST['kode_akun'] ?? null;
        $data['nama_akun_terpilih'] = '';
        $data['tanggal_mulai'] = $_POST['tanggal_mulai'] ?? date('Y-m-01');
        $data['tanggal_selesai'] = $_POST['tanggal_selesai'] ?? date('Y-m-t');

        if (!empty($data['kode_akun_terpilih'])) {
            $data = array_merge($data, $this->_prepareLaporanData('getBukuBesar', [
                'kode_akun' => $data['kode_akun_terpilih'],
                'tanggal_mulai' => $data['tanggal_mulai'],
                'tanggal_selesai' => $data['tanggal_selesai'],
            ]));
            $akun_info = $this->model('Akun')->getAkunByKode($data['kode_akun_terpilih'], $this->tenantId());
            $data['nama_akun_terpilih'] = $akun_info['nama_akun'];
        } else {
            $data['perusahaan'] = $this->model('Perusahaan')->getPerusahaan($this->tenantId());
        }

        $this->view('templates/header', $data);
        $this->view('laporan/bukubesar', $data);
        $this->view('templates/footer');
    }

    public function neracaSaldo() {
        $data['judul'] = 'Neraca Saldo';
        $params = ['tanggal_selesai' => $_POST['tanggal_selesai'] ?? date('Y-m-d')];
        $data = array_merge($data, $this->_prepareLaporanData('getNeracaSaldo', $params));
        $data['tanggal_selesai'] = $params['tanggal_selesai'];
        $this->view('templates/header', $data);
        $this->view('laporan/neracasaldo', $data);
        $this->view('templates/footer');
    }

    public function labaRugi() {
        $data['judul'] = 'Laporan Laba Rugi Komparatif';
        $params = [
            'tanggal_mulai_1' => $_POST['tanggal_mulai_1'] ?? date('Y-m-01'),
            'tanggal_selesai_1' => $_POST['tanggal_selesai_1'] ?? date('Y-m-t'),
            'tanggal_mulai_2' => !empty($_POST['tanggal_mulai_2']) ? $_POST['tanggal_mulai_2'] : null,
            'tanggal_selesai_2' => !empty($_POST['tanggal_selesai_2']) ? $_POST['tanggal_selesai_2'] : null,
        ];
        $data = array_merge($data, $this->_prepareLaporanData('getLabaRugi', $params));
        $data = array_merge($data, $params);
        $this->view('templates/header', $data);
        $this->view('laporan/labarugi', $data);
        $this->view('templates/footer');
    }

    public function posisiKeuangan() {
        $data['judul'] = 'Laporan Posisi Keuangan Komparatif';
        $params = [
            'tanggal_selesai_1' => $_POST['tanggal_selesai_1'] ?? date('Y-m-t'),
            'tanggal_selesai_2' => !empty($_POST['tanggal_selesai_2']) ? $_POST['tanggal_selesai_2'] : null,
        ];
        $data = array_merge($data, $this->_prepareLaporanData('getPosisiKeuangan', $params));
        $data = array_merge($data, $params);
        $this->view('templates/header', $data);
        $this->view('laporan/posisikeuangan', $data);
        $this->view('templates/footer');
    }

    public function arusKas() {
        $data['judul'] = 'Laporan Arus Kas';
        $params = [
            'tanggal_mulai' => $_POST['tanggal_mulai'] ?? date('Y-m-01'),
            'tanggal_selesai' => $_POST['tanggal_selesai'] ?? date('Y-m-t'),
            'metode' => $_POST['metode'] ?? 'indirect',
        ];
        $data = array_merge($data, $this->_prepareLaporanData('getArusKas', $params));
        $data = array_merge($data, $params);
        $this->view('templates/header', $data);
        $this->view('laporan/aruskas', $data);
        $this->view('templates/footer');
    }

    public function produksi() {
        $data['judul'] = 'Laporan Produksi';
        $data['tanggal_mulai'] = $_POST['tanggal_mulai'] ?? date('Y-m-01');
        $data['tanggal_selesai'] = $_POST['tanggal_selesai'] ?? date('Y-m-t');
        
        $params = [
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai']
        ];
        
        $data = array_merge($data, $this->_prepareLaporanData('getLaporanProduksi', $params, 'Produksi'));
        
        $this->view('templates/header', $data);
        $this->view('laporan/produksi', $data);
        $this->view('templates/footer');
    }

    public function neracaLajur() {
        $data['judul'] = 'Neraca Lajur (10 Kolom)';
        $params = [
            'tanggal_mulai' => $_POST['tanggal_mulai'] ?? date('Y-01-01'),
            'tanggal_selesai' => $_POST['tanggal_selesai'] ?? date('Y-m-t'),
        ];
        $data = array_merge($data, $this->_prepareLaporanData('getNeracaLajurLengkap', $params));
        $data = array_merge($data, $params);
        $this->view('templates/header', $data);
        $this->view('laporan/neracalajur', $data);
        $this->view('templates/footer');
    }

    public function perubahanEkuitas() {
        $data['judul'] = 'Laporan Perubahan Ekuitas';
        $params = [
            'tanggal_mulai_1' => $_POST['tanggal_mulai_1'] ?? date('Y-01-01'),
            'tanggal_selesai_1' => $_POST['tanggal_selesai_1'] ?? date('Y-m-t'),
            'tanggal_mulai_2' => !empty($_POST['tanggal_mulai_2']) ? $_POST['tanggal_mulai_2'] : null,
            'tanggal_selesai_2' => !empty($_POST['tanggal_selesai_2']) ? $_POST['tanggal_selesai_2'] : null,
        ];
        $data = array_merge($data, $this->_prepareLaporanData('getPerubahanEkuitas', $params));
        $data = array_merge($data, $params);
        $this->view('templates/header', $data);
        $this->view('laporan/perubahanekuitas', $data);
        $this->view('templates/footer');
    }

    public function pemakaianBahan() {
        $data['judul'] = 'Laporan Pemakaian Bahan Baku';
        $params = [
            'tanggal_mulai' => $_POST['tanggal_mulai'] ?? date('Y-m-01'),
            'tanggal_selesai' => $_POST['tanggal_selesai'] ?? date('Y-m-t'),
        ];
        $data = array_merge($data, $this->_prepareLaporanData('getLaporanPemakaianBahan', $params, 'Produksi'));
        $data = array_merge($data, $params);
        $this->view('templates/header', $data);
        $this->view('laporan/pemakaianbahan', $data);
        $this->view('templates/footer');
    }

    // --- EXCEL EXPORT METHODS ---

    public function eksporBukuBesar() {
        $params = [
            'kode_akun' => $_POST['kode_akun_export'],
            'tanggal_mulai' => $_POST['tanggal_mulai_export'],
            'tanggal_selesai' => $_POST['tanggal_selesai_export'],
        ];
        $data = $this->_prepareLaporanData('getBukuBesar', $params);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', $data['perusahaan']['nama_perusahaan'])->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->setCellValue('A2', 'LAPORAN BUKU BESAR');
        $sheet->setCellValue('A3', 'Akun: [' . $params['kode_akun'] . '] ' . $this->model('Akun')->getAkunByKode($params['kode_akun'], $this->tenantId())['nama_akun']);
        $sheet->setCellValue('A4', 'Periode: ' . $data['periode_1']);
        
        $row = 6;
        $sheet->fromArray(['Tanggal', 'No. Bukti', 'Keterangan', 'Debit', 'Kredit', 'Saldo'], NULL, 'A' . $row);
        $sheet->getStyle('A'.$row.':F'.$row)->getFont()->setBold(true);
        $row++;
        
        $saldo = $data['laporan']['saldo_awal_periode'];
        $sheet->setCellValue('A'.$row, $params['tanggal_mulai'])->setCellValue('C'.$row, 'SALDO AWAL')->setCellValue('F'.$row, $saldo);
        $row++;
        foreach ($data['laporan']['transaksi'] as $trx) {
            $saldo += ($data['laporan']['posisi_saldo_normal'] == 'Debit') ? ($trx['debit'] - $trx['kredit']) : ($trx['kredit'] - $trx['debit']);
            $sheet->fromArray([$trx['tanggal'], $trx['no_transaksi'], $trx['deskripsi'], $trx['debit'], $trx['kredit'], $saldo], NULL, 'A' . $row);
            $row++;
        }
        $sheet->getStyle('D7:F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        foreach(range('A','F') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
        $this->_outputExcel($spreadsheet, 'BukuBesar_' . $params['kode_akun']);
    }

    public function eksporLabaRugi() {
        $params = [
            'tanggal_mulai_1' => $_POST['tanggal_mulai_1_export'],
            'tanggal_selesai_1' => $_POST['tanggal_selesai_1_export'],
            'tanggal_mulai_2' => $_POST['tanggal_mulai_2_export'] ?: null,
            'tanggal_selesai_2' => $_POST['tanggal_selesai_2_export'] ?: null,
        ];
        $data = $this->_prepareLaporanData('getLabaRugi', $params);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', $data['perusahaan']['nama_perusahaan'])->getStyle('A1')->getFont()->setBold(true);
        $sheet->setCellValue('A2', 'LAPORAN LABA RUGI');
        $sheet->setCellValue('A3', 'Periode: ' . $data['periode_1']);
        
        $row = 5;
        $sheet->setCellValue('A'.$row, 'Keterangan')->setCellValue('B'.$row, $data['periode_1']);
        if ($data['periode_2']) $sheet->setCellValue('C'.$row, $data['periode_2']);
        $sheet->getStyle('A'.$row.':C'.$row)->getFont()->setBold(true);
        $row++;
        
        foreach (['pendapatan' => 'PENDAPATAN', 'beban' => 'BEBAN'] as $key => $title) {
            $sheet->setCellValue('A'.$row, $title)->getStyle('A'.$row)->getFont()->setBold(true);
            $row++;
            foreach ($data['laporan'][$key] as $item) {
                $sheet->setCellValue('A'.$row, $item['nama_akun'])->setCellValue('B'.$row, $item['total_1']);
                if ($data['periode_2']) $sheet->setCellValue('C'.$row, $item['total_2']);
                $row++;
            }
            $row++;
        }
        $sheet->getStyle('B5:C' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        foreach(range('A','C') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
        $this->_outputExcel($spreadsheet, 'LabaRugi');
    }

    public function eksporPosisiKeuangan() {
        $params = [
            'tanggal_selesai_1' => $_POST['tanggal_selesai_1_export'],
            'tanggal_selesai_2' => $_POST['tanggal_selesai_2_export'] ?: null,
        ];
        $data = $this->_prepareLaporanData('getPosisiKeuangan', $params);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', $data['perusahaan']['nama_perusahaan'])->getStyle('A1')->getFont()->setBold(true);
        $sheet->setCellValue('A2', 'LAPORAN POSISI KEUANGAN');
        $sheet->setCellValue('A3', 'Per Tanggal: ' . $data['periode_1']);
        
        $row = 5;
        $sheet->setCellValue('A'.$row, 'Keterangan')->setCellValue('B'.$row, $data['periode_1']);
        if ($data['periode_2']) $sheet->setCellValue('C'.$row, $data['periode_2']);
        $sheet->getStyle('A'.$row.':C'.$row)->getFont()->setBold(true);
        $row++;
        
        foreach (['aset' => 'ASET', 'kewajiban' => 'KEWAJIBAN', 'modal' => 'EKUITAS'] as $key => $title) {
            $sheet->setCellValue('A'.$row, $title)->getStyle('A'.$row)->getFont()->setBold(true);
            $row++;
            foreach ($data['laporan']['periode_1'][$key] as $item) {
                $key2 = array_search($item['kode_akun'], array_column($data['laporan']['periode_2'][$key] ?? [], 'kode_akun'));
                $total2 = ($key2 !== false) ? $data['laporan']['periode_2'][$key][$key2]['total'] : 0;
                $sheet->setCellValue('A'.$row, $item['nama_akun'])->setCellValue('B'.$row, $item['total']);
                if ($data['periode_2']) $sheet->setCellValue('C'.$row, $total2);
                $row++;
            }
            $row++;
        }
        $sheet->getStyle('B5:C' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        foreach(range('A','C') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
        $this->_outputExcel($spreadsheet, 'PosisiKeuangan');
    }

    public function eksporArusKas() {
        $params = [
            'tanggal_mulai' => $_POST['tanggal_mulai_export'],
            'tanggal_selesai' => $_POST['tanggal_selesai_export'],
            'metode' => $_POST['metode_export'] ?? 'indirect',
        ];
        $data = $this->_prepareLaporanData('getArusKas', $params);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', $data['perusahaan']['nama_perusahaan'])->getStyle('A1')->getFont()->setBold(true);
        $sheet->setCellValue('A2', 'LAPORAN ARUS KAS (' . strtoupper($data['laporan']['metode']) . ')');
        $sheet->setCellValue('A3', 'Periode: ' . $data['periode_1']);
        
        $row = 5;
        $sheet->setCellValue('A'.$row, 'Keterangan')->setCellValue('B'.$row, 'Jumlah (Rp)');
        $sheet->getStyle('A'.$row.':B'.$row)->getFont()->setBold(true);
        $row++;
        
        $sheet->setCellValue('A'.$row, 'Arus Kas dari Aktivitas Operasi')->getStyle('A'.$row)->getFont()->setBold(true);
        $row++;
        if ($data['laporan']['metode'] == 'Indirect') {
            $sheet->setCellValue('A'.$row, 'Laba Bersih')->setCellValue('B'.$row, $data['laporan']['laba_bersih']);
            $row++;
            foreach($data['laporan']['penyesuaian'] as $item) {
                $sheet->setCellValue('A'.$row, $item['label'])->setCellValue('B'.$row, $item['jumlah']);
                $row++;
            }
        } else {
            foreach($data['laporan']['arus_operasi'] as $item) {
                $sheet->setCellValue('A'.$row, $item['label'])->setCellValue('B'.$row, $item['jumlah']);
                $row++;
            }
        }
        $row++;
        $sheet->setCellValue('A'.$row, 'Kas Awal Periode')->setCellValue('B'.$row, $data['laporan']['kas_awal']);
        $row++;
        $sheet->setCellValue('A'.$row, 'Kas Akhir Periode')->setCellValue('B'.$row, $data['laporan']['kas_akhir'])->getStyle('A'.$row.':B'.$row)->getFont()->setBold(true);
        
        $sheet->getStyle('B5:B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        foreach(range('A','B') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
        $this->_outputExcel($spreadsheet, 'ArusKas');
    }

    public function eksporNeracaSaldo() {
        $params = ['tanggal_selesai' => $_POST['tanggal_selesai_export'] ?? date('Y-m-d')];
        $data = $this->_prepareLaporanData('getNeracaSaldo', $params);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', $data['perusahaan']['nama_perusahaan'])->getStyle('A1')->getFont()->setBold(true);
        $sheet->setCellValue('A2', 'LAPORAN NERACA SALDO');
        $sheet->setCellValue('A3', 'Per Tanggal: ' . $data['periode_1']);
        
        $row = 5;
        $sheet->fromArray(['Kode Akun', 'Nama Akun', 'Debit', 'Kredit'], NULL, 'A' . $row);
        $sheet->getStyle('A'.$row.':D'.$row)->getFont()->setBold(true);
        $row++;
        
        foreach ($data['laporan'] as $item) {
            $sheet->fromArray([$item['kode_akun'], $item['nama_akun'], $item['debit'], $item['kredit']], NULL, 'A' . $row);
            $row++;
        }
        $sheet->getStyle('C6:D' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        foreach(range('A','D') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
        $this->_outputExcel($spreadsheet, 'NeracaSaldo');
    }

    // --- PDF EXPORT METHODS ---

    public function eksporPdfBukuBesar() {
        $params = [
            'kode_akun' => $_POST['kode_akun_export'],
            'tanggal_mulai' => $_POST['tanggal_mulai_export'],
            'tanggal_selesai' => $_POST['tanggal_selesai_export'],
        ];
        $data = $this->_prepareLaporanData('getBukuBesar', $params);
        $akun_info = $this->model('Akun')->getAkunByKode($params['kode_akun'], $this->tenantId());
        $data['nama_akun_terpilih'] = $akun_info['nama_akun'];
        $data['kode_akun_terpilih'] = $params['kode_akun'];
        $data['tanggal_mulai'] = $params['tanggal_mulai'];
        $this->_generatePdf('laporan/bukubesar_pdf', $data, 'BukuBesar_' . $params['kode_akun']);
    }

    public function eksporPdfLabaRugi() {
        $params = [
            'tanggal_mulai_1' => $_POST['tanggal_mulai_1_export'],
            'tanggal_selesai_1' => $_POST['tanggal_selesai_1_export'],
            'tanggal_mulai_2' => $_POST['tanggal_mulai_2_export'] ?: null,
            'tanggal_selesai_2' => $_POST['tanggal_selesai_2_export'] ?: null,
        ];
        $data = $this->_prepareLaporanData('getLabaRugi', $params);
        $this->_generatePdf('laporan/labarugi_pdf', $data, 'LabaRugi');
    }

    public function eksporPdfPosisiKeuangan() {
        $params = [
            'tanggal_selesai_1' => $_POST['tanggal_selesai_1_export'],
            'tanggal_selesai_2' => $_POST['tanggal_selesai_2_export'] ?: null,
        ];
        $data = $this->_prepareLaporanData('getPosisiKeuangan', $params);
        $this->_generatePdf('laporan/posisikeuangan_pdf', $data, 'PosisiKeuangan');
    }

    public function eksporPdfArusKas() {
        $params = [
            'tanggal_mulai' => $_POST['tanggal_mulai_export'],
            'tanggal_selesai' => $_POST['tanggal_selesai_export'],
            'metode' => $_POST['metode_export'] ?? 'indirect',
        ];
        $data = $this->_prepareLaporanData('getArusKas', $params);
        $this->_generatePdf('laporan/aruskas_pdf', $data, 'ArusKas');
    }

    public function eksporPdfNeracaSaldo() {
        $params = ['tanggal_selesai' => $_POST['tanggal_selesai_export'] ?? date('Y-m-d')];
        $data = $this->_prepareLaporanData('getNeracaSaldo', $params);
        $this->_generatePdf('laporan/neracasaldo_pdf', $data, 'NeracaSaldo');
    }

    public function audit() {
        if (!Auth::isAdmin() && !Auth::isActuallySuperadmin()) {
            Flash::setFlash('Akses Ditolak', 'Hanya Administrator yang dapat melihat log aktivitas.', 'danger');
            header('Location: ' . BASEURL . '/laporan');
            exit;
        }
        $data['judul'] = 'Audit Log Aktivitas';
        // Ambil 100 log terbaru untuk tenant ini
        $this->db->query("SELECT * FROM activity_logs WHERE tenant_id = :tenant_id ORDER BY created_at DESC LIMIT 100");
        $this->db->bind('tenant_id', $this->tenantId());
        $data['logs'] = $this->db->resultSet();
        
        $this->view('templates/header', $data);
        $this->view('laporan/audit', $data);
        $this->view('templates/footer');
    }

    // --- HELPERS ---

    private function _prepareLaporanData($modelFunction, $params, $modelName = 'Jurnal') {
        $tenant_id = $this->tenantId();
        $call_params = array_values($params);
        $call_params[] = $tenant_id;
        if ($modelFunction) $data['laporan'] = call_user_func_array([$this->model($modelName), $modelFunction], $call_params);
        
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
        $user2 = $p2_id ? $userModel->getUserById($p2_id, $tenant_id) : ['nama_user' => '(Belum Diatur)', 'jabatan' => 'Bendahara'];
        if (!empty($user2['nama_lengkap'])) $user2['nama_user'] = $user2['nama_lengkap'];
        $data['penandatangan_2'] = $user2;
        
        $data['kota_laporan'] = $data['perusahaan']['kota_laporan'] ?? 'Mojokerto';
        
        // Periode strings
        if (isset($params['tanggal_mulai_1'])) {
            $data['periode_1'] = date('d/m/Y', strtotime($params['tanggal_mulai_1'])) . ' - ' . date('d/m/Y', strtotime($params['tanggal_selesai_1']));
            if (!empty($params['tanggal_mulai_2'])) $data['periode_2'] = date('d/m/Y', strtotime($params['tanggal_mulai_2'])) . ' - ' . date('d/m/Y', strtotime($params['tanggal_selesai_2']));
        } elseif (isset($params['tanggal_mulai'])) {
            $data['periode_1'] = date('d/m/Y', strtotime($params['tanggal_mulai'])) . ' - ' . date('d/m/Y', strtotime($params['tanggal_selesai']));
        } elseif (isset($params['tanggal_selesai_1'])) {
            $data['periode_1'] = date('d/m/Y', strtotime($params['tanggal_selesai_1']));
            if (!empty($params['tanggal_selesai_2'])) $data['periode_2'] = date('d/m/Y', strtotime($params['tanggal_selesai_2']));
        } elseif (isset($params['tanggal_selesai'])) {
            $data['periode_1'] = date('d/m/Y', strtotime($params['tanggal_selesai']));
        }
        return $data;
    }

    private function _generatePdf($view, $data, $filename) {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        ob_start();
        $this->view($view, $data);
        $html = ob_get_clean();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($filename . ".pdf", ["Attachment" => 0]);
        exit;
    }

    private function _outputExcel($spreadsheet, $filename) {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '_' . date('Ymd') . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}