<?php
$p = $data['penjualan'];
$c = $data['perusahaan'] ?? [];
$logoPath = !empty($c['path_logo']) ? BASEURL . '/' . $c['path_logo'] : BASEURL . '/img/logo_jatim.png';
$kota = !empty($c['kota_laporan']) ? $c['kota_laporan'] : 'Mojokerto';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Penjualan - <?php echo htmlspecialchars($p['no_faktur']); ?> - <?php echo htmlspecialchars($c['nama_perusahaan'] ?? 'SimpleAkunting'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>/css/print.css?v=3.6">
</head>
<body class="print-preview-mode">

    <!-- Floating Top Toolbar (Screen Only) -->
    <div class="screen-container no-print">
        <div class="toolbar-print">
            <div class="toolbar-left">
                <a href="<?php echo BASEURL; ?>/penjualan/lihat/<?php echo $p['id_penjualan']; ?>" class="btn-toolbar-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
                <div>
                    <div style="font-weight: 800; font-size: 12px; color: #ffffff; display: flex; align-items: center; gap: 8px;">
                        <span>Faktur Penjualan Resmi</span>
                        <span class="toolbar-badge"><?php echo htmlspecialchars($p['status_pembayaran'] ?? 'LUNAS'); ?></span>
                    </div>
                    <div style="font-size: 11px; color: #94a3b8;">No: <?php echo htmlspecialchars($p['no_faktur']); ?> &bull; Sistem SimpleAkunting</div>
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
                    <span class="kop-badge">FAKTUR PENJUALAN</span>
                    <div class="kop-date">Dicetak: <?php echo date('d/m/Y H:i'); ?> WIB</div>
                </div>
            </header>

            <div class="kop-divider"></div>
            <div class="kop-divider-sub"></div>

            <!-- 2. JUDUL DOKUMEN -->
            <div class="doc-title-block">
                <h2 class="doc-title">FAKTUR PENJUALAN / INVOICE</h2>
                <div class="doc-subtitle">BUKTI RESMI PENYERAHAN BARANG &amp; PENAGIHAN PIUTANG</div>
                <div class="doc-period-pill">
                    <i class="fas fa-receipt text-amber-600"></i>
                    <span>Nomor Faktur: <strong><?php echo htmlspecialchars($p['no_faktur']); ?></strong></span>
                </div>
            </div>

            <!-- 3. INFORMASI TRANSAKSI & PELANGGAN -->
            <div class="details-grid">
                <div class="details-box">
                    <div class="details-box-title">Tagihan Ditujukan Kepada:</div>
                    <table class="details-table">
                        <tr>
                            <td class="label-col">Pelanggan</td>
                            <td class="value-col">: <?php echo htmlspecialchars($p['nama_pelanggan']); ?></td>
                        </tr>
                        <tr>
                            <td class="label-col">Alamat</td>
                            <td class="value-col">: <?php echo nl2br(htmlspecialchars($p['alamat_pelanggan'] ?? '-')); ?></td>
                        </tr>
                        <tr>
                            <td class="label-col">Metode Bayar</td>
                            <td class="value-col">: <?php echo htmlspecialchars($p['metode_pembayaran'] ?? 'Tunai'); ?></td>
                        </tr>
                    </table>
                </div>

                <div class="details-box">
                    <div class="details-box-title">Rincian Dokumen &amp; Termin:</div>
                    <table class="details-table">
                        <tr>
                            <td class="label-col">Tanggal Faktur</td>
                            <td class="value-col font-mono">: <?php echo tanggal_indo($p['tanggal_faktur']); ?></td>
                        </tr>
                        <tr>
                            <td class="label-col">Jatuh Tempo</td>
                            <td class="value-col font-mono">: <?php echo !empty($p['jatuh_tempo']) ? tanggal_indo($p['jatuh_tempo']) : '-'; ?></td>
                        </tr>
                        <tr>
                            <td class="label-col">Status Pelunasan</td>
                            <td class="value-col">
                                : <span class="badge-status <?php echo ($p['status_pembayaran'] === 'Lunas') ? 'badge-lunas' : 'badge-belum-lunas'; ?>">
                                    <?php echo strtoupper($p['status_pembayaran'] ?? 'LUNAS'); ?>
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
                        <th style="width: 110px;">Kode Barang</th>
                        <th>Deskripsi Barang / Jasa</th>
                        <th class="text-center" style="width: 70px;">Qty</th>
                        <th class="text-right" style="width: 120px;">Harga Satuan</th>
                        <th class="text-right" style="width: 130px;">Subtotal (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $subtotalKotor = 0;
                    foreach($p['details'] as $item): 
                        $subtotalKotor += $item['subtotal'];
                    ?>
                    <tr>
                        <td class="text-center font-mono"><?php echo $no++; ?></td>
                        <td class="font-mono"><?php echo htmlspecialchars($item['kode_barang']); ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($item['nama_barang']); ?></strong>
                            <?php if(!empty($item['nama_akun'])): ?>
                                <br><small style="color: #64748b; font-size: 7.5pt;">(Pos Pendapatan: <?php echo htmlspecialchars($item['nama_akun']); ?>)</small>
                            <?php endif; ?>
                        </td>
                        <td class="text-center font-mono"><strong><?php echo (float)$item['kuantitas']; ?></strong></td>
                        <td class="text-right font-mono"><?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                        <td class="text-right font-mono font-bold"><?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="subtotal-row">
                        <td colspan="5" class="text-right">Subtotal DPP:</td>
                        <td class="text-right font-mono font-bold"><?php echo number_format($subtotalKotor, 0, ',', '.'); ?></td>
                    </tr>
                    <?php if(!empty($p['diskon']) && $p['diskon'] > 0): ?>
                    <tr>
                        <td colspan="5" class="text-right" style="color: #047857;">Potongan / Diskon:</td>
                        <td class="text-right font-mono" style="color: #047857;">- <?php echo number_format($p['diskon'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if(!empty($p['pajak']) && $p['pajak'] > 0): ?>
                    <tr>
                        <td colspan="5" class="text-right" style="color: #b91c1c;">Pajak (PPN):</td>
                        <td class="text-right font-mono" style="color: #b91c1c;">+ <?php echo number_format($p['pajak'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr class="grand-total-row">
                        <td colspan="5" class="text-right" style="font-size: 10pt;">TOTAL PEMBAYARAN:</td>
                        <td class="text-right font-mono" style="font-size: 11pt;"><?php echo format_rupiah($p['total'], 0); ?></td>
                    </tr>
                </tfoot>
            </table>

            <!-- 5. TERBILANG BOX -->
            <div class="terbilang-box">
                <strong>Terbilang:</strong>
                <span># <?php echo terbilang_rupiah($p['total']); ?> #</span>
            </div>

            <?php if(!empty($p['keterangan'])): ?>
            <div style="margin-bottom: 16px; font-size: 8pt; color: #475569; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px 12px;">
                <strong style="color: #0f172a; text-transform: uppercase;">Catatan Faktur:</strong>
                <p style="margin: 2px 0 0 0;"><?php echo nl2br(htmlspecialchars($p['keterangan'])); ?></p>
            </div>
            <?php endif; ?>

            <!-- 6. PENGESAHAN / TANDA TANGAN (3 KOLOM KORPORASI) -->
            <div class="ttd-container">
                <div class="ttd-city-date">
                    <?php echo htmlspecialchars($kota); ?>, <?php echo tanggal_indo($p['tanggal_faktur']); ?>
                </div>
                <div class="ttd-grid">
                    <div class="ttd-box">
                        <div class="ttd-title">Diterima Oleh (Pelanggan)</div>
                        <div>
                            <div class="ttd-name"><?php echo htmlspecialchars($p['nama_pelanggan']); ?></div>
                            <div class="ttd-role">Tanda Tangan &amp; Stempel</div>
                        </div>
                    </div>
                    <div class="ttd-box">
                        <div class="ttd-title">Dibuat Oleh (Admin/Kasir)</div>
                        <div>
                            <div class="ttd-name"><?php echo htmlspecialchars($c['nama_akuntan'] ?? 'Bagian Penjualan'); ?></div>
                            <div class="ttd-role">Petugas Administrasi</div>
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
                    Dokumen ini sah dan dicetak secara otomatis oleh sistem akuntansi <strong>SimpleAkunting</strong>.
                </div>
                <div class="font-mono">
                    Halaman 1 / 1
                </div>
            </footer>

        </div>
    </div>

</body>
</html>
