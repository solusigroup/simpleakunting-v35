<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Manajemen Pengguna</h3>
        <p class="text-muted small mb-0">Kelola daftar pengguna dan penetapan hak akses mereka.</p>
    </div>
    <div class="btn-group gap-2">
        <a href="<?php echo BASEURL; ?>/role" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-shield-lock me-2"></i>Pengaturan Role
        </a>
        <a href="<?php echo BASEURL; ?>/user/tambah" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-person-plus me-2"></i>Tambah User
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">User & Nama Tanda Tangan</th>
                                <th class="py-3">Jabatan</th>
                                <th class="py-3">Level/Role</th>
                                <th class="pe-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['users'] as $user): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 38px; height: 38px;">
                                            <?php echo strtoupper(substr($user['nama_user'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($user['nama_user']); ?></div>
                                            <div class="small text-muted italic"><?php echo htmlspecialchars($user['nama_lengkap'] ?? '-'); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted small"><?php echo htmlspecialchars($user['jabatan']); ?></span>
                                </td>
                                <td>
                                    <?php 
                                        $customRole = null;
                                        if(!empty($user['role_id'])) {
                                            $customRole = $this->model('Role')->getRoleById($user['role_id'], $this->tenantId());
                                        }
                                    ?>
                                    <span class="badge rounded-pill px-3 py-2 <?php echo ($user['role'] == 'Admin' || $user['role'] == 'Superadmin') ? 'bg-danger-soft text-danger' : 'bg-primary-soft text-primary'; ?>">
                                        <?php 
                                            echo $customRole ? $customRole['role_name'] : $user['role']; 
                                        ?>
                                    </span>
                                </td>
                                <td class="pe-4 text-center">
                                    <div class="btn-group">
                                        <a href="<?php echo BASEURL; ?>/user/edit/<?php echo $user['id_user']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-2">
                                            <i class="bi bi-pencil me-1"></i> Edit
                                        </a>
                                        <?php if ($user['id_user'] != Auth::user()['id']): ?>
                                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="if(confirm('Hapus pengguna ini?')){ window.location.href='<?php echo BASEURL; ?>/user/hapus/<?php echo $user['id_user']; ?>'; }">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-soft { background-color: rgba(79, 70, 229, 0.1); }
    .bg-danger-soft { background-color: rgba(239, 68, 68, 0.1); }
</style>
