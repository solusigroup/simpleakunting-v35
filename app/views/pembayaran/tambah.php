<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex align-items-center mb-4">
            <a href="<?php echo BASEURL; ?>/pembayaran" class="btn btn-link text-decoration-none text-muted p-0 me-3">
                <i class="bi bi-arrow-left fs-4"></i>
            </a>
            <h3 class="fw-bold mb-0">Catat Pembayaran Pemasok</h3>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <form action="<?php echo BASEURL; ?>/pembayaran/simpan" method="post">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="id_pemasok" class="form-label fw-semibold text-muted small uppercase">Pilih Pemasok</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-truck"></i></span>
                                <select id="id_pemasok" name="id_pemasok" class="form-select bg-light border-start-0" required>
                                    <option value="">Pilih Pemasok...</option>
                                    <?php foreach($data['pemasok'] as $pemasok): ?>
                                        <option value="<?php echo $pemasok['id_pemasok']; ?>"><?php echo $pemasok['nama_pemasok']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="akun_kas_bank" class="form-label fw-semibold text-muted small uppercase">Bayar Dari Akun</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-bank"></i></span>
                                <select id="akun_kas_bank" name="akun_kas_bank" class="form-select bg-light border-start-0" required>
                                    <option value="">Pilih Akun Kas/Bank...</option>
                                    <?php foreach($data['akun_kas_list'] as $akun): ?>
                                        <option value="<?php echo $akun['kode_akun']; ?>"><?php echo $akun['nama_akun']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="no_bukti" class="form-label fw-semibold text-muted small uppercase">No. Bukti / Referensi</label>
                            <input type="text" class="form-control bg-light" id="no_bukti" name="no_bukti" placeholder="Contoh: BKK-001" required>
                        </div>
                        <div class="col-md-4">
                            <label for="tanggal" class="form-label fw-semibold text-muted small uppercase">Tanggal Pembayaran</label>
                            <input type="date" class="form-control bg-light" id="tanggal" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="total_tampilan" class="form-label fw-semibold text-muted small uppercase">Total Dibayarkan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white border-0">Rp</span>
                                <input type="text" class="form-control bg-white fw-bold text-end fs-5" id="total_tampilan" readonly value="0">
                                <input type="hidden" id="total_pembayaran" name="total_pembayaran" value="0">
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="keterangan" class="form-label fw-semibold text-muted small uppercase">Keterangan Tambahan</label>
                            <textarea class="form-control bg-light" id="keterangan" name="keterangan" rows="2" placeholder="Tuliskan catatan pembayaran di sini..."></textarea>
                        </div>
                    </div>

                    <div class="mt-5 mb-3">
                        <h5 class="fw-bold d-flex align-items-center">
                            <i class="bi bi-list-check me-2 text-primary"></i> 
                            Daftar Tagihan Belum Lunas
                        </h5>
                        <p class="text-muted small">Pilih jumlah pembayaran untuk setiap faktur di bawah ini.</p>
                    </div>

                    <div class="table-responsive rounded-3 border">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">No. Faktur</th>
                                    <th>Tanggal</th>
                                    <th class="text-end">Sisa Tagihan</th>
                                    <th class="text-end" style="width: 200px;">Jumlah Bayar</th>
                                </tr>
                            </thead>
                            <tbody id="faktur-list">
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-info-circle d-block mb-2 fs-4"></i>
                                        Pilih pemasok terlebih dahulu untuk menampilkan daftar tagihan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-5 gap-2">
                        <a href="<?php echo BASEURL; ?>/pembayaran" class="btn btn-light rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow">
                            <i class="bi bi-check-circle me-2"></i>Simpan Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('id_pemasok').addEventListener('change', function() {
        const idPemasok = this.value;
        const tbody = document.getElementById('faktur-list');
        tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Memuat data...</td></tr>';

        if (!idPemasok) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center py-5 text-muted"><i class="bi bi-info-circle d-block mb-2 fs-4"></i> Pilih pemasok terlebih dahulu...</td></tr>';
            return;
        }

        fetch('<?php echo BASEURL; ?>/pembayaran/getFaktur/' + idPemasok)
            .then(response => response.json())
            .then(fakturList => {
                tbody.innerHTML = '';
                if (fakturList.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center py-5 text-danger"><i class="bi bi-exclamation-triangle d-block mb-2 fs-4"></i> Tidak ada tagihan yang belum lunas untuk pemasok ini.</td></tr>';
                    return;
                }
                
                fakturList.forEach(faktur => {
                    let row = `
                        <tr>
                            <td class="ps-3 fw-medium">${faktur.no_faktur_pembelian}</td>
                            <td class="text-muted small">${faktur.tanggal_faktur}</td>
                            <td class="text-end fw-bold">Rp ${new Intl.NumberFormat('id-ID').format(faktur.sisa_tagihan)}</td>
                            <td class="pe-3">
                                <div class="input-group input-group-sm">
                                    <input type="hidden" name="details[id_pembelian][]" value="${faktur.id_pembelian}">
                                    <input type="number" class="form-control text-end bayar-input fw-bold" name="details[jumlah_bayar][]" value="0" step="0.01" max="${faktur.sisa_tagihan}" onfocus="this.select()">
                                    <button type="button" class="btn btn-outline-secondary" onclick="setFullAmount(this, ${faktur.sisa_tagihan})">Full</button>
                                </div>
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
                
                document.querySelectorAll('.bayar-input').forEach(input => {
                    input.addEventListener('input', hitungTotal);
                });
                hitungTotal();
            });
    });

    function setFullAmount(btn, amount) {
        const input = btn.previousElementSibling;
        input.value = amount;
        hitungTotal();
    }

    function hitungTotal() {
        let total = 0;
        document.querySelectorAll('.bayar-input').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('total_pembayaran').value = total;
        document.getElementById('total_tampilan').value = new Intl.NumberFormat('id-ID').format(total);
    }
</script>

<style>
    .form-label.small { font-size: 0.7rem; letter-spacing: 0.05rem; }
    .bg-light { background-color: #f8f9fa !important; }
    .btn-link:hover { color: var(--primary-color) !important; }
    .table-responsive::-webkit-scrollbar { height: 6px; }
    .table-responsive::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
</style>