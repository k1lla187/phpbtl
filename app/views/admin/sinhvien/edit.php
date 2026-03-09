<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>
<?php
$sv = $data['sinhvien'] ?? null;
$lops = $data['lops'] ?? [];
?>

<?php if ($sv): ?>
<div class="page-header mb-4">
    <h4><i class="fas fa-user-edit me-2"></i>Cập nhật sinh viên</h4>
    <a href="index.php?url=SinhVien/index" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!empty($data['error'])): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($data['error']) ?>
        </div>
        <?php endif; ?>
        <form action="index.php?url=SinhVien/update/<?= htmlspecialchars($sv['MaSinhVien']) ?>" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mã sinh viên</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($sv['MaSinhVien']) ?>" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                    <input type="text" name="HoTen" class="form-control" value="<?= htmlspecialchars($sv['HoTen'] ?? '') ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ngày sinh</label>
                    <input type="date" name="NgaySinh" class="form-control" value="<?= !empty($sv['NgaySinh']) ? date('Y-m-d', strtotime($sv['NgaySinh'])) : '' ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Giới tính</label>
                    <select name="GioiTinh" class="form-select">
                        <option value="Nam" <?= ($sv['GioiTinh'] ?? '') === 'Nam' ? 'selected' : '' ?>>Nam</option>
                        <option value="Nữ" <?= ($sv['GioiTinh'] ?? '') === 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="Email" class="form-control" value="<?= htmlspecialchars($sv['Email'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="SoDienThoai" class="form-control" value="<?= htmlspecialchars($sv['SoDienThoai'] ?? '') ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text" name="DiaChi" class="form-control" value="<?= htmlspecialchars($sv['DiaChi'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Lớp hành chính</label>
                    <select name="MaLop" class="form-select">
                        <option value="">-- Chọn lớp --</option>
                        <?php foreach ($lops as $lop): ?>
                        <option value="<?= $lop['MaLop'] ?>" <?= ($sv['MaLop'] ?? '') === $lop['MaLop'] ? 'selected' : '' ?>><?= htmlspecialchars($lop['TenLop']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Trạng thái học tập</label>
                <select name="TrangThaiHocTap" class="form-select">
                    <option value="Đang học" <?= ($sv['TrangThaiHocTap'] ?? '') === 'Đang học' ? 'selected' : '' ?>>Đang học</option>
                    <option value="Bảo lưu" <?= ($sv['TrangThaiHocTap'] ?? '') === 'Bảo lưu' ? 'selected' : '' ?>>Bảo lưu</option>
                    <option value="Thôi học" <?= ($sv['TrangThaiHocTap'] ?? '') === 'Thôi học' ? 'selected' : '' ?>>Thôi học</option>
                    <option value="Tốt nghiệp" <?= ($sv['TrangThaiHocTap'] ?? '') === 'Tốt nghiệp' ? 'selected' : '' ?>>Tốt nghiệp</option>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu</button>
                <a href="index.php?url=SinhVien/index" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="alert alert-danger">Không tìm thấy sinh viên. <a href="index.php?url=SinhVien/index">Quay lại</a></div>
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
