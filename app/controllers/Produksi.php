<?php

class Produksi extends Controller {
    use PeriodLockTrait;

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['judul'] = 'Perintah Produksi';
        $data['produksi'] = $this->model('Produksi')->getAllProduksi($this->tenantId());
        $this->view('templates/header', $data);
        $this->view('produksi/index', $data);
        $this->view('templates/footer');
    }

    public function tambah() {
        $data['judul'] = 'Tambah Perintah Produksi';
        $data['bom_list'] = $this->model('Bom')->getAllBOM($this->tenantId());
        $data['no_produksi'] = $this->generateAutoNumber('PRD', 'produksi', 'no_produksi', $this->tenantId());

        $this->view('templates/header', $data);
        $this->view('produksi/tambah', $data);
        $this->view('templates/footer');
    }

    public function simpan() {
        $this->checkPeriodLock($_POST['tanggal'], BASEURL . '/produksi');
        
        if ($this->model('Produksi')->tambahProduksi($_POST, $this->tenantId())) {
            Flash::setFlash('Perintah produksi berhasil dibuat.', 'success');
            header('Location: ' . BASEURL . '/produksi');
            exit;
        } else {
            Flash::setFlash('Gagal membuat perintah produksi.', 'danger');
            header('Location: ' . BASEURL . '/produksi/tambah');
            exit;
        }
    }

    public function selesaikan($id) {
        $produksi = $this->model('Produksi')->getProduksiById($id, $this->tenantId());
        if ($produksi) {
            $this->checkPeriodLock($produksi['tanggal'], BASEURL . '/produksi');
        }

        if ($this->model('Produksi')->selesaikanProduksi($id, $_POST, $this->tenantId())) {
            Logger::log('FINISH_PRODUCTION', 'Manufacturing', "Finished Production Order ID: $id");
            Flash::setFlash('Produksi berhasil diselesaikan. Stok dan jurnal telah diperbarui.', 'success');
        }
        header('Location: ' . BASEURL . '/produksi');
        exit;
    }

    public function hapus($id) {
        if ($this->model('Produksi')->hapusProduksi($id, $this->tenantId())) {
            Flash::setFlash('Perintah produksi berhasil dihapus.', 'success');
        }
        header('Location: ' . BASEURL . '/produksi');
        exit;
    }

    public function lihat($id) {
        $data['judul'] = 'Detail Produksi';
        $data['produksi'] = $this->model('Produksi')->getProduksiById($id, $this->tenantId());
        if (!$data['produksi']) {
            Flash::setFlash('Data produksi tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/produksi');
            exit;
        }
        $this->view('templates/header', $data);
        $this->view('produksi/lihat', $data);
        $this->view('templates/footer');
    }
}
