<?php
$giangVienTen = $giangVien['HoTen'] ?? 'Giảng viên';
$giangVienMa = $giangVien['MaGiangVien'] ?? '';
$lichDay = $lichDay ?? [];
$hocKys = $hocKys ?? [];
$maHocKy = $_GET['maHocKy'] ?? null;
$baseUrl = defined('URLROOT') ? URLROOT : '';
$thuLabels = [2 => 'Thứ 2', 3 => 'Thứ 3', 4 => 'Thứ 4', 5 => 'Thứ 5', 6 => 'Thứ 6', 7 => 'Thứ 7'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Giảng Dạy - UNISCORE Giảng Viên</title>
    <link rel="icon" type="image/svg+xml" href="<?= rtrim($baseUrl ?? '', '/') ?>/favicon.svg">
    <link href="<?= rtrim($baseUrl ?? '', '/') ?>/css/giangvien.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="sidebar__brand">
            <img src="<?= rtrim($baseUrl ?? '', '/') ?>/favicon.svg" alt="UNISCORE" class="sidebar__logo" style="width: 34px; height: 34px; border-radius: 6px;">
            <div>
                <div class="sidebar__title" style="color: #d4af37;">UNISCORE</div>
                <div class="sidebar__subtitle">Cổng Giảng Viên</div>
            </div>
        </div>
        <nav class="sidebar__nav">
            <div class="nav-section-title">Tổng quan</div>
            <a href="<?= $baseUrl ?>/GiangVien/dashboard" class="nav-item"><div class="nav-item__icon">🏠</div><div>Bảng điều khiển</div></a>
            <div class="nav-section-title">Giảng dạy</div>
            <a href="<?= $baseUrl ?>/GiangVien/indexLopHocPhan" class="nav-item"><div class="nav-item__icon">📚</div><div>Lớp & môn được dạy</div></a>
            <div class="nav-section-title">Chức năng</div>
            <a href="<?= $baseUrl ?>/GiangVien/nhapDiem" class="nav-item"><div class="nav-item__icon">📝</div><div>Nhập điểm</div></a>
            <a href="<?= $baseUrl ?>/GiangVien/traCuuDiem" class="nav-item"><div class="nav-item__icon">🔍</div><div>Tra cứu điểm</div></a>
            <a href="<?= $baseUrl ?>/GiangVien/guiThongBao" class="nav-item"><div class="nav-item__icon">📧</div><div>Gửi thông báo</div></a>
            <a href="<?= $baseUrl ?>/GiangVien/lichDay" class="nav-item nav-item--active"><div class="nav-item__icon">📆</div><div>Lịch giảng dạy</div></a>
            <a href="<?= $baseUrl ?>/GiangVien/diemDanh" class="nav-item"><div class="nav-item__icon">📋</div><div>Điểm danh</div></a>
        </nav>
    </aside>

    <div class="main">
        <header class="topbar">
            <div>
                <div class="topbar__title">Lịch Giảng Dạy</div>
                <div class="topbar__breadcrumb">Thời khóa biểu / Lịch dạy của giảng viên</div>
            </div>
            <div class="topbar__right">
                <div class="topbar-dropdown" id="userDropdown">
                    <div class="topbar-dropdown__trigger user-info" id="userDropdownTrigger">
                        <div class="user-avatar"><?= strtoupper(mb_substr($giangVienTen, 0, 1, 'UTF-8')) ?></div>
                        <div class="user-meta">
                            <div class="user-meta__name"><?= htmlspecialchars($giangVienTen) ?></div>
                            <div class="user-meta__id"><?= htmlspecialchars($giangVienMa) ?></div>
                        </div>
                        <i class="fas fa-chevron-down" style="font-size: 11px; color: #a0aec0;"></i>
                    </div>
                    <div class="topbar-dropdown__menu" role="menu">
                        <a class="topbar-dropdown__item" href="<?= $baseUrl ?>/Profile/index"><i class="fas fa-user"></i> Hồ sơ</a>
                        <a class="topbar-dropdown__item" href="<?= $baseUrl ?>/Profile/settings"><i class="fas fa-cog"></i> Cài đặt</a>
                        <div class="topbar-dropdown__divider"></div>
                        <a class="topbar-dropdown__item topbar-dropdown__item--danger" href="<?= $baseUrl ?>/Auth/logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                    </div>
                </div>
            </div>
        </header>

        <main class="content">
            <div class="content-header">
                <div class="content-header__title">Thời khóa biểu giảng dạy</div>
                <form method="get" action="" style="display: flex; gap: 8px;">
                    <select name="maHocKy" class="select" onchange="this.form.submit()">
                        <option value="">Tất cả học kỳ</option>
                        <?php foreach ($hocKys as $hk): ?>
                            <option value="<?= htmlspecialchars($hk['MaHocKy']) ?>" <?= ($maHocKy === $hk['MaHocKy']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($hk['TenHocKy'] ?? '') ?> - <?= $hk['NamHoc'] ?? '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <?php
            $tongTiet = 0;
            $tongCa = 0;
            foreach ($lichDay as $ca) {
                $tietBD = (int)($ca['TietBatDau'] ?? 1);
                $tietKT = (int)($ca['TietKetThuc'] ?? 1);
                $soTiet = max(0, $tietKT - $tietBD + 1);
                $tongTiet += $soTiet;
                if ($soTiet >= 3) $tongCa++; else $tongCa += $soTiet / 3;
            }
            $tongCa = (int)round($tongCa);
            $tienChiTuongDuong = round($tongCa / 5, 1);
            $chuanTiet = 15 * 4;
            $phanTramTiet = $chuanTiet > 0 ? round($tongTiet / $chuanTiet * 100, 1) : 0;
            ?>
            <div class="card" style="margin-bottom: 16px;">
                <div class="card__title">Thống kê giảng dạy (1 ca = 3 tiết, 1 tín chỉ = 5 ca = 15 tiết)</div>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 12px;">
                    <div style="background: #ebf8ff; padding: 12px; border-radius: 8px; text-align: center;">
                        <div style="font-size: 20px; font-weight: 700; color: #2b6cb0;"><?= $tongTiet ?></div>
                        <div style="font-size: 12px; color: #718096;">Tổng tiết/tuần</div>
                    </div>
                    <div style="background: #e6fffa; padding: 12px; border-radius: 8px; text-align: center;">
                        <div style="font-size: 20px; font-weight: 700; color: #319795;"><?= $tongCa ?></div>
                        <div style="font-size: 12px; color: #718096;">Tổng ca (3 tiết/ca)</div>
                    </div>
                    <div style="background: #faf5ff; padding: 12px; border-radius: 8px; text-align: center;">
                        <div style="font-size: 20px; font-weight: 700; color: #805ad5;"><?= $tienChiTuongDuong ?></div>
                        <div style="font-size: 12px; color: #718096;">Tín chỉ tương đương</div>
                    </div>
                    <div style="background: #fffaf0; padding: 12px; border-radius: 8px; text-align: center;">
                        <div style="font-size: 20px; font-weight: 700; color: #dd6b20;"><?= $phanTramTiet ?>%</div>
                        <div style="font-size: 12px; color: #718096;">% theo tiết (chuẩn 60 tiết)</div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card__title">Lịch dạy theo tuần</div>
                <?php if (empty($lichDay)): ?>
                    <div class="empty-state">Chưa có lịch giảng dạy. Liên hệ phòng đào tạo để cập nhật thời khóa biểu.</div>
                <?php else:
                    $tietMax = 15;
                    $slots = [];
                    $rowspan = [];
                    for ($t = 1; $t <= $tietMax; $t++) {
                        for ($thu = 2; $thu <= 7; $thu++) {
                            $slots[$t][$thu] = null;
                            $rowspan[$t][$thu] = 0;
                        }
                    }
                    foreach ($lichDay as $ca) {
                        $tietBD = (int)($ca['TietBatDau'] ?? 1);
                        $tietKT = (int)($ca['TietKetThuc'] ?? 1);
                        $thu = (int)($ca['Thu'] ?? 2);
                        for ($t = $tietBD; $t <= $tietKT; $t++) {
                            $slots[$t][$thu] = $ca;
                        }
                        $rowspan[$tietBD][$thu] = max(1, $tietKT - $tietBD + 1);
                    }
                ?>
                <div class="table-wrapper" style="overflow-x: auto;">
                    <table class="table" style="min-width: 700px; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th style="width: 70px; padding: 10px; background: #f7fafc;">Tiết</th>
                                <?php for ($thu = 2; $thu <= 7; $thu++): ?>
                                    <th style="padding: 10px; background: #f7fafc;"><?= $thuLabels[$thu] ?? 'T' . $thu ?></th>
                                <?php endfor; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $gvCovered = [];
                            for ($thu = 2; $thu <= 7; $thu++) $gvCovered[$thu] = 0;
                            for ($tiet = 1; $tiet <= $tietMax; $tiet++): ?>
                                <tr>
                                    <td style="padding: 8px; background: #f7fafc; font-weight: 600;"><strong>Tiết <?= $tiet ?></strong></td>
                                    <?php for ($thu = 2; $thu <= 7; $thu++): ?>
                                        <?php
                                        if (isset($gvCovered[$thu]) && $gvCovered[$thu] > 0) { $gvCovered[$thu]--; continue; }
                                        $ca = $slots[$tiet][$thu] ?? null;
                                        $rs = (int)($rowspan[$tiet][$thu] ?? 0);
                                        if ($rs > 0) $gvCovered[$thu] = $rs - 1;
                                        ?>
                                        <td <?= $rs > 0 ? 'rowspan="' . $rs . '"' : '' ?> style="vertical-align: top; min-width: 120px; padding: 8px;<?= $ca ? ' background: #ebf8ff; border-left: 3px solid #2b6cb0;' : '' ?>">
                                            <?php if ($ca): ?>
                                                <div class="mon" style="font-weight: 600; color: #2b6cb0;"><?= htmlspecialchars($ca['TenMonHoc'] ?? '') ?></div>
                                                <div class="lop" style="font-size: 11px; color: #718096;"><?= htmlspecialchars($ca['MaLopHocPhan'] ?? '') ?> (<?= $ca['SiSo'] ?? 0 ?> SV)</div>
                                                <div class="phong" style="font-size: 11px; color: #38a169;">Phòng: <?= htmlspecialchars($ca['PhongHoc'] ?? $ca['PhongMacDinh'] ?? '-') ?></div>
                                                <div style="font-size: 10px; color: #a0aec0;">Tiết <?= $ca['TietBatDau'] ?? '' ?>-<?= $ca['TietKetThuc'] ?? '' ?></div>
                                            <?php endif; ?>
                                        </td>
                                    <?php endfor; ?>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>
<script>
(function() {
    var d = document.getElementById('userDropdown');
    var t = document.getElementById('userDropdownTrigger');
    if (d && t) {
        t.addEventListener('click', function(e) { e.stopPropagation(); d.classList.toggle('is-open'); });
        document.addEventListener('click', function() { d.classList.remove('is-open'); });
    }
})();
</script>
</body>
</html>
