<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-0">Riwayat Jurnal</h3>
        <p class="text-muted small">Kelola dan pantau seluruh catatan transaksi keuangan.</p>
    </div>
    <div class="d-flex gap-2">
        <form action="<?php echo BASEURL; ?>/jurnal" method="GET" class="d-flex gap-2">
            <select name="id_unit" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 200px;">
                <option value="">-- Semua Unit (Konsolidasi) --</option>
                <?php foreach($data['units'] as $u): ?>
                    <option value="<?php echo $u['id_unit']; ?>" <?php echo ($data['selected_unit'] == $u['id_unit']) ? 'selected' : ''; ?>>
                        <?php echo $u['nama_unit']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <a href="<?php echo BASEURL; ?>/jurnal/tambah" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Jurnal Baru
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Transaksi</th>
                        <th>Deskripsi</th>
                        <th>Sumber</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        // Cek peran user sekali saja untuk efisiensi
                        $isManagerOrAdmin = Auth::isAdmin() || Auth::isManager(); 
                        if (empty($data['jurnal'])): 
                    ?>
                        <tr><td colspan="6" class="text-center py-4">Belum ada entri jurnal.</td></tr>
                    <?php else: ?>
                        <?php foreach ($data['jurnal'] as $jurnal): ?>
                        <tr>
                            <td><?php echo date('d M Y', strtotime($jurnal['tanggal'])); ?></td>
                            <td><?php echo htmlspecialchars($jurnal['no_transaksi']); ?></td>
                            <td><?php echo htmlspecialchars($jurnal['deskripsi']); ?></td>
                            <td>
                                <?php 
                                    $sumber = htmlspecialchars($jurnal['sumber_jurnal']);
                                    $badge_class = 'text-bg-info';
                                    if ($sumber == 'Penjualan') $badge_class = 'text-bg-success';
                                    if ($sumber == 'Pembelian') $badge_class = 'text-bg-warning';
                                ?>
                                <span class="badge rounded-pill <?php echo $badge_class; ?>"><?php echo $sumber; ?></span>
                            </td>
                            <td class="text-end"><?php echo number_format($jurnal['total'], 2, ',', '.'); ?></td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm">
                                    <a href="<?php echo BASEURL; ?>/jurnal/detail/<?php echo $jurnal['id_jurnal']; ?>" class="btn btn-sm btn-info text-white" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo BASEURL; ?>/jurnal/cetak/<?php echo $jurnal['id_jurnal']; ?>" target="_blank" class="btn btn-sm btn-outline-warning text-dark" title="Cetak Bukti Memorial (Estetik)">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    <?php if ($jurnal['is_locked'] == 1): ?>
                                        <?php if ($isManagerOrAdmin): // Jika Manajer atau Admin, berikan opsi pembatalan khusus ?>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Opsi Pembatalan">
                                                    <i class="bi bi-lock-fill"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <?php if ($jurnal['sumber_jurnal'] == 'Penjualan' && !empty($jurnal['id_penjualan'])): ?>
                                                        <li><a class="dropdown-item text-danger small" href="<?php echo BASEURL; ?>/penjualan/hapus/<?php echo $jurnal['id_penjualan']; ?>" onclick="return confirm('Anda akan membatalkan FAKTUR PENJUALAN terkait. Lanjutkan?');">Batalkan Penjualan</a></li>
                                                    <?php elseif ($jurnal['sumber_jurnal'] == 'Pembelian' && !empty($jurnal['id_pembelian'])): ?>
                                                        <li><a class="dropdown-item text-danger small" href="<?php echo BASEURL; ?>/pembelian/hapus/<?php echo $jurnal['id_pembelian']; ?>" onclick="return confirm('Anda akan membatalkan FAKTUR PEMBELIAN terkait. Lanjutkan?');">Batalkan Pembelian</a></li>
                                                    <?php else: ?>
                                                        <li><span class="dropdown-item text-muted small">Terkunci (Sistem)</span></li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        <?php else: // Jika Staff, tampilkan status terkunci ?>
                                            <button class="btn btn-sm btn-secondary disabled" title="Terkunci">
                                                <i class="bi bi-lock-fill"></i>
                                            </button>
                                        <?php endif; ?>
                                    <?php else: // Jika jurnal tidak terkunci (entri Jurnal Umum manual) ?>
                                        <?php if ($isManagerOrAdmin): ?>
                                            <a href="<?php echo BASEURL; ?>/jurnal/edit/<?php echo $jurnal['id_jurnal']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?php echo BASEURL; ?>/jurnal/hapus/<?php echo $jurnal['id_jurnal']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus jurnal ini?');" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-light disabled"><i class="bi bi-dash"></i></button>
                                        <?php endif; ?>
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

