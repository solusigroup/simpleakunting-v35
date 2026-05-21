<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point of Sales - SimpleAkunting v3.5</title>
    <!-- Bootstrap 5.3.2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <!-- Google Fonts (Inter & Outfit) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-main: #0b0f19;
            --bg-card: rgba(17, 24, 39, 0.7);
            --border-color: rgba(255, 255, 255, 0.08);
            --primary-accent: #6366f1; /* Beautiful Indigo */
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            overflow: hidden;
            height: 100vh;
            margin: 0;
        }

        h1, h2, h3, h4, .brand-font {
            font-family: 'Outfit', sans-serif;
        }

        /* Glassmorphism utility */
        .glass-card {
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 16px;
        }

        /* POS Fullscreen Grid */
        .pos-wrapper {
            display: grid;
            grid-template-columns: 1fr 420px;
            height: calc(100vh - 65px);
            gap: 20px;
            padding: 20px;
        }

        @media (max-width: 992px) {
            .pos-wrapper {
                grid-template-columns: 1fr;
                overflow-y: auto;
                height: auto;
            }
            .cart-panel {
                height: 600px !important;
            }
        }

        /* Topbar styling */
        .pos-topbar {
            height: 65px;
            border-bottom: 1px solid var(--border-color);
            background: rgba(11, 15, 25, 0.8);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.1);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Product Catalog Panel */
        .catalog-panel {
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 16px;
            overflow-y: auto;
            flex-grow: 1;
            padding-right: 4px;
        }

        /* Product Cards */
        .product-card {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 190px;
        }

        .product-card:hover {
            transform: translateY(-4px);
            border-color: var(--primary-accent);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.15);
        }

        .product-card .stock-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 30px;
            font-weight: 600;
        }

        .product-card .product-title {
            font-size: 0.95rem;
            font-weight: 600;
            line-height: 1.3;
            margin-bottom: 6px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 2.6em;
        }

        .product-card .product-code {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-family: monospace;
        }

        .product-card .product-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: #818cf8; /* Light Indigo */
        }

        /* Shopping Cart Panel */
        .cart-panel {
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
        }

        .cart-items {
            flex-grow: 1;
            overflow-y: auto;
            padding: 8px;
        }

        .cart-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .cart-item:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(99, 102, 241, 0.3);
        }

        /* Qty buttons */
        .qty-ctrl {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .qty-btn {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.08);
            border: none;
            color: #fff;
            transition: background 0.2s;
        }

        .qty-btn:hover {
            background: var(--primary-accent);
        }

        .qty-input {
            width: 45px;
            height: 28px;
            text-align: center;
            background: rgba(0,0,0,0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 6px;
            font-size: 0.85rem;
        }

        /* Summary pricing */
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        /* Main Pay Button */
        .btn-pay {
            background: var(--success-gradient);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 16px;
            font-weight: 700;
            font-size: 1.15rem;
            width: 100%;
            transition: all 0.25s;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
            font-family: 'Outfit', sans-serif;
        }

        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
            filter: brightness(1.1);
        }

        /* Custom style input */
        .search-input-group {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 4px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-input-group input {
            background: transparent;
            border: none;
            color: #fff;
            outline: none;
            width: 100%;
            padding: 8px 0;
            font-size: 1rem;
        }

        .search-input-group input::placeholder {
            color: var(--text-muted);
        }

        /* Custom forms */
        .form-dark {
            background: rgba(0, 0, 0, 0.3) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #fff !important;
            border-radius: 8px;
        }

        .form-dark:focus {
            border-color: var(--primary-accent) !important;
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25) !important;
            background: rgba(0, 0, 0, 0.4) !important;
        }

        /* Keyboard Shortcut Legend */
        .shortcut-badge {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 4px;
            padding: 1px 6px;
            font-size: 0.75rem;
            font-family: monospace;
            color: var(--text-muted);
            margin-left: 6px;
        }

        /* Live Receipt Preview in Modal */
        .receipt-paper {
            background: #fff;
            color: #000;
            font-family: monospace;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            font-size: 0.85rem;
            max-width: 360px;
            margin: 0 auto;
        }

        /* Custom styles for quick pay */
        .btn-quick-pay {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: #fff;
            border-radius: 8px;
            padding: 8px;
            font-weight: 600;
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        .btn-quick-pay:hover {
            background: var(--primary-accent);
            border-color: var(--primary-accent);
            transform: translateY(-1px);
        }

        /* Switch styling PPN */
        .form-check-input:checked {
            background-color: var(--primary-accent);
            border-color: var(--primary-accent);
        }
    </style>
</head>
<body>

    <!-- Header / Topbar -->
    <header class="pos-topbar">
        <div class="d-flex align-items-center gap-3">
            <a href="<?php echo BASEURL; ?>/dashboard" class="btn btn-outline-light btn-sm border-secondary d-flex align-items-center gap-2 rounded-3 px-3 py-2">
                <i class="bi bi-arrow-left"></i> <span class="small fw-semibold">Dashboard</span>
            </a>
            <div class="h5 mb-0 brand-font fw-bold text-white d-flex align-items-center gap-2">
                <i class="bi bi-upc-scan text-indigo"></i> SimpleAkunting POS
            </div>
        </div>

        <!-- Cashier & Stats -->
        <div class="d-flex align-items-center gap-4">
            <div class="d-none d-md-flex align-items-center gap-3">
                <div class="text-end">
                    <div class="small text-muted">Kasir Aktif</div>
                    <div class="fw-bold text-white"><?php echo Auth::user()['name']; ?></div>
                </div>
                <div class="bg-secondary-subtle opacity-25" style="width: 1px; height: 30px;"></div>
                <div class="text-end">
                    <div class="small text-muted">Penjualan Hari Ini</div>
                    <div class="fw-bold text-success" id="stat-sales"><?php echo number_format($data['statistik']['jumlah_transaksi']); ?> Transaksi (Rp <?php echo number_format($data['statistik']['total_penjualan'], 0, ',', '.'); ?>)</div>
                </div>
            </div>
            
            <div id="live-clock" class="brand-font fw-bold text-muted bg-dark px-3 py-2 rounded-3 border border-secondary border-opacity-25" style="font-size: 0.95rem;">
                00:00:00
            </div>
        </div>
    </header>

    <!-- Main Container Grid -->
    <main class="pos-wrapper">
        
        <!-- Left Side: Catalog Panel -->
        <section class="catalog-panel">
            
            <!-- Filters & Search -->
            <div class="glass-card p-3 mb-3 d-flex flex-column flex-md-row gap-3 align-items-stretch">
                <!-- Search bar -->
                <div class="search-input-group flex-grow-1">
                    <i class="bi bi-search text-muted fs-5"></i>
                    <input type="text" id="search-product" placeholder="Cari barang berdasarkan nama, kode, atau barcode... (Shortcut: F2)" autocomplete="off">
                    <i class="bi bi-qr-code-scan text-primary fs-5 cursor-pointer" title="Scan Barcode Mode"></i>
                </div>

                <!-- Fast Categories or Quick Filters if needed -->
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-dark active rounded-3 px-3 py-2" id="filter-all">Semua Produk</button>
                    <button class="btn btn-sm btn-dark rounded-3 px-3 py-2" id="filter-stock">Tersedia</button>
                    <a href="<?php echo BASEURL; ?>/pos/riwayat" class="btn btn-sm btn-outline-secondary rounded-3 d-flex align-items-center gap-2 px-3 py-2">
                        <i class="bi bi-clock-history"></i> Riwayat
                    </a>
                </div>
            </div>

            <!-- Product Catalog Grid -->
            <div class="product-grid" id="product-list">
                <!-- Loaded via JS -->
            </div>
        </section>

        <!-- Right Side: Cart Panel -->
        <section class="cart-panel glass-card p-3 d-flex flex-column">
            
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2 border-secondary border-opacity-25">
                <div class="h5 mb-0 brand-font fw-bold text-white d-flex align-items-center gap-2">
                    <i class="bi bi-cart3 text-indigo"></i> Keranjang Belanja
                </div>
                <span class="badge bg-indigo rounded-pill px-3 py-1 fw-bold" id="cart-count">0 Item</span>
            </div>

            <!-- Customer & Kas/Bank Selectors -->
            <div class="mb-3 d-flex flex-column gap-2">
                <div>
                    <label class="small text-muted mb-1">Akun Kas/Bank Penerima</label>
                    <select id="select-kas" class="form-select form-dark text-white">
                        <?php foreach($data['akun_kas'] as $kas): ?>
                            <option value="<?php echo $kas['kode_akun']; ?>"><?php echo $kas['nama_akun']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="small text-muted mb-1">Pelanggan</label>
                    <select id="select-pelanggan" class="form-select form-dark text-white">
                        <?php foreach($data['pelanggan'] as $plg): ?>
                            <option value="<?php echo $plg['id_pelanggan']; ?>" <?php echo ($plg['id_pelanggan'] == ($data['walk_in']['id_pelanggan'] ?? '')) ? 'selected' : ''; ?>>
                                <?php echo $plg['nama_pelanggan']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Cart Line Items -->
            <div class="cart-items" id="cart-list">
                <!-- Cart items will be loaded here dynamically -->
                <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted" id="empty-cart-view">
                    <i class="bi bi-basket2 fs-1 mb-2 opacity-50 text-indigo"></i>
                    <p class="small text-center opacity-70">Keranjang masih kosong.<br>Klik produk di sebelah kiri untuk menambahkan.</p>
                </div>
            </div>

            <!-- Cart Pricing Summary & Checkout -->
            <div class="border-top pt-3 border-secondary border-opacity-25 mt-auto">
                <div class="summary-row">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-medium text-white" id="summary-subtotal">Rp 0</span>
                </div>

                <div class="summary-row">
                    <span class="text-muted">Diskon (Rp)</span>
                    <input type="number" id="summary-discount" class="form-control form-dark text-end py-1 px-2 border-secondary" style="width: 140px; font-size: 0.85rem;" value="0" min="0">
                </div>

                <div class="summary-row">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted">PPN (<?php echo (float)($data['perusahaan']['persentase_pajak_default'] ?? 11); ?>%)</span>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="tax-toggle" checked>
                        </div>
                    </div>
                    <span class="fw-medium text-white" id="summary-tax">Rp 0</span>
                </div>

                <div class="summary-row border-top border-secondary border-opacity-25 pt-2 mt-2">
                    <span class="h5 mb-0 brand-font fw-bold text-white">GRAND TOTAL</span>
                    <span class="h4 mb-0 brand-font fw-extrabold text-indigo" id="summary-total">Rp 0</span>
                </div>

                <button class="btn-pay mt-3 py-3" id="btn-trigger-pay">
                    <i class="bi bi-wallet2 me-2"></i> BAYAR SEKARANG <span class="shortcut-badge">F9</span>
                </button>
            </div>
        </section>
    </main>

    <!-- Modal 1: Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-card border border-secondary border-opacity-25" style="background: #0f172a; color: #fff;">
                <div class="modal-header border-bottom border-secondary border-opacity-25">
                    <h5 class="modal-title brand-font fw-bold text-white" id="paymentModalLabel">
                        <i class="bi bi-cash-coin text-success me-2"></i> Pembayaran Transaksi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Prominent Total -->
                    <div class="text-center mb-4 bg-dark bg-opacity-50 p-3 rounded-4 border border-secondary border-opacity-10">
                        <span class="text-muted d-block small text-uppercase fw-semibold mb-1">Total Tagihan</span>
                        <span class="h2 mb-0 brand-font fw-extrabold text-success" id="pay-modal-total">Rp 0</span>
                    </div>

                    <!-- Payment Cash Input -->
                    <div class="mb-4">
                        <label class="form-label text-muted fw-semibold">Jumlah Uang Diterima (Bayar)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-white font-monospace">Rp</span>
                            <input type="text" id="pay-amount-input" class="form-control form-dark text-end fs-3 fw-bold py-2 border-secondary" placeholder="0" autofocus autocomplete="off">
                        </div>
                    </div>

                    <!-- Quick Pay Buttons Grid -->
                    <label class="form-label text-muted small mb-2">Uang Pas / Nominal Cepat</label>
                    <div class="grid-quick-amounts d-grid gap-2 mb-4" style="grid-template-columns: repeat(3, 1fr);">
                        <button class="btn btn-quick-pay" id="btn-pay-exact">Uang Pas</button>
                        <button class="btn btn-quick-pay" id="btn-pay-10k">Rp 10.000</button>
                        <button class="btn btn-quick-pay" id="btn-pay-20k">Rp 20.000</button>
                        <button class="btn btn-quick-pay" id="btn-pay-50k">Rp 50.000</button>
                        <button class="btn btn-quick-pay" id="btn-pay-100k">Rp 100.000</button>
                        <button class="btn btn-quick-pay" id="btn-pay-200k">Rp 200.000</button>
                    </div>

                    <!-- Change/Kembalian Info -->
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-secondary bg-opacity-10 border border-secondary border-opacity-15 mb-4">
                        <span class="fw-medium text-muted">Uang Kembalian:</span>
                        <span class="h4 mb-0 brand-font fw-bold" id="pay-modal-change">Rp 0</span>
                    </div>

                    <!-- Alert message area -->
                    <div id="payment-alert" class="alert alert-danger d-none py-2 px-3 small rounded-3"></div>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25">
                    <button type="button" class="btn btn-dark px-4 py-2" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success px-4 py-2 fw-semibold" id="btn-confirm-payment">
                        <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true" id="pay-spinner"></span>
                        Konfirmasi & Cetak Struk
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 2: Success & Receipt View Modal -->
    <div class="modal fade" id="receiptModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-card border border-secondary border-opacity-25" style="background: #0f172a; color: #fff;">
                <div class="modal-header border-bottom border-secondary border-opacity-25 bg-success bg-opacity-10">
                    <h5 class="modal-title brand-font fw-bold text-success d-flex align-items-center gap-2" id="receiptModalLabel">
                        <i class="bi bi-check-circle-fill"></i> Transaksi Berhasil!
                    </h5>
                </div>
                <div class="modal-body p-3">
                    
                    <!-- Printable Receipt area -->
                    <div class="receipt-paper rounded-3 my-2" id="printable-receipt-area">
                        <!-- Content loaded dynamically from Javascript -->
                    </div>

                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-light rounded-3" id="btn-new-trx">
                        <i class="bi bi-plus-circle me-1"></i> Transaksi Baru
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary rounded-3 px-3" id="btn-print-receipt">
                            <i class="bi bi-printer me-1"></i> Cetak Struk
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Global Javascript Data & Logic -->
    <script>
        // Data injected from PHP
        const PRODUCTS = <?php echo json_encode($data['barang']); ?>;
        const WALK_IN_ID = '<?php echo $data['walk_in']['id_pelanggan'] ?? ''; ?>';
        const RECEIPT_NO = '<?php echo $data['no_receipt']; ?>';
        const TAX_RATE = <?php echo (float)($data['perusahaan']['persentase_pajak_default'] ?? 11); ?> / 100;
        const CSRF_TOKEN = '<?php echo Auth::getCsrfToken(); ?>';
        const BASE_URL = '<?php echo BASEURL; ?>';
        const COMPANY = <?php echo json_encode($data['perusahaan']); ?>;
        const KASIR_NAME = '<?php echo Auth::user()['name']; ?>';

        // Cart State
        let cart = [];
        let grandTotal = 0;
        let lastSavedPosId = null;

        // Modals
        let paymentModal;
        let receiptModal;

        document.addEventListener('DOMContentLoaded', function() {
            paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
            receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));

            // Initial catalog render
            renderCatalog(PRODUCTS);

            // Clock initiation
            startClock();

            // Keyboard shortcut listener
            document.addEventListener('keydown', handleShortcuts);

            // Setup Event Listeners
            document.getElementById('search-product').addEventListener('input', handleSearch);
            document.getElementById('filter-all').addEventListener('click', () => {
                document.getElementById('filter-all').classList.add('active');
                document.getElementById('filter-stock').classList.remove('active');
                renderCatalog(PRODUCTS);
            });
            document.getElementById('filter-stock').addEventListener('click', () => {
                document.getElementById('filter-stock').classList.add('active');
                document.getElementById('filter-all').classList.remove('active');
                const filtered = PRODUCTS.filter(p => parseFloat(p.stok_saat_ini) > 0);
                renderCatalog(filtered);
            });

            document.getElementById('summary-discount').addEventListener('input', updateCartSummary);
            document.getElementById('tax-toggle').addEventListener('change', updateCartSummary);
            document.getElementById('btn-trigger-pay').addEventListener('click', openPaymentModal);
            
            // Payment Form inputs
            document.getElementById('pay-amount-input').addEventListener('input', formatPayAmount);
            document.getElementById('btn-confirm-payment').addEventListener('click', processPayment);

            // Quick cash buttons
            document.getElementById('btn-pay-exact').addEventListener('click', () => setPayAmount(grandTotal));
            document.getElementById('btn-pay-10k').addEventListener('click', () => setPayAmount(10000));
            document.getElementById('btn-pay-20k').addEventListener('click', () => setPayAmount(20000));
            document.getElementById('btn-pay-50k').addEventListener('click', () => setPayAmount(50000));
            document.getElementById('btn-pay-100k').addEventListener('click', () => setPayAmount(100000));
            document.getElementById('btn-pay-200k').addEventListener('click', () => setPayAmount(200000));

            // Receipt buttons
            document.getElementById('btn-new-trx').addEventListener('click', resetPOSState);
            document.getElementById('btn-print-receipt').addEventListener('click', () => {
                if (lastSavedPosId) {
                    const printWindow = window.open(BASE_URL + '/pos/receipt/' + lastSavedPosId, '_blank');
                    printWindow.print();
                }
            });

            // Focus search on start
            document.getElementById('search-product').focus();
        });

        // ----------------- Helper Functions -----------------
        function formatIDR(num) {
            return 'Rp ' + parseFloat(num).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        function startClock() {
            setInterval(() => {
                const now = new Date();
                const str = now.toTimeString().split(' ')[0];
                document.getElementById('live-clock').innerText = str;
            }, 1000);
        }

        function handleShortcuts(e) {
            if (e.key === 'F2') {
                e.preventDefault();
                document.getElementById('search-product').focus();
            } else if (e.key === 'F9') {
                e.preventDefault();
                openPaymentModal();
            } else if (e.key === 'Escape') {
                // Modals will close automatically via Bootstrap if focus is correct
            }
        }

        // ----------------- Catalog Logic -----------------
        function renderCatalog(products) {
            const container = document.getElementById('product-list');
            container.innerHTML = '';

            if (products.length === 0) {
                container.innerHTML = `
                    <div class="col-12 text-center text-muted py-5 glass-card">
                        <i class="bi bi-search fs-1 mb-2 opacity-50"></i>
                        <p class="small">Tidak ada barang yang cocok dengan pencarian Anda.</p>
                    </div>
                `;
                return;
            }

            products.forEach(p => {
                const isOutOfStock = parseFloat(p.stok_saat_ini) <= 0;
                const stockBadgeClass = isOutOfStock ? 'bg-danger text-white' : 'bg-success text-white';
                const stockText = isOutOfStock ? 'Stok Habis' : `Stok: ${parseInt(p.stok_saat_ini)}`;
                const disabledAttr = isOutOfStock ? 'style="opacity: 0.6; pointer-events: none;"' : '';

                const card = document.createElement('div');
                card.className = 'glass-card p-3 product-card';
                card.innerHTML = `
                    <span class="stock-badge ${stockBadgeClass}">${stockText}</span>
                    <div class="mt-3">
                        <div class="product-code">${p.kode_barang}</div>
                        <div class="product-title" title="${p.nama_barang}">${p.nama_barang}</div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <div class="product-price">${formatIDR(p.harga_jual)}</div>
                        <button class="btn btn-sm btn-primary rounded-3 btn-add-cart" ${isOutOfStock ? 'disabled' : ''}>
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                `;

                if (!isOutOfStock) {
                    card.addEventListener('click', () => addToCart(p));
                }
                container.appendChild(card);
            });
        }

        function handleSearch(e) {
            const val = e.target.value.toLowerCase().trim();
            
            // Check if it's a perfect barcode scan match
            if (val.length >= 4) {
                const perfectMatch = PRODUCTS.find(p => p.barcode === val || p.kode_barang.toLowerCase() === val);
                if (perfectMatch && parseFloat(perfectMatch.stok_saat_ini) > 0) {
                    addToCart(perfectMatch);
                    e.target.value = ''; // clear scanner input
                    return;
                }
            }

            const filtered = PRODUCTS.filter(p => 
                p.nama_barang.toLowerCase().includes(val) || 
                p.kode_barang.toLowerCase().includes(val) || 
                (p.barcode && p.barcode.includes(val))
            );
            renderCatalog(filtered);
        }

        // ----------------- Cart Logic -----------------
        function addToCart(product) {
            const existing = cart.find(item => item.id_barang === product.id_barang);

            if (existing) {
                if (existing.qty + 1 > parseFloat(product.stok_saat_ini)) {
                    alert(`Stok tidak mencukupi. Stok maksimal: ${parseInt(product.stok_saat_ini)}`);
                    return;
                }
                existing.qty++;
                existing.subtotal = existing.qty * existing.harga;
            } else {
                if (parseFloat(product.stok_saat_ini) < 1) {
                    alert('Barang sedang kosong.');
                    return;
                }
                cart.push({
                    id_barang: product.id_barang,
                    kode_barang: product.kode_barang,
                    nama_barang: product.nama_barang,
                    harga: parseFloat(product.harga_jual),
                    qty: 1,
                    subtotal: parseFloat(product.harga_jual),
                    stok_max: parseFloat(product.stok_saat_ini)
                });
            }

            renderCart();
            updateCartSummary();

            // Highlight animation for product grid item or cart item
            const cartList = document.getElementById('cart-list');
            cartList.scrollTop = cartList.scrollHeight; // Scroll to bottom
        }

        function removeCartItem(id) {
            cart = cart.filter(item => item.id_barang !== id);
            renderCart();
            updateCartSummary();
        }

        function updateCartItemQty(id, delta) {
            const item = cart.find(i => i.id_barang === id);
            if (item) {
                const newQty = item.qty + delta;
                if (newQty <= 0) {
                    removeCartItem(id);
                } else if (newQty > item.stok_max) {
                    alert(`Stok tidak mencukupi. Stok maksimal: ${parseInt(item.stok_max)}`);
                } else {
                    item.qty = newQty;
                    item.subtotal = item.qty * item.harga;
                    renderCart();
                    updateCartSummary();
                }
            }
        }

        function handleManualQty(id, val) {
            const qty = parseInt(val) || 1;
            const item = cart.find(i => i.id_barang === id);
            if (item) {
                if (qty <= 0) {
                    removeCartItem(id);
                } else if (qty > item.stok_max) {
                    alert(`Stok tidak mencukupi. Stok maksimal: ${parseInt(item.stok_max)}`);
                    item.qty = item.stok_max;
                    item.subtotal = item.qty * item.harga;
                    renderCart();
                    updateCartSummary();
                } else {
                    item.qty = qty;
                    item.subtotal = item.qty * item.harga;
                    renderCart();
                    updateCartSummary();
                }
            }
        }

        function renderCart() {
            const list = document.getElementById('cart-list');
            const emptyView = document.getElementById('empty-cart-view');
            const countBadge = document.getElementById('cart-count');

            if (cart.length === 0) {
                list.innerHTML = '';
                list.appendChild(emptyView);
                countBadge.innerText = '0 Item';
                return;
            }

            if (emptyView.parentNode) {
                list.innerHTML = '';
            }

            countBadge.innerText = `${cart.reduce((a, b) => a + b.qty, 0)} Item`;

            cart.forEach(item => {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'cart-item';
                itemDiv.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small fw-bold text-white mb-1" style="line-height:1.2;">${item.nama_barang}</div>
                            <div class="small text-muted font-monospace" style="font-size:0.75rem;">${item.kode_barang} | ${formatIDR(item.harga)}</div>
                        </div>
                        <button class="btn btn-sm btn-outline-danger border-0 p-1" onclick="removeCartItem(${item.id_barang})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <div class="qty-ctrl">
                            <button class="qty-btn" onclick="updateCartItemQty(${item.id_barang}, -1)"><i class="bi bi-minus"></i></button>
                            <input type="text" class="qty-input" value="${item.qty}" onchange="handleManualQty(${item.id_barang}, this.value)">
                            <button class="qty-btn" onclick="updateCartItemQty(${item.id_barang}, 1)"><i class="bi bi-plus"></i></button>
                        </div>
                        <div class="fw-bold text-white font-monospace">${formatIDR(item.subtotal)}</div>
                    </div>
                `;
                list.appendChild(itemDiv);
            });
        }

        function updateCartSummary() {
            let subtotal = cart.reduce((a, b) => a + b.subtotal, 0);
            let discount = parseFloat(document.getElementById('summary-discount').value) || 0;
            
            if (discount < 0) discount = 0;
            if (discount > subtotal) {
                discount = subtotal;
                document.getElementById('summary-discount').value = subtotal;
            }

            let tax = 0;
            const useTax = document.getElementById('tax-toggle').checked;
            if (useTax) {
                tax = (subtotal - discount) * TAX_RATE;
            }

            grandTotal = subtotal - discount + tax;

            document.getElementById('summary-subtotal').innerText = formatIDR(subtotal);
            document.getElementById('summary-tax').innerText = formatIDR(tax);
            document.getElementById('summary-total').innerText = formatIDR(grandTotal);
        }

        // ----------------- Checkout / Payment Logic -----------------
        function openPaymentModal() {
            if (cart.length === 0) {
                alert('Keranjang belanja masih kosong.');
                return;
            }

            // Populate Modal values
            document.getElementById('pay-modal-total').innerText = formatIDR(grandTotal);
            document.getElementById('pay-amount-input').value = grandTotal.toLocaleString('id-ID');
            document.getElementById('pay-modal-change').innerText = formatIDR(0);
            
            document.getElementById('payment-alert').classList.add('d-none');
            
            paymentModal.show();

            // Focus payment input after modal displays
            setTimeout(() => {
                const inp = document.getElementById('pay-amount-input');
                inp.focus();
                inp.select();
            }, 500);
        }

        function formatPayAmount(e) {
            const rawVal = e.target.value.replace(/[^0-9]/g, '');
            const num = parseFloat(rawVal) || 0;
            e.target.value = num.toLocaleString('id-ID');
            
            // Calculate change
            const change = num - grandTotal;
            const changeLabel = document.getElementById('pay-modal-change');
            
            if (change < 0) {
                changeLabel.innerText = 'Kurang ' + formatIDR(Math.abs(change));
                changeLabel.className = 'h4 mb-0 brand-font fw-bold text-danger';
            } else {
                changeLabel.innerText = formatIDR(change);
                changeLabel.className = 'h4 mb-0 brand-font fw-bold text-success';
            }
        }

        function setPayAmount(amt) {
            const inp = document.getElementById('pay-amount-input');
            inp.value = amt.toLocaleString('id-ID');
            formatPayAmount({ target: inp });
        }

        function processPayment() {
            const rawVal = document.getElementById('pay-amount-input').value.replace(/[^0-9]/g, '');
            const payVal = parseFloat(rawVal) || 0;

            if (payVal < grandTotal) {
                const alertBox = document.getElementById('payment-alert');
                alertBox.innerText = 'Uang pembayaran kurang dari total tagihan.';
                alertBox.classList.remove('d-none');
                return;
            }

            // Spinner start
            document.getElementById('btn-confirm-payment').disabled = true;
            document.getElementById('pay-spinner').classList.remove('d-none');

            // Build payload
            const payload = {
                csrf_token: CSRF_TOKEN,
                id_pelanggan: document.getElementById('select-pelanggan').value,
                akun_kas_bank: document.getElementById('select-kas').value,
                diskon: parseFloat(document.getElementById('summary-discount').value) || 0,
                pajak: document.getElementById('tax-toggle').checked ? (cart.reduce((a, b) => a + b.subtotal, 0) - (parseFloat(document.getElementById('summary-discount').value) || 0)) * TAX_RATE : 0,
                bayar: payVal,
                items: cart.map(i => ({
                    id_barang: i.id_barang,
                    qty: i.qty,
                    harga: i.harga,
                    subtotal: i.subtotal
                }))
            };

            // Post to Server
            fetch(BASE_URL + '/pos/simpan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    lastSavedPosId = data.data.pos_id;
                    
                    // Populate receipt modal HTML
                    generateDigitalReceipt(data.data);
                    
                    paymentModal.hide();
                    receiptModal.show();
                } else {
                    const alertBox = document.getElementById('payment-alert');
                    alertBox.innerText = data.message || 'Gagal menyimpan transaksi.';
                    alertBox.classList.remove('d-none');
                }
            })
            .catch(err => {
                console.error(err);
                const alertBox = document.getElementById('payment-alert');
                alertBox.innerText = 'Koneksi gagal atau terjadi error server.';
                alertBox.classList.remove('d-none');
            })
            .finally(() => {
                document.getElementById('btn-confirm-payment').disabled = false;
                document.getElementById('pay-spinner').classList.add('d-none');
            });
        }

        // ----------------- Receipt Logic -----------------
        function generateDigitalReceipt(data) {
            const paper = document.getElementById('printable-receipt-area');
            
            let itemLines = '';
            cart.forEach(item => {
                const subStr = formatIDR(item.subtotal).replace('Rp ', '');
                const prcStr = formatIDR(item.harga).replace('Rp ', '');
                itemLines += `
<div class="d-flex justify-content-between">
    <span>${item.nama_barang}</span>
</div>
<div class="d-flex justify-content-between mb-1 text-muted" style="font-size:0.75rem;">
    <span>  ${item.qty} x ${prcStr}</span>
    <span>${subStr}</span>
</div>`;
            });

            const rawSub = cart.reduce((a, b) => a + b.subtotal, 0);
            
            paper.innerHTML = `
<div class="text-center mb-3">
    <div class="fw-bold fs-6 text-uppercase">${COMPANY.nama_perusahaan || 'SIMPLEAKUNTING'}</div>
    <div class="small opacity-75">${COMPANY.alamat || 'Alamat Perusahaan'}</div>
    <div class="small opacity-75">Tlp: ${COMPANY.telepon || '-'}</div>
</div>
<div class="border-bottom border-dashed border-dark my-2"></div>
<table class="w-100 mb-2 small text-muted" style="font-size: 0.75rem;">
    <tr><td>No. Receipt</td><td class="text-end fw-bold">${data.no_receipt}</td></tr>
    <tr><td>Tanggal</td><td class="text-end">${data.tanggal}</td></tr>
    <tr><td>Kasir</td><td class="text-end">${data.kasir}</td></tr>
    <tr><td>Pelanggan</td><td class="text-end">${data.pelanggan}</td></tr>
</table>
<div class="border-bottom border-dashed border-dark my-2"></div>

<div class="receipt-items mb-2">
    ${itemLines}
</div>

<div class="border-bottom border-dashed border-dark my-2"></div>
<table class="w-100 small font-monospace">
    <tr><td>Subtotal</td><td class="text-end">${formatIDR(rawSub).replace('Rp ', '')}</td></tr>
    ${data.diskon > 0 ? `<tr><td>Diskon</td><td class="text-end">-${formatIDR(data.diskon).replace('Rp ', '')}</td></tr>` : ''}
    ${data.pajak > 0 ? `<tr><td>PPN (${TAX_RATE*100}%)</td><td class="text-end">${formatIDR(data.pajak).replace('Rp ', '')}</td></tr>` : ''}
    <tr class="fw-bold"><td class="fs-6">GRAND TOTAL</td><td class="text-end fs-6">${formatIDR(data.total).replace('Rp ', '')}</td></tr>
    <tr class="text-muted"><td colspan="2"><hr class="my-1"></td></tr>
    <tr><td>Bayar (Tunai)</td><td class="text-end">${formatIDR(data.bayar).replace('Rp ', '')}</td></tr>
    <tr class="fw-bold text-success"><td>Kembalian</td><td class="text-end">${formatIDR(data.kembalian).replace('Rp ', '')}</td></tr>
</table>
<div class="border-bottom border-dashed border-dark my-2"></div>
<div class="text-center small mt-3 fw-bold" style="font-size: 0.75rem;">
    *** TERIMA KASIH ***<br>
    Barang yang sudah dibeli tidak dapat ditukar/dikembalikan
</div>`;
        }

        function resetPOSState() {
            cart = [];
            grandTotal = 0;
            lastSavedPosId = null;
            
            document.getElementById('summary-discount').value = '0';
            document.getElementById('tax-toggle').checked = true;
            document.getElementById('search-product').value = '';
            
            renderCart();
            updateCartSummary();
            renderCatalog(PRODUCTS);
            
            receiptModal.hide();
            
            // Reload statistics
            fetchStatistics();

            document.getElementById('search-product').focus();
        }

        function fetchStatistics() {
            // Simple statistics reload via AJAX
            fetch(BASE_URL + '/pos')
                .then(res => res.text())
                .then(html => {
                    const temp = document.createElement('div');
                    temp.innerHTML = html;
                    const stats = temp.querySelector('#stat-sales');
                    if (stats) {
                        document.getElementById('stat-sales').innerHTML = stats.innerHTML;
                    }
                });
        }
    </script>
</body>
</html>
