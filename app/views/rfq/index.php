<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Request for Quotation (RFQ)</h3>
        <p class="text-muted small mb-0">Kelola permintaan penawaran harga kepada pemasok atau vendor.</p>
    </div>
    <a href="<?php echo BASEURL; ?>/rfq/tambah" class="btn btn-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-plus-lg me-2"></i>Buat RFQ Baru
    </a>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-uppercase small fw-bold">
                    <tr>
                        <th class="ps-4 py-3">Tanggal</th>
                        <th class="py-3">No. RFQ</th>
                        <th class="py-3">Pemasok</th>
                        <th class="py-3 text-end">Est. Total</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="pe-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['rfq'])): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-envelope-paper fs-1 d-block mb-3 opacity-25"></i>
                                Belum ada RFQ yang dibuat.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['rfq'] as $r): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-medium"><?php echo date('d M Y', strtotime($r['tanggal'])); ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal"><?php echo htmlspecialchars($r['no_rfq']); ?></span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($r['nama_pemasok']); ?></div>
                            </td>
                            <td class="text-end fw-bold">
                                Rp <?php echo number_format($r['total_estimasi'], 0, ',', '.'); ?>
                            </td>
                            <td class="text-center">
                                <?php 
                                $badgeClass = 'bg-secondary';
                                if($r['status'] == 'Sent') $badgeClass = 'bg-info';
                                if($r['status'] == 'Ordered') $badgeClass = 'bg-success';
                                ?>
                                <span class="badge <?php echo $badgeClass; ?> rounded-pill px-3"><?php echo $r['status']; ?></span>
                            </td>
                            <td class="pe-4 text-center">
                                <div class="btn-group">
                                    <a href="<?php echo BASEURL; ?>/rfq/lihat/<?php echo $r['id_rfq']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-2">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    <?php if($r['status'] != 'Ordered'): ?>
                                        <a href="<?php echo BASEURL; ?>/rfq/convert_to_invoice/<?php echo $r['id_rfq']; ?>" 
                                           class="btn btn-sm btn-success rounded-pill px-3" 
                                           onclick="return confirm('Konversi RFQ ini menjadi faktur pembelian? Stok akan otomatis bertambah.');">
                                            <i class="bi bi-cart-check"></i> Pesan
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
