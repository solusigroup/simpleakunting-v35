<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <a href="<?php echo BASEURL; ?>/produksi" class="btn btn-link text-decoration-none text-muted p-0 me-3">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h3 class="fw-bold mb-0">Detail Produksi</h3>
            </div>
            <div class="d-print-none">
                <button onclick="window.print()" class="btn btn-outline-primary rounded-pill px-4">
                    <i class="bi bi-printer me-2"></i>Cetak WO
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4 overflow-hidden">
            <div class="card-header <?php echo ($data['produksi']['status'] === 'Selesai') ? 'bg-success' : 'bg-warning'; ?> py-4 text-white border-0">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <small class="text-uppercase opacity-75 fw-bold">Nomor Produksi</small>
                        <h2 class="fw-bold mb-0"><?php echo htmlspecialchars($data['produksi']['no_produksi']); ?></h2>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                        <small class="text-uppercase opacity-75 fw-bold">Status Produksi</small>
                        <div class="h4 fw-bold mb-0"><?php echo strtoupper($data['produksi']['status']); ?></div>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row mb-5 g-4">
                    <div class="col-md-6">
                        <div class="bg-light rounded-4 p-4 h-100">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-3">Informasi Produk</label>
                            <h5 class="fw-bold mb-1 text-primary"><?php echo htmlspecialchars($data['produksi']['nama_produk']); ?></h5>
                            <div class="text-muted mb-3 small"><?php echo htmlspecialchars($data['produksi']['kode_produk']); ?></div>
                            <div class="d-flex justify-content-between border-top pt-3 mt-2">
                                <span class="text-muted">Target Output:</span>
                                <span class="fw-bold fs-5"><?php echo (float)$data['produksi']['jumlah_target']; ?> Unit</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light rounded-4 p-4 h-100">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-3">Timeline & Biaya</label>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tanggal Mulai:</span>
                                <span class="fw-bold"><?php echo date('d M Y', strtotime($data['produksi']['tanggal'])); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                <span class="text-muted">Resep (BOM):</span>
                                <span class="fw-bold text-primary"><?php echo htmlspecialchars($data['produksi']['nama_bom']); ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Total Biaya Aktual:</span>
                                <span class="fw-bold h4 mb-0 text-success">Rp <?php echo number_format($data['produksi']['total_biaya_aktual'] ?? 0, 2, ',', '.'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($data['produksi']['status'] === 'Selesai' && !empty($data['produksi']['id_jurnal'])): ?>
                <div class="alert alert-success border-0 rounded-4 p-4 d-flex align-items-center shadow-sm">
                    <div class="bg-white rounded-circle p-2 me-4">
                        <i class="bi bi-journal-check fs-2 text-success"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Transaksi Jurnal Terbentuk</h6>
                        <p class="mb-0 small opacity-75">Sistem telah membukukan pemakaian bahan baku dan penambahan barang jadi secara otomatis.</p>
                        <a href="<?php echo BASEURL; ?>/jurnal" class="btn btn-sm btn-success mt-2 rounded-pill px-3">Lihat di Jurnal</a>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mt-5 mb-4">
                    <h5 class="fw-bold d-flex align-items-center">
                        <i class="bi bi-diagram-3 me-2 text-primary"></i> 
                        Dampak Aliran Persediaan
                    </h5>
                    <p class="text-muted small">Visualisasi perpindahan stok berdasarkan resep BOM.</p>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-5">
                        <div class="card border border-danger border-opacity-10 bg-danger-soft h-100">
                            <div class="card-body">
                                <h6 class="fw-bold text-danger mb-3 small uppercase">Pengurangan Bahan Baku (OUT)</h6>
                                <p class="small text-muted mb-0">Semua bahan baku yang terdaftar di <b>BOM <?php echo htmlspecialchars($data['produksi']['nama_bom']); ?></b> telah/akan dikurangi dari gudang.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-center justify-content-center">
                        <i class="bi bi-arrow-right fs-1 text-muted opacity-25 d-none d-md-block"></i>
                        <i class="bi bi-arrow-down fs-1 text-muted opacity-25 d-block d-md-none"></i>
                    </div>
                    <div class="col-md-5">
                        <div class="card border border-success border-opacity-10 bg-success-soft h-100">
                            <div class="card-body">
                                <h6 class="fw-bold text-success mb-3 small uppercase">Penambahan Barang Jadi (IN)</h6>
                                <p class="small text-muted mb-0">Produk <b><?php echo htmlspecialchars($data['produksi']['nama_produk']); ?></b> bertambah sebanyak <b><?php echo (float)$data['produksi']['jumlah_target']; ?> unit</b> ke inventori.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center d-print-none mt-4">
            <small class="text-muted">Work Order ini adalah dokumen resmi instruksi produksi.</small>
        </div>
    </div>
</div>

<style>
    .bg-danger-soft { background-color: rgba(239, 68, 68, 0.05); }
    .bg-success-soft { background-color: rgba(16, 185, 129, 0.05); }
    @media print {
        .sidebar, .topbar, .d-print-none { display: none !important; }
        .main-wrapper { margin-left: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #eee !important; }
        .bg-success { background-color: #10b981 !important; -webkit-print-color-adjust: exact; }
        .bg-warning { background-color: #f59e0b !important; -webkit-print-color-adjust: exact; }
    }
</style>
