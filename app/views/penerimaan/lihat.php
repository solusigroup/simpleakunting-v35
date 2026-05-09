<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <a href="<?php echo BASEURL; ?>/penerimaan" class="btn btn-link text-decoration-none text-muted p-0 me-3">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h3 class="fw-bold mb-0">Detail Penerimaan Piutang</h3>
            </div>
            <div class="d-print-none">
                <button onclick="window.print()" class="btn btn-outline-success rounded-pill px-4 me-2">
                    <i class="bi bi-printer me-2"></i>Cetak Bukti
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4 overflow-hidden">
            <div class="bg-success p-4 text-white">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-uppercase opacity-75 fw-bold">Nomor Bukti</small>
                        <h2 class="fw-bold mb-0"><?php echo htmlspecialchars($data['penerimaan']['no_bukti']); ?></h2>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <small class="text-uppercase opacity-75 fw-bold">Total Diterima</small>
                        <h2 class="fw-bold mb-0">Rp <?php echo number_format($data['penerimaan']['total_diterima'], 2, ',', '.'); ?></h2>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row mb-5 g-4">
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Diterima Dari</label>
                        <div class="d-flex align-items-start">
                            <div class="bg-light rounded-circle p-3 me-3">
                                <i class="bi bi-person-check fs-3 text-success"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($data['penerimaan']['nama_pelanggan']); ?></h5>
                                <p class="text-muted mb-0 small"><?php echo nl2br(htmlspecialchars($data['penerimaan']['alamat_pelanggan'] ?? '')); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light rounded-4 p-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Tanggal</label>
                                    <span class="fw-bold"><?php echo date('d M Y', strtotime($data['penerimaan']['tanggal'])); ?></span>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Masuk Ke Akun</label>
                                    <span class="fw-bold text-success"><?php echo htmlspecialchars($data['penerimaan']['nama_akun_kas']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold mb-3 d-flex align-items-center">
                    <i class="bi bi-receipt-cutoff me-2 text-success"></i> 
                    Rincian Pelunasan Faktur
                </h5>
                <div class="table-responsive rounded-3 border mb-4">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 py-3">Nomor Faktur</th>
                                <th class="py-3">Tanggal Faktur</th>
                                <th class="text-end pe-3 py-3">Jumlah Diterima</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['penerimaan']['details'] as $item): ?>
                            <tr>
                                <td class="ps-3 fw-medium text-success"><?php echo htmlspecialchars($item['no_faktur']); ?></td>
                                <td class="text-muted">
                                    <?php echo ($item['tanggal_faktur'] !== 'N/A' && $item['tanggal_faktur'] !== null) ? date('d M Y', strtotime($item['tanggal_faktur'])) : '<span class="badge bg-light text-dark fw-normal border">N/A</span>'; ?>
                                </td>
                                <td class="text-end pe-3 fw-bold">Rp <?php echo number_format($item['jumlah_bayar'], 2, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-light fw-bold">
                            <tr>
                                <td colspan="2" class="ps-3 py-3 text-end">TOTAL KESELURUHAN</td>
                                <td class="text-end pe-3 py-3 text-success fs-5">Rp <?php echo number_format($data['penerimaan']['total_diterima'], 2, ',', '.'); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <?php if(!empty($data['penerimaan']['keterangan'])): ?>
                <div class="bg-light rounded-3 p-3 border-start border-success border-4">
                    <label class="text-muted small fw-bold text-uppercase mb-1 d-block">Catatan / Keterangan</label>
                    <p class="mb-0 italic">"<?php echo nl2br(htmlspecialchars($data['penerimaan']['keterangan'])); ?>"</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="text-center d-print-none mt-4">
            <small class="text-muted">Bukti penerimaan ini sah dan dihasilkan secara otomatis oleh sistem.</small>
        </div>
    </div>
</div>

<style>
    @media print {
        .sidebar, .topbar, .d-print-none { display: none !important; }
        .main-wrapper { margin-left: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #eee !important; }
        .bg-success { background-color: #10b981 !important; -webkit-print-color-adjust: exact; }
    }
</style>
