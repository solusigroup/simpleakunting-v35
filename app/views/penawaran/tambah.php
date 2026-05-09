<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex align-items-center mb-4">
            <a href="<?php echo BASEURL; ?>/penawaran" class="btn btn-link text-decoration-none text-muted p-0 me-3">
                <i class="bi bi-arrow-left fs-4"></i>
            </a>
            <h3 class="fw-bold mb-0">Buat Penawaran Baru</h3>
        </div>

        <form action="<?php echo BASEURL; ?>/penawaran/simpan" method="post" id="offerForm">
            <div class="row g-4">
                <!-- Kolom Kiri: Informasi Barang -->
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Item Penawaran</h5>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead class="bg-light small fw-bold text-uppercase text-muted">
                                        <tr>
                                            <th>Item / Produk</th>
                                            <th width="120">Qty</th>
                                            <th width="180">Harga Satuan</th>
                                            <th width="200" class="text-end">Subtotal</th>
                                            <th width="50"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="detail-body"></tbody>
                                </table>
                            </div>
                            <button type="button" id="add-item" class="btn btn-outline-primary btn-sm rounded-pill px-4 mt-2">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Baris
                            </button>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Keterangan / Syarat & Ketentuan</label>
                            <textarea name="keterangan" class="form-control bg-light border-0" rows="4" placeholder="Contoh: Harga belum termasuk ongkos kirim. Penawaran berlaku 14 hari..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Detail & Ringkasan -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Detail Penawaran</h5>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Calon Pelanggan</label>
                                <select name="id_pelanggan" class="form-select bg-light border-0" required>
                                    <option value="">Pilih Pelanggan...</option>
                                    <?php foreach($data['pelanggan'] as $pl): ?>
                                        <option value="<?php echo $pl['id_pelanggan']; ?>"><?php echo htmlspecialchars($pl['nama_pelanggan']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Nomor Penawaran</label>
                                <input type="text" class="form-control bg-light border-0 fw-bold" name="no_penawaran" value="<?php echo $data['no_penawaran']; ?>" required>
                            </div>

                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold small text-uppercase text-muted">Tanggal</label>
                                    <input type="date" name="tanggal" class="form-control bg-light border-0" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold small text-uppercase text-muted">Kadaluarsa</label>
                                    <input type="date" name="tgl_kadaluarsa" class="form-control bg-light border-0" value="<?php echo date('Y-m-d', strtotime('+14 days')); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ringkasan Biaya -->
                    <div class="card border-0 shadow-sm bg-indigo text-white">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="opacity-75">Subtotal</span>
                                <span class="fw-bold" id="label-subtotal">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 align-items-center">
                                <span class="opacity-75">Diskon</span>
                                <div class="input-group input-group-sm" style="width: 120px;">
                                    <span class="input-group-text bg-transparent border-white text-white border-opacity-25">Rp</span>
                                    <input type="number" name="total_diskon" id="total_diskon" class="form-control bg-transparent border-white text-white border-opacity-25 text-end" value="0">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mb-3 align-items-center">
                                <div class="d-flex flex-column">
                                    <span class="opacity-75">Estimasi Pajak (<?php echo (float)($data['perusahaan']['persentase_pajak_default'] ?? 11); ?>%)</span>
                                    <div class="form-check form-switch mt-1">
                                        <input class="form-check-input" type="checkbox" id="use_tax" checked>
                                    </div>
                                </div>
                                <span class="fw-bold" id="label-pajak">Rp 0</span>
                                <input type="hidden" name="total_pajak" id="input_total_pajak" value="0">
                            </div>
                            <hr class="border-white border-opacity-25">
                            <div class="d-flex justify-content-between align-items-end">
                                <span class="fw-bold fs-5">TOTAL ESTIMASI</span>
                                <h3 class="fw-bold mb-0" id="label-total">Rp 0</h3>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dark w-100 rounded-pill py-3 mt-4 fw-bold shadow">
                        <i class="bi bi-send me-2"></i>Simpan Penawaran
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<template id="detail-row-template">
    <tr>
        <td>
            <select name="details[id_barang][]" class="form-select border-0 bg-light item-select" required>
                <option value="">Pilih Item...</option>
                <?php foreach($data['barang'] as $brg): ?>
                    <option value="<?php echo $brg['id_barang']; ?>" data-harga="<?php echo $brg['harga_jual']; ?>">
                        <?php echo $brg['nama_barang']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <input type="number" name="details[kuantitas][]" class="form-control border-0 bg-light qty" value="1" step="0.01" min="0.01" required>
        </td>
        <td>
            <div class="input-group input-group-sm">
                <span class="input-group-text border-0 bg-light">Rp</span>
                <input type="number" name="details[harga][]" class="form-control border-0 bg-light price" value="0" step="0.01" required>
            </div>
        </td>
        <td>
            <input type="text" name="details[subtotal][]" class="form-control-plaintext text-end fw-bold subtotal" readonly value="0">
        </td>
        <td class="text-end">
            <button type="button" class="btn btn-link text-danger p-0 remove-item">
                <i class="bi bi-x-circle fs-5"></i>
            </button>
        </td>
    </tr>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const taxRate = <?php echo (float)($data['perusahaan']['persentase_pajak_default'] ?? 11); ?> / 100;
    const tbody = document.getElementById('detail-body');
    const template = document.getElementById('detail-row-template');
    const labelSubtotal = document.getElementById('label-subtotal');
    const labelPajak = document.getElementById('label-pajak');
    const inputPajak = document.getElementById('input_total_pajak');
    const labelTotal = document.getElementById('label-total');
    const diskonInput = document.getElementById('total_diskon');
    const taxSwitch = document.getElementById('use_tax');

    function formatIDR(val) {
        return 'Rp ' + parseFloat(val).toLocaleString('id-ID', { minimumFractionDigits: 0 });
    }

    function calculateTotal() {
        let subtotal = 0;
        tbody.querySelectorAll('.subtotal').forEach(el => {
            subtotal += parseFloat(el.value) || 0;
        });

        const diskon = parseFloat(diskonInput.value) || 0;
        const subtotalNet = Math.max(0, subtotal - diskon);
        const pajak = taxSwitch.checked ? (subtotalNet * taxRate) : 0;
        const total = subtotalNet + pajak;

        labelSubtotal.textContent = formatIDR(subtotal);
        labelPajak.textContent = formatIDR(pajak);
        inputPajak.value = pajak;
        labelTotal.textContent = formatIDR(total);
    }

    function calculateRow(row) {
        const qty = parseFloat(row.querySelector('.qty').value) || 0;
        const price = parseFloat(row.querySelector('.price').value) || 0;
        const subtotal = qty * price;
        row.querySelector('.subtotal').value = subtotal.toFixed(2);
        calculateTotal();
    }

    function addRow() {
        const clone = template.content.cloneNode(true);
        tbody.appendChild(clone);
    }

    document.getElementById('add-item').addEventListener('click', addRow);
    
    tbody.addEventListener('change', e => {
        if (e.target.matches('.item-select')) {
            const opt = e.target.options[e.target.selectedIndex];
            const row = e.target.closest('tr');
            row.querySelector('.price').value = opt.dataset.harga || 0;
            calculateRow(row);
        }
    });

    tbody.addEventListener('input', e => {
        if (e.target.matches('.qty, .price')) {
            calculateRow(e.target.closest('tr'));
        }
    });

    tbody.addEventListener('click', e => {
        if (e.target.closest('.remove-item')) {
            e.target.closest('tr').remove();
            calculateTotal();
        }
    });

    diskonInput.addEventListener('input', calculateTotal);
    taxSwitch.addEventListener('change', calculateTotal);

    addRow();
});
</script>

<style>
    .bg-indigo { background-color: #6366f1 !important; }
    .bg-light { background-color: #f8f9fa !important; }
    .form-select, .form-control { border-radius: 0.75rem; }
    .card { border-radius: 1rem; }
</style>
