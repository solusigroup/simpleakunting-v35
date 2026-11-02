<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-archive-fill text-primary me-2"></i>Tutup Buku & Period Lock</h3>
        <p class="text-muted small mb-0">Standardisasi SAK EP: Penutupan akun nominal (Pendapatan & Beban) ke Laba Ditahan dan penguncian periode transaksi.</p>
    </div>
    <div>
        <?php if (!empty($data['latest_closed'])): ?>
            <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill">
                <i class="bi bi-lock-fill me-1"></i> Terakhir Ditutup: Bulan <?php echo str_pad($data['latest_closed']['bulan'], 2, '0', STR_PAD_LEFT) . '/' . $data['latest_closed']['tahun']; ?>
            </span>
        <?php else: ?>
            <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">
                <i class="bi bi-unlock me-1"></i> Belum ada periode yang ditutup
            </span>
        <?php endif; ?>
    </div>
</div>

<?php Flash::flash(); ?>

<!-- Info Banner Standardisasi SAK EP -->
<div class="alert alert-info border-0 shadow-sm rounded-4 mb-4 d-flex align-items-start gap-3 p-3">
    <i class="bi bi-shield-check fs-2 text-info"></i>
    <div>
        <h6 class="fw-bold mb-1">Mekanisme Integritas & Period Lock SAK EP</h6>
        <p class="small mb-0 text-muted">
            Proses Tutup Buku memindahkan saldo bersih akun Pendapatan dan Beban ke akun <strong>Laba Ditahan (Ekuitas)</strong>. 
            Setelah periode ditutup (<strong>Status: Closed</strong>), sistem mengaktifkan proteksi <em>Period Lock</em> secara otomatis sehingga transaksi pada periode tersebut tidak dapat dimanipulasi atau dihapus oleh staf operasional demi integritas audit perbankan.
        </p>
    </div>
</div>

<div class="row g-4">
    <!-- Opsi Tutup Buku Bulanan -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white border-bottom p-3">
                <h5 class="fw-bold mb-0 text-info"><i class="bi bi-calendar-month me-2"></i>Tutup Buku Bulanan</h5>
            </div>
            <div class="card-body d-flex flex-column p-4">
                <p class="text-muted small flex-grow-1">
                    Menutup akun pendapatan & beban secara spesifik untuk 1 bulan kalender yang dipilih dan mengunci seluruh transaksi operasional pada bulan tersebut.
                </p>
                <form action="<?php echo BASEURL; ?>/tutupBuku/index" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo Auth::getCsrfToken(); ?>">
                    <input type="hidden" name="tipe_proses" value="Bulanan">
                    <div class="mb-3">
                        <label for="periode_bulanan" class="form-label fw-semibold">Pilih Periode Bulan & Tahun</label>
                        <input type="month" name="periode" id="periode_bulanan" class="form-control form-control-lg" value="<?php echo htmlspecialchars($data['periode_bulanan_next'] ?? date('Y-m')); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-info text-white w-100 py-2 fw-bold rounded-pill">
                        <i class="bi bi-eye me-1"></i> Pratinjau Jurnal Penutup Bulanan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Opsi Tutup Buku Tahunan -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white border-bottom p-3">
                <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-calendar-date me-2"></i>Tutup Buku Tahunan (Akhir Tahun)</h5>
            </div>
            <div class="card-body d-flex flex-column p-4">
                <p class="text-muted small flex-grow-1">
                    Menutup total saldo pendapatan & beban selama 1 tahun buku penuh (12 bulan) dan mengunci seluruh 12 bulan tersebut untuk pembukuan tahun berjalan.
                </p>
                <form action="<?php echo BASEURL; ?>/tutupBuku/index" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo Auth::getCsrfToken(); ?>">
                    <input type="hidden" name="tipe_proses" value="Tahunan">
                    <div class="mb-3">
                        <label for="periode_tahunan" class="form-label fw-semibold">Pilih Tahun Pembukuan</label>
                        <select name="periode" id="periode_tahunan" class="form-select form-select-lg" required>
                            <?php for ($i = (int)date('Y'); $i >= (int)date('Y') - 5; $i--): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-pill">
                        <i class="bi bi-eye me-1"></i> Pratinjau Jurnal Penutup Tahunan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Area Pratinjau Jurnal Penutup -->
<?php if (isset($data['preview']) && $data['preview'] !== null): ?>
<?php 
    $totalPendapatan = (float)($data['preview']['total_pendapatan_1'] ?? 0);
    $totalBeban = (float)($data['preview']['total_beban_1'] ?? 0);
    $labaRugi = $totalPendapatan - $totalBeban;
?>
<div class="card shadow-sm border-0 rounded-4 mt-4 overflow-hidden border-start border-4 border-warning">
    <div class="card-header bg-warning bg-opacity-10 border-bottom p-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-journal-check text-warning me-2"></i>Pratinjau Jurnal Penutup <?php echo htmlspecialchars($data['tipe_proses'] ?? ''); ?> (Periode: <?php echo htmlspecialchars($data['periode_label'] ?? ''); ?>)
        </h5>
        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Menunggu Konfirmasi</span>
    </div>
    <div class="card-body p-4">
        <!-- 3 Metrik Pratinjau -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="p-3 bg-light rounded-3 border">
                    <div class="text-muted small">Total Akumulasi Pendapatan</div>
                    <h5 class="fw-bold text-success mb-0">Rp <?php echo number_format($totalPendapatan, 2, ',', '.'); ?></h5>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light rounded-3 border">
                    <div class="text-muted small">Total Akumulasi Beban</div>
                    <h5 class="fw-bold text-danger mb-0">Rp <?php echo number_format($totalBeban, 2, ',', '.'); ?></h5>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 <?php echo ($labaRugi >= 0) ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?> rounded-3 border">
                    <div class="small fw-semibold">Hasil Bersih Dipindahkan ke Laba Ditahan</div>
                    <h5 class="fw-bold mb-0">Rp <?php echo number_format($labaRugi, 2, ',', '.'); ?> (<?php echo ($labaRugi >= 0) ? 'Laba' : 'Rugi'; ?>)</h5>
                </div>
            </div>
        </div>

        <!-- Rincian Akun yang Akan Ditutup -->
        <h6 class="fw-bold mb-2">Simulasi Entri Ayat Jurnal Penutup:</h6>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 15%;">Kode Akun</th>
                        <th>Nama Akun</th>
                        <th class="text-end" style="width: 20%;">Debit (Rp)</th>
                        <th class="text-end" style="width: 20%;">Kredit (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- 1. Menutup Akun Pendapatan -->
                    <tr class="table-secondary fw-bold">
                        <td colspan="4"><i class="bi bi-arrow-right-circle me-1"></i> 1. Menutup Akun Pendapatan ke Ikhtisar L/R</td>
                    </tr>
                    <?php if (!empty($data['preview']['pendapatan'])): ?>
                        <?php foreach ($data['preview']['pendapatan'] as $p): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($p['kode_akun']); ?></code></td>
                                <td><?php echo htmlspecialchars($p['nama_akun']); ?></td>
                                <td class="text-end text-success fw-semibold"><?php echo number_format($p['total_1'], 2, ',', '.'); ?></td>
                                <td class="text-end">-</td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="fw-semibold">
                            <td><code>3-3000</code></td>
                            <td class="ps-4">Ikhtisar Laba Rugi</td>
                            <td class="text-end">-</td>
                            <td class="text-end text-success fw-bold"><?php echo number_format($totalPendapatan, 2, ',', '.'); ?></td>
                        </tr>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-muted text-center py-2">Tidak ada saldo akun pendapatan pada periode ini.</td></tr>
                    <?php endif; ?>

                    <!-- 2. Menutup Akun Beban -->
                    <tr class="table-secondary fw-bold">
                        <td colspan="4"><i class="bi bi-arrow-right-circle me-1"></i> 2. Menutup Akun Beban ke Ikhtisar L/R</td>
                    </tr>
                    <?php if (!empty($data['preview']['beban'])): ?>
                        <tr class="fw-semibold">
                            <td><code>3-3000</code></td>
                            <td>Ikhtisar Laba Rugi</td>
                            <td class="text-end text-danger fw-bold"><?php echo number_format($totalBeban, 2, ',', '.'); ?></td>
                            <td class="text-end">-</td>
                        </tr>
                        <?php foreach ($data['preview']['beban'] as $b): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($b['kode_akun']); ?></code></td>
                                <td class="ps-4"><?php echo htmlspecialchars($b['nama_akun']); ?></td>
                                <td class="text-end">-</td>
                                <td class="text-end text-danger fw-semibold"><?php echo number_format($b['total_1'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-muted text-center py-2">Tidak ada saldo akun beban pada periode ini.</td></tr>
                    <?php endif; ?>

                    <!-- 3. Menutup Ikhtisar L/R ke Laba Ditahan -->
                    <tr class="table-secondary fw-bold">
                        <td colspan="4"><i class="bi bi-arrow-right-circle me-1"></i> 3. Menutup Saldo Laba (Rugi) Bersih ke Ekuitas (Laba Ditahan)</td>
                    </tr>
                    <?php if ($labaRugi >= 0): ?>
                        <tr>
                            <td><code>3-3000</code></td>
                            <td>Ikhtisar Laba Rugi</td>
                            <td class="text-end fw-bold"><?php echo number_format($labaRugi, 2, ',', '.'); ?></td>
                            <td class="text-end">-</td>
                        </tr>
                        <tr>
                            <td><code>3-2000</code></td>
                            <td class="ps-4">Laba Ditahan</td>
                            <td class="text-end">-</td>
                            <td class="text-end fw-bold"><?php echo number_format($labaRugi, 2, ',', '.'); ?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td><code>3-2000</code></td>
                            <td>Laba Ditahan</td>
                            <td class="text-end fw-bold"><?php echo number_format(abs($labaRugi), 2, ',', '.'); ?></td>
                            <td class="text-end">-</td>
                        </tr>
                        <tr>
                            <td><code>3-3000</code></td>
                            <td class="ps-4">Ikhtisar Laba Rugi</td>
                            <td class="text-end">-</td>
                            <td class="text-end fw-bold"><?php echo number_format(abs($labaRugi), 2, ',', '.'); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2 border-top">
            <span class="text-muted small">
                <i class="bi bi-info-circle me-1"></i> Dengan memproses, ayat jurnal di atas otomatis dibukukan dan periode akan berstatus <strong>TERKUNCI (LOCKED)</strong>.
            </span>
            <div class="d-flex gap-2">
                <a href="<?php echo BASEURL; ?>/tutupBuku" class="btn btn-secondary px-4 rounded-pill">
                    <i class="bi bi-x-circle me-1"></i> Batal
                </a>
                <form action="<?php echo BASEURL; ?>/tutupBuku/proses" method="post" onsubmit="return confirm('KONFIRMASI AKHIR:\n\nApakah Anda yakin ingin memproses Tutup Buku untuk <?php echo htmlspecialchars($data['periode_label'] ?? ''); ?>?\n\nPeriode transaksi akan dikunci.');">
                    <input type="hidden" name="csrf_token" value="<?php echo Auth::getCsrfToken(); ?>">
                    <input type="hidden" name="periode" value="<?php echo htmlspecialchars($data['periode_raw'] ?? ''); ?>">
                    <input type="hidden" name="tipe_proses" value="<?php echo htmlspecialchars($data['tipe_proses'] ?? ''); ?>">
                    <button type="submit" class="btn btn-success px-4 fw-bold rounded-pill shadow-sm">
                        <i class="bi bi-lock-fill me-1"></i> Ya, Proses dan Kunci Tutup Buku
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Tabel Riwayat Tutup Buku -->
<div class="card shadow-sm border-0 rounded-4 mt-4">
    <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Periode Tutup Buku</h5>
        <span class="text-muted small">Total: <?php echo count($data['history_closed'] ?? []); ?> Periode Terdaftar</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 5%;">No</th>
                        <th>Periode</th>
                        <th>Tipe Tutup Buku</th>
                        <th>Waktu Eksekusi</th>
                        <th>Status Integritas</th>
                        <th class="text-center pe-3" style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['history_closed'])): ?>
                        <?php $no = 1; foreach ($data['history_closed'] as $h): ?>
                            <tr>
                                <td class="ps-3 text-muted"><?php echo $no++; ?></td>
                                <td>
                                    <strong><?php echo date('F', mktime(0, 0, 0, (int)$h['bulan'], 10)) . ' ' . $h['tahun']; ?></strong>
                                    <div class="small text-muted font-monospace"><?php echo $h['tahun'] . '-' . str_pad($h['bulan'], 2, '0', STR_PAD_LEFT); ?></div>
                                </td>
                                <td>
                                    <span class="badge <?php echo ($h['tipe_proses'] == 'Tahunan') ? 'bg-primary' : 'bg-info text-dark'; ?> rounded-pill px-3">
                                        <?php echo htmlspecialchars($h['tipe_proses']); ?>
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    <?php echo !empty($h['tanggal_tutup']) ? date('d/m/Y H:i', strtotime($h['tanggal_tutup'])) : '-'; ?>
                                </td>
                                <td>
                                    <?php if ($h['status'] === 'Closed'): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger rounded-pill px-3 py-1">
                                            <i class="bi bi-lock-fill me-1"></i> LOCKED (Closed)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3 py-1">
                                            <i class="bi bi-unlock-fill me-1"></i> OPEN
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-3">
                                    <?php if ($h['status'] === 'Closed'): ?>
                                        <a href="<?php echo BASEURL; ?>/tutupBuku/buka_kembali/<?php echo $h['id_periode']; ?>" 
                                           class="btn btn-outline-warning btn-sm rounded-pill px-3"
                                           onclick="return confirm('PERINGATAN:\nMembuka kembali periode ini akan menonaktifkan proteksi Period Lock sehingga transaksi pada periode <?php echo $h['tahun'] . '-' . str_pad($h['bulan'], 2, '0', STR_PAD_LEFT); ?> dapat diedit kembali.\n\nLanjutkan?');">
                                            <i class="bi bi-unlock me-1"></i> Buka Kembali
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-info-circle me-1"></i> Belum ada periode akuntansi yang ditutup bukunya.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
