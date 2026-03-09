<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <h4><i class="fas fa-clipboard-list me-2"></i>Quản lý Đăng ký học</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fas fa-plus me-2"></i>Thêm đăng ký
    </button>
</div>

<!-- Error Message -->
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
        <input type="text" class="form-control" placeholder="Tìm theo MSSV..." id="searchInput">
    </div>
    <select class="form-select" id="filterLopHP">
        <option value="">Tất cả lớp HP</option>
        <?php if (!empty($data['lophocphans'])): ?>
            <?php foreach($data['lophocphans'] as $lhp): ?>
            <option value="<?= $lhp['MaLopHocPhan'] ?>"><?= $lhp['MaLopHocPhan'] ?></option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-clipboard-list"></i></div>
            <div class="stat-value"><?= count($data['dangkyhocs'] ?? []) ?></div>
            <div class="stat-label">Tổng đăng ký</div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Danh sách Đăng ký học</h5>
        <span class="badge bg-primary"><?= count($data['dangkyhocs'] ?? []) ?> đăng ký</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table" id="dataTable">
                <thead>
                    <tr>
                        <th>Mã ĐK</th>
                        <th>MSSV</th>
                        <th>Họ tên</th>
                        <th>Lớp HP</th>
                        <th>Môn học</th>
                        <th>Ngày đăng ký</th>
                        <th>Điểm TK</th>
                        <th>Kết quả</th>
                        <th width="100" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['dangkyhocs'])): ?>
                        <?php foreach($data['dangkyhocs'] as $dk): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($dk['MaDangKy']) ?></strong></td>
                            <td><?= htmlspecialchars($dk['MaSinhVien']) ?></td>
                            <td><?= htmlspecialchars($dk['TenSinhVien'] ?? '') ?></td>
                            <td><span class="badge bg-info"><?= htmlspecialchars($dk['MaLopHocPhan']) ?></span></td>
                            <td><?= htmlspecialchars($dk['TenMonHoc'] ?? '') ?></td>
                            <td><?= isset($dk['NgayDangKy']) ? date('d/m/Y', strtotime($dk['NgayDangKy'])) : '-' ?></td>
                            <td><strong><?= $dk['DiemTongKet'] ?? '-' ?></strong></td>
                            <td>
                                <?php if(($dk['KetQua'] ?? '') == 'Đạt'): ?>
                                    <span class="badge bg-success">Đạt</span>
                                <?php elseif(isset($dk['KetQua']) && $dk['KetQua']): ?>
                                    <span class="badge bg-danger">Không đạt</span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="index.php?url=DangKyHoc/delete/<?= $dk['MaDangKy'] ?>" 
                                   class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa đăng ký này?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-clipboard-list"></i>
                                    <h5>Chưa có đăng ký nào</h5>
                                    <p>Bấm nút "Thêm đăng ký" để tạo mới</p>
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
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Thêm Đăng ký học</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?url=DangKyHoc/store" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Sinh viên <span class="text-danger">*</span></label>
                        <select name="MaSinhVien" class="form-select" required>
                            <option value="">-- Chọn sinh viên --</option>
                            <?php if (!empty($data['sinhviens'])): ?>
                                <?php foreach($data['sinhviens'] as $sv): ?>
                                <option value="<?= $sv['MaSinhVien'] ?>">
                                    <?= htmlspecialchars($sv['MaSinhVien'] . ' - ' . $sv['HoTen']) ?>
                                </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lớp học phần <span class="text-danger">*</span></label>
                        <select name="MaLopHocPhan" class="form-select" required>
                            <option value="">-- Chọn lớp học phần --</option>
                            <?php if (!empty($data['lophocphans'])): ?>
                                <?php foreach($data['lophocphans'] as $lhp): ?>
                                <option value="<?= $lhp['MaLopHocPhan'] ?>">
                                    <?= htmlspecialchars($lhp['MaLopHocPhan']) ?>
                                </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Đăng ký</button>
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

document.getElementById('filterLopHP').addEventListener('change', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#dataTable tbody tr');
    rows.forEach(row => {
        let lhp = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
        row.style.display = !filter || lhp.includes(filter) ? '' : 'none';
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
