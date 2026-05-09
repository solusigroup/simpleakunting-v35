<?php

class RFQ extends Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['judul'] = 'Request for Quotation (RFQ)';
        $data['rfq'] = $this->model('RFQ')->getAllRFQ($this->tenantId());
        $this->view('templates/header', $data);
        $this->view('rfq/index', $data);
        $this->view('templates/footer');
    }

    public function tambah() {
        $data['judul'] = 'Buat RFQ Baru';
        $data['pemasok'] = $this->model('Pemasok')->getAllPemasok($this->tenantId());
        $data['barang'] = $this->model('Persediaan')->getAllBarang($this->tenantId());
        $data['no_rfq'] = $this->generateAutoNumber('RFQ', 'rfq', 'no_rfq', $this->tenantId());
        
        $this->view('templates/header', $data);
        $this->view('rfq/tambah', $data);
        $this->view('templates/footer');
    }

    public function simpan() {
        if ($this->model('RFQ')->simpanRFQ($_POST, $this->tenantId())) {
            Flash::setFlash('RFQ berhasil disimpan.', 'success');
            header('Location: ' . BASEURL . '/rfq');
        } else {
            Flash::setFlash('Gagal menyimpan RFQ.', 'danger');
            header('Location: ' . BASEURL . '/rfq/tambah');
        }
        exit;
    }

    public function lihat($id) {
        $data['judul'] = 'Detail RFQ';
        $data['rfq'] = $this->model('RFQ')->getRFQById($id, $this->tenantId());
        if (!$data['rfq']) {
            Flash::setFlash('RFQ tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/rfq');
            exit;
        }
        $this->view('templates/header', $data);
        $this->view('rfq/lihat', $data);
        $this->view('templates/footer');
    }

    public function convert_to_invoice($id) {
        $rfq = $this->model('RFQ')->getRFQById($id, $this->tenantId());
        if (!$rfq) {
            Flash::setFlash('Data RFQ tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/rfq');
            exit;
        }

        // Siapkan data untuk Invoice (Pembelian)
        $dataPembelian = [
            'id_pemasok' => $rfq['id_pemasok'],
            'nama_pemasok' => $rfq['nama_pemasok'],
            'no_faktur_pembelian' => $this->generateAutoNumber('PB', 'pembelian', 'no_faktur_pembelian', $this->tenantId()),
            'tanggal_faktur' => date('Y-m-d'),
            'metode_pembayaran' => 'Kredit', // Default to Kredit
            'keterangan' => 'Konversi dari RFQ #' . $rfq['no_rfq'],
            'total_diskon' => 0,
            'total_pajak' => 0,
            'details' => [
                'id_barang' => [],
                'kuantitas' => [],
                'harga' => [],
                'subtotal' => []
            ]
        ];

        foreach ($rfq['details'] as $det) {
            $dataPembelian['details']['id_barang'][] = $det['id_barang'];
            $dataPembelian['details']['kuantitas'][] = $det['kuantitas'];
            $dataPembelian['details']['harga'][] = $det['harga_estimasi'];
            $dataPembelian['details']['subtotal'][] = $det['subtotal_estimasi'];
        }

        // Perlu akun kas jika tunai, tapi default kredit
        $dataPembelian['akun_kas_bank'] = null;

        if ($this->model('Pembelian')->simpanPembelian($dataPembelian, $this->tenantId())) {
            $this->model('RFQ')->updateStatus($id, 'Ordered', $this->tenantId());
            Flash::setFlash('RFQ berhasil dikonversi menjadi Faktur Pembelian.', 'success');
            header('Location: ' . BASEURL . '/pembelian');
        } else {
            header('Location: ' . BASEURL . '/rfq/lihat/' . $id);
        }
        exit;
    }
}
