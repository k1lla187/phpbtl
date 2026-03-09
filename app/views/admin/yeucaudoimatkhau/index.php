<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>
<?php
$requests = $requests ?? [];
$pendingCount = $pendingCount ?? 0;
$filter = $_GET['filter'] ?? 'all';
?>

<!-- Flash messages -->
<?php if (isset($_SESSION['flash_success'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['flash_success']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['flash_success']); endif; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['flash_error']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['flash_error']); endif; ?>

<!-- Page Header -->
<div class="page-header">
    <h4><i class="fas fa-key me-2"></i>Quản lý Yêu cầu Khôi phục Mật khẩu</h4>
    <?php if ($pendingCount > 0): ?>
    <span class="badge bg-warning text-dark" style="font-size: 14px;">
        <i class="fas fa-clock me-1"></i><?= $pendingCount ?> yêu cầu chờ xử lý
    </span>
    <?php endif; ?>
</div>

<!-- Filter Tabs -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link <?= $filter === 'all' ? 'active' : '' ?>" href="index.php?url=YeuCauDoiMatKhau/index&filter=all">
            <i class="fas fa-list me-1"></i>Tất cả
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $filter === 'pending' ? 'active' : '' ?>" href="index.php?url=YeuCauDoiMatKhau/index&filter=pending">
            <i class="fas fa-clock me-1"></i>Chờ xử lý
            <?php if ($pendingCount > 0): ?>
            <span class="badge bg-warning text-dark ms-1"><?= $pendingCount ?></span>
            <?php endif; ?>
        </a>
    </li>
</ul>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-envelope"></i></div>
            <div class="stat-value"><?= count($requests) ?></div>
            <div class="stat-label">Tổng yêu cầu</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon yellow"><i class="fas fa-clock"></i></div>
            <div class="stat-value"><?= $pendingCount ?></div>
            <div class="stat-label">Chờ xử lý</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-value"><?= count(array_filter($requests, fn($r) => $r['TrangThai'] === 'DaDuyet')) ?></div>
            <div class="stat-label">Đã duyệt</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
            <div class="stat-value"><?= count(array_filter($requests, fn($r) => $r['TrangThai'] === 'TuChoi')) ?></div>
            <div class="stat-label">Đã từ chối</div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Danh sách Yêu cầu</h5>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" class="form-control" placeholder="Tìm kiếm..." id="searchInput">
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table" id="dataTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Người yêu cầu</th>
                        <th>Tên đăng nhập</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Lý do</th>
                        <th>Ngày gửi</th>
                        <th>Trạng thái</th>
                        <th width="180">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($requests)): ?>
                        <?php foreach ($requests as $r): ?>
                        <tr>
                            <td><strong>#<?= $r['ID'] ?></strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2">
                                        <?= strtoupper(substr($r['HoTen'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-semibold"><?= htmlspecialchars($r['HoTen']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><code><?= htmlspecialchars($r['TenDangNhap']) ?></code></td>
                            <td><?= htmlspecialchars($r['Email']) ?></td>
                            <td>
                                <?php 
                                $badgeClass = match($r['VaiTro']) {
                                    'Admin' => 'bg-danger',
                                    'GiangVien' => 'bg-info',
                                    'SinhVien' => 'bg-success',
                                    default => 'bg-secondary'
                                };
                                $roleName = match($r['VaiTro']) {
                                    'Admin' => 'Quản trị viên',
                                    'GiangVien' => 'Giảng viên',
                                    'SinhVien' => 'Sinh viên',
                                    default => $r['VaiTro']
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= $roleName ?></span>
                            </td>
                            <td>
                                <span class="text-muted" style="max-width: 200px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($r['LyDo']) ?>">
                                    <?= htmlspecialchars($r['LyDo'] ?: 'Không có') ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= date('d/m/Y H:i', strtotime($r['NgayYeuCau'])) ?>
                                </small>
                            </td>
                            <td>
                                <?php 
                                $statusClass = match($r['TrangThai']) {
                                    'ChoXuLy' => 'bg-warning text-dark',
                                    'DaDuyet' => 'bg-success',
                                    'TuChoi' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                $statusName = match($r['TrangThai']) {
                                    'ChoXuLy' => 'Chờ xử lý',
                                    'DaDuyet' => 'Đã duyệt',
                                    'TuChoi' => 'Từ chối',
                                    default => $r['TrangThai']
                                };
                                ?>
                                <span class="badge <?= $statusClass ?>"><?= $statusName ?></span>
                                <?php if ($r['TrangThai'] !== 'ChoXuLy' && !empty($r['TenNguoiXuLy'])): ?>
                                <br><small class="text-muted">bởi <?= htmlspecialchars($r['TenNguoiXuLy']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                <?php if ($r['TrangThai'] === 'ChoXuLy'): ?>
                                <button class="action-btn action-btn-approve" onclick="showApproveModal(<?= $r['ID'] ?>, '<?= htmlspecialchars($r['HoTen']) ?>')" title="Duyệt yêu cầu">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="action-btn action-btn-reject" onclick="showRejectModal(<?= $r['ID'] ?>, '<?= htmlspecialchars($r['HoTen']) ?>')" title="Từ chối">
                                    <i class="fas fa-times"></i>
                                </button>
                                <?php else: ?>
                                <button class="action-btn action-btn-view" onclick="showDetailModal(<?= htmlspecialchars(json_encode($r)) ?>)" title="Xem chi tiết">
                                    <i class="fas fa-external-link-alt"></i>
                                </button>
                                <a href="index.php?url=YeuCauDoiMatKhau/delete&id=<?= $r['ID'] ?>" class="action-btn action-btn-delete" onclick="return confirm('Bạn có chắc muốn xóa yêu cầu này?')" title="Xóa">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                                <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    <p>Không có yêu cầu nào</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Duyệt yêu cầu -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Duyệt yêu cầu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?url=YeuCauDoiMatKhau/approve" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="approveId">
                    <p>Bạn có chắc muốn duyệt yêu cầu của <strong id="approveName"></strong>?</p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Hệ thống sẽ tự động tạo mật khẩu mới và gửi qua email cho người dùng.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú (không bắt buộc)</label>
                        <textarea name="ghichu" class="form-control" rows="2" placeholder="Nhập ghi chú nếu cần..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i>Duyệt & Gửi mật khẩu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Từ chối yêu cầu -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Từ chối yêu cầu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?url=YeuCauDoiMatKhau/reject" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="rejectId">
                    <p>Bạn có chắc muốn từ chối yêu cầu của <strong id="rejectName"></strong>?</p>
                    <div class="mb-3">
                        <label class="form-label">Lý do từ chối <span class="text-danger">*</span></label>
                        <textarea name="lydotuchoi" class="form-control" rows="3" placeholder="Nhập lý do từ chối yêu cầu..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i>Từ chối
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Chi tiết -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Chi tiết yêu cầu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="140" class="text-muted">Mã yêu cầu:</td>
                        <td><strong id="detailId"></strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Người yêu cầu:</td>
                        <td id="detailName"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tên đăng nhập:</td>
                        <td><code id="detailUsername"></code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email:</td>
                        <td id="detailEmail"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Vai trò:</td>
                        <td id="detailRole"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Lý do:</td>
                        <td id="detailReason"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Ngày gửi:</td>
                        <td id="detailDate"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Trạng thái:</td>
                        <td id="detailStatus"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Người xử lý:</td>
                        <td id="detailHandler"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Ngày xử lý:</td>
                        <td id="detailProcessDate"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Ghi chú Admin:</td>
                        <td id="detailNote"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}
.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
}
.stat-icon.blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.stat-icon.yellow { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stat-icon.green { background: linear-gradient(135deg, #10b981, #059669); }
.stat-icon.red { background: linear-gradient(135deg, #ef4444, #dc2626); }
.stat-value { font-size: 24px; font-weight: 700; color: #1e293b; }
.stat-label { font-size: 13px; color: #64748b; }
.search-box {
    position: relative;
    width: 250px;
}
.search-box i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}
.search-box input {
    padding-left: 38px;
}

/* Action Buttons - Professional Style */
.action-buttons {
    display: flex;
    gap: 8px;
    justify-content: center;
}

.action-btn {
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    font-size: 14px;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.action-btn-approve {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.action-btn-approve:hover {
    background: linear-gradient(135deg, #059669, #047857);
}

.action-btn-reject {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.action-btn-reject:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
}

.action-btn-view {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.action-btn-view:hover {
    background: linear-gradient(135deg, #d97706, #b45309);
}

.action-btn-delete {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.action-btn-delete:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: white;
}
</style>

<script>
// Hiển thị modal duyệt
function showApproveModal(id, name) {
    document.getElementById('approveId').value = id;
    document.getElementById('approveName').textContent = name;
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

// Hiển thị modal từ chối
function showRejectModal(id, name) {
    document.getElementById('rejectId').value = id;
    document.getElementById('rejectName').textContent = name;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

// Hiển thị modal chi tiết
function showDetailModal(data) {
    const roleNames = {
        'Admin': 'Quản trị viên',
        'GiangVien': 'Giảng viên',
        'SinhVien': 'Sinh viên'
    };
    const statusNames = {
        'ChoXuLy': '<span class="badge bg-warning text-dark">Chờ xử lý</span>',
        'DaDuyet': '<span class="badge bg-success">Đã duyệt</span>',
        'TuChoi': '<span class="badge bg-danger">Từ chối</span>'
    };
    
    document.getElementById('detailId').textContent = '#' + data.ID;
    document.getElementById('detailName').textContent = data.HoTen;
    document.getElementById('detailUsername').textContent = data.TenDangNhap;
    document.getElementById('detailEmail').textContent = data.Email;
    document.getElementById('detailRole').innerHTML = roleNames[data.VaiTro] || data.VaiTro;
    document.getElementById('detailReason').textContent = data.LyDo || 'Không có';
    document.getElementById('detailDate').textContent = new Date(data.NgayYeuCau).toLocaleString('vi-VN');
    document.getElementById('detailStatus').innerHTML = statusNames[data.TrangThai] || data.TrangThai;
    document.getElementById('detailHandler').textContent = data.TenNguoiXuLy || '-';
    document.getElementById('detailProcessDate').textContent = data.NgayXuLy ? new Date(data.NgayXuLy).toLocaleString('vi-VN') : '-';
    document.getElementById('detailNote').textContent = data.GhiChuAdmin || '-';
    
    new bootstrap.Modal(document.getElementById('detailModal')).show();
}

// Tìm kiếm
document.getElementById('searchInput')?.addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#dataTable tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
