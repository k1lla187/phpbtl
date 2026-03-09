<?php
$pageActive = 'mondadangky';
$pageTitle = 'Môn đã đăng ký';
$breadcrumb = 'Cổng Sinh viên / Môn đã đăng ký';
$dangKys = $dangKys ?? [];
$lichHocByDK = $lichHocByDK ?? [];
$hocKys = $hocKys ?? [];
$baseUrl = defined('URLROOT') ? URLROOT : '';

$maHK = $_GET['maHocKy'] ?? null;

require_once __DIR__ . '/_layout_sv.php';
?>
<div class="content-header">
    <div class="content-header__title">Danh sách môn đã đăng ký</div>
    <form method="get" action="" style="display: flex; gap: 8px; align-items: center;">
        <select name="maHocKy" class="form-select" style="min-width: 180px; padding: 8px 12px;" onchange="this.form.submit()">
            <option value="">Tất cả học kỳ</option>
            <?php foreach ($hocKys as $hk): ?>
                <option value="<?= htmlspecialchars($hk['MaHocKy']) ?>" <?= ($maHK === $hk['MaHocKy']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($hk['TenHocKy'] ?? '') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">Danh sách môn học đã đăng ký</h5>
    </div>
    <div class="card-body">
        <?php if (empty($dangKys)): ?>
            <div class="empty-state">
                <i class="fas fa-clipboard-check"></i>
                <h5>Chưa có môn đăng ký</h5>
                <p>Bạn chưa đăng ký môn học nào hoặc không có môn nào trong học kỳ đã chọn.</p>
                <a href="<?= $baseUrl ?>/SinhVien/dangKyHoc" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Đăng ký học phần
                </a>
            </div>
        <?php else: ?>
            <div class="table-wrapper" style="overflow-x: auto;">
                <table class="table" id="table-mondadky" style="min-width: 1000px;">
                    <thead>
                        <tr>
                            <th style="width: 90px; white-space: nowrap;">Mã LHP</th>
                            <th style="min-width: 180px;">Môn học</th>
                            <th style="width: 70px; white-space: nowrap;">Số TC</th>
                            <th style="width: 130px; white-space: nowrap;">Giảng viên</th>
                            <th style="width: 200px; white-space: nowrap;">Lịch học</th>
                            <th style="width: 80px; white-space: nowrap;">Phòng</th>
                            <th style="width: 100px; white-space: nowrap; text-align: center;">Điểm TK</th>
                            <th style="width: 80px; white-space: nowrap; text-align: center;">Kết quả</th>
                            <th style="width: 100px; white-space: nowrap; text-align: center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dangKys as $dk): ?>
                            <?php 
                                $schedule = $lichHocByDK[$dk['MaDangKy']] ?? [];
                                $maLHP = $dk['MaLopHocPhan'] ?? '';
                                
                                // Format lịch học
                                $lichHocText = [];
                                $phongHoc = '';
                                foreach ($schedule as $ca) {
                                    $thu = $ca['Thu'] ?? '';
                                    $tietBD = $ca['TietBatDau'] ?? '';
                                    $tietKT = $ca['TietKetThuc'] ?? '';
                                    $phong = $ca['PhongHoc'] ?? $ca['PhongMacDinh'] ?? '';
                                    if ($thu && $tietBD && $tietKT) {
                                        $lichHocText[] = "T$thu ($tietBD-$tietKT)";
                                    }
                                    if ($phong && !$phongHoc) {
                                        $phongHoc = $phong;
                                    }
                                }
                                $lichHocStr = implode('; ', $lichHocText);
                                
                                // Get giảng viên from lhp info
                                $tenGV = $dk['TenGV'] ?? '';
                                
                                // Điểm
                                $diem = $dk['DiemTongKet'] ?? null;
                                $diemChu = $dk['DiemChu'] ?? '';
                                $ketQua = $dk['KetQua'] ?? '';
                                
                                // Kiểm tra có thể hủy không (nếu đã có điểm thì không cho hủy)
                                $coDiem = isset($diem) && $diem !== null;
                            ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($maLHP) ?></strong></td>
                                <td><?= htmlspecialchars($dk['TenMonHoc'] ?? '') ?></td>
                                <td style="text-align: center;"><?= $dk['SoTinChi'] ?? '' ?></td>
                                <td style="white-space: nowrap;"><?= htmlspecialchars($tenGV ?: '-') ?></td>
                                <td style="font-size: 12px; white-space: nowrap;">
                                    <?php if ($lichHocStr): ?>
                                        <?= htmlspecialchars($lichHocStr) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td style="white-space: nowrap;"><?= htmlspecialchars($phongHoc ?: '-') ?></td>
                                <td style="text-align: center;">
                                    <?php if ($coDiem): ?>
                                        <strong><?= number_format($diem, 1) ?></strong>
                                        <div style="font-size: 11px; color: #6b7280;"><?= htmlspecialchars($diemChu) ?></div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($ketQua): ?>
                                        <?php 
                                            $badgeClass = '';
                                            if ($ketQua === 'Đạt' || $ketQua === 'Pass') $badgeClass = 'badge-success';
                                            elseif ($ketQua === 'Không đạt' || $ketQua === 'Fail') $badgeClass = 'badge-danger';
                                            else $badgeClass = 'badge-secondary';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($ketQua) ?></span>
                                    <?php elseif ($coDiem): ?>
                                        <?php 
                                            $isPass = $diem >= 4.0;
                                            $badgeClass = $isPass ? 'badge-success' : 'badge-danger';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= $isPass ? 'Đạt' : 'Không đạt' ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Chưa</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($coDiem): ?>
                                        <span class="text-muted" style="font-size: 11px;">-</span>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-danger btn-huy-dk" 
                                                style="padding: 4px 10px; font-size: 12px;"
                                                data-ma-dk="<?= $dk['MaDangKy'] ?>"
                                                data-ten-mon="<?= htmlspecialchars($dk['TenMonHoc'] ?? '') ?>">
                                            <i class="fas fa-times"></i> Hủy
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

<!-- Modal xác nhận hủy đăng ký -->
<div class="modal" id="cancelModal">
    <div class="modal-overlay"></div>
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h5 class="modal-title">Xác nhận hủy đăng ký</h5>
            <button class="modal-close" onclick="closeCancelModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Bạn có chắc chắn muốn hủy đăng ký môn học <strong id="cancelTenMon"></strong>?</p>
            <p class="text-muted" style="font-size: 13px;">Lưu ý: Sau khi hủy, bạn sẽ mất vị trí trong lớp học phần này.</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeCancelModal()">Không, giữ lại</button>
            <button class="btn btn-danger" id="confirmCancelBtn">Xác nhận hủy</button>
        </div>
    </div>
</div>

<!-- Toast notification -->
<div id="toast" class="toast-container"></div>

<script>
let currentMaDK = '';

function showCancelModal(maDK, tenMon) {
    currentMaDK = maDK;
    document.getElementById('cancelTenMon').textContent = tenMon;
    document.getElementById('cancelModal').classList.add('active');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.remove('active');
    currentMaDK = '';
}

// Close modal when clicking overlay
document.querySelector('#cancelModal .modal-overlay').addEventListener('click', closeCancelModal);

// Handle hủy đăng ký button clicks
document.querySelectorAll('.btn-huy-dk').forEach(btn => {
    btn.addEventListener('click', function() {
        const maDK = this.getAttribute('data-ma-dk');
        const tenMon = this.getAttribute('data-ten-mon');
        showCancelModal(maDK, tenMon);
    });
});

// Handle confirm cancel button
document.getElementById('confirmCancelBtn').addEventListener('click', function() {
    if (!currentMaDK) return;
    
    const btn = this;
    btn.disabled = true;
    btn.textContent = 'Đang xử lý...';
    
    const formData = new FormData();
    formData.append('maDangKy', currentMaDK);
    
    fetch('<?= $baseUrl ?>/SinhVien/huyDangKy', {
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
        btn.textContent = 'Xác nhận hủy';
        closeCancelModal();
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
</style>
<?php require_once __DIR__ . '/_layout_sv_footer.php';
