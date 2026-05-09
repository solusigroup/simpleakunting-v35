<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <a href="<?php echo BASEURL; ?>/penawaran" class="btn btn-link text-decoration-none text-muted p-0 me-3">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h3 class="fw-bold mb-0">Detail Penawaran Harga</h3>
            </div>
            <div>
                <?php if($data['penawaran']['status'] != 'Invoiced'): ?>
                    <a href="<?php echo BASEURL; ?>/penawaran/convert_to_invoice/<?php echo $data['penawaran']['id_penawaran']; ?>" 
                       class="btn btn-success rounded-pill px-4 shadow-sm fw-bold"
                       onclick="return confirm('Konversi penawaran ini menjadi faktur penjualan? Transaksi akan otomatis dijurnal dan stok akan berkurang.');">
                        <i class="bi bi-arrow-right-circle me-2"></i>Konversi ke Faktur (Invoice)
                    </a>
                <?php else: ?>
                    <span class="badge bg-primary rounded-pill px-4 py-2 fs-6">
                        <i class="bi bi-check-all me-1"></i> Sudah Menjadi Faktur
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="card border-0 shadow-sm overflow-hidden mb-4">
            <div class="card-header bg-white border-0 p-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="small text-uppercase text-muted fw-bold mb-1">Diberikan Kepada</div>
                        <h4 class="fw-bold text-dark"><?php echo htmlspecialchars($data['penawaran']['nama_pelanggan']); ?></h4>
                        <p class="text-muted mb-0"><?php echo nl2br(htmlspecialchars($data['penawaran']['alamat_pelanggan'] ?? '')); ?></p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="small text-uppercase text-muted fw-bold mb-1">No. Penawaran</div>
                        <h4 class="fw-bold text-primary mb-3"><?php echo htmlspecialchars($data['penawaran']['no_penawaran']); ?></h4>
                        <div class="d-flex justify-content-md-end">
                            <div class="me-4">
                                <div class="small text-uppercase text-muted fw-bold">Tanggal</div>
                                <div class="fw-bold"><?php echo date('d M Y', strtotime($data['penawaran']['tanggal'])); ?></div>
                            </div>
                            <?php if($data['penawaran']['tgl_kadaluarsa']): ?>
                            <div>
                                <div class="small text-uppercase text-muted fw-bold text-danger">Kadaluarsa</div>
                                <div class="fw-bold text-danger"><?php echo date('d M Y', strtotime($data['penawaran']['tgl_kadaluarsa'])); ?></div>
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
                                <th class="ps-4 py-3">Deskripsi Barang / Jasa</th>
                                <th class="py-3 text-center">Kuantitas</th>
                                <th class="py-3 text-end">Harga Satuan</th>
                                <th class="pe-4 py-3 text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['penawaran']['details'] as $det): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($det['nama_barang']); ?></div>
                                    <div class="small text-muted"><?php echo htmlspecialchars($det['kode_barang']); ?></div>
                                </td>
                                <td class="text-center"><?php echo (float)$det['kuantitas']; ?></td>
                                <td class="text-end">Rp <?php echo number_format($det['harga'], 0, ',', '.'); ?></td>
                                <td class="pe-4 text-end fw-bold">Rp <?php echo number_format($det['subtotal'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="3" class="ps-4 py-3 text-end fw-bold">Subtotal</td>
                                <td class="pe-4 py-3 text-end fw-bold">Rp <?php echo number_format($data['penawaran']['total'] + $data['penawaran']['diskon'] - $data['penawaran']['pajak'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php if($data['penawaran']['diskon'] > 0): ?>
                            <tr>
                                <td colspan="3" class="ps-4 py-2 text-end text-success">Diskon</td>
                                <td class="pe-4 py-2 text-end text-success">- Rp <?php echo number_format($data['penawaran']['diskon'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if($data['penawaran']['pajak'] > 0): ?>
                            <tr>
                                <td colspan="3" class="ps-4 py-2 text-end text-danger">Pajak (PPN)</td>
                                <td class="pe-4 py-2 text-end text-danger">+ Rp <?php echo number_format($data['penawaran']['pajak'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr class="fs-5">
                                <td colspan="3" class="ps-4 py-3 text-end fw-bold text-dark">Total Estimasi</td>
                                <td class="pe-4 py-3 text-end fw-bold text-primary">Rp <?php echo number_format($data['penawaran']['total'], 0, ',', '.'); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <?php if(!empty($data['penawaran']['keterangan'])): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h6 class="fw-bold small text-uppercase text-muted mb-3">Syarat & Ketentuan</h6>
                <div class="text-muted"><?php echo nl2br(htmlspecialchars($data['penawaran']['keterangan'])); ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
