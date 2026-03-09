<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>
<?php
$users = $data['users'] ?? [];
$flash = $data['flash'] ?? null;
?>

<!-- Flash message -->
<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
    <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i><?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header">
    <h4><i class="fas fa-users-cog me-2"></i>Quản lý Tài khoản</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fas fa-plus me-2"></i>Thêm tài khoản
    </button>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <select class="form-select" id="filterRole">
        <option value="">Tất cả vai trò</option>
        <option value="admin">Admin</option>
        <option value="teacher">Giảng viên</option>
        <option value="student">Sinh viên</option>
    </select>
    <select class="form-select" id="filterStatus">
        <option value="">Tất cả trạng thái</option>
        <option value="active">Hoạt động</option>
        <option value="inactive">Đã khóa</option>
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
            <div class="stat-icon blue"><i class="fas fa-users"></i></div>
            <div class="stat-value"><?= (int)($data['totalUsers'] ?? count($users)) ?></div>
            <div class="stat-label">Tổng tài khoản</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-user-shield"></i></div>
            <div class="stat-value"><?= (int)($data['totalAdmin'] ?? 0) ?></div>
            <div class="stat-label">Quản trị viên</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="stat-value"><?= (int)($data['totalTeacher'] ?? 0) ?></div>
            <div class="stat-label">Giảng viên</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon yellow"><i class="fas fa-user-graduate"></i></div>
            <div class="stat-value"><?= (int)($data['totalStudent'] ?? 0) ?></div>
            <div class="stat-label">Sinh viên</div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Danh sách Tài khoản</h5>
        <span class="badge bg-primary"><?= count($users) ?> tài khoản</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table" id="dataTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên đăng nhập</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Đăng nhập cuối</th>
                        <th width="130">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $u):
                            $vai = strtolower($u['VaiTro'] ?? '');
                            $roleFilter = $vai === 'admin' ? 'admin' : ($vai === 'giangvien' ? 'teacher' : 'student');
                            $statusFilter = (isset($u['TrangThai']) && (int)$u['TrangThai'] === 1) ? 'active' : 'inactive';
                            $maUser = $u['MaUser'] ?? $u['ID'] ?? '';
                        ?>
                        <tr data-role="<?= htmlspecialchars($roleFilter) ?>" data-status="<?= $statusFilter ?>">
                            <td><?= htmlspecialchars($maUser) ?></td>
                            <td><strong><?= htmlspecialchars($u['TenDangNhap'] ?? $u['Username'] ?? '') ?></strong></td>
                            <td><?= htmlspecialchars($u['HoTen'] ?? '') ?></td>
                            <td><?= htmlspecialchars($u['Email'] ?? '') ?></td>
                            <td>
                                <span class="badge <?= $vai === 'admin' ? 'bg-danger' : ($vai === 'giangvien' ? 'bg-success' : ($vai === 'quanly' ? 'bg-warning' : 'bg-info')) ?>"><?= $vai === 'admin' ? 'Admin' : ($vai === 'giangvien' ? 'Giảng viên' : ($vai === 'quanly' ? 'Quản lý' : 'Sinh viên')) ?></span>
                            </td>
                            <td>
                                <?php if ((isset($u['TrangThai']) ? (int)$u['TrangThai'] : 1) === 1): ?>
                                    <span class="status-active">Hoạt động</span>
                                <?php else: ?>
                                    <span class="status-inactive">Đã khóa</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($u['LastLogin'] ?? '-') ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="index.php?url=User/edit/<?= htmlspecialchars($maUser) ?>" class="btn-action btn-action-edit" data-tooltip="Sửa"><i class="fas fa-edit"></i></a>
                                    <?php if ($vai !== 'admin'): ?>
                                    <a href="index.php?url=User/delete/<?= htmlspecialchars($maUser) ?>" class="btn-action btn-action-delete" data-tooltip="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?');"><i class="fas fa-trash"></i></a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr data-empty="1">
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="fas fa-users"></i>
                                    <h5>Chưa có tài khoản nào</h5>
                                    <p>Bấm nút "Thêm tài khoản" để tạo mới</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Thêm tài khoản mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAddUser" action="index.php?url=User/store" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                            <input type="text" name="TenDangNhap" class="form-control" placeholder="Nhập tên đăng nhập" required minlength="2" maxlength="50" autocomplete="username">
                            <div class="form-text">Từ 2–50 ký tự, không trùng với tài khoản khác.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" name="MatKhau" id="addMatKhau" class="form-control" placeholder="Ít nhất 6 ký tự" required minlength="6" autocomplete="new-password">
                            <div class="form-text">Tối thiểu 6 ký tự.</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Họ tên</label>
                            <input type="text" name="HoTen" class="form-control" placeholder="Nhập họ tên" maxlength="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="Email" class="form-control" placeholder="email@example.com" maxlength="100">
                            <div class="form-text">Không trùng với tài khoản khác (nếu nhập).</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                            <select name="VaiTro" class="form-select" required>
                                <option value="SinhVien">Sinh viên</option>
                                <option value="GiangVien">Giảng viên</option>
                                <option value="QuanLy">Quản lý</option>
                                <option value="Admin">Quản trị viên</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="SoDienThoai" class="form-control" placeholder="Số điện thoại" maxlength="15" pattern="[0-9\s\-\+]*" title="Chỉ số, khoảng trắng, dấu - +">
                        </div>
                    </div>
                    <div class="mb-0">
                        <div class="form-check">
                            <input type="checkbox" name="TrangThai" value="1" class="form-check-input" id="addTrangThai" checked>
                            <label class="form-check-label" for="addTrangThai">Kích hoạt ngay sau khi tạo</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Tạo tài khoản</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    var searchInput = document.getElementById('searchInput');
    var filterRole = document.getElementById('filterRole');
    var filterStatus = document.getElementById('filterStatus');
    var formAdd = document.getElementById('formAddUser');
    var addMatKhau = document.getElementById('addMatKhau');

    function applyFilters() {
        var q = (searchInput && searchInput.value) ? searchInput.value.toLowerCase().trim() : '';
        var role = filterRole && filterRole.value ? filterRole.value.toLowerCase() : '';
        var status = filterStatus && filterStatus.value ? filterStatus.value.toLowerCase() : '';
        var rows = document.querySelectorAll('#dataTable tbody tr');
        rows.forEach(function(row) {
            if (row.getAttribute('data-empty') === '1') {
                row.style.display = '';
                return;
            }
            var show = true;
            if (q) {
                var text = row.textContent.toLowerCase();
                if (!text.includes(q)) show = false;
            }
            if (show && role && row.getAttribute('data-role') !== role) show = false;
            if (show && status && row.getAttribute('data-status') !== status) show = false;
            row.style.display = show ? '' : 'none';
        });
    }

    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (filterRole) filterRole.addEventListener('change', applyFilters);
    if (filterStatus) filterStatus.addEventListener('change', applyFilters);

    if (formAdd && addMatKhau) {
        formAdd.addEventListener('submit', function(e) {
            var pw = (addMatKhau.value || '').trim();
            if (pw.length > 0 && pw.length < 6) {
                e.preventDefault();
                alert('Mật khẩu phải có ít nhất 6 ký tự.');
                addMatKhau.focus();
                return false;
            }
        });
    }
})();
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
