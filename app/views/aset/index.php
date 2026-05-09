<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Manajemen Aset Tetap</h2>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalPenyusutan">
            <i class="bi bi-calculator me-2"></i>Jalankan Penyusutan
        </button>
        <a href="<?php echo BASEURL; ?>/aset/tambah" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-circle me-2"></i>Tambah Aset
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Kode</th>
                        <th>Nama Aset</th>
                        <th>Tanggal Perolehan</th>
                        <th class="text-end">Harga Perolehan</th>
                        <th class="text-center">Umur (Bulan)</th>
                        <th>Status</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['aset'])): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Belum ada data aset tetap.</td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach ($data['aset'] as $row): ?>
                    <tr>
                        <td class="ps-4 fw-medium text-primary"><?php echo $row['kode_aset']; ?></td>
                        <td>
                            <div class="fw-bold"><?php echo $row['nama_aset']; ?></div>
                            <small class="text-muted"><?php echo $row['metode_penyusutan']; ?></small>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($row['tanggal_perolehan'])); ?></td>
                        <td class="text-end fw-bold">Rp <?php echo number_format($row['harga_perolehan'], 2, ',', '.'); ?></td>
                        <td class="text-center"><?php echo $row['umur_ekonomis']; ?></td>
                        <td>
                            <span class="badge rounded-pill <?php 
                                echo $row['status'] == 'Aktif' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'; 
                            ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td class="text-center pe-4">
                            <div class="btn-group">
                                <a href="<?php echo BASEURL; ?>/aset/edit/<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary rounded-start-pill px-3">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?php echo BASEURL; ?>/aset/hapus/<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger rounded-end-pill px-3" onclick="return confirm('Apakah Anda yakin ingin menghapus aset ini?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Penyusutan -->
<div class="modal fade" id="modalPenyusutan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Proses Penyusutan Bulanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo BASEURL; ?>/aset/susutkan" method="post">
                <div class="modal-body py-4">
                    <p class="text-muted small mb-4">Sistem akan menghitung penyusutan untuk semua aset yang masih aktif dan membuat jurnal otomatis di akhir bulan yang dipilih.</p>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Pilih Bulan</label>
                            <select name="bulan" class="form-select">
                                <?php 
                                $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                foreach ($months as $i => $m): 
                                ?>
                                <option value="<?php echo sprintf('%02d', $i+1); ?>" <?php echo date('m') == ($i+1) ? 'selected' : ''; ?>>
                                    <?php echo $m; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Pilih Tahun</label>
                            <select name="tahun" class="form-select">
                                <?php for($y = date('Y')-2; $y <= date('Y')+1; $y++): ?>
                                <option value="<?php echo $y; ?>" <?php echo date('Y') == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Proses Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
