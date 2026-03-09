<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<?php
// Kiểm tra admin (cả 2 cách lưu session)
$isAdmin = false;
if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'Admin' || strtolower($_SESSION['user_role']) === 'admin')) {
    $isAdmin = true;
} elseif (isset($_SESSION['user']) && isset($_SESSION['user']['VaiTro']) && ($_SESSION['user']['VaiTro'] === 'Admin' || strtolower($_SESSION['user']['VaiTro']) === 'admin')) {
    $isAdmin = true;
}
$currentTrangThai = $data['trangThaiDiem'] ?? 0;
?>

<!-- Page Header -->
<div class="page-header">
    <h4><i class="fas fa-star me-2"></i>Quản lý Điểm</h4>
    <?php if (!empty($data['filterLop'])): ?>
    <div class="d-flex gap-2">
        <?php if ($currentTrangThai == 0): ?>
            <form method="POST" action="index.php?url=Diem/lock" class="d-inline">
                <input type="hidden" name="MaLopHocPhan" value="<?= htmlspecialchars($data['filterLop']) ?>">
                <button type="submit" class="btn btn-secondary" onclick="return confirm('Khóa điểm sẽ không cho phép giảng viên sửa điểm. Tiếp tục?')">
                    <i class="fas fa-lock me-2"></i>Khóa điểm
                </button>
            </form>
        <?php elseif ($currentTrangThai == 1 && $isAdmin): ?>
            <form method="POST" action="index.php?url=Diem/unlock" class="d-inline">
                <input type="hidden" name="MaLopHocPhan" value="<?= htmlspecialchars($data['filterLop']) ?>">
                <button type="submit" class="btn btn-warning" onclick="return confirm('Mở khóa để giảng viên có thể sửa điểm?')">
                    <i class="fas fa-unlock me-2"></i>Mở khóa
                </button>
            </form>
            <form method="POST" action="index.php?url=Diem/approve" class="d-inline">
                <input type="hidden" name="MaLopHocPhan" value="<?= htmlspecialchars($data['filterLop']) ?>">
                <button type="submit" class="btn btn-success" onclick="return confirm('Phê duyệt điểm sẽ khóa vĩnh viễn. Tiếp tục?')">
                    <i class="fas fa-check-double me-2"></i>Phê duyệt điểm
                </button>
            </form>
        <?php elseif ($currentTrangThai == 2 && $isAdmin): ?>
            <form method="POST" action="index.php?url=Diem/unapprove" class="d-inline">
                <input type="hidden" name="MaLopHocPhan" value="<?= htmlspecialchars($data['filterLop']) ?>">
                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Hủy phê duyệt để cho phép sửa điểm?')">
                    <i class="fas fa-undo me-2"></i>Hủy phê duyệt
                </button>
            </form>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php if (isset($_GET['success'])): ?>
<?php 
$successMsg = '';
switch($_GET['success']) {
    case '1': $successMsg = 'Khóa điểm thành công!'; break;
    case '2': $successMsg = 'Phê duyệt điểm thành công!'; break;
    case '3': $successMsg = 'Mở khóa điểm thành công!'; break;
    case '4': $successMsg = 'Hủy phê duyệt thành công!'; break;
}
?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($successMsg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<?php 
$errorMsg = '';
switch($_GET['error']) {
    case '1': $errorMsg = 'Khóa điểm thất bại!'; break;
    case '2': $errorMsg = 'Phê duyệt điểm thất bại!'; break;
    case '3': $errorMsg = 'Mở khóa điểm thất bại!'; break;
    case '4': $errorMsg = 'Hủy phê duyệt thất bại!'; break;
    case 'locked': $errorMsg = 'Điểm đã bị khóa! Chỉ admin mới có thể sửa.'; break;
    case 'unauthorized': $errorMsg = 'Bạn không có quyền thực hiện thao tác này!'; break;
}
?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($errorMsg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (!empty($data['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($data['error']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Filter Bar -->
<form method="get" action="index.php" class="filter-bar" id="filterForm">
    <input type="hidden" name="url" value="Diem/index">
    <select class="form-select" name="lop" id="filterLopHP" onchange="this.form.submit()">
        <option value="">Chọn lớp học phần</option>
        <?php if(isset($data['lophocphans'])): ?>
            <?php foreach($data['lophocphans'] as $lhp): ?>
                <option value="<?= htmlspecialchars($lhp['MaLopHocPhan']) ?>" <?= ($data['filterLop'] ?? '') === $lhp['MaLopHocPhan'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars(($lhp['MaLopHocPhan'] ?? '') . ' - ' . ($lhp['TenMonHoc'] ?? $lhp['MaMonHoc'] ?? '')) ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" class="form-control" placeholder="Tìm theo mã SV hoặc tên..." id="searchInput">
    </div>
</form>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-users"></i></div>
            <div class="stat-value"><?= $data['totalSV'] ?? 0 ?></div>
            <div class="stat-label">Tổng sinh viên</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-value"><?= $data['passed'] ?? 0 ?></div>
            <div class="stat-label">Đậu môn</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
            <div class="stat-value"><?= $data['failed'] ?? 0 ?></div>
            <div class="stat-label">Rớt môn</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon yellow"><i class="fas fa-clock"></i></div>
            <div class="stat-value"><?= $data['pending'] ?? 0 ?></div>
            <div class="stat-label">Chờ duyệt</div>
        </div>
    </div>
</div>

<?php
$isAdmin = isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'Admin' || strtolower($_SESSION['user_role']) === 'admin');
if (!$isAdmin && isset($_SESSION['user']) && isset($_SESSION['user']['VaiTro'])) {
    $isAdmin = ($_SESSION['user']['VaiTro'] === 'Admin' || strtolower($_SESSION['user']['VaiTro']) === 'admin');
}
$currentTrangThai = $data['trangThaiDiem'] ?? 0;
// Sau khi khóa điểm (TrangThai >= 1): KHÔNG ai được sửa (kể cả admin)
// Admin chỉ có thể mở khóa hoặc phê duyệt
$isEditable = ($currentTrangThai == 0);
?>

<!-- Data Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Bảng điểm sinh viên</h5>
        <?php if ($currentTrangThai > 0): ?>
            <span class="badge bg-warning text-dark"><i class="fas fa-lock me-1"></i>Điểm đã khóa - Vui lòng mở khóa để sửa</span>
        <?php endif; ?>
        <div>
            <a href="index.php?url=Diem/exportExcel<?= !empty($data['filterLop']) ? '&lop=' . urlencode($data['filterLop']) : '' ?>" class="btn btn-outline-primary btn-sm me-2" <?= empty($data['filterLop']) ? 'onclick="alert(\'Vui lòng chọn lớp học phần trước\'); return false;"' : '' ?>>
                <i class="fas fa-file-excel me-1"></i>Xuất Excel
            </a>
            <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="fas fa-print me-1"></i>In
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <form action="index.php?url=Diem/updateAll" method="POST">
            <?php if(!empty($data['filterLop'])): ?>
            <input type="hidden" name="MaLopHocPhan" value="<?= htmlspecialchars($data['filterLop']) ?>">
            <?php endif; ?>
            <div class="table-responsive">
                <table class="table" id="dataTable">
                    <thead>
                        <tr>
                            <th>MSSV</th>
                            <th>Họ tên</th>
                            <th>Lớp HP</th>
                            <th class="col-diem">Điểm QT</th>
                            <th class="col-diem">Điểm GK</th>
                            <th class="col-diem">Điểm CK</th>
                            <th>Điểm TB</th>
                            <th>Điểm chữ</th>
                            <th>Kết quả</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($data['bangdiem']) && count($data['bangdiem']) > 0): ?>
                            <?php foreach($data['bangdiem'] as $row): ?>
                            <tr data-mssv="<?= htmlspecialchars($row['MSSV'] ?? $row['MaSinhVien'] ?? '') ?>" data-hoten="<?= htmlspecialchars($row['HoTen'] ?? '') ?>">
                                <td><strong><?= $row['MSSV'] ?? $row['MaSinhVien'] ?? '' ?></strong></td>
                                <td><?= $row['HoTen'] ?? '' ?></td>
                                <td><?= $row['MaLopHocPhan'] ?? '' ?></td>
                                <td class="td-diem"><input type="number" step="0.1" min="0" max="10" name="diem[<?= $row['ID'] ?? 0 ?>][qt]" value="<?= $row['DiemCC'] ?? $row['DiemQT'] ?? '' ?>" class="form-control form-control-sm text-center input-diem" <?= !$isEditable ? 'disabled' : '' ?>></td>
                                <td class="td-diem"><input type="number" step="0.1" min="0" max="10" name="diem[<?= $row['ID'] ?? 0 ?>][gk]" value="<?= $row['DiemGK'] ?? '' ?>" class="form-control form-control-sm text-center input-diem" <?= !$isEditable ? 'disabled' : '' ?>></td>
                                <td class="td-diem"><input type="number" step="0.1" min="0" max="10" name="diem[<?= $row['ID'] ?? 0 ?>][ck]" value="<?= $row['DiemCK'] ?? '' ?>" class="form-control form-control-sm text-center input-diem" <?= !$isEditable ? 'disabled' : '' ?>></td>
                                <td class="text-center"><strong><?= $row['DiemTongKet'] ?? '-' ?></strong></td>
                                <td class="text-center"><?= $row['DiemChu'] ?? '-' ?></td>
                                <td class="text-center">
                                    <?php 
                                    $ketQua = $row['KetQua'] ?? '';
                                    if($ketQua === 'Đạt'): ?>
                                        <span class="grade-pass">Đạt</span>
                                    <?php elseif($ketQua === 'Không đạt'): ?>
                                        <span class="grade-fail">Không đạt</span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $trangThai = $row['TrangThaiDiem'] ?? 0;
                                    switch($trangThai) {
                                        case 0: echo '<span class="status-pending">Mới lưu</span>'; break;
                                        case 1: echo '<span class="status-locked">Đã khóa</span>'; break;
                                        case 2: echo '<span class="status-approved">Đã phê duyệt</span>'; break;
                                        default: echo '<span class="text-muted">-</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10">
                                    <div class="empty-state">
                                        <i class="fas fa-clipboard-list"></i>
                                        <h5>Chưa có dữ liệu điểm</h5>
                                        <p>Vui lòng chọn học kỳ và lớp học phần để xem điểm</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if(isset($data['bangdiem']) && count($data['bangdiem']) > 0 && $isEditable): ?>
            <div class="card-footer d-flex justify-content-end gap-2">
                <button type="submit" name="action" value="save" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Lưu điểm
                </button>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-check-double me-2"></i>Phê duyệt điểm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?url=Diem/approve" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Chọn lớp học phần</label>
                        <select name="MaLopHocPhan" class="form-select" required>
                            <option value="">-- Chọn lớp --</option>
                            <?php if(isset($data['lophocphans'])): ?>
                                <?php foreach($data['lophocphans'] as $lhp): ?>
                                    <option value="<?= htmlspecialchars($lhp['MaLopHocPhan']) ?>"><?= htmlspecialchars(($lhp['MaLopHocPhan'] ?? '') . ' - ' . ($lhp['TenMonHoc'] ?? $lhp['MaMonHoc'] ?? '')) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Tất cả điểm của lớp học phần này sẽ được phê duyệt.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-check me-2"></i>Phê duyệt</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase().trim();
    let rows = document.querySelectorAll('#dataTable tbody tr[data-mssv]');
    rows.forEach(row => {
        let mssv = (row.getAttribute('data-mssv') || '').toLowerCase();
        let hoten = (row.getAttribute('data-hoten') || '').toLowerCase();
        let show = !filter || mssv.includes(filter) || hoten.includes(filter);
        row.style.display = show ? '' : 'none';
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>