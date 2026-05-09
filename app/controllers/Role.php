<?php

class Role extends Controller {
    public function __construct() {
        parent::__construct();
        // Hanya Admin/Manager tenant yang bisa melihat Role
        if (Auth::user()['role'] !== 'Admin' && Auth::user()['role'] !== 'Superadmin' && Auth::user()['role'] !== 'Manager') {
            Flash::setFlash('Akses Ditolak', 'Hanya administrator atau manajer yang dapat melihat hak akses.', 'danger');
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }
    }

    public function index() {
        $data['judul'] = 'Manajemen Hak Akses (Roles)';
        // Menampilkan role global secara read-only
        $data['roles'] = $this->model('Role')->getAllRoles();
        
        $this->view('templates/header', $data);
        $this->view('role/index', $data);
        $this->view('templates/footer');
    }
}
