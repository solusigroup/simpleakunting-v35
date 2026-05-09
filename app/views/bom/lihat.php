<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <a href="<?php echo BASEURL; ?>/bom" class="btn btn-link text-decoration-none text-muted p-0 me-3">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h3 class="fw-bold mb-0">Detail BOM</h3>
            </div>
            <div class="d-print-none">
                <button onclick="window.print()" class="btn btn-outline-primary rounded-pill px-4">
                    <i class="bi bi-printer me-2"></i>Cetak Resep
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4 overflow-hidden">
            <div class="card-header bg-primary py-4 text-white">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <small class="text-uppercase opacity-75 fw-bold">Nama BOM / Versi</small>
                        <h2 class="fw-bold mb-0"><?php echo htmlspecialchars($data['bom']['nama_bom']); ?></h2>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                        <small class="text-uppercase opacity-75 fw-bold">Produk Hasil</small>
                        <h5 class="fw-bold mb-0 text-white-50"><?php echo htmlspecialchars($data['bom']['nama_produk']); ?></h5>
                        <small class="text-white-50"><?php echo htmlspecialchars($data['bom']['kode_barang']); ?></small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row mb-5">
                    <div class="col-md-4">
                        <div class="bg-light rounded-4 p-4 text-center h-100">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-2">Total Bahan</label>
                            <h3 class="fw-bold mb-0"><?php echo count($data['bom']['details']); ?> <small class="fs-6 text-muted fw-normal">Item</small></h3>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="bg-primary-soft rounded-4 p-4 h-100 border border-primary border-opacity-10">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Estimasi Biaya Produksi per Unit</label>
                                    <h2 class="fw-bold text-primary mb-0">Rp <?php echo number_format($data['bom']['total_biaya_estimasi'], 2, ',', '.'); ?></h2>
                                </div>
                                <div class="bg-primary text-white rounded-circle p-3">
                                    <i class="bi bi-currency-dollar fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold mb-3 d-flex align-items-center">
                    <i class="bi bi-box-seam me-2 text-primary"></i> 
                    Komposisi Bahan Baku (Recipe)
                </h5>
                <div class="table-responsive rounded-3 border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 py-3">Kode Bahan</th>
                                <th class="py-3">Nama Bahan</th>
                                <th class="py-3 text-center">Jumlah</th>
                                <th class="py-3 text-center">Satuan</th>
                                <th class="text-end pe-3 py-3">Estimasi Biaya</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['bom']['details'] as $item): ?>
                            <tr>
                                <td class="ps-3 text-muted"><?php echo htmlspecialchars($item['kode_barang']); ?></td>
                                <td class="fw-medium"><?php echo htmlspecialchars($item['nama_barang']); ?></td>
                                <td class="text-center"><?php echo (float)$item['jumlah']; ?></td>
                                <td class="text-center text-muted"><?php echo htmlspecialchars($item['satuan_barang']); ?></td>
                                <td class="text-end pe-3">Rp <?php echo number_format($item['biaya_satuan'] * $item['jumlah'], 2, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-light fw-bold">
                            <tr>
                                <td colspan="4" class="ps-3 py-3 text-end">ESTIMASI TOTAL BIAYA BAHAN BAKU</td>
                                <td class="text-end pe-3 py-3 text-primary fs-5">Rp <?php echo number_format($data['bom']['total_biaya_estimasi'], 2, ',', '.'); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="text-center d-print-none mt-4">
            <small class="text-muted">Gunakan BOM ini dalam modul <b>Perintah Produksi</b> untuk memulai proses manufaktur.</small>
        </div>
    </div>
</div>

<style>
    .bg-primary-soft { background-color: rgba(79, 70, 229, 0.05); }
    @media print {
        .sidebar, .topbar, .d-print-none { display: none !important; }
        .main-wrapper { margin-left: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #eee !important; }
        .bg-primary { background-color: #4f46e5 !important; -webkit-print-color-adjust: exact; }
    }
</style>
