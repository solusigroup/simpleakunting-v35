<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">Program & Sumber Dana</h1>
            <p class="text-muted">Kelola program bantuan pemerintah, CSR, atau dana hibah lainnya.</p>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahProgramModal">
                <i class="bi bi-plus-lg"></i> Tambah Program
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Nama Program</th>
                            <th>Tipe</th>
                            <th>Anggaran</th>
                            <th>Dibuat Pada</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($data['programs'] as $p): ?>
                            <tr>
                                <td class="ps-4"><?php echo $no++; ?></td>
                                <td class="fw-bold"><?php echo $p['nama_program']; ?></td>
                                <td>
                                    <span class="badge <?php 
                                        echo $p['tipe'] == 'Pemerintah' ? 'bg-primary' : ($p['tipe'] == 'CSR' ? 'bg-success' : 'bg-secondary'); 
                                    ?>">
                                        <?php echo $p['tipe']; ?>
                                    </span>
                                </td>
                                <td class="text-primary fw-bold">Rp <?php echo number_format($p['anggaran_total'], 0, ',', '.'); ?></td>
                                <td><?php echo date('d M Y', strtotime($p['created_at'])); ?></td>
                                <td class="text-end pe-4">
                                    <a href="<?php echo BASEURL; ?>/programs/realisasi/<?php echo $p['id_program']; ?>"
                                        class="btn btn-sm btn-info text-white" title="Lihat Realisasi Penggunaan Dana">
                                        <i class="bi bi-file-earmark-text"></i> Realisasi
                                    </a>
                                    <button class="btn btn-sm btn-outline-primary btn-edit"
                                        data-id="<?php echo $p['id_program']; ?>" 
                                        data-name="<?php echo $p['nama_program']; ?>"
                                        data-tipe="<?php echo $p['tipe']; ?>"
                                        data-anggaran="<?php echo $p['anggaran_total']; ?>"
                                        data-deskripsi="<?php echo $p['deskripsi']; ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editProgramModal">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="if(confirm('Hapus program ini? Data transaksi yang terkait akan tetap ada namun tidak lagi terikat program.')){ window.location.href='<?php echo BASEURL; ?>/programs/hapus/<?php echo $p['id_program']; ?>'; }">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(empty($data['programs'])): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada program yang terdaftar.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Program -->
<div class="modal fade" id="tambahProgramModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="<?php echo BASEURL; ?>/programs/tambah" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Program Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Program</label>
                        <input type="text" name="nama_program" class="form-control" required placeholder="Contoh: Dana Desa Tahap I 2024">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe Program</label>
                        <select name="tipe" class="form-select">
                            <option value="Pemerintah">Pemerintah</option>
                            <option value="CSR">CSR</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Anggaran (Rp)</label>
                        <input type="number" name="anggaran_total" class="form-control" step="0.01" placeholder="0.00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
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

<!-- Modal Edit Program -->
<div class="modal fade" id="editProgramModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="<?php echo BASEURL; ?>/programs/ubah" method="POST">
                <input type="hidden" name="id_program" id="edit-id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Program</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Program</label>
                        <input type="text" name="nama_program" id="edit-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe Program</label>
                        <select name="tipe" id="edit-tipe" class="form-select">
                            <option value="Pemerintah">Pemerintah</option>
                            <option value="CSR">CSR</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Anggaran (Rp)</label>
                        <input type="number" name="anggaran_total" id="edit-anggaran" class="form-control" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="edit-deskripsi" class="form-control" rows="3"></textarea>
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
            document.getElementById('edit-tipe').value = this.dataset.tipe;
            document.getElementById('edit-anggaran').value = this.dataset.anggaran;
            document.getElementById('edit-deskripsi').value = this.dataset.deskripsi;
        });
    });
</script>
