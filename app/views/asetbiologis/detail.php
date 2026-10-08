<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Detail Aset Biologis: <?php echo htmlspecialchars($data['aset']['kode_aset']); ?></h3>
    <a href="<?php echo BASEURL; ?>/asetbiologis" class="btn btn-light rounded-pill px-4 border">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<ul class="nav nav-tabs mb-4" id="detailTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-medium" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">Informasi Detail</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-medium" id="penyesuaian-tab" data-bs-toggle="tab" data-bs-target="#penyesuaian" type="button" role="tab">Riwayat Penyesuaian</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-medium" id="panen-tab" data-bs-toggle="tab" data-bs-target="#panen" type="button" role="tab">Riwayat Panen</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-medium" id="pelepasan-tab" data-bs-toggle="tab" data-bs-target="#pelepasan" type="button" role="tab">Riwayat Pelepasan</button>
    </li>
</ul>

<div class="tab-content" id="detailTabsContent">
    <!-- TAB INFO -->
    <div class="tab-pane fade show active" id="info" role="tabpanel">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center mb-4 pb-4 border-bottom">
                    <div class="col-md-8">
                        <h4 class="fw-bold text-primary mb-1"><?php echo htmlspecialchars($data['aset']['nama_aset']); ?></h4>
                        <div class="d-flex gap-2 mb-2">
                            <span class="badge bg-secondary-subtle text-secondary"><?php echo htmlspecialchars($data['aset']['kategori']); ?></span>
                            <?php if ($data['aset']['status_kematangan'] == 'Menghasilkan'): ?>
                                <span class="badge bg-success-subtle text-success">Menghasilkan</span>
                            <?php else: ?>
                                <span class="badge bg-warning-subtle text-warning">Belum Menghasilkan</span>
                            <?php endif; ?>
                            <?php 
                                $status_color = match($data['aset']['status']) {
                                    'Aktif' => 'success',
                                    'Dipanen' => 'info',
                                    'Dijual' => 'warning',
                                    'Mati/Hapus' => 'danger',
                                    default => 'secondary'
                                };
                            ?>
                            <span class="badge bg-<?php echo $status_color; ?>-subtle text-<?php echo $status_color; ?>"><?php echo htmlspecialchars($data['aset']['status']); ?></span>
                        </div>
                        <p class="text-muted mb-0 small"><i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($data['aset']['lokasi'] ?? 'Lokasi tidak diset'); ?></p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <h6 class="text-muted mb-1">Nilai Tercatat Saat Ini</h6>
                        <h3 class="fw-bold mb-0 text-success">Rp <?php echo number_format($data['aset']['nilai_tercatat'], 2, ',', '.'); ?></h3>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold border-bottom pb-2 mb-3">Informasi Umum</h6>
                        <table class="table table-sm table-borderless">
                            <tr><td class="text-muted w-50">Jenis/Spesies</td><td class="fw-medium"><?php echo htmlspecialchars($data['aset']['jenis'] ?? '-'); ?></td></tr>
                            <tr><td class="text-muted">Tanggal Perolehan</td><td class="fw-medium"><?php echo date('d/m/Y', strtotime($data['aset']['tanggal_perolehan'])); ?></td></tr>
                            <tr><td class="text-muted">Kuantitas</td><td class="fw-medium"><?php echo format_kuantitas($data['aset']['kuantitas'], $data['aset']['satuan']); ?></td></tr>
                            <tr><td class="text-muted">Metode Pengukuran</td><td class="fw-medium"><?php echo htmlspecialchars($data['aset']['metode_pengukuran']); ?></td></tr>
                            <tr><td class="text-muted">Keterangan</td><td class="fw-medium"><?php echo htmlspecialchars($data['aset']['keterangan'] ?? '-'); ?></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold border-bottom pb-2 mb-3">Pemetaan Akun</h6>
                        <table class="table table-sm table-borderless">
                            <tr><td class="text-muted w-50">Akun Aset</td><td class="fw-medium"><?php echo htmlspecialchars($data['aset']['akun_aset'] ?? '-'); ?></td></tr>
                            <tr><td class="text-muted">Akun Keuntungan FV</td><td class="fw-medium"><?php echo htmlspecialchars($data['aset']['akun_keuntungan_fv'] ?? '-'); ?></td></tr>
                            <tr><td class="text-muted">Akun Kerugian FV</td><td class="fw-medium"><?php echo htmlspecialchars($data['aset']['akun_kerugian_fv'] ?? '-'); ?></td></tr>
                            <tr><td class="text-muted">Akun Hasil Panen</td><td class="fw-medium"><?php echo htmlspecialchars($data['aset']['akun_hasil_panen'] ?? '-'); ?></td></tr>
                        </table>
                    </div>
                </div>

                <?php if ($data['aset']['status_kematangan'] == 'Belum Menghasilkan' && $data['aset']['status'] == 'Aktif'): ?>
                <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                    <form action="<?php echo BASEURL; ?>/asetbiologis/reklasifikasi" method="post" onsubmit="return confirm('Reklasifikasi menjadi aset Menghasilkan/Matang?');">
                        <input type="hidden" name="id_aset" value="<?php echo $data['aset']['id']; ?>">
                        <button type="submit" class="btn btn-success rounded-pill px-4">
                            <i class="bi bi-arrow-repeat me-2"></i>Reklasifikasi ke Menghasilkan
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- TAB RIWAYAT PENYESUAIAN -->
    <div class="tab-pane fade" id="penyesuaian" role="tabpanel">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Tanggal</th>
                                <th>Tipe</th>
                                <th class="text-end">Nilai Lama</th>
                                <th class="text-end">Nilai Baru</th>
                                <th class="text-end">Selisih</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($data['penyesuaian'])): ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat penyesuaian.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($data['penyesuaian'] ?? [] as $row): ?>
                            <tr>
                                <td class="ps-4"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                <td><?php echo htmlspecialchars($row['tipe_penyesuaian']); ?></td>
                                <td class="text-end">Rp <?php echo number_format($row['nilai_tercatat_lama'], 2, ',', '.'); ?></td>
                                <td class="text-end fw-bold">Rp <?php echo number_format($row['nilai_tercatat_baru'], 2, ',', '.'); ?></td>
                                <td class="text-end fw-bold <?php echo $row['selisih'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo $row['selisih'] >= 0 ? '+' : ''; ?>Rp <?php echo number_format($row['selisih'], 2, ',', '.'); ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB RIWAYAT PANEN -->
    <div class="tab-pane fade" id="panen" role="tabpanel">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Tanggal</th>
                                <th>Nama Hasil</th>
                                <th class="text-end">Kuantitas</th>
                                <th class="text-end">Nilai Wajar</th>
                                <th class="text-end">Biaya Panen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($data['panen'])): ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat panen.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($data['panen'] ?? [] as $row): ?>
                            <tr>
                                <td class="ps-4"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                <td><div class="fw-bold"><?php echo htmlspecialchars($row['nama_hasil']); ?></div></td>
                                <td class="text-end"><?php echo htmlspecialchars($row['kuantitas'] . ' ' . $row['satuan']); ?></td>
                                <td class="text-end fw-medium text-success">Rp <?php echo number_format($row['nilai_wajar_panen'], 2, ',', '.'); ?></td>
                                <td class="text-end text-danger">Rp <?php echo number_format($row['biaya_jual_panen'], 2, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB RIWAYAT PELEPASAN -->
    <div class="tab-pane fade" id="pelepasan" role="tabpanel">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Tanggal</th>
                                <th>Tipe</th>
                                <th class="text-end">Kuantitas</th>
                                <th class="text-end">Nilai Tercatat</th>
                                <th class="text-end">Nilai Jual</th>
                                <th class="text-end">Laba/Rugi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($data['pelepasan'])): ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat pelepasan.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($data['pelepasan'] ?? [] as $row): ?>
                            <tr>
                                <td class="ps-4"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                <td>
                                    <?php 
                                        $badge = match($row['tipe_pelepasan']) {
                                            'Penjualan' => 'bg-success',
                                            'Kematian' => 'bg-danger',
                                            'Hapus Buku' => 'bg-secondary',
                                            default => 'bg-dark'
                                        };
                                    ?>
                                    <span class="badge <?php echo $badge; ?>"><?php echo htmlspecialchars($row['tipe_pelepasan']); ?></span>
                                </td>
                                <td class="text-end"><?php echo htmlspecialchars($row['kuantitas_dilepas']); ?></td>
                                <td class="text-end">Rp <?php echo number_format($row['nilai_tercatat_dilepas'], 2, ',', '.'); ?></td>
                                <td class="text-end fw-medium">Rp <?php echo number_format($row['nilai_jual'], 2, ',', '.'); ?></td>
                                <td class="text-end fw-bold <?php echo $row['laba_rugi'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo $row['laba_rugi'] >= 0 ? '+' : ''; ?>Rp <?php echo number_format($row['laba_rugi'], 2, ',', '.'); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
