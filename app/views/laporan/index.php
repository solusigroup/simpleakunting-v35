<div class="row mb-4">
    <div class="col-md-12">
        <h3 class="fw-bold mb-1">Pusat Laporan</h3>
        <p class="text-muted">Akses semua laporan keuangan dan operasional dalam satu dashboard.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Kelompok Laporan Keuangan -->
    <div class="col-md-12 mt-4">
        <h5 class="fw-bold d-flex align-items-center mb-3">
            <i class="bi bi-journal-text me-2 text-primary"></i> Laporan Keuangan Utama
        </h5>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body p-4">
                <div class="bg-primary-soft text-primary rounded-circle p-3 mb-3 d-inline-block">
                    <i class="bi bi-table fs-4"></i>
                </div>
                <h5 class="fw-bold">Neraca Saldo</h5>
                <p class="text-muted small">Ringkasan saldo semua akun pada periode tertentu.</p>
                <a href="<?php echo BASEURL; ?>/laporan/neracaSaldo" class="btn btn-outline-primary btn-sm rounded-pill px-3 mt-2">Lihat Laporan</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body p-4">
                <div class="bg-success-soft text-success rounded-circle p-3 mb-3 d-inline-block">
                    <i class="bi bi-graph-up-arrow fs-4"></i>
                </div>
                <h5 class="fw-bold">Laba Rugi</h5>
                <p class="text-muted small">Evaluasi kinerja pendapatan dan beban operasional.</p>
                <a href="<?php echo BASEURL; ?>/laporan/labaRugi" class="btn btn-outline-success btn-sm rounded-pill px-3 mt-2">Lihat Laporan</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body p-4">
                <div class="bg-info-soft text-info rounded-circle p-3 mb-3 d-inline-block">
                    <i class="bi bi-balance fs-4"></i>
                </div>
                <h5 class="fw-bold">Posisi Keuangan</h5>
                <p class="text-muted small">Laporan aset, kewajiban, dan ekuitas perusahaan.</p>
                <a href="<?php echo BASEURL; ?>/laporan/posisiKeuangan" class="btn btn-outline-info btn-sm rounded-pill px-3 mt-2">Lihat Laporan</a>
            </div>
        </div>
    </div>

    <!-- Kelompok Laporan Manufaktur -->
    <div class="col-md-12 mt-5">
        <h5 class="fw-bold d-flex align-items-center mb-3">
            <i class="bi bi-gear-wide-connected me-2 text-warning"></i> Laporan Operasional Manufaktur
        </h5>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body p-4 d-flex align-items-start">
                <div class="bg-warning-soft text-warning rounded-circle p-3 me-3">
                    <i class="bi bi-box-seam fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold">Ringkasan Produksi</h5>
                    <p class="text-muted small">Daftar semua perintah produksi dan biaya aktual yang terjadi.</p>
                    <a href="<?php echo BASEURL; ?>/laporan/produksi" class="btn btn-warning btn-sm rounded-pill px-4 mt-2">Buka Laporan</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body p-4 d-flex align-items-start">
                <div class="bg-danger-soft text-danger rounded-circle p-3 me-3">
                    <i class="bi bi-recycle fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold">Pemakaian Bahan Baku</h5>
                    <p class="text-muted small">Analisis konsumsi bahan baku per proyek atau per periode.</p>
                    <a href="<?php echo BASEURL; ?>/laporan/pemakaianBahan" class="btn btn-danger btn-sm rounded-pill px-4 mt-2">Buka Laporan</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Kelompok Laporan Buku & Kas -->
    <div class="col-md-12 mt-5">
        <h5 class="fw-bold d-flex align-items-center mb-3">
            <i class="bi bi-book me-2 text-dark"></i> Buku Besar & Kas
        </h5>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body p-4 d-flex align-items-start">
                <div class="bg-dark text-white rounded-circle p-3 me-3">
                    <i class="bi bi-card-list fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold">Buku Besar</h5>
                    <p class="text-muted small">Detail mutasi transaksi untuk setiap akun perkiraan.</p>
                    <a href="<?php echo BASEURL; ?>/laporan/bukuBesar" class="btn btn-dark btn-sm rounded-pill px-4 mt-2">Pilih Akun</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body p-4 d-flex align-items-start">
                <div class="bg-indigo-soft text-indigo rounded-circle p-3 me-3">
                    <i class="bi bi-water fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold">Arus Kas</h5>
                    <p class="text-muted small">Pantau aliran masuk dan keluar kas (Metode Langsung/Tidak Langsung).</p>
                    <a href="<?php echo BASEURL; ?>/laporan/arusKas" class="btn btn-indigo btn-sm rounded-pill px-4 mt-2">Lihat Arus Kas</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body p-4 d-flex align-items-start">
                <div class="bg-primary text-white rounded-circle p-3 me-3">
                    <i class="bi bi-grid-3x3-gap-fill fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold">Neraca Lajur</h5>
                    <p class="text-muted small">Kertas kerja 10 kolom untuk penyesuaian dan persiapan laporan keuangan.</p>
                    <a href="<?php echo BASEURL; ?>/laporan/neracaLajur" class="btn btn-primary btn-sm rounded-pill px-4 mt-2">Buka Worksheet</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body p-4 d-flex align-items-start">
                <div class="bg-success text-white rounded-circle p-3 me-3">
                    <i class="bi bi-graph-up-arrow fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold">Perubahan Ekuitas</h5>
                    <p class="text-muted small">Lacak kenaikan atau penurunan modal bersih dalam satu periode.</p>
                    <a href="<?php echo BASEURL; ?>/laporan/perubahanEkuitas" class="btn btn-success btn-sm rounded-pill px-4 mt-2">Lihat Perubahan</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body p-4 d-flex align-items-start">
                <div class="bg-secondary text-white rounded-circle p-3 me-3">
                    <i class="bi bi-shield-lock-fill fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold">Audit Log Aktivitas</h5>
                    <p class="text-muted small">Jejak aktivitas pengguna untuk keamanan dan audit internal.</p>
                    <a href="<?php echo BASEURL; ?>/laporan/audit" class="btn btn-secondary btn-sm rounded-pill px-4 mt-2">Buka Log</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-soft { background-color: rgba(79, 70, 229, 0.1); }
    .bg-success-soft { background-color: rgba(16, 185, 129, 0.1); }
    .bg-info-soft { background-color: rgba(6, 182, 212, 0.1); }
    .bg-warning-soft { background-color: rgba(245, 158, 11, 0.1); }
    .bg-danger-soft { background-color: rgba(239, 68, 68, 0.1); }
    .bg-indigo-soft { background-color: rgba(79, 70, 229, 0.1); }
    .text-indigo { color: #4f46e5; }
    .btn-indigo { background-color: #4f46e5; color: white; }
    .btn-indigo:hover { background-color: #4338ca; color: white; }
    
    .hover-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
    }
</style>
