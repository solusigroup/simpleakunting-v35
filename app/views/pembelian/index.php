<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Jurnal Pembelian</h3>
        <p class="text-muted small mb-0">Kelola faktur pembelian stok dan hutang usaha kepada pemasok.</p>
    </div>
    <a href="<?php echo BASEURL; ?>/pembelian/tambah" class="btn btn-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-cart-plus me-2"></i>Tambah Pembelian
    </a>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-uppercase small fw-bold">
                    <tr>
                        <th class="ps-4 py-3">Tanggal</th>
                        <th class="py-3">No. Faktur</th>
                        <th class="py-3">Pemasok</th>
                        <th class="py-3 text-end">Total Akhir</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="pe-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['pembelian'])): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inboxes fs-1 d-block mb-3 opacity-25"></i>
                                Belum ada transaksi pembelian yang tercatat.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['pembelian'] as $p): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-medium"><?php echo date('d M Y', strtotime($p['tanggal_faktur'])); ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal"><?php echo htmlspecialchars($p['no_faktur_pembelian']); ?></span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($p['nama_pemasok']); ?></div>
                                <div class="small text-muted"><?php echo $p['metode_pembayaran']; ?></div>
                            </td>
                            <td class="text-end fw-bold text-primary">
                                Rp <?php echo number_format($p['total'], 0, ',', '.'); ?>
                            </td>
                            <td class="text-center">
                                <?php if($p['status_pembayaran'] === 'Lunas'): ?>
                                    <span class="badge bg-success-soft text-success rounded-pill px-3">Lunas</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-soft text-warning rounded-pill px-3">Belum Lunas</span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-4 text-center">
                                <div class="btn-group">
                                    <a href="<?php echo BASEURL; ?>/pembelian/lihat/<?php echo $p['id_pembelian']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-2">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    <?php if (Auth::isAdmin() || Auth::isManager()): ?>
                                        <a href="<?php echo BASEURL; ?>/pembelian/hapus/<?php echo $p['id_pembelian']; ?>" 
                                           class="btn btn-sm btn-outline-danger rounded-pill px-3" 
                                           onclick="return confirm('Yakin ingin membatalkan transaksi ini? Tindakan ini akan membatalkan jurnal dan mengurangi stok.');">
                                            <i class="bi bi-trash"></i>
                                        </a>
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
    .bg-warning-soft { background-color: rgba(245, 158, 11, 0.1); }
</style>
