<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Panel Pengelolaan Hasil Kurasi UMKM</h3>
        <p class="text-muted small mb-0">Verifikasi, penilaian kelayakan akuntansi SAK EP, dan rekomendasi pembiayaan UMKM Jawa Timur.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo BASEURL; ?>/kurasi" target="_blank" class="btn btn-outline-success rounded-pill px-3">
            <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Halaman Kurasi Publik
        </a>
        <a href="<?php echo BASEURL; ?>/kurasi/ajukan" target="_blank" class="btn btn-primary rounded-pill px-3">
            <i class="bi bi-plus-lg me-1"></i> Form Pengajuan Baru
        </a>
    </div>
</div>

<?php Flash::flash(); ?>

<!-- 4 Summary Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="d-flex align-items-center">
                <div class="p-3 bg-primary-subtle text-primary rounded-4 me-3">
                    <i class="bi bi-folder2-open fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Pengajuan</div>
                    <h3 class="fw-bold mb-0"><?php echo $data['summary']['total'] ?? 0; ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="d-flex align-items-center">
                <div class="p-3 bg-warning-subtle text-warning rounded-4 me-3">
                    <i class="bi bi-hourglass-split fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Menunggu Kurasi</div>
                    <h3 class="fw-bold mb-0 text-warning"><?php echo $data['summary']['total_pending'] ?? 0; ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="d-flex align-items-center">
                <div class="p-3 bg-success-subtle text-success rounded-4 me-3">
                    <i class="bi bi-patch-check-fill fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Disetujui (Bankable)</div>
                    <h3 class="fw-bold mb-0 text-success"><?php echo $data['summary']['total_approved'] ?? 0; ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="d-flex align-items-center">
                <div class="p-3 bg-info-subtle text-info rounded-4 me-3">
                    <i class="bi bi-trophy-fill fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small">Lolos Grade A</div>
                    <h3 class="fw-bold mb-0 text-info"><?php echo $data['summary']['total_grade_a'] ?? 0; ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="d-flex flex-wrap gap-2">
            <a href="<?php echo BASEURL; ?>/kurasi/kelola?status=Semua" class="btn btn-sm <?php echo ($data['selected_status'] == 'Semua') ? 'btn-dark' : 'btn-light'; ?> rounded-pill px-3">
                Semua Status
            </a>
            <a href="<?php echo BASEURL; ?>/kurasi/kelola?status=Menunggu Kurasi" class="btn btn-sm <?php echo ($data['selected_status'] == 'Menunggu Kurasi') ? 'btn-warning text-dark' : 'btn-light'; ?> rounded-pill px-3">
                <i class="bi bi-clock me-1"></i> Menunggu Kurasi
            </a>
            <a href="<?php echo BASEURL; ?>/kurasi/kelola?status=Disetujui" class="btn btn-sm <?php echo ($data['selected_status'] == 'Disetujui') ? 'btn-success' : 'btn-light'; ?> rounded-pill px-3">
                <i class="bi bi-check-circle me-1"></i> Disetujui / Bankable
            </a>
            <a href="<?php echo BASEURL; ?>/kurasi/kelola?status=Ditolak" class="btn btn-sm <?php echo ($data['selected_status'] == 'Ditolak') ? 'btn-danger' : 'btn-light'; ?> rounded-pill px-3">
                <i class="bi bi-x-circle me-1"></i> Perlu Pembinaan
            </a>
        </div>
    </div>
</div>

<!-- Tabel Pengajuan Kurasi -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">ID / Tgl</th>
                        <th class="py-3">Nama Usaha & Sektor</th>
                        <th class="py-3">Pemilik & Kontak</th>
                        <th class="py-3">Wilayah</th>
                        <th class="py-3 text-end">Omset / Bln</th>
                        <th class="py-3 text-center">Skor & Grade</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="pe-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['pengajuan'])): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                Belum ada data pengajuan kurasi untuk status ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['pengajuan'] as $p): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-light text-dark border font-mono">#KRS-<?php echo $p['id_pengajuan']; ?></span>
                                    <div class="text-muted small mt-1"><?php echo date('d/m/Y', strtotime($p['created_at'])); ?></div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($p['nama_usaha']); ?></div>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill small" style="font-size: 0.72rem;">
                                        <?php echo htmlspecialchars($p['sektor']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div><?php echo htmlspecialchars($p['nama_pemilik']); ?></div>
                                    <?php 
                                        $cleanPhone = preg_replace('/[^0-9]/', '', $p['telepon']);
                                        if (substr($cleanPhone, 0, 1) === '0') $cleanPhone = '62' . substr($cleanPhone, 1);
                                    ?>
                                    <a href="https://wa.me/<?php echo $cleanPhone; ?>" target="_blank" class="text-success text-decoration-none small">
                                        <i class="bi bi-whatsapp me-1"></i><?php echo htmlspecialchars($p['telepon']); ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="small text-muted"><i class="bi bi-geo-alt me-1 text-danger"></i><?php echo htmlspecialchars($p['kota']); ?></span>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    Rp <?php echo number_format($p['omset'], 0, ',', '.'); ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?php echo ($p['grade'] == 'A') ? 'bg-success' : 'bg-primary'; ?> rounded-pill px-3 py-1">
                                        Grade <?php echo $p['grade']; ?> &bull; <?php echo $p['skor_kelayakan']; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($p['status'] == 'Disetujui'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i> Disetujui
                                        </span>
                                    <?php elseif ($p['status'] == 'Ditolak'): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">
                                            <i class="bi bi-x-circle-fill me-1"></i> Pembinaan
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1 text-dark">
                                            <i class="bi bi-clock-fill me-1 text-warning"></i> Menunggu
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3 me-2 btn-verifikasi"
                                            data-id="<?php echo $p['id_pengajuan']; ?>"
                                            data-nama="<?php echo htmlspecialchars($p['nama_usaha']); ?>"
                                            data-pemilik="<?php echo htmlspecialchars($p['nama_pemilik']); ?>"
                                            data-telepon="<?php echo htmlspecialchars($p['telepon']); ?>"
                                            data-kota="<?php echo htmlspecialchars($p['kota']); ?>"
                                            data-sektor="<?php echo htmlspecialchars($p['sektor']); ?>"
                                            data-omset="<?php echo number_format($p['omset'], 0, ',', '.'); ?>"
                                            data-laba="<?php echo number_format($p['laba_bersih'], 0, ',', '.'); ?>"
                                            data-aset="<?php echo number_format($p['aset_usaha'], 0, ',', '.'); ?>"
                                            data-metode="<?php echo htmlspecialchars($p['metode_pembukuan']); ?>"
                                            data-skor="<?php echo $p['skor_kelayakan']; ?>"
                                            data-grade="<?php echo $p['grade']; ?>"
                                            data-status="<?php echo htmlspecialchars($p['status']); ?>"
                                            data-nda="<?php echo !empty($p['agree_nda']) ? 1 : 0; ?>"
                                            data-spk="<?php echo !empty($p['agree_spk']) ? 1 : 0; ?>"
                                            data-pakta="<?php echo !empty($p['agree_pakta']) ? 1 : 0; ?>"
                                            data-hardcopy="<?php echo !empty($p['agree_hardcopy']) ? 1 : 0; ?>"
                                            data-profit="<?php echo !empty($p['agree_share_profit']) ? 1 : 0; ?>"
                                            data-rekomendasi="<?php echo htmlspecialchars($p['rekomendasi_pembiayaan'] ?? ''); ?>"
                                            data-catatan="<?php echo htmlspecialchars($p['catatan_kurator'] ?? ''); ?>"
                                            data-deskripsi="<?php echo htmlspecialchars($p['deskripsi'] ?? ''); ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalVerifikasi">
                                            <i class="bi bi-pencil-square me-1"></i> Nilai / Proses
                                        </button>
                                        <a href="<?php echo BASEURL; ?>/kurasi/hapus/<?php echo $p['id_pengajuan']; ?>" 
                                            class="btn btn-sm btn-outline-danger rounded-pill px-2"
                                            onclick="return confirm('Hapus pengajuan kurasi #KRS-<?php echo $p['id_pengajuan']; ?>?');"
                                            title="Hapus Pengajuan">
                                            <i class="bi bi-trash"></i>
                                        </a>
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

<!-- Modal Verifikasi & Penilaian Kurasi -->
<div class="modal fade" id="modalVerifikasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="<?php echo BASEURL; ?>/kurasi/update_status" method="POST">
                <input type="hidden" name="id_pengajuan" id="modalId">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="modalTitle">Penilaian & Validasi Kurasi UMKM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Ringkasan Info Pengajuan -->
                    <div class="p-3 bg-light rounded-3 mb-4 border small">
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Nama Usaha:</span>
                                <strong id="modalNamaUsaha" class="fs-6">-</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Pemilik / Kontak:</span>
                                <strong id="modalPemilik">-</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Kota / Wilayah:</span>
                                <span id="modalKota">-</span> (<span id="modalSektor">-</span>)
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Metode Pembukuan:</span>
                                <span id="modalMetode" class="badge bg-secondary">-</span>
                            </div>
                            <div class="col-sm-4 pt-2 border-top">
                                <span class="text-muted d-block">Omset Bulanan:</span>
                                <strong class="text-success" id="modalOmset">-</strong>
                            </div>
                            <div class="col-sm-4 pt-2 border-top">
                                <span class="text-muted d-block">Estimasi Laba:</span>
                                <strong class="text-primary" id="modalLaba">-</strong>
                            </div>
                            <div class="col-sm-4 pt-2 border-top">
                                <span class="text-muted d-block">Total Aset:</span>
                                <strong id="modalAset">-</strong>
                            </div>
                            <div class="col-12 pt-2 border-top">
                                <span class="text-muted d-block">Deskripsi Usaha:</span>
                                <div id="modalDeskripsi" class="text-dark fst-italic mb-2">-</div>
                            </div>
                            <div class="col-12 pt-2 border-top">
                                <span class="text-muted d-block fw-semibold mb-1">Status Komitmen & Dokumen Legalitas:</span>
                                <div class="d-flex flex-wrap gap-2 small">
                                    <span id="badgeNda" class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                        <i class="bi bi-check-circle me-1"></i> NDA Disetujui
                                    </span>
                                    <span id="badgeSpk" class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                        <i class="bi bi-check-circle me-1"></i> SPK Disetujui
                                    </span>
                                    <span id="badgePakta" class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                        <i class="bi bi-check-circle me-1"></i> Pakta Integritas Disetujui
                                    </span>
                                    <span id="badgeHardcopy" class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                        <i class="bi bi-envelope-paper me-1"></i> Kirim Hardcopy ke Mojokerto
                                    </span>
                                    <span id="badgeProfit" class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                        <i class="bi bi-cash-stack me-1"></i> Share 0,1% GP Th-3
                                    </span>
                                </div>
                                <div class="mt-2 small text-muted">
                                    Link Dokumen: 
                                    <a href="https://docs.google.com/document/d/1kR4Iefb0fcEBS4LkuG_j2By-GF-S7YbHuSV6GBskw_o/edit?tab=t.0" target="_blank" class="text-success me-2">NDA ↗</a>
                                    <a href="https://docs.google.com/document/d/1wqptEM41hmiiIs4-yTjcJEJODR9hmMIPnqgX-L9GdO8/edit?tab=t.0" target="_blank" class="text-success me-2">SPK ↗</a>
                                    <a href="https://docs.google.com/document/d/1mFWWtUGIAL9XADrPf_M8joirW4EWBq92Xi3PaiBKdWw/edit?tab=t.0" target="_blank" class="text-success">Pakta Integritas ↗</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Input Evaluasi Kurator -->
                    <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-award me-1"></i> Keputusan Hasil Kurasi Superadmin</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Status Kurasi <span class="text-danger">*</span></label>
                            <select name="status" id="modalStatus" class="form-select" required>
                                <option value="Menunggu Kurasi">Menunggu Kurasi</option>
                                <option value="Disetujui">Disetujui (Bankable / Layak)</option>
                                <option value="Ditolak">Ditolak / Perlu Pembinaan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Grade Kelayakan</label>
                            <select name="grade" id="modalGrade" class="form-select">
                                <option value="A">Grade A (Sangat Layak / Bankable)</option>
                                <option value="B+">Grade B+ (Layak KUR Mikro / BUMDes)</option>
                                <option value="B">Grade B (Cukup Layak / Super Mikro)</option>
                                <option value="C">Grade C (Perlu Pembinaan SAK EP)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Skor Kelayakan (0 - 100)</label>
                            <input type="number" name="skor_kelayakan" id="modalSkor" class="form-control" min="0" max="100" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Rekomendasi Pembiayaan / Program</label>
                            <input type="text" name="rekomendasi_pembiayaan" id="modalRekomendasi" class="form-control" placeholder="Contoh: KUR Komersial Bank Jatim s/d Rp 250 Juta & CSR Semen Indonesia">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Catatan Kurator / Arahan Pendampingan</label>
                            <textarea name="catatan_kurator" id="modalCatatan" class="form-control" rows="3" placeholder="Tuliskan catatan evaluasi pembukuan dan tindak lanjut permodalan..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-save me-1"></i> Simpan Hasil Kurasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-verifikasi').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('modalId').value = this.dataset.id;
            document.getElementById('modalTitle').innerText = 'Validasi Kurasi: ' + this.dataset.nama;
            document.getElementById('modalNamaUsaha').innerText = this.dataset.nama;
            document.getElementById('modalPemilik').innerText = this.dataset.pemilik + ' (' + this.dataset.telepon + ')';
            document.getElementById('modalKota').innerText = this.dataset.kota;
            document.getElementById('modalSektor').innerText = this.dataset.sektor;
            document.getElementById('modalOmset').innerText = 'Rp ' + this.dataset.omset;
            document.getElementById('modalLaba').innerText = 'Rp ' + this.dataset.laba;
            document.getElementById('modalAset').innerText = 'Rp ' + this.dataset.aset;
            document.getElementById('modalMetode').innerText = this.dataset.metode;
            document.getElementById('modalDeskripsi').innerText = this.dataset.deskripsi || '-';

            document.getElementById('modalStatus').value = this.dataset.status;
            document.getElementById('modalGrade').value = this.dataset.grade;
            document.getElementById('modalSkor').value = this.dataset.skor;
            document.getElementById('modalRekomendasi').value = this.dataset.rekomendasi;
            document.getElementById('modalCatatan').value = this.dataset.catatan;

            // Update badge persetujuan legalitas
            const updateBadge = (id, val, labelYes, labelNo) => {
                const el = document.getElementById(id);
                if (el) {
                    if (parseInt(val) === 1) {
                        el.className = 'badge bg-success-subtle text-success border border-success-subtle rounded-pill';
                        el.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> ' + labelYes;
                    } else {
                        el.className = 'badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill';
                        el.innerHTML = '<i class="bi bi-x-circle me-1"></i> ' + labelNo;
                    }
                }
            };
            updateBadge('badgeNda', this.dataset.nda, 'NDA Disetujui', 'NDA Belum');
            updateBadge('badgeSpk', this.dataset.spk, 'SPK Disetujui', 'SPK Belum');
            updateBadge('badgePakta', this.dataset.pakta, 'Pakta Integritas Disetujui', 'Pakta Belum');
            updateBadge('badgeHardcopy', this.dataset.hardcopy, 'Kirim Hardcopy ke Mojokerto Disetujui', 'Hardcopy Belum');
            updateBadge('badgeProfit', this.dataset.profit, 'Share 0,1% GP Th-3 Disetujui', 'Share Profit Belum');
        });
    });
});
</script>
