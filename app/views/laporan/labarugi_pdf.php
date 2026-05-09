<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laba Rugi - <?php echo $data['perusahaan']['nama_perusahaan']; ?></title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10pt; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 9pt; color: #666; }
        .report-title { text-align: center; margin-bottom: 20px; }
        .report-title h3 { margin: 0; font-size: 14pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f2f2f2; border: 1px solid #ccc; padding: 8px; text-align: left; font-weight: bold; }
        td { border: 1px solid #ccc; padding: 6px 8px; }
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
        <p><?php echo htmlspecialchars($data['perusahaan']['alamat']); ?></p>
    </div>

    <div class="report-title">
        <h3>LAPORAN LABA RUGI KOMPARATIF</h3>
        <p>Periode: <?php echo $data['periode_1']; ?> <?php if($data['periode_2']) echo " vs " . $data['periode_2']; ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Keterangan</th>
                <th class="text-end"><?php echo $data['periode_1']; ?></th>
                <?php if($data['periode_2']): ?>
                    <th class="text-end"><?php echo $data['periode_2']; ?></th>
                    <th class="text-end">Var (Rp)</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <tr class="bg-light fw-bold">
                <td colspan="<?php echo $data['periode_2'] ? '4' : '2'; ?>">PENDAPATAN</td>
            </tr>
            <?php foreach($data['laporan']['pendapatan'] as $item): ?>
            <tr>
                <td class="ps-4"><?php echo htmlspecialchars($item['nama_akun']); ?></td>
                <td class="text-end"><?php echo number_format($item['total_1'], 2, ',', '.'); ?></td>
                <?php if($data['periode_2']): ?>
                    <td class="text-end"><?php echo number_format($item['total_2'], 2, ',', '.'); ?></td>
                    <td class="text-end"><?php echo number_format($item['total_1'] - $item['total_2'], 2, ',', '.'); ?></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
            <tr class="fw-bold">
                <td>TOTAL PENDAPATAN</td>
                <td class="text-end"><?php echo number_format($data['laporan']['total_pendapatan_1'], 2, ',', '.'); ?></td>
                <?php if($data['periode_2']): ?>
                    <td class="text-end"><?php echo number_format($data['laporan']['total_pendapatan_2'], 2, ',', '.'); ?></td>
                    <td></td>
                <?php endif; ?>
            </tr>

            <tr class="bg-light fw-bold">
                <td colspan="<?php echo $data['periode_2'] ? '4' : '2'; ?>" style="padding-top: 15px;">BEBAN-BEBAN</td>
            </tr>
            <?php foreach($data['laporan']['beban'] as $item): ?>
            <tr>
                <td class="ps-4"><?php echo htmlspecialchars($item['nama_akun']); ?></td>
                <td class="text-end"><?php echo number_format($item['total_1'], 2, ',', '.'); ?></td>
                <?php if($data['periode_2']): ?>
                    <td class="text-end"><?php echo number_format($item['total_2'], 2, ',', '.'); ?></td>
                    <td class="text-end"><?php echo number_format($item['total_1'] - $item['total_2'], 2, ',', '.'); ?></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
            <tr class="fw-bold">
                <td>TOTAL BEBAN</td>
                <td class="text-end"><?php echo number_format($data['laporan']['total_beban_1'], 2, ',', '.'); ?></td>
                <?php if($data['periode_2']): ?>
                    <td class="text-end"><?php echo number_format($data['laporan']['total_beban_2'], 2, ',', '.'); ?></td>
                    <td></td>
                <?php endif; ?>
            </tr>
        </tbody>
        <tfoot>
            <?php 
                $laba1 = $data['laporan']['total_pendapatan_1'] - $data['laporan']['total_beban_1'];
                $laba2 = $data['laporan']['total_pendapatan_2'] - $data['laporan']['total_beban_2'];
            ?>
            <tr class="fw-bold" style="background-color: #333; color: white;">
                <td style="border: none;">LABA (RUGI) BERSIH</td>
                <td class="text-end" style="border: none;"><?php echo number_format($laba1, 2, ',', '.'); ?></td>
                <?php if($data['periode_2']): ?>
                    <td class="text-end" style="border: none;"><?php echo number_format($laba2, 2, ',', '.'); ?></td>
                    <td style="border: none;"></td>
                <?php endif; ?>
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
