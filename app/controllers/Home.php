<?php

class Home extends Controller {

    public function index()
    {
        $tenantCount = 120;
        try {
            $tenantModel = $this->model('Tenants');
            if ($tenantModel && method_exists($tenantModel, 'getAllTenants')) {
                $tenants = $tenantModel->getAllTenants();
                if (!empty($tenants)) {
                    $tenantCount = count($tenants);
                }
            }
        } catch (Throwable $e) {}

        $data = [
            'judul' => 'SimpleAkunting v3.6 - Sistem Akuntansi & Kurasi UMKM Layak',
            'is_logged_in' => Auth::isLoggedIn(),
            'user' => Auth::user(),
            'tenant_count' => $tenantCount,
            'curated_umkm' => $this->getSampleCuratedUmkm()
        ];

        $this->view('home/index', $data);
    }

    private function getSampleCuratedUmkm()
    {
        return [
            [
                'id' => 1,
                'nama' => 'Kopi Bromo Arabika Mandiri',
                'pemilik' => 'Ahmad Fauzi & Kelompok Tani',
                'kluster' => 'Kabupaten Pasuruan',
                'sektor' => 'Agribisnis & Pengolahan',
                'grade' => 'A',
                'skor' => 95,
                'status' => 'Bankable / Siap Investasi',
                'omset_bulan' => 'Rp 45.000.000',
                'pertumbuhan' => '+28% YoY',
                'kepatuhan' => 'SAK EP Terverifikasi',
                'badge' => 'Unggulan Bromo'
            ],
            [
                'id' => 2,
                'nama' => 'Batik Sedap Malam Pasuruan',
                'pemilik' => 'Ibu Sri Wahyuni',
                'kluster' => 'Kabupaten Pasuruan',
                'sektor' => 'Kriya & Tekstil Kreatif',
                'grade' => 'A',
                'skor' => 93,
                'status' => 'Bankable / Siap Ekspor',
                'omset_bulan' => 'Rp 38.500.000',
                'pertumbuhan' => '+22% YoY',
                'kepatuhan' => 'SAK EP Terverifikasi',
                'badge' => 'Cagar Budaya'
            ],
            [
                'id' => 3,
                'nama' => 'BUMDes Makmur Sejahtera',
                'pemilik' => 'Pemerintah Desa Sumber Rejo',
                'kluster' => 'Jawa Timur',
                'sektor' => 'BUMDesa Multi-Unit',
                'grade' => 'A',
                'skor' => 97,
                'status' => 'Mitra Bantuan CSR & Pemda',
                'omset_bulan' => 'Rp 112.000.000',
                'pertumbuhan' => '+35% YoY',
                'kepatuhan' => 'SAK EP Terverifikasi',
                'badge' => 'BUMDes Mandiri'
            ],
            [
                'id' => 4,
                'nama' => 'Sentra Olahan Susu Murni Nongkojajar',
                'pemilik' => 'Koperasi Peternak Mandiri',
                'kluster' => 'Kabupaten Pasuruan',
                'sektor' => 'Peternakan & Olahan Susu',
                'grade' => 'A',
                'skor' => 94,
                'status' => 'Bankable / KUR Komersial',
                'omset_bulan' => 'Rp 82.000.000',
                'pertumbuhan' => '+19% YoY',
                'kepatuhan' => 'SAK EP Terverifikasi',
                'badge' => 'Mitra Industri'
            ],
            [
                'id' => 5,
                'nama' => 'Dapur Sambal Nusantara Barokah',
                'pemilik' => 'Ibu Rahmawati',
                'kluster' => 'Surabaya & Sidoarjo',
                'sektor' => 'Kuliner & Mamin',
                'grade' => 'B+',
                'skor' => 88,
                'status' => 'Layak KUR Mikro',
                'omset_bulan' => 'Rp 27.000.000',
                'pertumbuhan' => '+15% YoY',
                'kepatuhan' => 'SAK EP Sederhana',
                'badge' => 'Retail Champion'
            ],
            [
                'id' => 6,
                'nama' => 'Bambu Lestari & Kerajinan Alam',
                'pemilik' => 'Bapak Sudarmono',
                'kluster' => 'Bojonegoro & Tuban',
                'sektor' => 'Manufaktur Ringan',
                'grade' => 'B+',
                'skor' => 86,
                'status' => 'Inkubasi BUMDes & CSR',
                'omset_bulan' => 'Rp 21.500.000',
                'pertumbuhan' => '+12% YoY',
                'kepatuhan' => 'SAK EP Sederhana',
                'badge' => 'Eco-Friendly'
            ]
        ];
    }
}
