<?php

/**
 * Trait PeriodLockTrait
 * 
 * Trait ini menyediakan fungsionalitas pemeriksaan periode yang terkunci.
 * Digunakan untuk mencegah modifikasi transaksi pada periode akuntansi yang sudah ditutup.
 */
trait PeriodLockTrait {
    /**
     * Memeriksa apakah periode untuk tanggal tertentu sudah ditutup.
     * Jika sudah ditutup, akan menampilkan pesan error dan redirect.
     * 
     * @param string $tanggal Tanggal transaksi yang akan diperiksa
     * @param string|null $redirectUrl URL tujuan redirect (opsional)
     * @return void
     */
    protected function checkPeriodLock($tanggal, $redirectUrl = null) {
        $tutupBukuModel = $this->model('TutupBuku');
        if ($tutupBukuModel->isPeriodClosed($tanggal, $this->tenantId())) {
            Flash::setFlash('Gagal! Periode untuk tanggal transaksi ini sudah ditutup dan tidak bisa diubah atau dihapus.', 'danger');
            $redirect = $redirectUrl ?? ($_SERVER['HTTP_REFERER'] ?? BASEURL . '/dashboard');
            header('Location: ' . $redirect);
            exit;
        }
    }
}
