<?php
/**
 * Layout partial for GiangVien sidebar navigation
 * Include this in views after defining $baseUrl, $giangVienTen, $giangVienMa, $pageActive
 */

$pageActive = $pageActive ?? '';

$navItems = [
    'dashboard' => ['url' => $baseUrl . '/GiangVien/dashboard', 'icon' => '🏠', 'label' => 'Bảng điều khiển'],
    'lop' => ['url' => $baseUrl . '/GiangVien/indexLopHocPhan', 'icon' => '📚', 'label' => 'Lớp & môn được dạy'],
    'nhapdiem' => ['url' => $baseUrl . '/GiangVien/nhapDiem', 'icon' => '📝', 'label' => 'Nhập điểm'],
    'tracuudiem' => ['url' => $baseUrl . '/GiangVien/traCuuDiem', 'icon' => '🔍', 'label' => 'Tra cứu điểm'],
    'guithongbao' => ['url' => $baseUrl . '/GiangVien/guiThongBao', 'icon' => '📧', 'label' => 'Gửi thông báo'],
    'lichday' => ['url' => $baseUrl . '/GiangVien/lichDay', 'icon' => '📆', 'label' => 'Lịch giảng dạy'],
    'diemdanh' => ['url' => $baseUrl . '/GiangVien/diemDanh', 'icon' => '📋', 'label' => 'Điểm danh'],
];
?>
<nav class="sidebar__nav">
    <div class="nav-section-title">Tổng quan</div>
    <a href="<?php echo $navItems['dashboard']['url']; ?>" class="nav-item<?php echo $pageActive === 'dashboard' ? ' nav-item--active' : ''; ?>" style="text-decoration: none;">
        <div class="nav-item__icon"><?php echo $navItems['dashboard']['icon']; ?></div><div><?php echo $navItems['dashboard']['label']; ?></div>
    </a>
    <div class="nav-section-title">Giảng dạy</div>
    <a href="<?php echo $navItems['lop']['url']; ?>" class="nav-item<?php echo $pageActive === 'lop' ? ' nav-item--active' : ''; ?>" style="text-decoration: none;">
        <div class="nav-item__icon"><?php echo $navItems['lop']['icon']; ?></div><div><?php echo $navItems['lop']['label']; ?></div>
    </a>
    <div class="nav-section-title">Chức năng</div>
    <a href="<?php echo $navItems['nhapdiem']['url']; ?>" class="nav-item<?php echo $pageActive === 'nhapdiem' ? ' nav-item--active' : ''; ?>" style="text-decoration: none;">
        <div class="nav-item__icon"><?php echo $navItems['nhapdiem']['icon']; ?></div><div><?php echo $navItems['nhapdiem']['label']; ?></div>
    </a>
    <a href="<?php echo $navItems['tracuudiem']['url']; ?>" class="nav-item<?php echo $pageActive === 'tracuudiem' ? ' nav-item--active' : ''; ?>" style="text-decoration: none;">
        <div class="nav-item__icon"><?php echo $navItems['tracuudiem']['icon']; ?></div><div><?php echo $navItems['tracuudiem']['label']; ?></div>
    </a>
    <a href="<?php echo $navItems['guithongbao']['url']; ?>" class="nav-item<?php echo $pageActive === 'guithongbao' ? ' nav-item--active' : ''; ?>" style="text-decoration: none;">
        <div class="nav-item__icon"><?php echo $navItems['guithongbao']['icon']; ?></div><div><?php echo $navItems['guithongbao']['label']; ?></div>
    </a>
    <a href="<?php echo $navItems['lichday']['url']; ?>" class="nav-item<?php echo $pageActive === 'lichday' ? ' nav-item--active' : ''; ?>" style="text-decoration: none;">
        <div class="nav-item__icon"><?php echo $navItems['lichday']['icon']; ?></div><div><?php echo $navItems['lichday']['label']; ?></div>
    </a>
    <a href="<?php echo $navItems['diemdanh']['url']; ?>" class="nav-item<?php echo $pageActive === 'diemdanh' ? ' nav-item--active' : ''; ?>" style="text-decoration: none;">
        <div class="nav-item__icon"><?php echo $navItems['diemdanh']['icon']; ?></div><div><?php echo $navItems['diemdanh']['label']; ?></div>
    </a>
</nav>
