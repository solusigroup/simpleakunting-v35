<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center mb-4">
            <a href="<?php echo BASEURL; ?>/produksi" class="btn btn-link text-decoration-none text-muted p-0 me-3">
                <i class="bi bi-arrow-left fs-4"></i>
            </a>
            <h3 class="fw-bold mb-0">Buat Perintah Produksi</h3>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <form action="<?php echo BASEURL; ?>/produksi/simpan" method="post">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="no_produksi" class="form-label fw-bold small text-uppercase text-muted">Nomor Produksi</label>
                            <input type="text" class="form-control bg-light" id="no_produksi" name="no_produksi" value="<?php echo $data['no_produksi']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal" class="form-label fw-bold small text-uppercase text-muted">Tanggal Mulai</label>
                            <input type="date" class="form-control bg-light" id="tanggal" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-12">
                            <label for="id_bom" class="form-label fw-bold small text-uppercase text-muted">Pilih Resep (BOM)</label>
                            <select id="id_bom" name="id_bom" class="form-select bg-light" required>
                                <option value="">Pilih BOM...</option>
                                <?php foreach($data['bom_list'] as $b): ?>
                                    <option value="<?php echo $b['id']; ?>"><?php echo $b['nama_bom']; ?> - Produk: <?php echo $b['nama_produk']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text small">Hanya BOM yang sudah aktif yang dapat dipilih.</div>
                        </div>
                        <div class="col-md-12">
                            <label for="jumlah_target" class="form-label fw-bold small text-uppercase text-muted">Target Jumlah Produksi</label>
                            <div class="input-group">
                                <input type="number" class="form-control bg-light fw-bold fs-4" id="jumlah_target" name="jumlah_target" value="1" step="0.01" min="0.01" required>
                                <span class="input-group-text bg-light text-muted fw-bold">Unit / Pcs</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-primary-soft rounded-4 p-4 mt-5 mb-4 border border-primary border-opacity-10">
                        <h6 class="fw-bold mb-2 text-primary">Informasi Penting:</h6>
                        <ul class="mb-0 small text-muted">
                            <li>Membuat perintah produksi akan berstatus <b>Draft</b> terlebih dahulu.</li>
                            <li>Bahan baku <b>hanya</b> akan dikurangi saat status diubah menjadi <b>Selesai</b>.</li>
                            <li>Jurnal akuntansi akan dibuat otomatis saat produksi selesai.</li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?php echo BASEURL; ?>/produksi" class="btn btn-light rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow">
                            <i class="bi bi-check-circle me-2"></i>Buat Perintah
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-soft { background-color: rgba(79, 70, 229, 0.05); }
    .bg-light { background-color: #f8f9fa !important; }
</style>
