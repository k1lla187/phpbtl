<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <h4><i class="fas fa-layer-group me-2"></i>Quản lý Lớp Học Phần</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fas fa-plus me-2"></i>Thêm lớp HP
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
    <select class="form-select" id="filterHocKy">
        <option value="">Chọn học kỳ</option>
        <?php if(isset($data['hockys'])): ?>
            <?php foreach($data['hockys'] as $hk): ?>
                <option value="<?= $hk['MaHocKy'] ?>"><?= $hk['TenHocKy'] ?> - <?= $hk['NamHoc'] ?></option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
    <select class="form-select" id="filterMonHoc">
        <option value="">Chọn môn học</option>
        <?php if(isset($data['monhocs'])): ?>
            <?php foreach($data['monhocs'] as $mh): ?>
                <option value="<?= $mh['MaMonHoc'] ?>"><?= htmlspecialchars($mh['TenMonHoc'] ?? '') ?></option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" class="form-control" placeholder="Tìm kiếm..." id="searchInput">
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-layer-group"></i></div>
            <div class="stat-value"><?= $data['totalLHP'] ?? count($data['lophps'] ?? []) ?></div>
            <div class="stat-label">Tổng lớp HP</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="stat-value"><?= $data['totalGV'] ?? 0 ?></div>
            <div class="stat-label">Giảng viên</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon yellow"><i class="fas fa-book"></i></div>
            <div class="stat-value"><?= $data['totalMH'] ?? count($data['monhocs'] ?? []) ?></div>
            <div class="stat-label">Môn học</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-user-graduate"></i></div>
            <div class="stat-value"><?= $data['totalSV'] ?? 0 ?></div>
            <div class="stat-label">SV đăng ký</div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Danh sách Lớp Học Phần</h5>
        <span class="badge bg-primary"><?= count($data['lophps'] ?? []) ?> lớp</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table" id="dataTable">
                <thead>
                    <tr>
                        <th>Mã lớp HP</th>
                        <th>Môn học</th>
                        <th>Giảng viên</th>
                        <th>Học kỳ</th>
                        <th>Sĩ số</th>
                        <th>Trạng thái</th>
                        <th width="150">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['lophps'])): ?>
                        <?php foreach($data['lophps'] as $lhp): ?>
                        <tr data-mahocky="<?= htmlspecialchars($lhp['MaHocKy'] ?? '') ?>" data-mamonhoc="<?= htmlspecialchars($lhp['MaMonHoc'] ?? '') ?>">
                            <td><strong><?= $lhp['MaLopHocPhan'] ?? '' ?></strong></td>
                            <td><?= htmlspecialchars($lhp['TenMonHoc'] ?? '') ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2"><?= substr($lhp['TenGV'] ?? 'N', 0, 1) ?></div>
                                    <?= htmlspecialchars($lhp['TenGV'] ?? 'Chưa phân công') ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($lhp['TenHocKy'] ?? '') ?></td>
                            <td><span class="badge bg-info"><?= $lhp['SiSo'] ?? 0 ?> SV</span></td>
                            <td>
                                <?php if(($lhp['TrangThai'] ?? 1) == 1): ?>
                                    <span class="status-active">Đang mở</span>
                                <?php else: ?>
                                    <span class="status-inactive">Đã đóng</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn-action btn-action-view" data-tooltip="Phân công" data-bs-toggle="modal" data-bs-target="#assignModal" 
                                        onclick="setAssignLop('<?= $lhp['MaLopHocPhan'] ?? '' ?>')">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
                                    <a href="index.php?url=LopHocPhan/edit/<?= $lhp['MaLopHocPhan'] ?? '' ?>" class="btn-action btn-action-edit" data-tooltip="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?url=LopHocPhan/delete/<?= $lhp['MaLopHP'] ?? $lhp['MaLopHocPhan'] ?? '' ?>" class="btn-action btn-action-delete" data-tooltip="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
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
                                    <i class="fas fa-layer-group"></i>
                                    <h5>Chưa có dữ liệu</h5>
                                    <p>Bấm nút "Thêm lớp HP" để tạo mới</p>
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
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Thêm Lớp Học Phần</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?url=LopHocPhan/store" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Mã lớp HP <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="MaLopHocPhan" id="inputMaLopHocPhan" class="form-control" placeholder="VD: LHP001" required>
                            <span class="input-group-text bg-success text-white" id="autoIdBadge" style="display:none;">
                                <i class="fas fa-magic me-1"></i>Tự động
                            </span>
                        </div>
                        <small class="text-muted">Mã sẽ được tự động điền khi mở form</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Môn học <span class="text-danger">*</span></label>
                        <select name="MaMonHoc" class="form-select" required>
                            <option value="">-- Chọn môn học --</option>
                            <?php if(isset($data['monhocs'])): ?>
                                <?php foreach($data['monhocs'] as $mh): ?>
                                    <option value="<?= $mh['MaMonHoc'] ?>"><?= htmlspecialchars($mh['TenMonHoc'] ?? '') ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Giảng viên <span class="text-danger">*</span></label>
                        <select name="MaGiangVien" class="form-select" required>
                            <option value="">-- Chọn giảng viên --</option>
                            <?php if(isset($data['giangviens'])): ?>
                                <?php foreach($data['giangviens'] as $gv): ?>
                                    <option value="<?= $gv['MaGiangVien'] ?>"><?= $gv['HoTen'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
<div class="mb-3">
                        <label class="form-label">Học kỳ <span class="text-danger">*</span></label>
                        <select name="MaHocKy" class="form-select" required>
                            <option value="">-- Chọn học kỳ --</option>
                            <?php if(isset($data['hockys'])): ?>
                                <?php foreach($data['hockys'] as $hk): ?>
                                <option value="<?= $hk['MaHocKy'] ?>"><?= htmlspecialchars($hk['TenHocKy']) ?> - <?= $hk['NamHoc'] ?? '' ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phòng học</label>
                        <input type="text" name="PhongHoc" class="form-control" placeholder="VD: A101">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số lượng tối đa</label>
                        <input type="number" name="SoLuongToiDa" class="form-control" placeholder="Mặc định: 60" value="60" min="1">
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

<!-- Assign Student Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Thêm sinh viên vào lớp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?url=LopHocPhan/assignStudent" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="MaLopHocPhan" id="inputMaLop">
                    <div class="mb-3">
                        <label class="form-label">Mã lớp HP</label>
                        <input type="text" class="form-control" id="displayMaLop" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mã sinh viên <span class="text-danger">*</span></label>
                        <input type="text" name="MaSinhVien" class="form-control" placeholder="Nhập MSSV" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-user-plus me-2"></i>Thêm vào lớp</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
}
</style>

<script>
function setAssignLop(maLop) {
    document.getElementById('inputMaLop').value = maLop;
    document.getElementById('displayMaLop').value = maLop;
}

// Tự động lấy mã lớp học phần khi mở modal
document.getElementById('addModal').addEventListener('show.bs.modal', function() {
    fetch('index.php?url=LopHocPhan/getNextId')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.nextId) {
                document.getElementById('inputMaLopHocPhan').value = data.nextId;
                document.getElementById('autoIdBadge').style.display = 'flex';
            }
        })
        .catch(err => console.log('Không thể lấy mã tự động'));
});

// Tìm kiếm tự động
document.getElementById('searchInput').addEventListener('keyup', function() {
    filterTable();
});

// Filter theo học kỳ
document.getElementById('filterHocKy').addEventListener('change', function() {
    filterTable();
});

// Filter theo môn học
document.getElementById('filterMonHoc').addEventListener('change', function() {
    filterTable();
});

function filterTable() {
    let searchFilter = document.getElementById('searchInput').value.toLowerCase();
    let hocKyFilter = document.getElementById('filterHocKy').value;
    let monHocFilter = document.getElementById('filterMonHoc').value;
    let rows = document.querySelectorAll('#dataTable tbody tr');
    
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        let rowMaHocKy = row.getAttribute('data-mahocky') || '';
        let rowMaMonHoc = row.getAttribute('data-mamonhoc') || '';
        
        let matchSearch = text.includes(searchFilter);
        let matchHocKy = !hocKyFilter || rowMaHocKy === hocKyFilter;
        let matchMonHoc = !monHocFilter || rowMaMonHoc === monHocFilter;
        
        row.style.display = (matchSearch && matchHocKy && matchMonHoc) ? '' : 'none';
    });
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>