<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>
<?php
$nganh = $data['nganh'] ?? null;
$khoas = $data['khoas'] ?? [];
?>

<?php if ($nganh): ?>
<div class="page-header mb-4">
    <h4><i class="fas fa-graduation-cap me-2"></i>Cập nhật ngành</h4>
    <a href="index.php?url=Nganh/index" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="index.php?url=Nganh/update/<?= htmlspecialchars($nganh['MaNganh']) ?>" method="POST">
            <div class="mb-3">
                <label class="form-label">Mã ngành</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($nganh['MaNganh']) ?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Tên ngành <span class="text-danger">*</span></label>
                <input type="text" name="TenNganh" class="form-control" value="<?= htmlspecialchars($nganh['TenNganh'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Khoa <span class="text-danger">*</span></label>
                <select name="MaKhoa" class="form-select" required>
                    <option value="">-- Chọn khoa --</option>
                    <?php foreach ($khoas as $k): ?>
                    <option value="<?= $k['MaKhoa'] ?>" <?= ($nganh['MaKhoa'] ?? '') === $k['MaKhoa'] ? 'selected' : '' ?>><?= htmlspecialchars($k['TenKhoa']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu</button>
                <a href="index.php?url=Nganh/index" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="alert alert-danger">Không tìm thấy ngành. <a href="index.php?url=Nganh/index">Quay lại</a></div>
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
