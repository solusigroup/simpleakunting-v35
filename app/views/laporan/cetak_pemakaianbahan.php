<?php
$c = $data['perusahaan'] ?? [];
$logoPath = !empty($c['path_logo']) ? BASEURL . '/' . $c['path_logo'] : BASEURL . '/img/logo_jatim.png';
$kota = !empty($data['kota_laporan']) ? $data['kota_laporan'] : ($c['kota_laporan'] ?? 'Mojokerto');
$lap = $data['laporan'] ?? [];

$totalBiaya = 0;
$totalQty = 0;
$itemCount = count($lap);
foreach ($lap as $row) {
    $totalBiaya += (float)($row['total_biaya'] ?? 0);
    $totalQty += (float)($row['qty_dipakai'] ?? 0);
}
$rataBiaya = $itemCount > 0 ? $totalBiaya / $itemCount : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pemakaian Bahan Baku - <?php echo htmlspecialchars($c['nama_perusahaan'] ?? 'SimpleAkunting'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>/css/print.css?v=3.6">
</head>
<body class="print-preview-mode">

    <!-- Floating Top Toolbar (Screen Only) -->
    <div class="screen-container no-print">
        <div class="toolbar-print">
            <div class="toolbar-left">
                <a href="<?php echo BASEURL; ?>/laporan/pemakaianBahan" class="btn-toolbar-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Laporan</span>
                </a>
                <div>
                    <div style="font-weight: 800; font-size: 12px; color: #ffffff; display: flex; align-items: center; gap: 8px;">
                        <span>Laporan Pemakaian Bahan Baku Manufaktur</span>
                        <span class="toolbar-badge"><?php echo $itemCount; ?> TRANSAKSI</span>
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
                    <span class="kop-badge">MANUFAKTUR</span>
                    <div class="kop-date">Dicetak: <?php echo date('d/m/Y H:i'); ?> WIB</div>
                </div>
            </header>

            <div class="kop-divider"></div>
            <div class="kop-divider-sub"></div>

            <!-- 2. JUDUL DOKUMEN -->
            <div class="doc-title-block">
                <h2 class="doc-title">LAPORAN PEMAKAIAN BAHAN BAKU</h2>
                <div class="doc-subtitle">REKAPITULASI BIAYA BAHAN BAKU TERPAKAI DALAM PROSES PRODUKSI</div>
                <div class="doc-period-pill">
                    <i class="fas fa-calendar-alt text-amber-600"></i>
                    <span>Periode: <strong><?php echo htmlspecialchars($data['periode_1'] ?? '-'); ?></strong></span>
                </div>
            </div>

            <!-- 3. KPI RINGKASAN EKSEKUTIF -->
            <div class="kpi-grid">
                <div class="kpi-box">
                    <span class="kpi-label">Jumlah Transaksi</span>
                    <span class="kpi-value font-mono"><?php echo $itemCount; ?> Transaksi</span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Total Kuantitas Terpakai</span>
                    <span class="kpi-value font-mono"><?php echo number_format($totalQty, 2, ',', '.'); ?> Unit</span>
                </div>
                <div class="kpi-box highlight">
                    <span class="kpi-label">Total Biaya Bahan Baku</span>
                    <span class="kpi-value expense"><?php echo format_rupiah($totalBiaya, 0); ?></span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Rata-rata Biaya / Catatan</span>
                    <span class="kpi-value font-mono"><?php echo format_rupiah($rataBiaya, 0); ?></span>
                </div>
            </div>

            <!-- 4. TABEL PEMAKAIAN BAHAN -->
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 12%;">Tanggal</th>
                        <th style="width: 18%;">No. Produksi</th>
                        <th style="width: 32%;">Bahan Baku</th>
                        <th class="text-center" style="width: 14%;">Qty Dipakai</th>
                        <th style="width: 10%;">Satuan</th>
                        <th class="text-right" style="width: 14%;">Total Biaya (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lap)): ?>
                    <tr>
                        <td colspan="6" class="text-center" style="color: #64748b; font-style: italic; padding: 25px;">
                            Tidak ada data pemakaian bahan baku pada periode ini.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($lap as $row): 
                            $biaya = (float)($row['total_biaya'] ?? 0);
                            $qty = (float)($row['qty_dipakai'] ?? 0);
                        ?>
                        <tr>
                            <td class="font-mono"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                            <td class="font-mono font-bold text-slate-700"><?php echo htmlspecialchars($row['no_produksi']); ?></td>
                            <td>
                                <div style="font-weight: 700; color: #0f172a;"><?php echo htmlspecialchars($row['nama_bahan']); ?></div>
                                <div class="font-mono text-muted" style="font-size: 7.5pt;"><?php echo htmlspecialchars($row['kode_barang']); ?></div>
                            </td>
                            <td class="text-center font-mono font-bold"><?php echo number_format($qty, 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($row['satuan'] ?? '-'); ?></td>
                            <td class="text-right font-mono font-bold text-slate-800">
                                <?php echo number_format($biaya, 0, ',', '.'); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- GRAND TOTAL -->
                    <tr class="grand-total-row">
                        <td colspan="3" class="text-right">TOTAL BIAYA BAHAN BAKU TERPAKAI</td>
                        <td class="text-center font-mono"><?php echo number_format($totalQty, 2, ',', '.'); ?></td>
                        <td>-</td>
                        <td class="text-right font-mono"><?php echo number_format($totalBiaya, 0, ',', '.'); ?></td>
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
                    <p class="sig-post">Penyusun Laporan Bahan</p>
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
