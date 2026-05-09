<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Jurnal Penjualan</h3>
        <p class="text-muted small mb-0">Daftar seluruh faktur penjualan dan piutang pelanggan.</p>
    </div>
    <a href="<?php echo BASEURL; ?>/penjualan/tambah" class="btn btn-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-plus-lg me-2"></i>Buat Faktur Baru
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
                        <th class="py-3">Pelanggan</th>
                        <th class="py-3 text-end">Subtotal</th>
                        <th class="py-3 text-end">Pajak/Diskon</th>
                        <th class="py-3 text-end">Total Akhir</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="pe-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['penjualan'])): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-cart-x fs-1 d-block mb-3 opacity-25"></i>
                                Belum ada transaksi penjualan yang tercatat.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['penjualan'] as $pjl): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-medium"><?php echo date('d M Y', strtotime($pjl['tanggal_faktur'])); ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal"><?php echo htmlspecialchars($pjl['no_faktur']); ?></span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($pjl['nama_pelanggan']); ?></div>
                                <div class="small text-muted"><?php echo $pjl['metode_pembayaran']; ?></div>
                            </td>
                            <td class="text-end">
                                Rp <?php echo number_format($pjl['total'] - $pjl['pajak'] + $pjl['diskon'], 0, ',', '.'); ?>
                            </td>
                            <td class="text-end">
                                <div class="small text-success">- Rp <?php echo number_format($pjl['diskon'], 0, ',', '.'); ?></div>
                                <div class="small text-danger">+ Rp <?php echo number_format($pjl['pajak'], 0, ',', '.'); ?></div>
                            </td>
                            <td class="text-end fw-bold text-primary">
                                Rp <?php echo number_format($pjl['total'], 0, ',', '.'); ?>
                            </td>
                            <td class="text-center">
                                <?php if($pjl['status_pembayaran'] === 'Lunas'): ?>
                                    <span class="badge bg-success-soft text-success rounded-pill px-3">Lunas</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-soft text-warning rounded-pill px-3">Belum Lunas</span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-4 text-center">
                                <div class="btn-group">
                                    <a href="<?php echo BASEURL; ?>/penjualan/lihat/<?php echo $pjl['id_penjualan']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-2">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    <?php if (Auth::isAdmin() || Auth::isManager()): ?>
                                        <a href="<?php echo BASEURL; ?>/penjualan/hapus/<?php echo $pjl['id_penjualan']; ?>" 
                                           class="btn btn-sm btn-outline-danger rounded-pill px-3" 
                                           onclick="return confirm('Yakin ingin membatalkan faktur ini? Tindakan ini akan membatalkan jurnal dan mengembalikan stok.');">
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
