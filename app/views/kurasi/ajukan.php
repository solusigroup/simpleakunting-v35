<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['judul']); ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo BASEURL; ?>/img/logos/69ff050a79c48_favicon-SimpleAkunting.png">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --op-green-dark: #023826;
            --op-green-base: #034b34;
            --op-cream: #fafbf9;
            --op-border-fine: rgba(2, 56, 38, 0.16);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--op-green-dark);
            margin: 0;
            padding: 0;
        }

        .binder-outer-frame {
            min-height: 100vh;
            padding: 30px 20px;
        }

        .binder-inner-sheet {
            max-width: 860px;
            margin: 0 auto;
            background-color: var(--op-cream);
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35);
            border: 2px solid #084c36;
            padding: 40px;
        }

        @media (max-width: 768px) {
            .binder-inner-sheet {
                padding: 24px 18px;
            }
        }

        .btn-tech-primary {
            background-color: var(--op-green-base);
            color: #ffffff !important;
            font-size: 0.95rem;
            font-weight: 700;
            border-radius: 100px;
            padding: 12px 28px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-tech-primary:hover {
            background-color: #0b6348;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

<div class="binder-outer-frame">
    <div class="binder-inner-sheet">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <a href="<?php echo BASEURL; ?>/kurasi" class="text-decoration-none text-dark fw-bold small">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Katalog Kurasi
            </a>
            <div class="d-flex align-items-center gap-2">
                <?php if ($data['is_logged_in']): ?>
                    <a href="<?php echo BASEURL; ?>/dashboard" class="badge bg-light text-dark border text-decoration-none p-2 px-3 rounded-pill">
                        <i class="bi bi-grid-fill me-1"></i> Dashboard
                    </a>
                <?php endif; ?>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-mono-tech p-2 px-3">
                    FORMULIR KURASI v3.6
                </span>
            </div>
        </div>

        <!-- ALERT KHUSUS SUPERADMIN: AKSES HASIL KURASI -->
        <?php if (!empty($data['is_superadmin'])): ?>
            <div class="alert alert-success d-flex flex-wrap justify-content-between align-items-center mb-4 rounded-4 shadow-sm border-success p-3">
                <div class="me-2 mb-2 mb-md-0">
                    <i class="bi bi-shield-check fs-4 me-2 text-success align-middle"></i>
                    <strong>Akses Superadmin:</strong> Anda dapat melihat, mengelola, dan memvalidasi seluruh hasil pengajuan kurasi yang masuk.
                    <?php if (!empty($data['pending_count'])): ?>
                        <span class="badge bg-danger rounded-pill ms-2"><?php echo $data['pending_count']; ?> Menunggu Kurasi</span>
                    <?php endif; ?>
                </div>
                <a href="<?php echo BASEURL; ?>/kurasi/kelola" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-table me-1"></i> Kelola Hasil Kurasi Superadmin ↗
                </a>
            </div>
        <?php endif; ?>

        <?php Flash::flash(); ?>

        <div class="mb-4">
            <h2 class="fw-bold mb-1" style="color: var(--op-green-dark);">Pendaftaran & Kurasi UMKM Layak</h2>
            <p class="text-muted small mb-0">
                Lengkapi formulir di bawah ini untuk menilai kelayakan akuntansi usaha Anda sesuai standar SAK EP dan membuka akses pembiayaan perbankan, BUMDesa, dan program CSR Jawa Timur.
            </p>
        </div>

        <div class="card p-4 border-0 shadow-sm rounded-4 bg-white">
            <form action="<?php echo BASEURL; ?>/kurasi/ajukan" method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nama Usaha / BUMDesa <span class="text-danger">*</span></label>
                        <input type="text" name="nama_usaha" class="form-control" placeholder="Contoh: Kopi Bromo Mandiri" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nama Pemilik / Penanggung Jawab <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pemilik" class="form-control" placeholder="Nama lengkap Anda" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nomor WhatsApp / Telepon <span class="text-danger">*</span></label>
                        <input type="tel" name="telepon" class="form-control" placeholder="Contoh: 081234567890" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Kabupaten / Kota di Jawa Timur <span class="text-danger">*</span></label>
                        <input type="text" name="kota" class="form-control" placeholder="Contoh: Kabupaten Pasuruan" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Sektor Usaha <span class="text-danger">*</span></label>
                        <select name="sektor" class="form-select" required>
                            <option value="">Pilih Sektor...</option>
                            <option value="Agribisnis & Pertanian">Agribisnis & Pertanian</option>
                            <option value="Kuliner & Makanan-Minuman">Kuliner & Makanan-Minuman</option>
                            <option value="Kriya & Kerajinan Tangan">Kriya & Kerajinan Tangan</option>
                            <option value="Tekstil, Batik & Fashion">Tekstil, Batik & Fashion</option>
                            <option value="BUMDesa (Multi-Unit)">BUMDesa (Multi-Unit)</option>
                            <option value="Perdagangan & Jasa">Perdagangan & Jasa</option>
                            <option value="Manufaktur & Bengkel">Manufaktur & Bengkel</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Lama Usaha Berjalan</label>
                        <select name="lama_usaha" class="form-select">
                            <option value="1">Kurang dari 1 Tahun</option>
                            <option value="2" selected>1 - 3 Tahun</option>
                            <option value="4">Lebih dari 3 Tahun</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Omset Rata-rata per Bulan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="omset" class="form-control" placeholder="Contoh: 25000000" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Estimasi Laba Bersih per Bulan (Rp)</label>
                        <input type="number" name="laba_bersih" class="form-control" placeholder="Contoh: 6000000">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Total Nilai Aset Usaha (Rp)</label>
                        <input type="number" name="aset_usaha" class="form-control" placeholder="Contoh: 40000000">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Metode Pencatatan Pembukuan Saat Ini</label>
                        <select name="metode_pembukuan" class="form-select">
                            <option value="simpleakunting">SimpleAkunting / Software Akuntansi SAK EP</option>
                            <option value="excel">Microsoft Excel / Spreadsheet</option>
                            <option value="manual" selected>Buku Kas Manual / Nota Fisik</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Deskripsi Singkat Produk / Rencana Pengembangan Usaha</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Ceritakan produk unggulan, kapasitas produksi, dan tujuan permodalan yang diharapkan..."></textarea>
                    </div>
                    <div class="col-12 pt-3">
                        <button type="submit" class="btn-tech-primary w-100 justify-content-center">
                            <i class="bi bi-send-fill me-1"></i> Kirim Pendaftaran Kurasi UMKM
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
