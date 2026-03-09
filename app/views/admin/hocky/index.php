<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <h4><i class="fas fa-calendar-alt me-2"></i>Quản lý Học kỳ</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fas fa-plus me-2"></i>Thêm học kỳ
    </button>
</div>

<?php if (!empty($data['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($data['error']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Filter Bar -->
<div class="filter-bar">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" class="form-control" placeholder="Tìm kiếm..." id="searchInput">
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Danh sách Học kỳ</h5>
        <span class="badge bg-primary"><?= count($data['hockys'] ?? []) ?> học kỳ</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table" id="dataTable">
                <thead>
                    <tr>
                        <th>Mã HK</th>
                        <th>Tên học kỳ</th>
                        <th>Năm học</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Trạng thái</th>
                        <th width="120">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['hockys'])): ?>
                        <?php foreach($data['hockys'] as $hk): ?>
                        <tr>
                            <td><strong><?= $hk['MaHocKy'] ?></strong></td>
                            <td><?= $hk['TenHocKy'] ?></td>
                            <td><span class="badge bg-info"><?= $hk['NamHoc'] ?? '' ?></span></td>
                            <td><?= isset($hk['NgayBatDau']) ? date('d/m/Y', strtotime($hk['NgayBatDau'])) : '' ?></td>
                            <td><?= isset($hk['NgayKetThuc']) ? date('d/m/Y', strtotime($hk['NgayKetThuc'])) : '' ?></td>
                            <td>
                                <?php 
                                $now = time();
                                $start = isset($hk['NgayBatDau']) ? strtotime($hk['NgayBatDau']) : 0;
                                $end = isset($hk['NgayKetThuc']) ? strtotime($hk['NgayKetThuc']) : 0;
                                if($now >= $start && $now <= $end): ?>
                                    <span class="status-active">Đang diễn ra</span>
                                <?php elseif($now < $start): ?>
                                    <span class="status-pending">Sắp tới</span>
                                <?php else: ?>
                                    <span class="status-inactive">Đã kết thúc</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="index.php?url=HocKy/edit/<?= $hk['MaHocKy'] ?>" class="btn-action btn-action-edit" data-tooltip="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?url=HocKy/delete/<?= $hk['MaHocKy'] ?>" class="btn-action btn-action-delete" data-tooltip="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fas fa-calendar-alt"></i>
                                    <h5>Chưa có học kỳ nào</h5>
                                    <p>Bấm nút "Thêm học kỳ" để tạo mới</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Thêm Học kỳ Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?url=HocKy/store" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Mã học kỳ <span class="text-danger">*</span></label>
                        <input type="text" name="MaHocKy" class="form-control" placeholder="VD: HK01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tên học kỳ <span class="text-danger">*</span></label>
                        <input type="text" name="TenHocKy" class="form-control" placeholder="VD: Học kỳ 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Năm học</label>
                        <input type="number" name="NamHoc" class="form-control" placeholder="VD: 2024" min="2000" max="2100">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày bắt đầu</label>
                                <input type="date" name="NgayBatDau" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày kết thúc</label>
                                <input type="date" name="NgayKetThuc" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Lưu lại</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#dataTable tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
