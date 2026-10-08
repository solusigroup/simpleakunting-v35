<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">Unit Usaha BUMDesa</h1>
            <p class="text-muted">Kelola berbagai unit bisnis atau divisi di bawah BUMDesa Anda.</p>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahUnitModal">
                <i class="bi bi-plus-lg"></i> Tambah Unit Usaha
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Nama Unit</th>
                            <th>Kode</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($data['units'] as $u): ?>
                            <tr>
                                <td class="ps-4"><?php echo $no++; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary-subtle text-primary p-2 rounded-3 me-3">
                                            <i class="bi bi-shop fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?php echo $u['nama_unit']; ?></div>
                                            <?php if($u['is_default']): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.65rem;">Unit Utama</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-secondary"><?php echo $u['kode_unit']; ?></span></td>
                                <td class="text-muted small"><?php echo $u['deskripsi'] ?: '-'; ?></td>
                                <td>
                                    <span class="badge bg-success">Active</span>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary btn-edit"
                                        data-id="<?php echo $u['id_unit']; ?>" 
                                        data-name="<?php echo $u['nama_unit']; ?>"
                                        data-kode="<?php echo $u['kode_unit']; ?>"
                                        data-is_default="<?php echo $u['is_default']; ?>"
                                        data-deskripsi="<?php echo $u['deskripsi']; ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editUnitModal">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <?php if(!$u['is_default']): ?>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="if(confirm('Hapus unit usaha ini? Transaksi yang sudah ada akan tetap tersimpan namun referensi unit akan hilang.')){ window.location.href='<?php echo BASEURL; ?>/units/hapus/<?php echo $u['id_unit']; ?>'; }">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Unit -->
<div class="modal fade" id="tambahUnitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="<?php echo BASEURL; ?>/units/tambah" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Unit Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Unit Usaha</label>
                        <input type="text" name="nama_unit" class="form-control" required placeholder="Contoh: Unit Toko Kelontong">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kode Unit (Singkatan)</label>
                        <input type="text" name="kode_unit" class="form-control" required placeholder="Contoh: TKO">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_default" value="1" id="is_default_add">
                        <label class="form-check-label" for="is_default_add">
                            Jadikan Unit Utama
                        </label>
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

<!-- Modal Edit Unit -->
<div class="modal fade" id="editUnitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="<?php echo BASEURL; ?>/units/ubah" method="POST">
                <input type="hidden" name="id_unit" id="edit-id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Unit Usaha</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Unit Usaha</label>
                        <input type="text" name="nama_unit" id="edit-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kode Unit</label>
                        <input type="text" name="kode_unit" id="edit-kode" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="edit-deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_default" value="1" id="edit-is-default">
                        <label class="form-check-label" for="edit-is-default">
                            Jadikan Unit Utama
                        </label>
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
        btn.addEventListener('click', function () {
            document.getElementById('edit-id').value = this.dataset.id;
            document.getElementById('edit-name').value = this.dataset.name;
            document.getElementById('edit-kode').value = this.dataset.kode;
            document.getElementById('edit-deskripsi').value = this.dataset.deskripsi;
            document.getElementById('edit-is-default').checked = (this.dataset.is_default == 1);
        });
    });
</script>
