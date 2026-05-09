<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Penawaran Harga</h3>
        <p class="text-muted small mb-0">Kelola kuotasi dan penawaran harga kepada calon pelanggan.</p>
    </div>
    <a href="<?php echo BASEURL; ?>/penawaran/tambah" class="btn btn-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-plus-lg me-2"></i>Buat Penawaran
    </a>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-uppercase small fw-bold">
                    <tr>
                        <th class="ps-4 py-3">Tanggal</th>
                        <th class="py-3">No. Penawaran</th>
                        <th class="py-3">Pelanggan</th>
                        <th class="py-3 text-end">Total Est.</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="pe-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['penawaran'])): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark-text fs-1 d-block mb-3 opacity-25"></i>
                                Belum ada penawaran yang dibuat.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['penawaran'] as $p): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-medium"><?php echo date('d M Y', strtotime($p['tanggal'])); ?></div>
                                <?php if($p['tgl_kadaluarsa']): ?>
                                    <div class="small text-danger">Exp: <?php echo date('d/m/y', strtotime($p['tgl_kadaluarsa'])); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal"><?php echo htmlspecialchars($p['no_penawaran']); ?></span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($p['nama_pelanggan']); ?></div>
                            </td>
                            <td class="text-end fw-bold">
                                Rp <?php echo number_format($p['total'], 0, ',', '.'); ?>
                            </td>
                            <td class="text-center">
                                <?php 
                                $badgeClass = 'bg-secondary';
                                if($p['status'] == 'Accepted') $badgeClass = 'bg-success';
                                if($p['status'] == 'Invoiced') $badgeClass = 'bg-primary';
                                if($p['status'] == 'Rejected') $badgeClass = 'bg-danger';
                                ?>
                                <span class="badge <?php echo $badgeClass; ?> rounded-pill px-3"><?php echo $p['status']; ?></span>
                            </td>
                            <td class="pe-4 text-center">
                                <div class="btn-group">
                                    <a href="<?php echo BASEURL; ?>/penawaran/lihat/<?php echo $p['id_penawaran']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-2">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    <?php if($p['status'] != 'Invoiced'): ?>
                                        <a href="<?php echo BASEURL; ?>/penawaran/convert_to_invoice/<?php echo $p['id_penawaran']; ?>" 
                                           class="btn btn-sm btn-success rounded-pill px-3" 
                                           onclick="return confirm('Konversi penawaran ini menjadi faktur penjualan?');">
                                            <i class="bi bi-arrow-right-circle"></i> Invoice
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
