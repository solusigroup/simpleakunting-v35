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
                <?php if (empty($data['laporan']['arus_operasi'])): ?>
                    <tr><td class="ps-4" style="color: #888; font-style: italic;">(Tidak ada penyesuaian modal kerja)</td><td class="text-end">0,00</td></tr>
                <?php else: ?>
                    <?php foreach($data['laporan']['arus_operasi'] as $item): ?>
                        <tr><td class="ps-4"><?php echo htmlspecialchars($item['label']); ?></td><td class="text-end"><?php echo ($item['jumlah'] < 0) ? '(' . number_format(abs($item['jumlah']), 2, ',', '.') . ')' : number_format($item['jumlah'], 2, ',', '.'); ?></td></tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php else: ?>
                <?php if (empty($data['laporan']['arus_operasi'])): ?>
                    <tr><td class="ps-4" style="color: #888; font-style: italic;">(Tidak ada transaksi operasi)</td><td class="text-end">0,00</td></tr>
                <?php else: ?>
                    <?php foreach($data['laporan']['arus_operasi'] as $item): ?>
                        <tr><td class="ps-4"><?php echo htmlspecialchars($item['label']); ?></td><td class="text-end"><?php echo ($item['jumlah'] < 0) ? '(' . number_format(abs($item['jumlah']), 2, ',', '.') . ')' : number_format($item['jumlah'], 2, ',', '.'); ?></td></tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>
            <tr class="fw-bold bg-light"><td>Arus Kas Bersih dari Aktivitas Operasi</td><td class="text-end"><?php $totOp = $data['laporan']['total_operasi'] ?? 0; echo ($totOp < 0) ? '(' . number_format(abs($totOp), 2, ',', '.') . ')' : number_format($totOp, 2, ',', '.'); ?></td></tr>
            
            <tr><td colspan="2" class="fw-bold" style="padding-top: 15px;">Arus Kas dari Aktivitas Investasi</td></tr>
            <?php if(empty($data['laporan']['arus_investasi'])): ?>
                <tr><td class="ps-4" style="color: #888; font-style: italic;">(Tidak ada transaksi investasi)</td><td class="text-end">0,00</td></tr>
            <?php else: ?>
                <?php foreach($data['laporan']['arus_investasi'] as $item): ?>
                    <tr><td class="ps-4"><?php echo htmlspecialchars($item['label']); ?></td><td class="text-end"><?php echo ($item['jumlah'] < 0) ? '(' . number_format(abs($item['jumlah']), 2, ',', '.') . ')' : number_format($item['jumlah'], 2, ',', '.'); ?></td></tr>
                <?php endforeach; ?>
            <?php endif; ?>
            <tr class="fw-bold bg-light"><td>Arus Kas Bersih dari Aktivitas Investasi</td><td class="text-end"><?php $totInv = $data['laporan']['total_investasi'] ?? 0; echo ($totInv < 0) ? '(' . number_format(abs($totInv), 2, ',', '.') . ')' : number_format($totInv, 2, ',', '.'); ?></td></tr>

            <tr><td colspan="2" class="fw-bold" style="padding-top: 15px;">Arus Kas dari Aktivitas Pendanaan</td></tr>
            <?php if(empty($data['laporan']['arus_pendanaan'])): ?>
                <tr><td class="ps-4" style="color: #888; font-style: italic;">(Tidak ada transaksi pendanaan)</td><td class="text-end">0,00</td></tr>
            <?php else: ?>
                <?php foreach($data['laporan']['arus_pendanaan'] as $item): ?>
                    <tr><td class="ps-4"><?php echo htmlspecialchars($item['label']); ?></td><td class="text-end"><?php echo ($item['jumlah'] < 0) ? '(' . number_format(abs($item['jumlah']), 2, ',', '.') . ')' : number_format($item['jumlah'], 2, ',', '.'); ?></td></tr>
                <?php endforeach; ?>
            <?php endif; ?>
            <tr class="fw-bold bg-light"><td>Arus Kas Bersih dari Aktivitas Pendanaan</td><td class="text-end"><?php $totPen = $data['laporan']['total_pendanaan'] ?? 0; echo ($totPen < 0) ? '(' . number_format(abs($totPen), 2, ',', '.') . ')' : number_format($totPen, 2, ',', '.'); ?></td></tr>

            <tr><td colspan="2" class="fw-bold" style="padding-top: 15px;">Rekonsiliasi Kas &amp; Setara Kas</td></tr>
            <tr><td>Kenaikan (Penurunan) Kas Bersih</td><td class="text-end"><?php $kenaikan = $data['laporan']['kenaikan_kas_bersih'] ?? (($data['laporan']['kas_akhir'] ?? 0) - ($data['laporan']['kas_awal'] ?? 0)); echo ($kenaikan < 0) ? '(' . number_format(abs($kenaikan), 2, ',', '.') . ')' : number_format($kenaikan, 2, ',', '.'); ?></td></tr>
            <tr><td>Kas Awal Periode</td><td class="text-end"><?php echo number_format($data['laporan']['kas_awal'], 2, ',', '.'); ?></td></tr>
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
