<div class="row mb-4 no-print">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="<?php echo BASEURL; ?>/laporan/perubahanEkuitas" method="post" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fw-bold small">Periode Utama</label>
                        <div class="input-group">
                            <input type="date" name="tanggal_mulai_1" class="form-control" value="<?php echo $data['tanggal_mulai_1']; ?>">
                            <span class="input-group-text">s/d</span>
                            <input type="date" name="tanggal_selesai_1" class="form-control" value="<?php echo $data['tanggal_selesai_1']; ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="compareCheck" <?php echo !empty($data['tanggal_mulai_2']) ? 'checked' : ''; ?>>
                            <label class="form-check-label small fw-bold" for="compareCheck">Bandingkan Periode</label>
                        </div>
                        <div id="compareInputs" class="<?php echo !empty($data['tanggal_mulai_2']) ? '' : 'd-none'; ?>">
                            <div class="input-group">
                                <input type="date" name="tanggal_mulai_2" class="form-control" value="<?php echo $data['tanggal_mulai_2']; ?>">
                                <span class="input-group-text">s/d</span>
                                <input type="date" name="tanggal_selesai_2" class="form-control" value="<?php echo $data['tanggal_selesai_2']; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill">
                            <i class="bi bi-filter me-2"></i>Tampilkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-5">
        <!-- Header Laporan -->
        <div class="text-center mb-5">
            <h4 class="fw-bold mb-0"><?php echo strtoupper($data['perusahaan']['nama_perusahaan']); ?></h4>
            <h5 class="fw-bold mb-0">LAPORAN PERUBAHAN EKUITAS</h5>
            <p class="text-muted small">Untuk periode yang berakhir pada <?php echo date('d F Y', strtotime($data['tanggal_selesai_1'])); ?></p>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Deskripsi</th>
                        <th class="text-end"><?php echo $data['periode_1']; ?></th>
                        <?php if(!empty($data['periode_2'])): ?>
                            <th class="text-end pe-4"><?php echo $data['periode_2']; ?></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $p1 = $data['laporan']['periode_1'];
                        $p2 = $data['laporan']['periode_2'] ?? null;
                    ?>
                    <tr>
                        <td class="ps-4">Saldo Modal Awal</td>
                        <td class="text-end fw-bold"><?php echo number_format($p1['modal_awal'], 2); ?></td>
                        <?php if($p2): ?>
                            <td class="text-end pe-4 fw-bold"><?php echo number_format($p2['modal_awal'], 2); ?></td>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <td class="ps-4">Laba (Rugi) Bersih Periode Berjalan</td>
                        <td class="text-end"><?php echo number_format($p1['laba_rugi_periode_berjalan'], 2); ?></td>
                        <?php if($p2): ?>
                            <td class="text-end pe-4"><?php echo number_format($p2['laba_rugi_periode_berjalan'], 2); ?></td>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <td class="ps-4">Perubahan Modal Langsung / Prive</td>
                        <td class="text-end"><?php echo number_format($p1['perubahan_modal_langsung'], 2); ?></td>
                        <?php if($p2): ?>
                            <td class="text-end pe-4"><?php echo number_format($p2['perubahan_modal_langsung'], 2); ?></td>
                        <?php endif; ?>
                    </tr>
                    <tr class="table-light">
                        <td class="ps-4 fw-bold">Saldo Modal Akhir</td>
                        <td class="text-end fw-bold text-primary"><?php echo number_format($p1['modal_akhir'], 2); ?></td>
                        <?php if($p2): ?>
                            <td class="text-end pe-4 fw-bold text-primary"><?php echo number_format($p2['modal_akhir'], 2); ?></td>
                        <?php endif; ?>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer Tanda Tangan -->
        <div class="row mt-5 pt-4 text-center">
            <div class="col-4">
                <p class="mb-5"><?php echo $data['kota_laporan']; ?>, <?php echo date('d F Y'); ?></p>
                <p class="mb-0 fw-bold"><?php echo $data['penandatangan_1']['nama_user']; ?></p>
                <p class="text-muted small"><?php echo $data['penandatangan_1']['jabatan']; ?></p>
            </div>
            <div class="col-4"></div>
            <div class="col-4">
                <p class="mb-5">&nbsp;</p>
                <p class="mb-0 fw-bold"><?php echo $data['penandatangan_2']['nama_user']; ?></p>
                <p class="text-muted small"><?php echo $data['penandatangan_2']['jabatan']; ?></p>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('compareCheck').addEventListener('change', function() {
        document.getElementById('compareInputs').classList.toggle('d-none');
    });
</script>

<style>
    @media print {
        .no-print { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
        .main-wrapper { margin-left: 0 !important; padding: 0 !important; }
        .p-5 { padding: 0 !important; }
    }
</style>
