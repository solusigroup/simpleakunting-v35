<?php

class KlusterWilayah extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // Only pure Superadmin can manage kluster
        if (!Auth::isLoggedIn() || !Auth::hasRole('Superadmin')) {
            Flash::setFlash('Akses Ditolak', 'Hanya Superadmin yang bisa mengelola Kluster Wilayah', 'danger');
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }
    }

    public function index()
    {
        $data['judul'] = 'Manajemen Kluster Wilayah';
        $data['klusters'] = $this->model('KlusterWilayah')->getAllKluster();
        // Count tenants per kluster
        foreach ($data['klusters'] as &$k) {
            $this->db->query("SELECT COUNT(*) as total FROM tenants WHERE kluster_wilayah_id = :id");
            $this->db->bind('id', $k['id']);
            $result = $this->db->single();
            $k['tenant_count'] = $result['total'];
        }
        $this->view('templates/header', $data);
        $this->view('klusterwilayah/index', $data);
        $this->view('templates/footer');
    }

    public function tambah()
    {
        if ($this->model('KlusterWilayah')->tambahKluster($_POST) > 0) {
            Flash::setFlash('Berhasil', 'Kluster wilayah baru telah ditambahkan', 'success');
        } else {
            Flash::setFlash('Gagal', 'Terjadi kesalahan saat menambah kluster', 'danger');
        }
        header('Location: ' . BASEURL . '/klusterwilayah');
    }

    public function ubah()
    {
        if ($this->model('KlusterWilayah')->ubahKluster($_POST) > 0) {
            Flash::setFlash('Berhasil', 'Data kluster wilayah telah diperbarui', 'success');
        } else {
            Flash::setFlash('Gagal', 'Tidak ada perubahan data', 'info');
        }
        header('Location: ' . BASEURL . '/klusterwilayah');
    }

    public function hapus($id)
    {
        // Check if any tenants use this kluster
        $this->db->query("SELECT COUNT(*) as total FROM tenants WHERE kluster_wilayah_id = :id");
        $this->db->bind('id', $id);
        $result = $this->db->single();
        if ($result['total'] > 0) {
            Flash::setFlash('Gagal', 'Kluster ini masih memiliki ' . $result['total'] . ' tenant terdaftar. Pindahkan tenant terlebih dahulu.', 'danger');
            header('Location: ' . BASEURL . '/klusterwilayah');
            return;
        }

        if ($this->model('KlusterWilayah')->hapusKluster($id) > 0) {
            Flash::setFlash('Berhasil', 'Kluster wilayah telah dihapus', 'success');
        } else {
            Flash::setFlash('Gagal', 'Gagal menghapus kluster', 'danger');
        }
        header('Location: ' . BASEURL . '/klusterwilayah');
    }
}
