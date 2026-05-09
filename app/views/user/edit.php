<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center mb-4">
            <a href="<?php echo BASEURL; ?>/user" class="btn btn-link text-decoration-none text-muted p-0 me-3">
                <i class="bi bi-arrow-left fs-4"></i>
            </a>
            <h3 class="fw-bold mb-0">Edit Data Pengguna</h3>
        </div>

        <form action="<?php echo BASEURL; ?>/user/update" method="post">
            <input type="hidden" name="id_user" value="<?php echo $data['user']['id_user']; ?>">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="nama_user" class="form-label fw-bold small text-muted text-uppercase">Username</label>
                            <input type="text" class="form-control bg-light border-0" id="nama_user" name="nama_user" value="<?php echo htmlspecialchars($data['user']['nama_user']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="nama_lengkap" class="form-label fw-bold small text-muted text-uppercase">Nama untuk Tanda Tangan</label>
                            <input type="text" class="form-control bg-light border-0" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($data['user']['nama_lengkap'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-bold small text-muted text-uppercase">Ganti Password (Kosongkan jika tidak diganti)</label>
                            <input type="password" class="form-control bg-light border-0" id="password" name="password">
                        </div>
                        <div class="col-md-6">
                            <label for="jabatan" class="form-label fw-bold small text-muted text-uppercase">Jabatan</label>
                            <input type="text" class="form-control bg-light border-0" id="jabatan" name="jabatan" value="<?php echo htmlspecialchars($data['user']['jabatan']); ?>" required>
                        </div>
                        <div class="col-md-12">
                            <hr class="my-2">
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label fw-bold small text-muted text-uppercase">Legacy Role</label>
                            <select class="form-select bg-light border-0" id="role" name="role">
                                <option value="Staff" <?php echo ($data['user']['role'] == 'Staff') ? 'selected' : ''; ?>>Staff</option>
                                <option value="Manager" <?php echo ($data['user']['role'] == 'Manager') ? 'selected' : ''; ?>>Manager</option>
                                <option value="Admin" <?php echo ($data['user']['role'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                                <option value="Superadmin" <?php echo ($data['user']['role'] == 'Superadmin') ? 'selected' : ''; ?>>Superadmin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="role_id" class="form-label fw-bold small text-muted text-uppercase">Custom Role (RBAC)</label>
                            <select class="form-select bg-light border-0 border-primary" id="role_id" name="role_id">
                                <option value="">-- Gunakan Legacy Role --</option>
                                <?php foreach($data['roles'] as $r): ?>
                                    <option value="<?php echo $r['id']; ?>" <?php echo ($data['user']['role_id'] == $r['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($r['role_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="<?php echo BASEURL; ?>/user" class="btn btn-light rounded-pill px-4">Batal</a>
                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow">
                    <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
