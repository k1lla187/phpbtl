<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <h4><i class="fas fa-chalkboard-teacher me-2"></i>Quản lý Giảng viên</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fas fa-plus me-2"></i>Thêm giảng viên
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
        <input type="text" class="form-control" placeholder="Tìm kiếm giảng viên..." id="searchInput">
    </div>
    <select class="form-select" id="filterKhoa">
        <option value="">Tất cả khoa</option>
        <?php if(isset($data['khoas'])): ?>
            <?php foreach($data['khoas'] as $khoa): ?>
                <option value="<?= $khoa['MaKhoa'] ?>"><?= $khoa['TenKhoa'] ?></option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table" id="dataTable">
                <thead>
                    <tr>
                        <th>Mã GV</th>
                        <th>Họ tên</th>
                        <th>Ngày sinh</th>
                        <th>Giới tính</th>
                        <th>Email</th>
                        <th>SĐT</th>
                        <th>Khoa</th>
                        <th>Học vị</th>
                        <th width="120">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($data['giangviens']) && count($data['giangviens']) > 0): ?>
                        <?php foreach($data['giangviens'] as $gv): ?>
                        <tr>
                            <td><strong><?= $gv['MaGiangVien'] ?></strong></td>
                            <td><?= $gv['HoTen'] ?></td>
                            <td><?= isset($gv['NgaySinh']) ? date('d/m/Y', strtotime($gv['NgaySinh'])) : '' ?></td>
                            <td><?= $gv['GioiTinh'] ?? '' ?></td>
                            <td><?= $gv['Email'] ?? '' ?></td>
                            <td><?= $gv['SoDienThoai'] ?? '' ?></td>
                            <td><span class="badge bg-info-light"><?= $gv['MaKhoa'] ?? '' ?></span></td>
                            <td><?= $gv['HocVi'] ?? '' ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="index.php?url=GiangVien/edit/<?= $gv['MaGiangVien'] ?>" class="btn-action btn-action-edit" data-tooltip="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?url=GiangVien/delete/<?= $gv['MaGiangVien'] ?>" class="btn-action btn-action-delete" data-tooltip="Xóa" onclick="return confirm('Bạn có chắc muốn xóa?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                    <h5>Chưa có giảng viên nào</h5>
                                    <p>Nhấn nút "Thêm giảng viên" để bắt đầu</p>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Thêm giảng viên mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?url=GiangVien/store" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mã giảng viên <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="MaGiangVien" id="inputMaGiangVien" class="form-control" required>
                                <span class="input-group-text bg-success text-white" id="autoIdBadge" style="display:none;">
                                    <i class="fas fa-magic me-1"></i>Tự động
                                </span>
                            </div>
                            <small class="text-muted">Mã sẽ được tự động điền khi mở form</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                            <input type="text" name="HoTen" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" name="NgaySinh" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giới tính</label>
                            <select name="GioiTinh" class="form-select">
                                <option value="Nam">Nam</option>
                                <option value="Nữ">Nữ</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="Email" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="SoDienThoai" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Khoa <span class="text-danger">*</span></label>
                            <select name="MaKhoa" class="form-select" required>
                                <option value="">-- Chọn khoa --</option>
                                <?php if(isset($data['khoas'])): ?>
                                    <?php foreach($data['khoas'] as $khoa): ?>
                                        <option value="<?= $khoa['MaKhoa'] ?>"><?= $khoa['TenKhoa'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Học vị</label>
                            <select name="HocVi" class="form-select">
                                <option value="Cử nhân">Cử nhân</option>
                                <option value="Thạc sĩ">Thạc sĩ</option>
                                <option value="Tiến sĩ">Tiến sĩ</option>
                                <option value="Phó Giáo sư">Phó Giáo sư</option>
                                <option value="Giáo sư">Giáo sư</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Tự động lấy mã giảng viên khi mở modal
document.getElementById('addModal').addEventListener('show.bs.modal', function() {
    fetch('index.php?url=GiangVien/getNextId')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.nextId) {
                document.getElementById('inputMaGiangVien').value = data.nextId;
                document.getElementById('autoIdBadge').style.display = 'flex';
            }
        })
        .catch(err => console.log('Không thể lấy mã tự động'));
});

// Tìm kiếm tự động
document.getElementById('searchInput').addEventListener('keyup', function() {
    filterTable();
});

// Filter theo khoa
document.getElementById('filterKhoa').addEventListener('change', function() {
    filterTable();
});

function filterTable() {
    let searchFilter = document.getElementById('searchInput').value.toLowerCase();
    let khoaFilter = document.getElementById('filterKhoa').value;
    let rows = document.querySelectorAll('#dataTable tbody tr');
    
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        let khoaCell = row.querySelector('td:nth-child(7)'); // Cột Khoa
        let khoaValue = khoaCell ? khoaCell.textContent.trim() : '';
        
        let matchSearch = text.includes(searchFilter);
        let matchKhoa = !khoaFilter || khoaValue.includes(khoaFilter);
        
        row.style.display = (matchSearch && matchKhoa) ? '' : 'none';
    });
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>