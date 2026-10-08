<?php
$j = $data['jurnal'];
$c = $data['perusahaan'] ?? [];
$logoPath = !empty($c['path_logo']) ? BASEURL . '/' . $c['path_logo'] : BASEURL . '/img/logo_jatim.png';
$kota = !empty($c['kota_laporan']) ? $c['kota_laporan'] : 'Mojokerto';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Memorial Jurnal - <?php echo htmlspecialchars($j['no_transaksi']); ?> - <?php echo htmlspecialchars($c['nama_perusahaan'] ?? 'SimpleAkunting'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>/css/print.css?v=3.6">
</head>
<body class="print-preview-mode">

    <!-- Floating Top Toolbar (Screen Only) -->
    <div class="screen-container no-print">
        <div class="toolbar-print">
            <div class="toolbar-left">
                <a href="<?php echo BASEURL; ?>/jurnal/detail/<?php echo $j['id_jurnal']; ?>" class="btn-toolbar-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
                <div>
                    <div style="font-weight: 800; font-size: 12px; color: #ffffff; display: flex; align-items: center; gap: 8px;">
                        <span>Bukti Jurnal Memorial (Journal Voucher)</span>
                        <span class="toolbar-badge"><?php echo htmlspecialchars($j['sumber_jurnal'] ?? 'Umum'); ?></span>
                    </div>
                    <div style="font-size: 11px; color: #94a3b8;">No: <?php echo htmlspecialchars($j['no_transaksi']); ?> &bull; Sistem SimpleAkunting</div>
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
                    <span class="kop-badge" style="background-color: #334155;">JOURNAL VOUCHER</span>
                    <div class="kop-date">Dicetak: <?php echo date('d/m/Y H:i'); ?> WIB</div>
                </div>
            </header>

            <div class="kop-divider"></div>
            <div class="kop-divider-sub"></div>

            <!-- 2. JUDUL DOKUMEN -->
            <div class="doc-title-block">
                <h2 class="doc-title">BUKTI MEMORIAL / JURNAL UMUM</h2>
                <div class="doc-subtitle">DOKUMEN OTORISASI PEMBUKUAN AKUNTANSI GANDA (DOUBLE ENTRY)</div>
                <div class="doc-period-pill">
                    <i class="fas fa-book-bookmark text-slate-700"></i>
                    <span>No. Transaksi: <strong><?php echo htmlspecialchars($j['no_transaksi']); ?></strong></span>
                </div>
            </div>

            <!-- 3. INFORMASI TRANSAKSI -->
            <div class="details-grid">
                <div class="details-box">
                    <div class="details-box-title">Identitas Dokumen Jurnal:</div>
                    <table class="details-table">
                        <tr>
                            <td class="label-col">No. Bukti</td>
                            <td class="value-col font-mono">: <?php echo htmlspecialchars($j['no_transaksi']); ?></td>
                        </tr>
                        <tr>
                            <td class="label-col">Tanggal Buku</td>
                            <td class="value-col font-mono">: <?php echo tanggal_indo($j['tanggal']); ?></td>
                        </tr>
                    </table>
                </div>

                <div class="details-box">
                    <div class="details-box-title">Sumber &amp; Validasi:</div>
                    <table class="details-table">
                        <tr>
                            <td class="label-col">Modul Sumber</td>
                            <td class="value-col">: <?php echo htmlspecialchars($j['sumber_jurnal']); ?></td>
                        </tr>
                        <tr>
                            <td class="label-col">Status Entri</td>
                            <td class="value-col">
                                : <span class="badge-status <?php echo ($j['is_locked'] == 1) ? 'badge-lunas' : 'badge-belum-lunas'; ?>">
                                    <?php echo ($j['is_locked'] == 1) ? 'TERKUNCI (SISTEM)' : 'DAPAT DIEDIT'; ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <?php if(!empty($j['deskripsi'])): ?>
            <div style="margin-bottom: 16px; font-size: 8.5pt; color: #1e293b; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 12px;">
                <strong style="color: #475569; text-transform: uppercase; font-size: 7.5pt; display: block; margin-bottom: 2px;">Uraian / Deskripsi Transaksi:</strong>
                <p style="margin: 0; font-weight: 500;"><?php echo nl2br(htmlspecialchars($j['deskripsi'])); ?></p>
            </div>
            <?php endif; ?>

            <!-- 4. TABEL RINCIAN POS JURNAL (DEBIT & KREDIT) -->
            <table class="print-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 35px;">#</th>
                        <th style="width: 120px;">Kode Akun</th>
                        <th>Nama Akun Perkiraan (COA)</th>
                        <th class="text-right" style="width: 150px;">Debit (Rp)</th>
                        <th class="text-right" style="width: 150px;">Kredit (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $totalDebit = 0;
                    $totalKredit = 0;
                    foreach($j['details'] as $detail): 
                        $totalDebit += (float)$detail['debit'];
                        $totalKredit += (float)$detail['kredit'];
                        $isKredit = ((float)$detail['kredit'] > 0);
                    ?>
                    <tr>
                        <td class="text-center font-mono"><?php echo $no++; ?></td>
                        <td class="font-mono font-bold" style="color: #0f172a;"><?php echo htmlspecialchars($detail['kode_akun']); ?></td>
                        <td style="<?php echo $isKredit ? 'padding-left: 28px;' : ''; ?>">
                            <strong><?php echo htmlspecialchars($detail['nama_akun'] ?? '-'); ?></strong>
                        </td>
                        <td class="text-right font-mono"><?php echo ($detail['debit'] > 0) ? number_format($detail['debit'], 2, ',', '.') : '-'; ?></td>
                        <td class="text-right font-mono"><?php echo ($detail['kredit'] > 0) ? number_format($detail['kredit'], 2, ',', '.') : '-'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="grand-total-row">
                        <td colspan="3" class="text-right" style="font-size: 10pt;">TOTAL DEBIT &amp; KREDIT:</td>
                        <td class="text-right font-mono" style="font-size: 10pt;"><?php echo format_rupiah($totalDebit, 2); ?></td>
                        <td class="text-right font-mono" style="font-size: 10pt;"><?php echo format_rupiah($totalKredit, 2); ?></td>
                    </tr>
                </tfoot>
            </table>

            <!-- 5. TERBILANG BOX -->
            <div class="terbilang-box">
                <strong>Terbilang:</strong>
                <span># <?php echo terbilang_rupiah($totalDebit); ?> #</span>
            </div>

            <!-- 6. PENGESAHAN / TANDA TANGAN (3 KOLOM KORPORASI) -->
            <div class="ttd-container">
                <div class="ttd-city-date">
                    <?php echo htmlspecialchars($kota); ?>, <?php echo tanggal_indo($j['tanggal']); ?>
                </div>
                <div class="ttd-grid">
                    <div class="ttd-box">
                        <div class="ttd-title">Dibuat Oleh (Akuntan / Kasir)</div>
                        <div>
                            <div class="ttd-name"><?php echo htmlspecialchars($c['nama_akuntan'] ?? 'Bagian Pembukuan'); ?></div>
                            <div class="ttd-role">Pencatat Entri Jurnal</div>
                        </div>
                    </div>
                    <div class="ttd-box">
                        <div class="ttd-title">Diperiksa Oleh (Verifikator)</div>
                        <div>
                            <div class="ttd-name">Supervisor Akuntansi</div>
                            <div class="ttd-role">Pemeriksa &amp; Rekonsiliasi</div>
                        </div>
                    </div>
                    <div class="ttd-box">
                        <div class="ttd-title">Disetujui Oleh (Pimpinan)</div>
                        <div>
                            <div class="ttd-name"><?php echo htmlspecialchars($c['nama_direktur'] ?? 'Pimpinan / Direktur'); ?></div>
                            <div class="ttd-role">Direktur / Kepala Unit</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 7. FOOTER RESMI KOMPUTERISASI -->
            <footer class="paper-footer">
                <div>
                    Bukti memorial ini diterbitkan secara sah dan terekam di buku besar sistem <strong>SimpleAkunting</strong>.
                </div>
                <div class="font-mono">
                    Halaman 1 / 1
                </div>
            </footer>

        </div>
    </div>

</body>
</html>
