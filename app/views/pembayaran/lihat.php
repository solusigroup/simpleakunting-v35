<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Detail Pembayaran: <?php echo htmlspecialchars($data['pembayaran']['no_bukti']); ?></h4>
        <div>
            <a href="<?php echo BASEURL; ?>/pembayaran" class="btn btn-secondary btn-sm">Kembali</a>
            <button onclick="window.print()" class="btn btn-primary btn-sm">Cetak Bukti</button>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Dibayarkan Kepada:</h5>
                <p class="mb-1"><strong><?php echo htmlspecialchars($data['pembayaran']['nama_pemasok']); ?></strong></p>
                <p><?php echo nl2br(htmlspecialchars($data['pembayaran']['alamat_pemasok'] ?? '')); ?></p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-1"><strong>Tanggal:</strong> <?php echo date('d M Y', strtotime($data['pembayaran']['tanggal'])); ?></p>
                <p class="mb-1"><strong>Dari Akun:</strong> <?php echo htmlspecialchars($data['pembayaran']['nama_akun_kas']); ?></p>
            </div>
        </div>

        <h5>Detail Pembayaran Faktur</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>No. Faktur</th>
                        <th>Tanggal Faktur</th>
                        <th class="text-end">Jumlah Dibayar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data['pembayaran']['details'] as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['no_faktur_pembelian']); ?></td>
                        <td><?php echo ($item['tanggal_faktur'] !== 'N/A' && $item['tanggal_faktur'] !== null) ? date('d M Y', strtotime($item['tanggal_faktur'])) : 'N/A'; ?></td>
                        <td class="text-end"><?php echo number_format($item['jumlah_bayar'], 2, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <th colspan="2" class="text-end"><h4>Total Dibayar</h4></th>
                        <th class="text-end"><h4><?php echo number_format($data['pembayaran']['total_dibayar'], 2, ',', '.'); ?></h4></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <?php if(!empty($data['pembayaran']['keterangan'])): ?>
        <div class="mt-4">
            <strong>Keterangan:</strong>
            <p><?php echo nl2br(htmlspecialchars($data['pembayaran']['keterangan'])); ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>
