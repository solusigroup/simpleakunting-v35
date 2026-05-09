<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Posisi Keuangan - <?php echo $data['perusahaan']['nama_perusahaan']; ?></title>
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
        <h3>LAPORAN POSISI KEUANGAN (NERACA)</h3>
        <p>Per Tanggal: <?php echo $data['periode_1']; ?> <?php if($data['periode_2']) echo " dan " . $data['periode_2']; ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Keterangan</th>
                <th class="text-end"><?php echo $data['periode_1']; ?></th>
                <?php if($data['periode_2']): ?>
                    <th class="text-end"><?php echo $data['periode_2']; ?></th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <tr class="bg-light fw-bold">
                <td colspan="<?php echo $data['periode_2'] ? '3' : '2'; ?>">ASET</td>
            </tr>
            <?php foreach($data['laporan']['periode_1']['aset'] as $item): 
                $key2 = array_search($item['kode_akun'], array_column($data['laporan']['periode_2']['aset'] ?? [], 'kode_akun'));
                $total2 = ($key2 !== false) ? $data['laporan']['periode_2']['aset'][$key2]['total'] : 0;
            ?>
            <tr>
                <td class="ps-4"><?php echo htmlspecialchars($item['nama_akun']); ?></td>
                <td class="text-end"><?php echo number_format($item['total'], 2, ',', '.'); ?></td>
                <?php if($data['periode_2']): ?>
                    <td class="text-end"><?php echo number_format($total2, 2, ',', '.'); ?></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
            <tr class="fw-bold">
                <td>TOTAL ASET</td>
                <td class="text-end"><?php echo number_format($data['laporan']['periode_1']['total_aset'], 2, ',', '.'); ?></td>
                <?php if($data['periode_2']): ?>
                    <td class="text-end"><?php echo number_format($data['laporan']['periode_2']['total_aset'], 2, ',', '.'); ?></td>
                <?php endif; ?>
            </tr>

            <tr class="bg-light fw-bold">
                <td colspan="<?php echo $data['periode_2'] ? '3' : '2'; ?>" style="padding-top: 15px;">KEWAJIBAN</td>
            </tr>
            <?php foreach($data['laporan']['periode_1']['kewajiban'] as $item): 
                $key2 = array_search($item['kode_akun'], array_column($data['laporan']['periode_2']['kewajiban'] ?? [], 'kode_akun'));
                $total2 = ($key2 !== false) ? $data['laporan']['periode_2']['kewajiban'][$key2]['total'] : 0;
            ?>
            <tr>
                <td class="ps-4"><?php echo htmlspecialchars($item['nama_akun']); ?></td>
                <td class="text-end"><?php echo number_format($item['total'], 2, ',', '.'); ?></td>
                <?php if($data['periode_2']): ?>
                    <td class="text-end"><?php echo number_format($total2, 2, ',', '.'); ?></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
            <tr class="fw-bold">
                <td>TOTAL KEWAJIBAN</td>
                <td class="text-end"><?php echo number_format($data['laporan']['periode_1']['total_kewajiban'], 2, ',', '.'); ?></td>
                <?php if($data['periode_2']): ?>
                    <td class="text-end"><?php echo number_format($data['laporan']['periode_2']['total_kewajiban'], 2, ',', '.'); ?></td>
                <?php endif; ?>
            </tr>

            <tr class="bg-light fw-bold">
                <td colspan="<?php echo $data['periode_2'] ? '3' : '2'; ?>" style="padding-top: 15px;">EKUITAS</td>
            </tr>
            <?php foreach($data['laporan']['periode_1']['modal'] as $item): 
                $key2 = array_search($item['kode_akun'], array_column($data['laporan']['periode_2']['modal'] ?? [], 'kode_akun'));
                $total2 = ($key2 !== false) ? $data['laporan']['periode_2']['modal'][$key2]['total'] : 0;
            ?>
            <tr>
                <td class="ps-4"><?php echo htmlspecialchars($item['nama_akun']); ?></td>
                <td class="text-end"><?php echo number_format($item['total'], 2, ',', '.'); ?></td>
                <?php if($data['periode_2']): ?>
                    <td class="text-end"><?php echo number_format($total2, 2, ',', '.'); ?></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
            <tr class="fw-bold">
                <td>TOTAL EKUITAS</td>
                <td class="text-end"><?php echo number_format($data['laporan']['periode_1']['total_modal'], 2, ',', '.'); ?></td>
                <?php if($data['periode_2']): ?>
                    <td class="text-end"><?php echo number_format($data['laporan']['periode_2']['total_modal'], 2, ',', '.'); ?></td>
                <?php endif; ?>
            </tr>
        </tbody>
        <tfoot>
            <tr class="fw-bold" style="background-color: #333; color: white;">
                <td style="border: none;">TOTAL KEWAJIBAN & EKUITAS</td>
                <td class="text-end" style="border: none;"><?php echo number_format($data['laporan']['periode_1']['total_kewajiban'] + $data['laporan']['periode_1']['total_modal'], 2, ',', '.'); ?></td>
                <?php if($data['periode_2']): ?>
                    <td class="text-end" style="border: none;"><?php echo number_format($data['laporan']['periode_2']['total_kewajiban'] + $data['laporan']['periode_2']['total_modal'], 2, ',', '.'); ?></td>
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
