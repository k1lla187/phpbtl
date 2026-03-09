<?php
$pageActive = 'thongtincanhan';
$pageTitle = 'Thông tin cá nhân';
$breadcrumb = 'Cổng Sinh viên / Thông tin cá nhân';
require_once __DIR__ . '/_layout_sv.php';

$sv = $sinhVien;
?>
<div class="content-header">
    <div class="content-header__title">Thông tin cá nhân</div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">Hồ sơ sinh viên</h5>
    </div>
    <div class="card-body">
    <div class="table-wrapper">
        <table>
            <tbody>
                <tr><th width="180">Mã sinh viên</th><td><strong><?= htmlspecialchars($sv['MaSinhVien'] ?? '') ?></strong></td></tr>
                <tr><th>Họ và tên</th><td><?= htmlspecialchars($sv['HoTen'] ?? '') ?></td></tr>
                <tr><th>Ngày sinh</th><td><?= !empty($sv['NgaySinh']) ? date('d/m/Y', strtotime($sv['NgaySinh'])) : '—' ?></td></tr>
                <tr><th>Giới tính</th><td><?= htmlspecialchars($sv['GioiTinh'] ?? '—') ?></td></tr>
                <tr><th>Lớp hành chính</th><td><span class="tag tag--muted"><?= htmlspecialchars($sv['TenLop'] ?? $sv['MaLop'] ?? '—') ?></span></td></tr>
                <tr><th>Email</th><td><?= htmlspecialchars($sv['Email'] ?? '—') ?></td></tr>
                <tr><th>Số điện thoại</th><td><?= htmlspecialchars($sv['SoDienThoai'] ?? '—') ?></td></tr>
                <tr><th>Địa chỉ</th><td><?= htmlspecialchars($sv['DiaChi'] ?? '—') ?></td></tr>
                <tr><th>Trạng thái học tập</th><td>
                    <?php
                    $tt = $sv['TrangThaiHocTap'] ?? 'Đang học';
                    echo $tt === 'Đang học' ? '<span class="badge badge-success">Đang học</span>' : '<span class="badge badge-warning">' . htmlspecialchars($tt) . '</span>';
                    ?>
                </td></tr>
            </tbody>
        </table>
    </div>
    </div>
</div>

<?php require_once __DIR__ . '/_layout_sv_footer.php'; ?>
