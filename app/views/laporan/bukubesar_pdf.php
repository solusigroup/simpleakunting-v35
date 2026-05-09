<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Besar - <?php echo $data['perusahaan']['nama_perusahaan']; ?></title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10pt; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 9pt; color: #666; }
        .report-title { text-align: center; margin-bottom: 20px; }
        .report-title h3 { margin: 0; font-size: 14pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f2f2f2; border: 1px solid #ccc; padding: 8px; text-align: left; font-weight: bold; }
        td { border: 1px solid #ccc; padding: 6px 8px; vertical-align: top; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .footer-table { border: none; margin-top: 40px; }
        .footer-table td { border: none; text-align: center; width: 50%; }
    </style>
</head>
<body>
    <div class="header">
        <h2><?php echo htmlspecialchars($data['perusahaan']['nama_perusahaan']); ?></h2>
        <p><?php echo htmlspecialchars($data['perusahaan']['alamat']); ?></p>
        <p>Email: <?php echo htmlspecialchars($data['perusahaan']['email']); ?> | Telp: <?php echo htmlspecialchars($data['perusahaan']['telepon']); ?></p>
    </div>

    <div class="report-title">
        <h3>LAPORAN BUKU BESAR</h3>
        <p>Akun: [<?php echo $data['kode_akun_terpilih']; ?>] <?php echo $data['nama_akun_terpilih']; ?><br>
        Periode: <?php echo $data['periode_1']; ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">Tanggal</th>
                <th width="15%">No. Bukti</th>
                <th>Keterangan</th>
                <th width="15%" class="text-end">Debit</th>
                <th width="15%" class="text-end">Kredit</th>
                <th width="15%" class="text-end">Saldo</th>
            </tr>
        </thead>
        <tbody>
            <tr class="fw-bold">
                <td><?php echo date('d-m-Y', strtotime($data['tanggal_mulai'])); ?></td>
                <td>-</td>
                <td>SALDO AWAL PERIODE</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end"><?php echo number_format($data['laporan']['saldo_awal_periode'], 2, ',', '.'); ?></td>
            </tr>
            <?php 
                $saldo = $data['laporan']['saldo_awal_periode'];
                foreach ($data['laporan']['transaksi'] as $trx): 
                    if ($data['laporan']['posisi_saldo_normal'] == 'Debit') {
                        $saldo += ($trx['debit'] - $trx['kredit']);
                    } else {
                        $saldo += ($trx['kredit'] - $trx['debit']);
                    }
            ?>
            <tr>
                <td><?php echo date('d-m-Y', strtotime($trx['tanggal'])); ?></td>
                <td><?php echo $trx['no_transaksi']; ?></td>
                <td><?php echo htmlspecialchars($trx['deskripsi']); ?></td>
                <td class="text-end"><?php echo number_format($trx['debit'], 2, ',', '.'); ?></td>
                <td class="text-end"><?php echo number_format($trx['kredit'], 2, ',', '.'); ?></td>
                <td class="text-end"><?php echo number_format($saldo, 2, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="fw-bold" style="background-color: #f9f9f9;">
                <td colspan="5" class="text-end">SALDO AKHIR PERIODE</td>
                <td class="text-end"><?php echo number_format($saldo, 2, ',', '.'); ?></td>
            </tr>
        </tfoot>
    </table>

    <table class="footer-table">
        <tr>
            <td>
                <p><?php echo htmlspecialchars($data['penandatangan_1']['jabatan']); ?></p>
                <br><br><br>
                <p class="fw-bold"><u><?php echo htmlspecialchars($data['penandatangan_1']['nama_user']); ?></u></p>
            </td>
            <td>
                <p><?php echo htmlspecialchars($data['kota_laporan']); ?>, <?php echo date('d F Y'); ?></p>
                <p><?php echo htmlspecialchars($data['penandatangan_2']['jabatan']); ?></p>
                <br><br><br>
                <p class="fw-bold"><u><?php echo htmlspecialchars($data['penandatangan_2']['nama_user']); ?></u></p>
            </td>
        </tr>
    </table>
</body>
</html>
