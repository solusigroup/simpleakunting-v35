<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <h4 class="fw-bold mb-0">Laporan Aktivitas Produksi</h4>
    </div>
    <div class="card-body">
        <form action="<?php echo BASEURL; ?>/laporan/produksi" method="post" class="row g-3 align-items-end mb-4">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Dari Tanggal</label>
                <input type="date" name="tanggal_mulai" class="form-control" value="<?php echo $data['tanggal_mulai']; ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Sampai Tanggal</label>
                <input type="date" name="tanggal_selesai" class="form-control" value="<?php echo $data['tanggal_selesai']; ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100 rounded-pill">
                    <i class="bi bi-search me-2"></i>Tampilkan Laporan
                </button>
            </div>
        </form>

        <?php if (isset($data['laporan'])): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No. Produksi</th>
                        <th>Tanggal</th>
                        <th>Produk Jadi</th>
                        <th class="text-center">Target Qty</th>
                        <th class="text-end">Total Biaya (HPP)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['laporan'])): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Tidak ada aktivitas produksi pada periode ini.</td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach ($data['laporan'] as $row): ?>
                    <tr>
                        <td class="fw-bold text-primary"><?php echo $row['no_produksi']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                        <td>
                            <div class="fw-bold"><?php echo $row['nama_produk']; ?></div>
                            <small class="text-muted"><?php echo $row['nama_bom']; ?></small>
                        </td>
                        <td class="text-center"><?php echo number_format($row['jumlah_target'], 2); ?></td>
                        <td class="text-end fw-bold">
                            <?php echo $row['total_biaya_aktual'] > 0 ? 'Rp ' . number_format($row['total_biaya_aktual'], 2, ',', '.') : '-'; ?>
                        </td>
                        <td>
                            <span class="badge rounded-pill <?php echo $row['status'] == 'Selesai' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'; ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
