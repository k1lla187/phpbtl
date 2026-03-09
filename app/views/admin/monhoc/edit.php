<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>
<?php
$mh = $data['monhoc'] ?? null;
$nganhs = $data['nganhs'] ?? [];
?>

<?php if ($mh): ?>
<div class="page-header mb-4">
    <h4><i class="fas fa-book me-2"></i>Cập nhật môn học</h4>
    <a href="index.php?url=MonHoc/index" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="index.php?url=MonHoc/update/<?= htmlspecialchars($mh['MaMonHoc']) ?>" method="POST">
            <div class="mb-3">
                <label class="form-label">Mã môn</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($mh['MaMonHoc']) ?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Tên môn học <span class="text-danger">*</span></label>
                <input type="text" name="TenMonHoc" class="form-control" value="<?= htmlspecialchars($mh['TenMonHoc'] ?? '') ?>" required>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Số tín chỉ</label>
                    <input type="number" name="SoTinChi" class="form-control" value="<?= (int)($mh['SoTinChi'] ?? 0) ?>" min="1">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tiết LT</label>
                    <input type="number" name="SoTietLyThuyet" class="form-control" value="<?= (int)($mh['SoTietLyThuyet'] ?? 0) ?>" min="0">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tiết TH</label>
                    <input type="number" name="SoTietThucHanh" class="form-control" value="<?= (int)($mh['SoTietThucHanh'] ?? 0) ?>" min="0">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Ngành</label>
                <select name="MaNganh" class="form-select">
                    <option value="">-- Chọn ngành --</option>
                    <?php foreach ($nganhs as $n): ?>
                    <option value="<?= $n['MaNganh'] ?>" <?= ($mh['MaNganh'] ?? '') === $n['MaNganh'] ? 'selected' : '' ?>><?= htmlspecialchars($n['TenNganh']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu</button>
                <a href="index.php?url=MonHoc/index" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="alert alert-danger">Không tìm thấy môn học. <a href="index.php?url=MonHoc/index">Quay lại</a></div>
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
