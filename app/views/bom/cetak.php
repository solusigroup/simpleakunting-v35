<?php
$b = $data['bom'];
$c = $data['perusahaan'] ?? [];
$logoPath = !empty($c['path_logo']) ? BASEURL . '/' . $c['path_logo'] : BASEURL . '/img/logo_jatim.png';
$kota = !empty($c['kota_laporan']) ? $c['kota_laporan'] : 'Mojokerto';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill of Materials (BOM) - <?php echo htmlspecialchars($b['nama_bom']); ?> - <?php echo htmlspecialchars($c['nama_perusahaan'] ?? 'SimpleAkunting'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>/css/print.css?v=3.6">
</head>
<body class="print-preview-mode">

    <!-- Floating Top Toolbar (Screen Only) -->
    <div class="screen-container no-print">
        <div class="toolbar-print">
            <div class="toolbar-left">
                <a href="<?php echo BASEURL; ?>/bom/lihat/<?php echo $b['id_bom']; ?>" class="btn-toolbar-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
                <div>
                    <div style="font-weight: 800; font-size: 12px; color: #ffffff; display: flex; align-items: center; gap: 8px;">
                        <span>Formulasi Resep Standar Produksi (BOM)</span>
                        <span class="toolbar-badge">STANDAR BAKU</span>
                    </div>
                    <div style="font-size: 11px; color: #94a3b8;"><?php echo htmlspecialchars($b['nama_bom']); ?> &bull; Sistem SimpleAkunting</div>
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
                    <span class="kop-badge" style="background-color: #0f766e;">MASTER RESEP BOM</span>
                    <div class="kop-date">Dicetak: <?php echo date('d/m/Y H:i'); ?> WIB</div>
                </div>
            </header>

            <div class="kop-divider"></div>
            <div class="kop-divider-sub"></div>

            <!-- 2. JUDUL DOKUMEN -->
            <div class="doc-title-block">
                <h2 class="doc-title">BILL OF MATERIALS (BOM) / RESEP PRODUKSI</h2>
                <div class="doc-subtitle">STANDARISASI FORMULA KOMPOSISI &amp; ESTIMASI BIAYA POKOK PRODUKSI</div>
                <div class="doc-period-pill">
                    <i class="fas fa-flask text-teal-600"></i>
                    <span>Resep: <strong><?php echo htmlspecialchars($b['nama_bom']); ?></strong></span>
                </div>
            </div>

            <!-- 3. INFORMASI PRODUK & RESEP -->
            <div class="details-grid">
                <div class="details-box">
                    <div class="details-box-title">Spesifikasi Produk Jadi Target:</div>
                    <table class="details-table">
                        <tr>
                            <td class="label-col">Nama Produk</td>
                            <td class="value-col">: <?php echo htmlspecialchars($b['nama_produk']); ?></td>
                        </tr>
                        <tr>
                            <td class="label-col">Kode Barang</td>
                            <td class="value-col font-mono">: <?php echo htmlspecialchars($b['kode_barang']); ?></td>
                        </tr>
                    </table>
                </div>

                <div class="details-box">
                    <div class="details-box-title">Ringkasan Biaya Resep (Per Unit):</div>
                    <table class="details-table">
                        <tr>
                            <td class="label-col">Estimasi Biaya</td>
                            <td class="value-col font-mono" style="color: #0d9488; font-size: 10pt;">: <?php echo format_rupiah($b['total_biaya_estimasi'], 2); ?></td>
                        </tr>
                        <tr>
                            <td class="label-col">Jumlah Komponen</td>
                            <td class="value-col font-mono">: <?php echo count($b['details']); ?> Jenis Bahan Baku</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- 4. TABEL RINCIAN KOMPONEN RESEP -->
            <table class="print-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 35px;">#</th>
                        <th style="width: 120px;">Kode Bahan</th>
                        <th>Deskripsi Komponen / Bahan Baku</th>
                        <th class="text-center" style="width: 90px;">Takaran / Qty</th>
                        <th class="text-center" style="width: 75px;">Satuan</th>
                        <th class="text-right" style="width: 125px;">Biaya Satuan</th>
                        <th class="text-right" style="width: 140px;">Subtotal Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $totalEstimasi = 0;
                    foreach($b['details'] as $item): 
                        $sub = $item['biaya_satuan'] * $item['jumlah'];
                        $totalEstimasi += $sub;
                    ?>
                    <tr>
                        <td class="text-center font-mono"><?php echo $no++; ?></td>
                        <td class="font-mono"><?php echo htmlspecialchars($item['kode_barang']); ?></td>
                        <td><strong><?php echo htmlspecialchars($item['nama_barang']); ?></strong></td>
                        <td class="text-center font-mono"><strong><?php echo (float)$item['jumlah']; ?></strong></td>
                        <td class="text-center"><?php echo htmlspecialchars($item['satuan_barang']); ?></td>
                        <td class="text-right font-mono"><?php echo number_format($item['biaya_satuan'], 2, ',', '.'); ?></td>
                        <td class="text-right font-mono font-bold"><?php echo number_format($sub, 2, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="grand-total-row">
                        <td colspan="6" class="text-right" style="font-size: 10pt;">TOTAL ESTIMASI BIAYA BAHAN BAKU PER UNIT:</td>
                        <td class="text-right font-mono" style="font-size: 11pt;"><?php echo format_rupiah($b['total_biaya_estimasi'], 2); ?></td>
                    </tr>
                </tfoot>
            </table>

            <!-- 5. TERBILANG BOX -->
            <div class="terbilang-box">
                <strong>Terbilang:</strong>
                <span># <?php echo terbilang_rupiah($b['total_biaya_estimasi']); ?> #</span>
            </div>

            <!-- 6. PENGESAHAN / TANDA TANGAN (3 KOLOM KORPORASI) -->
            <div class="ttd-container">
                <div class="ttd-city-date">
                    <?php echo htmlspecialchars($kota); ?>, <?php echo date('d F Y'); ?>
                </div>
                <div class="ttd-grid">
                    <div class="ttd-box">
                        <div class="ttd-title">Diformulasikan Oleh (R&amp;D/Teknis)</div>
                        <div>
                            <div class="ttd-name">Tim Formulasi &amp; Produksi</div>
                            <div class="ttd-role">Analis Resep Produk</div>
                        </div>
                    </div>
                    <div class="ttd-box">
                        <div class="ttd-title">Diverifikasi Oleh (Akuntan Biaya)</div>
                        <div>
                            <div class="ttd-name"><?php echo htmlspecialchars($c['nama_akuntan'] ?? 'Akuntan Biaya'); ?></div>
                            <div class="ttd-role">Pemeriksa HPP Standar</div>
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
                    Spesifikasi standar resep ini resmi dan dihasilkan oleh modul Manufaktur <strong>SimpleAkunting</strong>.
                </div>
                <div class="font-mono">
                    Halaman 1 / 1
                </div>
            </footer>

        </div>
    </div>

</body>
</html>
