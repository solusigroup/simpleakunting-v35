<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">Manajemen Tenant</h1>
            <p class="text-muted">Kelola entitas bisnis dalam database terpadu terintegrasi.</p>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahTenantModal">
                <i class="bi bi-plus-lg"></i> Tambah Tenant
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Nama Bisnis</th>
                            <th>Kode</th>
                            <th>Tipe Bisnis</th>
                            <th>Status</th>
                            <th>Dibuat Pada</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['tenants'] as $tenant) : ?>
                        <tr>
                            <td class="ps-4"><?php echo $tenant['id']; ?></td>
                            <td class="fw-bold"><?php echo $tenant['name']; ?></td>
                            <td><span class="badge bg-secondary"><?php echo $tenant['code']; ?></span></td>
                            <td class="text-capitalize"><?php echo $tenant['database_type']; ?></td>
                            <td>
                                <span class="badge <?php echo $tenant['status'] == 'active' ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo ucfirst($tenant['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d M Y', strtotime($tenant['created_at'])); ?></td>
                             <td class="text-end pe-4">
                                 <a href="<?php echo BASEURL; ?>/tenants/switch/<?php echo $tenant['id']; ?>" class="btn btn-sm btn-info text-white" title="Login ke Dashboard Tenant">
                                     <i class="bi bi-box-arrow-in-right"></i> Login
                                 </a>
                                 <button class="btn btn-sm btn-outline-primary btn-edit" 
                                     data-id="<?php echo $tenant['id']; ?>"
                                     data-name="<?php echo $tenant['name']; ?>"
                                     data-code="<?php echo $tenant['code']; ?>"
                                     data-type="<?php echo $tenant['database_type']; ?>"
                                     data-status="<?php echo $tenant['status']; ?>"
                                     data-bs-toggle="modal" data-bs-target="#editTenantModal">
                                     <i class="bi bi-pencil"></i>
                                 </button>
                                 <button class="btn btn-sm btn-outline-danger" onclick="if(confirm('Hapus tenant ini?')){ window.location.href='<?php echo BASEURL; ?>/tenants/hapus/<?php echo $tenant['id']; ?>'; }">
                                     <i class="bi bi-trash"></i>
                                 </button>
                             </td>
                         </tr>
                         <?php endforeach; ?>
                     </tbody>
                 </table>
             </div>
         </div>
     </div>
 </div>

 <!-- Modal Tambah Tenant -->
 <div class="modal fade" id="tambahTenantModal" tabindex="-1">
     <div class="modal-dialog">
         <div class="modal-content border-0 shadow">
             <form action="<?php echo BASEURL; ?>/tenants/tambah" method="POST">
                 <div class="modal-header">
                     <h5 class="modal-title">Tambah Tenant Baru</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                 </div>
                 <div class="modal-body">
                     <div class="mb-3">
                         <label class="form-label">Nama Bisnis</label>
                         <input type="text" name="name" class="form-control" required placeholder="Contoh: PT Maju Jaya">
                     </div>
                     <div class="mb-3">
                         <label class="form-label">Kode Unik</label>
                         <input type="text" name="code" class="form-control" required placeholder="Contoh: MAJUJAYA">
                     </div>
                     <div class="mb-3">
                         <label class="form-label">Tipe Bisnis</label>
                         <select name="database_type" class="form-select">
                             <option value="jasa">Jasa</option>
                             <option value="dagang">Perdagangan</option>
                             <option value="manufaktur">Manufaktur</option>
                         </select>
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                     <button type="submit" class="btn btn-primary">Simpan</button>
                 </div>
             </form>
         </div>
     </div>
 </div>

 <!-- Modal Edit Tenant -->
 <div class="modal fade" id="editTenantModal" tabindex="-1">
     <div class="modal-dialog">
         <div class="modal-content border-0 shadow">
             <form action="<?php echo BASEURL; ?>/tenants/ubah" method="POST">
                 <input type="hidden" name="id" id="edit-id">
                 <div class="modal-header">
                     <h5 class="modal-title">Edit Tenant</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                 </div>
                 <div class="modal-body">
                     <div class="mb-3">
                         <label class="form-label">Nama Bisnis</label>
                         <input type="text" name="name" id="edit-name" class="form-control" required>
                     </div>
                     <div class="mb-3">
                         <label class="form-label">Kode Unik</label>
                         <input type="text" name="code" id="edit-code" class="form-control" required>
                     </div>
                     <div class="mb-3">
                         <label class="form-label">Tipe Bisnis</label>
                         <select name="database_type" id="edit-type" class="form-select">
                             <option value="jasa">Jasa</option>
                             <option value="dagang">Perdagangan</option>
                             <option value="manufaktur">Manufaktur</option>
                         </select>
                     </div>
                     <div class="mb-3">
                         <label class="form-label">Status</label>
                         <select name="status" id="edit-status" class="form-select">
                             <option value="active">Active</option>
                             <option value="inactive">Inactive</option>
                         </select>
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                     <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
         document.getElementById('edit-code').value = this.dataset.code;
         document.getElementById('edit-type').value = this.dataset.type;
         document.getElementById('edit-status').value = this.dataset.status;
     });
 });
 </script>
