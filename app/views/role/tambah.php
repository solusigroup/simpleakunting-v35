<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex align-items-center mb-4">
            <a href="<?php echo BASEURL; ?>/role" class="btn btn-link text-decoration-none text-muted p-0 me-3">
                <i class="bi bi-arrow-left fs-4"></i>
            </a>
            <h3 class="fw-bold mb-0">Tambah Role Baru</h3>
        </div>

        <form action="<?php echo BASEURL; ?>/role/simpan" method="post">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="role_name" class="form-label fw-bold small text-muted text-uppercase">Nama Role</label>
                            <input type="text" class="form-control bg-light border-0" id="role_name" name="role_name" placeholder="Contoh: Staff Gudang" required>
                        </div>
                        <div class="col-md-6">
                            <label for="description" class="form-label fw-bold small text-muted text-uppercase">Deskripsi</label>
                            <input type="text" class="form-control bg-light border-0" id="description" name="description" placeholder="Akses input stok dan produksi">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0">Konfigurasi Izin Akses</h5>
                </div>
                <div class="card-body p-4">
                    <?php 
                    $categories = [];
                    foreach($data['permissions'] as $p) $categories[$p['category']][] = $p;
                    ?>

                    <div class="row g-4">
                        <?php foreach($categories as $cat => $perms): ?>
                        <div class="col-md-6">
                            <div class="bg-light rounded-4 p-4 h-100 border border-opacity-10">
                                <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-tag-fill me-2"></i><?php echo $cat; ?></h6>
                                <div class="row g-2">
                                    <?php foreach($perms as $p): ?>
                                    <div class="col-12">
                                        <div class="form-check form-switch custom-switch">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="<?php echo $p['id']; ?>" id="perm_<?php echo $p['id']; ?>">
                                            <label class="form-check-label ms-2" for="perm_<?php echo $p['id']; ?>">
                                                <?php echo $p['display_name']; ?>
                                            </label>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="<?php echo BASEURL; ?>/role" class="btn btn-light rounded-pill px-4">Batal</a>
                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow">
                    <i class="bi bi-check-circle me-2"></i>Simpan Role
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .bg-light { background-color: #f8f9fa !important; }
    .custom-switch .form-check-input { width: 3em; height: 1.5em; cursor: pointer; }
    .custom-switch .form-check-label { cursor: pointer; padding-top: 0.25rem; }
    .custom-switch .form-check-input:checked { background-color: var(--primary-color); border-color: var(--primary-color); }
</style>
