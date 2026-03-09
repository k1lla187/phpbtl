<?php
$pageActive = 'dashboard';
$pageTitle = 'Bảng điều khiển';
$breadcrumb = 'Cổng Sinh viên / Trang chủ';
require_once __DIR__ . '/_layout_sv.php';
?>
<div class="content-header">
    <div class="content-header__title">Chào <?= htmlspecialchars($sinhVien['HoTen'] ?? 'bạn') ?>!</div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-book-open"></i></div>
        <div class="stat-value"><?= (int)($tongMon ?? 0) ?></div>
        <div class="stat-label">Môn đã đăng ký</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-book"></i></div>
        <div class="stat-value"><?= (int)($tongTinChi ?? 0) ?></div>
        <div class="stat-label">Tổng số tín chỉ</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-value"><?= (int)($tinChiDat ?? 0) ?></div>
        <div class="stat-label">Tín chỉ đã đạt</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon cyan"><i class="fas fa-chart-line"></i></div>
        <div class="stat-value"><?= $tbToanKhoa !== null ? number_format($tbToanKhoa, 1) : '—' ?></div>
        <div class="stat-label">Điểm TB toàn khóa</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">Truy cập nhanh</h5>
    </div>
    <div class="card-body">
        <div style="display: flex; flex-wrap: wrap; gap: 12px;">
            <a href="<?= $baseUrl ?? '' ?>/SinhVien/thongTinCaNhan" class="btn btn-primary">
                <i class="fas fa-user"></i> Thông tin cá nhân
            </a>
            <a href="<?= $baseUrl ?? '' ?>/SinhVien/xemDiem" class="btn btn-primary">
                <i class="fas fa-clipboard-list"></i> Xem điểm
            </a>
            <a href="<?= $baseUrl ?? '' ?>/SinhVien/thongKe" class="btn btn-primary">
                <i class="fas fa-chart-pie"></i> Thống kê cá nhân
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/_layout_sv_footer.php'; ?>
