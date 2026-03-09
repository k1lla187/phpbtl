<?php
$giangVienTen = $giangVien['HoTen'] ?? 'Giảng viên';
$giangVienMa = $giangVien['MaGiangVien'] ?? '';
$lopHocPhanList = $lopHocPhanList ?? [];
$lopHocPhanSelected = $lopHocPhanSelected ?? null;
$bangDiemDanh = $bangDiemDanh ?? [];
$baseUrl = defined('URLROOT') ? URLROOT : '';
$maLopHocPhan = $_GET['maLopHocPhan'] ?? null;
$buoiSelected = $buoiSelected ?? 1;
$success = isset($_GET['success']);
$sync = isset($_GET['sync']);

// Trạng thái điểm danh
const TRANG_THAI = [
    1 => ['label' => 'Có mặt', 'color' => '#22c55e', 'icon' => 'fa-check'],
    2 => ['label' => 'Muộn', 'color' => '#f59e0b', 'icon' => 'fa-clock'],
    3 => ['label' => 'Nghỉ có LD', 'color' => '#3b82f6', 'icon' => 'fa-calendar-check'],
    4 => ['label' => 'Nghỉ không LD', 'color' => '#ef4444', 'icon' => 'fa-times-circle'],
];

// Lấy dữ liệu điểm danh của buổi hiện tại
$diemDanhBuoiHienTai = [];
if ($maLopHocPhan && $buoiSelected > 0) {
    try {
        require_once __DIR__ . '/../../config/Database.php';
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("
            SELECT dd.MaDangKy, dd.TrangThai 
            FROM DIEM_DANH dd
            JOIN DANG_KY_HOC dk ON dd.MaDangKy = dk.MaDangKy
            WHERE dk.MaLopHocPhan = :maLop AND dd.BuoiThu = :buoi
        ");
        $stmt->bindParam(':maLop', $maLopHocPhan);
        $stmt->bindParam(':buoi', $buoiSelected);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $diemDanhBuoiHienTai[$row['MaDangKy']] = (int)$row['TrangThai'];
        }
    } catch (Exception $e) {
        error_log("Error loading attendance: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Điểm danh - UNISCORE Giảng Viên</title>
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
        <div class="nav-section-title">Tổng quan</div>
        <a href="<?= $baseUrl ?>/GiangVien/dashboard" class="nav-item"><div class="nav-item__icon">🏠</div><div>Bảng điều khiển</div></a>
        <div class="nav-section-title">Giảng dạy</div>
        <a href="<?= $baseUrl ?>/GiangVien/indexLopHocPhan" class="nav-item"><div class="nav-item__icon">📚</div><div>Lớp & môn được dạy</div></a>
        <div class="nav-section-title">Khác</div>
        <a href="<?= $baseUrl ?>/GiangVien/nhapDiem" class="nav-item"><div class="nav-item__icon">📝</div><div>Nhập điểm</div></a>
        <a href="<?= $baseUrl ?>/GiangVien/traCuuDiem" class="nav-item"><div class="nav-item__icon">🔍</div><div>Tra cứu điểm</div></a>
        <a href="<?= $baseUrl ?>/GiangVien/guiThongBao" class="nav-item"><div class="nav-item__icon">📧</div><div>Gửi thông báo</div></a>
        <a href="<?= $baseUrl ?>/GiangVien/lichDay" class="nav-item"><div class="nav-item__icon">📆</div><div>Lịch giảng dạy</div></a>
        <a href="<?= $baseUrl ?>/GiangVien/diemDanh" class="nav-item nav-item--active"><div class="nav-item__icon">📋</div><div>Điểm danh</div></a>
    </aside>

    <div class="main">
        <header class="topbar">
            <div>
                <div class="topbar__title">Điểm danh</div>
                <div class="topbar__breadcrumb">Bảng điểm danh - Điểm chuyên cần theo % tham gia buổi học (1 tín = 5 ca = 15 tiết)</div>
            </div>
        </header>

        <main class="content">
            <?php if ($success): ?>
            <div class="alert-success" id="success-alert"><i class="fas fa-check-circle me-2"></i>Đã lưu điểm danh buổi <?= $buoiSelected ?> thành công.</div>
            <?php endif; ?>
            <?php if ($sync): ?>
            <div class="alert-success" id="sync-alert"><i class="fas fa-sync me-2"></i>Đã đồng bộ điểm chuyên cần vào bảng điểm.</div>
            <?php endif; ?>
            <?php if (isset($_GET['error']) && $_GET['error'] === 'limit'): ?>
            <div class="alert-error" style="padding: 12px; margin-bottom: 16px; background: #fee; border-left: 4px solid #c00; color: #900;"><i class="fas fa-exclamation-triangle me-2"></i>Số buổi điểm danh vượt quá giới hạn cho phép!</div>
            <?php endif; ?>

            <div class="content-header">
                <div class="content-header__title">Chọn lớp học phần</div>
                <form method="get" action="<?= $baseUrl ?>/GiangVien/diemDanh" style="display: flex; gap: 8px;">
                    <input type="hidden" name="buoi" value="<?= $buoiSelected ?>">
                    <select name="maLopHocPhan" class="select" onchange="this.form.submit()">
                        <option value="">-- Chọn lớp học phần --</option>
                        <?php foreach ($lopHocPhanList as $lhp): ?>
                            <option value="<?= htmlspecialchars($lhp['MaLopHocPhan']) ?>" <?= ($maLopHocPhan === $lhp['MaLopHocPhan']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($lhp['MaLopHocPhan'] . ' - ' . ($lhp['TenMonHoc'] ?? '')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <?php if ($lopHocPhanSelected): ?>
            <div class="card">
                <div class="card__title">Bảng điểm danh - <?= htmlspecialchars($lopHocPhanSelected['TenMonHoc'] ?? $lopHocPhanSelected['MaMonHoc'] ?? '') ?> (<?= htmlspecialchars($maLopHocPhan) ?>)</div>
                <p style="font-size: 12px; color: #718096; margin-bottom: 12px;">1 tín chỉ = 5 ca = 15 tiết. Điểm chuyên cần = % tham gia buổi học × 10</p>

                <?php if (empty($bangDiemDanh)): ?>
                <div class="empty-state">Chưa có sinh viên đăng ký lớp học phần này.</div>
                <?php else: ?>
                <form action="<?= $baseUrl ?>/GiangVien/saveDiemDanh" method="POST">
                    <input type="hidden" name="MaLopHocPhan" value="<?= htmlspecialchars($maLopHocPhan) ?>">
                    <?php 
                        $soTinChi = (int)($lopHocPhanSelected['SoTinChi'] ?? 1);
                        $soBuoiToiDa = $soTinChi * 5 + 3; // Số tín * 5 + 3 buổi học bù
                    ?>
                    <div class="form-buoi" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                        <label style="font-weight: 500;">Điểm danh buổi thứ:</label>
                        <select name="BuoiThu_dummy" required class="select" style="width: 120px;" onchange="window.location.href='<?= $baseUrl ?>/GiangVien/diemDanh?maLopHocPhan=<?= urlencode($maLopHocPhan ?? '') ?>&buoi='+this.value">
                            <?php for ($i = 1; $i <= $soBuoiToiDa; $i++): ?>
                                <option value="<?= $i ?>" <?= ($buoiSelected == $i) ? 'selected' : '' ?>>Buổi <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                        <input type="hidden" name="BuoiThu" value="<?= $buoiSelected ?>">
                        <span style="font-size: 12px; color: #718096;">(Tối đa <?= $soBuoiToiDa ?> buổi)</span>
                        <button type="button" class="btn btn-sm" style="background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;" onclick="setAllStatus(1)"><i class="fas fa-check"></i> Tất cả có mặt</button>
                        <button type="button" class="btn btn-sm" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a;" onclick="setAllStatus(2)"><i class="fas fa-clock"></i> Tất cả muộn</button>
                        <button type="button" class="btn btn-sm" style="background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe;" onclick="setAllStatus(3)"><i class="fas fa-calendar-check"></i> Tất cả nghỉ có LD</button>
                        <button type="button" class="btn btn-sm" style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;" onclick="setAllStatus(4)"><i class="fas fa-times-circle"></i> Tất cả nghỉ</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu điểm danh</button>
                    </div>
                    <div class="table-wrapper" style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Mã SV</th>
                                    <th>Tên SV</th>
                                    <th>Mã học phần</th>
                                    <th>Có mặt</th>
                                    <th>Muộn</th>
                                    <th>Nghỉ có LD</th>
                                    <th>Nghỉ không LD</th>
                                    <th>Tổng buổi</th>
                                    <th>% tham gia</th>
                                    <th>Điểm CC</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bangDiemDanh as $i => $r): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($r['MaSinhVien'] ?? '') ?></strong></td>
                                    <td><?= htmlspecialchars($r['HoTen'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($r['MaLopHocPhan'] ?? $r['MaMonHoc'] ?? '') ?></td>
                                    <td style="text-align: center;"><span class="badge" style="background: #dcfce7; color: #166534;"><?= (int)($r['SoBuoiCoMat'] ?? 0) ?></span></td>
                                    <td style="text-align: center;"><span class="badge" style="background: #fef3c7; color: #92400e;"><?= (int)($r['SoBuoiMuon'] ?? 0) ?></span></td>
                                    <td style="text-align: center;"><span class="badge" style="background: #dbeafe; color: #1e40af;"><?= (int)($r['SoBuoiNghiCoLyDo'] ?? 0) ?></span></td>
                                    <td style="text-align: center;"><span class="badge" style="background: #fee2e2; color: #991b1b;"><?= (int)($r['SoBuoiNghiKhongLyDo'] ?? 0) ?></span></td>
                                    <td><?= (int)($r['TongBuoi'] ?? 0) ?></td>
                                    <td><?= number_format($r['PhanTramThamGia'] ?? 0, 1) ?>%</td>
                                    <td><strong><?= $r['DiemChuyenCan'] !== null ? number_format($r['DiemChuyenCan'], 2) : '-' ?></strong></td>
                                    <td style="text-align: center;">
                                        <?php 
                                        $maDK = $r['MaDangKy'];
                                        // Mặc định là nghỉ không lý do (4) nếu chưa điểm danh
                                        $trangThai = isset($diemDanhBuoiHienTai[$maDK]) ? (int)$diemDanhBuoiHienTai[$maDK] : 4;
                                        $tt = TRANG_THAI[$trangThai] ?? TRANG_THAI[4];
                                        ?>
                                        <select name="trangThai[<?= $maDK ?>]" class="select-trang-thai" data-ma="<?= $maDK ?>" style="padding: 4px 8px; border-radius: 4px; border: 1px solid #e2e8f0; font-size: 12px; font-weight: 500; cursor: pointer;" onchange="updateStatusLabel(this)">
                                            <?php foreach (TRANG_THAI as $key => $val): ?>
                                            <option value="<?= $key ?>" <?= ($trangThai == $key) ? 'selected' : '' ?> style="color: <?= $val['color'] ?>;"><?= $val['label'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </form>

                <div style="margin-top: 20px; padding: 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                        <div>
                            <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">Hướng dẫn sử dụng:</div>
                            <div style="font-size: 13px; color: #64748b;">
                                <i class="fas fa-save" style="color: #3b82f6; width: 16px;"></i> <strong>Lưu điểm danh</strong>: Chọn trạng thái cho từng sinh viên rồi bấm lưu<br>
                                <i class="fas fa-sync" style="color: #f59e0b; width: 16px;"></i> <strong>Đồng bộ</strong>: Bấm sau khi kết thúc môn học để cập nhật điểm chuyên cần vào bảng điểm
                            </div>
                        </div>
                        <a href="<?= $baseUrl ?>/GiangVien/dongBoDiemCC?maLopHocPhan=<?= urlencode($maLopHocPhan) ?>" class="btn" style="background: #f59e0b; color: white; border: none;" onclick="return confirm('Xác nhận đồng bộ điểm chuyên cần vào bảng điểm?\n\nChỉ nên đồng bộ khi đã hoàn thành tất cả các buổi điểm danh.');">
                            <i class="fas fa-sync"></i> Đồng bộ điểm CC
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php elseif ($maLopHocPhan): ?>
            <div class="card">
                <div class="empty-state">Bạn không có quyền xem lớp học phần này.</div>
            </div>
            <?php else: ?>
            <div class="card">
                <div class="empty-state">
                    <i class="fas fa-clipboard-list fa-2x mb-3"></i>
                    <p>Vui lòng chọn lớp học phần để xem bảng điểm danh.</p>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<script>
// Map trạng thái để hiển thị màu
const TRANG_THAI = {
    1: { label: 'Có mặt', color: '#22c55e' },
    2: { label: 'Muộn', color: '#f59e0b' },
    3: { label: 'Nghỉ có LD', color: '#3b82f6' },
    4: { label: 'Nghỉ không LD', color: '#ef4444' }
};

function setAllStatus(status) {
    document.querySelectorAll('.select-trang-thai').forEach(select => {
        select.value = status;
    });
}

function updateStatusLabel(select) {
    // Cập nhật màu select theo trạng thái
    const status = parseInt(select.value);
    const style = TRANG_THAI[status];
    select.style.color = style.color;
    select.style.borderColor = style.color;
}
</script>
</body>
</html>
