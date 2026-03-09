<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>
<?php
$lop = $data['lop'] ?? null;
$nganhs = $data['nganhs'] ?? [];
$giangviens = $data['giangviens'] ?? [];
?>

<?php if ($lop): ?>
<div class="page-header mb-4">
    <h4><i class="fas fa-users me-2"></i>Cập nhật lớp hành chính</h4>
    <a href="index.php?url=LopHanhChinh/index" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="index.php?url=LopHanhChinh/update/<?= htmlspecialchars($lop['MaLop']) ?>" method="POST">
            <div class="mb-3">
                <label class="form-label">Mã lớp</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($lop['MaLop']) ?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Tên lớp <span class="text-danger">*</span></label>
                <input type="text" name="TenLop" class="form-control" value="<?= htmlspecialchars($lop['TenLop'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Ngành <span class="text-danger">*</span></label>
                <select name="MaNganh" class="form-select" required>
                    <option value="">-- Chọn ngành --</option>
                    <?php foreach ($nganhs as $n): ?>
                    <option value="<?= $n['MaNganh'] ?>" <?= ($lop['MaNganh'] ?? '') === $n['MaNganh'] ? 'selected' : '' ?>><?= htmlspecialchars($n['TenNganh']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Khóa học</label>
                <input type="number" name="KhoaHoc" class="form-control" value="<?= (int)($lop['KhoaHoc'] ?? 0) ?: '' ?>" placeholder="VD: 21" min="1" max="99">
            </div>
            <div class="mb-3">
                <label class="form-label">Cố vấn học tập</label>
                <select name="MaCoVan" class="form-select">
                    <option value="">-- Chọn cố vấn --</option>
                    <?php foreach ($giangviens as $gv): ?>
                    <option value="<?= $gv['MaGiangVien'] ?>" <?= ($lop['MaCoVan'] ?? '') === $gv['MaGiangVien'] ? 'selected' : '' ?>><?= htmlspecialchars($gv['HoTen']) ?> (<?= $gv['MaGiangVien'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu</button>
                <a href="index.php?url=LopHanhChinh/index" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="alert alert-danger">Không tìm thấy lớp. <a href="index.php?url=LopHanhChinh/index">Quay lại</a></div>
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
