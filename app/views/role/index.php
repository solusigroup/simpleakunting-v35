<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Manajemen Hak Akses (Read-Only)</h3>
        <p class="text-muted small mb-0">Lihat daftar peran pengguna yang tersedia dalam sistem.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">Nama Role</th>
                                <th class="py-3">Deskripsi</th>
                                <th class="pe-4 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($data['roles'])): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-shield-lock fs-1 d-block mb-3 opacity-25"></i>
                                            Belum ada role kustom yang dibuat.
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($data['roles'] as $r): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-primary"><?php echo htmlspecialchars($r['role_name']); ?></div>
                                    </td>
                                    <td>
                                        <div class="text-muted small"><?php echo htmlspecialchars($r['description']); ?></div>
                                    </td>
                                    <td class="pe-4 text-center">
                                        <?php if ($r['id'] <= 4): ?>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">System Default</span>
                                        <?php else: ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">Custom Role</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-info-soft rounded-4 p-4 mt-4 border border-info border-opacity-10">
    <div class="d-flex align-items-center">
        <i class="bi bi-info-circle-fill text-info fs-4 me-3"></i>
        <div>
            <h6 class="fw-bold mb-1 text-info">Tentang Hak Akses Terpusat</h6>
            <p class="mb-0 small text-muted">Role yang dibuat dikelola secara terpusat oleh Central Admin. Anda dapat memberikan peran ini kepada pengguna di menu <b>Manajemen User</b>. Izin akses akan membatasi fitur apa saja yang dapat dilihat dan digunakan oleh pengguna tersebut.</p>
        </div>
    </div>
</div>

<style>
    .bg-info-soft { background-color: rgba(6, 182, 212, 0.05); }
    .table thead th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        font-weight: 700;
        border-bottom: 2px solid #f3f4f6;
    }
</style>
