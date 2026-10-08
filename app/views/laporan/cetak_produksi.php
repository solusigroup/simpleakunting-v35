<?php
$c = $data['perusahaan'] ?? [];
$logoPath = !empty($c['path_logo']) ? BASEURL . '/' . $c['path_logo'] : BASEURL . '/img/logo_jatim.png';
$kota = !empty($data['kota_laporan']) ? $data['kota_laporan'] : ($c['kota_laporan'] ?? 'Mojokerto');
$lap = $data['laporan'] ?? [];

$totalHpp = 0;
$totalTarget = 0;
$countSelesai = 0;
$totalBatch = count($lap);
foreach ($lap as $row) {
    $totalHpp += (float)($row['total_biaya_aktual'] ?? 0);
    $totalTarget += (float)($row['jumlah_target'] ?? 0);
    if (($row['status'] ?? '') == 'Selesai') {
        $countSelesai++;
    }
}
$pctSelesai = $totalBatch > 0 ? ($countSelesai / $totalBatch) * 100 : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Aktivitas Produksi - <?php echo htmlspecialchars($c['nama_perusahaan'] ?? 'SimpleAkunting'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>/css/print.css?v=3.6">
</head>
<body class="print-preview-mode">

    <!-- Floating Top Toolbar (Screen Only) -->
    <div class="screen-container no-print">
        <div class="toolbar-print">
            <div class="toolbar-left">
                <a href="<?php echo BASEURL; ?>/laporan/produksi" class="btn-toolbar-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Laporan</span>
                </a>
                <div>
                    <div style="font-weight: 800; font-size: 12px; color: #ffffff; display: flex; align-items: center; gap: 8px;">
                        <span>Laporan Aktivitas Produksi (Work Orders)</span>
                        <span class="toolbar-badge"><?php echo $totalBatch; ?> BATCH PRODUKSI</span>
                    </div>
                    <div style="font-size: 11px; color: #94a3b8;">Modul Manufaktur &amp; HPP &bull; Sistem SimpleAkunting</div>
                </div>
            </div>
            <div class="toolbar-right">
                <button onclick="window.print()" class="btn-toolbar-print">
                    <i class="fas fa-print"></i>
                    <span>Cetak Dokumen / Simpan PDF</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Paper Canvas Sheet -->
    <div class="screen-container">
        <div class="paper-canvas">
            
            <!-- 1. KOP SURAT RESMI KORPORASI -->
            <header class="kop-header">
                <div class="kop-brand">
                    <img src="<?php echo $logoPath; ?>" alt="Logo" class="kop-logo" onerror="this.src='<?php echo BASEURL; ?>/img/icon-512.png'">
                    <div>
                        <h1 class="kop-title"><?php echo htmlspecialchars($c['nama_perusahaan'] ?? 'KLINIK BUMDESA PROVINSI JAWA TIMUR'); ?></h1>
                        <p class="kop-sub"><?php echo htmlspecialchars($c['jenis_usaha'] ?? 'Sistem Informasi Akuntansi & Tata Kelola Keuangan'); ?></p>
                        <p class="kop-desc"><?php echo htmlspecialchars($c['alamat'] ?? 'Jawa Timur, Indonesia'); ?></p>
                        <p class="kop-meta">
                            <?php if(!empty($c['telepon'])): ?><span><strong>Telp:</strong> <?php echo htmlspecialchars($c['telepon']); ?></span> &bull; <?php endif; ?>
                            <?php if(!empty($c['email'])): ?><span><strong>Email:</strong> <?php echo htmlspecialchars($c['email']); ?></span> &bull; <?php endif; ?>
                            <span><strong>Kota:</strong> <?php echo htmlspecialchars($kota); ?></span>
                        </p>
                    </div>
                </div>
                <div class="kop-badge-box">
                    <span class="kop-badge">PRODUKSI</span>
                    <div class="kop-date">Dicetak: <?php echo date('d/m/Y H:i'); ?> WIB</div>
                </div>
            </header>

            <div class="kop-divider"></div>
            <div class="kop-divider-sub"></div>

            <!-- 2. JUDUL DOKUMEN -->
            <div class="doc-title-block">
                <h2 class="doc-title">LAPORAN AKTIVITAS PRODUKSI</h2>
                <div class="doc-subtitle">REKAPITULASI ORDER PRODUKSI (SPK) DAN REALISASI HARGA POKOK PRODUKSI (HPP)</div>
                <div class="doc-period-pill">
                    <i class="fas fa-calendar-alt text-amber-600"></i>
                    <span>Periode: <strong><?php echo htmlspecialchars($data['periode_1'] ?? '-'); ?></strong></span>
                </div>
            </div>

            <!-- 3. KPI RINGKASAN EKSEKUTIF -->
            <div class="kpi-grid">
                <div class="kpi-box">
                    <span class="kpi-label">Total Batch Produksi</span>
                    <span class="kpi-value font-mono"><?php echo $totalBatch; ?> Order</span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Total Target Output</span>
                    <span class="kpi-value font-mono"><?php echo number_format($totalTarget, 2, ',', '.'); ?> Unit</span>
                </div>
                <div class="kpi-box highlight">
                    <span class="kpi-label">Total Biaya Aktual (HPP)</span>
                    <span class="kpi-value expense"><?php echo format_rupiah($totalHpp, 0); ?></span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Penyelesaian (Selesai)</span>
                    <span class="kpi-value income font-mono"><?php echo $countSelesai; ?> (<?php echo number_format($pctSelesai, 0); ?>%)</span>
                </div>
            </div>

            <!-- 4. TABEL AKTIVITAS PRODUKSI -->
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">No. Produksi</th>
                        <th style="width: 12%;">Tanggal</th>
                        <th style="width: 33%;">Produk Jadi &amp; Resep (BOM)</th>
                        <th class="text-center" style="width: 12%;">Target Qty</th>
                        <th class="text-right" style="width: 16%;">Biaya Aktual (HPP)</th>
                        <th class="text-center" style="width: 12%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lap)): ?>
                    <tr>
                        <td colspan="6" class="text-center" style="color: #64748b; font-style: italic; padding: 25px;">
                            Tidak ada aktivitas produksi pada periode ini.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($lap as $row): 
                            $biaya = (float)($row['total_biaya_aktual'] ?? 0);
                            $target = (float)($row['jumlah_target'] ?? 0);
                            $isDone = ($row['status'] == 'Selesai');
                        ?>
                        <tr>
                            <td class="font-mono font-bold text-slate-800"><?php echo htmlspecialchars($row['no_produksi']); ?></td>
                            <td class="font-mono"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                            <td>
                                <div style="font-weight: 700; color: #0f172a;"><?php echo htmlspecialchars($row['nama_produk']); ?></div>
                                <div class="text-muted" style="font-size: 7.5pt;"><?php echo htmlspecialchars($row['nama_bom']); ?></div>
                            </td>
                            <td class="text-center font-mono font-bold"><?php echo number_format($target, 2, ',', '.'); ?></td>
                            <td class="text-right font-mono font-bold text-slate-800">
                                <?php echo $biaya > 0 ? number_format($biaya, 0, ',', '.') : '-'; ?>
                            </td>
                            <td class="text-center">
                                <span class="toolbar-badge" style="display: inline-block; padding: 2px 8px; font-size: 7pt; background-color: <?php echo $isDone ? '#ecfdf5' : '#fffbeb'; ?>; color: <?php echo $isDone ? '#065f46' : '#92400e'; ?>; border: 1px solid <?php echo $isDone ? '#a7f3d0' : '#fde68a'; ?>;">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- GRAND TOTAL -->
                    <tr class="grand-total-row">
                        <td colspan="3" class="text-right">TOTAL REALISASI HPP PRODUKSI</td>
                        <td class="text-center font-mono"><?php echo number_format($totalTarget, 2, ',', '.'); ?></td>
                        <td class="text-right font-mono"><?php echo number_format($totalHpp, 0, ',', '.'); ?></td>
                        <td class="text-center">-</td>
                    </tr>
                </tbody>
            </table>

            <!-- PENGESAHAN / TANDA TANGAN FORMAL -->
            <section class="signatures-section">
                <div class="sig-col">
                    <p class="sig-title"><?php echo htmlspecialchars($data['penandatangan_1']['jabatan'] ?? 'Pimpinan / Direktur'); ?></p>
                    <div class="sig-space"></div>
                    <p class="sig-name"><?php echo htmlspecialchars($data['penandatangan_1']['nama_user'] ?? 'Pimpinan'); ?></p>
                    <p class="sig-post">Penanggung Jawab Operasional</p>
                </div>
                <div class="sig-col">
                    <p class="sig-title"><?php echo htmlspecialchars($kota); ?>, <?php echo tanggal_indo(date('Y-m-d')); ?></p>
                    <p class="sig-title" style="margin-top: 2px;"><?php echo htmlspecialchars($data['penandatangan_2']['jabatan'] ?? 'Kepala Produksi / Akuntan'); ?></p>
                    <div class="sig-space"></div>
                    <p class="sig-name"><?php echo htmlspecialchars($data['penandatangan_2']['nama_user'] ?? 'Kepala Produksi'); ?></p>
                    <p class="sig-post">Penyusun Laporan Produksi</p>
                </div>
            </section>

            <!-- FOOTER RESMI DOKUMEN -->
            <footer class="doc-footer">
                <div class="doc-footer-left">
                    <span>Dokumen Resmi Sistem Akuntansi &bull; Dicetak secara digital</span>
                </div>
                <div class="doc-footer-right">
                    <span>Halaman 1 dari 1</span>
                </div>
            </footer>

        </div>
    </div>

</body>
</html>
