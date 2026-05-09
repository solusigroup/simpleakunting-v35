<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3 mb-0 text-gray-800">Laporan Agregat Tenant</h1>
            <p class="text-muted">Ringkasan performa dan skala bisnis dari setiap tenant yang terdaftar.</p>
        </div>
    </div>

    <!-- Quick Stats Global -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-primary-subtle text-primary p-3 rounded-3">
                            <i class="bi bi-buildings fs-4"></i>
                        </div>
                        <div class="text-end">
                            <h6 class="text-muted mb-1 small uppercase">Total Tenants</h6>
                            <h3 class="mb-0 fw-bold"><?php echo count($data['tenants_report']); ?></h3>
                        </div>
                    </div>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-success-subtle text-success p-3 rounded-3">
                            <i class="bi bi-cart-check fs-4"></i>
                        </div>
                        <div class="text-end">
                            <h6 class="text-muted mb-1 small uppercase">Global Sales</h6>
                            <h3 class="mb-0 fw-bold">Rp <?php echo number_format($data['summary']['total_sales_all'] ?? 0, 0, ',', '.'); ?></h3>
                        </div>
                    </div>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-info-subtle text-info p-3 rounded-3">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                        <div class="text-end">
                            <h6 class="text-muted mb-1 small uppercase">Global Users</h6>
                            <h3 class="mb-0 fw-bold"><?php echo $data['summary']['total_users']; ?></h3>
                        </div>
                    </div>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-info" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 text-dark"><i class="bi bi-table me-2"></i> Perbandingan Performa Tenant</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Nama Tenant</th>
                            <th>Tipe</th>
                            <th class="text-center">Users</th>
                            <th class="text-end">Total Penjualan</th>
                            <th class="text-end">Total Pembelian</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['tenants_report'] as $tenant) : ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold"><?php echo htmlspecialchars($tenant['name']); ?></div>
                                <small class="text-muted"><?php echo $tenant['code']; ?></small>
                            </td>
                            <td><span class="text-capitalize"><?php echo $tenant['database_type']; ?></span></td>
                            <td class="text-center"><span class="badge bg-light text-dark border"><?php echo $tenant['user_count']; ?></span></td>
                            <td class="text-end fw-bold text-success">
                                Rp <?php echo number_format($tenant['total_sales'] ?? 0, 0, ',', '.'); ?>
                            </td>
                            <td class="text-end text-danger">
                                Rp <?php echo number_format($tenant['total_purchases'] ?? 0, 0, ',', '.'); ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?php echo $tenant['status'] == 'active' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle'; ?>">
                                    <?php echo ucfirst($tenant['status']); ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="<?php echo BASEURL; ?>/tenants/switch/<?php echo $tenant['id']; ?>" class="btn btn-sm btn-outline-primary" title="Pantau Dashboard">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
