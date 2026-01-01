"<!DOCTYPE html>
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
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
        }
        .main-container {
            display: flex;
            flex-direction: column; /* Mengubah arah flex */
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 1rem;
        }
        .login-wrapper {
            display: flex;
            width: 100%;
            max-width: 900px;
            min-height: 550px;
            background-color: #fff;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 2rem; /* Memberi jarak dari footer */
        }
        .login-form-side {
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .illustration-side {
            background: linear-gradient(135deg, #1b4e78, #2a6a9c);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 40px;
            text-align: center;
        }
        .illustration-side img {
            width: 80%;
            max-width: 300px;
            height: auto;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            background: white;
            padding: 15px;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #ddd;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            border-color: #86b7fe;
        }
        .btn-primary {
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
        }
        .brand-icon {
            font-size: 2rem;
            color: #0d6efd;
        }
        .login-footer {
            width: 100%;
            text-align: center;
            padding: 1rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
        .login-footer a {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 600;
        }
        @media (max-width: 768px) {
            .illustration-side {
                display: none;
            }
            .login-wrapper {
                min-height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="login-wrapper">
            <!-- Sisi Ilustrasi -->
            <div class="col-lg-6 illustration-side d-none d-lg-flex">
                <div>
                    <img src="<?php echo BASEURL; ?>/img/login_illustration.jpg" alt="Illustration" class="img-fluid mb-4">
                    <h2 class="fw-bold mt-4">Kelola Keuangan Anda</h2>
                    <p class="mt-2">SIMPLE AKUNTING membantu Anda mencatat setiap transaksi dengan mudah dan menghasilkan laporan yang akurat sesuai kaidah.</p>
                </div>
            </div>
            <!-- Sisi Form Login -->
            <div class="col-12 col-lg-6 login-form-side">
                <div class="text-center mb-4">
                   
                    <img src="<?php echo BASEURL; ?>/img/logo_klinik.png" alt="Logo PPNI" class="mx-auto" style="max-height: 80px;">
                </div>
                <h3 class="text-center fw-bold mb-1">Selamat Datang</h3>
                <p class="text-center text-muted mb-4">Silakan masuk untuk melanjutkan.</p>
                
                <?php Flash::flash(); ?>

                <form action="<?php echo BASEURL; ?>/login/process" method="post">
                    <div class="mb-3">
                        <label for="nama_user" class="form-label">Nama Pengguna</label>
                        <input type="text" class="form-control" id="nama_user" name="nama_user" placeholder="Contoh: Administrator" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Sandi</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="******" required>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- **PERUBAHAN: Footer ditambahkan di sini** -->
        <footer class="login-footer">
            &copy; <?php echo date('Y'); ?> - SIMPLE AKUNTING created by <a href="https://solusiconsulting.simpleakunting.biz.id" target="_blank">Kurniawan @Simple Akunting</a>
        </footer>
        <footer class="login-footer">
            &copy; <?php echo date('Y'); ?> - supported by <a href="https://simpleakunting.biz.id/Kurniawan_Profile_Infographic.html" target="_blank">PT Mentari Commsindo Jaya </a>
        </footer>
    </div>
</body>
</html>

