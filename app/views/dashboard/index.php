<div class="row g-4 mb-5">
    <!-- Stat Cards -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white h-100 overflow-hidden position-relative">
            <div class="card-body p-4 position-relative" style="z-index: 1;">
                <h6 class="text-white-50 small fw-bold text-uppercase mb-2">Total Piutang</h6>
                <h2 class="fw-bold mb-0">Rp <?php echo number_format($data['summary']['total_piutang'] ?? 0, 0, ',', '.'); ?></h2>
                <div class="mt-3 small">
                    <i class="bi bi-arrow-up-short"></i> Saldo pelanggan saat ini
                </div>
            </div>
            <i class="bi bi-person-check position-absolute bottom-0 end-0 opacity-25" style="font-size: 6rem; margin: 0 -1rem -1rem 0;"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-danger text-white h-100 overflow-hidden position-relative">
            <div class="card-body p-4 position-relative" style="z-index: 1;">
                <h6 class="text-white-50 small fw-bold text-uppercase mb-2">Total Utang</h6>
                <h2 class="fw-bold mb-0">Rp <?php echo number_format($data['summary']['total_utang'] ?? 0, 0, ',', '.'); ?></h2>
                <div class="mt-3 small">
                    <i class="bi bi-truck"></i> Kewajiban ke pemasok
                </div>
            </div>
            <i class="bi bi-truck position-absolute bottom-0 end-0 opacity-25" style="font-size: 6rem; margin: 0 -1rem -1rem 0;"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white h-100 overflow-hidden position-relative">
            <div class="card-body p-4 position-relative" style="z-index: 1;">
                <h6 class="text-white-50 small fw-bold text-uppercase mb-2">Nilai Persediaan</h6>
                <h2 class="fw-bold mb-0">Rp <?php echo number_format($data['summary']['nilai_persediaan'] ?? 0, 0, ',', '.'); ?></h2>
                <div class="mt-3 small">
                    <i class="bi bi-box-seam"></i> Aset lancar di gudang
                </div>
            </div>
            <i class="bi bi-box-seam position-absolute bottom-0 end-0 opacity-25" style="font-size: 6rem; margin: 0 -1rem -1rem 0;"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info text-white h-100 overflow-hidden position-relative">
            <div class="card-body p-4 position-relative" style="z-index: 1;">
                <h6 class="text-white-50 small fw-bold text-uppercase mb-2">Produksi Aktif</h6>
                <h2 class="fw-bold mb-0"><?php echo $data['summary']['total_produksi_aktif'] ?? 0; ?> <small class="fs-6">Order</small></h2>
                <div class="mt-3 small">
                    <i class="bi bi-gear-wide-connected"></i> Dalam proses manufaktur
                </div>
            </div>
            <i class="bi bi-gear-wide-connected position-absolute bottom-0 end-0 opacity-25" style="font-size: 6rem; margin: 0 -1rem -1rem 0;"></i>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Chart Section -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">Performa Perdagangan (6 Bulan)</h6>
                <span class="badge bg-light text-primary rounded-pill px-3 py-2 fw-normal border">Sales vs Purchase</span>
            </div>
            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="salesPurchasesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- Quick Access Section -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100 bg-light-soft">
            <div class="card-header bg-transparent py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark">Akses Cepat (Improvements)</h6>
            </div>
            <div class="card-body pt-0">
                <div class="list-group list-group-flush rounded-3 overflow-hidden border">
                    <a href="<?php echo BASEURL; ?>/kas" class="list-group-item list-group-item-action d-flex align-items-center p-3">
                        <div class="bg-primary-soft text-primary rounded p-2 me-3">
                            <i class="bi bi-bank"></i>
                        </div>
                        <div>
                            <div class="fw-bold small">Kas & Bank</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Kelola mutasi saldo kas</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                    <a href="<?php echo BASEURL; ?>/penjualan" class="list-group-item list-group-item-action d-flex align-items-center p-3">
                        <div class="bg-success-soft text-success rounded p-2 me-3">
                            <i class="bi bi-cart-check"></i>
                        </div>
                        <div>
                            <div class="fw-bold small">Penjualan</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Input transaksi penjualan</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                    <a href="<?php echo BASEURL; ?>/pembelian" class="list-group-item list-group-item-action d-flex align-items-center p-3">
                        <div class="bg-danger-soft text-danger rounded p-2 me-3">
                            <i class="bi bi-bag-plus"></i>
                        </div>
                        <div>
                            <div class="fw-bold small">Pembelian</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Input transaksi pembelian</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                    <a href="<?php echo BASEURL; ?>/laporan" class="list-group-item list-group-item-action d-flex align-items-center p-3">
                        <div class="bg-info-soft text-info rounded p-2 me-3">
                            <i class="bi bi-pie-chart-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold small">Laporan</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Akses laporan keuangan</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesPurchasesChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo $data['chart_trend']['labels']; ?>,
            datasets: [{
                label: 'Penjualan',
                data: <?php echo $data['chart_trend']['sales']; ?>,
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderColor: '#4f46e5',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#4f46e5'
            }, {
                label: 'Pembelian',
                data: <?php echo $data['chart_trend']['purchases']; ?>,
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                borderColor: '#ef4444',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#ef4444'
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false },
                    ticks: { callback: value => 'Rp ' + value.toLocaleString('id-ID') }
                },
                x: { grid: { display: false } }
            },
            plugins: {
                legend: { display: true, position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 6, font: { size: 12, weight: '600' } } }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>

<style>
    .bg-light-soft { background-color: #f8f9fa; }
    .bg-primary-soft { background-color: rgba(79, 70, 229, 0.1); }
    .bg-success-soft { background-color: rgba(16, 185, 129, 0.1); }
    .bg-danger-soft { background-color: rgba(239, 68, 68, 0.1); }
    .bg-info-soft { background-color: rgba(6, 182, 212, 0.1); }
    .animate-pulse { animation: pulse 2s infinite; }
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }
</style>
