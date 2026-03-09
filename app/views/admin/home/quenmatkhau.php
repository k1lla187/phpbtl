<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php $yeuCaus = $data['yeuCaus'] ?? []; ?>
<?php $success = isset($_GET['success']); ?>

<div class="card">
    <div class="card-header" style="background: linear-gradient(135deg, #fffbeb 0%, #ffffff 100%);">
        <h5 class="mb-0"><i class="fas fa-key me-2" style="color: #d4af37;"></i>Quản lý yêu cầu đổi mật khẩu</h5>
    </div>
    <div class="card-body">
        <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            Đã xử lý yêu cầu thành công!
        </div>
        <?php endif; ?>
        
        <?php if (empty($yeuCaus)): ?>
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <p class="text-muted">Chưa có yêu cầu đổi mật khẩu nào.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên đăng nhập</th>
                        <th>Mã người dùng</th>
                        <th>Vai trò</th>
                        <th>Ngày yêu cầu</th>
                        <th>Trạng thái</th>
                        <th>Mật khẩu mới</th>
                        <th>Người xử lý</th>
                        <th>Ghi chú</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($yeuCaus as $index => $yc): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><strong><?= htmlspecialchars($yc['TenDangNhap'] ?? '') ?></strong></td>
                        <td><?= htmlspecialchars($yc['MaNguoiDung'] ?? '') ?></td>
                        <td>
                            <?php if ($yc['VaiTro'] === 'SinhVien'): ?>
                            <span class="badge bg-primary">Sinh viên</span>
                            <?php else: ?>
                            <span class="badge bg-info">Giảng viên</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($yc['NgayYeuCau'])) ?></td>
                        <td>
                            <?php 
                            $trangThai = $yc['TrangThai'] ?? 'ChoXuLy';
                            if ($trangThai === 'ChoXuLy'): ?>
                            <span class="badge bg-warning text-dark">Chờ xử lý</span>
                            <?php elseif ($trangThai === 'DaDuyet'): ?>
                            <span class="badge bg-success">Đã duyệt</span>
                            <?php else: ?>
                            <span class="badge bg-danger">Từ chối</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($yc['MatKhauMoi'])): ?>
                            <code class="text-success" style="cursor: pointer; font-size: 14px;" 
                                  onclick="navigator.clipboard.writeText('<?= htmlspecialchars($yc['MatKhauMoi']) ?>'); alert('Đã copy!');"
                                  title="Click để copy">
                                <?= htmlspecialchars($yc['MatKhauMoi']) ?> 
                                <i class="fas fa-copy"></i>
                            </code>
                            <?php else: ?>
                            <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($yc['NguoiXuLy'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($yc['GhiChu'] ?? '-') ?></td>
                        <td>
                            <?php if ($trangThai === 'ChoXuLy'): ?>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-success rounded-circle" 
                                        onclick="xacNhan(<?= $yc['ID'] ?>, 'DaDuyet')"
                                        style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;"
                                        title="Duyệt">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger rounded-circle" 
                                        onclick="xacNhan(<?= $yc['ID'] ?>, 'TuChoi')"
                                        style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;"
                                        title="Từ chối">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <?php else: ?>
                            <span class="text-muted">Đã xử lý</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal xác nhận -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận yêu cầu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?url=Home/duyetYeuCauMK" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="yeuCauId">
                    <input type="hidden" name="trangThai" id="trangThai">
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea class="form-control" name="ghiChu" rows="3" placeholder="Nhập ghi chú (tùy chọn)"></textarea>
                    </div>
                    <p id="confirmMessage" class="mb-0"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Xác nhận</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let confirmModal = null;

document.addEventListener('DOMContentLoaded', function() {
    confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
});

function xacNhan(id, trangThai) {
    document.getElementById('yeuCauId').value = id;
    document.getElementById('trangThai').value = trangThai;
    
    let message = '';
    if (trangThai === 'DaDuyet') {
        message = 'Bạn có chắc muốn <strong class="text-success">DUYỆT</strong> yêu cầu này?<br>Hệ thống sẽ tự động tạo mật khẩu mới và gửi cho người dùng.';
    } else {
        message = 'Bạn có chắc muốn <strong class="text-danger">TỪ CHỐI</strong> yêu cầu này?';
    }
    
    document.getElementById('confirmMessage').innerHTML = message;
    confirmModal.show();
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
