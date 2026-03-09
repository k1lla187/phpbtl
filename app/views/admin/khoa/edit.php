<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>
<?php $khoa = $data['khoa'] ?? null; ?>

<?php if ($khoa): ?>
<div class="page-header mb-4">
    <h4><i class="fas fa-building me-2"></i>Cập nhật khoa</h4>
    <a href="index.php?url=Khoa/index" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="index.php?url=Khoa/update/<?= htmlspecialchars($khoa['MaKhoa']) ?>" method="POST">
            <div class="mb-3">
                <label class="form-label">Mã khoa</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($khoa['MaKhoa']) ?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Tên khoa <span class="text-danger">*</span></label>
                <input type="text" name="TenKhoa" class="form-control" value="<?= htmlspecialchars($khoa['TenKhoa'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Ngày thành lập</label>
                <input type="date" name="NgayThanhLap" class="form-control" value="<?= !empty($khoa['NgayThanhLap']) ? date('Y-m-d', strtotime($khoa['NgayThanhLap'])) : '' ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Trưởng khoa</label>
                <input type="text" name="TruongKhoa" class="form-control" value="<?= htmlspecialchars($khoa['TruongKhoa'] ?? '') ?>">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu</button>
                <a href="index.php?url=Khoa/index" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="alert alert-danger">Không tìm thấy khoa. <a href="index.php?url=Khoa/index">Quay lại</a></div>
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
