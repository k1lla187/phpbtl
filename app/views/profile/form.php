<?php
$user = $data['user'] ?? [];
$error = $data['error'] ?? '';
$success = $data['success'] ?? '';
$baseUrl = defined('URLROOT') ? rtrim(URLROOT, '/') : '';
?>
<div class="page-header mb-4">
    <h4><i class="fas fa-user me-2"></i>Hồ sơ cá nhân</h4>
    <a href="javascript:history.back()" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
</div>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($success) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Ảnh đại diện đã lưu -->
<?php 
$baseUrl = defined('URLROOT') ? rtrim(URLROOT, '/') : '';
$avatarUrl = !empty($user['Avatar']) ? $baseUrl . '/' . ltrim($user['Avatar'], '/') : '';
?>
<?php if ($avatarUrl): ?>
<div class="card mb-4">
    <div class="card-header"><h5 class="mb-0"><i class="fas fa-image me-2"></i>Ảnh đại diện đã lưu</h5></div>
    <div class="card-body text-center">
        <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Ảnh đại diện" class="rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #e2e8f0;">
        <p class="text-muted mt-2 mb-0">Ảnh được lưu trong cơ sở dữ liệu</p>
    </div>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Thông tin tài khoản</h5></div>
            <div class="card-body">
                <form action="index.php?url=Profile/update" method="POST" enctype="multipart/form-data">
                    <div class="mb-3 text-center">
                        <?php $formAvatarUrl = !empty($user['Avatar']) ? $baseUrl . '/' . ltrim($user['Avatar'], '/') : ''; ?>
                        <div class="mb-2">
                            <?php if ($formAvatarUrl): ?>
                                <img src="<?= htmlspecialchars($formAvatarUrl) ?>" alt="Avatar" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center text-white" style="width: 100px; height: 100px; font-size: 2rem;"><?= strtoupper(mb_substr($user['HoTen'] ?? 'U', 0, 1, 'UTF-8')) ?></div>
                            <?php endif; ?>
                        </div>
                        <label class="form-label">Thay đổi ảnh đại diện</label>
                        <input type="file" name="avatar" class="form-control form-control-sm" accept="image/jpeg,image/png,image/gif,image/webp">
                        <small class="text-muted">JPG, PNG, GIF, WebP. Tối đa 2MB</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tên đăng nhập</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['TenDangNhap'] ?? '') ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" name="HoTen" class="form-control" value="<?= htmlspecialchars($user['HoTen'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="Email" class="form-control" value="<?= htmlspecialchars($user['Email'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="SoDienThoai" class="form-control" value="<?= htmlspecialchars($user['SoDienThoai'] ?? '') ?>">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu thay đổi</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Đổi mật khẩu</h5></div>
            <div class="card-body">
                <form action="index.php?url=Profile/changePassword" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu hiện tại</label>
                        <input type="password" name="matKhauCu" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới</label>
                        <input type="password" name="matKhauMoi" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Xác nhận mật khẩu mới</label>
                        <input type="password" name="matKhauXacNhan" class="form-control" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-key me-1"></i>Đổi mật khẩu</button>
                </form>
            </div>
        </div>
    </div>
</div>
