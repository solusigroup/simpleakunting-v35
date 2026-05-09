<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex align-items-center mb-4">
            <a href="<?php echo BASEURL; ?>/bom" class="btn btn-link text-decoration-none text-muted p-0 me-3">
                <i class="bi bi-arrow-left fs-4"></i>
            </a>
            <h3 class="fw-bold mb-0">Buat Resep Produk (BOM)</h3>
        </div>

        <form action="<?php echo BASEURL; ?>/bom/simpan" method="post" id="bomForm">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="id_barang_jadi" class="form-label fw-bold small text-uppercase text-muted">Pilih Produk Jadi</label>
                            <select id="id_barang_jadi" name="id_barang_jadi" class="form-select bg-light" required>
                                <option value="">Pilih Produk...</option>
                                <?php foreach($data['produk'] as $p): ?>
                                    <option value="<?php echo $p['id_barang']; ?>"><?php echo $p['nama_barang']; ?> (<?php echo $p['satuan']; ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="nama_bom" class="form-label fw-bold small text-uppercase text-muted">Nama BOM / Versi</label>
                            <input type="text" class="form-control bg-light" id="nama_bom" name="nama_bom" placeholder="Contoh: Standar v1" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-layers me-2 text-primary"></i>Komposisi Bahan Baku</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addRow()">
                        <i class="bi bi-plus-circle me-1"></i>Tambah Bahan
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="bomTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Bahan Baku</th>
                                    <th class="text-end" style="width: 150px;">Jumlah</th>
                                    <th style="width: 120px;">Satuan</th>
                                    <th class="text-end" style="width: 200px;">Estimasi Biaya Satuan</th>
                                    <th class="pe-4 text-center" style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Rows will be added here -->
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 bg-light border-top d-flex justify-content-between align-items-center">
                        <div class="text-muted small">Total Biaya Produksi per Unit</div>
                        <div class="h4 fw-bold text-primary mb-0">Rp <span id="total_biaya_label">0,00</span></div>
                        <input type="hidden" name="total_biaya" id="total_biaya_input" value="0">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="<?php echo BASEURL; ?>/bom" class="btn btn-light rounded-pill px-4">Batal</a>
                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow">
                    <i class="bi bi-check-circle me-2"></i>Simpan BOM
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let rowCount = 0;
    const products = <?php echo json_encode($data['produk']); ?>;

    function addRow() {
        rowCount++;
        const tbody = document.querySelector('#bomTable tbody');
        const row = `
            <tr id="row-${rowCount}">
                <td class="ps-4">
                    <select name="items[${rowCount}][id_barang]" class="form-select select-bahan bg-light border-0" onchange="updateRowInfo(this, ${rowCount})" required>
                        <option value="">Pilih Bahan...</option>
                        <?php foreach($data['produk'] as $p): ?>
                            <option value="<?php echo $p['id_barang']; ?>" data-satuan="<?php echo $p['satuan']; ?>" data-harga="<?php echo $p['harga_beli']; ?>">
                                <?php echo $p['nama_barang']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <input type="number" name="items[${rowCount}][jumlah]" class="form-control text-end bg-light border-0 qty-input" value="1" step="0.01" oninput="calculateTotal()" required>
                </td>
                <td>
                    <input type="text" name="items[${rowCount}][satuan]" class="form-control text-muted bg-transparent border-0 satuan-label" readonly>
                </td>
                <td class="text-end">
                    <input type="number" name="items[${rowCount}][biaya_satuan]" class="form-control text-end bg-transparent border-0 cost-input" readonly>
                </td>
                <td class="pe-4 text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeRow(${rowCount})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    }

    function removeRow(id) {
        document.getElementById(`row-${id}`).remove();
        calculateTotal();
    }

    function updateRowInfo(select, id) {
        const option = select.options[select.selectedIndex];
        const row = document.getElementById(`row-${id}`);
        row.querySelector('.satuan-label').value = option.dataset.satuan || '';
        row.querySelector('.cost-input').value = option.dataset.harga || 0;
        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('#bomTable tbody tr').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
            total += qty * cost;
        });
        document.getElementById('total_biaya_label').innerText = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2 }).format(total);
        document.getElementById('total_biaya_input').value = total;
    }

    // Add first row on load
    window.onload = addRow;
</script>

<style>
    .bg-light { background-color: #f8f9fa !important; }
    .select-bahan:focus, .qty-input:focus { background-color: #fff !important; box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.1); }
</style>
