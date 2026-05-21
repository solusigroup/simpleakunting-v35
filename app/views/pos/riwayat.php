<div class="container-fluid py-2">
    
    <!-- Title & Navigation -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 brand-font fw-bold"><i class="bi bi-clock-history text-indigo me-2"></i> Riwayat Transaksi POS</h1>
            <p class="text-muted small mb-0">Daftar transaksi Point of Sales yang tersimpan dan teraplikasi ke jurnal umum.</p>
        </div>
        <a href="<?php echo BASEURL; ?>/pos" class="btn btn-indigo rounded-3 d-flex align-items-center gap-2 px-3 py-2 fw-semibold">
            <i class="bi bi-upc-scan"></i> Buka Kasir POS
        </a>
    </div>

    <!-- Quick Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: #fff;">
                <div class="card-body p-4 position-relative">
                    <div class="position-absolute" style="top: 15px; right: 20px; font-size: 3rem; opacity: 0.15;">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="small text-white-50 text-uppercase fw-bold mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">Total Penjualan POS Hari Ini</div>
                    <h2 class="h1 mb-0 brand-font fw-extrabold">Rp <?php echo number_format($data['statistik']['total_penjualan'], 0, ',', '.'); ?></h2>
                    <div class="mt-3 small text-white-50">Tercatat dari kasir aktif hari ini</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff;">
                <div class="card-body p-4 position-relative">
                    <div class="position-absolute" style="top: 15px; right: 20px; font-size: 3rem; opacity: 0.15;">
                        <i class="bi bi-cart-check"></i>
                    </div>
                    <div class="small text-white-50 text-uppercase fw-bold mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">Jumlah Transaksi Hari Ini</div>
                    <h2 class="h1 mb-0 brand-font fw-extrabold"><?php echo number_format($data['statistik']['jumlah_transaksi']); ?> Transaksi</h2>
                    <div class="mt-3 small text-white-50">Rata-rata penjualan: Rp <?php echo ($data['statistik']['jumlah_transaksi'] > 0) ? number_format($data['statistik']['total_penjualan'] / $data['statistik']['jumlah_transaksi'], 0, ',', '.') : '0'; ?> per transaksi</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Table Panel -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="mb-0 brand-font fw-bold text-gray-800"><i class="bi bi-filter-right me-2 text-indigo"></i> Filter Periode Transaksi</h5>
            
            <!-- Date Filter Form -->
            <form action="<?php echo BASEURL; ?>/pos/riwayat" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                <div>
                    <input type="date" name="dari" class="form-control form-control-sm rounded-3" value="<?php echo $data['dari']; ?>">
                </div>
                <div class="text-muted small">s/d</div>
                <div>
                    <input type="date" name="sampai" class="form-control form-control-sm rounded-3" value="<?php echo $data['sampai']; ?>">
                </div>
                <button type="submit" class="btn btn-sm btn-indigo rounded-3 px-3">
                    <i class="bi bi-funnel"></i> Terapkan
                </button>
                <?php if ($data['dari'] !== date('Y-m-d') || $data['sampai'] !== date('Y-m-d')): ?>
                    <a href="<?php echo BASEURL; ?>/pos/riwayat" class="btn btn-sm btn-light rounded-3">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase font-monospace" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                        <tr>
                            <th class="px-4 py-3 text-center" style="width: 60px;">No</th>
                            <th class="py-3">Waktu</th>
                            <th class="py-3">No. Receipt</th>
                            <th class="py-3">No. Jurnal / Faktur</th>
                            <th class="py-3">Kasir</th>
                            <th class="py-3 text-end">Total Belanja</th>
                            <th class="py-3 text-end">Bayar</th>
                            <th class="py-3 text-end">Kembalian</th>
                            <th class="px-4 py-3 text-center" style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['riwayat'])): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-clock-history fs-1 mb-2 opacity-50 text-indigo d-block"></i>
                                    Tidak ada transaksi POS yang ditemukan untuk periode ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $i = 1; 
                            foreach ($data['riwayat'] as $row): 
                            ?>
                                <tr>
                                    <td class="px-4 py-3 text-center text-muted"><?php echo $i++; ?></td>
                                    <td class="py-3">
                                        <div class="fw-semibold"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></div>
                                        <small class="text-muted"><?php echo date('H:i', strtotime($row['created_at'])); ?> WIB</small>
                                    </td>
                                    <td class="py-3"><span class="badge bg-indigo-subtle text-indigo rounded-pill px-3 py-1 font-monospace fw-bold"><?php echo $row['no_receipt']; ?></span></td>
                                    <td class="py-3 font-monospace small"><?php echo $row['no_faktur']; ?></td>
                                    <td class="py-3"><?php echo $row['kasir_name'] ?? '-'; ?></td>
                                    <td class="py-3 text-end fw-bold text-dark">Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                                    <td class="py-3 text-end text-muted">Rp <?php echo number_format($row['bayar'], 0, ',', '.'); ?></td>
                                    <td class="py-3 text-end text-muted">Rp <?php echo number_format($row['kembalian'], 0, ',', '.'); ?></td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <!-- Print receipt -->
                                            <a href="<?php echo BASEURL; ?>/pos/receipt/<?php echo $row['id']; ?>" class="btn btn-sm btn-light border d-flex align-items-center gap-1 rounded-3 px-2 py-1" title="Lihat Struk" target="_blank">
                                                <i class="bi bi-printer text-primary"></i> <span class="small d-none d-lg-inline">Struk</span>
                                            </a>
                                            <!-- Void Button -->
                                            <?php if (Auth::isAdmin() || Auth::isManager()): ?>
                                                <button class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1 rounded-3 px-2 py-1" onclick="confirmVoid('<?php echo BASEURL; ?>/pos/hapus/<?php echo $row['id']; ?>', '<?php echo $row['no_receipt']; ?>')">
                                                    <i class="bi bi-trash"></i> <span class="small d-none d-lg-inline">Void</span>
                                                </button>
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
</div>

<!-- Modal Konfirmasi Void -->
<div class="modal fade" id="voidModal" tabindex="-1" aria-labelledby="voidModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0 justify-content-end">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="bi bi-exclamation-triangle-fill text-danger mb-3" style="font-size: 3.5rem;"></i>
                <h4 class="brand-font fw-bold text-gray-800 mb-2">Batalkan Transaksi POS?</h4>
                <p class="text-muted small px-3">
                    Apakah Anda yakin ingin membatalkan transaksi POS <strong id="void-receipt-no" class="text-dark">#POS/...</strong>?<br>
                    Tindakan ini akan mengembalikan stok persediaan barang dan membalikkan jurnal umum otomatis yang bersangkutan. Tindakan ini tidak bisa dibatalkan.
                </p>
            </div>
            <div class="modal-footer border-0 p-3 justify-content-center gap-2">
                <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="btn-confirm-void" class="btn btn-danger rounded-3 px-4 fw-semibold">Ya, Batalkan Transaksi</a>
            </div>
        </div>
    </div>
</div>

<!-- Custom styles for indigo colors -->
<style>
    .btn-indigo {
        background-color: #4f46e5;
        border-color: #4f46e5;
        color: #fff;
    }
    .btn-indigo:hover {
        background-color: #4338ca;
        border-color: #4338ca;
        color: #fff;
    }
    .text-indigo {
        color: #4f46e5;
    }
    .bg-indigo {
        background-color: #4f46e5;
    }
    .bg-indigo-subtle {
        background-color: rgba(79, 70, 229, 0.1);
    }
</style>

<script>
    let voidModal;
    
    document.addEventListener('DOMContentLoaded', function() {
        voidModal = new bootstrap.Modal(document.getElementById('voidModal'));
    });

    function confirmVoid(url, receiptNo) {
        document.getElementById('void-receipt-no').innerText = '#' + receiptNo;
        document.getElementById('btn-confirm-void').setAttribute('href', url);
        voidModal.show();
    }
</script>
