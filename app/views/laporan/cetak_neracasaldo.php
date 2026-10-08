<?php
$c = $data['perusahaan'] ?? [];
$logoPath = !empty($c['path_logo']) ? BASEURL . '/' . $c['path_logo'] : BASEURL . '/img/logo_jatim.png';
$kota = !empty($data['kota_laporan']) ? $data['kota_laporan'] : ($c['kota_laporan'] ?? 'Mojokerto');
$lap = $data['laporan'] ?? [];

$totalDebit = 0;
$totalKredit = 0;
foreach ($lap as $row) {
    $totalDebit += (float)($row['debit'] ?? 0);
    $totalKredit += (float)($row['kredit'] ?? 0);
}
$selisih = abs($totalDebit - $totalKredit);
$isBalanced = $selisih < 0.01;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Neraca Saldo - <?php echo htmlspecialchars($c['nama_perusahaan'] ?? 'SimpleAkunting'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>/css/print.css?v=3.6">
</head>
<body class="print-preview-mode">

    <!-- Floating Top Toolbar (Screen Only) -->
    <div class="screen-container no-print">
        <div class="toolbar-print">
            <div class="toolbar-left">
                <a href="<?php echo BASEURL; ?>/laporan/neracaSaldo" class="btn-toolbar-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Laporan</span>
                </a>
                <div>
                    <div style="font-weight: 800; font-size: 12px; color: #ffffff; display: flex; align-items: center; gap: 8px;">
                        <span>Laporan Neraca Saldo (Trial Balance)</span>
                        <span class="toolbar-badge"><?php echo $isBalanced ? 'STATUS: SEIMBANG' : 'STATUS: SELISIH'; ?></span>
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
                    <span class="kop-badge">NERACA SALDO</span>
                    <div class="kop-date">Dicetak: <?php echo date('d/m/Y H:i'); ?> WIB</div>
                </div>
            </header>

            <div class="kop-divider"></div>
            <div class="kop-divider-sub"></div>

            <!-- 2. JUDUL DOKUMEN -->
            <div class="doc-title-block">
                <h2 class="doc-title">LAPORAN NERACA SALDO</h2>
                <div class="doc-subtitle">TRIAL BALANCE &bull; KESEIMBANGAN DEBIT DAN KREDIT BUKU BESAR</div>
                <div class="doc-period-pill">
                    <i class="fas fa-calendar-alt text-amber-600"></i>
                    <span>Per Tanggal: <strong><?php echo htmlspecialchars($data['periode_1'] ?? date('d/m/Y')); ?></strong></span>
                </div>
            </div>

            <!-- 3. KPI RINGKASAN EKSEKUTIF -->
            <div class="kpi-grid">
                <div class="kpi-box">
                    <span class="kpi-label">Total Saldo Debit</span>
                    <span class="kpi-value income"><?php echo format_rupiah($totalDebit, 0); ?></span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Total Saldo Kredit</span>
                    <span class="kpi-value expense"><?php echo format_rupiah($totalKredit, 0); ?></span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Selisih Keseimbangan</span>
                    <span class="kpi-value font-mono <?php echo $isBalanced ? 'text-slate-700' : 'expense'; ?>">
                        <?php echo format_rupiah($selisih, 0); ?>
                    </span>
                </div>
                <div class="kpi-box highlight">
                    <span class="kpi-label">Status Neraca Saldo</span>
                    <span class="kpi-value <?php echo $isBalanced ? 'income' : 'expense'; ?>" style="font-size: 11pt;">
                        <i class="fas fa-<?php echo $isBalanced ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                        <?php echo $isBalanced ? 'SEIMBANG (BALANCED)' : 'SELISIH (UNBALANCED)'; ?>
                    </span>
                </div>
            </div>

            <!-- 4. TABEL NERACA SALDO -->
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 20%;">Kode Akun</th>
                        <th style="width: 44%;">Nama Akun Perkiraan</th>
                        <th class="text-right" style="width: 18%;">Debit (Rp)</th>
                        <th class="text-right" style="width: 18%;">Kredit (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lap)): ?>
                    <tr>
                        <td colspan="4" class="text-center" style="color: #64748b; font-style: italic; padding: 25px;">
                            Tidak ada data neraca saldo yang ditemukan.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($lap as $row): 
                            $deb = (float)($row['debit'] ?? 0);
                            $kre = (float)($row['kredit'] ?? 0);
                        ?>
                        <tr>
                            <td class="font-mono font-bold text-slate-700"><?php echo htmlspecialchars($row['kode_akun']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_akun']); ?></td>
                            <td class="text-right font-mono <?php echo $deb > 0 ? '' : 'text-muted'; ?>">
                                <?php echo $deb > 0 ? number_format($deb, 0, ',', '.') : '-'; ?>
                            </td>
                            <td class="text-right font-mono <?php echo $kre > 0 ? '' : 'text-muted'; ?>">
                                <?php echo $kre > 0 ? number_format($kre, 0, ',', '.') : '-'; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- GRAND TOTAL -->
                    <tr class="grand-total-row">
                        <td colspan="2" class="text-right">TOTAL KESEIMBANGAN</td>
                        <td class="text-right font-mono"><?php echo number_format($totalDebit, 0, ',', '.'); ?></td>
                        <td class="text-right font-mono"><?php echo number_format($totalKredit, 0, ',', '.'); ?></td>
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
