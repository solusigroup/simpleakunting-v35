<?php
$c = $data['perusahaan'] ?? [];
$logoPath = !empty($c['path_logo']) ? BASEURL . '/' . $c['path_logo'] : BASEURL . '/img/logo_jatim.png';
$kota = !empty($data['kota_laporan']) ? $data['kota_laporan'] : ($c['kota_laporan'] ?? 'Mojokerto');
$lap = $data['laporan'] ?? [];

$isKomparatif = !empty($data['periode_2']);
$p1 = $lap['periode_1'] ?? ['modal_awal' => 0, 'perubahan_modal_langsung' => 0, 'laba_rugi_periode_berjalan' => 0, 'modal_akhir' => 0];
$p2 = $lap['periode_2'] ?? ['modal_awal' => 0, 'perubahan_modal_langsung' => 0, 'laba_rugi_periode_berjalan' => 0, 'modal_akhir' => 0];

$modAwal1 = (float)($p1['modal_awal'] ?? 0);
$setoran1 = (float)($p1['perubahan_modal_langsung'] ?? 0);
$labaBerjalan1 = (float)($p1['laba_rugi_periode_berjalan'] ?? 0);
$modAkhir1 = (float)($p1['modal_akhir'] ?? 0);

$modAwal2 = (float)($p2['modal_awal'] ?? 0);
$setoran2 = (float)($p2['perubahan_modal_langsung'] ?? 0);
$labaBerjalan2 = (float)($p2['laba_rugi_periode_berjalan'] ?? 0);
$modAkhir2 = (float)($p2['modal_akhir'] ?? 0);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perubahan Ekuitas - <?php echo htmlspecialchars($c['nama_perusahaan'] ?? 'SimpleAkunting'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>/css/print.css?v=3.6">
</head>
<body class="print-preview-mode">

    <!-- Floating Top Toolbar (Screen Only) -->
    <div class="screen-container no-print">
        <div class="toolbar-print">
            <div class="toolbar-left">
                <a href="<?php echo BASEURL; ?>/laporan/perubahanEkuitas" class="btn-toolbar-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Laporan</span>
                </a>
                <div>
                    <div style="font-weight: 800; font-size: 12px; color: #ffffff; display: flex; align-items: center; gap: 8px;">
                        <span>Laporan Perubahan Ekuitas (Changes in Equity)</span>
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
                    <span class="kop-badge">EKUITAS</span>
                    <div class="kop-date">Dicetak: <?php echo date('d/m/Y H:i'); ?> WIB</div>
                </div>
            </header>

            <div class="kop-divider"></div>
            <div class="kop-divider-sub"></div>

            <!-- 2. JUDUL DOKUMEN -->
            <div class="doc-title-block">
                <h2 class="doc-title">LAPORAN PERUBAHAN EKUITAS</h2>
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
                    <span class="kpi-label">Modal Awal Periode</span>
                    <span class="kpi-value font-mono"><?php echo format_rupiah($modAwal1, 0); ?></span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Setoran / (Prive) Modal</span>
                    <span class="kpi-value <?php echo $setoran1 >= 0 ? 'income' : 'expense'; ?>">
                        <?php echo format_rupiah($setoran1, 0); ?>
                    </span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label"><?php echo $labaBerjalan1 >= 0 ? 'Laba Bersih Berjalan' : 'Rugi Bersih Berjalan'; ?></span>
                    <span class="kpi-value <?php echo $labaBerjalan1 >= 0 ? 'income' : 'expense'; ?>">
                        <?php echo format_rupiah($labaBerjalan1, 0); ?>
                    </span>
                </div>
                <div class="kpi-box highlight">
                    <span class="kpi-label">Modal Akhir Periode</span>
                    <span class="kpi-value income">
                        <?php echo format_rupiah($modAkhir1, 0); ?>
                    </span>
                </div>
            </div>

            <!-- 4. TABEL PERUBAHAN EKUITAS -->
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 55%;">Komponen Perubahan Modal</th>
                        <th class="text-right" style="width: <?php echo $isKomparatif ? '25%' : '45%'; ?>;">
                            Periode <?php echo $data['periode_1']; ?> (Rp)
                        </th>
                        <?php if($isKomparatif): ?>
                            <th class="text-right" style="width: 20%;">
                                Pembanding <?php echo $data['periode_2']; ?> (Rp)
                            </th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="font-weight: 600;">Saldo Modal Awal Periode</td>
                        <td class="text-right font-mono font-bold"><?php echo number_format($modAwal1, 0, ',', '.'); ?></td>
                        <?php if($isKomparatif): ?>
                            <td class="text-right font-mono" style="color: #64748b;"><?php echo number_format($modAwal2, 0, ',', '.'); ?></td>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <td style="padding-left: 25px;">Setoran (Penarikan) Modal Pemilik / Prive</td>
                        <td class="text-right font-mono <?php echo $setoran1 < 0 ? 'text-danger' : ''; ?>">
                            <?php echo ($setoran1 < 0) ? '(' . number_format(abs($setoran1), 0, ',', '.') . ')' : number_format($setoran1, 0, ',', '.'); ?>
                        </td>
                        <?php if($isKomparatif): ?>
                            <td class="text-right font-mono" style="color: #64748b;">
                                <?php echo ($setoran2 < 0) ? '(' . number_format(abs($setoran2), 0, ',', '.') . ')' : number_format($setoran2, 0, ',', '.'); ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <td style="padding-left: 25px;">Laba (Rugi) Bersih Periode Berjalan</td>
                        <td class="text-right font-mono <?php echo $labaBerjalan1 < 0 ? 'text-danger' : ''; ?>">
                            <?php echo ($labaBerjalan1 < 0) ? '(' . number_format(abs($labaBerjalan1), 0, ',', '.') . ')' : number_format($labaBerjalan1, 0, ',', '.'); ?>
                        </td>
                        <?php if($isKomparatif): ?>
                            <td class="text-right font-mono" style="color: #64748b;">
                                <?php echo ($labaBerjalan2 < 0) ? '(' . number_format(abs($labaBerjalan2), 0, ',', '.') . ')' : number_format($labaBerjalan2, 0, ',', '.'); ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <tr class="grand-total-row">
                        <td>SALDO MODAL AKHIR PERIODE</td>
                        <td class="text-right font-mono"><?php echo number_format($modAkhir1, 0, ',', '.'); ?></td>
                        <?php if($isKomparatif): ?>
                            <td class="text-right font-mono"><?php echo number_format($modAkhir2, 0, ',', '.'); ?></td>
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
                    <p class="sig-post">Penyusun Laporan Ekuitas</p>
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
