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
            --op-green-mid: #0b6348;
            --op-green-bright: #0ea968;
            --op-green-soft: #eaf5ee;
            --op-green-mint: #d5ecd8;
            --op-cream: #fafbf9;
            --op-text-main: #14241d;
            --op-text-muted: #4e6357;
            --op-border-fine: rgba(2, 56, 38, 0.16);
            --op-radius: 14px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--op-green-dark);
            color: var(--op-text-main);
            margin: 0;
            padding: 0;
            line-height: 1.55;
            -webkit-font-smoothing: antialiased;
        }

        .font-mono-tech {
            font-family: 'Space Grotesk', monospace;
            letter-spacing: -0.02em;
        }

        /* --- BINDER RING FRAME PERFORATION STYLING --- */
        .binder-outer-frame {
            background-color: var(--op-green-dark);
            min-height: 100vh;
            padding: 24px 20px;
            background-image: 
                radial-gradient(circle at 50% 0%, rgba(14, 169, 104, 0.18) 0%, transparent 60%),
                radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
            background-size: 100% 100%, 28px 28px;
        }

        .binder-inner-sheet {
            max-width: 1240px;
            margin: 0 auto;
            background-color: var(--op-cream);
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.12);
            position: relative;
            overflow: hidden;
            border: 2px solid #084c36;
        }

        /* Perforated Binder Rings on Left & Right */
        .binder-punches-left,
        .binder-punches-right {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 32px;
            z-index: 10;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            align-items: center;
            padding: 40px 0;
            pointer-events: none;
        }

        .binder-punches-left {
            left: 8px;
        }

        .binder-punches-right {
            right: 8px;
        }

        .punch-ring {
            width: 16px;
            height: 16px;
            background: #02291c;
            border: 3px solid #eaf5ee;
            border-radius: 50%;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.6), 0 1px 2px rgba(255,255,255,0.8);
            position: relative;
        }

        .punch-ring::after {
            content: '';
            position: absolute;
            top: 50%;
            left: -8px;
            right: -8px;
            height: 2px;
            background: rgba(2, 41, 28, 0.25);
            z-index: -1;
        }

        .sheet-content {
            padding: 24px 52px 60px 52px;
        }

        @media (max-width: 768px) {
            .binder-outer-frame {
                padding: 10px 6px;
            }
            .binder-punches-left,
            .binder-punches-right {
                display: none;
            }
            .sheet-content {
                padding: 16px 20px 40px 20px;
            }
        }

        /* --- TOP TECHNICAL CAPSULE BAR --- */
        .top-tech-bar {
            border: 1px solid var(--op-border-fine);
            border-radius: 100px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            padding: 8px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 42px;
        }

        .tech-pill-tag {
            font-size: 0.76rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.08em;
            color: var(--op-green-base);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .tech-pill-tag .live-dot {
            width: 8px;
            height: 8px;
            background-color: var(--op-green-bright);
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 3px rgba(14, 169, 104, 0.25);
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }

        .nav-link-custom {
            font-size: 0.86rem;
            font-weight: 600;
            color: var(--op-text-main);
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 100px;
            transition: all 0.2s;
        }

        .nav-link-custom:hover {
            color: var(--op-green-bright);
            background: var(--op-green-soft);
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
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-tech-primary:hover {
            background-color: var(--op-green-mid);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(3, 75, 52, 0.25);
        }

        .btn-tech-outline {
            border: 1px solid var(--op-border-fine);
            background: #ffffff;
            color: var(--op-text-main) !important;
            font-size: 0.84rem;
            font-weight: 700;
            border-radius: 100px;
            padding: 8px 18px;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-tech-outline:hover {
            background: var(--op-green-soft);
            border-color: var(--op-green-base);
        }

        /* --- HERO SECTION --- */
        .hero-title {
            font-size: clamp(2.2rem, 4.2vw, 3.6rem);
            font-weight: 800;
            line-height: 1.08;
            letter-spacing: -0.035em;
            color: var(--op-green-dark);
            margin-bottom: 24px;
        }

        .hero-desc {
            font-size: 1.05rem;
            color: var(--op-text-muted);
            line-height: 1.65;
            margin-bottom: 24px;
        }

        /* --- HERO APP WINDOW MOCKUP --- */
        .app-window-frame {
            border: 1.5px solid rgba(2, 56, 38, 0.2);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 20px 45px rgba(2, 56, 38, 0.09), 0 2px 6px rgba(0,0,0,0.04);
            margin: 40px 0 60px 0;
            overflow: hidden;
            position: relative;
        }

        .app-window-header {
            background: #f1f6f3;
            border-bottom: 1.5px solid rgba(2, 56, 38, 0.12);
            padding: 10px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .window-dots {
            display: flex;
            gap: 6px;
        }

        .window-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .dot-red { background: #ff5f56; }
        .dot-yellow { background: #ffbd2e; }
        .dot-green { background: #27c93f; }

        .app-window-body {
            padding: 24px;
            background: #ffffff;
        }

        .metric-badge-box {
            border: 1px solid var(--op-border-fine);
            border-radius: 10px;
            padding: 14px 18px;
            background: #fbfdfc;
        }

        .floating-curated-card {
            border: 1.5px solid var(--op-green-base);
            border-radius: 14px;
            background: #ffffff;
            padding: 20px;
            box-shadow: 0 12px 30px rgba(3, 75, 52, 0.15);
        }

        /* --- 4-STEP BLUEPRINT ARCHITECTURE (GETTING OPERATIONAL) --- */
        .step-tech-card {
            border: 1px solid var(--op-border-fine);
            border-radius: 12px;
            background: #ffffff;
            padding: 20px 24px;
            margin-bottom: 16px;
            transition: all 0.25s;
        }

        .step-tech-card:hover {
            border-color: var(--op-green-base);
            box-shadow: 0 8px 24px rgba(3, 75, 52, 0.08);
            transform: translateX(4px);
        }

        .step-number {
            font-size: 0.78rem;
            font-weight: 800;
            color: var(--op-green-base);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
        }

        .step-tag-pill {
            font-size: 0.68rem;
            font-weight: 700;
            background: var(--op-green-soft);
            color: var(--op-green-base);
            padding: 3px 10px;
            border-radius: 100px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-block;
        }

        /* --- MANIFESTO GREEN SAGE CARDS (AS SEEN IN REFERENCE) --- */
        .manifesto-container {
            background-color: var(--op-green-mint);
            border: 1.5px solid var(--op-green-base);
            border-radius: 16px;
            padding: 32px 30px;
            margin-top: 50px;
            position: relative;
            overflow: hidden;
        }

        .manifesto-col {
            border-right: 1px solid rgba(2, 56, 38, 0.2);
            padding: 0 24px;
        }

        .manifesto-col:last-child {
            border-right: none;
        }

        @media (max-width: 991px) {
            .manifesto-col {
                border-right: none;
                border-bottom: 1px solid rgba(2, 56, 38, 0.2);
                padding: 16px 0;
            }
            .manifesto-col:last-child {
                border-bottom: none;
            }
        }

        /* --- CURASI DIRECTORY CARDS --- */
        .curated-umkm-card {
            border: 1px solid var(--op-border-fine);
            border-radius: 14px;
            background: #ffffff;
            padding: 22px;
            transition: all 0.25s;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .curated-umkm-card:hover {
            border-color: var(--op-green-bright);
            box-shadow: 0 10px 28px rgba(3, 75, 52, 0.1);
            transform: translateY(-3px);
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

        /* Form Interactive Box */
        .calc-box {
            background: #ffffff;
            border: 1.5px solid var(--op-green-base);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 12px 36px rgba(0,0,0,0.06);
        }
    </style>
</head>
<body>

<div class="binder-outer-frame">
    <div class="binder-inner-sheet">
        <!-- Punch Holes Visuals -->
        <div class="binder-punches-left">
            <?php for($i=0; $i<18; $i++): ?>
                <div class="punch-ring"></div>
            <?php endfor; ?>
        </div>
        <div class="binder-punches-right">
            <?php for($i=0; $i<18; $i++): ?>
                <div class="punch-ring"></div>
            <?php endfor; ?>
        </div>

        <div class="sheet-content">
            <!-- 1. TOP TECHNICAL CAPSULE BAR -->
            <header class="top-tech-bar">
                <div class="d-flex align-items-center gap-3">
                    <span class="tech-pill-tag font-mono-tech">
                        <span class="live-dot"></span>
                        OPERATE // SIMPLEAKUNTING v3.5
                    </span>
                    <span class="badge bg-light text-dark border d-none d-md-inline-block" style="font-size: 0.7rem;">
                        SAK EP &bull; JATIM INTEGRATED
                    </span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="#kurasi" class="nav-link-custom d-none d-sm-inline-block">Kurasi UMKM</a>
                    <a href="#arsitektur" class="nav-link-custom d-none d-md-inline-block">Alur Operasional</a>
                    <a href="#simulasi" class="nav-link-custom d-none d-lg-inline-block">Cek Kelayakan</a>
                    <a href="<?php echo BASEURL; ?>/kurasi" class="btn-tech-outline">
                        <i class="bi bi-patch-check-fill text-success"></i> Menu Kurasi ↗
                    </a>
                    <?php if ($data['is_logged_in']): ?>
                        <a href="<?php echo BASEURL; ?>/dashboard" class="btn-tech-primary">
                            <i class="bi bi-grid-fill"></i> Masuk Dashboard
                        </a>
                    <?php else: ?>
                        <a href="<?php echo BASEURL; ?>/login" class="btn-tech-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Masuk Sistem
                        </a>
                    <?php endif; ?>
                </div>
            </header>

            <!-- 2. HERO SECTION -->
            <section class="py-2">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white border mb-3 font-mono-tech small text-muted">
                            <i class="bi bi-shield-check text-success"></i>
                            Puspa Candra &bull; Kearifan Lokal Jawa Timur
                        </div>
                        <h1 class="hero-title">
                            Sistem Akuntansi Terpadu untuk Akselerasi & Kurasi UMKM Layak
                        </h1>
                    </div>
                    <div class="col-lg-5">
                        <p class="hero-desc">
                            SimpleAkunting v3.5 menghubungkan UMKM, BUMDesa, dan perbankan melalui tata kelola pembukuan terstandar SAK EP. 
                            Mewujudkan kurasi kelayakan usaha yang terverifikasi, transparan, dan siap akses pembiayaan.
                        </p>
                        <div class="d-flex flex-wrap gap-2 pt-2">
                            <a href="#kurasi" class="btn-tech-primary py-2 px-4">
                                <i class="bi bi-award-fill"></i> Jelajahi Kurasi UMKM
                            </a>
                            <a href="#simulasi" class="btn-tech-outline py-2 px-3">
                                <i class="bi bi-calculator"></i> Cek Skor Kelayakan
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 3. INTERACTIVE HERO APP WINDOW MOCKUP -->
                <div class="app-window-frame">
                    <div class="app-window-header">
                        <div class="window-dots">
                            <span class="window-dot dot-red"></span>
                            <span class="window-dot dot-yellow"></span>
                            <span class="window-dot dot-green"></span>
                        </div>
                        <div class="font-mono-tech text-muted small">
                            <i class="bi bi-lock-fill text-success me-1"></i> simpleakunting.id / tenant: bumdes-jatim / sak-ep / status: terkurasi-grade-a
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-mono-tech" style="font-size: 0.72rem;">
                            LIVE V3.5 AUDIT-READY
                        </span>
                    </div>

                    <div class="app-window-body">
                        <div class="row g-4 align-items-center">
                            <!-- Left: Metric Snapshot -->
                            <div class="col-lg-7">
                                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                    <div>
                                        <h5 class="fw-bold mb-0">Dashboard Akuntansi & Monitoring Unit Usaha</h5>
                                        <small class="text-muted">Kepatuhan Standar Akuntansi Keuangan Entitas Privat (SAK EP)</small>
                                    </div>
                                    <span class="badge bg-primary text-white rounded-pill px-3 py-1">Periode Aktif 2026</span>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-sm-4">
                                        <div class="metric-badge-box">
                                            <div class="text-muted small">Pendapatan Bersih</div>
                                            <div class="fw-bold fs-5 text-success">Rp 48.500.000</div>
                                            <small class="text-success"><i class="bi bi-arrow-up"></i> +24% YoY</small>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="metric-badge-box">
                                            <div class="text-muted small">Arus Kas Operasi</div>
                                            <div class="fw-bold fs-5 text-primary">Rp 36.200.000</div>
                                            <small class="text-muted">Likuid & Sehat</small>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="metric-badge-box">
                                            <div class="text-muted small">Rasio Likuiditas</div>
                                            <div class="fw-bold fs-5 text-dark">2.45x</div>
                                            <small class="text-success"><i class="bi bi-check-circle"></i> Standar Bankable</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Checklist compliance bar -->
                                <div class="p-3 bg-light rounded-3 border">
                                    <div class="row text-center font-mono-tech small">
                                        <div class="col-4 border-end">
                                            <i class="bi bi-check2-circle text-success fs-6"></i>
                                            <div class="fw-bold">Jurnal Otomatis</div>
                                            <span class="text-muted" style="font-size: 0.72rem;">Multi-Unit Terpadu</span>
                                        </div>
                                        <div class="col-4 border-end">
                                            <i class="bi bi-check2-circle text-success fs-6"></i>
                                            <div class="fw-bold">SAK EP Selesai</div>
                                            <span class="text-muted" style="font-size: 0.72rem;">Laba Rugi & Neraca</span>
                                        </div>
                                        <div class="col-4">
                                            <i class="bi bi-check2-circle text-success fs-6"></i>
                                            <div class="fw-bold">Lolos Kurasi</div>
                                            <span class="text-muted" style="font-size: 0.72rem;">Skor 95.4 / 100</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Curated Status Floating Card -->
                            <div class="col-lg-5">
                                <div class="floating-curated-card">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-mono-tech mb-1" style="font-size: 0.7rem;">
                                                SERTIFIKASI KELAYAKAN
                                            </span>
                                            <h6 class="fw-bold mb-0">Status Kurasi UMKM</h6>
                                        </div>
                                        <span class="badge-grade-a fs-6">GRADE A</span>
                                    </div>

                                    <div class="p-2 mb-3 bg-light rounded border small">
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span class="text-muted">Kategori Kelayakan:</span>
                                            <strong class="text-success">Bankable / Layak Kredit</strong>
                                        </div>
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span class="text-muted">Rekomendasi Pembiayaan:</span>
                                            <strong>KUR Komersial s/d Rp 500 Juta</strong>
                                        </div>
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span class="text-muted">Kepatuhan Pembukuan:</span>
                                            <span class="text-primary fw-bold">100% SAK EP Valid</span>
                                        </div>
                                        <div class="d-flex justify-content-between py-1">
                                            <span class="text-muted">Mitra Pembiayaan:</span>
                                            <span>Bank Jatim &bull; BUMD Jatim</span>
                                        </div>
                                    </div>

                                    <a href="<?php echo BASEURL; ?>/kurasi" class="btn btn-sm btn-outline-success w-100 rounded-pill fw-bold">
                                        <i class="bi bi-search me-1"></i> Buka Direktori Lengkap Kurasi
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 4. SECTION: GETTING OPERATIONAL (NUMBERED SYSTEM BLUEPRINT) -->
            <section id="arsitektur" class="py-5 border-top">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <span class="tech-pill-tag font-mono-tech mb-2">
                            01 // ALUR OPERASIONAL
                        </span>
                        <h2 class="fw-bold mb-3" style="font-size: 1.85rem; color: var(--op-green-dark);">
                            Standar Akuntansi Menjadi Fondasi Kepercayaan
                        </h2>
                        <p class="text-muted">
                            Lembaga keuangan, CSR korporasi, dan pemerintah tidak sekadar mencari UMKM yang ramai penjualan, melainkan usaha dengan tata kelola keuangan yang transparan dan dapat dipertanggungjawabkan.
                        </p>
                        <a href="<?php echo BASEURL; ?>/kurasi" class="btn-tech-outline mt-2">
                            <i class="bi bi-compass"></i> Pelajari Standar Kurasi ↗
                        </a>
                    </div>

                    <div class="col-lg-8">
                        <!-- Step 1 -->
                        <div class="step-tech-card">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="step-number font-mono-tech">1. THE STANDARD &bull; SAK EP</div>
                                <span class="step-tag-pill">Standardisasi</span>
                            </div>
                            <h5 class="fw-bold mb-2">Pencatatan Keuangan Terstandar Otomatis</h5>
                            <p class="text-muted small mb-0">
                                Menggantikan istilah konvensional menjadi <strong>Aset</strong>, Kewajiban, dan Ekuitas sesuai SAK EP. 
                                Transaksi penjualan, pembelian, kas, dan persediaan langsung membentuk jurnal dan buku besar otomatis tanpa perlu keahlian akuntansi rumit.
                            </p>
                        </div>

                        <!-- Step 2 -->
                        <div class="step-tech-card">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="step-number font-mono-tech">2. MULTI-UNIT & PROGRAM &bull; DANA DESA & CSR</div>
                                <span class="step-tag-pill">Multi-Bisnis</span>
                            </div>
                            <h5 class="fw-bold mb-2">Tata Kelola Unit Usaha & Sumber Dana Terikat</h5>
                            <p class="text-muted small mb-0">
                                Memfasilitasi BUMDesa dan UMKM dengan banyak cabang atau divisi usaha. Setiap realisasi bantuan pemerintah, CSR perusahaan, atau modal kerja terpantau per pos anggaran secara transparan.
                            </p>
                        </div>

                        <!-- Step 3 -->
                        <div class="step-tech-card">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="step-number font-mono-tech">3. SCORING & KURASI &bull; FINANCIAL HEALTH CHECK</div>
                                <span class="step-tag-pill">Kurasi Finansial</span>
                            </div>
                            <h5 class="fw-bold mb-2">Penilaian Kelayakan (Bankable Score) Objektif</h5>
                            <p class="text-muted small mb-0">
                                Algoritma SimpleAkunting mengukur rasio laba bersih, rasio likuiditas, perputaran kas, serta keteraturan pencatatan untuk memberikan predikat kurasi: Grade A (Bankable), Grade B+ (Layak Mikro), atau Pendampingan.
                            </p>
                        </div>

                        <!-- Step 4 -->
                        <div class="step-tech-card">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="step-number font-mono-tech">4. AUDIT TRAIL & RAPOR BISNIS &bull; REAL-TIME REPORTING</div>
                                <span class="step-tag-pill">Akuntabilitas</span>
                            </div>
                            <h5 class="fw-bold mb-2">Laporan Keuangan Real-Time Siap Audit</h5>
                            <p class="text-muted small mb-0">
                                Laporan Posisi Keuangan, Laba Rugi Komprehensif, Arus Kas, dan Neraca Lajur dapat diunduh instan dalam format PDF/Excel resmi untuk kebutuhan pengajuan pinjaman bank, pajak, dan pertanggungjawaban program.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 5. SECTION: SHOWCASE KURASI UMKM YANG LAYAK -->
            <section id="kurasi" class="py-5 border-top">
                <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
                    <div>
                        <span class="tech-pill-tag font-mono-tech mb-2">
                            02 // DIREKTORI UNGGULAN
                        </span>
                        <h2 class="fw-bold mb-1" style="color: var(--op-green-dark);">
                            Kurasi UMKM yang Layak & Berdaya Saing
                        </h2>
                        <p class="text-muted small mb-0">
                            Daftar UMKM dan BUMDesa di Jawa Timur yang telah terverifikasi melalui SimpleAkunting v3.5 dengan pembukuan sehat dan layak pembiayaan.
                        </p>
                    </div>
                    <div class="pt-2">
                        <a href="<?php echo BASEURL; ?>/kurasi" class="btn-tech-primary">
                            Buka Seluruh Direktori Kurasi (148 UMKM) ↗
                        </a>
                    </div>
                </div>

                <div class="row g-4">
                    <?php foreach ($data['curated_umkm'] as $item): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="curated-umkm-card">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-light text-dark border font-mono-tech" style="font-size: 0.7rem;">
                                            <i class="bi bi-geo-alt-fill text-danger me-1"></i><?php echo htmlspecialchars($item['kluster']); ?>
                                        </span>
                                        <span class="<?php echo ($item['grade'] == 'A') ? 'badge-grade-a' : 'badge-grade-b'; ?>">
                                            GRADE <?php echo $item['grade']; ?> &bull; <?php echo $item['skor']; ?>/100
                                        </span>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($item['nama']); ?></h5>
                                    <div class="text-muted small mb-3">
                                        <i class="bi bi-person me-1"></i><?php echo htmlspecialchars($item['pemilik']); ?> &bull; 
                                        <span class="text-primary"><?php echo htmlspecialchars($item['sektor']); ?></span>
                                    </div>

                                    <div class="p-2 bg-light rounded-3 small mb-3">
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span class="text-muted">Estimasi Omset/Bln:</span>
                                            <strong class="text-success"><?php echo $item['omset_bulan']; ?></strong>
                                        </div>
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span class="text-muted">Pertumbuhan:</span>
                                            <strong class="text-primary"><?php echo $item['pertumbuhan']; ?></strong>
                                        </div>
                                        <div class="d-flex justify-content-between py-1">
                                            <span class="text-muted">Kelayakan:</span>
                                            <span class="fw-bold text-dark"><?php echo $item['status']; ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-mono-tech" style="font-size: 0.68rem;">
                                        <i class="bi bi-patch-check-fill me-1"></i> <?php echo $item['kepatuhan']; ?>
                                    </span>
                                    <a href="<?php echo BASEURL; ?>/kurasi?q=<?php echo urlencode($item['nama']); ?>" class="text-decoration-none fw-bold small text-success">
                                        Detail Profil ↗
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- 6. SECTION: SELF-ASSESSMENT WIDGET (CEK KELAYAKAN UMKM MANDIRI) -->
            <section id="simulasi" class="py-5 border-top">
                <div class="row align-items-center g-4">
                    <div class="col-lg-5">
                        <span class="tech-pill-tag font-mono-tech mb-2">
                            03 // KALKULATOR KELAYAKAN
                        </span>
                        <h2 class="fw-bold mb-3" style="color: var(--op-green-dark);">
                            Apakah Usaha Anda Layak Mendapatkan Pembiayaan?
                        </h2>
                        <p class="text-muted">
                            Lakukan self-assessment instan untuk mengetahui perkiraan skor kelayakan perbankan (Bankable Score) dan jenis pembiayaan yang paling tepat untuk UMKM Anda.
                        </p>
                        <div class="p-3 bg-white border rounded-3 mb-3 small">
                            <div class="fw-bold mb-1"><i class="bi bi-info-circle-fill text-primary me-1"></i> Parameter Penilaian SAK EP:</div>
                            <ul class="mb-0 ps-3 text-muted">
                                <li>Net Profit Margin (Rasio Laba Bersih terhadap Omset)</li>
                                <li>Metode & Konsistensi Pencatatan Buku Keuangan</li>
                                <li>Lama Usaha Beroperasi & Kelancaran Arus Kas</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="calc-box">
                            <h5 class="fw-bold mb-3">Simulasi Cek Skor Kelayakan Usaha</h5>
                            <form id="formSimulasi">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="form-label small fw-semibold">Omset Rata-rata per Bulan (Rp)</label>
                                        <input type="number" id="calcOmset" class="form-control" placeholder="Contoh: 35000000" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label small fw-semibold">Estimasi Laba Bersih per Bulan (Rp)</label>
                                        <input type="number" id="calcLaba" class="form-control" placeholder="Contoh: 7500000" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label small fw-semibold">Lama Usaha Berjalan</label>
                                        <select id="calcUsia" class="form-select">
                                            <option value="1">Kurang dari 1 Tahun</option>
                                            <option value="2">1 - 2 Tahun</option>
                                            <option value="3" selected>3 Tahun ke atas</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label small fw-semibold">Metode Pembukuan Saat Ini</label>
                                        <select id="calcMetode" class="form-select">
                                            <option value="simpleakunting">SimpleAkunting / Software Akuntansi SAK EP</option>
                                            <option value="excel">Microsoft Excel / Spreadsheet</option>
                                            <option value="manual">Buku Kas Manual / Nota Fisik</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn-tech-primary w-100 py-2 justify-content-center">
                                            <i class="bi bi-cpu me-1"></i> Hitung Skor Kelayakan Sekarang
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Hasil Simulasi -->
                            <div id="hasilSimulasi" class="mt-4 p-3 bg-light rounded-3 border d-none">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0">Hasil Evaluasi Kelayakan:</h6>
                                    <span id="hasilGradeBadge" class="badge-grade-a fs-6">GRADE A</span>
                                </div>
                                <div class="row g-2 mb-2 small">
                                    <div class="col-6">
                                        <span class="text-muted">Skor Kelayakan:</span>
                                        <div class="fw-bold fs-5 text-success" id="hasilSkorText">92 / 100</div>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted">Margin Laba Bersih:</span>
                                        <div class="fw-bold fs-5 text-primary" id="hasilNpmText">21.4%</div>
                                    </div>
                                </div>
                                <div class="p-2 bg-white rounded border small mb-3">
                                    <div class="fw-semibold text-dark" id="hasilStatusText">Sangat Layak (Bankable)</div>
                                    <div class="text-muted" id="hasilRekomendasiText">Rekomendasi pembiayaan KUR Bank Jatim / BRI s/d Rp 250 Juta.</div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="<?php echo BASEURL; ?>/kurasi/ajukan" class="btn btn-sm btn-success rounded-pill px-3">
                                        <i class="bi bi-pencil-square me-1"></i> Ajukan Kurasi Resmi
                                    </a>
                                    <a href="<?php echo BASEURL; ?>/login" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                        Mulai Gunakan SimpleAkunting
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 7. SECTION: MANIFESTO SAGE GREEN BOX GRID (REFERENSI OPERATE) -->
            <section class="manifesto-container">
                <div class="row g-4 align-items-stretch">
                    <!-- Column 1 -->
                    <div class="col-lg-4 manifesto-col">
                        <span class="font-mono-tech small text-uppercase fw-bold text-success d-block mb-2">
                            &bull; FILOSOFI KEUANGAN
                        </span>
                        <h3 class="fw-bold mb-3" style="color: var(--op-green-dark); line-height: 1.15;">
                            Aplikasi tidak mencetak laba. Usaha Anda yang melakukannya.
                        </h3>
                        <p class="small text-muted mb-0">
                            SimpleAkunting v3.5 dirancang bukan sekadar sebagai database penyimpan angka, melainkan ruang kerja finansial yang teratur, presisi, dan memberikan kendali penuh di tangan pelaku usaha.
                        </p>
                    </div>

                    <!-- Column 2 -->
                    <div class="col-lg-4 manifesto-col">
                        <span class="font-mono-tech small text-uppercase fw-bold text-success d-block mb-2">
                            &bull; PUSPA CANDRA
                        </span>
                        <h4 class="fw-bold mb-2" style="color: var(--op-green-dark);">
                            Kearifan Lokal Jawa Timur
                        </h4>
                        <p class="small text-muted mb-2">
                            Mengintegrasikan ketahanan ekonomi rakyat dari lereng Bromo Pasuruan, sentra industri Surabaya-Sidoarjo, hingga pelosok BUMDesa Jawa Timur dalam ekosistem akuntansi yang subur dan transparan.
                        </p>
                        <div class="font-mono-tech small text-dark fw-semibold">
                            Versi 3.5 &bull; Berbasis SAK EP Nasional
                        </div>
                    </div>

                    <!-- Column 3 -->
                    <div class="col-lg-4 manifesto-col d-flex flex-column justify-content-between">
                        <div>
                            <span class="font-mono-tech small text-uppercase fw-bold text-success d-block mb-2">
                                &bull; AKUNTABILITAS PUBLIK
                            </span>
                            <h4 class="fw-bold mb-2" style="color: var(--op-green-dark);">
                                Mitra Terpercaya Pembiayaan
                            </h4>
                            <p class="small text-muted mb-0">
                                Setiap laporan terhubung langsung dengan audit trail, memastikan perbankan, dinas koperasi, dan mitra CSR dapat memverifikasi keabsahan laporan secara cepat dan akurat.
                            </p>
                        </div>
                        <div class="pt-3">
                            <a href="<?php echo BASEURL; ?>/kurasi" class="btn btn-sm btn-dark rounded-pill px-4 font-mono-tech">
                                BUKA MENU KURASI ↗
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 8. FOOTER -->
            <footer class="pt-5 mt-5 border-top d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="fw-bold" style="color: var(--op-green-dark);">
                        SimpleAkunting v3.5 &bull; Puspa Candra
                    </div>
                    <small class="text-muted">
                        Sistem Akuntansi Terintegrasi dengan kearifan lokal Kabupaten Kota di Jawa Timur.
                    </small>
                </div>
                <div class="d-flex align-items-center gap-3 small font-mono-tech">
                    <a href="<?php echo BASEURL; ?>/kurasi" class="text-decoration-none text-muted">Kurasi UMKM</a>
                    <a href="<?php echo BASEURL; ?>/panduan_pengguna.html" target="_blank" class="text-decoration-none text-muted">Panduan</a>
                    <a href="<?php echo BASEURL; ?>/login" class="text-decoration-none text-success fw-bold">Login Sistem ↗</a>
                </div>
            </footer>
        </div>
    </div>
</div>

<!-- Script Kalkulator Kelayakan -->
<script>
document.getElementById('formSimulasi').addEventListener('submit', function(e) {
    e.preventDefault();
    const omset = parseFloat(document.getElementById('calcOmset').value) || 0;
    const laba = parseFloat(document.getElementById('calcLaba').value) || 0;
    const usia = parseInt(document.getElementById('calcUsia').value) || 1;
    const metode = document.getElementById('calcMetode').value;

    let skor = 40;
    const npm = (omset > 0) ? (laba / omset) : 0;
    if (npm >= 0.20) skor += 25;
    else if (npm >= 0.10) skor += 18;
    else if (npm > 0) skor += 10;

    if (usia >= 3) skor += 15;
    else if (usia >= 2) skor += 10;
    else skor += 5;

    if (metode === 'simpleakunting') skor += 20;
    else if (metode === 'excel') skor += 12;
    else skor += 5;

    skor = Math.min(100, Math.max(35, skor));

    let grade = 'C';
    let statusText = 'Perlu Pendampingan Pembukuan';
    let rekomendasiText = 'Gunakan SimpleAkunting v3.5 minimal 3 bulan untuk merapikan pembukuan SAK EP.';
    let badgeClass = 'badge bg-secondary';

    if (skor >= 85) {
        grade = 'GRADE A';
        statusText = 'Sangat Layak (Bankable & Siap Investasi)';
        rekomendasiText = 'Memenuhi kriteria KUR Komersial Bank Jatim / BRI hingga Rp 500 Juta dan Program Kemitraan CSR BUMN/BUMD.';
        badgeClass = 'badge-grade-a';
    } else if (skor >= 70) {
        grade = 'GRADE B+';
        statusText = 'Layak Pembiayaan Mikro & BUMDes';
        rekomendasiText = 'Memenuhi kriteria KUR Mikro hingga Rp 100 Juta dan permodalan bergulir BUMDesa.';
        badgeClass = 'badge-grade-b';
    } else {
        grade = 'GRADE B';
        statusText = 'Cukup Layak / Tahap Kurasi Pemula';
        rekomendasiText = 'Direkomendasikan KUR Super Mikro hingga Rp 10 Juta dan pelatihan akuntansi SAK EP.';
        badgeClass = 'badge bg-warning text-dark';
    }

    document.getElementById('hasilGradeBadge').className = badgeClass;
    document.getElementById('hasilGradeBadge').innerText = grade;
    document.getElementById('hasilSkorText').innerText = skor + ' / 100';
    document.getElementById('hasilNpmText').innerText = (npm * 100).toFixed(1) + '%';
    document.getElementById('hasilStatusText').innerText = statusText;
    document.getElementById('hasilRekomendasiText').innerText = rekomendasiText;

    document.getElementById('hasilSimulasi').classList.remove('d-none');
    document.getElementById('hasilSimulasi').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
});
</script>

</body>
</html>
