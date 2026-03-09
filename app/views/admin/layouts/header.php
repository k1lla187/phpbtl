<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UNISCORE - Hệ Thống Quản Lý Điểm</title>
    <link rel="icon" type="image/svg+xml" href="<?php echo (defined('URLROOT') ? rtrim(URLROOT, '/') : '') . '/favicon.svg'; ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo (defined('URLROOT') ? rtrim(URLROOT, '/') : '') . '/css/admin.css'; ?>" rel="stylesheet">
    <script>window.APP_BASE_URL = '<?php echo defined("URLROOT") ? rtrim(URLROOT, "/") : ""; ?>';</script>
    <script>(function(){var t=localStorage.getItem('app-theme');if(t==='dark')document.documentElement.classList.add('dark-theme');})();</script>
    <?php
    // Lấy số yêu cầu đổi mật khẩu đang chờ xử lý
    $pendingPasswordRequests = 0;
    try {
        require_once __DIR__ . '/../../../models/YeuCauDoiMatKhauModel.php';
        require_once __DIR__ . '/../../../config/Database.php';
        $headerDb = new Database();
        $headerYeuCauModel = new YeuCauDoiMatKhauModel($headerDb->getConnection());
        $pendingPasswordRequests = $headerYeuCauModel->countPending();
    } catch (Exception $e) {
        $pendingPasswordRequests = 0;
    }
    ?>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <img src="<?php echo (defined('URLROOT') ? rtrim(URLROOT, '/') : '') . '/favicon.svg'; ?>" alt="UNISCORE" class="logo-img" style="width: 40px; height: 40px;">
            <div>
                <div class="brand-text">UNISCORE</div>
                <div class="brand-subtitle">Quản lý điểm sinh viên</div>
            </div>
        </div>
        
        <div class="sidebar-section">Tổng quan</div>
        <ul class="sidebar-menu">
            <li><a href="index.php?url=Home/index" class="<?= strpos($_GET['url'] ?? '', 'Home') !== false ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        </ul>
        
        <div class="sidebar-section">Quản lý</div>
        <ul class="sidebar-menu">
            <li><a href="index.php?url=SinhVien/index" class="<?= strpos($_GET['url'] ?? '', 'SinhVien') !== false ? 'active' : '' ?>"><i class="fas fa-user-graduate"></i> Sinh viên</a></li>
            <li><a href="index.php?url=GiangVien/index" class="<?= strpos($_GET['url'] ?? '', 'GiangVien') !== false ? 'active' : '' ?>"><i class="fas fa-chalkboard-teacher"></i> Giảng viên</a></li>
            <li><a href="index.php?url=MonHoc/index" class="<?= strpos($_GET['url'] ?? '', 'MonHoc') !== false ? 'active' : '' ?>"><i class="fas fa-book"></i> Môn học</a></li>
            <li><a href="index.php?url=LopHocPhan/index" class="<?= strpos($_GET['url'] ?? '', 'LopHocPhan') !== false ? 'active' : '' ?>"><i class="fas fa-chalkboard"></i> Lớp học phần</a></li>
            <li><a href="index.php?url=Diem/index" class="<?= strpos($_GET['url'] ?? '', 'Diem') !== false ? 'active' : '' ?>"><i class="fas fa-star"></i> Quản lý điểm</a></li>
        </ul>
        
        <div class="sidebar-section">Hệ thống</div>
        <ul class="sidebar-menu">
            <li><a href="index.php?url=User/index" class="<?= strpos($_GET['url'] ?? '', 'User') !== false ? 'active' : '' ?>"><i class="fas fa-users-cog"></i> Tài khoản</a></li>
            <li>
                <a href="index.php?url=YeuCauDoiMatKhau/index" class="<?= strpos($_GET['url'] ?? '', 'YeuCauDoiMatKhau') !== false ? 'active' : '' ?>">
                    <i class="fas fa-key"></i> Yêu cầu đổi MK
                    <?php if ($pendingPasswordRequests > 0): ?>
                    <span class="menu-badge"><?= $pendingPasswordRequests ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li><a href="index.php?url=ThongKe/index" class="<?= strpos($_GET['url'] ?? '', 'ThongKe') !== false ? 'active' : '' ?>"><i class="fas fa-chart-bar"></i> Thống kê</a></li>
        </ul>
        
        <div class="sidebar-section">Danh mục</div>
        <ul class="sidebar-menu">
            <li><a href="index.php?url=Khoa/index" class="<?= strpos($_GET['url'] ?? '', 'Khoa') !== false ? 'active' : '' ?>"><i class="fas fa-building"></i> Khoa</a></li>
            <li><a href="index.php?url=Nganh/index" class="<?= strpos($_GET['url'] ?? '', 'Nganh') !== false ? 'active' : '' ?>"><i class="fas fa-graduation-cap"></i> Ngành</a></li>
            <li><a href="index.php?url=HocKy/index" class="<?= strpos($_GET['url'] ?? '', 'HocKy') !== false ? 'active' : '' ?>"><i class="fas fa-calendar-alt"></i> Học kỳ</a></li>
            <li><a href="index.php?url=LopHanhChinh/index" class="<?= strpos($_GET['url'] ?? '', 'LopHanhChinh') !== false ? 'active' : '' ?>"><i class="fas fa-users"></i> Lớp hành chính</a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="top-header">
            <div class="page-title">
                <h1><?= $data['pageTitle'] ?? 'Dashboard' ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.php?url=Home/index">Trang chủ</a></li>
                        <?php if(isset($data['breadcrumb'])): ?>
                        <li class="breadcrumb-item active"><?= $data['breadcrumb'] ?></li>
                        <?php endif; ?>
                    </ol>
                </nav>
            </div>
            <div class="header-actions">
                <a href="index.php?url=YeuCauDoiMatKhau/index" class="notification-badge" title="Yêu cầu đổi mật khẩu">
                    <i class="fas fa-bell" style="font-size: 18px; color: #64748b; cursor: pointer;"></i>
                    <?php if ($pendingPasswordRequests > 0): ?>
                    <span class="badge"><?= $pendingPasswordRequests ?></span>
                    <?php endif; ?>
                </a>
                <div class="dropdown">
                    <div class="user-profile" data-bs-toggle="dropdown">
                        <?php $avtUrl = !empty($_SESSION['user_avatar']) ? rtrim(defined('URLROOT') ? URLROOT : '', '/') . '/' . ltrim($_SESSION['user_avatar'], '/') : ''; ?>
                        <?php if ($avtUrl): ?>
                            <img src="<?= htmlspecialchars($avtUrl) ?>" alt="Avatar" class="user-avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                        <?php else: ?>
                            <div class="user-avatar"><?= strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)) ?></div>
                        <?php endif; ?>
                        <div class="user-info d-none d-md-block">
                            <div class="user-name"><?= $_SESSION['user_name'] ?? 'Admin' ?></div>
                            <div class="user-role"><?= $_SESSION['user_role'] ?? 'Quản trị viên' ?></div>
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="index.php?url=Profile/index"><i class="fas fa-user me-2"></i> Hồ sơ</a></li>
                        <li><a class="dropdown-item" href="index.php?url=Profile/settings"><i class="fas fa-cog me-2"></i> Cài đặt</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="index.php?url=Auth/logout"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="content-area">