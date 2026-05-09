<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Perintah Produksi</h3>
        <p class="text-muted small mb-0">Kelola proses manufaktur dan transformasi bahan baku menjadi produk jadi.</p>
    </div>
    <a href="<?php echo BASEURL; ?>/produksi/tambah" class="btn btn-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-plus-lg me-2"></i>Buat Produksi
    </a>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">Tanggal</th>
                        <th class="py-3">No. Produksi</th>
                        <th class="py-3">Produk & BOM</th>
                        <th class="py-3 text-center">Target Qty</th>
                        <th class="py-3">Status</th>
                        <th class="pe-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['produksi'])): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-gear-wide-connected fs-1 d-block mb-3 opacity-25"></i>
                                    Belum ada perintah produksi yang tercatat.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['produksi'] as $p): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-medium small text-muted"><?php echo date('d M Y', strtotime($p['tanggal'])); ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal"><?php echo htmlspecialchars($p['no_produksi']); ?></span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($p['nama_produk']); ?></div>
                                <div class="text-primary small">Resep: <?php echo htmlspecialchars($p['nama_bom']); ?></div>
                            </td>
                            <td class="text-center">
                                <div class="fw-bold fs-5 mb-0"><?php echo (float)$p['jumlah_target']; ?></div>
                            </td>
                            <td>
                                <?php if($p['status'] === 'Draft'): ?>
                                    <span class="badge bg-warning-soft text-warning rounded-pill px-3">
                                        <i class="bi bi-clock me-1"></i> Draft / Progress
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success-soft text-success rounded-pill px-3">
                                        <i class="bi bi-check-all me-1"></i> Selesai
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-4 text-center">
                                <div class="btn-group">
                                    <a href="<?php echo BASEURL; ?>/produksi/lihat/<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-2">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </a>
                                    <?php if($p['status'] === 'Draft'): ?>
                                        <button class="btn btn-sm btn-success rounded-pill px-3 me-2" data-bs-toggle="modal" data-bs-target="#modalSelesai<?php echo $p['id']; ?>">
                                            <i class="bi bi-check-circle me-1"></i> Selesaikan
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="if(confirm('Hapus perintah produksi ini?')){ window.location.href='<?php echo BASEURL; ?>/produksi/hapus/<?php echo $p['id']; ?>'; }">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                        <!-- Modal Selesaikan -->
                                        <div class="modal fade" id="modalSelesai<?php echo $p['id']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold text-dark">Selesaikan Produksi</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="<?php echo BASEURL; ?>/produksi/selesaikan/<?php echo $p['id']; ?>" method="post">
                                                        <div class="modal-body py-4">
                                                            <div class="text-center mb-4">
                                                                <div class="bg-success-soft text-success rounded-circle p-3 d-inline-block mb-3">
                                                                    <i class="bi bi-gear-wide-connected fs-1"></i>
                                                                </div>
                                                                <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($p['no_produksi']); ?></h6>
                                                                <p class="text-muted small"><?php echo htmlspecialchars($p['nama_produk']); ?></p>
                                                            </div>
                                                            
                                                            <p class="small text-muted mb-3">Masukkan biaya tambahan selain bahan baku untuk menghitung HPP yang akurat:</p>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold">Biaya Tenaga Kerja Langsung (Optional)</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text">Rp</span>
                                                                    <input type="number" name="biaya_tenaga_kerja" class="form-control" placeholder="0" step="0.01">
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="mb-0">
                                                                <label class="form-label small fw-bold">Biaya Overhead Pabrik (Optional)</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text">Rp</span>
                                                                    <input type="number" name="biaya_overhead" class="form-control" placeholder="0" step="0.01">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0">
                                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-success rounded-pill px-4 shadow">Konfirmasi Selesai</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .bg-warning-soft { background-color: rgba(245, 158, 11, 0.1); }
    .bg-success-soft { background-color: rgba(16, 185, 129, 0.1); }
    .table thead th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        font-weight: 700;
        border-bottom: 2px solid #f3f4f6;
    }
</style>
