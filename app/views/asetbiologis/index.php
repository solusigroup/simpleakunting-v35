<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Manajemen Aset Biologis (PSAK 241)</h2>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalPenyesuaian">
            <i class="bi bi-graph-up-arrow me-2"></i>Penyesuaian Nilai Wajar
        </button>
        <a href="<?php echo BASEURL; ?>/asetbiologis/laporan" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-file-earmark-text me-2"></i>Laporan
        </a>
        <a href="<?php echo BASEURL; ?>/asetbiologis/tambah" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-circle me-2"></i>Tambah Aset
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total Aset Aktif</h6>
                <h3 class="fw-bold mb-0"><?php echo count(array_filter($data['aset'], fn($a) => $a['status'] == 'Aktif')); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total Nilai Tercatat</h6>
                <h3 class="fw-bold mb-0">Rp <?php 
                    $total_nilai = array_sum(array_column(array_filter($data['aset'], fn($a) => $a['status'] == 'Aktif'), 'nilai_tercatat'));
                    echo number_format($total_nilai, 2, ',', '.'); 
                ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-2">Hewan</h6>
                <h3 class="fw-bold mb-0"><?php echo count(array_filter($data['aset'], fn($a) => in_array($a['kategori'], ['Hewan Konsumsi', 'Hewan Produktif']) && $a['status'] == 'Aktif')); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-2">Tanaman</h6>
                <h3 class="fw-bold mb-0"><?php echo count(array_filter($data['aset'], fn($a) => in_array($a['kategori'], ['Tanaman Konsumsi', 'Tanaman Produktif', 'Hasil pada Tanaman']) && $a['status'] == 'Aktif')); ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden mb-4">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm w-auto rounded-pill" id="filterKategori">
                <option value="">Semua Kategori</option>
                <option value="Hewan Konsumsi">Hewan Konsumsi</option>
                <option value="Hewan Produktif">Hewan Produktif</option>
                <option value="Tanaman Konsumsi">Tanaman Konsumsi</option>
                <option value="Tanaman Produktif">Tanaman Produktif</option>
                <option value="Hasil pada Tanaman">Hasil pada Tanaman</option>
            </select>
            <select class="form-select form-select-sm w-auto rounded-pill" id="filterStatus">
                <option value="">Semua Status</option>
                <option value="Aktif">Aktif</option>
                <option value="Dipanen">Dipanen</option>
                <option value="Dijual">Dijual</option>
                <option value="Mati/Hapus">Mati/Hapus</option>
            </select>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalPanen">
                <i class="bi bi-flower1 me-1"></i>Panen
            </button>
            <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalPelepasan">
                <i class="bi bi-box-arrow-right me-1"></i>Pelepasan
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tableAset">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Kode</th>
                        <th>Nama Aset</th>
                        <th>Kategori</th>
                        <th>Kematangan</th>
                        <th class="text-end">Kuantitas</th>
                        <th class="text-end">Nilai Tercatat</th>
                        <th class="text-center">Status</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['aset'])): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">Belum ada data aset biologis.</td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach ($data['aset'] as $row): ?>
                    <tr data-kategori="<?php echo htmlspecialchars($row['kategori']); ?>" data-status="<?php echo htmlspecialchars($row['status']); ?>">
                        <td class="ps-4 fw-medium text-primary"><?php echo htmlspecialchars($row['kode_aset']); ?></td>
                        <td>
                            <div class="fw-bold"><?php echo htmlspecialchars($row['nama_aset']); ?></div>
                        </td>
                        <td>
                            <?php 
                                $cat_color = match($row['kategori']) {
                                    'Hewan Konsumsi' => 'primary',
                                    'Hewan Produktif' => 'info',
                                    'Tanaman Konsumsi' => 'success',
                                    'Tanaman Produktif' => 'warning',
                                    'Hasil pada Tanaman' => 'secondary',
                                    default => 'dark'
                                };
                            ?>
                            <span class="badge bg-<?php echo $cat_color; ?>-subtle text-<?php echo $cat_color; ?>"><?php echo htmlspecialchars($row['kategori']); ?></span>
                        </td>
                        <td>
                            <?php if ($row['status_kematangan'] == 'Menghasilkan'): ?>
                                <span class="badge bg-success-subtle text-success">Menghasilkan</span>
                            <?php else: ?>
                                <span class="badge bg-warning-subtle text-warning">Belum Menghasilkan</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end"><?php echo format_kuantitas($row['kuantitas'], $row['satuan']); ?></td>
                        <td class="text-end fw-bold">Rp <?php echo number_format($row['nilai_tercatat'], 2, ',', '.'); ?></td>
                        <td class="text-center">
                            <?php 
                                $status_color = match($row['status']) {
                                    'Aktif' => 'success',
                                    'Dipanen' => 'info',
                                    'Dijual' => 'warning',
                                    'Mati/Hapus' => 'danger',
                                    default => 'secondary'
                                };
                            ?>
                            <span class="badge rounded-pill bg-<?php echo $status_color; ?>-subtle text-<?php echo $status_color; ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                        </td>
                        <td class="text-center pe-4">
                            <div class="btn-group">
                                <a href="<?php echo BASEURL; ?>/asetbiologis/detail/<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-info rounded-start-pill px-3" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo BASEURL; ?>/asetbiologis/edit/<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary px-3" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?php echo BASEURL; ?>/asetbiologis/hapus/<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger rounded-end-pill px-3" onclick="return confirm('Apakah Anda yakin ingin menghapus aset ini?')" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Penyesuaian Nilai Wajar -->
<div class="modal fade" id="modalPenyesuaian" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Penyesuaian Nilai Wajar Aset Biologis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo BASEURL; ?>/asetbiologis/sesuaikan_batch" method="post">
                <div class="modal-body py-4">
                    <div class="mb-3 w-25">
                        <label class="form-label fw-bold small">Tanggal Penyesuaian</label>
                        <input type="date" name="tanggal_penyesuaian" class="form-control rounded-3" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="table-responsive" style="max-height: 400px;">
                        <table class="table table-bordered align-middle">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th>Aset</th>
                                    <th>Kuantitas</th>
                                    <th class="text-end">Nilai Tercatat Lama</th>
                                    <th>Nilai Wajar Baru</th>
                                    <th>Biaya Jual Baru</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['aset'] as $row): if ($row['status'] == 'Aktif' && $row['metode_pengukuran'] == 'Nilai Wajar'): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($row['nama_aset']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($row['kode_aset']); ?></small>
                                        <input type="hidden" name="penyesuaian[<?php echo $row['id']; ?>][id_aset]" value="<?php echo $row['id']; ?>">
                                    </td>
                                    <td><?php echo htmlspecialchars($row['kuantitas'] . ' ' . $row['satuan']); ?></td>
                                    <td class="text-end">Rp <?php echo number_format($row['nilai_tercatat'], 2, ',', '.'); ?></td>
                                    <td>
                                        <input type="number" step="0.01" name="penyesuaian[<?php echo $row['id']; ?>][nilai_wajar_baru]" class="form-control form-control-sm" placeholder="Opsional jika tidak berubah">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="penyesuaian[<?php echo $row['id']; ?>][biaya_jual_baru]" class="form-control form-control-sm" placeholder="Opsional jika tidak berubah">
                                    </td>
                                </tr>
                                <?php endif; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Proses Penyesuaian</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Panen -->
<div class="modal fade" id="modalPanen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Catat Panen / Agrikultur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo BASEURL; ?>/asetbiologis/panen" method="post">
                <div class="modal-body py-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Aset Sumber</label>
                            <select name="id_aset" class="form-select rounded-3 search-select shadow-none" required>
                                <option value="">-- Pilih Aset --</option>
                                <?php foreach ($data['aset'] as $row): if ($row['status'] == 'Aktif'): ?>
                                <option value="<?php echo $row['id']; ?>">[<?php echo htmlspecialchars($row['kode_aset']); ?>] <?php echo htmlspecialchars($row['nama_aset']); ?></option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Tanggal Panen</label>
                            <input type="date" name="tanggal" class="form-control rounded-3" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Nama Hasil Panen</label>
                            <input type="text" name="nama_hasil" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Kuantitas</label>
                            <input type="number" step="0.01" name="kuantitas" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Satuan</label>
                            <input type="text" name="satuan" class="form-control rounded-3" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Nilai Wajar Saat Panen</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" step="0.01" name="nilai_wajar" class="form-control rounded-3 border-start-0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Estimasi Biaya Jual/Panen</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" step="0.01" name="biaya_panen" class="form-control rounded-3 border-start-0" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Akun Persediaan (Debit)</label>
                        <select name="akun_persediaan" class="form-select rounded-3 search-select shadow-none" required>
                            <option value="">-- Pilih Akun Persediaan --</option>
                            <?php foreach($data['akun'] as $row): if(substr($row['kode_akun'], 0, 1) == '1' && $row['tipe_akun'] == 'Detail'): ?>
                            <option value="<?php echo $row['kode_akun']; ?>">[<?php echo $row['kode_akun']; ?>] <?php echo htmlspecialchars($row['nama_akun']); ?></option>
                            <?php endif; endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Catat Panen</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Pelepasan -->
<div class="modal fade" id="modalPelepasan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Pelepasan Aset (Jual / Mati)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo BASEURL; ?>/asetbiologis/pelepasan" method="post">
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Aset</label>
                        <select name="id_aset" class="form-select rounded-3 search-select shadow-none" required>
                            <option value="">-- Pilih Aset --</option>
                            <?php foreach ($data['aset'] as $row): if ($row['status'] == 'Aktif'): ?>
                            <option value="<?php echo $row['id']; ?>">[<?php echo htmlspecialchars($row['kode_aset']); ?>] <?php echo htmlspecialchars($row['nama_aset']); ?> (Sisa: <?php echo htmlspecialchars($row['kuantitas'] . ' ' . $row['satuan']); ?>)</option>
                            <?php endif; endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Tipe Pelepasan</label>
                            <select name="tipe_pelepasan" id="tipePelepasan" class="form-select rounded-3 shadow-none" required>
                                <option value="Penjualan">Penjualan</option>
                                <option value="Kematian">Kematian</option>
                                <option value="Hapus Buku">Hapus Buku</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Kuantitas Dilepas</label>
                            <input type="number" step="0.01" name="kuantitas" class="form-control rounded-3" required>
                        </div>
                    </div>
                    <div class="mb-3" id="divNilaiJual">
                        <label class="form-label fw-bold small">Total Nilai Jual</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">Rp</span>
                            <input type="number" step="0.01" name="nilai_jual" class="form-control rounded-3 border-start-0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Keterangan</label>
                        <textarea name="keterangan" class="form-control rounded-3" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4">Proses Pelepasan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tom Select init
    if (typeof TomSelect !== 'undefined') {
        document.querySelectorAll('.search-select').forEach(function(el) {
            new TomSelect(el, {
                create: false,
                sortField: { field: "text", direction: "asc" }
            });
        });
    }

    // Filtering logic
    const filterKat = document.getElementById('filterKategori');
    const filterStat = document.getElementById('filterStatus');
    const rows = document.querySelectorAll('#tableAset tbody tr[data-kategori]');

    function applyFilters() {
        const kat = filterKat.value;
        const stat = filterStat.value;

        rows.forEach(row => {
            const rowKat = row.getAttribute('data-kategori');
            const rowStat = row.getAttribute('data-status');
            const matchKat = kat === '' || rowKat === kat;
            const matchStat = stat === '' || rowStat === stat;
            row.style.display = matchKat && matchStat ? '' : 'none';
        });
    }

    if (filterKat) filterKat.addEventListener('change', applyFilters);
    if (filterStat) filterStat.addEventListener('change', applyFilters);

    // Pelepasan logic
    const tipePelepasan = document.getElementById('tipePelepasan');
    const divNilaiJual = document.getElementById('divNilaiJual');
    if (tipePelepasan && divNilaiJual) {
        tipePelepasan.addEventListener('change', function() {
            if (this.value === 'Penjualan') {
                divNilaiJual.style.display = 'block';
                divNilaiJual.querySelector('input').required = true;
            } else {
                divNilaiJual.style.display = 'none';
                divNilaiJual.querySelector('input').required = false;
            }
        });
    }
});
</script>
