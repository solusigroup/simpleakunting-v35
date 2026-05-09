<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <a href="<?php echo BASEURL; ?>/pembayaran" class="btn btn-link text-decoration-none text-muted p-0 me-3">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h3 class="fw-bold mb-0">Detail Pembayaran</h3>
            </div>
            <div class="d-print-none">
                <button onclick="window.print()" class="btn btn-outline-primary rounded-pill px-4 me-2">
                    <i class="bi bi-printer me-2"></i>Cetak Bukti
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4 overflow-hidden">
            <div class="bg-primary p-4 text-white">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-uppercase opacity-75 fw-bold">Nomor Bukti</small>
                        <h2 class="fw-bold mb-0"><?php echo htmlspecialchars($data['pembayaran']['no_bukti']); ?></h2>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <small class="text-uppercase opacity-75 fw-bold">Total Pembayaran</small>
                        <h2 class="fw-bold mb-0">Rp <?php echo number_format($data['pembayaran']['total_dibayar'], 2, ',', '.'); ?></h2>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row mb-5 g-4">
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Dibayarkan Kepada</label>
                        <div class="d-flex align-items-start">
                            <div class="bg-light rounded-circle p-3 me-3">
                                <i class="bi bi-truck fs-3 text-primary"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($data['pembayaran']['nama_pemasok']); ?></h5>
                                <p class="text-muted mb-0 small"><?php echo nl2br(htmlspecialchars($data['pembayaran']['alamat_pemasok'] ?? '')); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light rounded-4 p-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Tanggal</label>
                                    <span class="fw-bold"><?php echo date('d M Y', strtotime($data['pembayaran']['tanggal'])); ?></span>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Sumber Dana</label>
                                    <span class="fw-bold text-primary"><?php echo htmlspecialchars($data['pembayaran']['nama_akun_kas']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold mb-3 d-flex align-items-center">
                    <i class="bi bi-list-columns-reverse me-2 text-primary"></i> 
                    Rincian Pembayaran Faktur
                </h5>
                <div class="table-responsive rounded-3 border mb-4">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 py-3">Nomor Faktur</th>
                                <th class="py-3">Tanggal Faktur</th>
                                <th class="text-end pe-3 py-3">Jumlah Dibayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['pembayaran']['details'] as $item): ?>
                            <tr>
                                <td class="ps-3 fw-medium text-primary"><?php echo htmlspecialchars($item['no_faktur_pembelian']); ?></td>
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
                                <td class="text-end pe-3 py-3 text-primary fs-5">Rp <?php echo number_format($data['pembayaran']['total_dibayar'], 2, ',', '.'); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <?php if(!empty($data['pembayaran']['keterangan'])): ?>
                <div class="bg-light rounded-3 p-3 border-start border-primary border-4">
                    <label class="text-muted small fw-bold text-uppercase mb-1 d-block">Catatan / Keterangan</label>
                    <p class="mb-0 italic">"<?php echo nl2br(htmlspecialchars($data['pembayaran']['keterangan'])); ?>"</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="text-center d-print-none mt-4">
            <small class="text-muted">Bukti pembayaran ini sah dan dihasilkan secara otomatis oleh sistem.</small>
        </div>
    </div>
</div>

<style>
    @media print {
        .sidebar, .topbar, .d-print-none { display: none !important; }
        .main-wrapper { margin-left: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #eee !important; }
        .bg-primary { background-color: #4f46e5 !important; -webkit-print-color-adjust: exact; }
    }
</style>
