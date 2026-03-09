<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>
<?php
$lhp = $data['lophocphan'] ?? null;
$monhocs = $data['monhocs'] ?? [];
$hockys = $data['hockys'] ?? [];
$giangviens = $data['giangviens'] ?? [];
?>

<?php if ($lhp): ?>
<div class="page-header mb-4">
    <h4><i class="fas fa-layer-group me-2"></i>Cập nhật lớp học phần</h4>
    <a href="index.php?url=LopHocPhan/index" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="index.php?url=LopHocPhan/update/<?= htmlspecialchars($lhp['MaLopHocPhan']) ?>" method="POST">
            <div class="mb-3">
                <label class="form-label">Mã lớp HP</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($lhp['MaLopHocPhan']) ?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Môn học <span class="text-danger">*</span></label>
                <select name="MaMonHoc" class="form-select" required>
                    <option value="">-- Chọn môn học --</option>
                    <?php foreach ($monhocs as $mh): ?>
                    <option value="<?= $mh['MaMonHoc'] ?>" <?= ($lhp['MaMonHoc'] ?? '') === $mh['MaMonHoc'] ? 'selected' : '' ?>><?= htmlspecialchars($mh['TenMonHoc']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Học kỳ <span class="text-danger">*</span></label>
                <select name="MaHocKy" class="form-select" required>
                    <option value="">-- Chọn học kỳ --</option>
                    <?php foreach ($hockys as $hk): ?>
                    <option value="<?= $hk['MaHocKy'] ?>" <?= ($lhp['MaHocKy'] ?? '') === $hk['MaHocKy'] ? 'selected' : '' ?>><?= htmlspecialchars($hk['TenHocKy']) ?> - <?= $hk['NamHoc'] ?? '' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Giảng viên <span class="text-danger">*</span></label>
                <select name="MaGiangVien" class="form-select" required>
                    <option value="">-- Chọn giảng viên --</option>
                    <?php foreach ($giangviens as $gv): ?>
                    <option value="<?= $gv['MaGiangVien'] ?>" <?= ($lhp['MaGiangVien'] ?? '') === $gv['MaGiangVien'] ? 'selected' : '' ?>><?= htmlspecialchars($gv['HoTen']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Phòng học</label>
                <input type="text" name="PhongHoc" class="form-control" value="<?= htmlspecialchars($lhp['PhongHoc'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Số lượng tối đa</label>
                <input type="number" name="SoLuongToiDa" class="form-control" value="<?= (int)($lhp['SoLuongToiDa'] ?? 60) ?>" min="1">
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="TrangThai" value="1" class="form-check-input" id="trangThai" <?= ($lhp['TrangThai'] ?? 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="trangThai">Đang mở đăng ký</label>
                </div>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="ChoPhepDangKyKhacKhoa" value="1" class="form-check-input" id="choPhepKhacKhoa" <?= ($lhp['ChoPhepDangKyKhacKhoa'] ?? 0) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="choPhepKhacKhoa">Cho phép sinh viên khác khoa đăng ký</label>
                </div>
                <small class="text-muted">Nếu checked, sinh viên các khoa khác có thể đăng ký môn này</small>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu</button>
                <a href="index.php?url=LopHocPhan/index" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="alert alert-danger">Không tìm thấy lớp học phần. <a href="index.php?url=LopHocPhan/index">Quay lại</a></div>
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
