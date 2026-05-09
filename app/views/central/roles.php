<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">Role & Permission Management (RBAC)</h1>
            <p class="text-muted">Definisikan tingkat akses secara sentral untuk berbagai peran pengguna di seluruh tenant.</p>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahRoleModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah Role Baru
            </button>
        </div>
    </div>

    <?php Flash::flash(); ?>

    <div class="row g-4">
        <?php foreach($data['roles'] as $role) : ?>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm border-0 border-top border-4 <?php echo $role['id'] <= 4 ? 'border-primary' : 'border-success'; ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($role['role_name']); ?></h5>
                        <?php if ($role['id'] <= 4): ?>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">System Default</span>
                        <?php else: ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Custom Role</span>
                        <?php endif; ?>
                    </div>
                    <p class="text-muted small mb-4" style="min-height: 40px;"><?php echo htmlspecialchars($role['description']); ?></p>
                    
                    <h6 class="small fw-bold text-uppercase opacity-50 mb-3">Assigned Permissions:</h6>
                    <div class="d-flex flex-wrap gap-2 mb-3" style="max-height: 120px; overflow-y: auto;">
                        <?php if (!empty($role['permissions'])): ?>
                            <?php foreach($role['permissions'] as $perm_key): ?>
                                <span class="badge bg-light text-dark border"><i class="bi bi-check2 text-success me-1"></i><?php echo $perm_key; ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="text-muted small"><em>Belum ada permission.</em></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-3 d-flex justify-content-between">
                    <button class="btn btn-sm btn-outline-primary btn-edit" 
                        data-id="<?php echo $role['id']; ?>"
                        data-name="<?php echo htmlspecialchars($role['role_name']); ?>"
                        data-desc="<?php echo htmlspecialchars($role['description']); ?>"
                        data-perms='<?php echo json_encode($role['permissions'] ?? []); ?>'
                        data-bs-toggle="modal" data-bs-target="#editRoleModal">
                        <i class="bi bi-pencil"></i> Edit Izin
                    </button>
                    <?php if ($role['id'] > 4): ?>
                        <button class="btn btn-sm btn-outline-danger" onclick="if(confirm('Hapus role ini secara permanen?')){ window.location.href='<?php echo BASEURL; ?>/central/role_hapus/<?php echo $role['id']; ?>'; }">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    <?php else: ?>
                        <button class="btn btn-sm btn-outline-secondary border-0 disabled" title="Role dasar sistem tidak bisa dihapus, tapi izinnya bisa diedit">
                            <i class="bi bi-shield-lock"></i> Default
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal Tambah Role -->
<div class="modal fade" id="tambahRoleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="<?php echo BASEURL; ?>/central/role_simpan" method="POST">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Tambah Role Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label fw-bold">Nama Role</label>
                            <input type="text" name="role_name" class="form-control" required placeholder="Contoh: Supervisor">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <input type="text" name="description" class="form-control" required placeholder="Deskripsi singkat hak akses">
                        </div>
                    </div>
                    
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Pilih Permissions</h6>
                    <div class="row g-3">
                        <?php 
                        $current_category = '';
                        foreach($data['permissions'] as $perm): 
                            if ($current_category != $perm['category']) {
                                if ($current_category != '') echo '</div></div>';
                                $current_category = $perm['category'];
                                echo '<div class="col-md-4"><div class="p-3 bg-light rounded-3 border h-100">';
                                echo '<h6 class="text-primary mb-3 small fw-bold text-uppercase">' . $current_category . '</h6>';
                            }
                        ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="<?php echo $perm['id']; ?>" id="perm_<?php echo $perm['id']; ?>">
                                <label class="form-check-label small" for="perm_<?php echo $perm['id']; ?>">
                                    <?php echo $perm['display_name']; ?>
                                </label>
                            </div>
                        <?php endforeach; 
                        if ($current_category != '') echo '</div></div>';
                        ?>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Role -->
<div class="modal fade" id="editRoleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="<?php echo BASEURL; ?>/central/role_update" method="POST">
                <input type="hidden" name="id" id="edit-id">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Edit Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label fw-bold">Nama Role</label>
                            <input type="text" name="role_name" id="edit-name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <input type="text" name="description" id="edit-desc" class="form-control" required>
                        </div>
                    </div>
                    
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Pilih Permissions</h6>
                    <div class="row g-3">
                        <?php 
                        $current_category = '';
                        foreach($data['permissions'] as $perm): 
                            if ($current_category != $perm['category']) {
                                if ($current_category != '') echo '</div></div>';
                                $current_category = $perm['category'];
                                echo '<div class="col-md-4"><div class="p-3 bg-light rounded-3 border h-100">';
                                echo '<h6 class="text-primary mb-3 small fw-bold text-uppercase">' . $current_category . '</h6>';
                            }
                        ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input edit-perm-checkbox" type="checkbox" name="permissions[]" value="<?php echo $perm['id']; ?>" id="edit_perm_<?php echo $perm['id']; ?>" data-key="<?php echo $perm['permission_key']; ?>">
                                <label class="form-check-label small" for="edit_perm_<?php echo $perm['id']; ?>">
                                    <?php echo $perm['display_name']; ?>
                                </label>
                            </div>
                        <?php endforeach; 
                        if ($current_category != '') echo '</div></div>';
                        ?>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('edit-id').value = this.dataset.id;
        document.getElementById('edit-name').value = this.dataset.name;
        document.getElementById('edit-desc').value = this.dataset.desc;
        
        // Reset all checkboxes first
        document.querySelectorAll('.edit-perm-checkbox').forEach(cb => cb.checked = false);
        
        // Check the ones the role has
        let perms = JSON.parse(this.dataset.perms);
        document.querySelectorAll('.edit-perm-checkbox').forEach(cb => {
            if (perms.includes(cb.dataset.key)) {
                cb.checked = true;
            }
        });
        
        // Optional: lock the name input for system roles (ID 1-4)
        if (this.dataset.id <= 4) {
            document.getElementById('edit-name').readOnly = true;
        } else {
            document.getElementById('edit-name').readOnly = false;
        }
    });
});
</script>
