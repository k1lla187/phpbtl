<?php
$pageActive = 'thongke';
$pageTitle = 'Thống kê cá nhân';
$breadcrumb = 'Cổng Sinh viên / Thống kê';
$canhBao = $canhBao ?? [];
$tongTinChi = $tongTinChi ?? 0;
$tinChiDat = $tinChiDat ?? 0;
$tbToanKhoa = $tbToanKhoa ?? null;
$monRot = $monRot ?? 0;
require_once __DIR__ . '/_layout_sv.php';
?>
<div class="content-header">
    <div class="content-header__title">Thống kê cá nhân</div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-book"></i></div>
        <div class="stat-value"><?= (int)$tongTinChi ?></div>
        <div class="stat-label">Tổng số tín chỉ đã đăng ký</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-value"><?= (int)$tinChiDat ?></div>
        <div class="stat-label">Tín chỉ đã đạt (điểm ≥ 4.0)</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon cyan"><i class="fas fa-chart-line"></i></div>
        <div class="stat-value"><?= $tbToanKhoa !== null ? number_format($tbToanKhoa, 2) : '—' ?></div>
        <div class="stat-label">Điểm TB toàn khóa</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="stat-value"><?= (int)$monRot ?></div>
        <div class="stat-label">Môn chưa đạt</div>
    </div>
</div>

<?php if (!empty($canhBao)): ?>
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Cảnh báo / Đánh giá học lực</h5>
    </div>
    <div class="card-body">
    <?php foreach ($canhBao as $cb): ?>
        <div class="alert <?= strpos($cb, 'Cảnh báo') !== false || strpos($cb, 'cải thiện') !== false ? 'alert-warning' : 'alert-success' ?>">
            <?= htmlspecialchars($cb) ?>
        </div>
    <?php endforeach; ?>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Cảnh báo / Đánh giá học lực</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-success">Hiện không có cảnh báo học lực. Tiếp tục phát huy!</div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/_layout_sv_footer.php'; ?>
