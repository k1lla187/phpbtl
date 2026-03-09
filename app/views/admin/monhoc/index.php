<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <h4><i class="fas fa-book me-2"></i>Quản lý Môn học</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fas fa-plus me-2"></i>Thêm môn học
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
        <input type="text" class="form-control" placeholder="Tìm kiếm môn học..." id="searchInput">
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
        <h5 class="card-title mb-0">Danh sách Môn học</h5>
        <span class="badge bg-primary"><?= count($data['monhocs'] ?? []) ?> môn</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table" id="dataTable">
                <thead>
                    <tr>
                        <th>Mã môn</th>
                        <th>Tên môn học</th>
                        <th>Số tín chỉ</th>
                        <th>LT</th>
                        <th>TH</th>
                        <th>Ngành</th>
                        <th width="120">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['monhocs'])): ?>
                        <?php foreach($data['monhocs'] as $mh): ?>
                        <tr>
                            <td><strong><?= $mh['MaMonHoc'] ?></strong></td>
                            <td><?= $mh['TenMonHoc'] ?? $mh['TenMon'] ?? '' ?></td>
                            <td><span class="badge bg-info"><?= $mh['SoTinChi'] ?? 0 ?> TC</span></td>
                            <td><?= $mh['SoTietLyThuyet'] ?? 0 ?></td>
                            <td><?= $mh['SoTietThucHanh'] ?? 0 ?></td>
                            <td><?= $mh['MaNganh'] ?? '' ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="index.php?url=MonHoc/edit/<?= $mh['MaMonHoc'] ?>" class="btn-action btn-action-edit" data-tooltip="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?url=MonHoc/delete/<?= $mh['MaMonHoc'] ?>" class="btn-action btn-action-delete" data-tooltip="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
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
                                    <i class="fas fa-book"></i>
                                    <h5>Chưa có môn học nào</h5>
                                    <p>Bấm nút "Thêm môn học" để tạo mới</p>
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
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Thêm Môn học Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?url=MonHoc/store" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Mã môn <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="MaMonHoc" id="inputMaMonHoc" class="form-control" placeholder="VD: MH001" required>
                            <span class="input-group-text bg-success text-white" id="autoIdBadge" style="display:none;">
                                <i class="fas fa-magic me-1"></i>Tự động
                            </span>
                        </div>
                        <small class="text-muted">Mã sẽ được tự động điền khi mở form</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tên môn học <span class="text-danger">*</span></label>
                        <input type="text" name="TenMonHoc" class="form-control" placeholder="Nhập tên môn học" required>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Số tín chỉ</label>
                                <input type="number" name="SoTinChi" class="form-control" value="3" min="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Tiết LT</label>
                                <input type="number" name="SoTietLyThuyet" class="form-control" value="30" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Tiết TH</label>
                                <input type="number" name="SoTietThucHanh" class="form-control" value="15" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ngành</label>
                        <select name="MaNganh" class="form-select">
                            <option value="">-- Chọn ngành --</option>
                            <?php if (isset($data['nganhs'])): ?>
                                <?php foreach ($data['nganhs'] as $n): ?>
                                <option value="<?= $n['MaNganh'] ?>"><?= htmlspecialchars($n['TenNganh']) ?></option>
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
// Tự động lấy mã môn học khi mở modal
document.getElementById('addModal').addEventListener('show.bs.modal', function() {
    fetch('index.php?url=MonHoc/getNextId')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.nextId) {
                document.getElementById('inputMaMonHoc').value = data.nextId;
                document.getElementById('autoIdBadge').style.display = 'flex';
            }
        })
        .catch(err => console.log('Không thể lấy mã tự động'));
});

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
        let nganhCell = row.querySelector('td:nth-child(6)'); // Cột Ngành
        let nganhValue = nganhCell ? nganhCell.textContent.trim() : '';
        
        let matchSearch = text.includes(searchFilter);
        let matchNganh = !nganhFilter || nganhValue.includes(nganhFilter);
        
        row.style.display = (matchSearch && matchNganh) ? '' : 'none';
    });
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
