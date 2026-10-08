<?php
$p = $data['rfq'];
$c = $data['perusahaan'] ?? [];
$logoPath = !empty($c['path_logo']) ? BASEURL . '/' . $c['path_logo'] : BASEURL . '/img/logo_jatim.png';
$kota = !empty($c['kota_laporan']) ? $c['kota_laporan'] : 'Mojokerto';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Penawaran (RFQ) - <?php echo htmlspecialchars($p['no_rfq']); ?> - <?php echo htmlspecialchars($c['nama_perusahaan'] ?? 'SimpleAkunting'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>/css/print.css?v=3.6">
</head>
<body class="print-preview-mode">

    <!-- Floating Top Toolbar (Screen Only) -->
    <div class="screen-container no-print">
        <div class="toolbar-print">
            <div class="toolbar-left">
                <a href="<?php echo BASEURL; ?>/rfq/lihat/<?php echo $p['id_rfq']; ?>" class="btn-toolbar-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
                <div>
                    <div style="font-weight: 800; font-size: 12px; color: #ffffff; display: flex; align-items: center; gap: 8px;">
                        <span>Permintaan Penawaran Harga (RFQ)</span>
                        <span class="toolbar-badge"><?php echo htmlspecialchars($p['status'] ?? 'Draft'); ?></span>
                    </div>
                    <div style="font-size: 11px; color: #94a3b8;">No: <?php echo htmlspecialchars($p['no_rfq']); ?> &bull; Sistem SimpleAkunting</div>
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
                    <span class="kop-badge" style="background-color: #4338ca;">REQUEST FOR QUOTE</span>
                    <div class="kop-date">Dicetak: <?php echo date('d/m/Y H:i'); ?> WIB</div>
                </div>
            </header>

            <div class="kop-divider"></div>
            <div class="kop-divider-sub"></div>

            <!-- 2. JUDUL DOKUMEN -->
            <div class="doc-title-block">
                <h2 class="doc-title">SURAT PERMINTAAN PENAWARAN HARGA (RFQ)</h2>
                <div class="doc-subtitle">PERMOHONAN RESMI PENAWARAN HARGA &amp; SPESIFIKASI DARI PEMASOK</div>
                <div class="doc-period-pill">
                    <i class="fas fa-file-lines text-indigo-600"></i>
                    <span>Nomor RFQ: <strong><?php echo htmlspecialchars($p['no_rfq']); ?></strong></span>
                </div>
            </div>

            <!-- 3. INFORMASI TRANSAKSI -->
            <div class="details-grid">
                <div class="details-box">
                    <div class="details-box-title">Kepada Pemasok / Vendor:</div>
                    <table class="details-table">
                        <tr>
                            <td class="label-col">Nama Vendor</td>
                            <td class="value-col">: <?php echo htmlspecialchars($p['nama_pemasok']); ?></td>
                        </tr>
                        <tr>
                            <td class="label-col">Alamat</td>
                            <td class="value-col">: <?php echo nl2br(htmlspecialchars($p['alamat_pemasok'] ?? '-')); ?></td>
                        </tr>
                    </table>
                </div>

                <div class="details-box">
                    <div class="details-box-title">Waktu &amp; Status Pengajuan:</div>
                    <table class="details-table">
                        <tr>
                            <td class="label-col">Tanggal RFQ</td>
                            <td class="value-col font-mono">: <?php echo tanggal_indo($p['tanggal']); ?></td>
                        </tr>
                        <tr>
                            <td class="label-col">Batas Waktu Penawaran</td>
                            <td class="value-col font-mono" style="color: #b91c1c;">: <?php echo !empty($p['tgl_kadaluarsa']) ? tanggal_indo($p['tgl_kadaluarsa']) : '14 Hari Kerja'; ?></td>
                        </tr>
                        <tr>
                            <td class="label-col">Status Permintaan</td>
                            <td class="value-col">
                                : <span class="badge-status <?php echo ($p['status'] == 'Ordered') ? 'badge-lunas' : 'badge-belum-lunas'; ?>">
                                    <?php echo strtoupper($p['status'] ?? 'DRAFT'); ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- 4. TABEL RINCIAN ITEM -->
            <table class="print-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 35px;">#</th>
                        <th style="width: 120px;">Kode Barang</th>
                        <th>Deskripsi Spesifikasi Bahan / Barang</th>
                        <th class="text-center" style="width: 80px;">Kuantitas</th>
                        <th class="text-right" style="width: 130px;">Est. Harga Beli</th>
                        <th class="text-right" style="width: 140px;">Est. Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    foreach($p['details'] as $item): 
                    ?>
                    <tr>
                        <td class="text-center font-mono"><?php echo $no++; ?></td>
                        <td class="font-mono"><?php echo htmlspecialchars($item['kode_barang']); ?></td>
                        <td><strong><?php echo htmlspecialchars($item['nama_barang']); ?></strong></td>
                        <td class="text-center font-mono"><strong><?php echo (float)$item['kuantitas']; ?></strong></td>
                        <td class="text-right font-mono"><?php echo number_format($item['harga_estimasi'], 0, ',', '.'); ?></td>
                        <td class="text-right font-mono font-bold"><?php echo number_format($item['subtotal_estimasi'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="grand-total-row">
                        <td colspan="5" class="text-right" style="font-size: 10pt;">TOTAL ESTIMASI NILAI PERMINTAAN:</td>
                        <td class="text-right font-mono" style="font-size: 11pt;"><?php echo format_rupiah($p['total_estimasi'], 0); ?></td>
                    </tr>
                </tfoot>
            </table>

            <!-- 5. TERBILANG BOX -->
            <div class="terbilang-box">
                <strong>Terbilang:</strong>
                <span># <?php echo terbilang_rupiah($p['total_estimasi']); ?> #</span>
            </div>

            <?php if(!empty($p['keterangan'])): ?>
            <div style="margin-bottom: 16px; font-size: 8pt; color: #475569; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px 12px;">
                <strong style="color: #0f172a; text-transform: uppercase;">Catatan &amp; Ketentuan Tambahan:</strong>
                <p style="margin: 2px 0 0 0;"><?php echo nl2br(htmlspecialchars($p['keterangan'])); ?></p>
            </div>
            <?php endif; ?>

            <!-- 6. PENGESAHAN / TANDA TANGAN (3 KOLOM KORPORASI) -->
            <div class="ttd-container">
                <div class="ttd-city-date">
                    <?php echo htmlspecialchars($kota); ?>, <?php echo tanggal_indo($p['tanggal']); ?>
                </div>
                <div class="ttd-grid">
                    <div class="ttd-box">
                        <div class="ttd-title">Diterima Pemasok</div>
                        <div>
                            <div class="ttd-name"><?php echo htmlspecialchars($p['nama_pemasok']); ?></div>
                            <div class="ttd-role">Tanda Tangan Konfirmasi</div>
                        </div>
                    </div>
                    <div class="ttd-box">
                        <div class="ttd-title">Bagian Pengadaan (Purchasing)</div>
                        <div>
                            <div class="ttd-name"><?php echo htmlspecialchars($c['nama_akuntan'] ?? 'Petugas Purchasing'); ?></div>
                            <div class="ttd-role">Staff Pengadaan</div>
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
                    Dokumen permintaan penawaran ini diterbitkan secara sah oleh sistem <strong>SimpleAkunting</strong>.
                </div>
                <div class="font-mono">
                    Halaman 1 / 1
                </div>
            </footer>

        </div>
    </div>

</body>
</html>
