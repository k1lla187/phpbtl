<?php
$giangVienTen = $giangVien['HoTen'] ?? 'Giảng viên';
$giangVienMa = $giangVien['MaGiangVien'] ?? '';

$baseUrl = defined('URLROOT') ? URLROOT : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNISCORE - Bảng điều khiển Giảng viên</title>
    <link rel="icon" type="image/svg+xml" href="<?= rtrim($baseUrl, '/') ?>/favicon.svg">
    <link href="<?= rtrim($baseUrl, '/') ?>/css/giangvien.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .dashboard-welcome {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 28px 32px;
            color: white;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }
        .dashboard-welcome::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        .dashboard-welcome h2 {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .dashboard-welcome p {
            opacity: 0.9;
            font-size: 15px;
        }
        .dashboard-welcome .date-display {
            margin-top: 12px;
            font-size: 13px;
            opacity: 0.8;
        }

        .stats-grid-dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(15,23,42,0.08);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(15,23,42,0.12);
        }
        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }
        .stat-icon.blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .stat-icon.green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; }
        .stat-icon.orange { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
        .stat-icon.cyan { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
        .stat-icon.yellow { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; }
        .stat-icon.purple { background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); color: white; }

        .stat-content { flex: 1; }
        .stat-value { font-size: 28px; font-weight: 700; color: #1e293b; line-height: 1.2; }
        .stat-label { font-size: 13px; color: #64748b; margin-top: 2px; }

        .quick-actions {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(15,23,42,0.08);
            margin-bottom: 24px;
        }
        .quick-actions h3 {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 16px;
        }
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .action-btn i { font-size: 16px; }
        
        .action-btn--primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .action-btn--primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102,126,234,0.4);
        }
        
        .action-btn--success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }
        .action-btn--success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(17,153,142,0.4);
        }
        
        .action-btn--warning {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }
        .action-btn--warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(250,112,154,0.4);
        }
        
        .action-btn--info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        .action-btn--info:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79,172,254,0.4);
        }

        .recent-classes {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(15,23,42,0.08);
        }
        .recent-classes h3 {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 16px;
        }
        .class-item {
            display: flex;
            align-items: center;
            padding: 14px;
            border-radius: 10px;
            margin-bottom: 10px;
            background: #f8fafc;
            transition: background 0.2s;
        }
        .class-item:hover {
            background: #f1f5f9;
        }
        .class-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-right: 14px;
        }
        .class-info { flex: 1; }
        .class-name { font-weight: 600; color: #1e293b; font-size: 14px; }
        .class-meta { font-size: 12px; color: #64748b; margin-top: 2px; }
        .class-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge--active { background: #dcfce7; color: #166534; }
        .badge--students { background: #e0f2fe; color: #075985; }

        .empty-state-dashboard {
            padding: 40px;
            text-align: center;
            color: #94a3b8;
        }
        .empty-state-dashboard i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .dashboard-welcome { padding: 20px; }
            .dashboard-welcome h2 { font-size: 22px; }
            .stats-grid-dashboard { grid-template-columns: 1fr 1fr; }
            .action-buttons { flex-direction: column; }
            .action-btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="sidebar__brand">
            <img src="<?= rtrim($baseUrl, '/') ?>/favicon.svg" alt="UNISCORE" class="sidebar__logo" style="width: 34px; height: 34px; border-radius: 6px;">
            <div>
                <div class="sidebar__title" style="color: #d4af37;">UNISCORE</div>
                <div class="sidebar__subtitle">Cổng Giảng Viên</div>
            </div>
        </div>
        <nav class="sidebar__nav">
            <div class="nav-section-title">Tổng quan</div>
            <a href="<?php echo $baseUrl; ?>/GiangVien/dashboard" class="nav-item nav-item--active" style="text-decoration: none;">
                <div class="nav-item__icon">🏠</div><div>Bảng điều khiển</div>
            </a>
            <div class="nav-section-title">Giảng dạy</div>
            <a href="<?php echo $baseUrl; ?>/GiangVien/indexLopHocPhan" class="nav-item" style="text-decoration: none;">
                <div class="nav-item__icon">📚</div><div>Lớp & môn được dạy</div><div class="nav-item__chevron">▾</div>
            </a>
            <div class="nav-section-title">Chức năng</div>
            <a href="<?php echo $baseUrl; ?>/GiangVien/nhapDiem" class="nav-item" style="text-decoration: none;"><div class="nav-item__icon">📝</div><div>Nhập điểm</div></a>
            <a href="<?php echo $baseUrl; ?>/GiangVien/traCuuDiem" class="nav-item" style="text-decoration: none;"><div class="nav-item__icon">🔍</div><div>Tra cứu điểm</div></a>
            <a href="<?php echo $baseUrl; ?>/GiangVien/guiThongBao" class="nav-item" style="text-decoration: none;"><div class="nav-item__icon">📧</div><div>Gửi thông báo</div></a>
            <a href="<?php echo $baseUrl; ?>/GiangVien/lichDay" class="nav-item" style="text-decoration: none;"><div class="nav-item__icon">📆</div><div>Lịch giảng dạy</div></a>
            <a href="<?php echo $baseUrl; ?>/GiangVien/diemDanh" class="nav-item" style="text-decoration: none;"><div class="nav-item__icon">📋</div><div>Điểm danh</div></a>
        </nav>
    </aside>

    <div class="main">
        <header class="topbar">
            <div class="topbar__left">
                <div>
                    <div class="topbar__title">Bảng điều khiển</div>
                    <div class="topbar__breadcrumb">Cổng Giảng Viên / Tổng quan</div>
                </div>
            </div>
            <div class="topbar__right">
                <div class="text-muted">Thông báo <span class="badge-pill">0</span></div>
                <div class="topbar-dropdown" id="userDropdown">
                    <div class="topbar-dropdown__trigger user-info" id="userDropdownTrigger" aria-expanded="false">
                        <div class="user-avatar"><?php echo strtoupper(mb_substr($giangVienTen, 0, 1, 'UTF-8')); ?></div>
                        <div class="user-meta">
                            <div class="user-meta__name"><?php echo htmlspecialchars($giangVienTen); ?></div>
                            <div class="user-meta__id"><?php echo htmlspecialchars($giangVienMa); ?></div>
                        </div>
                        <i class="fas fa-chevron-down" style="font-size: 11px; color: #a0aec0;"></i>
                    </div>
                    <div class="topbar-dropdown__menu" role="menu">
                        <a class="topbar-dropdown__item" href="<?php echo $baseUrl; ?>/Profile/index"><i class="fas fa-user"></i> Hồ sơ</a>
                        <a class="topbar-dropdown__item" href="<?php echo $baseUrl; ?>/Profile/settings"><i class="fas fa-cog"></i> Cài đặt</a>
                        <div class="topbar-dropdown__divider"></div>
                        <a class="topbar-dropdown__item topbar-dropdown__item--danger" href="<?php echo $baseUrl; ?>/Auth/logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                    </div>
                </div>
            </div>
        </header>

        <main class="content">
            <!-- Welcome Banner -->
            <div class="dashboard-welcome">
                <h2>Xin chào, <?php echo htmlspecialchars($giangVienTen); ?>!</h2>
                <p>Chúc bạn một ngày giảng dạy hiệu quả. Dưới đây là tổng quan công việc hôm nay.</p>
                <div class="date-display">
                    <i class="fas fa-calendar-alt"></i> 
                    <?php 
                        $days = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];
                        $dayName = $days[date('w')];
                        echo $dayName . ', ngày ' . date('d/m/Y'); 
                    ?>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid-dashboard">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo (int)$thongKe['tongLop']; ?></div>
                        <div class="stat-label">Lớp đang dạy</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo (int)$thongKe['tongSinhVien']; ?></div>
                        <div class="stat-label">Tổng sinh viên</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo (int)$thongKe['tongMon']; ?></div>
                        <div class="stat-label">Môn học</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon cyan">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo (int)$thongKe['diemDaNhap']; ?></div>
                        <div class="stat-label">Điểm đã nhập</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon yellow">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo (int)$thongKe['buoiDaDiemDanh']; ?></div>
                        <div class="stat-label">Buổi đã điểm danh</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon purple">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo number_format($thongKe['diemTrungBinh'] ?? 0, 1); ?></div>
                        <div class="stat-label">TB điểm lớp</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h3><i class="fas fa-bolt" style="color: #f59e0b; margin-right: 8px;"></i>Truy cập nhanh</h3>
                <div class="action-buttons">
                    <a href="<?php echo $baseUrl; ?>/GiangVien/nhapDiem" class="action-btn action-btn--primary">
                        <i class="fas fa-pen"></i> Nhập điểm
                    </a>
                    <a href="<?php echo $baseUrl; ?>/GiangVien/diemDanh" class="action-btn action-btn--success">
                        <i class="fas fa-clipboard-list"></i> Điểm danh
                    </a>
                    <a href="<?php echo $baseUrl; ?>/GiangVien/traCuuDiem" class="action-btn action-btn--warning">
                        <i class="fas fa-search"></i> Tra cứu điểm
                    </a>
                    <a href="<?php echo $baseUrl; ?>/GiangVien/guiThongBao" class="action-btn action-btn--info">
                        <i class="fas fa-paper-plane"></i> Gửi thông báo
                    </a>
                    <a href="<?php echo $baseUrl; ?>/GiangVien/lichDay" class="action-btn action-btn--primary">
                        <i class="fas fa-calendar"></i> Lịch giảng dạy
                    </a>
                    <a href="<?php echo $baseUrl; ?>/GiangVien/indexLopHocPhan" class="action-btn action-btn--success">
                        <i class="fas fa-users"></i> DS Sinh viên
                    </a>
                </div>
            </div>

            <!-- Recent Classes -->
            <div class="recent-classes">
                <h3><i class="fas fa-book-open" style="color: #667eea; margin-right: 8px;"></i>Lớp học phần đang giảng dạy</h3>
                
                <?php if (empty($lopHocPhanList)): ?>
                    <div class="empty-state-dashboard">
                        <i class="fas fa-inbox"></i>
                        <p>Hiện tại bạn chưa được phân công giảng dạy lớp học phần nào.</p>
                    </div>
                <?php else: ?>
                    <?php 
                    $displayLops = array_slice($lopHocPhanList, 0, 5);
                    foreach ($displayLops as $lop): 
                        $siSo = (int)($lop['SiSo'] ?? 0);
                        $tenMon = $lop['TenMonHoc'] ?? ($lop['MaMonHoc'] ?? '');
                        $maLop = $lop['MaLopHocPhan'] ?? '';
                        $thu = $lop['Thu'] ?? '';
                        $phong = $lop['PhongHoc'] ?? '';
                    ?>
                        <div class="class-item">
                            <div class="class-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="class-info">
                                <div class="class-name"><?php echo htmlspecialchars($tenMon); ?></div>
                                <div class="class-meta">
                                    <?php if ($maLop): ?><?php echo htmlspecialchars($maLop); ?> | <?php endif; ?>
                                    <?php if ($thu): ?><?php echo htmlspecialchars($thu); ?> | <?php endif; ?>
                                    <?php if ($phong): ?>Phòng <?php echo htmlspecialchars($phong); ?><?php endif; ?>
                                </div>
                            </div>
                            <span class="class-badge badge--students">
                                <i class="fas fa-user-friends"></i> <?php echo $siSo; ?> SV
                            </span>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if (count($lopHocPhanList) > 5): ?>
                        <div style="text-align: center; margin-top: 16px;">
                            <a href="<?php echo $baseUrl; ?>/GiangVien/indexLopHocPhan" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fas fa-eye"></i> Xem tất cả <?php echo count($lopHocPhanList); ?> lớp
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<script>
(function() {
    var dropdown = document.getElementById('userDropdown');
    var trigger = document.getElementById('userDropdownTrigger');
    if (dropdown && trigger) {
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('is-open');
        });
        document.addEventListener('click', function() {
            dropdown.classList.remove('is-open');
        });
        dropdown.querySelector('.topbar-dropdown__menu') && dropdown.querySelector('.topbar-dropdown__menu').addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
})();
</script>
</body>
</html>
