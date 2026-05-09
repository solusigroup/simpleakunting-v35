<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Arus Kas - <?php echo $data['perusahaan']['nama_perusahaan']; ?></title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10pt; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .report-title { text-align: center; margin-bottom: 20px; }
        .report-title h3 { margin: 0; font-size: 14pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        td { border-bottom: 1px solid #eee; padding: 8px; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .ps-4 { padding-left: 25px; }
        .footer-table { border: none; margin-top: 40px; }
        .footer-table td { border: none; text-align: center; width: 50%; }
        .bg-light { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="header">
        <h2><?php echo htmlspecialchars($data['perusahaan']['nama_perusahaan']); ?></h2>
    </div>

    <div class="report-title">
        <h3>LAPORAN ARUS KAS (Metode <?php echo $data['laporan']['metode']; ?>)</h3>
        <p>Periode: <?php echo $data['periode_1']; ?></p>
    </div>

    <table>
        <tbody>
            <tr><td colspan="2" class="fw-bold">Arus Kas dari Aktivitas Operasi</td></tr>
            <?php if($data['laporan']['metode'] == 'Indirect'): ?>
                <tr><td class="ps-4">Laba Bersih</td><td class="text-end"><?php echo number_format($data['laporan']['laba_bersih'], 2, ',', '.'); ?></td></tr>
                <?php $totalOperasi = $data['laporan']['laba_bersih']; foreach($data['laporan']['penyesuaian'] as $item): $totalOperasi += $item['jumlah']; ?>
                    <tr><td class="ps-4"><?php echo htmlspecialchars($item['label']); ?></td><td class="text-end"><?php echo number_format($item['jumlah'], 2, ',', '.'); ?></td></tr>
                <?php endforeach; ?>
            <?php else: ?>
                <?php $totalOperasi = 0; foreach($data['laporan']['arus_operasi'] as $item): $totalOperasi += $item['jumlah']; ?>
                    <tr><td class="ps-4"><?php echo htmlspecialchars($item['label']); ?></td><td class="text-end"><?php echo number_format($item['jumlah'], 2, ',', '.'); ?></td></tr>
                <?php endforeach; ?>
            <?php endif; ?>
            <tr class="fw-bold bg-light"><td>Arus Kas Bersih dari Aktivitas Operasi</td><td class="text-end"><?php echo number_format($totalOperasi, 2, ',', '.'); ?></td></tr>
            
            <tr><td colspan="2" class="fw-bold" style="padding-top: 15px;">Kenaikan/Penurunan Kas</td></tr>
            <tr><td>Kas Awal Periode</td><td class="text-end"><?php echo number_format($data['laporan']['kas_awal'], 2, ',', '.'); ?></td></tr>
            <tr><td>Kenaikan (Penurunan) Kas Bersih</td><td class="text-end"><?php echo number_format($data['laporan']['kas_akhir'] - $data['laporan']['kas_awal'], 2, ',', '.'); ?></td></tr>
            <tr class="fw-bold bg-light" style="font-size: 11pt;"><td>KAS AKHIR PERIODE</td><td class="text-end"><?php echo number_format($data['laporan']['kas_akhir'], 2, ',', '.'); ?></td></tr>
        </tbody>
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
