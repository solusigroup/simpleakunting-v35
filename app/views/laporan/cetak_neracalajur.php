<?php
$c = $data['perusahaan'] ?? [];
$logoPath = !empty($c['path_logo']) ? BASEURL . '/' . $c['path_logo'] : BASEURL . '/img/logo_jatim.png';
$kota = !empty($data['kota_laporan']) ? $data['kota_laporan'] : ($c['kota_laporan'] ?? 'Mojokerto');
$lap = $data['laporan'] ?? [];

$totals = array_fill_keys([
    'sa_debit', 'sa_kredit', 
    'mutasi_debit', 'mutasi_kredit', 
    'ns_debit', 'ns_kredit', 
    'penyesuaian_debit', 'penyesuaian_kredit', 
    'nsd_debit', 'nsd_kredit', 
    'lr_debit', 'lr_kredit', 
    'poskeu_debit', 'poskeu_kredit'
], 0);

if (!empty($lap)) {
    foreach ($lap as $row) {
        foreach ($totals as $key => &$tot) {
            $tot += (float)($row[$key] ?? 0);
        }
    }
}

// Laba Bersih = LR Kredit - LR Debit
$labaBersih = $totals['lr_kredit'] - $totals['lr_debit'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neraca Lajur 10 Kolom - <?php echo htmlspecialchars($c['nama_perusahaan'] ?? 'SimpleAkunting'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>/css/print.css?v=3.6">
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 6mm;
        }
        .screen-container.landscape {
            max-width: 1400px;
        }
        .landscape-canvas {
            width: 100%;
            max-width: 1400px;
            padding: 24px;
        }
        .tbl-lajur {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
        }
        .tbl-lajur th, .tbl-lajur td {
            border: 1px solid #cbd5e1;
            padding: 3px 4px;
            vertical-align: middle;
        }
        .tbl-lajur thead th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 700;
            text-align: center;
            font-size: 7pt;
        }
        .tbl-lajur thead tr.sub-th th {
            background-color: #1e293b;
            color: #f1f5f9;
            font-size: 6.5pt;
        }
        .tbl-lajur tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .tbl-lajur tfoot td {
            font-weight: 700;
            background-color: #f1f5f9;
            border-top: 2px solid #0f172a;
        }
        .tbl-lajur tfoot tr.tfoot-balance td {
            background-color: #e2e8f0;
            border-bottom: 2.5px double #0f172a;
        }
        @media print {
            body {
                background: white !important;
            }
            .screen-container.landscape {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .landscape-canvas {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
            .tbl-lajur th, .tbl-lajur td {
                font-size: 6.5pt !important;
                padding: 2px 3px !important;
            }
            .tbl-lajur thead th {
                background-color: #e2e8f0 !important;
                color: #000 !important;
            }
            .tbl-lajur thead tr.sub-th th {
                background-color: #f1f5f9 !important;
                color: #000 !important;
            }
        }
    </style>
</head>
<body class="print-preview-mode">

    <!-- Floating Top Toolbar (Screen Only) -->
    <div class="screen-container landscape no-print">
        <div class="toolbar-print">
            <div class="toolbar-left">
                <a href="<?php echo BASEURL; ?>/laporan/neracaLajur" class="btn-toolbar-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Laporan</span>
                </a>
                <div>
                    <div style="font-weight: 800; font-size: 12px; color: #ffffff; display: flex; align-items: center; gap: 8px;">
                        <span>Kertas Kerja Neraca Lajur (10 Kolom)</span>
                        <span class="toolbar-badge">A4 LANDSCAPE</span>
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
    <div class="screen-container landscape">
        <div class="paper-canvas landscape-canvas">
            
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
                    <span class="kop-badge">NERACA LAJUR</span>
                    <div class="kop-date">Dicetak: <?php echo date('d/m/Y H:i'); ?> WIB</div>
                </div>
            </header>

            <div class="kop-divider"></div>
            <div class="kop-divider-sub"></div>

            <!-- 2. JUDUL DOKUMEN -->
            <div class="doc-title-block">
                <h2 class="doc-title">KERTAS KERJA NERACA LAJUR (WORKSHEET)</h2>
                <div class="doc-subtitle">10 KOLOM SIKLUS AKUNTANSI KEUANGAN LENGKAP &bull; SAK EP / EMKM</div>
                <div class="doc-period-pill">
                    <i class="fas fa-calendar-alt text-amber-600"></i>
                    <span>Periode: <strong><?php echo htmlspecialchars($data['periode_1'] ?? '-'); ?></strong></span>
                </div>
            </div>

            <!-- 3. KPI RINGKASAN EKSEKUTIF -->
            <div class="kpi-grid">
                <div class="kpi-box">
                    <span class="kpi-label">Total Saldo Awal (D)</span>
                    <span class="kpi-value font-mono"><?php echo format_rupiah($totals['sa_debit'], 0); ?></span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Total Mutasi Periode</span>
                    <span class="kpi-value font-mono"><?php echo format_rupiah($totals['mutasi_debit'], 0); ?></span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Total NS Disesuaikan</span>
                    <span class="kpi-value font-mono"><?php echo format_rupiah($totals['nsd_debit'], 0); ?></span>
                </div>
                <div class="kpi-box highlight">
                    <span class="kpi-label"><?php echo $labaBersih >= 0 ? 'Laba Bersih Berjalan' : 'Rugi Bersih Berjalan'; ?></span>
                    <span class="kpi-value <?php echo $labaBersih >= 0 ? 'income' : 'expense'; ?>">
                        <?php echo format_rupiah($labaBersih, 0); ?>
                    </span>
                </div>
            </div>

            <!-- 4. TABEL 10 KOLOM -->
            <div style="overflow-x: auto; margin-top: 15px;">
                <table class="tbl-lajur">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 50px;">Kode</th>
                            <th rowspan="2" style="width: 140px; text-align: left; padding-left: 6px;">Nama Akun Perkiraan</th>
                            <th colspan="2">Saldo Awal</th>
                            <th colspan="2">Mutasi Periode</th>
                            <th colspan="2">Neraca Saldo</th>
                            <th colspan="2">Penyesuaian</th>
                            <th colspan="2">NS Disesuaikan</th>
                            <th colspan="2">Laba / Rugi</th>
                            <th colspan="2">Posisi Keuangan</th>
                        </tr>
                        <tr class="sub-th">
                            <th style="width: 60px;">Debit</th><th style="width: 60px;">Kredit</th>
                            <th style="width: 60px;">Debit</th><th style="width: 60px;">Kredit</th>
                            <th style="width: 60px;">Debit</th><th style="width: 60px;">Kredit</th>
                            <th style="width: 60px;">Debit</th><th style="width: 60px;">Kredit</th>
                            <th style="width: 65px;">Debit</th><th style="width: 65px;">Kredit</th>
                            <th style="width: 65px;">Debit</th><th style="width: 65px;">Kredit</th>
                            <th style="width: 65px;">Debit</th><th style="width: 65px;">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($lap)): ?>
                        <tr>
                            <td colspan="16" class="text-center" style="color: #64748b; font-style: italic; padding: 20px;">
                                Belum ada data transaksi untuk neraca lajur pada periode ini.
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach($lap as $r): ?>
                            <tr>
                                <td class="font-mono text-center font-bold" style="color: #475569;"><?php echo htmlspecialchars($r['kode_akun']); ?></td>
                                <td style="text-align: left;"><?php echo htmlspecialchars($r['nama_akun']); ?></td>
                                <td class="text-right font-mono"><?php echo $r['sa_debit'] != 0 ? number_format($r['sa_debit'], 0, ',', '.') : '-'; ?></td>
                                <td class="text-right font-mono"><?php echo $r['sa_kredit'] != 0 ? number_format($r['sa_kredit'], 0, ',', '.') : '-'; ?></td>
                                <td class="text-right font-mono"><?php echo $r['mutasi_debit'] != 0 ? number_format($r['mutasi_debit'], 0, ',', '.') : '-'; ?></td>
                                <td class="text-right font-mono"><?php echo $r['mutasi_kredit'] != 0 ? number_format($r['mutasi_kredit'], 0, ',', '.') : '-'; ?></td>
                                <td class="text-right font-mono"><?php echo $r['ns_debit'] != 0 ? number_format($r['ns_debit'], 0, ',', '.') : '-'; ?></td>
                                <td class="text-right font-mono"><?php echo $r['ns_kredit'] != 0 ? number_format($r['ns_kredit'], 0, ',', '.') : '-'; ?></td>
                                <td class="text-right font-mono"><?php echo $r['penyesuaian_debit'] != 0 ? number_format($r['penyesuaian_debit'], 0, ',', '.') : '-'; ?></td>
                                <td class="text-right font-mono"><?php echo $r['penyesuaian_kredit'] != 0 ? number_format($r['penyesuaian_kredit'], 0, ',', '.') : '-'; ?></td>
                                <td class="text-right font-mono"><?php echo $r['nsd_debit'] != 0 ? number_format($r['nsd_debit'], 0, ',', '.') : '-'; ?></td>
                                <td class="text-right font-mono"><?php echo $r['nsd_kredit'] != 0 ? number_format($r['nsd_kredit'], 0, ',', '.') : '-'; ?></td>
                                <td class="text-right font-mono"><?php echo $r['lr_debit'] != 0 ? number_format($r['lr_debit'], 0, ',', '.') : '-'; ?></td>
                                <td class="text-right font-mono"><?php echo $r['lr_kredit'] != 0 ? number_format($r['lr_kredit'], 0, ',', '.') : '-'; ?></td>
                                <td class="text-right font-mono"><?php echo $r['poskeu_debit'] != 0 ? number_format($r['poskeu_debit'], 0, ',', '.') : '-'; ?></td>
                                <td class="text-right font-mono"><?php echo $r['poskeu_kredit'] != 0 ? number_format($r['poskeu_kredit'], 0, ',', '.') : '-'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-center">JUMLAH (TOTAL)</td>
                            <td class="text-right font-mono"><?php echo number_format($totals['sa_debit'], 0, ',', '.'); ?></td>
                            <td class="text-right font-mono"><?php echo number_format($totals['sa_kredit'], 0, ',', '.'); ?></td>
                            <td class="text-right font-mono"><?php echo number_format($totals['mutasi_debit'], 0, ',', '.'); ?></td>
                            <td class="text-right font-mono"><?php echo number_format($totals['mutasi_kredit'], 0, ',', '.'); ?></td>
                            <td class="text-right font-mono"><?php echo number_format($totals['ns_debit'], 0, ',', '.'); ?></td>
                            <td class="text-right font-mono"><?php echo number_format($totals['ns_kredit'], 0, ',', '.'); ?></td>
                            <td class="text-right font-mono"><?php echo number_format($totals['penyesuaian_debit'], 0, ',', '.'); ?></td>
                            <td class="text-right font-mono"><?php echo number_format($totals['penyesuaian_kredit'], 0, ',', '.'); ?></td>
                            <td class="text-right font-mono"><?php echo number_format($totals['nsd_debit'], 0, ',', '.'); ?></td>
                            <td class="text-right font-mono"><?php echo number_format($totals['nsd_kredit'], 0, ',', '.'); ?></td>
                            <td class="text-right font-mono"><?php echo number_format($totals['lr_debit'], 0, ',', '.'); ?></td>
                            <td class="text-right font-mono"><?php echo number_format($totals['lr_kredit'], 0, ',', '.'); ?></td>
                            <td class="text-right font-mono"><?php echo number_format($totals['poskeu_debit'], 0, ',', '.'); ?></td>
                            <td class="text-right font-mono"><?php echo number_format($totals['poskeu_kredit'], 0, ',', '.'); ?></td>
                        </tr>

                        <!-- LABA / RUGI BERSIH PENYEIMBANG -->
                        <tr style="background-color: #f8fafc; font-weight: bold;">
                            <td colspan="12" class="text-right" style="color: #0f172a;">
                                <?php echo ($labaBersih >= 0) ? 'LABA BERSIH PERIODE BERJALAN' : 'RUGI BERSIH PERIODE BERJALAN'; ?>
                            </td>
                            <td class="text-right font-mono" style="color: #047857;">
                                <?php echo ($labaBersih >= 0) ? number_format($labaBersih, 0, ',', '.') : '-'; ?>
                            </td>
                            <td class="text-right font-mono text-muted">
                                <?php echo ($labaBersih < 0) ? number_format(abs($labaBersih), 0, ',', '.') : '-'; ?>
                            </td>
                            <td class="text-right font-mono text-muted">
                                <?php echo ($labaBersih < 0) ? number_format(abs($labaBersih), 0, ',', '.') : '-'; ?>
                            </td>
                            <td class="text-right font-mono" style="color: #047857;">
                                <?php echo ($labaBersih >= 0) ? number_format($labaBersih, 0, ',', '.') : '-'; ?>
                            </td>
                        </tr>

                        <!-- BALANCE FINAL -->
                        <tr class="tfoot-balance">
                            <td colspan="12" class="text-right">TOTAL SEIMBANG (BALANCED)</td>
                            <td class="text-right font-mono"><?php echo number_format(max($totals['lr_debit'], $totals['lr_kredit']), 0, ',', '.'); ?></td>
                            <td class="text-right font-mono"><?php echo number_format(max($totals['lr_debit'], $totals['lr_kredit']), 0, ',', '.'); ?></td>
                            <td class="text-right font-mono"><?php echo number_format(max($totals['poskeu_debit'], $totals['poskeu_kredit']), 0, ',', '.'); ?></td>
                            <td class="text-right font-mono"><?php echo number_format(max($totals['poskeu_debit'], $totals['poskeu_kredit']), 0, ',', '.'); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- PENGESAHAN / TANDA TANGAN FORMAL -->
            <section class="signatures-section" style="margin-top: 35px;">
                <div class="sig-col">
                    <p class="sig-title"><?php echo htmlspecialchars($data['penandatangan_1']['jabatan'] ?? 'Pimpinan / Direktur'); ?></p>
                    <div class="sig-space"></div>
                    <p class="sig-name"><?php echo htmlspecialchars($data['penandatangan_1']['nama_user'] ?? 'Pimpinan'); ?></p>
                    <p class="sig-post">Penanggung Jawab Keuangan</p>
                </div>
                <div class="sig-col">
                    <p class="sig-title"><?php echo htmlspecialchars($kota); ?>, <?php echo tanggal_indo(date('Y-m-d')); ?></p>
                    <p class="sig-title" style="margin-top: 2px;"><?php echo htmlspecialchars($data['penandatangan_2']['jabatan'] ?? 'Bendahara / Akuntan'); ?></p>
                    <div class="sig-space"></div>
                    <p class="sig-name"><?php echo htmlspecialchars($data['penandatangan_2']['nama_user'] ?? 'Bendahara'); ?></p>
                    <p class="sig-post">Penyusun Neraca Lajur</p>
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
