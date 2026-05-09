"
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['judul']; ?> - SIMPLE AKUNTING</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --pasuruan-green: #065f46;
            /* Hijau Puspa Candra */
            --pasuruan-green-light: #10b981;
            --accent-cream: #fefce8;
        }

        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: var(--accent-cream);
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23065f46' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .main-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
        }

        .login-wrapper {
            display: flex;
            width: 100%;
            max-width: 1000px;
            min-height: 600px;
            background-color: #fff;
            box-shadow: 0 20px 50px rgba(6, 95, 70, 0.15);
            border-radius: 30px;
            overflow: hidden;
            margin-bottom: 2rem;
            border: 1px solid rgba(6, 95, 70, 0.1);
        }

        .login-form-side {
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        .illustration-side {
            background: linear-gradient(135deg, var(--pasuruan-green), #064e3b);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 60px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .illustration-side::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 86c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm66-3c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm-46-45c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm54 54c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM58 7c0 1.105-.895 2-2 2s-2-.895-2-2 .895-2 2-2 2 .895 2 2zM6 46c0 1.105-.895 2-2 2s-2-.895-2-2 .895-2 2-2 2 .895 2 2zm92 2c0 1.105-.895 2-2 2s-2-.895-2-2 .895-2 2-2 2 .895 2 2zM30 66c0 1.105-.895 2-2 2s-2-.895-2-2 .895-2 2-2 2 .895 2 2zm24-26c0 1.105-.895 2-2 2s-2-.895-2-2 .895-2 2-2 2 .895 2 2zM0 15c0 1.105.895 2 2 2s2-.895 2-2-.895-2-2-2-2 .895-2 2zm100 60c0 1.105-.895 2-2 2s-2-.895-2-2 .895-2 2-2 2 .895 2 2zM8 21c0 5.523 4.477 10 10 10s10-4.477 10-10-4.477-10-10-10-10 4.477-10 10zM44 65c0 4.418 3.582 8 8 8s8-3.582 8-8-3.582-8-8-8-8 3.582-8 8zm-2 20c0 2.76 2.24 5 5 5s5-2.24 5-5-2.24-5-5-5-5 2.24-5 5zM68 58c0 2.21 1.79 4 4 4s4-1.79 4-4-1.79-4-4-4-4 1.79-4 4zM31 13c0 2.21 1.79 4 4 4s4-1.79 4-4-1.79-4-4-4-4 1.79-4 4zm40 5c0 4.97 4.03 9 9 9s9-4.03 9-9-4.03-9-9-9-9 4.03-9 9zM20 75c0 4.97 4.03 9 9 9s9-4.03 9-9-4.03-9-9-9-9 4.03-9 9z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
            opacity: 0.3;
        }

        .illustration-side img {
            width: 90%;
            max-width: 350px;
            height: auto;
            border-radius: 25px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            background: white;
            padding: 10px;
            position: relative;
            z-index: 1;
        }

        .form-control {
            border-radius: 12px;
            padding: 14px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
            border-color: var(--pasuruan-green-light);
            background-color: #fff;
        }

        .btn-primary {
            background-color: var(--pasuruan-green);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: #064e3b;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(6, 95, 70, 0.2);
        }

        .nav-pills .nav-link.active {
            background-color: var(--pasuruan-green);
        }

        .nav-link {
            color: var(--pasuruan-green);
            font-weight: 600;
        }

        .login-footer {
            color: #64748b;
        }

        .login-footer a {
            color: var(--pasuruan-green);
        }

        .badge.bg-pasuruan {
            background-color: rgba(6, 95, 70, 0.1);
            color: var(--pasuruan-green);
            border: 1px solid rgba(6, 95, 70, 0.2);
        }

        @media (max-width: 991px) {
            .illustration-side {
                display: none;
            }

            .login-form-side {
                padding: 40px;
            }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <div class="login-wrapper">
            <!-- Sisi Ilustrasi -->
            <div class="col-lg-6 illustration-side">
                <div style="position: relative; z-index: 2;">
                    <img src="<?php echo BASEURL; ?>/img/pasuruan_sedap_malam.png" alt="Sedap Malam Pasuruan"
                        class="img-fluid mb-4 shadow-lg">
                    <h2 class="fw-bold mt-4">Puspa Candra </h2>
                    <p class="mt-2 opacity-90">Sistem Akuntansi Terintegrasi dengan kearifan lokal Kabupaten Pasuruan.
                        Mewujudkan tata kelola keuangan yang subur dan transparan.</p>
                </div>
            </div>
            <!-- Sisi Form Login -->
            <div class="col-12 col-lg-6 login-form-side">
                <div class="text-center mb-5">
                    <img src="<?php echo BASEURL; ?>/img/logo_pasuruan.png" alt="Logo Kabupaten Pasuruan"
                        class="mx-auto mb-3" style="max-height: 120px;">
                    <h3 class="fw-bold mb-1" style="color: var(--pasuruan-green);">SIMPLE AKUNTING</h3>
                    <div class="badge bg-pasuruan rounded-pill px-3 py-2 mt-2">
                        <i class="bi bi-flower1 me-1"></i>
                    </div>
                </div>
                <h3 class="text-center fw-bold mb-1">Selamat Datang</h3>
                <p class="text-center text-muted mb-4">Pilih jenis akses Anda untuk melanjutkan.</p>

                <?php Flash::flash(); ?>

                <ul class="nav nav-pills nav-fill mb-4 p-1 bg-light rounded-pill" id="loginTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill px-4" id="tenant-tab" data-bs-toggle="pill"
                            data-bs-target="#tenant" type="button" role="tab">
                            <i class="bi bi-shop me-2"></i>Tenant
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-4" id="central-tab" data-bs-toggle="pill"
                            data-bs-target="#central" type="button" role="tab">
                            <i class="bi bi-shield-lock me-2"></i>Central
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="loginTabContent">
                    <!-- Tenant Login Form -->
                    <div class="tab-pane fade show active" id="tenant" role="tabpanel">
                        <form action="<?php echo BASEURL; ?>/login/process" method="post">
                            <input type="hidden" name="login_type" value="tenant">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Kode Bisnis / Tenant Code</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="bi bi-buildings"></i></span>
                                    <input type="text" class="form-control border-start-0" name="tenant_code"
                                        placeholder="Contoh: MAJUJAYA" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Pengguna</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="bi bi-person"></i></span>
                                    <input type="text" class="form-control border-start-0" name="nama_user"
                                        placeholder="Username" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Sandi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="bi bi-key"></i></span>
                                    <input type="password" class="form-control border-start-0" name="password"
                                        placeholder="******" required>
                                </div>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary shadow-sm">Masuk ke Dashboard
                                    Bisnis</button>
                            </div>
                        </form>
                    </div>

                    <!-- Central Login Form -->
                    <div class="tab-pane fade" id="central" role="tabpanel">
                        <form action="<?php echo BASEURL; ?>/login/process" method="post">
                            <input type="hidden" name="login_type" value="central">
                            <div class="mb-3 text-center">
                                <div class="badge bg-danger-subtle text-danger p-2 px-3 rounded-pill mb-3">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Area Khusus Superadmin
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Pengguna Central</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="bi bi-shield-shaded"></i></span>
                                    <input type="text" class="form-control border-start-0" name="nama_user"
                                        placeholder="Username Admin" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Sandi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="bi bi-key"></i></span>
                                    <input type="password" class="form-control border-start-0" name="password"
                                        placeholder="******" required>
                                </div>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-dark shadow-sm">Masuk Central Monitoring</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="login-footer text-center mt-4">
            <p class="mb-1">&copy; 2025 - SIMPLE AKUNTING created by <a
                    href="https://simpleakunting.my.id/riwayathidupku.html#home" target="_blank"
                    class="text-decoration-none">Kurniawan @Simple Akunting</a></p>
            <p class="mb-0">&copy; <?php echo date('Y'); ?> - supported by <a
                    href="https://www.instagram.com/inbisfunvitaindonesia/" target="_blank"
                    class="text-decoration-none">PT Funvita Indonesia Investama</a></p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>