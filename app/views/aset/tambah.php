<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h4 class="fw-bold mb-0">Tambah Aset Tetap Baru</h4>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo BASEURL; ?>/aset/simpan" method="post">
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Kode Aset</label>
                            <input type="text" name="kode_aset" class="form-control rounded-3" value="<?php echo $data['kode_otomatis']; ?>" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold small">Nama Aset</label>
                            <input type="text" name="nama_aset" class="form-control rounded-3" placeholder="Contoh: Laptop Kantor MacBook Air" required>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Tanggal Perolehan</label>
                            <input type="date" name="tanggal_perolehan" class="form-control rounded-3" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Harga Perolehan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" step="0.01" name="harga_perolehan" class="form-control rounded-3 border-start-0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Nilai Residu</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" step="0.01" name="nilai_residu" class="form-control rounded-3 border-start-0" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Umur Ekonomis (Bulan)</label>
                            <input type="number" name="umur_ekonomis" class="form-control rounded-3" placeholder="Contoh: 48" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Metode Penyusutan</label>
                            <select name="metode_penyusutan" class="form-select rounded-3 shadow-none">
                                <option value="Garis Lurus">Garis Lurus (Straight Line)</option>
                                <option value="Saldo Menurun">Saldo Menurun (Declining Balance)</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4 text-muted opacity-25">
                    <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-link-45deg me-2"></i>Pemetaan Akun Akuntansi</h6>

                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Akun Aset</label>
                            <select name="akun_aset" class="form-select rounded-3 search-select shadow-none">
                                <option value="">-- Pilih Akun Aset --</option>
                                <?php foreach($data['akun'] as $row): if(substr($row['kode_akun'], 0, 1) == '1' && $row['tipe_akun'] == 'Detail'): ?>
                                <option value="<?php echo $row['kode_akun']; ?>">[<?php echo $row['kode_akun']; ?>] <?php echo $row['nama_akun']; ?></option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Akun Akumulasi Penyusutan</label>
                            <select name="akun_akumulasi" class="form-select rounded-3 search-select shadow-none">
                                <option value="">-- Pilih Akun Akumulasi --</option>
                                <?php 
                                $def_akum = $data['perusahaan']['akun_akumulasi_depresiasi_default'] ?? '';
                                foreach($data['akun'] as $row): 
                                    if(substr($row['kode_akun'], 0, 1) == '1' && $row['tipe_akun'] == 'Detail'): 
                                ?>
                                <option value="<?php echo $row['kode_akun']; ?>" <?php echo $row['kode_akun'] == $def_akum ? 'selected' : ''; ?>>
                                    [<?php echo $row['kode_akun']; ?>] <?php echo $row['nama_akun']; ?>
                                </option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Akun Beban Penyusutan</label>
                            <select name="akun_beban" class="form-select rounded-3 search-select shadow-none">
                                <option value="">-- Pilih Akun Beban --</option>
                                <?php 
                                $def_beban = $data['perusahaan']['akun_beban_depresiasi_default'] ?? '';
                                foreach($data['akun'] as $row): 
                                    if(substr($row['kode_akun'], 0, 1) == '6' && $row['tipe_akun'] == 'Detail'): 
                                ?>
                                <option value="<?php echo $row['kode_akun']; ?>" <?php echo $row['kode_akun'] == $def_beban ? 'selected' : ''; ?>>
                                    [<?php echo $row['kode_akun']; ?>] <?php echo $row['nama_akun']; ?>
                                </option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small">Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control rounded-3" rows="3"></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-5">
                        <a href="<?php echo BASEURL; ?>/aset" class="btn btn-light rounded-pill px-4 border">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5">Simpan Data Aset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
