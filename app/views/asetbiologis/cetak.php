<?php
$c = $data['perusahaan'] ?? [];
$logoPath = !empty($c['path_logo']) ? BASEURL . '/' . $c['path_logo'] : BASEURL . '/img/logo_jatim.png';
$kota = !empty($data['kota_laporan']) ? $data['kota_laporan'] : ($c['kota_laporan'] ?? 'Mojokerto');

$rekonsiliasi = $data['rekonsiliasi'] ?? [];
$ringkasan = $data['ringkasan'] ?? [];

$tot_saldo_awal = 0;
$tot_pembelian = 0;
$tot_kelahiran = 0;
$tot_keuntungan_fv = 0;
$tot_kerugian_fv = 0;
$tot_panen = 0;
$tot_penjualan = 0;
$tot_kematian = 0;
$tot_saldo_akhir = 0;

foreach ($rekonsiliasi as $row) {
    $tot_saldo_awal += (float)($row['saldo_awal'] ?? 0);
    $tot_pembelian += (float)($row['pembelian'] ?? 0);
    $tot_kelahiran += (float)($row['kelahiran'] ?? 0);
    $tot_keuntungan_fv += (float)($row['keuntungan_fv'] ?? 0);
    $tot_kerugian_fv += (float)($row['kerugian_fv'] ?? 0);
    $tot_panen += (float)($row['panen'] ?? 0);
    $tot_penjualan += (float)($row['penjualan'] ?? 0);
    $tot_kematian += (float)($row['kematian'] ?? 0);
    $tot_saldo_akhir += (float)($row['saldo_akhir'] ?? 0);
}

$tot_penambahan = $tot_pembelian + $tot_kelahiran;
$net_fv = $tot_keuntungan_fv - $tot_kerugian_fv;
$tot_pengurangan = $tot_panen + $tot_penjualan + $tot_kematian;

$tot_populasi_aktif = 0;
$tot_nilai_aktif = 0;
foreach ($ringkasan as $r) {
    $tot_populasi_aktif += (int)($r['jumlah'] ?? 0);
    $tot_nilai_aktif += (float)($r['total_nilai'] ?? 0);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Aset Biologis (PSAK 241) - <?php echo htmlspecialchars($c['nama_perusahaan'] ?? 'SimpleAkunting'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>/css/print.css?v=3.6">
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 6mm;
        }
        .screen-container.landscape {
            max-width: 1240px;
        }
        .landscape-canvas {
            width: 100%;
            max-width: 1240px;
            padding: 28px 36px;
        }
        .print-table th, .print-table td {
            padding: 5px 6px;
            font-size: 8pt;
        }
        .section-badge {
            display: inline-block;
            padding: 2px 8px;
            background-color: #0f172a;
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: 800;
            border-radius: 4px;
            text-transform: uppercase;
        }
        @media print {
            body {
                background: #ffffff !important;
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
            .print-table th, .print-table td {
                font-size: 7.5pt !important;
                padding: 4px 5px !important;
            }
        }
    </style>
</head>
<body class="print-preview-mode">

    <!-- Floating Top Toolbar (Screen Only) -->
    <div class="screen-container landscape no-print">
        <div class="toolbar-print">
            <div class="toolbar-left">
                <a href="<?php echo BASEURL; ?>/asetbiologis/laporan?mulai=<?php echo urlencode($data['tanggal_mulai']); ?>&selesai=<?php echo urlencode($data['tanggal_selesai']); ?>" class="btn-toolbar-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Laporan</span>
                </a>
                <div>
                    <div style="font-weight: 800; font-size: 12px; color: #ffffff; display: flex; align-items: center; gap: 8px;">
                        <span>Laporan Aset Biologis (PSAK 241)</span>
                        <span class="toolbar-badge">A4 LANDSCAPE</span>
                        <span class="toolbar-badge" style="background: rgba(14, 165, 233, 0.15); color: #38bdf8; border-color: rgba(14, 165, 233, 0.3);">PSAK 241 / IAS 41</span>
                    </div>
                    <div style="font-size: 11px; color: #94a3b8;">Standar Akuntansi Agrikultur &bull; Sistem SimpleAkunting</div>
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
                    <span class="kop-badge" style="background-color: #065f46;">PSAK 241 / IAS 41</span>
                    <div class="kop-date">Dicetak: <?php echo date('d/m/Y H:i'); ?> WIB</div>
                </div>
            </header>

            <div class="kop-divider"></div>
            <div class="kop-divider-sub"></div>

            <!-- 2. JUDUL DOKUMEN -->
            <div class="doc-title-block">
                <h2 class="doc-title">LAPORAN ASET BIOLOGIS (AGRIKULTUR)</h2>
                <div class="doc-subtitle">STANDAR PSAK 241 / IAS 41 AGRIKULTUR &bull; ENTITAS BISNIS &amp; BUMDESA</div>
                <div class="doc-period-pill">
                    <i class="fas fa-calendar-alt text-amber-600"></i>
                    <span>Periode: <strong><?php echo htmlspecialchars($data['periode_1'] ?? '-'); ?></strong></span>
                </div>
            </div>

            <!-- 3. KPI RINGKASAN EKSEKUTIF -->
            <div class="kpi-grid">
                <div class="kpi-box">
                    <span class="kpi-label">Saldo Awal Periode</span>
                    <span class="kpi-value font-mono"><?php echo format_rupiah($tot_saldo_awal, 0); ?></span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Penambahan &amp; Pertumbuhan</span>
                    <span class="kpi-value income"><?php echo format_rupiah($tot_penambahan, 0); ?></span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Net Penyesuaian Nilai Wajar</span>
                    <span class="kpi-value <?php echo ($net_fv >= 0) ? 'income' : 'expense'; ?>">
                        <?php echo ($net_fv >= 0 ? '+' : '') . format_rupiah($net_fv, 0); ?>
                    </span>
                </div>
                <div class="kpi-box highlight">
                    <span class="kpi-label">Total Nilai Tercatat Akhir</span>
                    <span class="kpi-value income" style="font-size: 11.5pt;">
                        <?php echo format_rupiah($tot_saldo_akhir, 0); ?>
                    </span>
                </div>
            </div>

            <!-- 4. TABEL I: REKONSILIASI NILAI TERCATAT (IAS 41.50) -->
            <div style="font-weight: 800; font-size: 9pt; text-transform: uppercase; color: #0f172a; margin-bottom: 6px; display: flex; align-items: center; justify-content: space-between;">
                <span>I. Rekonsiliasi Perubahan Nilai Tercatat Aset Biologis (IAS 41.50 / PSAK 241)</span>
                <span style="font-size: 7.5pt; font-weight: 600; color: #64748b; text-transform: none;">(Disajikan dalam Rupiah)</span>
            </div>
            <table class="print-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="vertical-align: middle; width: 16%;">Kategori Aset</th>
                        <th rowspan="2" class="text-right" style="vertical-align: middle; width: 10%;">Saldo Awal</th>
                        <th colspan="2" class="text-center" style="width: 18%;">Penambahan</th>
                        <th colspan="2" class="text-center" style="width: 18%;">Penyesuaian Nilai Wajar</th>
                        <th colspan="3" class="text-center" style="width: 26%;">Pengurangan</th>
                        <th rowspan="2" class="text-right" style="vertical-align: middle; width: 12%;">Saldo Akhir</th>
                    </tr>
                    <tr>
                        <th class="text-right" style="font-size: 7pt; width: 9%;">Pembelian</th>
                        <th class="text-right" style="font-size: 7pt; width: 9%;">Kelahiran/Tumbuh</th>
                        <th class="text-right" style="font-size: 7pt; width: 9%;">Keuntungan</th>
                        <th class="text-right" style="font-size: 7pt; width: 9%;">Kerugian</th>
                        <th class="text-right" style="font-size: 7pt; width: 8.6%;">Panen</th>
                        <th class="text-right" style="font-size: 7pt; width: 8.6%;">Penjualan</th>
                        <th class="text-right" style="font-size: 7pt; width: 8.8%;">Kematian/Hapus</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rekonsiliasi)): ?>
                    <tr>
                        <td colspan="10" class="text-center" style="color: #64748b; font-style: italic; padding: 15px;">
                            Tidak ada data rekonsiliasi aset biologis pada periode ini.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($rekonsiliasi as $kat => $row): ?>
                        <tr>
                            <td style="font-weight: 700; color: #1e293b;"><?php echo htmlspecialchars($kat); ?></td>
                            <td class="text-right font-mono"><?php echo number_format($row['saldo_awal'] ?? 0, 0, ',', '.'); ?></td>
                            <td class="text-right font-mono"><?php echo number_format($row['pembelian'] ?? 0, 0, ',', '.'); ?></td>
                            <td class="text-right font-mono"><?php echo number_format($row['kelahiran'] ?? 0, 0, ',', '.'); ?></td>
                            <td class="text-right font-mono" style="color: #047857;"><?php echo ($row['keuntungan_fv'] > 0 ? '+' : '') . number_format($row['keuntungan_fv'] ?? 0, 0, ',', '.'); ?></td>
                            <td class="text-right font-mono" style="color: #b91c1c;"><?php echo ($row['kerugian_fv'] > 0 ? '-' : '') . number_format($row['kerugian_fv'] ?? 0, 0, ',', '.'); ?></td>
                            <td class="text-right font-mono" style="color: #b91c1c;"><?php echo ($row['panen'] > 0 ? '-' : '') . number_format($row['panen'] ?? 0, 0, ',', '.'); ?></td>
                            <td class="text-right font-mono" style="color: #b91c1c;"><?php echo ($row['penjualan'] > 0 ? '-' : '') . number_format($row['penjualan'] ?? 0, 0, ',', '.'); ?></td>
                            <td class="text-right font-mono" style="color: #b91c1c;"><?php echo ($row['kematian'] > 0 ? '-' : '') . number_format($row['kematian'] ?? 0, 0, ',', '.'); ?></td>
                            <td class="text-right font-mono" style="font-weight: 800; color: #0f172a;"><?php echo number_format($row['saldo_akhir'] ?? 0, 0, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="grand-total-row">
                        <td style="font-weight: 800; text-transform: uppercase;">TOTAL KESELURUHAN</td>
                        <td class="text-right font-mono font-bold"><?php echo number_format($tot_saldo_awal, 0, ',', '.'); ?></td>
                        <td class="text-right font-mono font-bold"><?php echo number_format($tot_pembelian, 0, ',', '.'); ?></td>
                        <td class="text-right font-mono font-bold"><?php echo number_format($tot_kelahiran, 0, ',', '.'); ?></td>
                        <td class="text-right font-mono font-bold" style="color: #047857;"><?php echo number_format($tot_keuntungan_fv, 0, ',', '.'); ?></td>
                        <td class="text-right font-mono font-bold" style="color: #b91c1c;"><?php echo number_format($tot_kerugian_fv, 0, ',', '.'); ?></td>
                        <td class="text-right font-mono font-bold" style="color: #b91c1c;"><?php echo number_format($tot_panen, 0, ',', '.'); ?></td>
                        <td class="text-right font-mono font-bold" style="color: #b91c1c;"><?php echo number_format($tot_penjualan, 0, ',', '.'); ?></td>
                        <td class="text-right font-mono font-bold" style="color: #b91c1c;"><?php echo number_format($tot_kematian, 0, ',', '.'); ?></td>
                        <td class="text-right font-mono font-bold" style="color: #0f172a;"><?php echo number_format($tot_saldo_akhir, 0, ',', '.'); ?></td>
                    </tr>
                </tfoot>
            </table>

            <!-- 5. TABEL II: RINGKASAN KLASIFIKASI & POPULASI ASET BIOLOGIS AKTIF -->
            <div style="font-weight: 800; font-size: 9pt; text-transform: uppercase; color: #0f172a; margin-top: 18px; margin-bottom: 6px; display: flex; align-items: center; justify-content: space-between;">
                <span>II. Ringkasan Klasifikasi &amp; Populasi Aset Biologis Aktif</span>
                <span style="font-size: 7.5pt; font-weight: 600; color: #64748b; text-transform: none;">(Posisi Per Tanggal <?php echo date('d/m/Y', strtotime($data['tanggal_selesai'])); ?>)</span>
            </div>
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 35%;">Kategori Aset</th>
                        <th style="width: 25%;">Status Kematangan</th>
                        <th class="text-center" style="width: 15%;">Jumlah Populasi / Unit</th>
                        <th class="text-right" style="width: 25%;">Total Nilai Tercatat (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ringkasan)): ?>
                    <tr>
                        <td colspan="4" class="text-center" style="color: #64748b; font-style: italic; padding: 15px;">
                            Tidak ada aset biologis aktif yang tercatat.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php 
                        $total_ringkasan_aset = 0;
                        $total_ringkasan_nilai = 0;
                        foreach ($ringkasan as $row): 
                            $total_ringkasan_aset += (int)$row['jumlah'];
                            $total_ringkasan_nilai += (float)$row['total_nilai'];
                        ?>
                        <tr>
                            <td style="font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($row['kategori']); ?></td>
                            <td>
                                <span class="badge-status <?php echo ($row['status_kematangan'] == 'Menghasilkan') ? 'badge-lunas' : 'badge-belum-lunas'; ?>">
                                    <?php echo htmlspecialchars($row['status_kematangan']); ?>
                                </span>
                            </td>
                            <td class="text-center font-mono font-bold"><?php echo number_format($row['jumlah'], 0, ',', '.'); ?></td>
                            <td class="text-right font-mono font-bold"><?php echo number_format($row['total_nilai'], 0, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="grand-total-row">
                        <td colspan="2" style="font-weight: 800; text-transform: uppercase;">TOTAL POPULASI &amp; NILAI TERCATAT AKTIF</td>
                        <td class="text-center font-mono font-bold"><?php echo number_format($total_ringkasan_aset ?? 0, 0, ',', '.'); ?></td>
                        <td class="text-right font-mono font-bold text-success" style="color: #047857;"><?php echo format_rupiah($total_ringkasan_nilai ?? 0, 0); ?></td>
                    </tr>
                </tfoot>
            </table>

            <!-- 6. PENGESAHAN / TANDA TANGAN KORPORASI -->
            <div class="ttd-container">
                <div class="ttd-city-date">
                    <?php echo htmlspecialchars($kota); ?>, <?php echo tanggal_indo(date('Y-m-d')); ?>
                </div>
                <div class="ttd-grid cols-2">
                    <div class="ttd-box">
                        <div class="ttd-title">Disetujui Oleh (Pimpinan / Direktur)</div>
                        <div>
                            <div class="ttd-name"><?php echo htmlspecialchars($data['penandatangan_1']['nama_user'] ?? 'Pimpinan'); ?></div>
                            <div class="ttd-role"><?php echo htmlspecialchars($data['penandatangan_1']['jabatan'] ?? 'Direktur Utama'); ?></div>
                        </div>
                    </div>
                    <div class="ttd-box">
                        <div class="ttd-title">Disusun Oleh (Pengelola Aset / Akuntan)</div>
                        <div>
                            <div class="ttd-name"><?php echo htmlspecialchars($data['penandatangan_2']['nama_user'] ?? 'Pengelola Aset'); ?></div>
                            <div class="ttd-role"><?php echo htmlspecialchars($data['penandatangan_2']['jabatan'] ?? 'Akuntan / Bendahara'); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 7. FOOTER RESMI KOMPUTERISASI -->
            <footer class="paper-footer">
                <div>
                    Laporan ini sah dan disajikan otomatis berdasarkan standar PSAK 241 / IAS 41 Agrikultur oleh sistem <strong>SimpleAkunting</strong>.
                </div>
                <div class="font-mono">
                    Halaman 1 / 1
                </div>
            </footer>

        </div>
    </div>

</body>
</html>
