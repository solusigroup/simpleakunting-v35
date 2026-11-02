<?php

class Kurasi extends Controller {

    public function index()
    {
        $kategori = $_GET['kategori'] ?? 'semua';
        $pencarian = trim($_GET['q'] ?? '');

        $allUmkm = $this->getCuratedList();

        if ($kategori !== 'semua') {
            $allUmkm = array_filter($allUmkm, function($item) use ($kategori) {
                return strtolower($item['kategori_key']) === strtolower($kategori);
            });
        }

        if (!empty($pencarian)) {
            $allUmkm = array_filter($allUmkm, function($item) use ($pencarian) {
                return (stripos($item['nama'], $pencarian) !== false) ||
                       (stripos($item['kluster'], $pencarian) !== false) ||
                       (stripos($item['sektor'], $pencarian) !== false);
            });
        }

        $data = [
            'judul' => 'Kurasi UMKM Layak Pembiayaan - SimpleAkunting v3.6',
            'is_logged_in' => Auth::isLoggedIn(),
            'user' => Auth::user(),
            'umkm_list' => array_values($allUmkm),
            'selected_kategori' => $kategori,
            'pencarian' => $pencarian,
            'summary_metrics' => [
                'total_terkurasi' => 148,
                'grade_a_bankable' => 86,
                'total_pembiayaan' => 'Rp 14.2 Miliar',
                'tingkat_kelayakan' => '94.6%'
            ]
        ];

        $this->view('kurasi/index', $data);
    }

    public function ajukan()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = htmlspecialchars($_POST['nama_usaha'] ?? '');
            $pemilik = htmlspecialchars($_POST['nama_pemilik'] ?? '');
            $telepon = htmlspecialchars($_POST['telepon'] ?? '');
            $kota = htmlspecialchars($_POST['kota'] ?? '');
            $sektor = htmlspecialchars($_POST['sektor'] ?? '');
            $omset = (float)($_POST['omset'] ?? 0);

            Flash::setFlash("Terima kasih, pengajuan kurasi untuk \"$nama\" berhasil dikirim. Tim kurator SimpleAkunting v3.6 & dinas terkait akan menghubungi $telepon dalam 2-3 hari kerja.", 'success');
            header('Location: ' . BASEURL . '/kurasi#daftar-kurasi');
            exit;
        }

        $data = [
            'judul' => 'Ajukan Kurasi UMKM - SimpleAkunting v3.6',
            'is_logged_in' => Auth::isLoggedIn(),
            'user' => Auth::user()
        ];
        $this->view('kurasi/ajukan', $data);
    }

    public function simulasi()
    {
        header('Content-Type: application/json');

        $omset = (float)($_POST['omset'] ?? 0);
        $laba = (float)($_POST['laba'] ?? 0);
        $aset = (float)($_POST['aset'] ?? 0);
        $usiaUsahaTahun = (int)($_POST['usia_usaha'] ?? 1);
        $metodePencatatan = $_POST['metode_pencatatan'] ?? 'manual';
        $statusHutang = $_POST['status_hutang'] ?? 'lancar';

        // Hitung skor kelayakan (0 - 100)
        $skor = 40; // baseline

        // Rasio laba bersih (NPM)
        $npm = ($omset > 0) ? ($laba / $omset) : 0;
        if ($npm >= 0.20) $skor += 20;
        elseif ($npm >= 0.10) $skor += 15;
        elseif ($npm > 0) $skor += 8;

        // Lama usaha berjalan
        if ($usiaUsahaTahun >= 3) $skor += 15;
        elseif ($usiaUsahaTahun >= 1) $skor += 10;
        else $skor += 5;

        // Metode pencatatan akuntansi
        if ($metodePencatatan === 'simpleakunting' || $metodePencatatan === 'software') $skor += 20;
        elseif ($metodePencatatan === 'excel') $skor += 12;
        else $skor += 5;

        // Status hutang / riwayat kredit
        if ($statusHutang === 'lancar' || $statusHutang === 'tidak_ada') $skor += 10;
        else $skor -= 15;

        $skor = max(30, min(100, $skor));

        $grade = 'C';
        $status = 'Perlu Pendampingan Akuntansi';
        $rekomendasi = 'Gunakan SimpleAkunting v3.6 untuk merapikan pembukuan SAK EP selama minimal 3 bulan sebelum pengajuan pembiayaan.';

        if ($skor >= 85) {
            $grade = 'A';
            $status = 'Sangat Layak (Bankable & Siap Investasi)';
            $rekomendasi = 'Memenuhi kriteria KUR Komersial Bank Jatim / BRI hingga Rp 500 Juta, pembiayaan syariah, serta kemitraan CSR BUMN/BUMD.';
        } elseif ($skor >= 70) {
            $grade = 'B+';
            $status = 'Layak Pembiayaan Mikro & BUMDes';
            $rekomendasi = 'Layak untuk KUR Mikro s/d Rp 100 Juta, pembiayaan bergulir BUMDesa, atau program inkubasi dinas koperasi.';
        } elseif ($skor >= 55) {
            $grade = 'B';
            $status = 'Cukup Layak / Tahap Kurasi Awal';
            $rekomendasi = 'Direkomendasikan mengakses KUR Super Mikro s/d Rp 10 Juta dan pelatihan standardisasi pembukuan SAK EP.';
        }

        echo json_encode([
            'status' => 'success',
            'skor' => $skor,
            'grade' => $grade,
            'kelayakan' => $status,
            'rekomendasi' => $rekomendasi,
            'rasio_npm' => round($npm * 100, 1) . '%'
        ]);
        exit;
    }

    private function getCuratedList()
    {
        return [
            [
                'id' => 1,
                'nama' => 'Kopi Bromo Arabika Mandiri',
                'pemilik' => 'Ahmad Fauzi & Kelompok Tani',
                'kluster' => 'Kabupaten Pasuruan',
                'kategori_key' => 'agribisnis',
                'sektor' => 'Agribisnis & Pengolahan',
                'grade' => 'A',
                'skor' => 95,
                'status' => 'Bankable / Siap Investasi',
                'omset_bulan' => 'Rp 45.000.000',
                'pertumbuhan' => '+28% YoY',
                'kepatuhan' => 'SAK EP Terverifikasi',
                'rekomendasi_dana' => 'KUR Komersial Rp 250 Juta',
                'deskripsi' => 'Pengolahan biji kopi lereng Gunung Bromo dengan kemasan ramah lingkungan dan distribusi ke 35 kedai kopi Jawa Timur.'
            ],
            [
                'id' => 2,
                'nama' => 'Batik Sedap Malam Pasuruan',
                'pemilik' => 'Ibu Sri Wahyuni',
                'kluster' => 'Kabupaten Pasuruan',
                'kategori_key' => 'kriya',
                'sektor' => 'Kriya & Tekstil Kreatif',
                'grade' => 'A',
                'skor' => 93,
                'status' => 'Bankable / Siap Ekspor',
                'omset_bulan' => 'Rp 38.500.000',
                'pertumbuhan' => '+22% YoY',
                'kepatuhan' => 'SAK EP Terverifikasi',
                'rekomendasi_dana' => 'Pembiayaan Ekspor & Pameran',
                'deskripsi' => 'Batik cap & tulis dengan motif khas bunga sedap malam Pasuruan, pewarna alami, dan pangsa pasar nasional.'
            ],
            [
                'id' => 3,
                'nama' => 'BUMDes Makmur Sejahtera',
                'pemilik' => 'Pemerintah Desa Sumber Rejo',
                'kluster' => 'Jawa Timur',
                'kategori_key' => 'bumdes',
                'sektor' => 'BUMDesa Multi-Unit',
                'grade' => 'A',
                'skor' => 97,
                'status' => 'Pengelolaan Dana Desa & CSR Transparan',
                'omset_bulan' => 'Rp 112.000.000',
                'pertumbuhan' => '+35% YoY',
                'kepatuhan' => 'SAK EP Terverifikasi',
                'rekomendasi_dana' => 'Penyertaan Modal Desa & CSR BUMN',
                'deskripsi' => 'Pengelolaan unit simpan pinjam, perdagangan sarana produksi pertanian, dan wisata desa terintegrasi.'
            ],
            [
                'id' => 4,
                'nama' => 'Sentra Olahan Susu Murni Nongkojajar',
                'pemilik' => 'Koperasi Peternak Mandiri',
                'kluster' => 'Kabupaten Pasuruan',
                'kategori_key' => 'agribisnis',
                'sektor' => 'Peternakan & Olahan Susu',
                'grade' => 'A',
                'skor' => 94,
                'status' => 'Bankable / KUR Komersial',
                'omset_bulan' => 'Rp 82.000.000',
                'pertumbuhan' => '+19% YoY',
                'kepatuhan' => 'SAK EP Terverifikasi',
                'rekomendasi_dana' => 'Kredit Investasi Mesin Pasteur',
                'deskripsi' => 'Pemasok susu sapi perah pasteurisasi, yoghurt kemasan, dan keju mozzarella lokal berkualifikasi industri.'
            ],
            [
                'id' => 5,
                'nama' => 'Dapur Sambal Nusantara Barokah',
                'pemilik' => 'Ibu Rahmawati',
                'kluster' => 'Surabaya & Sidoarjo',
                'kategori_key' => 'kuliner',
                'sektor' => 'Kuliner & Mamin',
                'grade' => 'B+',
                'skor' => 88,
                'status' => 'Layak KUR Mikro',
                'omset_bulan' => 'Rp 27.000.000',
                'pertumbuhan' => '+15% YoY',
                'kepatuhan' => 'SAK EP Sederhana',
                'rekomendasi_dana' => 'KUR Mikro Rp 50 Juta',
                'deskripsi' => 'Sambal kemasan botol aneka varian tradisional nusantara tanpa pengawet berbahaya dengan izin P-IRT dan Halal.'
            ],
            [
                'id' => 6,
                'nama' => 'Bambu Lestari & Kerajinan Alam',
                'pemilik' => 'Bapak Sudarmono',
                'kluster' => 'Bojonegoro & Tuban',
                'kategori_key' => 'kriya',
                'sektor' => 'Manufaktur Ringan & Kriya',
                'grade' => 'B+',
                'skor' => 86,
                'status' => 'Inkubasi BUMDes & CSR',
                'omset_bulan' => 'Rp 21.500.000',
                'pertumbuhan' => '+12% YoY',
                'kepatuhan' => 'SAK EP Sederhana',
                'rekomendasi_dana' => 'Bantuan Hibah CSR Mesin Serut',
                'deskripsi' => 'Peralatan makan, sedotan bambu, dan dekorasi interior berbahan bambu lokal dengan orientasi pasar ramah lingkungan.'
            ],
            [
                'id' => 7,
                'nama' => 'Gula Semut Aren Organik Prigen',
                'pemilik' => 'Pak Joko & Paguyuban Sadap',
                'kluster' => 'Kabupaten Pasuruan',
                'kategori_key' => 'agribisnis',
                'sektor' => 'Agroindustri Pangan',
                'grade' => 'A',
                'skor' => 91,
                'status' => 'Bankable / Siap Kerjasama Hotel',
                'omset_bulan' => 'Rp 34.000.000',
                'pertumbuhan' => '+25% YoY',
                'kepatuhan' => 'SAK EP Terverifikasi',
                'rekomendasi_dana' => 'Pembiayaan Modal Kerja Rp 100 Juta',
                'deskripsi' => 'Pengolahan nira aren murni lereng Gunung Arjuno menjadi gula kristal organik untuk konsumsi sehat dan Horeka.'
            ],
            [
                'id' => 8,
                'nama' => 'Bengkel Logam & Alat Pertanian Tani Jaya',
                'pemilik' => 'Mas Hendro Karyadi',
                'kluster' => 'Malang Raya',
                'kategori_key' => 'jasa',
                'sektor' => 'Jasa Bengkel & Rekayasa',
                'grade' => 'B+',
                'skor' => 85,
                'status' => 'Layak KUR Mikro',
                'omset_bulan' => 'Rp 29.000.000',
                'pertumbuhan' => '+14% YoY',
                'kepatuhan' => 'SAK EP Sederhana',
                'rekomendasi_dana' => 'Kredit Usaha Rakyat Rp 75 Juta',
                'deskripsi' => 'Pembuatan sabit khusus panen, cangkul baja, dan jasa servis traktor tangan desa dengan kepuasan pelanggan tinggi.'
            ]
        ];
    }
}
