<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0">Tambah Aset Biologis (PSAK 241)</h4>
                <a href="<?php echo BASEURL; ?>/asetbiologis" class="btn-close" aria-label="Close"></a>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo BASEURL; ?>/asetbiologis/simpan" method="post" id="formAset">
                    <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-info-circle me-2"></i>Informasi Umum</h6>
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Kode Aset</label>
                            <input type="text" name="kode_aset" class="form-control rounded-3" value="<?php echo htmlspecialchars($data['kode_otomatis']); ?>" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold small">Nama Aset</label>
                            <input type="text" name="nama_aset" class="form-control rounded-3" placeholder="Contoh: Sapi Perah Holstein" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Kategori</label>
                            <select name="kategori" id="kategori" class="form-select rounded-3 shadow-none" required>
                                <option value="Hewan Konsumsi">Hewan Konsumsi</option>
                                <option value="Hewan Produktif">Hewan Produktif</option>
                                <option value="Tanaman Konsumsi">Tanaman Konsumsi</option>
                                <option value="Tanaman Produktif">Tanaman Produktif</option>
                                <option value="Hasil pada Tanaman">Hasil pada Tanaman</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Jenis/Spesies</label>
                            <input type="text" name="jenis" class="form-control rounded-3">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Status Kematangan</label>
                            <select name="status_kematangan" class="form-select rounded-3 shadow-none" required>
                                <option value="Belum Menghasilkan">Belum Menghasilkan (Belum Matang)</option>
                                <option value="Menghasilkan">Menghasilkan (Matang)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Tanggal Perolehan</label>
                            <input type="date" name="tanggal_perolehan" class="form-control rounded-3" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Lokasi</label>
                            <input type="text" name="lokasi" class="form-control rounded-3" placeholder="Kandang / Lahan">
                        </div>
                    </div>

                    <hr class="my-4 text-muted opacity-25">
                    <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-calculator me-2"></i>Kuantitas & Pengukuran Awal</h6>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Kuantitas</label>
                            <input type="number" step="0.01" name="kuantitas" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Satuan</label>
                            <select name="satuan" class="form-select rounded-3 shadow-none" required>
                                <option value="Ekor">Ekor</option>
                                <option value="Pohon">Pohon</option>
                                <option value="Ha">Ha</option>
                                <option value="Kg">Kg</option>
                                <option value="Ton">Ton</option>
                                <option value="Liter">Liter</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Metode Pengukuran</label>
                            <select name="metode_pengukuran" id="metode_pengukuran" class="form-select rounded-3 shadow-none" required>
                                <option value="Nilai Wajar">Nilai Wajar dikurangi Biaya untuk Menjual (Utama)</option>
                                <option value="Biaya Perolehan">Biaya Perolehan (Hanya jika Nilai Wajar tak terukur)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-4 mb-4" id="divNilaiWajar">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Nilai Wajar</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" step="0.01" id="nilai_wajar" name="nilai_wajar" class="form-control rounded-3 border-start-0" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Biaya untuk Menjual</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" step="0.01" id="biaya_jual" name="biaya_jual" class="form-control rounded-3 border-start-0" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Nilai Tercatat Awal</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" step="0.01" id="nilai_tercatat_fv" class="form-control rounded-3 border-start-0 bg-light" value="0" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4" id="divBiayaPerolehan" style="display:none;">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Biaya Perolehan Historis</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" step="0.01" name="biaya_perolehan" class="form-control rounded-3 border-start-0" value="0">
                            </div>
                        </div>
                        <div class="col-md-6" id="divUmur" style="display:none;">
                            <label class="form-label fw-bold small">Umur Ekonomis (Bulan)</label>
                            <input type="number" name="umur_ekonomis" class="form-control rounded-3" placeholder="Untuk penyusutan metode biaya">
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
                                <option value="<?php echo $row['kode_akun']; ?>">[<?php echo $row['kode_akun']; ?>] <?php echo htmlspecialchars($row['nama_akun']); ?></option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 fv-accounts">
                            <label class="form-label fw-bold small">Akun Keuntungan Nilai Wajar</label>
                            <select name="akun_keuntungan_fv" class="form-select rounded-3 search-select shadow-none">
                                <option value="">-- Pilih Akun --</option>
                                <?php foreach($data['akun'] as $row): if((substr($row['kode_akun'], 0, 1) == '4' || substr($row['kode_akun'], 0, 1) == '7') && $row['tipe_akun'] == 'Detail'): ?>
                                <option value="<?php echo $row['kode_akun']; ?>">[<?php echo $row['kode_akun']; ?>] <?php echo htmlspecialchars($row['nama_akun']); ?></option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 fv-accounts">
                            <label class="form-label fw-bold small">Akun Kerugian Nilai Wajar</label>
                            <select name="akun_kerugian_fv" class="form-select rounded-3 search-select shadow-none">
                                <option value="">-- Pilih Akun --</option>
                                <?php foreach($data['akun'] as $row): if((substr($row['kode_akun'], 0, 1) == '6' || substr($row['kode_akun'], 0, 1) == '8') && $row['tipe_akun'] == 'Detail'): ?>
                                <option value="<?php echo $row['kode_akun']; ?>">[<?php echo $row['kode_akun']; ?>] <?php echo htmlspecialchars($row['nama_akun']); ?></option>
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
                                <option value="<?php echo $row['kode_akun']; ?>">[<?php echo $row['kode_akun']; ?>] <?php echo htmlspecialchars($row['nama_akun']); ?></option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Akun Hasil Panen (Persediaan)</label>
                            <select name="akun_hasil_panen" class="form-select rounded-3 search-select shadow-none">
                                <option value="">-- Pilih Akun --</option>
                                <?php foreach($data['akun'] as $row): if(substr($row['kode_akun'], 0, 1) == '1' && $row['tipe_akun'] == 'Detail'): ?>
                                <option value="<?php echo $row['kode_akun']; ?>">[<?php echo $row['kode_akun']; ?>] <?php echo htmlspecialchars($row['nama_akun']); ?></option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Akun Keuntungan Panen</label>
                            <select name="akun_keuntungan_panen" class="form-select rounded-3 search-select shadow-none">
                                <option value="">-- Pilih Akun --</option>
                                <?php foreach($data['akun'] as $row): if(in_array(substr($row['kode_akun'], 0, 1), ['4','7']) && $row['tipe_akun'] == 'Detail'): ?>
                                <option value="<?php echo $row['kode_akun']; ?>">[<?php echo $row['kode_akun']; ?>] <?php echo htmlspecialchars($row['nama_akun']); ?></option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-success"><i class="bi bi-wallet2 me-1"></i>Akun Sumber Perolehan (Kredit Jurnal Awal)</label>
                            <select name="akun_sumber" id="akun_sumber" class="form-select rounded-3 search-select shadow-none">
                                <option value="">-- Pilih Kas / Bank / Utang --</option>
                                <?php foreach($data['akun'] as $row): if(in_array(substr($row['kode_akun'], 0, 1), ['1', '2', '3']) && $row['tipe_akun'] == 'Detail'): ?>
                                <option value="<?php echo $row['kode_akun']; ?>" <?php echo (str_contains(strtolower($row['nama_akun']), 'kas') || $row['kode_akun'] == '1-10001') ? 'selected' : ''; ?>>
                                    [<?php echo $row['kode_akun']; ?>] <?php echo htmlspecialchars($row['nama_akun']); ?>
                                </option>
                                <?php endif; endforeach; ?>
                            </select>
                            <small class="text-muted">Akun kas/bank atau utang yang dikreditkan saat jurnal otomatis pengakuan awal dibuat.</small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small">Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control rounded-3" rows="3" placeholder="Informasi tambahan mengenai aset biologis ini..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-5">
                        <a href="<?php echo BASEURL; ?>/asetbiologis" class="btn btn-light rounded-pill px-4 border">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5">Simpan Data Aset</button>
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
        ntFvInput.value = Math.max(0, nw - bj);
    }

    metodePengukuran.addEventListener('change', updateForm);
    kategori.addEventListener('change', updateForm);
    nwInput.addEventListener('input', calculateTercatat);
    bjInput.addEventListener('input', calculateTercatat);

    updateForm();
});
</script>
