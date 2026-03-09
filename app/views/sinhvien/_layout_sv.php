<?php
$svTen = $sinhVien['HoTen'] ?? 'Sinh viên';
$svMa = $sinhVien['MaSinhVien'] ?? '';
$baseUrl = defined('URLROOT') ? URLROOT : '';
$pageActive = $pageActive ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'UNISCORE - Cổng Sinh Viên' ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= rtrim($baseUrl ?? '', '/') ?>/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= (defined('URLROOT') ? rtrim(URLROOT, '/') : '') . '/css/sinhvien.css'; ?>" rel="stylesheet">
    <script>window.APP_BASE_URL = '<?= defined("URLROOT") ? rtrim(URLROOT, "/") : "" ?>';</script>
    <script>(function(){var t=localStorage.getItem('app-theme');if(t==='dark')document.documentElement.classList.add('dark-theme');})();</script>
</head>
<body>
<aside class="sidebar">
    <div class="sidebar-brand">
        <img src="<?= rtrim($baseUrl ?? '', '/') ?>/favicon.svg" alt="UNISCORE" class="logo" style="width: 44px; height: 44px;">
        <div>
            <div class="brand-text" style="color: #d4af37;">UNISCORE</div>
            <div class="brand-subtitle">Cổng Sinh Viên</div>
        </div>
    </div>
    <div class="sidebar-section">Tổng quan</div>
    <ul class="sidebar-menu">
        <li><a href="<?= $baseUrl ?>/SinhVien/dashboard" class="<?= $pageActive === 'dashboard' ? 'active' : '' ?>"><i class="fas fa-home"></i> Bảng điều khiển</a></li>
    </ul>
    <div class="sidebar-section">Cá nhân</div>
    
    <ul class="sidebar-menu">
        <li><a href="<?= $baseUrl ?>/SinhVien/thongTinCaNhan" class="<?= $pageActive === 'thongtincanhan' ? 'active' : '' ?>"><i class="fas fa-user"></i> Thông tin cá nhân</a></li>
        <li><a href="<?= $baseUrl ?>/SinhVien/xemDiem" class="<?= $pageActive === 'xemdiem' ? 'active' : '' ?>"><i class="fas fa-clipboard-list"></i> Xem điểm</a></li>
        <li><a href="<?= $baseUrl ?>/SinhVien/thongKe" class="<?= $pageActive === 'thongke' ? 'active' : '' ?>"><i class="fas fa-chart-pie" ></i> Thống kê cá nhân</a></li>
        <li><a href="<?= $baseUrl ?>/SinhVien/lichHoc" class="<?= $pageActive === 'lichhoc' ? 'active' : '' ?>"><i class="fas fa-calendar-alt"></i> Thời khóa biểu</a></li>
        <li><a href="<?= $baseUrl ?>/SinhVien/monHoc" class="<?= $pageActive === 'monhoc' ? 'active' : '' ?>"><i class="fas fa-book"></i> Môn đang/chưa học</a></li>
    </ul>
    <div class="sidebar-section">Đăng ký học phần</div>
    <ul class="sidebar-menu">
        <li><a href="<?= $baseUrl ?>/SinhVien/dangKyHoc" class="<?= $pageActive === 'dangkylophoc' ? 'active' : '' ?>"><i class="fas fa-plus-circle"></i> Đăng ký học phần</a></li>
        <li><a href="<?= $baseUrl ?>/SinhVien/monDaDangKy" class="<?= $pageActive === 'mondadangky' ? 'active' : '' ?>"><i class="fas fa-list-check"></i> Môn đã đăng ký</a></li>
        <li><a href="<?= $baseUrl ?>/SinhVien/xemDiemDanh" class="<?= $pageActive === 'xemdiemdanh' ? 'active' : '' ?>"><i class="fas fa-clipboard-check"></i> Xem điểm danh</a></li>
    </ul>
    <div class="sidebar-section">Hệ thống</div>
    <ul class="sidebar-menu">
        <li><a href="<?= $baseUrl ?>/Auth/logout" class="sidebar-logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
    </ul>
</aside>
<div class="main-content">
    <header class="top-header">
        <div class="page-title">
            <h1><?= htmlspecialchars($pageTitle ?? 'Cổng Sinh Viên') ?></h1>
            <p class="breadcrumb"><?= $breadcrumb ?? '' ?></p>
        </div>
        <div class="header-actions">
            <div class="topbar-dropdown" id="userDropdown">
                <div class="user-profile topbar-dropdown__trigger" id="userDropdownTrigger">
                    <?php $avtUrl = !empty($_SESSION['user_avatar']) ? rtrim($baseUrl, '/') . '/' . ltrim($_SESSION['user_avatar'], '/') : ''; ?>
                    <?php if ($avtUrl): ?>
                        <img src="<?= htmlspecialchars($avtUrl) ?>" alt="Avatar" class="user-avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                    <?php else: ?>
                        <div class="user-avatar"><?= strtoupper(mb_substr($svTen, 0, 1, 'UTF-8')) ?></div>
                    <?php endif; ?>
                    <div class="user-info">
                        <span class="user-name"><?= htmlspecialchars($svTen) ?></span>
                        <span class="user-role"><?= htmlspecialchars($svMa) ?></span>
                    </div>
                    <i class="fas fa-chevron-down topbar-chevron"></i>
                </div>
                <div class="topbar-dropdown__menu dropdown-menu">
                    <a class="topbar-dropdown__item dropdown-item" href="<?= $baseUrl ?>/Profile/index"><i class="fas fa-user"></i> Hồ sơ</a>
                    <a class="topbar-dropdown__item dropdown-item" href="<?= $baseUrl ?>/Profile/settings"><i class="fas fa-cog"></i> Cài đặt</a>
                    <a class="topbar-dropdown__item dropdown-item" href="<?= $baseUrl ?>/SinhVien/thongTinCaNhan"><i class="fas fa-id-card"></i> Thông tin cá nhân</a>
                    <div class="topbar-dropdown__divider"></div>
                    <a class="topbar-dropdown__item topbar-dropdown__item--danger dropdown-item" href="<?= $baseUrl ?>/Auth/logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                </div>
            </div>
        </div>
    </header>
    <main class="content-area">
