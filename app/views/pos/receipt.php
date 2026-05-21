<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran POS - <?php echo $data['transaksi']['no_receipt']; ?></title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            background-color: #fff;
            color: #000;
            margin: 0;
            padding: 20px;
            font-size: 13px;
            line-height: 1.4;
        }

        .receipt-container {
            max-width: 320px;
            margin: 0 auto;
            background: #fff;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .border-dashed {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
            vertical-align: top;
        }

        .text-end {
            text-align: right;
        }

        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .mt-2 { margin-top: 8px; }
        .mt-4 { margin-top: 24px; }

        /* Print styling */
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
            .receipt-container {
                max-width: 100%;
            }
        }

        .btn {
            display: inline-block;
            padding: 8px 16px;
            font-family: sans-serif;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            border: 1px solid #ccc;
            background-color: #f8f9fa;
            color: #333;
            margin-right: 8px;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
            color: white;
        }

        .btn-primary:hover {
            background-color: #4338ca;
        }

        .button-bar {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <!-- Print Action Bar -->
    <div class="button-bar no-print">
        <button class="btn btn-primary" onclick="window.print()">Cetak Struk</button>
        <a href="<?php echo BASEURL; ?>/pos" class="btn">Kembali ke POS</a>
        <a href="<?php echo BASEURL; ?>/pos/riwayat" class="btn">Riwayat Transaksi</a>
    </div>

    <!-- Receipt paper container -->
    <div class="receipt-container">
        
        <!-- Company Header -->
        <div class="text-center">
            <div class="fw-bold text-uppercase" style="font-size: 15px;"><?php echo $data['transaksi']['nama_perusahaan'] ?? 'SIMPLEAKUNTING'; ?></div>
            <div style="font-size: 11px;"><?php echo $data['transaksi']['alamat_perusahaan'] ?? 'Alamat Perusahaan'; ?></div>
            <div style="font-size: 11px;">Tlp: <?php echo $data['transaksi']['telepon_perusahaan'] ?? '-'; ?></div>
        </div>

        <div class="border-dashed"></div>

        <!-- Transaction Details Header -->
        <table style="font-size: 11px; color: #555;">
            <tr>
                <td>No. Struk</td>
                <td class="text-end fw-bold" style="color: #000;"><?php echo $data['transaksi']['no_receipt']; ?></td>
            </tr>
            <tr>
                <td>No. Faktur</td>
                <td class="text-end font-monospace"><?php echo $data['transaksi']['no_faktur']; ?></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td class="text-end"><?php echo date('d M Y H:i', strtotime($data['transaksi']['created_at'])); ?></td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td class="text-end"><?php echo $data['transaksi']['kasir_name'] ?? 'Kasir'; ?></td>
            </tr>
            <tr>
                <td>Pelanggan</td>
                <td class="text-end"><?php echo $data['transaksi']['nama_pelanggan'] ?? 'Walk-in Customer'; ?></td>
            </tr>
        </table>

        <div class="border-dashed"></div>

        <!-- Item line items -->
        <div class="mb-2">
            <?php 
            $subtotal_items = 0;
            foreach ($data['transaksi']['details'] as $item): 
                $subtotal_items += $item['subtotal'];
            ?>
                <div>
                    <div><?php echo $item['nama_barang']; ?></div>
                    <table style="font-size: 12px; color: #444;">
                        <tr>
                            <td style="padding-left: 10px;">  <?php echo (float)$item['kuantitas']; ?> <?php echo $item['satuan']; ?> x <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                            <td class="text-end"><?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                        </tr>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="border-dashed"></div>

        <!-- Pricing Totals -->
        <table class="fw-bold" style="font-size: 13px;">
            <tr>
                <td>Subtotal</td>
                <td class="text-end"><?php echo number_format($subtotal_items, 0, ',', '.'); ?></td>
            </tr>
            <?php if ($data['transaksi']['total_diskon'] > 0): ?>
                <tr>
                    <td>Diskon</td>
                    <td class="text-end">-<?php echo number_format($data['transaksi']['total_diskon'], 0, ',', '.'); ?></td>
                </tr>
            <?php endif; ?>
            <?php if ($data['transaksi']['total_pajak'] > 0): ?>
                <tr>
                    <td>PPN</td>
                    <td class="text-end"><?php echo number_format($data['transaksi']['total_pajak'], 0, ',', '.'); ?></td>
                </tr>
            <?php endif; ?>
            <tr style="font-size: 15px;">
                <td>GRAND TOTAL</td>
                <td class="text-end"><?php echo number_format($data['transaksi']['total'], 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <td colspan="2"><div style="border-top: 1px solid #000; margin: 4px 0;"></div></td>
            </tr>
            <tr style="font-weight: normal; font-size: 12px;">
                <td>Bayar (Tunai)</td>
                <td class="text-end"><?php echo number_format($data['transaksi']['bayar'], 0, ',', '.'); ?></td>
            </tr>
            <tr class="fw-bold">
                <td>Kembalian</td>
                <td class="text-end"><?php echo number_format($data['transaksi']['kembalian'], 0, ',', '.'); ?></td>
            </tr>
        </table>

        <div class="border-dashed"></div>

        <!-- Footer Message -->
        <div class="text-center fw-bold mt-4" style="font-size: 11px;">
            *** TERIMA KASIH ***<br>
            Barang yang sudah dibeli tidak dapat ditukar/dikembalikan
        </div>

    </div>

    <!-- Automatically print on load if requested -->
    <?php if (isset($_GET['autoprint']) && $_GET['autoprint'] == '1'): ?>
        <script>
            window.addEventListener('load', function() {
                window.print();
            });
        </script>
    <?php endif; ?>
</body>
</html>
