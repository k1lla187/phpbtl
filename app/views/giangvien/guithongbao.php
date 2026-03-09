<?php
// Biến từ controller: $giangVien, $lopHocPhanList, $lopHocPhanSelected, $sinhVienList
$giangVienTen = $giangVien['HoTen'] ?? 'Giảng viên';
$giangVienMa = $giangVien['MaGiangVien'] ?? '';
$lopHocPhanList = $lopHocPhanList ?? [];
$lopHocPhanSelected = $lopHocPhanSelected ?? null;
$sinhVienList = $sinhVienList ?? [];
$baseUrl = defined('URLROOT') ? URLROOT : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gửi Thông Báo - UNISCORE Giảng Viên</title>
    <link rel="icon" type="image/svg+xml" href="<?= rtrim($baseUrl ?? '', '/') ?>/favicon.svg">
    <link href="<?= rtrim($baseUrl ?? '', '/') ?>/css/giangvien.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>window.APP_BASE_URL = '<?= defined("URLROOT") ? rtrim(URLROOT, "/") : "" ?>';</script>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="sidebar__brand">
            <img src="<?= rtrim($baseUrl ?? '', '/') ?>/favicon.svg" alt="UNISCORE" class="sidebar__logo" style="width: 34px; height: 34px; border-radius: 6px;">
            <div>
                <div class="sidebar__title" style="color: #d4af37;">UNISCORE</div>
                <div class="sidebar__subtitle">Cổng Giảng Viên</div>
            </div>
        </div>
        <nav class="sidebar__nav">
            <div class="nav-section-title">Tổng quan</div>
            <a href="<?php echo $baseUrl; ?>/GiangVien/dashboard" class="nav-item"><div class="nav-item__icon">🏠</div><div>Bảng điều khiển</div></a>
            <div class="nav-section-title">Giảng dạy</div>
            <a href="<?php echo $baseUrl; ?>/GiangVien/indexLopHocPhan" class="nav-item"><div class="nav-item__icon">📚</div><div>Lớp & môn được dạy</div></a>
            <div class="nav-section-title">Chức năng</div>
            <a href="<?php echo $baseUrl; ?>/GiangVien/nhapDiem" class="nav-item"><div class="nav-item__icon">📝</div><div>Nhập điểm</div></a>
            <a href="<?php echo $baseUrl; ?>/GiangVien/traCuuDiem" class="nav-item"><div class="nav-item__icon">🔍</div><div>Tra cứu điểm</div></a>
            <a href="<?php echo $baseUrl; ?>/GiangVien/guiThongBao" class="nav-item nav-item--active"><div class="nav-item__icon">📧</div><div>Gửi thông báo</div></a>
            <a href="<?php echo $baseUrl; ?>/GiangVien/lichDay" class="nav-item"><div class="nav-item__icon">📆</div><div>Lịch giảng dạy</div></a>
            <a href="<?php echo $baseUrl; ?>/GiangVien/diemDanh" class="nav-item"><div class="nav-item__icon">📋</div><div>Điểm danh</div></a>
        </nav>
    </aside>

    <div class="main">
        <header class="topbar">
            <div>
                <div class="topbar__title">Gửi Thông Báo Cho Sinh Viên</div>
                <div class="topbar__breadcrumb">Giảng dạy / Gửi thông báo</div>
            </div>
            <div class="topbar__right">
                <div class="topbar-dropdown" id="userDropdown">
                    <div class="topbar-dropdown__trigger user-info" id="userDropdownTrigger">
                        <div class="user-avatar"><?php echo strtoupper(mb_substr($giangVienTen, 0, 1, 'UTF-8')); ?></div>
                        <div class="user-meta">
                            <div class="user-meta__name"><?php echo htmlspecialchars($giangVienTen); ?></div>
                            <div class="user-meta__id"><?php echo htmlspecialchars($giangVienMa); ?></div>
                        </div>
                        <i class="fas fa-chevron-down" style="font-size: 11px; color: #a0aec0;"></i>
                    </div>
                    <div class="topbar-dropdown__menu" role="menu">
                        <a class="topbar-dropdown__item" href="<?php echo $baseUrl; ?>/Profile/index"><i class="fas fa-user"></i> Hồ sơ</a>
                        <a class="topbar-dropdown__item" href="<?php echo $baseUrl; ?>/Profile/settings"><i class="fas fa-cog"></i> Cài đặt</a>
                        <div class="topbar-dropdown__divider"></div>
                        <a class="topbar-dropdown__item topbar-dropdown__item--danger" href="<?php echo $baseUrl; ?>/Auth/logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                    </div>
                </div>
            </div>
        </header>

        <main class="content">
            <div id="alertContainer"></div>

            <div class="content-header">
                <div class="content-header__title">Chọn lớp học phần</div>
                <select class="select" id="selectLopHocPhan">
                    <option value="">-- Chọn lớp học phần --</option>
                    <?php foreach ($lopHocPhanList as $lop): ?>
                        <option value="<?php echo htmlspecialchars($lop['MaLopHocPhan']); ?>" <?php echo ($lopHocPhanSelected && $lop['MaLopHocPhan'] == $lopHocPhanSelected['MaLopHocPhan']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($lop['MaLopHocPhan'] . ' - ' . ($lop['TenMonHoc'] ?? $lop['MaMonHoc'] ?? '')); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($lopHocPhanSelected && !empty($sinhVienList)): ?>
                <div class="card">
                    <div class="card__title">Danh sách sinh viên trong lớp</div>
                    <div class="table-wrapper table-wrapper--scroll">
                        <table>
                            <thead class="sticky">
                                <tr>
                                    <th><input type="checkbox" id="checkAll" onchange="toggleAll(this)"></th>
                                    <th>STT</th>
                                    <th>Mã SV</th>
                                    <th>Họ tên</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sinhVienList as $idx => $sv): ?>
                                    <tr>
                                        <td><input type="checkbox" name="sinhVien[]" value="<?php echo htmlspecialchars($sv['MaSinhVien']); ?>" class="checkbox-sv"></td>
                                        <td><?php echo $idx + 1; ?></td>
                                        <td><?php echo htmlspecialchars($sv['MaSinhVien']); ?></td>
                                        <td><?php echo htmlspecialchars($sv['HoTen']); ?></td>
                                        <td><?php echo htmlspecialchars($sv['Email'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($sv['SoDienThoai'] ?? '-'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card__title">Nội dung thông báo</div>
                    <form id="formThongBao" onsubmit="return guiThongBao(event)">
                        <div class="form-group">
                            <label>Tiêu đề *</label>
                            <input type="text" name="tieuDe" id="tieuDe" required placeholder="Nhập tiêu đề thông báo...">
                        </div>
                        <div class="form-group">
                            <label>Nội dung *</label>
                            <textarea name="noiDung" id="noiDung" required placeholder="Nhập nội dung thông báo..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>Gửi đến</label>
                            <div class="checkbox-group">
                                <div class="checkbox-item">
                                    <input type="checkbox" id="chonTatCa" checked onchange="toggleAllCheckboxes(this)">
                                    <label for="chonTatCa">Tất cả sinh viên</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="chonDau" onchange="filterByResult('dau')">
                                    <label for="chonDau">Sinh viên đậu</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="chonRot" onchange="filterByResult('rot')">
                                    <label for="chonRot">Sinh viên rớt</label>
                                </div>
                            </div>
                        </div>
                        <div style="text-align: right; margin-top: 20px;">
                            <button type="button" class="btn btn-outline" onclick="resetForm()">Reset</button>
                            <button type="submit" class="btn btn-success">📧 Gửi thông báo</button>
                        </div>
                    </form>
                </div>
            <?php elseif ($lopHocPhanSelected): ?>
                <div class="card">
                    <div class="empty-state">Lớp học phần này chưa có sinh viên đăng ký.</div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="empty-state">Vui lòng chọn lớp học phần để gửi thông báo.</div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<script>
function toggleAll(checkbox) {
    const checkboxes = document.querySelectorAll('.checkbox-sv');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
}

function toggleAllCheckboxes(checkbox) {
    const checkboxes = document.querySelectorAll('.checkbox-sv');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
    document.getElementById('checkAll').checked = checkbox.checked;
}

function filterByResult(type) {
    toggleAllCheckboxes(document.getElementById('chonTatCa'));
}

function resetForm() {
    document.getElementById('formThongBao').reset();
    document.querySelectorAll('.checkbox-sv').forEach(cb => cb.checked = false);
}

function guiThongBao(e) {
    e.preventDefault();
    const maLopHocPhan = document.getElementById('selectLopHocPhan').value;
    const tieuDe = document.getElementById('tieuDe').value;
    const noiDung = document.getElementById('noiDung').value;
    const selectedSinhVien = Array.from(document.querySelectorAll('.checkbox-sv:checked')).map(cb => cb.value);

    if (!maLopHocPhan) {
        alert('Vui lòng chọn lớp học phần');
        return false;
    }

    if (selectedSinhVien.length === 0) {
        alert('Vui lòng chọn ít nhất một sinh viên');
        return false;
    }

    const btn = e.target.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.textContent = 'Đang gửi...';

    fetch('<?php echo $baseUrl; ?>/GiangVien/guiThongBao', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            maLopHocPhan: maLopHocPhan,
            tieuDe: tieuDe,
            noiDung: noiDung,
            sinhVien: selectedSinhVien
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('alertContainer').innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
            resetForm();
        } else {
            document.getElementById('alertContainer').innerHTML = '<div class="alert alert-error">' + (data.message || 'Lỗi khi gửi thông báo') + '</div>';
        }
    })
    .catch(err => {
        document.getElementById('alertContainer').innerHTML = '<div class="alert alert-error">Lỗi kết nối</div>';
        console.error(err);
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = '📧 Gửi thông báo';
    });

    return false;
}

const selectLop = document.getElementById('selectLopHocPhan');
if (selectLop) {
    selectLop.addEventListener('change', function() {
        const maLop = this.value;
        if (maLop) {
            window.location.href = '<?php echo $baseUrl; ?>/GiangVien/guiThongBao?maLopHocPhan=' + encodeURIComponent(maLop);
        } else {
            window.location.href = '<?php echo $baseUrl; ?>/GiangVien/guiThongBao';
        }
    });
}

(function() {
    var dropdown = document.getElementById('userDropdown');
    var trigger = document.getElementById('userDropdownTrigger');
    if (dropdown && trigger) {
        trigger.addEventListener('click', function(e) { e.stopPropagation(); dropdown.classList.toggle('is-open'); });
        document.addEventListener('click', function() { dropdown.classList.remove('is-open'); });
        dropdown.querySelector('.topbar-dropdown__menu') && dropdown.querySelector('.topbar-dropdown__menu').addEventListener('click', function(e) { e.stopPropagation(); });
    }
})();
</script>
</body>
</html>
