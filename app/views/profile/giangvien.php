<?php
$user = $data['user'] ?? [];
$error = $data['error'] ?? '';
$success = $data['success'] ?? '';
$baseUrl = defined('URLROOT') ? rtrim(URLROOT, '/') : '';
$userName = $_SESSION['user_name'] ?? 'GV';
$userRole = $_SESSION['user_role'] ?? 'Giảng viên';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ - UNISCORE Giảng Viên</title>
    <link rel="icon" type="image/svg+xml" href="<?= $baseUrl ?>/favicon.svg">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f5f6fa; }
        .layout { display: grid; grid-template-columns: 260px 1fr; min-height: 100vh; }
        .sidebar { background: #fff; border-right: 1px solid #e3e6f0; padding: 18px; }
        .sidebar__brand { display: flex; align-items: center; margin-bottom: 24px; }
        .sidebar__logo { width: 34px; height: 34px; border-radius: 6px; margin-right: 10px; }
        .sidebar__title { font-size: 18px; font-weight: 700; color: #d4af37; }
        .nav-item { display: flex; align-items: center; padding: 9px 10px; border-radius: 6px; font-size: 14px; color: #4a5568; text-decoration: none; margin-bottom: 4px; }
        .nav-item:hover { background: #edf2ff; color: #2b6cb0; }
        .nav-item--active { background: #d4af37; color: white; }
        .main { padding: 24px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="sidebar__brand">
            <img src="<?= $baseUrl ?>/favicon.svg" alt="UNISCORE" class="sidebar__logo">
            <div><div class="sidebar__title">UNISCORE</div></div>
        </div>
        <a href="<?= $baseUrl ?>/GiangVien/dashboard" class="nav-item"><i class="fas fa-home me-2"></i>Bảng điều khiển</a>
        <a href="<?= $baseUrl ?>/Profile/index" class="nav-item nav-item--active"><i class="fas fa-user me-2"></i>Hồ sơ</a>
        <a href="<?= $baseUrl ?>/Auth/logout" class="nav-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a>
    </aside>
    <main class="main">
        <div class="topbar">
            <h4><i class="fas fa-user me-2"></i>Hồ sơ cá nhân</h4>
            <a href="<?= $baseUrl ?>/GiangVien/dashboard" class="btn btn-outline-secondary">Quay lại</a>
        </div>
        <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
        <?php 
        $avatarUrl = !empty($user['Avatar']) ? (defined('URLROOT') ? rtrim(URLROOT,'/') : '') . '/' . ltrim($user['Avatar'], '/') : '';
        if ($avatarUrl): ?>
        <div class="card mb-4">
            <div class="card-header">Ảnh đại diện đã lưu</div>
            <div class="card-body text-center">
                <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Ảnh đại diện" class="rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #e2e8f0;">
                <p class="text-muted mt-2 mb-0">Ảnh được lưu trong cơ sở dữ liệu</p>
            </div>
        </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">Thông tin tài khoản</div>
                    <div class="card-body">
                        <form action="index.php?url=Profile/update" method="POST" enctype="multipart/form-data">
                            <div class="mb-3 text-center">
                                <?php $formAvatarUrl = !empty($user['Avatar']) ? (defined('URLROOT') ? rtrim(URLROOT,'/') : '') . '/' . ltrim($user['Avatar'], '/') : ''; ?>
                                <?php if ($formAvatarUrl): ?>
                                    <img src="<?= htmlspecialchars($formAvatarUrl) ?>" alt="Avatar" class="rounded-circle mb-2" style="width: 80px; height: 80px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center text-white mb-2" style="width: 80px; height: 80px; font-size: 1.5rem;"><?= strtoupper(mb_substr($user['HoTen'] ?? 'G', 0, 1, 'UTF-8')) ?></div>
                                <?php endif; ?>
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
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">Đổi mật khẩu</div>
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
    </main>
</div>
</body>
</html>
