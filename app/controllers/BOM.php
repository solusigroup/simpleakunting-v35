<?php

class BOM extends Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['judul'] = 'Bill of Materials (BOM)';
        $data['bom'] = $this->model('BOM')->getAllBOM($this->tenantId());
        $this->view('templates/header', $data);
        $this->view('bom/index', $data);
        $this->view('templates/footer');
    }

    public function tambah() {
        $data['judul'] = 'Tambah BOM Baru';
        $data['produk'] = $this->model('Persediaan')->getAllBarang($this->tenantId());
        $this->view('templates/header', $data);
        $this->view('bom/tambah', $data);
        $this->view('templates/footer');
    }

    public function simpan() {
        if ($this->model('BOM')->tambahBOM($_POST, $this->tenantId())) {
            Flash::setFlash('BOM berhasil disimpan.', 'success');
            header('Location: ' . BASEURL . '/bom');
            exit;
        } else {
            Flash::setFlash('Gagal menyimpan BOM.', 'danger');
            header('Location: ' . BASEURL . '/bom/tambah');
            exit;
        }
    }

    public function lihat($id) {
        $data['judul'] = 'Detail BOM';
        $data['bom'] = $this->model('BOM')->getBOMById($id, $this->tenantId());
        if (!$data['bom']) {
            Flash::setFlash('BOM tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/bom');
            exit;
        }
        $this->view('templates/header', $data);
        $this->view('bom/lihat', $data);
        $this->view('templates/footer');
    }

    public function hapus($id) {
        if ($this->model('BOM')->hapusBOM($id, $this->tenantId())) {
            Flash::setFlash('BOM berhasil dihapus.', 'success');
        }
        header('Location: ' . BASEURL . '/bom');
        exit;
    }
}
