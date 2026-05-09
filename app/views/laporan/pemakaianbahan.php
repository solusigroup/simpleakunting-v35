<div class="row mb-4 d-print-none">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="<?php echo BASEURL; ?>/laporan/pemakaianBahan" method="post" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" value="<?php echo $data['tanggal_mulai']; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control" value="<?php echo $data['tanggal_selesai']; ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill">
                            <i class="bi bi-search me-2"></i>Tampilkan Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-5">
        <!-- Header Laporan -->
        <div class="text-center mb-5">
            <h4 class="fw-bold mb-1"><?php echo strtoupper($data['perusahaan']['nama_perusahaan']); ?></h4>
            <h5 class="mb-1">LAPORAN PEMAKAIAN BAHAN BAKU</h5>
            <p class="text-muted small mb-0">Periode: <?php echo $data['periode_1']; ?></p>
            <hr class="mx-auto" style="width: 100px; border-top: 3px solid var(--primary-color);">
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3">Tanggal</th>
                        <th class="py-3">No. Produksi</th>
                        <th class="py-3">Bahan Baku</th>
                        <th class="text-center py-3">Qty Dipakai</th>
                        <th class="py-3">Satuan</th>
                        <th class="text-end pe-3 py-3">Total Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['laporan'])): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Tidak ada data pemakaian bahan pada periode ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $grandTotal = 0;
                        foreach ($data['laporan'] as $row): 
                            $grandTotal += $row['total_biaya'];
                        ?>
                        <tr>
                            <td class="ps-3 small"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                            <td class="small"><?php echo $row['no_produksi']; ?></td>
                            <td>
                                <div class="fw-bold"><?php echo $row['nama_bahan']; ?></div>
                                <small class="text-muted"><?php echo $row['kode_barang']; ?></small>
                            </td>
                            <td class="text-center fw-bold"><?php echo number_format($row['qty_dipakai'], 2); ?></td>
                            <td class="text-muted"><?php echo $row['satuan']; ?></td>
                            <td class="text-end pe-3">Rp <?php echo number_format($row['total_biaya'], 2, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($data['laporan'])): ?>
                <tfoot class="bg-light fw-bold">
                    <tr>
                        <td colspan="5" class="text-end ps-3 py-3">TOTAL BIAYA BAHAN BAKU TERPAKAI</td>
                        <td class="text-end pe-3 py-3 text-danger fs-5">Rp <?php echo number_format($grandTotal, 2, ',', '.'); ?></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>

        <!-- Penandatangan -->
        <div class="row mt-5 pt-5">
            <div class="col-6 text-center">
                <p class="mb-5">Mengetahui,</p>
                <br><br>
                <p class="mb-0 fw-bold"><u><?php echo $data['penandatangan_1']['nama_user']; ?></u></p>
                <p class="text-muted small"><?php echo $data['penandatangan_1']['jabatan']; ?></p>
            </div>
            <div class="col-6 text-center">
                <p class="mb-0"><?php echo $data['kota_laporan']; ?>, <?php echo date('d F Y'); ?></p>
                <p class="mb-5">Dibuat Oleh,</p>
                <br><br>
                <p class="mb-0 fw-bold"><u><?php echo $data['penandatangan_2']['nama_user']; ?></u></p>
                <p class="text-muted small"><?php echo $data['penandatangan_2']['jabatan']; ?></p>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .sidebar, .topbar, .d-print-none { display: none !important; }
        .main-wrapper { margin-left: 0 !important; }
        .card { box-shadow: none !important; border: 0 !important; }
    }
</style>
