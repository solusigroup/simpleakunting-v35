<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center mb-4">
            <a href="<?php echo BASEURL; ?>/user" class="btn btn-link text-decoration-none text-muted p-0 me-3">
                <i class="bi bi-arrow-left fs-4"></i>
            </a>
            <h3 class="fw-bold mb-0">Tambah Pengguna Baru</h3>
        </div>

        <form action="<?php echo BASEURL; ?>/user/simpan" method="post">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="nama_user" class="form-label fw-bold small text-muted text-uppercase">Username</label>
                            <input type="text" class="form-control bg-light border-0" id="nama_user" name="nama_user" placeholder="digunakan untuk login" required>
                        </div>
                        <div class="col-md-6">
                            <label for="nama_lengkap" class="form-label fw-bold small text-muted text-uppercase">Nama untuk Tanda Tangan</label>
                            <input type="text" class="form-control bg-light border-0" id="nama_lengkap" name="nama_lengkap" placeholder="Contoh: Budi Santoso, S.E." required>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-bold small text-muted text-uppercase">Password</label>
                            <input type="password" class="form-control bg-light border-0" id="password" name="password" required>
                        </div>
                        <div class="col-md-6">
                            <label for="jabatan" class="form-label fw-bold small text-muted text-uppercase">Jabatan</label>
                            <input type="text" class="form-control bg-light border-0" id="jabatan" name="jabatan" placeholder="Contoh: Manager Keuangan" required>
                        </div>
                        <div class="col-md-12">
                            <hr class="my-2">
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label fw-bold small text-muted text-uppercase">Legacy Role (Optional)</label>
                            <select class="form-select bg-light border-0" id="role" name="role">
                                <option value="Staff">Staff</option>
                                <option value="Manager">Manager</option>
                                <option value="Admin">Admin</option>
                            </select>
                            <div class="form-text text-muted small">Role dasar sistem.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="role_id" class="form-label fw-bold small text-muted text-uppercase">Custom Role (RBAC)</label>
                            <select class="form-select bg-light border-0 border-primary" id="role_id" name="role_id">
                                <option value="">-- Gunakan Legacy Role --</option>
                                <?php foreach($data['roles'] as $r): ?>
                                    <option value="<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['role_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text text-primary fw-bold small">Rekomendasi: Gunakan role kustom untuk izin yang lebih presisi.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="<?php echo BASEURL; ?>/user" class="btn btn-light rounded-pill px-4">Batal</a>
                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow">
                    <i class="bi bi-person-check me-2"></i>Simpan User
                </button>
            </div>
        </form>
    </div>
</div>
