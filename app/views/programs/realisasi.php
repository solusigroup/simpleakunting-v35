<?php 
$realisasi = $data['realisasi'];
$program = $realisasi['program'];
$c = $data['perusahaan'] ?? [];
$logoPath = !empty($c['path_logo']) ? BASEURL . '/' . $c['path_logo'] : BASEURL . '/img/logo_jatim.png';
$kota = $c['kota_laporan'] ?? 'Mojokerto';
?>
<div class="container-fluid">
    <!-- Kop Surat Resmi (Print Only) -->
    <div class="d-none d-print-block mb-4">
        <div style="display: flex; align-items: center; gap: 20px; padding-bottom: 12px;">
            <img src="<?php echo $logoPath; ?>" alt="Logo" style="width: 65px; height: 65px; object-fit: contain;" onerror="this.src='<?php echo BASEURL; ?>/img/icon-512.png'">
            <div style="flex-grow: 1;">
                <div style="font-size: 15pt; font-weight: 800; text-transform: uppercase; color: #0f172a; line-height: 1.2;"><?php echo htmlspecialchars($c['nama_perusahaan'] ?? 'KLINIK BUMDESA PROVINSI JAWA TIMUR'); ?></div>
                <div style="font-size: 8.5pt; font-weight: 600; color: #475569; text-transform: uppercase;"><?php echo htmlspecialchars($c['jenis_usaha'] ?? 'Sistem Informasi Akuntansi & Tata Kelola Keuangan'); ?></div>
                <div style="font-size: 8pt; color: #64748b;"><?php echo htmlspecialchars($c['alamat'] ?? 'Jawa Timur, Indonesia'); ?></div>
            </div>
            <div style="text-align: right;">
                <span style="display: inline-block; padding: 3px 8px; font-size: 7.5pt; font-weight: 700; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 4px;">REALISASI PROGRAM</span>
                <div style="font-size: 7.5pt; color: #94a3b8; margin-top: 4px;">Dicetak: <?php echo date('d/m/Y H:i'); ?> WIB</div>
            </div>
        </div>
        <div style="height: 2.5px; background: #0f172a; margin-bottom: 2px;"></div>
        <div style="height: 1px; background: #94a3b8; margin-bottom: 15px;"></div>
        
        <div style="text-align: center; margin-bottom: 18px;">
            <h4 style="font-size: 13pt; font-weight: 800; text-transform: uppercase; margin: 0; color: #0f172a;">LAPORAN REALISASI PENGGUNAAN DANA PROGRAM</h4>
            <div style="font-size: 9pt; font-weight: 700; color: #0284c7; margin-top: 4px;"><?php echo strtoupper(htmlspecialchars($program['nama_program'])); ?></div>
        </div>
    </div>

    <div class="row mb-4 d-print-none">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">Laporan Realisasi Dana</h1>
            <p class="text-muted">Rincian penggunaan dana untuk: <strong><?php echo $program['nama_program']; ?></strong></p>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-dark rounded-pill px-4" onclick="window.print()">
                <i class="fas fa-print me-2"></i> Cetak Laporan
            </button>
            <a href="<?php echo BASEURL; ?>/programs" class="btn btn-outline-secondary rounded-pill px-4">Kembali</a>
        </div>
    </div>

    <div class="row">
        <!-- Summary Cards -->
        <div class="col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Dana Masuk</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?php echo number_format($realisasi['total_income'], 0, ',', '.'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Penggunaan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?php echo number_format($realisasi['total_expense'], 0, ',', '.'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Sisa Saldo Dana</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?php echo number_format($realisasi['total_income'] - $realisasi['total_expense'], 0, ',', '.'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Income Table -->
        <div class="col-md-6 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 font-weight-bold">Rincian Penerimaan Dana</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Akun Pendapatan</th>
                                    <th class="text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($realisasi['income'] as $inc): ?>
                                <tr>
                                    <td><?php echo $inc['nama']; ?></td>
                                    <td class="text-end">Rp <?php echo number_format($inc['jumlah'], 0, ',', '.'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($realisasi['income'])): ?>
                                <tr><td colspan="2" class="text-center text-muted">Belum ada dana masuk.</td></tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="fw-bold bg-light">
                                <tr>
                                    <td>TOTAL</td>
                                    <td class="text-end">Rp <?php echo number_format($realisasi['total_income'], 0, ',', '.'); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expense Table -->
        <div class="col-md-6 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-danger text-white py-3">
                    <h6 class="m-0 font-weight-bold">Rincian Penggunaan Dana</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Akun Biaya/Belanja</th>
                                    <th class="text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($realisasi['expense'] as $exp): ?>
                                <tr>
                                    <td><?php echo $exp['nama']; ?></td>
                                    <td class="text-end">Rp <?php echo number_format($exp['jumlah'], 0, ',', '.'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($realisasi['expense'])): ?>
                                <tr><td colspan="2" class="text-center text-muted">Belum ada penggunaan dana.</td></tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="fw-bold bg-light">
                                <tr>
                                    <td>TOTAL</td>
                                    <td class="text-end">Rp <?php echo number_format($realisasi['total_expense'], 0, ',', '.'); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Tanda Tangan Formal Saat Cetak -->
    <div class="d-none d-print-block mt-5 pt-4">
        <div style="display: flex; justify-content: space-between; text-align: center; page-break-inside: avoid;">
            <div style="width: 250px;">
                <p style="margin: 0; font-size: 9pt; color: #475569;">Mengetahui,</p>
                <p style="margin: 2px 0 0 0; font-weight: 700; font-size: 9.5pt; color: #0f172a;">Pimpinan / Direktur</p>
                <div style="height: 65px;"></div>
                <p style="margin: 0; font-weight: 700; text-decoration: underline; font-size: 9.5pt; color: #0f172a;">( ............................................ )</p>
                <p style="margin: 2px 0 0 0; font-size: 8pt; color: #64748b;">Penanggung Jawab Program</p>
            </div>
            <div style="width: 250px;">
                <p style="margin: 0; font-size: 9pt; color: #475569;"><?php echo htmlspecialchars($kota); ?>, <?php echo tanggal_indo(date('Y-m-d')); ?></p>
                <p style="margin: 2px 0 0 0; font-weight: 700; font-size: 9.5pt; color: #0f172a;">Bendahara / Manajer Program</p>
                <div style="height: 65px;"></div>
                <p style="margin: 0; font-weight: 700; text-decoration: underline; font-size: 9.5pt; color: #0f172a;">( ............................................ )</p>
                <p style="margin: 2px 0 0 0; font-size: 8pt; color: #64748b;">Penyusun Laporan</p>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .sidebar, .navbar, .topbar, .d-print-none { display: none !important; }
    .main-wrapper { margin-left: 0 !important; }
    .card { border: 1px solid #cbd5e1 !important; box-shadow: none !important; margin-bottom: 20px !important; }
    .card-header { background-color: #f8fafc !important; }
    .table-bordered th, .table-bordered td { border: 1px solid #94a3b8 !important; font-size: 8.5pt !important; }
    .container-fluid { padding: 0 !important; }
}
.border-left-primary { border-left: .25rem solid #4e73df!important; }
.border-left-success { border-left: .25rem solid #1cc88a!important; }
.border-left-info { border-left: .25rem solid #36b9cc!important; }
.border-left-danger { border-left: .25rem solid #e74a3b!important; }
</style>
