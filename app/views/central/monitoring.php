<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3 mb-0 text-gray-800">Global Transaction Monitoring</h1>
            <p class="text-muted">Memantau seluruh aktivitas transaksi dari semua tenant secara real-time.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 text-dark"><i class="bi bi-clock-history me-2"></i> 50 Transaksi Terbaru</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Tanggal & Waktu</th>
                            <th>Tenant</th>
                            <th>No. Transaksi</th>
                            <th>Deskripsi</th>
                            <th>Sumber</th>
                            <th class="text-end pe-4">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['transactions'])): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada transaksi di sistem.</td>
                        </tr>
                        <?php endif; ?>
                        <?php foreach($data['transactions'] as $trx) : ?>
                        <tr>
                            <td class="ps-4">
                                <div><?php echo date('d M Y', strtotime($trx['tanggal'])); ?></div>
                                <small class="text-muted"><?php echo date('H:i:s', strtotime($trx['created_at'])); ?></small>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border border-primary-subtle px-2 py-1">
                                    <?php echo htmlspecialchars($trx['tenant_name']); ?>
                                </span>
                            </td>
                            <td class="fw-bold"><?php echo $trx['no_transaksi']; ?></td>
                            <td><?php echo htmlspecialchars($trx['deskripsi']); ?></td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                    <?php echo $trx['sumber_jurnal']; ?>
                                </span>
                            </td>
                            <td class="text-end pe-4 fw-bold text-primary">
                                Rp <?php echo number_format($trx['total'], 0, ',', '.'); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white text-center py-3">
            <small class="text-muted">Data diperbarui secara otomatis berdasarkan timestamp pembuatan jurnal.</small>
        </div>
    </div>
</div>
