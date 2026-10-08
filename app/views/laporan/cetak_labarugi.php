<?php
$c = $data['perusahaan'] ?? [];
$logoPath = !empty($c['path_logo']) ? BASEURL . '/' . $c['path_logo'] : BASEURL . '/img/logo_jatim.png';
$kota = !empty($data['kota_laporan']) ? $data['kota_laporan'] : ($c['kota_laporan'] ?? 'Mojokerto');
$laporan = $data['laporan'] ?? ['pendapatan' => [], 'beban' => [], 'total_pendapatan_1' => 0, 'total_pendapatan_2' => 0, 'total_beban_1' => 0, 'total_beban_2' => 0];

$isKomparatif = !empty($data['periode_2']);
$totPendapatan1 = (float)($laporan['total_pendapatan_1'] ?? 0);
$totPendapatan2 = (float)($laporan['total_pendapatan_2'] ?? 0);
$totBeban1 = (float)($laporan['total_beban_1'] ?? 0);
$totBeban2 = (float)($laporan['total_beban_2'] ?? 0);
$netLaba1 = $totPendapatan1 - $totBeban1;
$netLaba2 = $totPendapatan2 - $totBeban2;
$npm1 = $totPendapatan1 > 0 ? ($netLaba1 / $totPendapatan1) * 100 : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi - <?php echo htmlspecialchars($c['nama_perusahaan'] ?? 'SimpleAkunting'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>/css/print.css?v=3.6">
</head>
<body class="print-preview-mode">

    <!-- Floating Top Toolbar (Screen Only) -->
    <div class="screen-container no-print">
        <div class="toolbar-print">
            <div class="toolbar-left">
                <a href="<?php echo BASEURL; ?>/laporan/labaRugi" class="btn-toolbar-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Laporan</span>
                </a>
                <div>
                    <div style="font-weight: 800; font-size: 12px; color: #ffffff; display: flex; align-items: center; gap: 8px;">
                        <span>Laporan Laba Rugi Komprehensif</span>
                        <span class="toolbar-badge"><?php echo $isKomparatif ? 'MODE KOMPARATIF' : 'SINGLE PERIODE'; ?></span>
                    </div>
                    <div style="font-size: 11px; color: #94a3b8;">Standar SAK EP / SAK EMKM &bull; Sistem SimpleAkunting</div>
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
                    <span class="kop-badge">SAK EP / EMKM</span>
                    <div class="kop-date">Dicetak: <?php echo date('d/m/Y H:i'); ?> WIB</div>
                </div>
            </header>

            <div class="kop-divider"></div>
            <div class="kop-divider-sub"></div>

            <!-- 2. JUDUL DOKUMEN -->
            <div class="doc-title-block">
                <h2 class="doc-title">LAPORAN LABA RUGI KOMPREHENSIF</h2>
                <div class="doc-subtitle">STANDAR PELAPORAN KEUANGAN ENTITAS BISNIS &amp; BUMDESA</div>
                <div class="doc-period-pill">
                    <i class="fas fa-calendar-alt text-amber-600"></i>
                    <span>Periode: <strong><?php echo htmlspecialchars($data['periode_1'] ?? '-'); ?></strong>
                    <?php if($isKomparatif): ?>
                        <span style="color: #64748b; margin: 0 4px;">vs</span>
                        <strong style="color: #0284c7;"><?php echo htmlspecialchars($data['periode_2']); ?></strong>
                    <?php endif; ?>
                    </span>
                </div>
            </div>

            <!-- 3. KPI RINGKASAN EKSEKUTIF -->
            <div class="kpi-grid">
                <div class="kpi-box">
                    <span class="kpi-label">Total Pendapatan</span>
                    <span class="kpi-value income"><?php echo format_rupiah($totPendapatan1, 0); ?></span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Total Beban</span>
                    <span class="kpi-value expense"><?php echo format_rupiah($totBeban1, 0); ?></span>
                </div>
                <div class="kpi-box highlight">
                    <span class="kpi-label"><?php echo ($netLaba1 >= 0) ? 'Laba Bersih' : 'Rugi Bersih'; ?></span>
                    <span class="kpi-value <?php echo ($netLaba1 >= 0) ? 'income' : 'expense'; ?>">
                        <?php echo format_rupiah($netLaba1, 0); ?>
                    </span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Net Profit Margin</span>
                    <span class="kpi-value font-mono"><?php echo number_format($npm1, 1, ',', '.'); ?>%</span>
                </div>
            </div>

            <!-- 4. TABEL LABA RUGI -->
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 45%;">Pos Akun / Keterangan</th>
                        <th class="text-right" style="width: 25%;">Periode (Rp)<br><span style="font-size: 7pt; font-weight: normal;"><?php echo $data['periode_1']; ?></span></th>
                        <?php if($isKomparatif): ?>
                            <th class="text-right" style="width: 20%;">Pembanding (Rp)<br><span style="font-size: 7pt; font-weight: normal;"><?php echo $data['periode_2']; ?></span></th>
                            <th class="text-right" style="width: 10%;">Selisih (%)</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <!-- BAGIAN I: PENDAPATAN -->
                    <tr class="section-header">
                        <td colspan="<?php echo $isKomparatif ? '4' : '2'; ?>">I. PENDAPATAN OPERASIONAL &amp; USAHA</td>
                    </tr>
                    <?php if (empty($laporan['pendapatan'])): ?>
                    <tr>
                        <td colspan="<?php echo $isKomparatif ? '4' : '2'; ?>" class="text-center" style="color: #64748b; font-style: italic;">Tidak ada pos pendapatan tercatat</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($laporan['pendapatan'] as $item): 
                            $v1 = (float)$item['total_1'];
                            $v2 = (float)($item['total_2'] ?? 0);
                            $selisih = $v1 - $v2;
                            $pct = ($v2 != 0) ? ($selisih / abs($v2)) * 100 : 0;
                        ?>
                        <tr>
                            <td style="padding-left: 20px;"><?php echo htmlspecialchars($item['nama_akun']); ?></td>
                            <td class="text-right font-mono"><?php echo number_format($v1, 0, ',', '.'); ?></td>
                            <?php if($isKomparatif): ?>
                                <td class="text-right font-mono" style="color: #64748b;"><?php echo number_format($v2, 0, ',', '.'); ?></td>
                                <td class="text-right font-mono" style="font-size: 7.5pt; color: <?php echo $selisih >= 0 ? '#047857' : '#b91c1c'; ?>">
                                    <?php echo ($selisih >= 0 ? '+' : '') . number_format($pct, 1, ',', '.') . '%'; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr class="subtotal-row">
                        <td style="text-transform: uppercase;">Subtotal Pendapatan:</td>
                        <td class="text-right font-mono font-bold"><?php echo number_format($totPendapatan1, 0, ',', '.'); ?></td>
                        <?php if($isKomparatif): ?>
                            <td class="text-right font-mono font-bold"><?php echo number_format($totPendapatan2, 0, ',', '.'); ?></td>
                            <td class="text-right font-mono" style="font-size: 7.5pt;">
                                <?php 
                                $diffTotP = $totPendapatan1 - $totPendapatan2;
                                $pctTotP = ($totPendapatan2 != 0) ? ($diffTotP / abs($totPendapatan2)) * 100 : 0;
                                echo ($diffTotP >= 0 ? '+' : '') . number_format($pctTotP, 1, ',', '.') . '%'; 
                                ?>
                            </td>
                        <?php endif; ?>
                    </tr>

                    <!-- BAGIAN II: BEBAN -->
                    <tr class="section-header" style="background-color: #334155;">
                        <td colspan="<?php echo $isKomparatif ? '4' : '2'; ?>">II. BEBAN OPERASIONAL &amp; USAHA</td>
                    </tr>
                    <?php if (empty($laporan['beban'])): ?>
                    <tr>
                        <td colspan="<?php echo $isKomparatif ? '4' : '2'; ?>" class="text-center" style="color: #64748b; font-style: italic;">Tidak ada pos beban tercatat</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($laporan['beban'] as $item): 
                            $v1 = (float)$item['total_1'];
                            $v2 = (float)($item['total_2'] ?? 0);
                            $selisih = $v1 - $v2;
                            $pct = ($v2 != 0) ? ($selisih / abs($v2)) * 100 : 0;
                        ?>
                        <tr>
                            <td style="padding-left: 20px;"><?php echo htmlspecialchars($item['nama_akun']); ?></td>
                            <td class="text-right font-mono"><?php echo number_format($v1, 0, ',', '.'); ?></td>
                            <?php if($isKomparatif): ?>
                                <td class="text-right font-mono" style="color: #64748b;"><?php echo number_format($v2, 0, ',', '.'); ?></td>
                                <td class="text-right font-mono" style="font-size: 7.5pt; color: <?php echo $selisih <= 0 ? '#047857' : '#b91c1c'; ?>">
                                    <?php echo ($selisih >= 0 ? '+' : '') . number_format($pct, 1, ',', '.') . '%'; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr class="subtotal-row">
                        <td style="text-transform: uppercase;">Subtotal Beban:</td>
                        <td class="text-right font-mono font-bold"><?php echo number_format($totBeban1, 0, ',', '.'); ?></td>
                        <?php if($isKomparatif): ?>
                            <td class="text-right font-mono font-bold"><?php echo number_format($totBeban2, 0, ',', '.'); ?></td>
                            <td class="text-right font-mono" style="font-size: 7.5pt;">
                                <?php 
                                $diffTotB = $totBeban1 - $totBeban2;
                                $pctTotB = ($totBeban2 != 0) ? ($diffTotB / abs($totBeban2)) * 100 : 0;
                                echo ($diffTotB >= 0 ? '+' : '') . number_format($pctTotB, 1, ',', '.') . '%'; 
                                ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="grand-total-row">
                        <td style="font-size: 9.5pt;">LABA / (RUGI) BERSIH TAHUN BERJALAN:</td>
                        <td class="text-right font-mono font-bold" style="font-size: 10.5pt; color: <?php echo $netLaba1 >= 0 ? '#047857' : '#b91c1c'; ?>;">
                            <?php echo format_rupiah($netLaba1, 0); ?>
                        </td>
                        <?php if($isKomparatif): ?>
                            <td class="text-right font-mono font-bold" style="font-size: 10.5pt; color: <?php echo $netLaba2 >= 0 ? '#047857' : '#b91c1c'; ?>;">
                                <?php echo format_rupiah($netLaba2, 0); ?>
                            </td>
                            <td class="text-right font-mono font-bold" style="font-size: 8pt;">
                                <?php 
                                $diffNet = $netLaba1 - $netLaba2;
                                $pctNet = ($netLaba2 != 0) ? ($diffNet / abs($netLaba2)) * 100 : 0;
                                echo ($diffNet >= 0 ? '+' : '') . number_format($pctNet, 1, ',', '.') . '%';
                                ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                </tfoot>
            </table>

            <!-- 5. PENGESAHAN / TANDA TANGAN (2 ATAU 3 KOLOM KORPORASI) -->
            <div class="ttd-container">
                <div class="ttd-city-date">
                    <?php echo htmlspecialchars($kota); ?>, <?php echo date('d F Y'); ?>
                </div>
                <div class="ttd-grid cols-2">
                    <div class="ttd-box">
                        <div class="ttd-title">Disiapkan Oleh (Bendahara / Akuntan)</div>
                        <div>
                            <div class="ttd-name"><?php echo htmlspecialchars($data['penandatangan_2']['nama_user'] ?? 'Bendahara'); ?></div>
                            <div class="ttd-role"><?php echo htmlspecialchars($data['penandatangan_2']['jabatan'] ?? 'Bendahara / Akuntan'); ?></div>
                        </div>
                    </div>
                    <div class="ttd-box">
                        <div class="ttd-title">Disetujui Oleh (Pimpinan / Direktur)</div>
                        <div>
                            <div class="ttd-name"><?php echo htmlspecialchars($data['penandatangan_1']['nama_user'] ?? 'Pimpinan'); ?></div>
                            <div class="ttd-role"><?php echo htmlspecialchars($data['penandatangan_1']['jabatan'] ?? 'Direktur Utama'); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 6. FOOTER RESMI KOMPUTERISASI -->
            <footer class="paper-footer">
                <div>
                    Laporan keuangan ini sah dan disajikan otomatis berdasarkan standar SAK EP/EMKM oleh sistem <strong>SimpleAkunting</strong>.
                </div>
                <div class="font-mono">
                    Halaman 1 / 1
                </div>
            </footer>

        </div>
    </div>

</body>
</html>
