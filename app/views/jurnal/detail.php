<?php $jurnal = $data['jurnal']; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Detail Jurnal Umum</h3>
        <p class="text-muted small mb-0">Rincian pencatatan akuntansi untuk nomor transaksi <strong><?php echo $jurnal['no_transaksi']; ?></strong></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo BASEURL; ?>/jurnal/cetak/<?php echo $jurnal['id_jurnal']; ?>" target="_blank" class="btn btn-dark shadow-sm fw-bold">
            <i class="bi bi-printer me-2"></i>Cetak Voucher Jurnal
        </a>
        <a href="<?php echo BASEURL; ?>/jurnal" class="btn btn-outline-secondary shadow-sm">Kembali</a>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">Informasi Header</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small d-block">Nomor Transaksi</label>
                    <span class="fw-bold fs-5 text-primary"><?php echo $jurnal['no_transaksi']; ?></span>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Tanggal</label>
                    <span class="fw-medium"><?php echo date('d F Y', strtotime($jurnal['tanggal'])); ?></span>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Sumber Jurnal</label>
                    <span class="badge rounded-pill text-bg-info"><?php echo $jurnal['sumber_jurnal']; ?></span>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Status</label>
                    <?php if ($jurnal['is_locked'] == 1): ?>
                        <span class="badge rounded-pill text-bg-secondary"><i class="bi bi-lock-fill me-1"></i> Terkunci (Sistem)</span>
                    <?php else: ?>
                        <span class="badge rounded-pill text-bg-success">Dapat Diubah</span>
                    <?php endif; ?>
                </div>
                <div class="mb-0">
                    <label class="text-muted small d-block">Keterangan / Deskripsi</label>
                    <p class="mb-0 fw-medium"><?php echo htmlspecialchars($jurnal['deskripsi'] ?? '-'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">Rincian Pos Jurnal (Debit & Kredit)</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Kode Akun</th>
                                <th>Nama Akun</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end pe-4">Kredit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalDebit = 0;
                            $totalKredit = 0;
                            foreach ($jurnal['details'] as $detail): 
                                $totalDebit += $detail['debit'];
                                $totalKredit += $detail['kredit'];
                            ?>
                            <tr>
                                <td class="ps-4 fw-bold text-primary"><?php echo $detail['kode_akun']; ?></td>
                                <td><?php echo htmlspecialchars($detail['nama_akun'] ?? '-'); ?></td>
                                <td class="text-end"><?php echo number_format($detail['debit'], 2, ',', '.'); ?></td>
                                <td class="text-end pe-4"><?php echo number_format($detail['kredit'], 2, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="2" class="text-center ps-4">TOTAL</td>
                                <td class="text-end">Rp <?php echo number_format($totalDebit, 2, ',', '.'); ?></td>
                                <td class="text-end pe-4">Rp <?php echo number_format($totalKredit, 2, ',', '.'); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php if ($jurnal['is_locked'] == 0): ?>
            <div class="card-footer bg-white py-3 text-end">
                <a href="<?php echo BASEURL; ?>/jurnal/edit/<?php echo $jurnal['id_jurnal']; ?>" class="btn btn-warning px-4 rounded-pill">
                    <i class="bi bi-pencil me-2"></i>Edit Jurnal
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .sidebar, .navbar, .mobile-bottom-nav { display: none !important; }
    .card { border: 1px solid #eee !important; box-shadow: none !important; }
    .main-wrapper { margin-left: 0 !important; }
}
</style>
