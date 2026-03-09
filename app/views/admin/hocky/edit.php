<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>
<?php $hk = $data['hocky'] ?? null; ?>

<?php if ($hk): ?>
<div class="page-header mb-4">
    <h4><i class="fas fa-calendar-alt me-2"></i>Cập nhật học kỳ</h4>
    <a href="index.php?url=HocKy/index" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="index.php?url=HocKy/update/<?= htmlspecialchars($hk['MaHocKy']) ?>" method="POST">
            <div class="mb-3">
                <label class="form-label">Mã học kỳ</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($hk['MaHocKy']) ?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Tên học kỳ <span class="text-danger">*</span></label>
                <input type="text" name="TenHocKy" class="form-control" value="<?= htmlspecialchars($hk['TenHocKy'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Năm học</label>
                <input type="number" name="NamHoc" class="form-control" value="<?= (int)($hk['NamHoc'] ?? 0) ?: '' ?>" placeholder="VD: 2024" min="2000" max="2100">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ngày bắt đầu</label>
                    <input type="date" name="NgayBatDau" class="form-control" value="<?= !empty($hk['NgayBatDau']) ? date('Y-m-d', strtotime($hk['NgayBatDau'])) : '' ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ngày kết thúc</label>
                    <input type="date" name="NgayKetThuc" class="form-control" value="<?= !empty($hk['NgayKetThuc']) ? date('Y-m-d', strtotime($hk['NgayKetThuc'])) : '' ?>">
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu</button>
                <a href="index.php?url=HocKy/index" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="alert alert-danger">Không tìm thấy học kỳ. <a href="index.php?url=HocKy/index">Quay lại</a></div>
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
