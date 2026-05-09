<?php

class Penawaran extends Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['judul'] = 'Penawaran Harga';
        $data['penawaran'] = $this->model('Penawaran')->getAllPenawaran($this->tenantId());
        $this->view('templates/header', $data);
        $this->view('penawaran/index', $data);
        $this->view('templates/footer');
    }

    public function tambah() {
        $data['judul'] = 'Buat Penawaran Baru';
        $data['pelanggan'] = $this->model('Pelanggan')->getAllPelanggan($this->tenantId());
        $data['barang'] = $this->model('Persediaan')->getAllBarang($this->tenantId());
        $data['perusahaan'] = $this->model('Perusahaan')->getPerusahaan($this->tenantId());
        $data['no_penawaran'] = $this->generateAutoNumber('QT', 'penawaran', 'no_penawaran', $this->tenantId());
        
        $this->view('templates/header', $data);
        $this->view('penawaran/tambah', $data);
        $this->view('templates/footer');
    }

    public function simpan() {
        if ($this->model('Penawaran')->simpanPenawaran($_POST, $this->tenantId())) {
            Flash::setFlash('Penawaran berhasil disimpan.', 'success');
            header('Location: ' . BASEURL . '/penawaran');
        } else {
            Flash::setFlash('Gagal menyimpan penawaran.', 'danger');
            header('Location: ' . BASEURL . '/penawaran/tambah');
        }
        exit;
    }

    public function lihat($id) {
        $data['judul'] = 'Detail Penawaran';
        $data['penawaran'] = $this->model('Penawaran')->getPenawaranById($id, $this->tenantId());
        if (!$data['penawaran']) {
            Flash::setFlash('Penawaran tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/penawaran');
            exit;
        }
        $this->view('templates/header', $data);
        $this->view('penawaran/lihat', $data);
        $this->view('templates/footer');
    }

    public function convert_to_invoice($id) {
        $penawaran = $this->model('Penawaran')->getPenawaranById($id, $this->tenantId());
        if (!$penawaran) {
            Flash::setFlash('Data penawaran tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/penawaran');
            exit;
        }

        // Siapkan data untuk Invoice (Penjualan)
        $dataPenjualan = [
            'id_pelanggan' => $penawaran['id_pelanggan'],
            'nama_pelanggan' => $penawaran['nama_pelanggan'],
            'no_faktur' => $this->generateAutoNumber('PJ', 'penjualan', 'no_faktur', $this->tenantId()),
            'tanggal_faktur' => date('Y-m-d'),
            'metode_pembayaran' => 'Kredit', // Default to Kredit
            'keterangan' => 'Konversi dari Penawaran #' . $penawaran['no_penawaran'],
            'total_diskon' => $penawaran['diskon'],
            'total_pajak' => $penawaran['pajak'],
            'details' => [
                'id_barang' => [],
                'kuantitas' => [],
                'harga' => [],
                'subtotal' => []
            ]
        ];

        foreach ($penawaran['details'] as $det) {
            $dataPenjualan['details']['id_barang'][] = $det['id_barang'];
            $dataPenjualan['details']['kuantitas'][] = $det['kuantitas'];
            $dataPenjualan['details']['harga'][] = $det['harga'];
            $dataPenjualan['details']['subtotal'][] = $det['subtotal'];
        }

        if ($this->model('Penjualan')->simpanPenjualan($dataPenjualan, $this->tenantId())) {
            $this->model('Penawaran')->updateStatus($id, 'Invoiced', $this->tenantId());
            Flash::setFlash('Penawaran berhasil dikonversi menjadi Faktur Penjualan.', 'success');
            header('Location: ' . BASEURL . '/penjualan');
        } else {
            // Error Flash handled by Penjualan Model
            header('Location: ' . BASEURL . '/penawaran/lihat/' . $id);
        }
        exit;
    }
}
