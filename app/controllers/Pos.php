<?php
/**
 * POS Controller for SimpleAkunting v3.5
 * Manages the Point of Sales interface and checkout
 */
class Pos extends Controller {
    use PeriodLockTrait;

    public function __construct() {
        parent::__construct();
        // Permission check
        if (!Auth::hasPermission('trx_pos')) {
            Flash::setFlash('Anda tidak memiliki akses ke modul Point of Sales.', 'danger');
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }
    }

    // Cashier UI
    public function index() {
        $tenantId = $this->tenantId();
        $posModel = $this->model('Pos');
        
        $data['judul'] = 'Point of Sales (Kasir)';
        $data['barang'] = $posModel->getAllBarangAktif($tenantId);
        $data['pelanggan'] = $this->model('Pelanggan')->getAllPelanggan($tenantId);
        $data['akun_kas'] = $this->model('Akun')->getAkunKasBank($tenantId);
        $data['perusahaan'] = $this->model('Perusahaan')->getPerusahaan($tenantId);
        $data['no_receipt'] = $posModel->generateReceiptNumber($tenantId);
        $data['walk_in'] = $posModel->getWalkInCustomer($tenantId);
        $data['statistik'] = $posModel->getStatistikHariIni($tenantId);

        // Standalone fullscreen cashier screen
        $this->view('pos/index', $data);
    }

    // AJAX product search
    public function cari_barang() {
        $keyword = $_GET['q'] ?? '';
        $results = $this->model('Pos')->cariBarang($keyword, $this->tenantId());
        header('Content-Type: application/json');
        echo json_encode($results);
        exit;
    }

    // Process POS transaction (AJAX)
    public function simpan() {
        header('Content-Type: application/json');

        try {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                $input = $_POST;
            }

            // CSRF protection check
            $csrf = $input['csrf_token'] ?? '';
            if (empty($csrf) || !hash_equals($_SESSION['csrf_token'] ?? '', $csrf)) {
                echo json_encode(['success' => false, 'message' => 'Token keamanan (CSRF) tidak valid. Harap reload halaman.']);
                exit;
            }

            $tenantId = $this->tenantId();
            $posModel = $this->model('Pos');

            // 1. Period Lock Check
            $today = date('Y-m-d');
            $this->checkPeriodLock($today, null); // passing null redirect since it's an AJAX call and will throw an exception instead of redirecting if locked

            // 2. Customer
            $id_pelanggan = $input['id_pelanggan'] ?? null;
            if (empty($id_pelanggan)) {
                $walkIn = $posModel->getWalkInCustomer($tenantId);
                if (!$walkIn) {
                    echo json_encode(['success' => false, 'message' => 'Pelanggan Walk-in default belum dibuat. Jalankan migrasi database.']);
                    exit;
                }
                $id_pelanggan = $walkIn['id_pelanggan'];
                $nama_pelanggan = 'Walk-in Customer';
            } else {
                $pelanggan = $this->model('Pelanggan')->getPelangganById($id_pelanggan, $tenantId);
                $nama_pelanggan = $pelanggan['nama_pelanggan'] ?? 'Walk-in Customer';
            }

            // 3. Cart items validation
            $items = $input['items'] ?? [];
            if (empty($items)) {
                echo json_encode(['success' => false, 'message' => 'Keranjang belanja kosong.']);
                exit;
            }

            // 4. Generate transaction numbers
            $no_receipt = $posModel->generateReceiptNumber($tenantId);
            $no_faktur = $this->generateAutoNumber('PJ', 'penjualan', 'no_faktur', $tenantId);

            // 5. Structure data in format expected by Penjualan_model::simpanPenjualan()
            $postData = [
                'no_faktur' => $no_faktur,
                'tanggal_faktur' => $today,
                'id_pelanggan' => $id_pelanggan,
                'nama_pelanggan' => $nama_pelanggan,
                'metode_pembayaran' => 'Tunai',
                'akun_kas_bank' => $input['akun_kas_bank'] ?? '',
                'total_diskon' => $input['diskon'] ?? 0,
                'total_pajak' => $input['pajak'] ?? 0,
                'keterangan' => 'POS Transaction: ' . $no_receipt,
                'details' => [
                    'id_barang' => [],
                    'kuantitas' => [],
                    'harga' => [],
                    'subtotal' => []
                ]
            ];

            foreach ($items as $item) {
                $postData['details']['id_barang'][] = $item['id_barang'];
                $postData['details']['kuantitas'][] = $item['qty'];
                $postData['details']['harga'][] = $item['harga'];
                $postData['details']['subtotal'][] = $item['subtotal'];
            }

            // 6. Save using standard Penjualan model (wrapped in transaction, does stock update & COA journals)
            $penjualanModel = $this->model('Penjualan');
            $result = $penjualanModel->simpanPenjualan($postData, $tenantId);

            if ($result) {
                // Fetch the newly created penjualan record ID
                $this->db->query("SELECT id_penjualan FROM penjualan WHERE no_faktur = :nf AND tenant_id = :tid");
                $this->db->bind('nf', $no_faktur);
                $this->db->bind('tid', $tenantId);
                $penjualan = $this->db->single();

                if (!$penjualan) {
                    echo json_encode(['success' => false, 'message' => 'Gagal melacak ID penjualan yang disimpan.']);
                    exit;
                }

                // Calculate totals
                $subtotal = 0;
                foreach ($items as $item) {
                    $subtotal += $item['subtotal'];
                }
                $diskon = (float)($input['diskon'] ?? 0);
                $pajak = (float)($input['pajak'] ?? 0);
                $total = $subtotal - $diskon + $pajak;
                $bayar = (float)($input['bayar'] ?? $total);
                $kembalian = $bayar - $total;

                // 7. Save POS metadata
                $user = Auth::user();
                $posData = [
                    'id_penjualan' => $penjualan['id_penjualan'],
                    'no_receipt' => $no_receipt,
                    'kasir_id' => $user['id'],
                    'kasir_name' => $user['name'],
                    'total' => $total,
                    'bayar' => $bayar,
                    'kembalian' => $kembalian,
                    'metode_pembayaran' => 'Tunai'
                ];

                $posId = $posModel->simpanTransaksiPos($posData, $tenantId);

                if ($posId) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Transaksi POS berhasil disimpan!',
                        'data' => [
                            'pos_id' => $posId,
                            'no_receipt' => $no_receipt,
                            'total' => $total,
                            'bayar' => $bayar,
                            'kembalian' => $kembalian,
                            'pelanggan' => $nama_pelanggan,
                            'kasir' => $user['name'],
                            'tanggal' => date('d/m/Y H:i')
                        ]
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Penjualan tersimpan, tetapi gagal membuat riwayat POS.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menyimpan transaksi. Pastikan stok persediaan mencukupi.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    // View Receipt Page
    public function receipt($id) {
        $tenantId = $this->tenantId();
        $data['judul'] = 'Struk Pembayaran POS';
        $data['transaksi'] = $this->model('Pos')->getTransaksiById($id, $tenantId);

        if (!$data['transaksi']) {
            Flash::setFlash('Struk transaksi tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/pos/riwayat');
            exit;
        }

        $this->view('pos/receipt', $data);
    }

    // View Transaction History Page
    public function riwayat() {
        $tenantId = $this->tenantId();
        $posModel = $this->model('Pos');
        
        $dari = $_GET['dari'] ?? date('Y-m-d');
        $sampai = $_GET['sampai'] ?? date('Y-m-d');

        $data['judul'] = 'Riwayat Transaksi POS';
        $data['dari'] = $dari;
        $data['sampai'] = $sampai;
        $data['riwayat'] = $posModel->getTransaksiByPeriode($tenantId, $dari, $sampai);
        $data['statistik'] = $posModel->getStatistikHariIni($tenantId);

        $this->view('templates/header', $data);
        $this->view('pos/riwayat', $data);
        $this->view('templates/footer');
    }

    // Void / Cancel POS Transaction
    public function hapus($id) {
        if (!Auth::isAdmin() && !Auth::isManager()) {
            Flash::setFlash('Akses ditolak. Hanya Admin atau Manager yang dapat membatalkan transaksi POS.', 'danger');
            header('Location: ' . BASEURL . '/pos/riwayat');
            exit;
        }

        $tenantId = $this->tenantId();
        $posModel = $this->model('Pos');
        
        $transaksi = $posModel->getTransaksiById($id, $tenantId);
        if (!$transaksi) {
            Flash::setFlash('Transaksi tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/pos/riwayat');
            exit;
        }

        // 1. Period Lock Check
        $this->checkPeriodLock($transaksi['tanggal_faktur'], BASEURL . '/pos/riwayat');

        // 2. Void using Penjualan model (reverses stock & journal)
        $penjualanModel = $this->model('Penjualan');
        $result = $penjualanModel->hapusPenjualan($transaksi['id_penjualan'], $tenantId);

        if ($result) {
            // 3. Delete POS transaction record
            $this->db->query("DELETE FROM pos_transactions WHERE id = :id AND tenant_id = :tenant_id");
            $this->db->bind('id', $id);
            $this->db->bind('tenant_id', $tenantId);
            $this->db->execute();

            Flash::setFlash('Transaksi POS #' . $transaksi['no_receipt'] . ' berhasil dibatalkan (VOID).', 'success');
        } else {
            Flash::setFlash('Gagal membatalkan transaksi POS.', 'danger');
        }

        header('Location: ' . BASEURL . '/pos/riwayat');
        exit;
    }
}
