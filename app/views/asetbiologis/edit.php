<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0">Edit Aset Biologis</h4>
                <a href="<?php echo BASEURL; ?>/asetbiologis" class="btn-close" aria-label="Close"></a>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo BASEURL; ?>/asetbiologis/update" method="post" id="formAset">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($data['aset']['id']); ?>">
                    
                    <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-info-circle me-2"></i>Informasi Umum</h6>
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Kode Aset</label>
                            <input type="text" name="kode_aset" class="form-control rounded-3" value="<?php echo htmlspecialchars($data['aset']['kode_aset']); ?>" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold small">Nama Aset</label>
                            <input type="text" name="nama_aset" class="form-control rounded-3" value="<?php echo htmlspecialchars($data['aset']['nama_aset']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Kategori</label>
                            <select name="kategori" id="kategori" class="form-select rounded-3 shadow-none" required>
                                <?php $cats = ['Hewan Konsumsi', 'Hewan Produktif', 'Tanaman Konsumsi', 'Tanaman Produktif', 'Hasil pada Tanaman'];
                                foreach($cats as $cat): ?>
                                <option value="<?php echo $cat; ?>" <?php echo $data['aset']['kategori'] == $cat ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Jenis/Spesies</label>
                            <input type="text" name="jenis" class="form-control rounded-3" value="<?php echo htmlspecialchars($data['aset']['jenis']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Status Kematangan</label>
                            <select name="status_kematangan" class="form-select rounded-3 shadow-none" required>
                                <option value="Belum Menghasilkan" <?php echo $data['aset']['status_kematangan'] == 'Belum Menghasilkan' ? 'selected' : ''; ?>>Belum Menghasilkan</option>
                                <option value="Menghasilkan" <?php echo $data['aset']['status_kematangan'] == 'Menghasilkan' ? 'selected' : ''; ?>>Menghasilkan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Tanggal Perolehan</label>
                            <input type="date" name="tanggal_perolehan" class="form-control rounded-3" value="<?php echo htmlspecialchars($data['aset']['tanggal_perolehan']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Lokasi</label>
                            <input type="text" name="lokasi" class="form-control rounded-3" value="<?php echo htmlspecialchars($data['aset']['lokasi']); ?>">
                        </div>
                    </div>

                    <hr class="my-4 text-muted opacity-25">
                    <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-calculator me-2"></i>Kuantitas & Pengukuran Awal</h6>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Kuantitas</label>
                            <input type="number" step="0.01" name="kuantitas" class="form-control rounded-3" value="<?php echo htmlspecialchars($data['aset']['kuantitas']); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Satuan</label>
                            <select name="satuan" class="form-select rounded-3 shadow-none" required>
                                <?php $satuans = ['Ekor', 'Pohon', 'Ha', 'Kg', 'Ton', 'Liter'];
                                foreach($satuans as $s): ?>
                                <option value="<?php echo $s; ?>" <?php echo $data['aset']['satuan'] == $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Metode Pengukuran</label>
                            <select name="metode_pengukuran" id="metode_pengukuran" class="form-select rounded-3 shadow-none" required>
                                <option value="Nilai Wajar" <?php echo $data['aset']['metode_pengukuran'] == 'Nilai Wajar' ? 'selected' : ''; ?>>Nilai Wajar dikurangi Biaya untuk Menjual</option>
                                <option value="Biaya Perolehan" <?php echo $data['aset']['metode_pengukuran'] == 'Biaya Perolehan' ? 'selected' : ''; ?>>Biaya Perolehan</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-4 mb-4" id="divNilaiWajar">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Nilai Wajar</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" step="0.01" id="nilai_wajar" name="nilai_wajar" class="form-control rounded-3 border-start-0" value="<?php echo htmlspecialchars($data['aset']['nilai_wajar']); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Biaya untuk Menjual</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" step="0.01" id="biaya_jual" name="biaya_jual" class="form-control rounded-3 border-start-0" value="<?php echo htmlspecialchars($data['aset']['biaya_jual']); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Nilai Tercatat</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" step="0.01" id="nilai_tercatat_fv" class="form-control rounded-3 border-start-0 bg-light" value="<?php echo htmlspecialchars($data['aset']['nilai_tercatat']); ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4" id="divBiayaPerolehan" style="display:none;">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Biaya Perolehan Historis</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" step="0.01" name="biaya_perolehan" class="form-control rounded-3 border-start-0" value="<?php echo htmlspecialchars($data['aset']['biaya_perolehan']); ?>">
                            </div>
                        </div>
                        <div class="col-md-6" id="divUmur" style="display:none;">
                            <label class="form-label fw-bold small">Umur Ekonomis (Bulan)</label>
                            <input type="number" name="umur_ekonomis" class="form-control rounded-3" value="<?php echo htmlspecialchars($data['aset']['umur_ekonomis']); ?>">
                        </div>
                    </div>

                    <hr class="my-4 text-muted opacity-25">
                    <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-link-45deg me-2"></i>Pemetaan Akun Akuntansi</h6>

                    <div class="row g-4 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Akun Aset Biologis</label>
                            <select name="akun_aset" class="form-select rounded-3 search-select shadow-none" required>
                                <option value="">-- Pilih Akun Aset --</option>
                                <?php foreach($data['akun'] as $row): if(substr($row['kode_akun'], 0, 1) == '1' && $row['tipe_akun'] == 'Detail'): ?>
                                <option value="<?php echo $row['kode_akun']; ?>" <?php echo $data['aset']['akun_aset'] == $row['kode_akun'] ? 'selected' : ''; ?>>[<?php echo $row['kode_akun']; ?>] <?php echo htmlspecialchars($row['nama_akun']); ?></option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 fv-accounts">
                            <label class="form-label fw-bold small">Akun Keuntungan Nilai Wajar</label>
                            <select name="akun_keuntungan_fv" class="form-select rounded-3 search-select shadow-none">
                                <option value="">-- Pilih Akun --</option>
                                <?php foreach($data['akun'] as $row): if((substr($row['kode_akun'], 0, 1) == '4' || substr($row['kode_akun'], 0, 1) == '7') && $row['tipe_akun'] == 'Detail'): ?>
                                <option value="<?php echo $row['kode_akun']; ?>" <?php echo $data['aset']['akun_keuntungan_fv'] == $row['kode_akun'] ? 'selected' : ''; ?>>[<?php echo $row['kode_akun']; ?>] <?php echo htmlspecialchars($row['nama_akun']); ?></option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 fv-accounts">
                            <label class="form-label fw-bold small">Akun Kerugian Nilai Wajar</label>
                            <select name="akun_kerugian_fv" class="form-select rounded-3 search-select shadow-none">
                                <option value="">-- Pilih Akun --</option>
                                <?php foreach($data['akun'] as $row): if((substr($row['kode_akun'], 0, 1) == '6' || substr($row['kode_akun'], 0, 1) == '8') && $row['tipe_akun'] == 'Detail'): ?>
                                <option value="<?php echo $row['kode_akun']; ?>" <?php echo $data['aset']['akun_kerugian_fv'] == $row['kode_akun'] ? 'selected' : ''; ?>>[<?php echo $row['kode_akun']; ?>] <?php echo htmlspecialchars($row['nama_akun']); ?></option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Akun Beban Pemeliharaan</label>
                            <select name="akun_beban_pemeliharaan" class="form-select rounded-3 search-select shadow-none">
                                <option value="">-- Pilih Akun --</option>
                                <?php foreach($data['akun'] as $row): if(substr($row['kode_akun'], 0, 1) == '6' && $row['tipe_akun'] == 'Detail'): ?>
                                <option value="<?php echo $row['kode_akun']; ?>" <?php echo $data['aset']['akun_beban_pemeliharaan'] == $row['kode_akun'] ? 'selected' : ''; ?>>[<?php echo $row['kode_akun']; ?>] <?php echo htmlspecialchars($row['nama_akun']); ?></option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Akun Hasil Panen (Persediaan)</label>
                            <select name="akun_hasil_panen" class="form-select rounded-3 search-select shadow-none">
                                <option value="">-- Pilih Akun --</option>
                                <?php foreach($data['akun'] as $row): if(substr($row['kode_akun'], 0, 1) == '1' && $row['tipe_akun'] == 'Detail'): ?>
                                <option value="<?php echo $row['kode_akun']; ?>" <?php echo $data['aset']['akun_hasil_panen'] == $row['kode_akun'] ? 'selected' : ''; ?>>[<?php echo $row['kode_akun']; ?>] <?php echo htmlspecialchars($row['nama_akun']); ?></option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Akun Keuntungan Panen</label>
                            <select name="akun_keuntungan_panen" class="form-select rounded-3 search-select shadow-none">
                                <option value="">-- Pilih Akun --</option>
                                <?php foreach($data['akun'] as $row): if(in_array(substr($row['kode_akun'], 0, 1), ['4','7']) && $row['tipe_akun'] == 'Detail'): ?>
                                <option value="<?php echo $row['kode_akun']; ?>" <?php echo $data['aset']['akun_keuntungan_panen'] == $row['kode_akun'] ? 'selected' : ''; ?>>[<?php echo $row['kode_akun']; ?>] <?php echo htmlspecialchars($row['nama_akun']); ?></option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small">Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control rounded-3" rows="3"><?php echo htmlspecialchars($data['aset']['keterangan']); ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-5">
                        <a href="<?php echo BASEURL; ?>/asetbiologis" class="btn btn-light rounded-pill px-4 border">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5">Update Data Aset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof TomSelect !== 'undefined') {
        document.querySelectorAll('.search-select').forEach(function(el) {
            new TomSelect(el, {
                create: false,
                sortField: { field: "text", direction: "asc" }
            });
        });
    }

    const metodePengukuran = document.getElementById('metode_pengukuran');
    const kategori = document.getElementById('kategori');
    const divNilaiWajar = document.getElementById('divNilaiWajar');
    const divBiayaPerolehan = document.getElementById('divBiayaPerolehan');
    const divUmur = document.getElementById('divUmur');
    const fvAccounts = document.querySelectorAll('.fv-accounts');
    const nwInput = document.getElementById('nilai_wajar');
    const bjInput = document.getElementById('biaya_jual');
    const ntFvInput = document.getElementById('nilai_tercatat_fv');

    function updateForm() {
        const metode = metodePengukuran.value;
        const kat = kategori.value;

        if (metode === 'Nilai Wajar') {
            divNilaiWajar.style.display = 'flex';
            divBiayaPerolehan.style.display = 'none';
            fvAccounts.forEach(el => el.style.display = 'block');
        } else {
            divNilaiWajar.style.display = 'none';
            divBiayaPerolehan.style.display = 'flex';
            fvAccounts.forEach(el => el.style.display = 'none');
            
            if (kat === 'Tanaman Produktif' || kat === 'Hewan Produktif') {
                divUmur.style.display = 'block';
            } else {
                divUmur.style.display = 'none';
            }
        }
    }

    function calculateTercatat() {
        const nw = parseFloat(nwInput.value) || 0;
        const bj = parseFloat(bjInput.value) || 0;
        ntFvInput.value = nw - bj;
    }

    metodePengukuran.addEventListener('change', updateForm);
    kategori.addEventListener('change', updateForm);
    nwInput.addEventListener('input', calculateTercatat);
    bjInput.addEventListener('input', calculateTercatat);

    updateForm();
});
</script>
