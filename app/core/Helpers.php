<?php

/**
 * Helper Terbilang Bahasa Indonesia
 */
function terbilang($angka) {
    $angka = (int)abs($angka);
    $baca = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
    $terbilang = '';

    if ($angka < 12) {
        $terbilang = ' ' . $baca[$angka];
    } elseif ($angka < 20) {
        $terbilang = terbilang($angka - 10) . ' Belas';
    } elseif ($angka < 100) {
        $terbilang = terbilang(intdiv($angka, 10)) . ' Puluh ' . terbilang($angka % 10);
    } elseif ($angka < 200) {
        $terbilang = ' Seratus ' . terbilang($angka - 100);
    } elseif ($angka < 1000) {
        $terbilang = terbilang(intdiv($angka, 100)) . ' Ratus ' . terbilang($angka % 100);
    } elseif ($angka < 2000) {
        $terbilang = ' Seribu ' . terbilang($angka - 1000);
    } elseif ($angka < 1000000) {
        $terbilang = terbilang(intdiv($angka, 1000)) . ' Ribu ' . terbilang($angka % 1000);
    } elseif ($angka < 1000000000) {
        $terbilang = terbilang(intdiv($angka, 1000000)) . ' Juta ' . terbilang($angka % 1000000);
    } elseif ($angka < 1000000000000) {
        $terbilang = terbilang(intdiv($angka, 1000000000)) . ' Miliar ' . terbilang($angka % 1000000000);
    } elseif ($angka < 1000000000000000) {
        $terbilang = terbilang(intdiv($angka, 1000000000000)) . ' Triliun ' . terbilang($angka % 1000000000000);
    }

    return preg_replace('/\s+/', ' ', trim($terbilang));
}

function terbilang_rupiah($angka) {
    $num = (float)$angka;
    if ($num == 0) return 'Nol Rupiah';
    $prefix = ($num < 0) ? 'Minus ' : '';
    return $prefix . trim(terbilang(abs($num))) . ' Rupiah';
}

/**
 * Format Tanggal Indonesia
 */
function tanggal_indo($tanggal, $cetak_hari = false) {
    if (empty($tanggal) || $tanggal === '0000-00-00') return '-';
    $hari = [
        1 => 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'
    ];
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $timestamp = strtotime($tanggal);
    if (!$timestamp) return $tanggal;
    
    $tgl = date('d', $timestamp);
    $bln = $bulan[(int)date('m', $timestamp)];
    $thn = date('Y', $timestamp);
    
    $hasil = "$tgl $bln $thn";
    if ($cetak_hari) {
        $nama_hari = $hari[(int)date('N', $timestamp)];
        $hasil = "$nama_hari, $hasil";
    }
    return $hasil;
}

/**
 * Format Rupiah Standar
 */
function format_rupiah($angka, $desimal = 0) {
    return 'Rp ' . number_format((float)$angka, $desimal, ',', '.');
}

/**
 * Format Angka Gaya Indonesia
 * - Pemisah ribuan: titik (.)
 * - Pemisah desimal: koma (,)
 * - Menghilangkan angka nol yang tidak dibutuhkan di belakang koma (contoh: 3 -> "3", 1000 -> "1.000", 3.5 -> "3,5", 1250.75 -> "1.250,75")
 */
function format_angka($angka, $max_desimal = 4) {
    if ($angka === null || $angka === '') return '0';
    $num = round((float)$angka, $max_desimal);
    if (round($num) == $num) {
        return number_format($num, 0, ',', '.');
    }
    $formatted = number_format($num, $max_desimal, ',', '.');
    return rtrim(rtrim($formatted, '0'), ',');
}

/**
 * Format Kuantitas + Satuan Gaya Indonesia
 * Contoh: format_kuantitas(3, 'Ekor') -> "3 Ekor"
 * Contoh: format_kuantitas(1250.5, 'Kg') -> "1.250,5 Kg"
 */
function format_kuantitas($angka, $satuan = '') {
    $hasil = format_angka($angka);
    if (!empty($satuan)) {
        $hasil .= ' ' . trim($satuan);
    }
    return $hasil;
}

