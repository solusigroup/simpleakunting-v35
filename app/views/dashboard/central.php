<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4 text-primary">Central Monitoring Dashboard</h2>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm bg-light-subtle" style="border-left: 4px solid #059669 !important;">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3 py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success-subtle text-success p-2 rounded-3 fs-4">
                        <i class="bi bi-journal-check"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold text-dark">Pusat Panduan Penyelia (Superadmin)</h6>
                        <small class="text-muted">Pelajari tata cara mendaftarkan Tenant (BUMDesa/UMKM) baru dan menambahkan user dengan aman.</small>
                    </div>
                </div>
                <a href="<?php echo BASEURL; ?>/panduan_superadmin.html" target="_blank" class="btn btn-success btn-sm rounded-pill px-4 fw-medium">
                    <i class="bi bi-book me-1"></i> Buka Panduan
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card bg-primary text-white h-100 shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-uppercase small fw-bold opacity-75">Active Tenants</h6>
                <h2 class="mb-0"><?php echo $data['summary']['total_active_tenants']; ?></h2>
                <i class="bi bi-building position-absolute top-0 end-0 m-3 opacity-25" style="font-size: 3rem;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white h-100 shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-uppercase small fw-bold opacity-75">Total Global Sales</h6>
                <h2 class="mb-0">Rp <?php echo number_format($data['summary']['total_sales_all'] ?? 0, 0, ',', '.'); ?></h2>
                <i class="bi bi-cart-check position-absolute top-0 end-0 m-3 opacity-25" style="font-size: 3rem;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white h-100 shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-uppercase small fw-bold opacity-75">Total Global Purchases</h6>
                <h2 class="mb-0">Rp <?php echo number_format($data['summary']['total_purchases_all'] ?? 0, 0, ',', '.'); ?></h2>
                <i class="bi bi-cart-dash position-absolute top-0 end-0 m-3 opacity-25" style="font-size: 3rem;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white h-100 shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-uppercase small fw-bold opacity-75">Total System Users</h6>
                <h2 class="mb-0"><?php echo $data['summary']['total_users']; ?></h2>
                <i class="bi bi-people position-absolute top-0 end-0 m-3 opacity-25" style="font-size: 3rem;"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-dark">Global Transaction Trend (Aggregate)</h5>
            </div>
            <div class="card-body">
                <canvas id="trendChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-dark">Recent Tenants</h5>
                <a href="<?php echo BASEURL; ?>/tenants" class="btn btn-sm btn-outline-primary">Manage</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach($data['tenants'] as $tenant): ?>
                    <div class="list-group-item px-4 py-3 border-0 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($tenant['name']); ?></h6>
                                <small class="text-muted"><?php echo $tenant['code']; ?> | <?php echo ucfirst($tenant['database_type']); ?></small>
                            </div>
                            <span class="badge <?php echo $tenant['status'] === 'active' ? 'bg-light text-success' : 'bg-light text-danger'; ?> rounded-pill">
                                <?php echo ucfirst($tenant['status']); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('trendChart').getContext('2d');
const trendChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo $data['chart_trend']['labels']; ?>,
        datasets: [{
            label: 'Global Sales',
            data: <?php echo $data['chart_trend']['sales']; ?>,
            borderColor: '#198754',
            backgroundColor: 'rgba(25, 135, 84, 0.1)',
            fill: true,
            tension: 0.4
        }, {
            label: 'Global Purchases',
            data: <?php echo $data['chart_trend']['purchases']; ?>,
            borderColor: '#dc3545',
            backgroundColor: 'rgba(220, 53, 69, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top' }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
