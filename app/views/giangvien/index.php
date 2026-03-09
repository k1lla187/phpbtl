<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <h4><i class="fas fa-chalkboard-teacher me-2"></i>Quản lý Giảng viên</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fas fa-plus me-2"></i>Thêm giảng viên
    </button>
</div>

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
                                <div class="table-actions">
                                    <a href="index.php?url=GiangVien/edit/<?= $gv['MaGiangVien'] ?>" class="btn btn-sm btn-warning btn-action" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?url=GiangVien/delete/<?= $gv['MaGiangVien'] ?>" class="btn btn-sm btn-danger btn-action" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa?')">
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
                            <input type="text" name="MaGiangVien" class="form-control" required>
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
                            <label class="form-label">Khoa</label>
                            <select name="MaKhoa" class="form-select">
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
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="DiaChi" class="form-control">
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
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#dataTable tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>