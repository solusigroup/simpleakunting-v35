<?php
$c = $data['perusahaan'] ?? [];
$logoPath = !empty($c['path_logo']) ? BASEURL . '/' . $c['path_logo'] : BASEURL . '/img/logo_jatim.png';
$kota = !empty($data['kota_laporan']) ? $data['kota_laporan'] : ($c['kota_laporan'] ?? 'Mojokerto');
$lap = $data['laporan'] ?? [];

$isKomparatif = !empty($data['periode_2']);
$p1 = $lap['periode_1'] ?? ['aset' => [], 'kewajiban' => [], 'modal' => [], 'total_aset' => 0, 'total_kewajiban' => 0, 'total_modal' => 0];
$p2 = $lap['periode_2'] ?? ['aset' => [], 'kewajiban' => [], 'modal' => [], 'total_aset' => 0, 'total_kewajiban' => 0, 'total_modal' => 0];

$totAset1 = (float)($p1['total_aset'] ?? 0);
$totKewajiban1 = (float)($p1['total_kewajiban'] ?? 0);
$totModal1 = (float)($p1['total_modal'] ?? 0);
$totPasiva1 = $totKewajiban1 + $totModal1;
$isBalance1 = abs($totAset1 - $totPasiva1) < 0.01;

$totAset2 = (float)($p2['total_aset'] ?? 0);
$totKewajiban2 = (float)($p2['total_kewajiban'] ?? 0);
$totModal2 = (float)($p2['total_modal'] ?? 0);
$totPasiva2 = $totKewajiban2 + $totModal2;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Posisi Keuangan - <?php echo htmlspecialchars($c['nama_perusahaan'] ?? 'SimpleAkunting'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>/css/print.css?v=3.6">
</head>
<body class="print-preview-mode">

    <!-- Floating Top Toolbar (Screen Only) -->
    <div class="screen-container no-print">
        <div class="toolbar-print">
            <div class="toolbar-left">
                <a href="<?php echo BASEURL; ?>/laporan/posisiKeuangan" class="btn-toolbar-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Laporan</span>
                </a>
                <div>
                    <div style="font-weight: 800; font-size: 12px; color: #ffffff; display: flex; align-items: center; gap: 8px;">
                        <span>Laporan Posisi Keuangan (Neraca)</span>
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
                <h2 class="doc-title">LAPORAN POSISI KEUANGAN (NERACA)</h2>
                <div class="doc-subtitle">STANDAR PELAPORAN KEUANGAN ENTITAS BISNIS &amp; BUMDESA</div>
                <div class="doc-period-pill">
                    <i class="fas fa-calendar-alt text-amber-600"></i>
                    <span>Per Tanggal: <strong><?php echo htmlspecialchars($data['periode_1'] ?? '-'); ?></strong>
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
                    <span class="kpi-label">Total Aset (Aktiva)</span>
                    <span class="kpi-value income"><?php echo format_rupiah($totAset1, 0); ?></span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Total Kewajiban</span>
                    <span class="kpi-value expense"><?php echo format_rupiah($totKewajiban1, 0); ?></span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Total Ekuitas (Modal)</span>
                    <span class="kpi-value income"><?php echo format_rupiah($totModal1, 0); ?></span>
                </div>
                <div class="kpi-box highlight">
                    <span class="kpi-label">Status Keseimbangan</span>
                    <span class="kpi-value <?php echo $isBalance1 ? 'income' : 'expense'; ?>" style="font-size: 11pt;">
                        <i class="fas fa-<?php echo $isBalance1 ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                        <?php echo $isBalance1 ? 'BALANCED (SEIMBANG)' : 'TIDAK SEIMBANG'; ?>
                    </span>
                </div>
            </div>

            <!-- 4. TABEL POSISI KEUANGAN -->
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 55%;">Pos Akun / Keterangan</th>
                        <th class="text-right" style="width: <?php echo $isKomparatif ? '25%' : '45%'; ?>;">
                            Per <?php echo $data['periode_1']; ?> (Rp)
                        </th>
                        <?php if($isKomparatif): ?>
                            <th class="text-right" style="width: 20%;">
                                Per <?php echo $data['periode_2']; ?> (Rp)
                            </th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <!-- BAGIAN I: ASET -->
                    <tr class="section-header">
                        <td colspan="<?php echo $isKomparatif ? '3' : '2'; ?>">I. ASET (AKTIVA)</td>
                    </tr>
                    <?php if (empty($p1['aset'])): ?>
                    <tr>
                        <td colspan="<?php echo $isKomparatif ? '3' : '2'; ?>" class="text-center" style="color: #64748b; font-style: italic;">Tidak ada pos aset tercatat</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($p1['aset'] as $item): 
                            $val1 = (float)$item['total'];
                            $key2 = array_search($item['kode_akun'], array_column($p2['aset'] ?? [], 'kode_akun'));
                            $val2 = ($key2 !== false) ? (float)$p2['aset'][$key2]['total'] : 0;
                        ?>
                        <tr>
                            <td style="padding-left: 20px;">
                                <span class="font-mono text-muted" style="font-size: 7.5pt; margin-right: 6px;"><?php echo htmlspecialchars($item['kode_akun']); ?></span>
                                <?php echo htmlspecialchars($item['nama_akun']); ?>
                            </td>
                            <td class="text-right font-mono"><?php echo number_format($val1, 0, ',', '.'); ?></td>
                            <?php if($isKomparatif): ?>
                                <td class="text-right font-mono" style="color: #64748b;"><?php echo number_format($val2, 0, ',', '.'); ?></td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr class="total-row">
                        <td>TOTAL ASET</td>
                        <td class="text-right font-mono"><?php echo number_format($totAset1, 0, ',', '.'); ?></td>
                        <?php if($isKomparatif): ?>
                            <td class="text-right font-mono"><?php echo number_format($totAset2, 0, ',', '.'); ?></td>
                        <?php endif; ?>
                    </tr>

                    <!-- BAGIAN II: KEWAJIBAN -->
                    <tr class="section-header" style="padding-top: 15px;">
                        <td colspan="<?php echo $isKomparatif ? '3' : '2'; ?>">II. KEWAJIBAN (LIABILITAS)</td>
                    </tr>
                    <?php if (empty($p1['kewajiban'])): ?>
                    <tr>
                        <td colspan="<?php echo $isKomparatif ? '3' : '2'; ?>" class="text-center" style="color: #64748b; font-style: italic;">Tidak ada pos kewajiban tercatat</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($p1['kewajiban'] as $item): 
                            $val1 = (float)$item['total'];
                            $key2 = array_search($item['kode_akun'], array_column($p2['kewajiban'] ?? [], 'kode_akun'));
                            $val2 = ($key2 !== false) ? (float)$p2['kewajiban'][$key2]['total'] : 0;
                        ?>
                        <tr>
                            <td style="padding-left: 20px;">
                                <span class="font-mono text-muted" style="font-size: 7.5pt; margin-right: 6px;"><?php echo htmlspecialchars($item['kode_akun']); ?></span>
                                <?php echo htmlspecialchars($item['nama_akun']); ?>
                            </td>
                            <td class="text-right font-mono"><?php echo number_format($val1, 0, ',', '.'); ?></td>
                            <?php if($isKomparatif): ?>
                                <td class="text-right font-mono" style="color: #64748b;"><?php echo number_format($val2, 0, ',', '.'); ?></td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr class="total-row">
                        <td>TOTAL KEWAJIBAN</td>
                        <td class="text-right font-mono"><?php echo number_format($totKewajiban1, 0, ',', '.'); ?></td>
                        <?php if($isKomparatif): ?>
                            <td class="text-right font-mono"><?php echo number_format($totKewajiban2, 0, ',', '.'); ?></td>
                        <?php endif; ?>
                    </tr>

                    <!-- BAGIAN III: EKUITAS -->
                    <tr class="section-header" style="padding-top: 15px;">
                        <td colspan="<?php echo $isKomparatif ? '3' : '2'; ?>">III. EKUITAS (MODAL)</td>
                    </tr>
                    <?php if (empty($p1['modal'])): ?>
                    <tr>
                        <td colspan="<?php echo $isKomparatif ? '3' : '2'; ?>" class="text-center" style="color: #64748b; font-style: italic;">Tidak ada pos ekuitas tercatat</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($p1['modal'] as $item): 
                            $val1 = (float)$item['total'];
                            $key2 = array_search($item['kode_akun'], array_column($p2['modal'] ?? [], 'kode_akun'));
                            $val2 = ($key2 !== false) ? (float)$p2['modal'][$key2]['total'] : 0;
                        ?>
                        <tr>
                            <td style="padding-left: 20px;">
                                <span class="font-mono text-muted" style="font-size: 7.5pt; margin-right: 6px;"><?php echo htmlspecialchars($item['kode_akun']); ?></span>
                                <?php echo htmlspecialchars($item['nama_akun']); ?>
                            </td>
                            <td class="text-right font-mono"><?php echo number_format($val1, 0, ',', '.'); ?></td>
                            <?php if($isKomparatif): ?>
                                <td class="text-right font-mono" style="color: #64748b;"><?php echo number_format($val2, 0, ',', '.'); ?></td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr class="total-row">
                        <td>TOTAL EKUITAS</td>
                        <td class="text-right font-mono"><?php echo number_format($totModal1, 0, ',', '.'); ?></td>
                        <?php if($isKomparatif): ?>
                            <td class="text-right font-mono"><?php echo number_format($totModal2, 0, ',', '.'); ?></td>
                        <?php endif; ?>
                    </tr>

                    <!-- GRAND TOTAL PASIVA -->
                    <tr class="grand-total-row">
                        <td>TOTAL KEWAJIBAN &amp; EKUITAS (PASIVA)</td>
                        <td class="text-right font-mono"><?php echo number_format($totPasiva1, 0, ',', '.'); ?></td>
                        <?php if($isKomparatif): ?>
                            <td class="text-right font-mono"><?php echo number_format($totPasiva2, 0, ',', '.'); ?></td>
                        <?php endif; ?>
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
                    <p class="sig-post">Penyusun Laporan</p>
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
