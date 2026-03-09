<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <h4><i class="fas fa-building me-2"></i>Quản lý Khoa</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fas fa-plus me-2"></i>Thêm khoa
    </button>
</div>

<!-- Error/Success Messages -->
<?php if (!empty($data['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($data['error']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (!empty($data['success'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($data['success']) ?>
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
        <h5 class="card-title mb-0">Danh sách Khoa</h5>
        <span class="badge bg-primary"><?= count($data['khoas'] ?? []) ?> khoa</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table" id="dataTable">
                <thead>
                    <tr>
                        <th>Mã Khoa</th>
                        <th>Tên Khoa</th>
                        <th>Ngày thành lập</th>
                        <th>Trưởng khoa</th>
                        <th width="120" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['khoas'])): ?>
                        <?php foreach($data['khoas'] as $khoa): ?>
                        <tr>
                            <td><strong><?= $khoa['MaKhoa'] ?></strong></td>
                            <td><?= $khoa['TenKhoa'] ?></td>
                            <td><?= isset($khoa['NgayThanhLap']) ? date('d/m/Y', strtotime($khoa['NgayThanhLap'])) : '' ?></td>
                            <td><?= $khoa['TruongKhoa'] ?? '' ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="index.php?url=Khoa/edit/<?= $khoa['MaKhoa'] ?>" 
                                       class="btn-action btn-action-edit" data-tooltip="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?url=Khoa/delete/<?= $khoa['MaKhoa'] ?>" 
                                       class="btn-action btn-action-delete" data-tooltip="Xóa"
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa khoa này?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-building"></i>
                                    <h5>Chưa có dữ liệu</h5>
                                    <p>Bấm nút "Thêm khoa" để tạo mới</p>
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
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Thêm Khoa Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?url=Khoa/store" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Mã Khoa <span class="text-danger">*</span></label>
                        <input type="text" name="MaKhoa" class="form-control" placeholder="VD: KHOA01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tên Khoa <span class="text-danger">*</span></label>
                        <input type="text" name="TenKhoa" class="form-control" placeholder="Nhập tên khoa" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ngày thành lập</label>
                        <input type="date" name="NgayThanhLap" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Trưởng khoa</label>
                        <input type="text" name="TruongKhoa" class="form-control" placeholder="Tên trưởng khoa">
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
