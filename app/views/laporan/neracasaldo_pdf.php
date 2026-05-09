<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Neraca Saldo - <?php echo $data['perusahaan']['nama_perusahaan']; ?></title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10pt; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .report-title { text-align: center; margin-bottom: 20px; }
        .report-title h3 { margin: 0; font-size: 14pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f2f2f2; border: 1px solid #ccc; padding: 8px; text-align: left; font-weight: bold; }
        td { border: 1px solid #ccc; padding: 6px 8px; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .footer-table { border: none; margin-top: 40px; }
        .footer-table td { border: none; text-align: center; width: 50%; }
    </style>
</head>
<body>
    <div class="header">
        <h2><?php echo htmlspecialchars($data['perusahaan']['nama_perusahaan']); ?></h2>
    </div>

    <div class="report-title">
        <h3>LAPORAN NERACA SALDO</h3>
        <p>Per Tanggal: <?php echo $data['periode_1']; ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">Kode Akun</th>
                <th>Nama Akun</th>
                <th width="20%" class="text-end">Debit</th>
                <th width="20%" class="text-end">Kredit</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $totalDebit = 0;
                $totalKredit = 0;
                foreach($data['laporan'] as $row): 
                $totalDebit += $row['debit'];
                $totalKredit += $row['kredit'];
            ?>
            <tr>
                <td><?php echo $row['kode_akun']; ?></td>
                <td><?php echo htmlspecialchars($row['nama_akun']); ?></td>
                <td class="text-end"><?php echo number_format($row['debit'], 2, ',', '.'); ?></td>
                <td class="text-end"><?php echo number_format($row['kredit'], 2, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="fw-bold" style="background-color: #f9f9f9;">
                <td colspan="2" class="text-end">TOTAL</td>
                <td class="text-end"><?php echo number_format($totalDebit, 2, ',', '.'); ?></td>
                <td class="text-end"><?php echo number_format($totalKredit, 2, ',', '.'); ?></td>
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
