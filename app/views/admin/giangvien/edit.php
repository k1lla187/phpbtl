<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>
<?php
$gv = $data['giangvien'] ?? null;
$khoas = $data['khoas'] ?? [];
?>

<?php if ($gv): ?>
<div class="page-header mb-4">
    <h4><i class="fas fa-user-edit me-2"></i>Cập nhật giảng viên</h4>
    <a href="index.php?url=GiangVien/index" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="index.php?url=GiangVien/update/<?= htmlspecialchars($gv['MaGiangVien']) ?>" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mã giảng viên</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($gv['MaGiangVien']) ?>" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                    <input type="text" name="HoTen" class="form-control" value="<?= htmlspecialchars($gv['HoTen'] ?? '') ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ngày sinh</label>
                    <input type="date" name="NgaySinh" class="form-control" value="<?= !empty($gv['NgaySinh']) ? date('Y-m-d', strtotime($gv['NgaySinh'])) : '' ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Giới tính</label>
                    <select name="GioiTinh" class="form-select">
                        <option value="Nam" <?= ($gv['GioiTinh'] ?? '') === 'Nam' ? 'selected' : '' ?>>Nam</option>
                        <option value="Nữ" <?= ($gv['GioiTinh'] ?? '') === 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="Email" class="form-control" value="<?= htmlspecialchars($gv['Email'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="SoDienThoai" class="form-control" value="<?= htmlspecialchars($gv['SoDienThoai'] ?? '') ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Khoa <span class="text-danger">*</span></label>
                    <select name="MaKhoa" class="form-select" required>
                        <option value="">-- Chọn khoa --</option>
                        <?php foreach ($khoas as $k): ?>
                        <option value="<?= $k['MaKhoa'] ?>" <?= ($gv['MaKhoa'] ?? '') === $k['MaKhoa'] ? 'selected' : '' ?>><?= htmlspecialchars($k['TenKhoa']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Học vị</label>
                    <select name="HocVi" class="form-select">
                        <option value="Cử nhân" <?= ($gv['HocVi'] ?? '') === 'Cử nhân' ? 'selected' : '' ?>>Cử nhân</option>
                        <option value="Thạc sĩ" <?= ($gv['HocVi'] ?? '') === 'Thạc sĩ' ? 'selected' : '' ?>>Thạc sĩ</option>
                        <option value="Tiến sĩ" <?= ($gv['HocVi'] ?? '') === 'Tiến sĩ' ? 'selected' : '' ?>>Tiến sĩ</option>
                        <option value="Phó Giáo sư" <?= ($gv['HocVi'] ?? '') === 'Phó Giáo sư' ? 'selected' : '' ?>>Phó Giáo sư</option>
                        <option value="Giáo sư" <?= ($gv['HocVi'] ?? '') === 'Giáo sư' ? 'selected' : '' ?>>Giáo sư</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="TrangThai" value="1" class="form-check-input" id="trangThai" <?= ($gv['TrangThai'] ?? 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="trangThai">Đang giảng dạy</label>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu</button>
                <a href="index.php?url=GiangVien/index" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="alert alert-danger">Không tìm thấy giảng viên. <a href="index.php?url=GiangVien/index">Quay lại</a></div>
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
