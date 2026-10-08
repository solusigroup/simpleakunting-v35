<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Laporan Aset Biologis (PSAK 241)</h3>
        <p class="text-muted mb-0 small">Rekonsiliasi Nilai Tercatat (IAS 41.50) &amp; Klasifikasi Aset Agrikultur</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo BASEURL; ?>/asetbiologis" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form id="laporan-form" action="<?php echo BASEURL; ?>/asetbiologis/laporan" method="get" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold small text-muted text-uppercase">Periode Mulai</label>
                <input type="date" name="mulai" id="mulai" class="form-control rounded-3" value="<?php echo htmlspecialchars($data['tanggal_mulai']); ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold small text-muted text-uppercase">Periode Selesai</label>
                <input type="date" name="selesai" id="selesai" class="form-control rounded-3" value="<?php echo htmlspecialchars($data['tanggal_selesai']); ?>" required>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 flex-grow-1">
                        <i class="bi bi-filter me-2"></i>Tampilkan
                    </button>
                    <button type="button" id="btn-cetak-estetik" class="btn btn-dark rounded-pill px-4 text-nowrap" title="Cetak Dokumen Estetik (PBS-ERP)">
                        <i class="fas fa-print me-1"></i> Cetak
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (isset($data['rekonsiliasi'])): ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-1">Rekonsiliasi Nilai Tercatat (IAS 41.50 / PSAK 241)</h5>
            <p class="text-muted small mb-0">Periode: <?php echo htmlspecialchars($data['periode_1']); ?></p>
        </div>
        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-semibold">
            PSAK 241 / IAS 41
        </span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-center align-middle">
                        <th rowspan="2">Kategori</th>
                        <th rowspan="2">Saldo Awal</th>
                        <th colspan="2">Penambahan</th>
                        <th colspan="2">Penyesuaian Nilai Wajar</th>
                        <th colspan="3">Pengurangan</th>
                        <th rowspan="2">Saldo Akhir</th>
                    </tr>
                    <tr class="text-center">
                        <th>Pembelian</th>
                        <th>Kelahiran/Pertumbuhan</th>
                        <th>Keuntungan</th>
                        <th>Kerugian</th>
                        <th>Panen</th>
                        <th>Penjualan</th>
                        <th>Kematian/Hapus</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $tot_saldo_awal = 0;
                    $tot_pembelian = 0;
                    $tot_kelahiran = 0;
                    $tot_keuntungan_fv = 0;
                    $tot_kerugian_fv = 0;
                    $tot_panen = 0;
                    $tot_penjualan = 0;
                    $tot_kematian = 0;
                    $tot_saldo_akhir = 0;

                    foreach ($data['rekonsiliasi'] as $kat => $row): 
                        $tot_saldo_awal += (float)($row['saldo_awal'] ?? 0);
                        $tot_pembelian += (float)($row['pembelian'] ?? 0);
                        $tot_kelahiran += (float)($row['kelahiran'] ?? 0);
                        $tot_keuntungan_fv += (float)($row['keuntungan_fv'] ?? 0);
                        $tot_kerugian_fv += (float)($row['kerugian_fv'] ?? 0);
                        $tot_panen += (float)($row['panen'] ?? 0);
                        $tot_penjualan += (float)($row['penjualan'] ?? 0);
                        $tot_kematian += (float)($row['kematian'] ?? 0);
                        $tot_saldo_akhir += (float)($row['saldo_akhir'] ?? 0);
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><?php echo htmlspecialchars($kat); ?></td>
                        <td class="text-end font-monospace">Rp <?php echo number_format($row['saldo_awal'] ?? 0, 2, ',', '.'); ?></td>
                        <td class="text-end font-monospace">Rp <?php echo number_format($row['pembelian'] ?? 0, 2, ',', '.'); ?></td>
                        <td class="text-end font-monospace">Rp <?php echo number_format($row['kelahiran'] ?? 0, 2, ',', '.'); ?></td>
                        <td class="text-end font-monospace text-success"><?php echo ($row['keuntungan_fv'] > 0 ? '+' : ''); ?>Rp <?php echo number_format($row['keuntungan_fv'] ?? 0, 2, ',', '.'); ?></td>
                        <td class="text-end font-monospace text-danger"><?php echo ($row['kerugian_fv'] > 0 ? '-' : ''); ?>Rp <?php echo number_format($row['kerugian_fv'] ?? 0, 2, ',', '.'); ?></td>
                        <td class="text-end font-monospace text-danger"><?php echo ($row['panen'] > 0 ? '-' : ''); ?>Rp <?php echo number_format($row['panen'] ?? 0, 2, ',', '.'); ?></td>
                        <td class="text-end font-monospace text-danger"><?php echo ($row['penjualan'] > 0 ? '-' : ''); ?>Rp <?php echo number_format($row['penjualan'] ?? 0, 2, ',', '.'); ?></td>
                        <td class="text-end font-monospace text-danger"><?php echo ($row['kematian'] > 0 ? '-' : ''); ?>Rp <?php echo number_format($row['kematian'] ?? 0, 2, ',', '.'); ?></td>
                        <td class="text-end font-monospace fw-bold text-dark">Rp <?php echo number_format($row['saldo_akhir'] ?? 0, 2, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td>TOTAL KESELURUHAN</td>
                        <td class="text-end font-monospace">Rp <?php echo number_format($tot_saldo_awal, 2, ',', '.'); ?></td>
                        <td class="text-end font-monospace">Rp <?php echo number_format($tot_pembelian, 2, ',', '.'); ?></td>
                        <td class="text-end font-monospace">Rp <?php echo number_format($tot_kelahiran, 2, ',', '.'); ?></td>
                        <td class="text-end font-monospace text-success">+Rp <?php echo number_format($tot_keuntungan_fv, 2, ',', '.'); ?></td>
                        <td class="text-end font-monospace text-danger">-Rp <?php echo number_format($tot_kerugian_fv, 2, ',', '.'); ?></td>
                        <td class="text-end font-monospace text-danger">-Rp <?php echo number_format($tot_panen, 2, ',', '.'); ?></td>
                        <td class="text-end font-monospace text-danger">-Rp <?php echo number_format($tot_penjualan, 2, ',', '.'); ?></td>
                        <td class="text-end font-monospace text-danger">-Rp <?php echo number_format($tot_kematian, 2, ',', '.'); ?></td>
                        <td class="text-end font-monospace text-primary fs-6">Rp <?php echo number_format($tot_saldo_akhir, 2, ',', '.'); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 pt-4 pb-0">
        <h5 class="fw-bold mb-0">Ringkasan Klasifikasi Aset Aktif</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Kategori</th>
                        <th>Status Kematangan</th>
                        <th class="text-center">Jumlah Aset (Populasi)</th>
                        <th class="text-end">Total Nilai Tercatat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_aset = 0;
                    $total_nilai = 0;
                    foreach ($data['ringkasan'] ?? [] as $row): 
                        $total_aset += (int)$row['jumlah'];
                        $total_nilai += (float)$row['total_nilai'];
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><?php echo htmlspecialchars($row['kategori']); ?></td>
                        <td>
                            <span class="badge <?php echo ($row['status_kematangan'] == 'Menghasilkan') ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'; ?> rounded-pill px-3 py-1">
                                <?php echo htmlspecialchars($row['status_kematangan']); ?>
                            </span>
                        </td>
                        <td class="text-center font-monospace fw-bold"><?php echo number_format($row['jumlah'], 0, ',', '.'); ?></td>
                        <td class="text-end font-monospace fw-bold">Rp <?php echo number_format($row['total_nilai'], 2, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="table-light fw-bold">
                        <td colspan="2" class="text-end">TOTAL</td>
                        <td class="text-center font-monospace"><?php echo number_format($total_aset, 0, ',', '.'); ?></td>
                        <td class="text-end font-monospace text-success fs-6">Rp <?php echo number_format($total_nilai, 2, ',', '.'); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="text-end mb-5">
    <button type="button" id="btn-cetak-bottom" class="btn btn-dark rounded-pill px-4 shadow-sm" title="Cetak Dokumen Estetik (PBS-ERP)">
        <i class="fas fa-print me-2"></i>Cetak Dokumen (Estetik)
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('laporan-form');
        const cetakEstetikBtn = document.getElementById('btn-cetak-estetik');
        const cetakBottomBtn = document.getElementById('btn-cetak-bottom');

        function triggerCetakEstetik() {
            const originalAction = form.action;
            const originalTarget = form.target;
            
            form.action = "<?php echo BASEURL; ?>/asetbiologis/cetak";
            form.target = "_blank";
            form.submit();
            
            form.action = originalAction;
            form.target = originalTarget;
        }

        if (cetakEstetikBtn) {
            cetakEstetikBtn.addEventListener('click', triggerCetakEstetik);
        }
        if (cetakBottomBtn) {
            cetakBottomBtn.addEventListener('click', triggerCetakEstetik);
        }
    });
</script>
