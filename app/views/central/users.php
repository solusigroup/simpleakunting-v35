<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h1 class="h3 mb-1 text-gray-800 fw-bold">Global User Management</h1>
            <p class="text-muted small">Kelola dan pantau seluruh pengguna di ekosistem SimpleAkunting.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="btn-group shadow-sm">
                <button class="btn btn-primary px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#tambahUserModal">
                    <i class="bi bi-person-plus me-2"></i>Tambah User Global
                </button>
            </div>
        </div>
    </div>

    <!-- Statistik Singkat -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body p-3">
                    <div class="small opacity-75">Total User</div>
                    <div class="h4 fw-bold mb-0"><?php echo count($data['users']); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white">
                <div class="card-body p-3">
                    <div class="small text-muted">Total Tenant</div>
                    <div class="h4 fw-bold mb-0 text-dark"><?php echo count($data['tenants']); ?></div>
                </div>
            </div>
        </div>
        <?php if (Auth::user()['impersonating']): ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-warning text-dark">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Anda sedang login sebagai <strong><?php echo Auth::user()['name']; ?></strong>
                    </div>
                    <a href="<?php echo BASEURL; ?>/central/user_stop_impersonate" class="btn btn-sm btn-dark rounded-pill px-3">
                        Hentikan Impersonation
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="card-header bg-white py-3 border-0">
            <div class="input-group bg-light rounded-pill px-3">
                <span class="input-group-text bg-transparent border-0"><i class="bi bi-search"></i></span>
                <input type="text" id="userSearch" class="form-control bg-transparent border-0" placeholder="Cari nama, role, atau tenant...">
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="userTable">
                    <thead class="bg-light text-uppercase small fw-bold text-muted">
                        <tr>
                            <th class="ps-4 py-3">Pengguna</th>
                            <th class="py-3">Akses & Jabatan</th>
                            <th class="py-3">Tenant / Entitas Bisnis</th>
                            <th class="text-end pe-4 py-3">Aksi Strategis</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['users'] as $user) : ?>
                        <tr class="user-row">
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3 <?php echo $user['role'] == 'Superadmin' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary'; ?>">
                                        <?php echo strtoupper(substr($user['nama_user'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark user-name"><?php echo $user['nama_user']; ?></div>
                                        <small class="text-muted">ID: #<?php echo $user['id_user']; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="badge rounded-pill px-3 py-1 user-role <?php 
                                        echo $user['role'] == 'Superadmin' ? 'bg-danger' : 
                                            ($user['role'] == 'Admin' ? 'bg-primary' : 'bg-info'); 
                                    ?>">
                                        <?php echo $user['role']; ?>
                                    </span>
                                </div>
                                <div class="small text-muted mt-1 user-jabatan"><?php echo $user['jabatan'] ?: '-'; ?></div>
                            </td>
                            <td>
                                <?php 
                                    if ($user['role'] == 'Superadmin') {
                                        echo '<span class="badge bg-dark rounded-pill px-3 py-1 user-tenant"><i class="bi bi-globe me-1"></i> Seluruh Sistem</span>';
                                    } else {
                                        $found = false;
                                        foreach($data['tenants'] as $tenant) {
                                            if ($tenant['id'] == $user['tenant_id']) {
                                                echo '<div class="fw-bold text-dark user-tenant">' . $tenant['name'] . '</div>';
                                                echo '<div class="small text-muted">Code: ' . $tenant['code'] . '</div>';
                                                $found = true;
                                                break;
                                            }
                                        }
                                        if (!$found) echo '<span class="text-muted italic user-tenant">Tenant Tidak Ditemukan</span>';
                                    }
                                ?>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <?php if ($user['role'] != 'Superadmin' && $user['id_user'] != Auth::user()['id']): ?>
                                    <a href="<?php echo BASEURL; ?>/central/user_impersonate/<?php echo $user['id_user']; ?>" 
                                       class="btn btn-sm btn-outline-info rounded-pill px-3 me-2" title="Log in as this user">
                                        <i class="bi bi-box-arrow-in-right"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($user['role'] != 'Superadmin'): ?>
                                        <button class="btn btn-sm btn-light rounded-circle edit-user me-2" 
                                            data-id="<?php echo $user['id_user']; ?>"
                                            data-nama="<?php echo $user['nama_user']; ?>"
                                            data-role="<?php echo $user['role']; ?>"
                                            data-jabatan="<?php echo $user['jabatan']; ?>"
                                            data-tenant="<?php echo $user['tenant_id']; ?>"
                                            data-bs-toggle="modal" data-bs-target="#editUserModal">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        
                                        <?php if ($user['id_user'] != Auth::user()['id']): ?>
                                        <a href="<?php echo BASEURL; ?>/central/user_hapus/<?php echo $user['id_user']; ?>" 
                                           class="btn btn-sm btn-light rounded-circle text-danger" 
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                             <i class="bi bi-trash"></i>
                                         </a>
                                         <?php endif; ?>
                                     <?php else: ?>
                                         <span class="badge bg-light text-muted rounded-pill px-2">Protected</span>
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

<!-- Modal Tambah User -->
<div class="modal fade" id="tambahUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="<?php echo BASEURL; ?>/central/user_simpan" method="POST">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold">Tambah User Global</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">NAMA PENGGUNA / USERNAME</label>
                        <input type="text" name="nama_user" class="form-control border-0 bg-light py-2" placeholder="Masukkan username..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">PASSWORD</label>
                        <input type="password" name="password" class="form-control border-0 bg-light py-2" placeholder="Masukkan password minimal 6 karakter..." required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">ROLE / HAK AKSES</label>
                            <select name="role" id="role_select" class="form-select border-0 bg-light py-2" required>
                                <option value="Staff">Staff</option>
                                <option value="Manager">Manager</option>
                                <option value="Admin">Admin (Tenant)</option>
                                <option value="Superadmin">Superadmin (Global)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">JABATAN</label>
                            <input type="text" name="jabatan" class="form-control border-0 bg-light py-2" placeholder="Contoh: Akuntan">
                        </div>
                    </div>
                    <div class="mb-0" id="tenant_select_container">
                        <label class="form-label fw-bold small text-muted">PENUGASAN TENANT</label>
                        <select name="tenant_id" class="form-select border-0 bg-light py-2">
                            <?php foreach($data['tenants'] as $tenant): ?>
                            <option value="<?php echo $tenant['id']; ?>"><?php echo $tenant['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text text-info small mt-2">
                            <i class="bi bi-info-circle me-1"></i> Tenant akan diabaikan jika role adalah Superadmin.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit User -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="<?php echo BASEURL; ?>/central/user_update" method="POST">
                <input type="hidden" name="id_user" id="edit_id">
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title fw-bold">Perbarui Data User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">NAMA PENGGUNA</label>
                        <input type="text" name="nama_user" id="edit_nama" class="form-control border-0 bg-light py-2" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">PASSWORD BARU (OPSIONAL)</label>
                        <input type="password" name="password" class="form-control border-0 bg-light py-2" placeholder="Kosongkan jika tidak ingin mengubah password">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">ROLE</label>
                            <select name="role" id="edit_role" class="form-select border-0 bg-light py-2" required>
                                <option value="Staff">Staff</option>
                                <option value="Manager">Manager</option>
                                <option value="Admin">Admin (Tenant)</option>
                                <option value="Superadmin">Superadmin (Global)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">JABATAN</label>
                            <input type="text" name="jabatan" id="edit_jabatan" class="form-control border-0 bg-light py-2">
                        </div>
                    </div>
                    <div class="mb-0" id="edit_tenant_container">
                        <label class="form-label fw-bold small text-muted">TENANT</label>
                        <select name="tenant_id" id="edit_tenant" class="form-select border-0 bg-light py-2">
                            <?php foreach($data['tenants'] as $tenant): ?>
                            <option value="<?php echo $tenant['id']; ?>"><?php echo $tenant['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search Functionality
    const searchInput = document.getElementById('userSearch');
    const userRows = document.querySelectorAll('.user-row');

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        userRows.forEach(row => {
            const name = row.querySelector('.user-name').textContent.toLowerCase();
            const role = row.querySelector('.user-role').textContent.toLowerCase();
            const tenant = row.querySelector('.user-tenant').textContent.toLowerCase();
            const jabatan = row.querySelector('.user-jabatan').textContent.toLowerCase();
            
            if (name.includes(query) || role.includes(query) || tenant.includes(query) || jabatan.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Populate Edit Modal
    document.querySelectorAll('.edit-user').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_nama').value = this.dataset.nama;
            document.getElementById('edit_role').value = this.dataset.role;
            document.getElementById('edit_jabatan').value = this.dataset.jabatan;
            document.getElementById('edit_tenant').value = this.dataset.tenant;
            
            toggleTenantSelect('edit_role', 'edit_tenant_container');
        });
    });

    // Show/Hide Tenant Select based on Role
    function toggleTenantSelect(roleSelectId, containerId) {
        const role = document.getElementById(roleSelectId).value;
        const container = document.getElementById(containerId);
        container.style.display = (role === 'Superadmin') ? 'none' : 'block';
    }

    document.getElementById('role_select').addEventListener('change', () => toggleTenantSelect('role_select', 'tenant_select_container'));
    document.getElementById('edit_role').addEventListener('change', () => toggleTenantSelect('edit_role', 'edit_tenant_container'));
});
</script>

<style>
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
    }
    .bg-primary-subtle { background-color: #e0e7ff; }
    .bg-danger-subtle { background-color: #fee2e2; }
    .table-hover tbody tr:hover { background-color: #f9fafb; cursor: default; }
    .form-control:focus, .form-select:focus {
        box-shadow: none;
        background-color: #f3f4f6 !important;
    }
</style>
