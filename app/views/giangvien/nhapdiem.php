<?php
// Biến từ controller: $giangVien, $lopHocPhanList, $lopHocPhanSelected, $cauTrucDiem
$giangVienTen = $giangVien['HoTen'] ?? 'Giảng viên';
$giangVienMa = $giangVien['MaGiangVien'] ?? '';
$lopHocPhanList = $lopHocPhanList ?? [];
$lopHocPhanSelected = $lopHocPhanSelected ?? null;
$cauTrucDiem = $cauTrucDiem ?? [];
$baseUrl = defined('URLROOT') ? URLROOT : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhập Điểm - UNISCORE Giảng Viên</title>
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
            <a href="<?php echo $baseUrl; ?>/GiangVien/nhapDiem" class="nav-item nav-item--active"><div class="nav-item__icon">📝</div><div>Nhập điểm</div></a>
            <a href="<?php echo $baseUrl; ?>/GiangVien/traCuuDiem" class="nav-item"><div class="nav-item__icon">🔍</div><div>Tra cứu điểm</div></a>
            <a href="<?php echo $baseUrl; ?>/GiangVien/guiThongBao" class="nav-item"><div class="nav-item__icon">📧</div><div>Gửi thông báo</div></a>
            <a href="<?php echo $baseUrl; ?>/GiangVien/lichDay" class="nav-item"><div class="nav-item__icon">📆</div><div>Lịch giảng dạy</div></a>
            <a href="<?php echo $baseUrl; ?>/GiangVien/diemDanh" class="nav-item"><div class="nav-item__icon">📋</div><div>Điểm danh</div></a>
        </nav>
    </aside>

    <div class="main">
        <header class="topbar">
            <div>
                <div class="topbar__title">Nhập & Quản lý Điểm</div>
                <div class="topbar__breadcrumb">Giảng dạy / Nhập điểm</div>
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
            <div class="content-header">
                <div class="content-header__title">Chọn lớp học phần để nhập điểm</div>
                <select class="select" id="selectLopHocPhan">
                    <option value="">-- Chọn lớp học phần --</option>
                    <?php foreach ($lopHocPhanList as $lop): ?>
                        <option value="<?php echo htmlspecialchars($lop['MaLopHocPhan']); ?>" <?php echo ($lopHocPhanSelected && $lop['MaLopHocPhan'] == $lopHocPhanSelected['MaLopHocPhan']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($lop['MaLopHocPhan'] . ' - ' . ($lop['TenMonHoc'] ?? $lop['MaMonHoc'] ?? '')); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="alertContainer"></div>

            <?php if ($lopHocPhanSelected && !empty($cauTrucDiem)): ?>
                <div class="card">
                    <div class="card__title">Cấu trúc điểm môn học</div>
                    <div class="cau-truc-info">
                        <?php foreach ($cauTrucDiem as $ct): ?>
                            <strong><?php echo htmlspecialchars($ct['TenLoaiDiem']); ?>:</strong> Hệ số <?php echo number_format($ct['HeSo'], 2); ?>
                            <?php if ($ct['MoTa']): ?> (<?php echo htmlspecialchars($ct['MoTa']); ?>)<?php endif; ?>
                            <?php if ($ct !== end($cauTrucDiem)): ?> | <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card__title">Danh sách sinh viên và điểm</div>
                    <div class="table-wrapper">
                        <table id="tableDiem">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Mã SV</th>
                                    <th>Họ tên</th>
                                    <?php foreach ($cauTrucDiem as $ct): ?>
                                        <th><?php echo htmlspecialchars($ct['TenLoaiDiem']); ?><br><span class="text-muted">(Hệ số: <?php echo number_format($ct['HeSo'], 2); ?>)</span></th>
                                    <?php endforeach; ?>
                                    <th>Điểm tổng</th>
                                    <th>Điểm chữ</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="tbodySinhVien">
                                <!-- Sẽ được load bằng AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div style="margin-top: 16px; text-align: right;">
                        <button class="btn btn-success" id="btnSaveAll">Lưu tất cả điểm</button>
                    </div>
                </div>
            <?php elseif ($lopHocPhanSelected): ?>
                <div class="card">
                    <div class="empty-state">Môn học này chưa có cấu trúc điểm. Vui lòng liên hệ quản trị viên để thiết lập.</div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="empty-state">Vui lòng chọn lớp học phần để xem danh sách sinh viên và nhập điểm.</div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<script>
(function() {
    const selectLop = document.getElementById('selectLopHocPhan');
    const tbody = document.getElementById('tbodySinhVien');
    const alertContainer = document.getElementById('alertContainer');
    const btnSaveAll = document.getElementById('btnSaveAll');
    let cauTrucDiem = <?php echo json_encode($cauTrucDiem, JSON_UNESCAPED_UNICODE); ?>;
    let currentMaLopHocPhan = null;

    function showAlert(message, type) {
        alertContainer.innerHTML = '<div class="alert alert-' + type + '">' + escapeHtml(message) + '</div>';
        setTimeout(() => { alertContainer.innerHTML = ''; }, 5000);
    }

    function escapeHtml(s) {
        if (s == null) return '';
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function tinhDiemTong(row) {
        let tong = 0;
        cauTrucDiem.forEach(function(ct, idx) {
            const input = row.querySelector('input[data-loai="' + ct.MaLoaiDiem + '"]');
            const diem = parseFloat(input.value) || 0;
            tong += diem * parseFloat(ct.HeSo);
        });
        return tong;
    }

    function chuyenDiemChu(diem) {
        if (diem >= 9) return 'A+';
        if (diem >= 8.5) return 'A';
        if (diem >= 8) return 'B+';
        if (diem >= 7) return 'B';
        if (diem >= 6.5) return 'C+';
        if (diem >= 6) return 'C';
        if (diem >= 5) return 'D+';
        if (diem >= 4) return 'D';
        return 'F';
    }

    function validateDiem(value) {
        const num = parseFloat(value);
        return !isNaN(num) && num >= 0 && num <= 10;
    }

    function loadSinhVienDiem(maLopHocPhan) {
        if (!maLopHocPhan) {
            tbody.innerHTML = '';
            return;
        }
        currentMaLopHocPhan = maLopHocPhan;
        tbody.innerHTML = '<tr><td colspan="' + (5 + cauTrucDiem.length) + '" style="text-align:center;padding:20px;">Đang tải...</td></tr>';

        fetch('<?php echo $baseUrl; ?>/GiangVien/getSinhVienDiem?maLopHocPhan=' + encodeURIComponent(maLopHocPhan))
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderTable(data.sinhVien);
                } else {
                    tbody.innerHTML = '<tr><td colspan="' + (5 + cauTrucDiem.length) + '" style="text-align:center;padding:20px;color:#a0aec0;">' + escapeHtml(data.message || 'Không có dữ liệu') + '</td></tr>';
                }
            })
            .catch(err => {
                tbody.innerHTML = '<tr><td colspan="' + (5 + cauTrucDiem.length) + '" style="text-align:center;padding:20px;color:#e53e3e;">Lỗi khi tải dữ liệu</td></tr>';
                console.error(err);
            });
    }

    function renderTable(sinhVien) {
        if (!sinhVien || sinhVien.length === 0) {
            tbody.innerHTML = '<tr><td colspan="' + (5 + cauTrucDiem.length) + '" style="text-align:center;padding:20px;color:#a0aec0;">Lớp học phần này chưa có sinh viên đăng ký.</td></tr>';
            return;
        }

        tbody.innerHTML = sinhVien.map(function(sv, idx) {
            let html = '<tr data-ma-dang-ky="' + escapeHtml(sv.MaDangKy || '') + '" data-ma-sinh-vien="' + escapeHtml(sv.MaSinhVien) + '">';
            html += '<td>' + (idx + 1) + '</td>';
            html += '<td>' + escapeHtml(sv.MaSinhVien) + '</td>';
            html += '<td>' + escapeHtml(sv.HoTen) + '</td>';

            cauTrucDiem.forEach(function(ct) {
                const diem = sv.diem && sv.diem[ct.MaLoaiDiem] ? sv.diem[ct.MaLoaiDiem].SoDiem : '';
                html += '<td><input type="number" step="0.01" min="0" max="10" class="input-diem" data-loai="' + escapeHtml(ct.MaLoaiDiem) + '" value="' + escapeHtml(diem) + '" placeholder="0-10"></td>';
            });

            const diemTong = sv.DiemTongKet || '';
            const diemChu = sv.DiemChu || '';
            html += '<td><strong id="diem-tong-' + idx + '">' + escapeHtml(diemTong ? diemTong.toFixed(2) : '') + '</strong></td>';
            html += '<td><span class="badge badge-success" id="diem-chu-' + idx + '">' + escapeHtml(diemChu) + '</span></td>';
            html += '<td><button class="btn btn-primary btn-sm" onclick="saveRow(this)">Lưu</button></td>';
            html += '</tr>';

            return html;
        }).join('');

        // Gắn event listener cho các input điểm
        tbody.querySelectorAll('.input-diem').forEach(function(input) {
            input.addEventListener('input', function() {
                if (!validateDiem(this.value)) {
                    this.classList.add('error');
                } else {
                    this.classList.remove('error');
                    const row = this.closest('tr');
                    const tong = tinhDiemTong(row);
                    const idx = Array.from(row.parentNode.children).indexOf(row);
                    document.getElementById('diem-tong-' + idx).textContent = tong.toFixed(2);
                    document.getElementById('diem-chu-' + idx).textContent = chuyenDiemChu(tong);
                }
            });
        });
    }

    window.saveRow = function(btn) {
        const row = btn.closest('tr');
        const maDangKy = row.getAttribute('data-ma-dang-ky');
        if (!maDangKy) {
            showAlert('Không tìm thấy mã đăng ký', 'error');
            return;
        }

        const diemData = {};
        let hasError = false;
        let missingFields = [];
        
        cauTrucDiem.forEach(function(ct) {
            const input = row.querySelector('input[data-loai="' + ct.MaLoaiDiem + '"]');
            const value = input.value.trim();
            
            // Kiểm tra bắt buộc nhập đủ tất cả điểm thành phần
            if (!value || value === '') {
                input.classList.add('error');
                missingFields.push(ct.TenLoaiDiem || ct.MaLoaiDiem);
                hasError = true;
            } else if (!validateDiem(value)) {
                input.classList.add('error');
                hasError = true;
            } else {
                input.classList.remove('error');
                diemData[ct.MaLoaiDiem] = parseFloat(value);
            }
        });

        if (missingFields.length > 0) {
            showAlert('Vui lòng nhập đầy đủ tất cả điểm thành phần: ' + missingFields.join(', '), 'error');
            return;
        }

        if (hasError) {
            showAlert('Vui lòng nhập điểm hợp lệ (0-10)', 'error');
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Đang lưu...';

        fetch('<?php echo $baseUrl; ?>/GiangVien/saveDiem', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ maDangKy: maDangKy, diem: diemData })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showAlert('Lưu điểm thành công!', 'success');
                loadSinhVienDiem(currentMaLopHocPhan);
            } else {
                showAlert(data.message || 'Lỗi khi lưu điểm', 'error');
            }
        })
        .catch(err => {
            showAlert('Lỗi kết nối', 'error');
            console.error(err);
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Lưu';
        });
    };

    if (btnSaveAll) {
        btnSaveAll.addEventListener('click', function() {
            const rows = tbody.querySelectorAll('tr[data-ma-dang-ky]');
            let saved = 0;
            rows.forEach(function(row) {
                const btn = row.querySelector('button');
                if (btn) {
                    saveRow(btn);
                    saved++;
                }
            });
            if (saved === 0) {
                showAlert('Không có dữ liệu để lưu', 'error');
            }
        });
    }

    if (selectLop) {
        selectLop.addEventListener('change', function() {
            const maLop = this.value;
            if (maLop) {
                window.location.href = '<?php echo $baseUrl; ?>/GiangVien/nhapDiem?maLopHocPhan=' + encodeURIComponent(maLop);
            } else {
                window.location.href = '<?php echo $baseUrl; ?>/GiangVien/nhapDiem';
            }
        });
    }

    // Tự động load nếu đã chọn lớp
    <?php if ($lopHocPhanSelected): ?>
    loadSinhVienDiem('<?php echo htmlspecialchars($lopHocPhanSelected['MaLopHocPhan']); ?>');
    <?php endif; ?>

    (function() {
        var dropdown = document.getElementById('userDropdown');
        var trigger = document.getElementById('userDropdownTrigger');
        if (dropdown && trigger) {
            trigger.addEventListener('click', function(e) { e.stopPropagation(); dropdown.classList.toggle('is-open'); });
            document.addEventListener('click', function() { dropdown.classList.remove('is-open'); });
            dropdown.querySelector('.topbar-dropdown__menu') && dropdown.querySelector('.topbar-dropdown__menu').addEventListener('click', function(e) { e.stopPropagation(); });
        }
    })();
})();
</script>
</body>
</html>
