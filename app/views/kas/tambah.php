<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h4 class="mb-0 fw-bold">Form Transaksi Kas & Bank</h4>
        <a href="<?php echo BASEURL; ?>/kas" class="btn-close" aria-label="Close"></a>
    </div>
    <div class="card-body p-4">
        <form action="<?php echo BASEURL; ?>/kas/simpan" method="post">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Tipe Transaksi</label>
                    <select class="form-select bg-light" disabled>
                        <option value="Masuk" <?php echo ($data['tipe'] == 'Masuk') ? 'selected' : ''; ?>>Kas Masuk</option>
                        <option value="Keluar" <?php echo ($data['tipe'] == 'Keluar') ? 'selected' : ''; ?>>Kas Keluar</option>
                    </select>
                    <!-- Hidden input to ensure value is sent to server -->
                    <input type="hidden" name="tipe_transaksi" value="<?php echo $data['tipe']; ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="no_bukti" class="form-label fw-semibold">No. Bukti / Voucher</label>
                    <input type="text" class="form-control" id="no_bukti" name="no_bukti" value="<?php echo $data['no_bukti']; ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Unit Usaha</label>
                    <select name="id_unit" class="form-select">
                        <option value="">-- Unit Utama / Konsolidasi --</option>
                        <?php if (!empty($data['units'])): foreach ($data['units'] as $u): ?>
                            <option value="<?php echo $u['id_unit']; ?>" <?php echo (!empty($u['is_default']) ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($u['nama_unit']); ?>
                            </option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Akun Kas / Bank</label>
                    <select name="akun_kas_bank" class="form-select searchable-select" required>
                        <?php foreach($data['akun_kas_list'] as $akun): ?>
                            <option value="<?php echo $akun['kode_akun']; ?>"><?php echo $akun['nama_akun']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Akun Lawan (Pendapatan/Beban/Modal)</label>
                    <select name="akun_lawan" class="form-select searchable-select" required>
                        <option value="">Pilih Akun...</option>
                        <?php foreach($data['akun_lawan_list'] as $grup => $akuns): ?>
                            <optgroup label="<?php echo htmlspecialchars($grup); ?>">
                                <?php foreach($akuns as $akun): ?>
                                    <option value="<?php echo $akun['kode_akun']; ?>"><?php echo $akun['nama_akun']; ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Jumlah (Rp)</label>
                    <input type="number" step="0.01" name="jumlah" class="form-control" required placeholder="0.00">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Program / Sumber Dana <small class="text-muted">(Opsional)</small></label>
                    <select name="id_program" class="form-select">
                        <option value="">-- Reguler / Non-Program --</option>
                        <?php if (!empty($data['programs'])): foreach ($data['programs'] as $p): ?>
                            <option value="<?php echo $p['id_program']; ?>">
                                <?php echo htmlspecialchars($p['nama_program']); ?> (<?php echo htmlspecialchars($p['tipe']); ?>)
                            </option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Deskripsi / Keterangan</label>
                    <input type="text" name="deskripsi" class="form-control" required placeholder="Contoh: Pembayaran listrik, penerimaan pendapatan jasa...">
                </div>
            </div>
            <hr class="my-4">
            <div class="d-flex justify-content-end gap-2">
                <a href="<?php echo BASEURL; ?>/kas" class="btn btn-light px-4">Batal</a>
                <button type="submit" class="btn btn-primary px-4">Simpan Transaksi</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.searchable-select').forEach(el => {
        new TomSelect(el, {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            dropdownParent: 'body'
        });
    });
});
</script>
