<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Penerimaan Piutang</h3>
        <p class="text-muted small mb-0">Catat dan kelola penerimaan pembayaran dari pelanggan.</p>
    </div>
    <a href="<?php echo BASEURL; ?>/penerimaan/tambah" class="btn btn-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-plus-lg me-2"></i>Catat Penerimaan
    </a>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">Tanggal</th>
                        <th class="py-3">No. Bukti</th>
                        <th class="py-3">Pelanggan</th>
                        <th class="py-3 text-end">Total Diterima</th>
                        <th class="pe-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['penerimaan'])): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-cash-stack fs-1 d-block mb-3 opacity-25"></i>
                                    Belum ada data penerimaan yang tercatat.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['penerimaan'] as $pnr): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-medium"><?php echo date('d M Y', strtotime($pnr['tanggal'])); ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal"><?php echo htmlspecialchars($pnr['no_bukti']); ?></span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success-soft text-success rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="bi bi-person-check fs-6"></i>
                                    </div>
                                    <?php echo htmlspecialchars($pnr['nama_pelanggan']); ?>
                                </div>
                            </td>
                            <td class="text-end fw-bold text-success">
                                Rp <?php echo number_format($pnr['total_diterima'], 2, ',', '.'); ?>
                            </td>
                            <td class="pe-4 text-center">
                                <div class="btn-group">
                                    <a href="<?php echo BASEURL; ?>/penerimaan/lihat/<?php echo $pnr['id_penerimaan']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-2">
                                        <i class="bi bi-eye me-1"></i> Lihat
                                    </a>
                                    <?php if (Auth::isAdmin() || Auth::isManager()): ?>
                                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="if(confirm('Yakin ingin membatalkan penerimaan ini?')){ window.location.href='<?php echo BASEURL; ?>/penerimaan/hapus/<?php echo $pnr['id_penerimaan']; ?>'; }">
                                            <i class="bi bi-trash me-1"></i> Batalkan
                                        </button>
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
    .bg-success-soft { background-color: rgba(16, 185, 129, 0.1); }
    .table thead th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        font-weight: 700;
        border-bottom: 2px solid #f3f4f6;
    }
    .table tbody tr:last-child td { border-bottom: none; }
</style>
