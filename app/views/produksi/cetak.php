<?php
$p = $data['produksi'];
$b = $data['bom'] ?? null;
$c = $data['perusahaan'] ?? [];
$logoPath = !empty($c['path_logo']) ? BASEURL . '/' . $c['path_logo'] : BASEURL . '/img/logo_jatim.png';
$kota = !empty($c['kota_laporan']) ? $c['kota_laporan'] : 'Mojokerto';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Perintah Kerja Produksi (WO) - <?php echo htmlspecialchars($p['no_produksi']); ?> - <?php echo htmlspecialchars($c['nama_perusahaan'] ?? 'SimpleAkunting'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>/css/print.css?v=3.6">
</head>
<body class="print-preview-mode">

    <!-- Floating Top Toolbar (Screen Only) -->
    <div class="screen-container no-print">
        <div class="toolbar-print">
            <div class="toolbar-left">
                <a href="<?php echo BASEURL; ?>/produksi/lihat/<?php echo $p['id_produksi']; ?>" class="btn-toolbar-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
                <div>
                    <div style="font-weight: 800; font-size: 12px; color: #ffffff; display: flex; align-items: center; gap: 8px;">
                        <span>Surat Perintah Kerja Produksi (Work Order)</span>
                        <span class="toolbar-badge"><?php echo strtoupper($p['status']); ?></span>
                    </div>
                    <div style="font-size: 11px; color: #94a3b8;">No. WO: <?php echo htmlspecialchars($p['no_produksi']); ?> &bull; Sistem SimpleAkunting</div>
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
                    <span class="kop-badge" style="background-color: #047857;">WORK ORDER (SPK)</span>
                    <div class="kop-date">Dicetak: <?php echo date('d/m/Y H:i'); ?> WIB</div>
                </div>
            </header>

            <div class="kop-divider"></div>
            <div class="kop-divider-sub"></div>

            <!-- 2. JUDUL DOKUMEN -->
            <div class="doc-title-block">
                <h2 class="doc-title">SURAT PERINTAH KERJA PRODUKSI (WORK ORDER)</h2>
                <div class="doc-subtitle">DOKUMEN INSTRUKSI RESMI OPERASIONAL PENGOLAHAN MANUFAKTUR</div>
                <div class="doc-period-pill">
                    <i class="fas fa-industry text-emerald-600"></i>
                    <span>Nomor Produksi: <strong><?php echo htmlspecialchars($p['no_produksi']); ?></strong></span>
                </div>
            </div>

            <!-- 3. KPI RINGKASAN PRODUKSI -->
            <div class="kpi-grid">
                <div class="kpi-box">
                    <span class="kpi-label">Produk Hasil Jadi</span>
                    <span class="kpi-value" style="font-size: 9.5pt;"><?php echo htmlspecialchars($p['nama_produk']); ?></span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Target Output</span>
                    <span class="kpi-value font-mono"><?php echo (float)$p['jumlah_target']; ?> Unit</span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Total Biaya Aktual</span>
                    <span class="kpi-value font-mono"><?php echo !empty($p['total_biaya_aktual']) ? format_rupiah($p['total_biaya_aktual']) : '-'; ?></span>
                </div>
                <div class="kpi-box highlight">
                    <span class="kpi-label">Status WO</span>
                    <span class="badge-status <?php echo ($p['status'] === 'Selesai') ? 'badge-lunas' : 'badge-belum-lunas'; ?>" style="font-size: 9pt;">
                        <?php echo strtoupper($p['status']); ?>
                    </span>
                </div>
            </div>

            <!-- 4. INFORMASI DETIL WORK ORDER -->
            <div class="details-grid">
                <div class="details-box">
                    <div class="details-box-title">Spesifikasi Output Produksi:</div>
                    <table class="details-table">
                        <tr>
                            <td class="label-col">Kode Produk</td>
                            <td class="value-col font-mono">: <?php echo htmlspecialchars($p['kode_produk']); ?></td>
                        </tr>
                        <tr>
                            <td class="label-col">Nama Barang</td>
                            <td class="value-col">: <?php echo htmlspecialchars($p['nama_produk']); ?></td>
                        </tr>
                        <tr>
                            <td class="label-col">Resep (BOM)</td>
                            <td class="value-col">: <?php echo htmlspecialchars($p['nama_bom']); ?></td>
                        </tr>
                    </table>
                </div>

                <div class="details-box">
                    <div class="details-box-title">Timeline &amp; Pengesahan Sistem:</div>
                    <table class="details-table">
                        <tr>
                            <td class="label-col">Tanggal Mulai</td>
                            <td class="value-col font-mono">: <?php echo tanggal_indo($p['tanggal']); ?></td>
                        </tr>
                        <tr>
                            <td class="label-col">Ref. Jurnal</td>
                            <td class="value-col font-mono">: <?php echo !empty($p['id_jurnal']) ? 'JRNL-#' . $p['id_jurnal'] . ' (Tercatat Otomatis)' : 'Menunggu Penyelesaian'; ?></td>
                        </tr>
                        <tr>
                            <td class="label-col">Aliran Stok</td>
                            <td class="value-col">: Bahan Baku Out &rarr; Produk Jadi In</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- 5. TABEL RINCIAN FORMULASI BAHAN BAKU BERDASARKAN BOM -->
            <?php if (!empty($b['details'])): ?>
            <div style="font-weight: 800; font-size: 8.5pt; color: #1e293b; text-transform: uppercase; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                <i class="fas fa-boxes-stacked text-amber-600"></i>
                <span>Rincian Kebutuhan Bahan Baku (Formula Resep / BOM)</span>
            </div>
            <table class="print-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 35px;">#</th>
                        <th style="width: 120px;">Kode Bahan</th>
                        <th>Deskripsi Komponen / Bahan Baku</th>
                        <th class="text-center" style="width: 90px;">Rasio Resep</th>
                        <th class="text-center" style="width: 80px;">Satuan</th>
                        <th class="text-right" style="width: 140px;">Total Dibutuhkan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $targetQty = (float)$p['jumlah_target'];
                    foreach($b['details'] as $item): 
                        $totalDibutuhkan = (float)$item['jumlah'] * $targetQty;
                    ?>
                    <tr>
                        <td class="text-center font-mono"><?php echo $no++; ?></td>
                        <td class="font-mono"><?php echo htmlspecialchars($item['kode_barang']); ?></td>
                        <td><strong><?php echo htmlspecialchars($item['nama_barang']); ?></strong></td>
                        <td class="text-center font-mono"><?php echo (float)$item['jumlah']; ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($item['satuan_barang']); ?></td>
                        <td class="text-right font-mono font-bold"><?php echo number_format($totalDibutuhkan, 2, ',', '.'); ?> <?php echo htmlspecialchars($item['satuan_barang']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>

            <!-- 6. PENGESAHAN / TANDA TANGAN (3 KOLOM KORPORASI) -->
            <div class="ttd-container">
                <div class="ttd-city-date">
                    <?php echo htmlspecialchars($kota); ?>, <?php echo tanggal_indo($p['tanggal']); ?>
                </div>
                <div class="ttd-grid">
                    <div class="ttd-box">
                        <div class="ttd-title">Pelaksana Produksi (Operator)</div>
                        <div>
                            <div class="ttd-name">Kepala Regu / Mandor</div>
                            <div class="ttd-role">Divisi Pabrik &amp; Operasional</div>
                        </div>
                    </div>
                    <div class="ttd-box">
                        <div class="ttd-title">Pengendali Mutu &amp; Gudang</div>
                        <div>
                            <div class="ttd-name"><?php echo htmlspecialchars($c['nama_akuntan'] ?? 'Supervisor Gudang'); ?></div>
                            <div class="ttd-role">QC &amp; Inventori Bahan</div>
                        </div>
                    </div>
                    <div class="ttd-box">
                        <div class="ttd-title">Disetujui Oleh (Pimpinan)</div>
                        <div>
                            <div class="ttd-name"><?php echo htmlspecialchars($c['nama_direktur'] ?? 'Pimpinan / Direktur'); ?></div>
                            <div class="ttd-role">Manajer Pabrik / Direktur</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 7. FOOTER RESMI KOMPUTERISASI -->
            <footer class="paper-footer">
                <div>
                    Dokumen instruksi kerja ini sah dan diterbitkan resmi oleh sistem produksi <strong>SimpleAkunting</strong>.
                </div>
                <div class="font-mono">
                    Halaman 1 / 1
                </div>
            </footer>

        </div>
    </div>

</body>
</html>
