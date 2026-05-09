<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Bill of Materials (BOM)</h3>
        <p class="text-muted small mb-0">Kelola resep dan komposisi bahan baku untuk produk jadi.</p>
    </div>
    <a href="<?php echo BASEURL; ?>/bom/tambah" class="btn btn-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-plus-lg me-2"></i>Tambah BOM
    </a>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">Nama BOM</th>
                        <th class="py-3">Produk Jadi</th>
                        <th class="py-3 text-end">Estimasi Biaya / Unit</th>
                        <th class="pe-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['bom'])): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-recipe fs-1 d-block mb-3 opacity-25"></i>
                                    Belum ada data BOM yang tercatat.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['bom'] as $b): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-primary"><?php echo htmlspecialchars($b['nama_bom']); ?></div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary-soft text-primary rounded p-2 me-2">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                    <?php echo htmlspecialchars($b['nama_produk']); ?>
                                </div>
                            </td>
                            <td class="text-end fw-bold">
                                Rp <?php echo number_format($b['total_biaya_estimasi'], 2, ',', '.'); ?>
                            </td>
                            <td class="pe-4 text-center">
                                <div class="btn-group">
                                    <a href="<?php echo BASEURL; ?>/bom/lihat/<?php echo $b['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-2">
                                        <i class="bi bi-eye me-1"></i> Lihat
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="if(confirm('Yakin ingin menghapus BOM ini?')){ window.location.href='<?php echo BASEURL; ?>/bom/hapus/<?php echo $b['id']; ?>'; }">
                                        <i class="bi bi-trash me-1"></i> Hapus
                                    </button>
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
    .bg-primary-soft { background-color: rgba(79, 70, 229, 0.1); }
    .table thead th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        font-weight: 700;
        border-bottom: 2px solid #f3f4f6;
    }
</style>
