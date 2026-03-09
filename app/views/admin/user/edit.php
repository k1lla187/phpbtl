<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>
<?php
$u = $data['user'] ?? null;
$flash = $data['flash'] ?? null;
?>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show mb-4" role="alert">
    <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i><?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($u): ?>
<div class="page-header mb-4">
    <h4><i class="fas fa-user-edit me-2"></i>Cập nhật tài khoản</h4>
    <a href="index.php?url=User/index" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
</div>

<div class="card">
    <div class="card-body">
        <form id="formEditUser" action="index.php?url=User/update/<?= (int)($u['MaUser']) ?>" method="POST">
            <div class="mb-3">
                <label class="form-label">Mã tài khoản</label>
                <input type="text" class="form-control" value="<?= (int)($u['MaUser']) ?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                <input type="text" name="TenDangNhap" class="form-control" value="<?= htmlspecialchars($u['TenDangNhap'] ?? '') ?>" required minlength="2" maxlength="50" autocomplete="username">
                <div class="form-text">Từ 2–50 ký tự, không trùng với tài khoản khác.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Mật khẩu mới</label>
                <input type="password" name="MatKhau" id="editMatKhau" class="form-control" placeholder="Để trống nếu không đổi" minlength="6" autocomplete="new-password">
                <div class="form-text">Chỉ nhập khi đổi mật khẩu; tối thiểu 6 ký tự.</div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Họ tên</label>
                    <input type="text" name="HoTen" class="form-control" value="<?= htmlspecialchars($u['HoTen'] ?? '') ?>" maxlength="100">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="Email" class="form-control" value="<?= htmlspecialchars($u['Email'] ?? '') ?>" maxlength="100">
                    <div class="form-text">Không trùng với tài khoản khác (nếu nhập).</div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="SoDienThoai" class="form-control" value="<?= htmlspecialchars($u['SoDienThoai'] ?? '') ?>" maxlength="15" pattern="[0-9\s\-\+]*" title="Chỉ số, khoảng trắng, dấu - +">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                    <select name="VaiTro" class="form-select" required>
                        <option value="SinhVien" <?= ($u['VaiTro'] ?? '') === 'SinhVien' ? 'selected' : '' ?>>Sinh viên</option>
                        <option value="GiangVien" <?= ($u['VaiTro'] ?? '') === 'GiangVien' ? 'selected' : '' ?>>Giảng viên</option>
                        <option value="QuanLy" <?= ($u['VaiTro'] ?? '') === 'QuanLy' ? 'selected' : '' ?>>Quản lý</option>
                        <option value="Admin" <?= ($u['VaiTro'] ?? '') === 'Admin' ? 'selected' : '' ?>>Quản trị viên</option>
                    </select>
                </div>
            </div>
            <div class="mb-4">
                <div class="form-check">
                    <input type="hidden" name="TrangThai" value="0">
                    <input type="checkbox" name="TrangThai" value="1" class="form-check-input" id="editTrangThai" <?= (isset($u['TrangThai']) && (int)$u['TrangThai'] === 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="editTrangThai">Hoạt động</label>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu</button>
                <a href="index.php?url=User/index" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    var form = document.getElementById('formEditUser');
    var pw = document.getElementById('editMatKhau');
    if (form && pw) {
        form.addEventListener('submit', function(e) {
            var v = (pw.value || '').trim();
            if (v.length > 0 && v.length < 6) {
                e.preventDefault();
                alert('Mật khẩu mới phải có ít nhất 6 ký tự.');
                pw.focus();
                return false;
            }
        });
    }
})();
</script>
<?php else: ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-triangle me-2"></i>Không tìm thấy tài khoản.
    <a href="index.php?url=User/index" class="alert-link">Quay lại danh sách</a>
</div>
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
