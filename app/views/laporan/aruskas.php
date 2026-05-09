<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h3 class="fw-bold text-dark mb-4">Laporan Arus Kas</h3>
        <form id="laporan-form" action="<?php echo BASEURL; ?>/laporan/arusKas" method="post">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="tanggal_mulai" class="form-label small fw-bold text-muted">DARI TANGGAL</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($data['tanggal_mulai'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label for="tanggal_selesai" class="form-label small fw-bold text-muted">SAMPAI TANGGAL</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($data['tanggal_selesai'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label for="metode" class="form-label small fw-bold text-muted">METODE LAPORAN</label>
                    <select name="metode" id="metode" class="form-select rounded-pill px-3">
                        <option value="indirect" <?php echo ($data['metode'] == 'indirect') ? 'selected' : ''; ?>>Tidak Langsung (Indirect)</option>
                        <option value="direct" <?php echo ($data['metode'] == 'direct') ? 'selected' : ''; ?>>Langsung (Direct)</option>
                    </select>
                </div>
                <div class="col-md-3 text-end">
                    <div class="btn-group w-100 shadow-sm rounded-pill overflow-hidden">
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Tampilkan</button>
                        <button type="button" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><button type="button" id="export-excel" class="dropdown-item py-2"><i class="bi bi-file-earmark-excel text-success me-2"></i> Ekspor ke Excel</button></li>
                            <li><button type="button" id="export-pdf" class="dropdown-item py-2"><i class="bi bi-file-earmark-pdf text-danger me-2"></i> Ekspor ke PDF</button></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Input tersembunyi untuk ekspor -->
            <input type="hidden" name="tanggal_mulai_export" id="tanggal_mulai_export">
            <input type="hidden" name="tanggal_selesai_export" id="tanggal_selesai_export">
            <input type="hidden" name="metode_export" id="metode_export">
        </form>
    </div>
</div>

<?php if (isset($data['laporan']) && $data['laporan'] !== null): ?>
<div class="card shadow-sm border-0 mt-4 overflow-hidden">
    <div class="card-header bg-white text-center py-4 border-0">
        <h4 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($data['perusahaan']['nama_perusahaan'] ?? 'Nama Perusahaan'); ?></h4>
        <h5 class="text-muted mb-2">Laporan Arus Kas (Metode <?php echo ($data['metode'] == 'direct') ? 'Langsung' : 'Tidak Langsung'; ?>)</h5>
        <p class="mb-0 small text-muted">Periode: <?php echo htmlspecialchars($data['periode_1'] ?? ''); ?></p>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light text-uppercase small fw-bold text-muted">
                    <tr>
                        <th>Keterangan</th>
                        <th class="text-end pe-4">Nilai (IDR)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Bagian Arus Kas Operasi -->
                    <tr>
                        <td colspan="2" class="bg-light fw-bold py-3"><i class="bi bi-activity me-2"></i> ARUS KAS DARI AKTIVITAS OPERASI</td>
                    </tr>
                    
                    <?php if ($data['metode'] == 'indirect'): ?>
                        <!-- Tampilan Metode Tidak Langsung -->
                        <tr>
                            <td class="ps-4">Laba Bersih</td>
                            <td class="text-end pe-4 fw-bold"><?php echo number_format($data['laporan']['laba_bersih'] ?? 0, 2, ',', '.'); ?></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="ps-4 small text-muted italic">Penyesuaian laba bersih ke kas bersih:</td>
                        </tr>
                        <?php 
                            $totalOperasi = $data['laporan']['laba_bersih'] ?? 0;
                            foreach(($data['laporan']['penyesuaian'] ?? []) as $item): 
                            $totalOperasi += $item['jumlah'];
                        ?>
                        <tr>
                            <td class="ps-5"><?php echo htmlspecialchars($item['label']); ?></td>
                            <td class="text-end pe-4 text-<?php echo ($item['jumlah'] < 0) ? 'danger' : 'success'; ?>">
                                <?php echo number_format($item['jumlah'], 2, ',', '.'); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Tampilan Metode Langsung -->
                        <?php 
                            $totalOperasi = 0;
                            foreach(($data['laporan']['arus_operasi'] ?? []) as $item): 
                            $totalOperasi += $item['jumlah'];
                        ?>
                        <tr>
                            <td class="ps-4"><?php echo htmlspecialchars($item['label']); ?></td>
                            <td class="text-end pe-4 text-<?php echo ($item['jumlah'] < 0) ? 'danger' : 'success'; ?>">
                                <?php echo number_format($item['jumlah'], 2, ',', '.'); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <tr class="table-primary-subtle fw-bold">
                        <td class="ps-4">Total Arus Kas Bersih dari Aktivitas Operasi</td>
                        <td class="text-end pe-4 border-top border-dark"><?php echo number_format($totalOperasi, 2, ',', '.'); ?></td>
                    </tr>

                    <!-- Bagian Investasi & Pendanaan (Placeholders) -->
                    <tr>
                        <td colspan="2" class="bg-light fw-bold py-3"><i class="bi bi-piggy-bank me-2"></i> ARUS KAS DARI AKTIVITAS INVESTASI</td>
                    </tr>
                    <tr>
                        <td class="ps-4 text-muted"><em>(Belum ada transaksi investasi)</em></td>
                        <td class="text-end pe-4">0,00</td>
                    </tr>

                    <tr>
                        <td colspan="2" class="bg-light fw-bold py-3"><i class="bi bi-cash-stack me-2"></i> ARUS KAS DARI AKTIVITAS PENDANAAN</td>
                    </tr>
                    <tr>
                        <td class="ps-4 text-muted"><em>(Belum ada transaksi pendanaan)</em></td>
                        <td class="text-end pe-4">0,00</td>
                    </tr>
                </tbody>
                <tfoot class="border-top-0">
                    <tr class="fw-bold bg-light">
                        <td class="py-3">Kenaikan (Penurunan) Bersih Kas</td>
                        <td class="text-end pe-4 py-3 border-top"><?php echo number_format(($data['laporan']['kas_akhir'] ?? 0) - ($data['laporan']['kas_awal'] ?? 0), 2, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td class="py-3">Saldo Kas Awal Periode</td>
                        <td class="text-end pe-4 py-3"><?php echo number_format($data['laporan']['kas_awal'] ?? 0, 2, ',', '.'); ?></td>
                    </tr>
                    <tr class="bg-primary text-white fs-5 fw-bold">
                        <td class="py-3 px-4 rounded-start">SALDO KAS AKHIR PERIODE</td>
                        <td class="text-end pe-4 py-3 rounded-end"><?php echo number_format($data['laporan']['kas_akhir'] ?? 0, 2, ',', '.'); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="row mt-5" style="page-break-inside: avoid;">
            <div class="col-6 text-center">
                <p class="text-muted small mb-4">Disetujui Oleh,</p>
                <br><br><br>
                <p class="fw-bold mb-0"><u><?php echo htmlspecialchars($data['penandatangan_1']['nama_user'] ?? ''); ?></u></p>
                <p class="small text-muted"><?php echo htmlspecialchars($data['penandatangan_1']['jabatan'] ?? ''); ?></p>
            </div>
            <div class="col-6 text-center">
                <p class="text-muted small mb-1"><?php echo htmlspecialchars($data['kota_laporan'] ?? 'Mojokerto'); ?>, <?php echo date('d/m/Y'); ?></p>
                <p class="text-muted small mb-4">Dibuat Oleh,</p>
                <br><br><br>
                <p class="fw-bold mb-0"><u><?php echo htmlspecialchars($data['penandatangan_2']['nama_user'] ?? ''); ?></u></p>
                <p class="small text-muted"><?php echo htmlspecialchars($data['penandatangan_2']['jabatan'] ?? ''); ?></p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('laporan-form');
        const exportExcelBtn = document.getElementById('export-excel');
        const exportPdfBtn = document.getElementById('export-pdf');

        function prepareExportData() {
            document.getElementById('tanggal_mulai_export').value = document.getElementById('tanggal_mulai').value;
            document.getElementById('tanggal_selesai_export').value = document.getElementById('tanggal_selesai').value;
            document.getElementById('metode_export').value = document.getElementById('metode').value;
        }
        
        if(exportExcelBtn) {
            exportExcelBtn.addEventListener('click', function() {
                prepareExportData();
                form.action = "<?php echo BASEURL; ?>/laporan/eksporArusKas";
                form.submit();
                form.action = "<?php echo BASEURL; ?>/laporan/arusKas";
            });
        }

        if(exportPdfBtn) {
            exportPdfBtn.addEventListener('click', function() {
                prepareExportData();
                form.action = "<?php echo BASEURL; ?>/laporan/eksporPdfArusKas";
                form.target = "_blank";
                form.submit();
                form.action = "<?php echo BASEURL; ?>/laporan/arusKas";
                form.target = "_self";
            });
        }
    });
</script>

<style>
    .table-primary-subtle { background-color: #e0e7ff; }
    .italic { font-style: italic; }
</style>
