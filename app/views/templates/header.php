<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['judul']; ?> - SimpleAkunting</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <style>
        /* Custom Tom Select Styling to match Premium UI */
        .ts-control {
            border: none !important;
            background-color: #f8f9fa !important;
            border-radius: 0.75rem !important;
            padding: 0.6rem 1rem !important;
            box-shadow: none !important;
            transition: all 0.2s ease;
        }
        .ts-wrapper.focus .ts-control {
            background-color: #fff !important;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1) !important;
            border: 1px solid rgba(79, 70, 229, 0.2) !important;
        }
        .ts-dropdown {
            border: none !important;
            border-radius: 0.75rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            margin-top: 5px !important;
            padding: 0.5rem !important;
            z-index: 2000 !important;
        }
        .ts-dropdown .active {
            background-color: #4f46e5 !important;
            color: #fff !important;
            border-radius: 0.5rem !important;
        }
        .ts-dropdown .option {
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem !important;
            margin-bottom: 2px;
        }
        /* Fix for table overflow */
        .table-responsive {
            overflow: visible !important;
        }
    </style>
    <link rel="manifest" href="<?php echo BASEURL; ?>/manifest.json?v=2">
    <meta name="theme-color" content="#059669">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SimpleAkunting">
    <link rel="apple-touch-icon" href="<?php echo BASEURL; ?>/img/icon-512.png">
    <link rel="icon" type="image/png" href="<?php echo BASEURL; ?>/img/icon-512.png">



    <style>
        :root {
            --sidebar-width: 280px;
            --primary-color: #059669;
            /* Hijau Pasuruan */
            --primary-hover: #047857;
            --bg-light: #f9fafb;
            --bg-dark: #111827;
            --card-bg-light: #ffffff;
            --card-bg-dark: #1f2937;
            --text-light: #111827;
            --text-dark: #f9fafb;
            --sidebar-bg: #064e3b;
            /* Deep Pasuruan Green */
            --sidebar-text: #ecfdf5;
            --sidebar-active-bg: rgba(255, 255, 255, 0.1);

            /* Default to Light */
            --body-bg: var(--bg-light);
            --card-bg: var(--card-bg-light);
            --text-main: var(--text-light);
            --topbar-bg: rgba(255, 255, 255, 0.8);
        }

        [data-theme="dark"] {
            --body-bg: var(--bg-dark);
            --card-bg: var(--card-bg-dark);
            --text-main: var(--text-dark);
            --topbar-bg: rgba(17, 24, 39, 0.8);
        }

        [data-theme="dark"] .table {
            --bs-table-bg: transparent;
            --bs-table-color: var(--text-dark);
            --bs-table-border-color: rgba(255, 255, 255, 0.1);
        }

        [data-theme="dark"] .table thead th {
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--text-dark);
            border-bottom-color: rgba(255, 255, 255, 0.2);
        }

        [data-theme="dark"] .card {
            background-color: var(--card-bg-dark);
            color: var(--text-dark);
        }

        [data-theme="dark"] .form-control,
        [data-theme="dark"] .form-select {
            background-color: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: var(--text-dark);
        }

        [data-theme="dark"] .form-control:focus,
        [data-theme="dark"] .form-select:focus {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: var(--text-dark);
        }

        [data-theme="dark"] .bg-light {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }

        [data-theme="dark"] .text-muted {
            color: rgba(255, 255, 255, 0.5) !important;
        }

        [data-theme="dark"] .modal-content {
            background-color: var(--card-bg-dark);
            color: var(--text-dark);
        }

        [data-theme="dark"] .topbar .nav-link {
            color: var(--text-dark) !important;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--body-bg);
            color: var(--text-main);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, #065f46 100%);
            padding: 1.5rem 1rem;
            z-index: 1030;
            overflow-y: auto;
            color: var(--sidebar-text);
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0 0.5rem;
        }

        .sidebar .nav-link {
            color: var(--sidebar-text);
            opacity: 0.8;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            transition: all 0.2s;
        }

        .sidebar .nav-link:hover {
            opacity: 1;
            background-color: var(--sidebar-active-bg);
            transform: translateX(4px);
        }

        .sidebar .nav-link.active {
            opacity: 1;
            background-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .sidebar .nav-link .bi {
            font-size: 1.1rem;
            margin-right: 0.75rem;
        }

        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .topbar {
            background-color: var(--topbar-bg);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 0.75rem 1.5rem;
        }

        .card {
            background-color: var(--card-bg);
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        #theme-toggle {
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: background 0.2s;
        }

        #theme-toggle:hover {
            background: rgba(0, 0, 0, 0.05);
        }

        .tenant-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary-color);
            font-weight: 600;
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }

            .main-wrapper {
                margin-left: 0 !important;
            }

            body.sidebar-open .sidebar {
                transform: translateX(0);
            }
        }

        /* Desktop Toggle */
        body.sidebar-collapsed .sidebar {
            transform: translateX(-100%);
        }

        body.sidebar-collapsed .main-wrapper {
            margin-left: 0;
        }

        .sidebar,
        .main-wrapper {
            transition: all 0.3s ease-in-out;
        }

        .animate-pulse {
            animation: pulse-animation 2s infinite;
        }

        @keyframes pulse-animation {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Bottom Navigation Mobile */
        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 65px;
            background-color: var(--card-bg);
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1040;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            padding: 0.5rem 0;
        }

        [data-theme="dark"] .mobile-bottom-nav {
            border-top-color: rgba(255, 255, 255, 0.1);
        }

        .mobile-bottom-nav .nav-item {
            flex: 1;
            text-align: center;
        }

        .mobile-bottom-nav .nav-link {
            color: var(--text-main);
            opacity: 0.6;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 0.75rem;
            gap: 2px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .mobile-bottom-nav .nav-link.active {
            color: var(--primary-color);
            opacity: 1;
        }

        .mobile-bottom-nav .nav-link i {
            font-size: 1.25rem;
        }

        @media (max-width: 768px) {
            .mobile-bottom-nav {
                display: flex;
            }

            body {
                padding-bottom: 70px;
                /* Space for bottom nav */
            }

            .footer {
                margin-bottom: 70px;
            }
        }
    </style>
</head>

<body data-theme="light">

    <?php
    $url_parts = explode('/', trim($_GET['url'] ?? 'home', '/'));
    $current_controller = strtolower($url_parts[0]);

    $master_controllers = ['akun', 'pelanggan', 'pemasok', 'persediaan', 'aset'];
    $transaksi_controllers = ['penjualan', 'pembelian', 'penerimaan', 'pembayaran', 'kas', 'penyesuaian', 'jurnal', 'tutupbuku', 'produksi', 'bom'];
    $laporan_controllers = ['laporan', 'analisis'];
    $user = Auth::user();
    ?>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="bg-white p-1 rounded-2 shadow-sm d-inline-flex overflow-hidden"
                style="width: 40px; height: 40px;">
                <img src="<?php echo BASEURL; ?>/img/logo_pasuruan.png" alt="Logo"
                    class="w-100 h-100 object-fit-contain">
            </div>
            <span class="ms-1">SA v3.5</span>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_controller == 'dashboard') ? 'active' : ''; ?>"
                    href="<?php echo BASEURL; ?>/dashboard">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
            </li>

            <?php if (Auth::isActuallySuperadmin()): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo (in_array($current_controller, ['tenants', 'central'])) ? 'active' : ''; ?>"
                        data-bs-toggle="collapse" href="#centralCollapse">
                        <i class="bi bi-shield-lock-fill"></i> Central Admin <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse <?php echo (in_array($current_controller, ['tenants', 'central'])) ? 'show' : ''; ?>"
                        id="centralCollapse">
                        <a class="nav-link ms-4 py-1 <?php echo ($current_controller == 'tenants') ? 'fw-bold text-white' : ''; ?>"
                            href="<?php echo BASEURL; ?>/tenants">
                            <i class="bi bi-buildings"></i> Tenants
                        </a>
                        <a class="nav-link ms-4 py-1 <?php echo ($current_controller == 'central' && $url_parts[1] == 'users') ? 'fw-bold text-white' : ''; ?>"
                            href="<?php echo BASEURL; ?>/central/users">
                            <i class="bi bi-people"></i> Users Global
                        </a>
                        <a class="nav-link ms-4 py-1 <?php echo ($current_controller == 'central' && $url_parts[1] == 'roles') ? 'fw-bold text-white' : ''; ?>"
                            href="<?php echo BASEURL; ?>/central/roles">
                            <i class="bi bi-key"></i> Roles List
                        </a>
                        <a class="nav-link ms-4 py-1 <?php echo ($current_controller == 'central' && $url_parts[1] == 'monitoring') ? 'fw-bold text-white' : ''; ?>"
                            href="<?php echo BASEURL; ?>/central/monitoring">
                            <i class="bi bi-activity"></i> Monitoring Transaksi
                        </a>
                        <a class="nav-link ms-4 py-1 <?php echo ($current_controller == 'central' && $url_parts[1] == 'agregat') ? 'fw-bold text-white' : ''; ?>"
                            href="<?php echo BASEURL; ?>/central/agregat">
                            <i class="bi bi-bar-chart-steps"></i> Laporan Agregat
                        </a>
                    </div>
                </li>
            <?php endif; ?>

            <?php if ($user['tenant_id'] !== null): ?>
                <li class="nav-item mt-3">
                    <small class="text-uppercase px-3 opacity-50 fw-bold" style="font-size: 0.7rem;">Data Master</small>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_controller == 'pelanggan') ? 'active' : ''; ?>"
                        href="<?php echo BASEURL; ?>/pelanggan">
                        <i class="bi bi-people"></i> Pelanggan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_controller == 'pemasok') ? 'active' : ''; ?>"
                        href="<?php echo BASEURL; ?>/pemasok">
                        <i class="bi bi-truck"></i> Pemasok
                    </a>
                </li>

                <?php if (Auth::hasPermission('master_akun')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_controller == 'akun') ? 'active' : ''; ?>"
                            href="<?php echo BASEURL; ?>/akun">
                            <i class="bi bi-journal-text"></i> Bagan Akun
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($user['database_type'] !== 'jasa' && Auth::hasPermission('master_persediaan')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_controller == 'persediaan') ? 'active' : ''; ?>"
                            href="<?php echo BASEURL; ?>/persediaan">
                            <i class="bi bi-box-seam"></i> Persediaan
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (Auth::hasPermission('master_aset')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_controller == 'aset') ? 'active' : ''; ?>"
                            href="<?php echo BASEURL; ?>/aset">
                            <i class="bi bi-building"></i> Aset Tetap
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item mt-3">
                    <small class="text-uppercase px-3 opacity-50 fw-bold" style="font-size: 0.7rem;">Operasional</small>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (in_array($current_controller, ['penjualan', 'penerimaan', 'pembelian', 'pembayaran'])) ? 'active' : ''; ?>"
                        data-bs-toggle="collapse" href="#tradeCollapse">
                        <i class="bi bi-cart-check"></i> Perdagangan <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse <?php echo (in_array($current_controller, ['penjualan', 'penerimaan', 'pembelian', 'pembayaran', 'penawaran', 'rfq'])) ? 'show' : ''; ?>"
                        id="tradeCollapse">
                        <?php if (Auth::hasPermission('trx_penjualan')): ?>
                            <a class="nav-link ms-4 py-1 <?php echo ($current_controller == 'penawaran') ? 'fw-bold text-white' : ''; ?>"
                                href="<?php echo BASEURL; ?>/penawaran">
                                <i class="bi bi-file-earmark-text"></i> Penawaran Harga
                            </a>
                            <a class="nav-link ms-4 py-1 <?php echo ($current_controller == 'penjualan') ? 'fw-bold text-white' : ''; ?>"
                                href="<?php echo BASEURL; ?>/penjualan">
                                <i class="bi bi-receipt"></i> Penjualan (Faktur)
                            </a>
                        <?php endif; ?>
                        <?php if (Auth::hasPermission('trx_penerimaan')): ?>
                            <a class="nav-link ms-4 py-1 <?php echo ($current_controller == 'penerimaan') ? 'fw-bold text-white' : ''; ?>"
                                href="<?php echo BASEURL; ?>/penerimaan">
                                <i class="bi bi-cash-stack"></i> Penerimaan Piutang
                            </a>
                        <?php endif; ?>

                        <?php if ($user['database_type'] !== 'jasa'): ?>
                            <hr class="dropdown-divider bg-white opacity-25 mx-4">
                            <?php if (Auth::hasPermission('trx_pembelian')): ?>
                                <a class="nav-link ms-4 py-1 <?php echo ($current_controller == 'rfq') ? 'fw-bold text-white' : ''; ?>"
                                    href="<?php echo BASEURL; ?>/rfq">
                                    <i class="bi bi-envelope-paper"></i> Request for Quotation
                                </a>
                                <a class="nav-link ms-4 py-1 <?php echo ($current_controller == 'pembelian') ? 'fw-bold text-white' : ''; ?>"
                                    href="<?php echo BASEURL; ?>/pembelian">
                                    <i class="bi bi-bag-check"></i> Pembelian (Faktur)
                                </a>
                            <?php endif; ?>
                            <?php if (Auth::hasPermission('trx_pembayaran')): ?>
                                <a class="nav-link ms-4 py-1 <?php echo ($current_controller == 'pembayaran') ? 'fw-bold text-white' : ''; ?>"
                                    href="<?php echo BASEURL; ?>/pembayaran">
                                    <i class="bi bi-wallet2"></i> Pembayaran Pemasok
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </li>

                <?php if ($user['database_type'] === 'manufaktur'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (in_array($current_controller, ['bom', 'produksi'])) ? 'active' : ''; ?>"
                            data-bs-toggle="collapse" href="#manufCollapse">
                            <i class="bi bi-tools"></i> Manufaktur <i class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse <?php echo (in_array($current_controller, ['bom', 'produksi'])) ? 'show' : ''; ?>"
                            id="manufCollapse">
                            <?php if (Auth::hasPermission('trx_bom')): ?>
                                <a class="nav-link ms-4 py-1 <?php echo ($current_controller == 'bom') ? 'fw-bold text-white' : ''; ?>"
                                    href="<?php echo BASEURL; ?>/bom">
                                    <i class="bi bi-receipt-cutoff"></i> Bill of Materials
                                </a>
                            <?php endif; ?>
                            <?php if (Auth::hasPermission('trx_produksi')): ?>
                                <a class="nav-link ms-4 py-1 <?php echo ($current_controller == 'produksi') ? 'fw-bold text-white' : ''; ?>"
                                    href="<?php echo BASEURL; ?>/produksi">
                                    <i class="bi bi-gear-wide-connected"></i> Perintah Produksi
                                </a>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endif; ?>

                <li class="nav-item mt-3">
                    <small class="text-uppercase px-3 opacity-50 fw-bold" style="font-size: 0.7rem;">Keuangan</small>
                </li>
                <?php if (Auth::hasPermission('trx_kas')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_controller == 'kas') ? 'active' : ''; ?>"
                            href="<?php echo BASEURL; ?>/kas">
                            <i class="bi bi-bank"></i> Kas & Bank
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (Auth::hasPermission('fin_jurnal')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_controller == 'jurnal') ? 'active' : ''; ?>"
                            href="<?php echo BASEURL; ?>/jurnal">
                            <i class="bi bi-vector-pen"></i> Jurnal Umum
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (Auth::hasPermission('fin_laporan')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_controller == 'laporan') ? 'active' : ''; ?>"
                            href="<?php echo BASEURL; ?>/laporan">
                            <i class="bi bi-pie-chart-fill"></i> Laporan
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Menu Bantuan & Edukasi (Selalu Tampil) -->
            <li class="nav-item mt-4 pt-3 border-top border-secondary-subtle">
                <h6 class="sidebar-heading px-3 text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">Pusat
                    Edukasi</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASEURL; ?>/panduan_pengguna.html" target="_blank">
                    <i class="bi bi-book-half"></i> Panduan v3.5
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-info" href="<?php echo BASEURL; ?>/InfografisSimpleAkuntingUMKM.html"
                    target="_blank">
                    <i class="bi bi-info-square-fill"></i> Infografis UMKM
                </a>
            </li>
        </ul>
    </aside>

    <div class="main-wrapper">
        <nav class="navbar navbar-expand-lg topbar sticky-top">
            <div class="container-fluid">
                <button class="btn border-0 me-2" id="sidebar-toggle">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <div class="d-flex align-items-center gap-3">
                    <h5 class="mb-0 fw-bold d-none d-md-block"><?php echo $data['judul']; ?></h5>
                    <?php if ($user['impersonating']): ?>
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2 border-0 shadow-sm animate-pulse">
                            <i class="bi bi-eye-fill me-1"></i> Impersonating: <?php echo $user['name']; ?>
                        </span>
                        <a href="<?php echo BASEURL; ?>/central/user_stop_impersonate"
                            class="btn btn-sm btn-outline-danger rounded-pill px-3">
                            <i class="bi bi-x-circle"></i> Stop
                        </a>
                    <?php elseif (!empty($user['tenant_id'])): ?>
                        <span class="tenant-badge">
                            <i class="bi bi-shop me-1"></i>
                            <?php echo $user['tenant_name'] ?? 'Tenant: ' . $user['tenant_id']; ?>
                        </span>
                    <?php else: ?>
                        <span class="badge bg-primary rounded-pill px-3 py-2">
                            <i class="bi bi-shield-check me-1"></i> Central Admin
                        </span>
                    <?php endif; ?>
                </div>

                <ul class="navbar-nav ms-auto align-items-center gap-3">
                    <li class="nav-item">
                        <div id="theme-toggle">
                            <i class="bi bi-sun-fill fs-5" id="theme-icon"></i>
                        </div>
                    </li>
                    <?php if (Auth::isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#"
                                data-bs-toggle="dropdown">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 32px; height: 32px; font-size: 0.8rem;">
                                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                </div>
                                <span class="fw-medium d-none d-sm-inline"><?php echo $user['name']; ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2">
                                <li><a class="dropdown-item rounded-2" href="<?php echo BASEURL; ?>/perusahaan"><i
                                            class="bi bi-gear me-2"></i> Settings</a></li>
                                <?php if (Auth::isAdmin() || Auth::hasPermission('sys_user_management')): ?>
                                    <li><a class="dropdown-item rounded-2" href="<?php echo BASEURL; ?>/user"><i
                                                class="bi bi-people me-2"></i> User Management</a></li>
                                    <li><a class="dropdown-item rounded-2" href="<?php echo BASEURL; ?>/role"><i
                                                class="bi bi-shield-check me-2"></i> Role Management</a></li>
                                <?php endif; ?>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item rounded-2 text-danger"
                                        href="<?php echo BASEURL; ?>/login/logout"><i
                                            class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>

        <main class="p-4">
            <!-- Global Quick Access Bar -->
            <div class="quick-access-bar mb-4 d-none d-md-flex align-items-center gap-2 p-2 bg-white rounded-pill shadow-sm border" style="width: max-content;">
                <div class="px-3 border-end me-1">
                    <small class="fw-bold text-muted text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.05em;">Akses Cepat</small>
                </div>
                <a href="<?php echo BASEURL; ?>/kas" class="btn btn-sm btn-light rounded-pill px-3 d-flex align-items-center gap-2 border-0">
                    <i class="bi bi-bank text-primary"></i> <span class="small fw-medium">Kas</span>
                </a>
                <a href="<?php echo BASEURL; ?>/penjualan" class="btn btn-sm btn-light rounded-pill px-3 d-flex align-items-center gap-2 border-0">
                    <i class="bi bi-cart-check text-success"></i> <span class="small fw-medium">Penjualan</span>
                </a>
                <a href="<?php echo BASEURL; ?>/pembelian" class="btn btn-sm btn-light rounded-pill px-3 d-flex align-items-center gap-2 border-0">
                    <i class="bi bi-bag-plus text-danger"></i> <span class="small fw-medium">Pembelian</span>
                </a>
                <a href="<?php echo BASEURL; ?>/laporan" class="btn btn-sm btn-light rounded-pill px-3 d-flex align-items-center gap-2 border-0">
                    <i class="bi bi-pie-chart-fill text-info"></i> <span class="small fw-medium">Laporan</span>
                </a>
            </div>

            <style>
                .quick-access-bar .btn-light {
                    background-color: transparent;
                    transition: all 0.2s;
                }
                .quick-access-bar .btn-light:hover {
                    background-color: #f8f9fa;
                    transform: translateY(-1px);
                }
                .quick-access-bar .btn-light i {
                    font-size: 1rem;
                }
            </style>
            
            <?php Flash::flash(); ?>