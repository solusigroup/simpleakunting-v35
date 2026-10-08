<?php
$c = $data['perusahaan'] ?? [];
$logoPath = !empty($c['path_logo']) ? BASEURL . '/' . $c['path_logo'] : BASEURL . '/img/logo_jatim.png';
$kota = !empty($data['kota_laporan']) ? $data['kota_laporan'] : ($c['kota_laporan'] ?? 'Mojokerto');
$lap = $data['laporan'] ?? [];

$metode = !empty($lap['metode']) ? ucfirst(strtolower($lap['metode'])) : 'Indirect';
$isIndirect = (strtolower($metode) == 'indirect');

$kasAwal = (float)($lap['kas_awal'] ?? 0);
$kasAkhir = (float)($lap['kas_akhir'] ?? 0);
$kenaikanKas = $kasAkhir - $kasAwal;

$totalOperasi = 0;
if ($isIndirect) {
    $labaBersih = (float)($lap['laba_bersih'] ?? 0);
    $totalOperasi = $labaBersih;
    $penyesuaian = $lap['penyesuaian'] ?? [];
    foreach ($penyesuaian as $p) {
        $totalOperasi += (float)($p['jumlah'] ?? 0);
    }
} else {
    $arusOperasi = $lap['arus_operasi'] ?? [];
    foreach ($arusOperasi as $ao) {
        $totalOperasi += (float)($ao['jumlah'] ?? 0);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Arus Kas - <?php echo htmlspecialchars($c['nama_perusahaan'] ?? 'SimpleAkunting'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>/css/print.css?v=3.6">
</head>
<body class="print-preview-mode">

    <!-- Floating Top Toolbar (Screen Only) -->
    <div class="screen-container no-print">
        <div class="toolbar-print">
            <div class="toolbar-left">
                <a href="<?php echo BASEURL; ?>/laporan/arusKas" class="btn-toolbar-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Laporan</span>
                </a>
                <div>
                    <div style="font-weight: 800; font-size: 12px; color: #ffffff; display: flex; align-items: center; gap: 8px;">
                        <span>Laporan Arus Kas (Cash Flow Statement)</span>
                        <span class="toolbar-badge">METODE <?php echo strtoupper($metode); ?></span>
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
                    <span class="kop-badge">ARUS KAS</span>
                    <div class="kop-date">Dicetak: <?php echo date('d/m/Y H:i'); ?> WIB</div>
                </div>
            </header>

            <div class="kop-divider"></div>
            <div class="kop-divider-sub"></div>

            <!-- 2. JUDUL DOKUMEN -->
            <div class="doc-title-block">
                <h2 class="doc-title">LAPORAN ARUS KAS</h2>
                <div class="doc-subtitle">METODE <?php echo strtoupper($metode); ?> &bull; STANDAR PELAPORAN KEUANGAN ENTITAS BISNIS &amp; BUMDESA</div>
                <div class="doc-period-pill">
                    <i class="fas fa-calendar-alt text-amber-600"></i>
                    <span>Periode: <strong><?php echo htmlspecialchars($data['periode_1'] ?? '-'); ?></strong></span>
                </div>
            </div>

            <!-- 3. KPI RINGKASAN EKSEKUTIF -->
            <div class="kpi-grid">
                <div class="kpi-box">
                    <span class="kpi-label">Kas Awal Periode</span>
                    <span class="kpi-value"><?php echo format_rupiah($kasAwal, 0); ?></span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Arus Kas Operasi</span>
                    <span class="kpi-value <?php echo $totalOperasi >= 0 ? 'income' : 'expense'; ?>">
                        <?php echo format_rupiah($totalOperasi, 0); ?>
                    </span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Kenaikan (Penurunan) Kas</span>
                    <span class="kpi-value <?php echo $kenaikanKas >= 0 ? 'income' : 'expense'; ?>">
                        <?php echo format_rupiah($kenaikanKas, 0); ?>
                    </span>
                </div>
                <div class="kpi-box highlight">
                    <span class="kpi-label">Kas Akhir Periode</span>
                    <span class="kpi-value income">
                        <?php echo format_rupiah($kasAkhir, 0); ?>
                    </span>
                </div>
            </div>

            <!-- 4. TABEL ARUS KAS -->
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 70%;">Aktivitas / Uraian Arus Kas</th>
                        <th class="text-right" style="width: 30%;">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- I. ARUS KAS DARI AKTIVITAS OPERASI -->
                    <tr class="section-header">
                        <td colspan="2">I. ARUS KAS DARI AKTIVITAS OPERASI</td>
                    </tr>

                    <?php if ($isIndirect): ?>
                        <tr>
                            <td style="padding-left: 20px;">Laba (Rugi) Bersih Periode Berjalan</td>
                            <td class="text-right font-mono"><?php echo number_format($labaBersih, 0, ',', '.'); ?></td>
                        </tr>
                        <?php if (!empty($penyesuaian)): ?>
                            <tr style="background-color: #f8fafc; font-style: italic;">
                                <td colspan="2" style="padding-left: 20px; color: #64748b; font-size: 8pt;">Penyesuaian untuk merekonsiliasi laba bersih menjadi arus kas operasi:</td>
                            </tr>
                            <?php foreach ($penyesuaian as $item): 
                                $v = (float)($item['jumlah'] ?? 0);
                            ?>
                            <tr>
                                <td style="padding-left: 35px;"><?php echo htmlspecialchars($item['label']); ?></td>
                                <td class="text-right font-mono <?php echo $v < 0 ? 'text-danger' : ''; ?>">
                                    <?php echo ($v < 0) ? '(' . number_format(abs($v), 0, ',', '.') . ')' : number_format($v, 0, ',', '.'); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if (empty($arusOperasi)): ?>
                        <tr>
                            <td colspan="2" class="text-center" style="color: #64748b; font-style: italic;">Tidak ada pos penerimaan/pengeluaran operasi</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($arusOperasi as $item): 
                                $v = (float)($item['jumlah'] ?? 0);
                            ?>
                            <tr>
                                <td style="padding-left: 20px;"><?php echo htmlspecialchars($item['label']); ?></td>
                                <td class="text-right font-mono <?php echo $v < 0 ? 'text-danger' : ''; ?>">
                                    <?php echo ($v < 0) ? '(' . number_format(abs($v), 0, ',', '.') . ')' : number_format($v, 0, ',', '.'); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <tr class="total-row">
                        <td>ARUS KAS BERSIH DARI AKTIVITAS OPERASI</td>
                        <td class="text-right font-mono <?php echo $totalOperasi < 0 ? 'text-danger' : ''; ?>">
                            <?php echo ($totalOperasi < 0) ? '(' . number_format(abs($totalOperasi), 0, ',', '.') . ')' : number_format($totalOperasi, 0, ',', '.'); ?>
                        </td>
                    </tr>

                    <!-- II. REKONSILIASI KAS -->
                    <tr class="section-header" style="padding-top: 15px;">
                        <td colspan="2">II. REKONSILIASI SALDO KAS &amp; SETARA KAS</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 20px;">Saldo Kas Awal Periode</td>
                        <td class="text-right font-mono"><?php echo number_format($kasAwal, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td style="padding-left: 20px;">Kenaikan (Penurunan) Bersih Kas &amp; Setara Kas</td>
                        <td class="text-right font-mono <?php echo $kenaikanKas < 0 ? 'text-danger' : ''; ?>">
                            <?php echo ($kenaikanKas < 0) ? '(' . number_format(abs($kenaikanKas), 0, ',', '.') . ')' : number_format($kenaikanKas, 0, ',', '.'); ?>
                        </td>
                    </tr>
                    <tr class="grand-total-row">
                        <td>SALDO KAS &amp; SETARA KAS AKHIR PERIODE</td>
                        <td class="text-right font-mono"><?php echo number_format($kasAkhir, 0, ',', '.'); ?></td>
                    </tr>
                </tbody>
            </table>

            <!-- PENGESAHAN / TANDA TANGAN FORMAL -->
            <section class="signatures-section">
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
                    <p class="sig-post">Penyusun Arus Kas</p>
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
