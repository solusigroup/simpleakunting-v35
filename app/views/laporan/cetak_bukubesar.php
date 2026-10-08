<?php
$c = $data['perusahaan'] ?? [];
$logoPath = !empty($c['path_logo']) ? BASEURL . '/' . $c['path_logo'] : BASEURL . '/img/logo_jatim.png';
$kota = !empty($data['kota_laporan']) ? $data['kota_laporan'] : ($c['kota_laporan'] ?? 'Mojokerto');
$lap = $data['laporan'] ?? [];

$kodeAkun = $data['kode_akun_terpilih'] ?? '-';
$namaAkun = $data['nama_akun_terpilih'] ?? 'Semua Akun';
$saldoAwal = (float)($lap['saldo_awal_periode'] ?? 0);
$posisiNormal = $lap['posisi_saldo_normal'] ?? 'Debit';

$transaksi = $lap['transaksi'] ?? [];
$totDebit = 0;
$totKredit = 0;
foreach ($transaksi as $t) {
    $totDebit += (float)($t['debit'] ?? 0);
    $totKredit += (float)($t['kredit'] ?? 0);
}

$saldoAkhir = $saldoAwal;
if ($posisiNormal == 'Debit') {
    $saldoAkhir += ($totDebit - $totKredit);
} else {
    $saldoAkhir += ($totKredit - $totDebit);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Buku Besar - <?php echo htmlspecialchars($namaAkun); ?> - <?php echo htmlspecialchars($c['nama_perusahaan'] ?? 'SimpleAkunting'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>/css/print.css?v=3.6">
</head>
<body class="print-preview-mode">

    <!-- Floating Top Toolbar (Screen Only) -->
    <div class="screen-container no-print">
        <div class="toolbar-print">
            <div class="toolbar-left">
                <a href="<?php echo BASEURL; ?>/laporan/bukuBesar" class="btn-toolbar-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Laporan</span>
                </a>
                <div>
                    <div style="font-weight: 800; font-size: 12px; color: #ffffff; display: flex; align-items: center; gap: 8px;">
                        <span>Laporan Buku Besar (General Ledger)</span>
                        <span class="toolbar-badge font-mono">[<?php echo htmlspecialchars($kodeAkun); ?>] <?php echo htmlspecialchars($namaAkun); ?></span>
                    </div>
                    <div style="font-size: 11px; color: #94a3b8;">Standar SAK EP / SAK EMKM &bull; Posisi Saldo Normal: <?php echo strtoupper($posisiNormal); ?></div>
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
                    <span class="kop-badge">BUKU BESAR</span>
                    <div class="kop-date">Dicetak: <?php echo date('d/m/Y H:i'); ?> WIB</div>
                </div>
            </header>

            <div class="kop-divider"></div>
            <div class="kop-divider-sub"></div>

            <!-- 2. JUDUL DOKUMEN -->
            <div class="doc-title-block">
                <h2 class="doc-title">LAPORAN BUKU BESAR</h2>
                <div class="doc-subtitle">
                    AKUN: <span class="font-mono" style="font-weight: 700; color: #0284c7;">[<?php echo htmlspecialchars($kodeAkun); ?>]</span> 
                    <strong><?php echo strtoupper(htmlspecialchars($namaAkun)); ?></strong>
                    &bull; SALDO NORMAL: <strong><?php echo strtoupper($posisiNormal); ?></strong>
                </div>
                <div class="doc-period-pill">
                    <i class="fas fa-calendar-alt text-amber-600"></i>
                    <span>Periode: <strong><?php echo htmlspecialchars($data['periode_1'] ?? '-'); ?></strong></span>
                </div>
            </div>

            <!-- 3. KPI RINGKASAN EKSEKUTIF -->
            <div class="kpi-grid">
                <div class="kpi-box">
                    <span class="kpi-label">Saldo Awal Periode</span>
                    <span class="kpi-value"><?php echo format_rupiah($saldoAwal, 0); ?></span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Total Mutasi Debit</span>
                    <span class="kpi-value income"><?php echo format_rupiah($totDebit, 0); ?></span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Total Mutasi Kredit</span>
                    <span class="kpi-value expense"><?php echo format_rupiah($totKredit, 0); ?></span>
                </div>
                <div class="kpi-box highlight">
                    <span class="kpi-label">Saldo Akhir Periode</span>
                    <span class="kpi-value <?php echo $saldoAkhir >= 0 ? 'income' : 'expense'; ?>">
                        <?php echo format_rupiah($saldoAkhir, 0); ?>
                    </span>
                </div>
            </div>

            <!-- 4. TABEL BUKU BESAR -->
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 12%;">Tanggal</th>
                        <th style="width: 16%;">No. Bukti</th>
                        <th style="width: 32%;">Keterangan Transaksi</th>
                        <th class="text-right" style="width: 13%;">Debit (Rp)</th>
                        <th class="text-right" style="width: 13%;">Kredit (Rp)</th>
                        <th class="text-right" style="width: 14%;">Saldo (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- SALDO AWAL -->
                    <tr style="background-color: #f8fafc; font-weight: 700;">
                        <td class="font-mono"><?php echo !empty($data['tanggal_mulai']) ? date('d/m/Y', strtotime($data['tanggal_mulai'])) : '-'; ?></td>
                        <td class="font-mono text-muted">-</td>
                        <td>SALDO AWAL PERIODE</td>
                        <td class="text-right font-mono text-muted">-</td>
                        <td class="text-right font-mono text-muted">-</td>
                        <td class="text-right font-mono"><?php echo number_format($saldoAwal, 0, ',', '.'); ?></td>
                    </tr>

                    <?php if (empty($transaksi)): ?>
                    <tr>
                        <td colspan="6" class="text-center" style="color: #64748b; font-style: italic; padding: 25px;">
                            Tidak ada transaksi mutasi pada periode ini.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php 
                        $runningSaldo = $saldoAwal;
                        foreach($transaksi as $trx): 
                            $d = (float)($trx['debit'] ?? 0);
                            $k = (float)($trx['kredit'] ?? 0);
                            if ($posisiNormal == 'Debit') {
                                $runningSaldo += ($d - $k);
                            } else {
                                $runningSaldo += ($k - $d);
                            }
                        ?>
                        <tr>
                            <td class="font-mono"><?php echo date('d/m/Y', strtotime($trx['tanggal'])); ?></td>
                            <td class="font-mono font-bold text-slate-700"><?php echo htmlspecialchars($trx['no_transaksi']); ?></td>
                            <td><?php echo htmlspecialchars($trx['deskripsi']); ?></td>
                            <td class="text-right font-mono <?php echo $d > 0 ? '' : 'text-muted'; ?>">
                                <?php echo $d > 0 ? number_format($d, 0, ',', '.') : '-'; ?>
                            </td>
                            <td class="text-right font-mono <?php echo $k > 0 ? '' : 'text-muted'; ?>">
                                <?php echo $k > 0 ? number_format($k, 0, ',', '.') : '-'; ?>
                            </td>
                            <td class="text-right font-mono font-bold">
                                <?php echo number_format($runningSaldo, 0, ',', '.'); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- TOTAL MUTASI -->
                    <tr class="total-row">
                        <td colspan="3" class="text-right">TOTAL MUTASI PERIODE</td>
                        <td class="text-right font-mono"><?php echo number_format($totDebit, 0, ',', '.'); ?></td>
                        <td class="text-right font-mono"><?php echo number_format($totKredit, 0, ',', '.'); ?></td>
                        <td class="text-right font-mono text-muted">-</td>
                    </tr>

                    <!-- SALDO AKHIR -->
                    <tr class="grand-total-row">
                        <td colspan="5" class="text-right">SALDO AKHIR PERIODE (<?php echo strtoupper($posisiNormal); ?>)</td>
                        <td class="text-right font-mono"><?php echo number_format($saldoAkhir, 0, ',', '.'); ?></td>
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
                    <p class="sig-post">Penyusun Buku Besar</p>
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
