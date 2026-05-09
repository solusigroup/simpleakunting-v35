<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Transaksi Kas & Bank</h3>
        <p class="text-muted small mb-0">Kelola mutasi saldo kas dan rekening bank operasional.</p>
    </div>
    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#pilihTipeKasModal">
        <i class="bi bi-plus-lg me-2"></i>Tambah Transaksi
    </button>
</div>

<!-- Modal Pilih Tipe Kas -->
<div class="modal fade" id="pilihTipeKasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-5">
                <h4 class="fw-bold mb-4">Pilih Tipe Transaksi Kas</h4>
                <div class="row g-4">
                    <div class="col-6">
                        <a href="<?php echo BASEURL; ?>/kas/tambah/Masuk" class="d-block p-4 rounded-4 border-0 shadow-sm transition-all hover-scale text-decoration-none bg-primary text-white">
                            <i class="bi bi-box-arrow-in-down fs-1 mb-3 d-block"></i>
                            <span class="fw-bold fs-5">Kas Masuk</span>
                            <small class="d-block opacity-75 mt-1">Penerimaan Dana</small>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?php echo BASEURL; ?>/kas/tambah/Keluar" class="d-block p-4 rounded-4 border-0 shadow-sm transition-all hover-scale text-decoration-none bg-danger text-white">
                            <i class="bi bi-box-arrow-up fs-1 mb-3 d-block"></i>
                            <span class="fw-bold fs-5">Kas Keluar</span>
                            <small class="d-block opacity-75 mt-1">Pengeluaran Dana</small>
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-5">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .bg-success-soft { background-color: rgba(16, 185, 129, 0.1); }

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">Tanggal</th>
                        <th class="py-3">Referensi</th>
                        <th class="py-3">Tipe</th>
                        <th class="py-3">Akun</th>
                        <th class="py-3">Keterangan</th>
                        <th class="py-3 text-end">Jumlah</th>
                        <th class="pe-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['transaksi'])): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-bank fs-1 d-block mb-3 opacity-25"></i>
                                    Belum ada transaksi kas & bank yang tercatat.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['transaksi'] as $trx): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-medium"><?php echo date('d M Y', strtotime($trx['tanggal'])); ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal"><?php echo htmlspecialchars($trx['no_bukti']); ?></span>
                            </td>
                            <td>
                                <?php if($trx['tipe_transaksi'] == 'Masuk'): ?>
                                    <span class="badge bg-success-soft text-success rounded-pill px-3">
                                        <i class="bi bi-arrow-down-left me-1"></i> Masuk
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger-soft text-danger rounded-pill px-3">
                                        <i class="bi bi-arrow-up-right me-1"></i> Keluar
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold small text-primary mb-0"><?php echo htmlspecialchars($trx['nama_akun_kas']); ?></div>
                                <div class="text-muted small">Lawan: <?php echo htmlspecialchars($trx['nama_akun_lawan']); ?></div>
                            </td>
                            <td style="max-width: 250px;">
                                <div class="text-truncate" title="<?php echo htmlspecialchars($trx['deskripsi']); ?>">
                                    <?php echo htmlspecialchars($trx['deskripsi']); ?>
                                </div>
                            </td>
                            <td class="text-end fw-bold <?php echo ($trx['tipe_transaksi'] == 'Masuk') ? 'text-success' : 'text-danger'; ?>">
                                Rp <?php echo number_format($trx['jumlah'], 2, ',', '.'); ?>
                            </td>
                            <td class="pe-4 text-center">
                                <div class="btn-group">
                                    <?php if (Auth::isAdmin() || Auth::isManager()): ?>
                                        <a href="<?php echo BASEURL; ?>/kas/edit/<?php echo $trx['id_transaksi']; ?>" class="btn btn-sm btn-outline-warning rounded-pill px-3 me-2">
                                            <i class="bi bi-pencil me-1"></i> Edit
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="if(confirm('Yakin ingin membatalkan transaksi ini?')){ window.location.href='<?php echo BASEURL; ?>/kas/hapus/<?php echo $trx['id_transaksi']; ?>'; }">
                                            <i class="bi bi-trash me-1"></i> Hapus
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted small">Read Only</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .bg-success-soft { background-color: rgba(16, 185, 129, 0.1); }
    .bg-danger-soft { background-color: rgba(239, 68, 68, 0.1); }
    .table thead th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        font-weight: 700;
        border-bottom: 2px solid #f3f4f6;
    }
</style>
