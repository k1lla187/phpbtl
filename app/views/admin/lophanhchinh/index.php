<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <h4><i class="fas fa-users me-2"></i>Quản lý Lớp Hành Chính</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fas fa-plus me-2"></i>Thêm lớp
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
    <select class="form-select" id="filterNganh">
        <option value="">Tất cả ngành</option>
        <?php if (isset($data['nganhs'])): ?>
            <?php foreach ($data['nganhs'] as $n): ?>
            <option value="<?= $n['MaNganh'] ?>"><?= htmlspecialchars($n['TenNganh']) ?></option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Danh sách Lớp Hành Chính</h5>
        <span class="badge bg-primary"><?= count($data['lophanhchinhs'] ?? []) ?> lớp</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table" id="dataTable">
                <thead>
                    <tr>
                        <th>Mã Lớp</th>
                        <th>Tên Lớp</th>
                        <th>Ngành</th>
                        <th>Khóa học</th>
                        <th>Cố vấn</th>
                        <th>Sĩ số</th>
                        <th width="120">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['lophanhchinhs'])): ?>
                        <?php foreach($data['lophanhchinhs'] as $lop): ?>
                        <tr>
                            <td><strong><?= $lop['MaLop'] ?></strong></td>
                            <td><?= $lop['TenLop'] ?></td>
                            <td><span class="badge bg-info-light"><?= $lop['MaNganh'] ?? '' ?></span></td>
                            <td><?= $lop['KhoaHoc'] ?? '' ?></td>
                            <td><?= $lop['MaCoVan'] ?? '' ?></td>
                            <td><span class="badge bg-success"><?= $lop['SiSo'] ?? 0 ?> SV</span></td>
                            <td>
                                <div class="action-btns">
                                    <a href="index.php?url=LopHanhChinh/edit/<?= $lop['MaLop'] ?>" class="btn-action btn-action-edit" data-tooltip="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?url=LopHanhChinh/delete/<?= $lop['MaLop'] ?>" class="btn-action btn-action-delete" data-tooltip="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
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
                                    <i class="fas fa-users"></i>
                                    <h5>Chưa có lớp nào</h5>
                                    <p>Bấm nút "Thêm lớp" để tạo mới</p>
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
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Thêm Lớp Hành Chính</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?url=LopHanhChinh/store" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Mã Lớp <span class="text-danger">*</span></label>
                        <input type="text" name="MaLop" class="form-control" placeholder="VD: LOP01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tên Lớp <span class="text-danger">*</span></label>
                        <input type="text" name="TenLop" class="form-control" placeholder="Nhập tên lớp" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ngành <span class="text-danger">*</span></label>
                        <select name="MaNganh" class="form-select" required>
                            <option value="">-- Chọn ngành --</option>
                            <?php if (isset($data['nganhs'])): ?>
                                <?php foreach ($data['nganhs'] as $n): ?>
                                <option value="<?= $n['MaNganh'] ?>"><?= htmlspecialchars($n['TenNganh']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Khóa học</label>
                        <input type="number" name="KhoaHoc" class="form-control" placeholder="VD: 21" min="1" max="99">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cố vấn học tập</label>
                        <select name="MaCoVan" class="form-select">
                            <option value="">-- Chọn cố vấn --</option>
                            <?php if (isset($data['giangviens'])): ?>
                                <?php foreach ($data['giangviens'] as $gv): ?>
                                <option value="<?= $gv['MaGiangVien'] ?>"><?= htmlspecialchars($gv['HoTen']) ?> (<?= $gv['MaGiangVien'] ?>)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
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
// Tìm kiếm tự động
document.getElementById('searchInput').addEventListener('keyup', function() {
    filterTable();
});

// Filter theo ngành
document.getElementById('filterNganh').addEventListener('change', function() {
    filterTable();
});

function filterTable() {
    let searchFilter = document.getElementById('searchInput').value.toLowerCase();
    let nganhFilter = document.getElementById('filterNganh').value;
    let rows = document.querySelectorAll('#dataTable tbody tr');
    
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        let nganhCell = row.querySelector('td:nth-child(3)'); // Cột Ngành
        let nganhValue = nganhCell ? nganhCell.textContent.trim() : '';
        
        let matchSearch = text.includes(searchFilter);
        let matchNganh = !nganhFilter || nganhValue.includes(nganhFilter);
        
        row.style.display = (matchSearch && matchNganh) ? '' : 'none';
    });
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
