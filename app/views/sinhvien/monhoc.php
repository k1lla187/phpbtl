<?php
$pageActive = 'monhoc';
$pageTitle = 'Môn học theo ngành';
$breadcrumb = 'Cổng Sinh viên / Môn học theo ngành';
$monDangHoc = $monDangHoc ?? [];
$monChuaHoc = $monChuaHoc ?? [];
$monDaHoc = $monDaHoc ?? [];
$tenNganh = $tenNganh ?? '';
$maNganh = $maNganh ?? null;
require_once __DIR__ . '/_layout_sv.php';
?>
<div class="page-header">
    <h4 class="content-header__title">Môn học theo ngành <?= htmlspecialchars($tenNganh) ?></h4>
</div>

<?php if (!$maNganh): ?>
<div class="card">
    <div class="card-body text-center text-muted py-5">
        <i class="fas fa-info-circle fa-2x mb-3"></i>
        <p>Chưa xác định được ngành học của bạn. Vui lòng liên hệ phòng đào tạo.</p>
    </div>
</div>
<?php else: ?>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-book-reader me-2"></i>Môn đang học (học kỳ hiện tại)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Mã môn</th>
                        <th>Tên môn học</th>
                        <th>Số tín chỉ</th>
                        <th>Lớp HP</th>
                        <th>Học kỳ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($monDangHoc)): ?>
                        <?php foreach ($monDangHoc as $i => $m): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($m['MaMonHoc'] ?? '') ?></td>
                            <td><strong><?= htmlspecialchars($m['TenMonHoc'] ?? '') ?></strong></td>
                            <td><?= (int)($m['SoTinChi'] ?? 0) ?></td>
                            <td><?= htmlspecialchars($m['MaLopHocPhan'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($m['TenHocKy'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Chưa có môn đang học trong học kỳ hiện tại.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Môn đã học (đã hoàn thành)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Mã môn</th>
                        <th>Tên môn học</th>
                        <th>Số tín chỉ</th>
                        <th>Lớp HP</th>
                        <th>Học kỳ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($monDaHoc)): ?>
                        <?php foreach ($monDaHoc as $i => $m): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($m['MaMonHoc'] ?? '') ?></td>
                            <td><strong><?= htmlspecialchars($m['TenMonHoc'] ?? '') ?></strong></td>
                            <td><?= (int)($m['SoTinChi'] ?? 0) ?></td>
                            <td><?= htmlspecialchars($m['MaLopHocPhan'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($m['TenHocKy'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Chưa có môn đã hoàn thành.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-warning">
        <h5 class="mb-0"><i class="fas fa-hourglass-half me-2"></i>Môn chưa học (thuộc ngành của bạn)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Mã môn</th>
                        <th>Tên môn học</th>
                        <th>Số tín chỉ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($monChuaHoc)): ?>
                        <?php foreach ($monChuaHoc as $i => $m): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($m['MaMonHoc'] ?? '') ?></td>
                            <td><strong><?= htmlspecialchars($m['TenMonHoc'] ?? '') ?></strong></td>
                            <td><?= (int)($m['SoTinChi'] ?? 0) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">Bạn đã đăng ký tất cả môn thuộc ngành của mình.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php endif; ?>

<?php require_once __DIR__ . '/_layout_sv_footer.php'; ?>
