<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <a href="<?php echo BASEURL; ?>/rfq" class="btn btn-link text-decoration-none text-muted p-0 me-3">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h3 class="fw-bold mb-0">Detail RFQ</h3>
            </div>
            <div>
                <?php if($data['rfq']['status'] != 'Ordered'): ?>
                    <a href="<?php echo BASEURL; ?>/rfq/convert_to_invoice/<?php echo $data['rfq']['id_rfq']; ?>" 
                       class="btn btn-success rounded-pill px-4 shadow-sm fw-bold"
                       onclick="return confirm('Konversi RFQ ini menjadi pesanan pembelian? Transaksi akan otomatis dijurnal dan stok akan bertambah.');">
                        <i class="bi bi-cart-check me-2"></i>Konversi ke Faktur Pembelian
                    </a>
                <?php else: ?>
                    <span class="badge bg-primary rounded-pill px-4 py-2 fs-6">
                        <i class="bi bi-check-all me-1"></i> Sudah Dipesan
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="card border-0 shadow-sm overflow-hidden mb-4">
            <div class="card-header bg-white border-0 p-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="small text-uppercase text-muted fw-bold mb-1">Kepada Pemasok</div>
                        <h4 class="fw-bold text-dark"><?php echo htmlspecialchars($data['rfq']['nama_pemasok']); ?></h4>
                        <p class="text-muted mb-0"><?php echo nl2br(htmlspecialchars($data['rfq']['alamat_pemasok'] ?? '')); ?></p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="small text-uppercase text-muted fw-bold mb-1">No. RFQ</div>
                        <h4 class="fw-bold text-primary mb-3"><?php echo htmlspecialchars($data['rfq']['no_rfq']); ?></h4>
                        <div class="d-flex justify-content-md-end">
                            <div class="me-4">
                                <div class="small text-uppercase text-muted fw-bold">Tanggal</div>
                                <div class="fw-bold"><?php echo date('d M Y', strtotime($data['rfq']['tanggal'])); ?></div>
                            </div>
                            <?php if($data['rfq']['tgl_kadaluarsa']): ?>
                            <div>
                                <div class="small text-uppercase text-muted fw-bold text-danger">Batas Waktu</div>
                                <div class="fw-bold text-danger"><?php echo date('d M Y', strtotime($data['rfq']['tgl_kadaluarsa'])); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light small fw-bold text-uppercase text-muted">
                            <tr>
                                <th class="ps-4 py-3">Nama Barang</th>
                                <th class="py-3 text-center">Kuantitas</th>
                                <th class="py-3 text-end">Est. Harga Beli</th>
                                <th class="pe-4 py-3 text-end">Total Estimasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['rfq']['details'] as $det): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($det['nama_barang']); ?></div>
                                    <div class="small text-muted"><?php echo htmlspecialchars($det['kode_barang']); ?></div>
                                </td>
                                <td class="text-center"><?php echo (float)$det['kuantitas']; ?></td>
                                <td class="text-end">Rp <?php echo number_format($det['harga_estimasi'], 0, ',', '.'); ?></td>
                                <td class="pe-4 text-end fw-bold">Rp <?php echo number_format($det['subtotal_estimasi'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr class="fs-5">
                                <td colspan="3" class="ps-4 py-3 text-end fw-bold text-dark">Total Nilai Permintaan</td>
                                <td class="pe-4 py-3 text-end fw-bold text-primary">Rp <?php echo number_format($data['rfq']['total_estimasi'], 0, ',', '.'); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <?php if(!empty($data['rfq']['keterangan'])): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h6 class="fw-bold small text-uppercase text-muted mb-3">Catatan Tambahan</h6>
                <div class="text-muted"><?php echo nl2br(htmlspecialchars($data['rfq']['keterangan'])); ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
