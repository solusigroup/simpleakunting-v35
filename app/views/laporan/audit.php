<div class="row mb-4">
    <div class="col-md-12">
        <h3 class="fw-bold mb-1">Audit Log Aktivitas</h3>
        <p class="text-muted small">Menampilkan 100 aktivitas terakhir yang dilakukan oleh pengguna dalam organisasi ini.</p>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">Waktu</th>
                        <th class="py-3">User</th>
                        <th class="py-3">Aksi</th>
                        <th class="py-3">Modul</th>
                        <th class="py-3">Deskripsi</th>
                        <th class="pe-4 py-3 text-end">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['logs'])): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Belum ada aktivitas yang tercatat.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['logs'] as $log): ?>
                        <tr>
                            <td class="ps-4 text-muted small">
                                <?php echo date('d/m/Y H:i:s', strtotime($log['created_at'])); ?>
                            </td>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($log['user_name']); ?></div>
                                <small class="text-muted small">ID: <?php echo $log['user_id']; ?></small>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2 <?php 
                                    echo strpos($log['action'], 'DELETE') !== false ? 'bg-danger-soft text-danger' : 
                                        (strpos($log['action'], 'UPDATE') !== false ? 'bg-warning-soft text-warning' : 'bg-primary-soft text-primary'); 
                                ?>">
                                    <?php echo $log['action']; ?>
                                </span>
                            </td>
                            <td class="fw-medium"><?php echo $log['module']; ?></td>
                            <td class="small text-muted"><?php echo htmlspecialchars($log['description']); ?></td>
                            <td class="pe-4 text-end text-muted small"><?php echo $log['ip_address']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .bg-primary-soft { background-color: rgba(79, 70, 229, 0.1); }
    .bg-warning-soft { background-color: rgba(245, 158, 11, 0.1); }
    .bg-danger-soft { background-color: rgba(239, 68, 68, 0.1); }
    .table thead th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        border-bottom: 2px solid #f3f4f6;
    }
</style>
