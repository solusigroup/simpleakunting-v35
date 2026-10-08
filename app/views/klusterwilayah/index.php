<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">Manajemen Kluster Wilayah</h1>
            <p class="text-muted">Kelola daftar kabupaten/kota sebagai kluster wilayah untuk pengelompokan BUMDesa.</p>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahKlusterModal">
                <i class="bi bi-plus-lg"></i> Tambah Kluster
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
                            <th>Nama Kabupaten/Kota</th>
                            <th>Kode</th>
                            <th>Provinsi</th>
                            <th>Jumlah Tenant</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['klusters'])): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada data kluster wilayah.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data['klusters'] as $kluster): ?>
                                <tr>
                                    <td class="ps-4"><?php echo $kluster['id']; ?></td>
                                    <td class="fw-bold"><?php echo htmlspecialchars($kluster['nama_kabupaten']); ?></td>
                                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($kluster['kode_kabupaten']); ?></span></td>
                                    <td><?php echo htmlspecialchars($kluster['provinsi']); ?></td>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            <?php echo $kluster['tenant_count'] ?? 0; ?> Tenant
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo ($kluster['status'] ?? 'active') == 'active' ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo ucfirst($kluster['status'] ?? 'active'); ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-outline-primary btn-edit"
                                            data-id="<?php echo $kluster['id']; ?>"
                                            data-nama="<?php echo htmlspecialchars($kluster['nama_kabupaten'], ENT_QUOTES); ?>"
                                            data-kode="<?php echo htmlspecialchars($kluster['kode_kabupaten'], ENT_QUOTES); ?>"
                                            data-provinsi="<?php echo htmlspecialchars($kluster['provinsi'], ENT_QUOTES); ?>"
                                            data-status="<?php echo htmlspecialchars($kluster['status'] ?? 'active', ENT_QUOTES); ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editKlusterModal"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger"
                                            onclick="if(confirm('Yakin ingin menghapus kluster wilayah ini?')){ window.location.href='<?php echo BASEURL; ?>/klusterwilayah/hapus/<?php echo $kluster['id']; ?>'; }"
                                            title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kluster -->
<div class="modal fade" id="tambahKlusterModal" tabindex="-1" aria-labelledby="tambahKlusterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="<?php echo BASEURL; ?>/klusterwilayah/tambah" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahKlusterModalLabel">Tambah Kluster Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kabupaten/Kota</label>
                        <input type="text" name="nama_kabupaten" class="form-control" required placeholder="Contoh: Kabupaten Malang">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kode Kabupaten</label>
                        <input type="text" name="kode_kabupaten" class="form-control" required placeholder="Contoh: MLG">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Provinsi</label>
                        <input type="text" name="provinsi" class="form-control" required value="Jawa Timur">
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

<!-- Modal Edit Kluster -->
<div class="modal fade" id="editKlusterModal" tabindex="-1" aria-labelledby="editKlusterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="<?php echo BASEURL; ?>/klusterwilayah/ubah" method="POST">
                <input type="hidden" name="id" id="edit-id">
                <div class="modal-header">
                    <h5 class="modal-title" id="editKlusterModalLabel">Edit Kluster Wilayah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kabupaten/Kota</label>
                        <input type="text" name="nama_kabupaten" id="edit-nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kode Kabupaten</label>
                        <input type="text" name="kode_kabupaten" id="edit-kode" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Provinsi</label>
                        <input type="text" name="provinsi" id="edit-provinsi" class="form-control" required>
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
        btn.addEventListener('click', function () {
            document.getElementById('edit-id').value = this.dataset.id;
            document.getElementById('edit-nama').value = this.dataset.nama;
            document.getElementById('edit-kode').value = this.dataset.kode;
            document.getElementById('edit-provinsi').value = this.dataset.provinsi;
            document.getElementById('edit-status').value = this.dataset.status;
        });
    });
</script>
