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
            --op-green-bright: #0ea968;
            --op-green-soft: #eaf5ee;
            --op-cream: #fafbf9;
            --op-text-main: #14241d;
            --op-text-muted: #4e6357;
            --op-border-fine: rgba(2, 56, 38, 0.16);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--op-green-dark);
            color: var(--op-text-main);
            margin: 0;
            padding: 0;
        }

        .font-mono-tech {
            font-family: 'Space Grotesk', monospace;
            letter-spacing: -0.02em;
        }

        .binder-outer-frame {
            background-color: var(--op-green-dark);
            min-height: 100vh;
            padding: 24px 20px;
            background-image: 
                radial-gradient(circle at 50% 0%, rgba(14, 169, 104, 0.15) 0%, transparent 60%),
                radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
            background-size: 100% 100%, 28px 28px;
        }

        .binder-inner-sheet {
            max-width: 1240px;
            margin: 0 auto;
            background-color: var(--op-cream);
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35);
            position: relative;
            border: 2px solid #084c36;
            padding: 30px 45px 60px 45px;
        }

        @media (max-width: 768px) {
            .binder-outer-frame {
                padding: 10px 6px;
            }
            .binder-inner-sheet {
                padding: 20px 18px 40px 18px;
            }
        }

        .top-tech-bar {
            border: 1px solid var(--op-border-fine);
            border-radius: 100px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            padding: 8px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 35px;
        }

        .btn-tech-primary {
            background-color: var(--op-green-base);
            color: #ffffff !important;
            font-size: 0.84rem;
            font-weight: 700;
            border-radius: 100px;
            padding: 8px 20px;
            border: 1px solid var(--op-green-dark);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-tech-primary:hover {
            background-color: #0b6348;
        }

        .filter-pill {
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
            color: var(--op-text-muted);
            border: 1px solid var(--op-border-fine);
            background: #ffffff;
            transition: all 0.2s;
            display: inline-block;
        }

        .filter-pill.active, .filter-pill:hover {
            background-color: var(--op-green-base);
            color: #ffffff;
            border-color: var(--op-green-base);
        }

        .curated-card {
            border: 1px solid var(--op-border-fine);
            border-radius: 16px;
            background: #ffffff;
            padding: 24px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.2s;
        }

        .curated-card:hover {
            border-color: var(--op-green-bright);
            box-shadow: 0 12px 30px rgba(3, 75, 52, 0.1);
            transform: translateY(-2px);
        }

        .badge-grade-a {
            background-color: #034b34;
            color: #ffffff;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 100px;
            font-size: 0.75rem;
        }

        .badge-grade-b {
            background-color: #0b6348;
            color: #ffffff;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 100px;
            font-size: 0.75rem;
        }

        .metric-summary-card {
            border: 1px solid var(--op-border-fine);
            border-radius: 12px;
            padding: 16px 20px;
            background: #ffffff;
        }
    </style>
</head>
<body>

<div class="binder-outer-frame">
    <div class="binder-inner-sheet">
        <!-- Top Nav Bar -->
        <header class="top-tech-bar">
            <div class="d-flex align-items-center gap-2">
                <a href="<?php echo BASEURL; ?>/" class="text-decoration-none text-dark fw-bold font-mono-tech d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left"></i> BERANDA
                </a>
                <span class="text-muted">|</span>
                <span class="font-mono-tech fw-bold text-success" style="font-size: 0.85rem;">
                    KATALOG KURASI UMKM LAYAK
                </span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="<?php echo BASEURL; ?>/kurasi/ajukan" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold">
                    <i class="bi bi-plus-circle me-1"></i> Ajukan Kurasi
                </a>
                <?php if ($data['is_logged_in']): ?>
                    <a href="<?php echo BASEURL; ?>/dashboard" class="btn-tech-primary">
                        Dashboard
                    </a>
                <?php else: ?>
                    <a href="<?php echo BASEURL; ?>/login" class="btn-tech-primary">
                        Masuk Sistem
                    </a>
                <?php endif; ?>
            </div>
        </header>

        <!-- Header Kurasi Title & Metrics -->
        <div class="mb-4">
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-mono-tech mb-2">
                STANDARDISASI SAK EP &bull; SIMPLEAKUNTING v3.6
            </span>
            <h1 class="fw-bold mb-2" style="font-size: 2.2rem; color: var(--op-green-dark);">
                Direktori Kurasi UMKM Layak Pembiayaan
            </h1>
            <p class="text-muted" style="max-width: 800px;">
                Daftar pelaku usaha mikro, kecil, menengah, dan BUMDesa di Jawa Timur yang telah melewati penilaian kelayakan akuntansi, kesehatan rasio finansial, dan audit trail transparan.
            </p>
        </div>

        <!-- 4 Summary Metrics -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="metric-summary-card">
                    <div class="text-muted small">Total UMKM Terkurasi</div>
                    <div class="fs-4 fw-bold text-dark"><?php echo $data['summary_metrics']['total_terkurasi']; ?> Usaha</div>
                    <small class="text-success"><i class="bi bi-shield-check"></i> SAK EP Valid</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="metric-summary-card">
                    <div class="text-muted small">Grade A (Bankable)</div>
                    <div class="fs-4 fw-bold text-success"><?php echo $data['summary_metrics']['grade_a_bankable']; ?> Usaha</div>
                    <small class="text-muted">Siap Akses Perbankan</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="metric-summary-card">
                    <div class="text-muted small">Fasilitasi Pembiayaan</div>
                    <div class="fs-4 fw-bold text-primary"><?php echo $data['summary_metrics']['total_pembiayaan']; ?></div>
                    <small class="text-muted">KUR & Kemitraan CSR</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="metric-summary-card">
                    <div class="text-muted small">Tingkat Kelayakan</div>
                    <div class="fs-4 fw-bold text-dark"><?php echo $data['summary_metrics']['tingkat_kelayakan']; ?></div>
                    <small class="text-success"><i class="bi bi-graph-up-arrow"></i> Kepatuhan Tinggi</small>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="card p-3 mb-4 border-0 shadow-sm rounded-4 bg-white">
            <form method="GET" action="<?php echo BASEURL; ?>/kurasi" class="row g-2 align-items-center">
                <div class="col-md-7">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?php echo BASEURL; ?>/kurasi?kategori=semua" class="filter-pill <?php echo ($data['selected_kategori'] == 'semua') ? 'active' : ''; ?>">Semua</a>
                        <a href="<?php echo BASEURL; ?>/kurasi?kategori=agribisnis" class="filter-pill <?php echo ($data['selected_kategori'] == 'agribisnis') ? 'active' : ''; ?>">Agribisnis</a>
                        <a href="<?php echo BASEURL; ?>/kurasi?kategori=kuliner" class="filter-pill <?php echo ($data['selected_kategori'] == 'kuliner') ? 'active' : ''; ?>">Kuliner & Mamin</a>
                        <a href="<?php echo BASEURL; ?>/kurasi?kategori=kriya" class="filter-pill <?php echo ($data['selected_kategori'] == 'kriya') ? 'active' : ''; ?>">Kriya & Fashion</a>
                        <a href="<?php echo BASEURL; ?>/kurasi?kategori=bumdes" class="filter-pill <?php echo ($data['selected_kategori'] == 'bumdes') ? 'active' : ''; ?>">BUMDesa</a>
                        <a href="<?php echo BASEURL; ?>/kurasi?kategori=jasa" class="filter-pill <?php echo ($data['selected_kategori'] == 'jasa') ? 'active' : ''; ?>">Jasa & Lainnya</a>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Cari nama UMKM, kota, atau produk..." value="<?php echo htmlspecialchars($data['pencarian']); ?>">
                        <button class="btn btn-outline-success" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Grid UMKM List -->
        <?php if (empty($data['umkm_list'])): ?>
            <div class="p-5 text-center bg-white rounded-4 border">
                <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
                <h5 class="fw-bold">Tidak ada UMKM yang cocok dengan filter atau pencarian.</h5>
                <p class="text-muted small">Coba ubah kata kunci atau pilih kategori lain.</p>
                <a href="<?php echo BASEURL; ?>/kurasi" class="btn btn-sm btn-outline-success rounded-pill px-3">Reset Filter</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($data['umkm_list'] as $item): ?>
                    <div class="col-md-6 col-lg-6">
                        <div class="curated-card">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="badge bg-light text-dark border font-mono-tech me-1" style="font-size: 0.72rem;">
                                            <i class="bi bi-geo-alt-fill text-danger me-1"></i><?php echo htmlspecialchars($item['kluster']); ?>
                                        </span>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle font-mono-tech" style="font-size: 0.72rem;">
                                            <?php echo htmlspecialchars($item['sektor']); ?>
                                        </span>
                                    </div>
                                    <span class="<?php echo ($item['grade'] == 'A') ? 'badge-grade-a' : 'badge-grade-b'; ?>">
                                        GRADE <?php echo $item['grade']; ?> &bull; <?php echo $item['skor']; ?>/100
                                    </span>
                                </div>

                                <h4 class="fw-bold mb-1" style="color: var(--op-green-dark);"><?php echo htmlspecialchars($item['nama']); ?></h4>
                                <div class="text-muted small mb-3">
                                    <i class="bi bi-person me-1"></i>Pengelola: <?php echo htmlspecialchars($item['pemilik']); ?>
                                </div>

                                <p class="small text-muted mb-3" style="line-height: 1.5;">
                                    <?php echo htmlspecialchars($item['deskripsi']); ?>
                                </p>

                                <div class="p-3 bg-light rounded-3 small mb-3 border">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <span class="text-muted d-block">Rata-rata Omset:</span>
                                            <strong class="text-success fs-6"><?php echo $item['omset_bulan']; ?></strong>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted d-block">Tren Pertumbuhan:</span>
                                            <strong class="text-primary fs-6"><?php echo $item['pertumbuhan']; ?></strong>
                                        </div>
                                        <div class="col-12 pt-2 border-top">
                                            <span class="text-muted d-block">Rekomendasi Pembiayaan:</span>
                                            <strong class="text-dark"><?php echo $item['rekomendasi_dana']; ?></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                                <span class="badge bg-success text-white rounded-pill px-3 py-1 font-mono-tech" style="font-size: 0.72rem;">
                                    <i class="bi bi-check-circle-fill me-1"></i> <?php echo $item['status']; ?>
                                </span>
                                <a href="<?php echo BASEURL; ?>/kurasi/ajukan?mitra=<?php echo urlencode($item['nama']); ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                    Hubungkan Kemitraan ↗
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- CTA Ajukan Kurasi -->
        <div class="mt-5 p-4 rounded-4 bg-white border text-center">
            <h4 class="fw-bold mb-2">Ingin UMKM Anda Masuk dalam Kurasi SimpleAkunting v3.6?</h4>
            <p class="text-muted small mb-3" style="max-width: 650px; margin: 0 auto;">
                Dapatkan pendampingan standardisasi SAK EP, penilaian kelayakan perbankan (Bankable Score), serta peluang fasilitasi pembiayaan dari mitra bank dan BUMD Jawa Timur.
            </p>
            <a href="<?php echo BASEURL; ?>/kurasi/ajukan" class="btn-tech-primary px-4 py-2">
                <i class="bi bi-pencil-square me-1"></i> Daftarkan Usaha Anda Sekarang
            </a>
        </div>
    </div>
</div>

</body>
</html>
