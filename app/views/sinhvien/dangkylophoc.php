<?php
$pageActive = 'dangkylophoc';
$pageTitle = 'Đăng ký học phần';
$breadcrumb = 'Cổng Sinh viên / Đăng ký học phần';
$danhSachLHP = $danhSachLHP ?? [];
$lichHocByLHP = $lichHocByLHP ?? [];
$hocKys = $hocKys ?? [];
$monHocList = $monHocList ?? [];
$maHKHienTai = $maHKHienTai ?? null;
$baseUrl = defined('URLROOT') ? URLROOT : '';

$maHK = $_GET['maHocKy'] ?? $maHKHienTai;
$maMon = $_GET['maMon'] ?? '';
$tuKhoa = $_GET['tuKhoa'] ?? '';

require_once __DIR__ . '/_layout_sv.php';
?>
<div class="content-header">
    <div class="content-header__title">Đăng ký học phần</div>
    <?php if (isset($tenKhoa)): ?>
    <div class="content-header__subtitle" style="font-size: 14px; color: #6c757d; margin-top: 4px;">
        <i class="fas fa-building"></i> Khoa: <?= htmlspecialchars($tenKhoa) ?> - Chỉ hiển thị môn học thuộc khoa của bạn
    </div>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">Danh sách lớp học phần đang mở</h5>
    </div>
    <div class="card-body">
        <!-- Form lọc - 1 hàng -->
        <div class="filter-bar" style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; align-items: flex-end;">
            <div class="form-group" style="margin-bottom: 0; min-width: 160px;">
                <label class="form-label" style="margin-bottom: 4px; font-size: 13px;">Học kỳ</label>
                <select name="maHocKy" class="form-select" style="padding: 8px 12px;" onchange="this.form.submit()">
                    <option value="">Tất cả</option>
                    <?php foreach ($hocKys as $hk): ?>
                        <option value="<?= htmlspecialchars($hk['MaHocKy']) ?>" <?= ($maHK === $hk['MaHocKy']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($hk['TenHocKy'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 0; min-width: 180px;">
                <label class="form-label" style="margin-bottom: 4px; font-size: 13px;">Môn học</label>
                <select name="maMon" class="form-select" style="padding: 8px 12px;" onchange="this.form.submit()">
                    <option value="">Tất cả</option>
                    <?php foreach ($monHocList as $mh): ?>
                        <option value="<?= htmlspecialchars($mh['MaMonHoc']) ?>" <?= ($maMon === $mh['MaMonHoc']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($mh['TenMonHoc'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                <label class="form-label" style="margin-bottom: 4px; font-size: 13px;">Tìm kiếm</label>
                <div style="display: flex; gap: 6px;">
                    <input type="text" name="tuKhoa" class="form-input" style="padding: 8px 12px;" placeholder="Mã LHP, tên môn, giảng viên..." value="<?= htmlspecialchars($tuKhoa) ?>">
                    <button type="submit" class="btn btn-primary" style="padding: 8px 14px;">
                        <i class="fas fa-search"></i>
                    </button>
                    <?php if ($maHK || $maMon || $tuKhoa): ?>
                        <a href="<?= $baseUrl ?>/SinhVien/dangKyHoc" class="btn btn-secondary" style="padding: 8px 14px;">
                            <i class="fas fa-redo"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (empty($danhSachLHP)): ?>
            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <h5>Không có lớp học phần nào</h5>
                <p>Không tìm thấy lớp học phần nào phù hợp với điều kiện lọc.</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper" style="overflow-x: auto;">
                <table class="table" id="table-dangkylophoc" style="min-width: 900px;">
                    <thead>
                        <tr>
                            <th style="width: 90px; white-space: nowrap;">Mã LHP</th>
                            <th style="min-width: 180px;">Môn học</th>
                            <th style="width: 140px; white-space: nowrap;">Giảng viên</th>
                            <th style="width: 220px; white-space: nowrap;">Lịch học</th>
                            <th style="width: 100px; white-space: nowrap; text-align: center;">Sĩ số</th>
                            <th style="width: 130px; white-space: nowrap; text-align: center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($danhSachLHP as $lhp): ?>
                            <?php 
                                $maLHP = $lhp['MaLopHocPhan'];
                                $schedule = $lichHocByLHP[$maLHP] ?? [];
                                $siSoConLai = $lhp['SiSoConLai'] ?? 0;
                                $toiDa = $lhp['SoLuongToiDa'] ?? 0;
                                $dangKy = $lhp['DaDangKy'] ?? false;
                                $siSoDangKy = $lhp['SiSoDangKy'] ?? 0;
                                
                                // Format lịch học
                                $lichHocText = [];
                                foreach ($schedule as $ca) {
                                    $thu = $ca['Thu'] ?? '';
                                    $tietBD = $ca['TietBatDau'] ?? '';
                                    $tietKT = $ca['TietKetThuc'] ?? '';
                                    $phong = $ca['PhongHoc'] ?? $ca['PhongMacDinh'] ?? '';
                                    if ($thu && $tietBD && $tietKT) {
                                        $lichHocText[] = "T$thu ($tietBD-$tietKT)" . ($phong ? ", P$phong" : "");
                                    }
                                }
                                $lichHocStr = implode('; ', $lichHocText);
                            ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($lhp['MaLopHocPhan'] ?? '') ?></strong></td>
                                <td>
                                    <div><?= htmlspecialchars($lhp['TenMonHoc'] ?? '') ?></div>
                                    <div class="text-muted" style="font-size: 11px;"><?= $lhp['SoTinChi'] ?? '' ?> tín chỉ</div>
                                </td>
                                <td style="white-space: nowrap;"><?= htmlspecialchars($lhp['TenGiangVien'] ?? 'Chưa phân công') ?></td>
                                <td style="font-size: 12px; white-space: nowrap;">
                                    <?php if ($lichHocStr): ?>
                                        <?= htmlspecialchars($lichHocStr) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Chưa có lịch</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($siSoConLai <= 0): ?>
                                        <span class="badge badge-danger">Đầy</span>
                                        <div class="text-muted" style="font-size: 11px;"><?= $siSoDangKy ?>/<?= $toiDa ?></div>
                                    <?php elseif ($siSoConLai <= 5): ?>
                                        <span class="badge badge-warning"><?= $siSoConLai ?></span>
                                        <div class="text-muted" style="font-size: 11px;"><?= $siSoDangKy ?>/<?= $toiDa ?></div>
                                    <?php else: ?>
                                        <span class="badge badge-success"><?= $siSoConLai ?></span>
                                        <div class="text-muted" style="font-size: 11px;"><?= $siSoDangKy ?>/<?= $toiDa ?></div>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($dangKy): ?>
                                        <span class="badge badge-success">Đã đăng ký</span>
                                    <?php elseif ($siSoConLai <= 0): ?>
                                        <button class="btn btn-sm btn-disabled" disabled style="padding: 4px 10px; font-size: 12px;">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-primary btn-dang-ky" 
                                                style="padding: 4px 10px; font-size: 12px;"
                                                data-ma-lhp="<?= htmlspecialchars($maLHP) ?>"
                                                data-ten-mon="<?= htmlspecialchars($lhp['TenMonHoc'] ?? '') ?>">
                                            <i class="fas fa-plus"></i> ĐKý
                                        </button>
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

<!-- Modal xác nhận đăng ký -->
<div class="modal" id="confirmModal">
    <div class="modal-overlay"></div>
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h5 class="modal-title">Xác nhận đăng ký</h5>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Bạn có chắc chắn muốn đăng ký môn học <strong id="confirmTenMon"></strong>?</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal()">Hủy</button>
            <button class="btn btn-primary" id="confirmBtn">Xác nhận đăng ký</button>
        </div>
    </div>
</div>

<!-- Toast notification -->
<div id="toast" class="toast-container"></div>

<script>
let currentMaLHP = '';

function showModal(maLHP, tenMon) {
    currentMaLHP = maLHP;
    document.getElementById('confirmTenMon').textContent = tenMon;
    document.getElementById('confirmModal').classList.add('active');
}

function closeModal() {
    document.getElementById('confirmModal').classList.remove('active');
    currentMaLHP = '';
}

// Close modal when clicking overlay
document.querySelector('.modal-overlay').addEventListener('click', closeModal);

// Handle đăng ký button clicks
document.querySelectorAll('.btn-dang-ky').forEach(btn => {
    btn.addEventListener('click', function() {
        const maLHP = this.getAttribute('data-ma-lhp');
        const tenMon = this.getAttribute('data-ten-mon');
        showModal(maLHP, tenMon);
    });
});

// Handle confirm button
document.getElementById('confirmBtn').addEventListener('click', function() {
    if (!currentMaLHP) return;
    
    const btn = this;
    btn.disabled = true;
    btn.textContent = 'Đang xử lý...';
    
    const formData = new FormData();
    formData.append('maLopHocPhan', currentMaLHP);
    
    fetch('<?= $baseUrl ?>/SinhVien/dangKy', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        showToast('error', 'Đã xảy ra lỗi. Vui lòng thử lại!');
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = 'Xác nhận đăng ký';
        closeModal();
    });
});

function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    document.getElementById('toast').appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>

<style>
.toast-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
}
.toast {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 20px;
    border-radius: 8px;
    background: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    margin-top: 10px;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.3s ease;
}
.toast.show {
    opacity: 1;
    transform: translateX(0);
}
.toast-success {
    border-left: 4px solid #22c55e;
}
.toast-success i {
    color: #22c55e;
}
.toast-error {
    border-left: 4px solid #ef4444;
}
.toast-error i {
    color: #ef4444;
}
.btn-disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
<?php require_once __DIR__ . '/_layout_sv_footer.php';
