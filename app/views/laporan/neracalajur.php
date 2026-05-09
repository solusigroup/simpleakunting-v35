<div class="row mb-4 no-print">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="<?php echo BASEURL; ?>/laporan/neracaLajur" method="post" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Mulai Tanggal</label>
                        <input type="date" name="tanggal_mulai" class="form-control" value="<?php echo $data['tanggal_mulai']; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Sampai Tanggal</label>
                        <input type="date" name="tanggal_selesai" class="form-control" value="<?php echo $data['tanggal_selesai']; ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill">
                            <i class="bi bi-filter me-2"></i>Tampilkan Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-5">
        <!-- Header Laporan -->
        <div class="text-center mb-5">
            <h4 class="fw-bold mb-0"><?php echo strtoupper($data['perusahaan']['nama_perusahaan']); ?></h4>
            <h5 class="fw-bold mb-0">NERACA LAJUR (WORKSHEET)</h5>
            <p class="text-muted">Periode: <?php echo $data['periode_1']; ?></p>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle text-nowrap" style="font-size: 0.8rem;">
                <thead class="bg-light text-center">
                    <tr>
                        <th rowspan="2" class="align-middle">Kode</th>
                        <th rowspan="2" class="align-middle">Nama Akun</th>
                        <th colspan="2">Neraca Saldo</th>
                        <th colspan="2">Penyesuaian</th>
                        <th colspan="2">NS Setelah Penyesuaian</th>
                        <th colspan="2">Laba Rugi</th>
                        <th colspan="2">Neraca</th>
                    </tr>
                    <tr>
                        <th>Debit</th>
                        <th>Kredit</th>
                        <th>Debit</th>
                        <th>Kredit</th>
                        <th>Debit</th>
                        <th>Kredit</th>
                        <th>Debit</th>
                        <th>Kredit</th>
                        <th>Debit</th>
                        <th>Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totals = array_fill_keys(['sa_d', 'sa_k', 'adj_d', 'adj_k', 'nsd_d', 'nsd_k', 'lr_d', 'lr_k', 'pos_d', 'pos_k'], 0);
                    foreach ($data['laporan'] as $row): 
                        if ($row['nsd_debit'] == 0 && $row['nsd_kredit'] == 0) continue;
                        
                        $totals['sa_d'] += $row['ns_debit'];
                        $totals['sa_k'] += $row['ns_kredit'];
                        $totals['adj_d'] += $row['penyesuaian_debit'];
                        $totals['adj_k'] += $row['penyesuaian_kredit'];
                        $totals['nsd_d'] += $row['nsd_debit'];
                        $totals['nsd_k'] += $row['nsd_kredit'];
                        $totals['lr_d'] += $row['lr_debit'];
                        $totals['lr_k'] += $row['lr_kredit'];
                        $totals['pos_d'] += $row['poskeu_debit'];
                        $totals['pos_k'] += $row['poskeu_kredit'];
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $row['kode_akun']; ?></td>
                        <td><?php echo $row['nama_akun']; ?></td>
                        <td class="text-end"><?php echo number_format($row['ns_debit'], 2); ?></td>
                        <td class="text-end"><?php echo number_format($row['ns_kredit'], 2); ?></td>
                        <td class="text-end"><?php echo number_format($row['penyesuaian_debit'], 2); ?></td>
                        <td class="text-end"><?php echo number_format($row['penyesuaian_kredit'], 2); ?></td>
                        <td class="text-end"><?php echo number_format($row['nsd_debit'], 2); ?></td>
                        <td class="text-end"><?php echo number_format($row['nsd_kredit'], 2); ?></td>
                        <td class="text-end text-primary fw-medium"><?php echo number_format($row['lr_debit'], 2); ?></td>
                        <td class="text-end text-primary fw-medium"><?php echo number_format($row['lr_kredit'], 2); ?></td>
                        <td class="text-end text-success fw-medium"><?php echo number_format($row['poskeu_debit'], 2); ?></td>
                        <td class="text-end text-success fw-medium"><?php echo number_format($row['poskeu_kredit'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-light fw-bold">
                    <tr>
                        <td colspan="2" class="text-center">JUMLAH</td>
                        <td class="text-end"><?php echo number_format($totals['sa_d'], 2); ?></td>
                        <td class="text-end"><?php echo number_format($totals['sa_k'], 2); ?></td>
                        <td class="text-end"><?php echo number_format($totals['adj_d'], 2); ?></td>
                        <td class="text-end"><?php echo number_format($totals['adj_k'], 2); ?></td>
                        <td class="text-end"><?php echo number_format($totals['nsd_d'], 2); ?></td>
                        <td class="text-end"><?php echo number_format($totals['nsd_k'], 2); ?></td>
                        <td class="text-end"><?php echo number_format($totals['lr_d'], 2); ?></td>
                        <td class="text-end"><?php echo number_format($totals['lr_k'], 2); ?></td>
                        <td class="text-end"><?php echo number_format($totals['pos_d'], 2); ?></td>
                        <td class="text-end"><?php echo number_format($totals['pos_k'], 2); ?></td>
                    </tr>
                    <?php 
                        $laba_rugi = $totals['lr_k'] - $totals['lr_d'];
                        $lr_debit_final = $laba_rugi > 0 ? $laba_rugi : 0;
                        $lr_kredit_final = $laba_rugi < 0 ? abs($laba_rugi) : 0;
                        
                        $pos_debit_final = $laba_rugi < 0 ? abs($laba_rugi) : 0;
                        $pos_kredit_final = $laba_rugi > 0 ? $laba_rugi : 0;
                    ?>
                    <tr>
                        <td colspan="8" class="text-end">LABA (RUGI) BERSIH</td>
                        <td class="text-end"><?php echo number_format($lr_debit_final, 2); ?></td>
                        <td class="text-end"><?php echo number_format($lr_kredit_final, 2); ?></td>
                        <td class="text-end"><?php echo number_format($pos_debit_final, 2); ?></td>
                        <td class="text-end"><?php echo number_format($pos_kredit_final, 2); ?></td>
                    </tr>
                    <tr class="table-dark">
                        <td colspan="8" class="text-end text-uppercase">Total Balance</td>
                        <td class="text-end"><?php echo number_format($totals['lr_d'] + $lr_debit_final, 2); ?></td>
                        <td class="text-end"><?php echo number_format($totals['lr_k'] + $lr_kredit_final, 2); ?></td>
                        <td class="text-end"><?php echo number_format($totals['pos_d'] + $pos_debit_final, 2); ?></td>
                        <td class="text-end"><?php echo number_format($totals['pos_k'] + $pos_kredit_final, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Footer Tanda Tangan -->
        <div class="row mt-5 pt-4 text-center">
            <div class="col-4">
                <p class="mb-5"><?php echo $data['kota_laporan']; ?>, <?php echo date('d F Y'); ?></p>
                <p class="mb-0 fw-bold"><?php echo $data['penandatangan_1']['nama_user']; ?></p>
                <p class="text-muted small"><?php echo $data['penandatangan_1']['jabatan']; ?></p>
            </div>
            <div class="col-4"></div>
            <div class="col-4">
                <p class="mb-5">&nbsp;</p>
                <p class="mb-0 fw-bold"><?php echo $data['penandatangan_2']['nama_user']; ?></p>
                <p class="text-muted small"><?php echo $data['penandatangan_2']['jabatan']; ?></p>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
        .main-wrapper { margin-left: 0 !important; padding: 0 !important; }
        .p-5 { padding: 0 !important; }
    }
</style>
