<?php
$pageActive = 'xemdiem';
$pageTitle = 'Xem điểm';
$breadcrumb = 'Cổng Sinh viên / Xem điểm';
$hocKyList = $hocKyList ?? [];
$dangKyList = $dangKyList ?? [];
$chiTietByDangKy = $chiTietByDangKy ?? [];
$cauTrucByMonHoc = $cauTrucByMonHoc ?? [];
$maHK = $maHK ?? null;
$tbHK = $tbHK ?? null;
$tbToanKhoa = $tbToanKhoa ?? null;
$tbcTichLuyThang4 = $tbcTichLuyThang4 ?? null;
$xepHangHocLuc = $xepHangHocLuc ?? '—';
$tbcHocTapThang4 = $tbcHocTapThang4 ?? null;
$xepLoaiThang4 = $xepLoaiThang4 ?? '—';
$tongTinChiTichLuy = $tongTinChiTichLuy ?? 0;
$tbcHocTapThang10 = $tbcHocTapThang10 ?? null;
$xepLoaiThang10 = $xepLoaiThang10 ?? '—';
require_once __DIR__ . '/_layout_sv.php';
?>
<div class="page-header">
    <h4 class="content-header__title">Xem điểm theo môn, theo học kỳ</h4>
    <form method="get" action="<?= rtrim($baseUrl ?? '', '/') ?>/index.php" class="filter-bar" style="margin-bottom: 0;">
        <input type="hidden" name="url" value="SinhVien/xemDiem">
        <select name="maHocKy" class="form-select" onchange="this.form.submit()">
            <option value="">— Tất cả học kỳ —</option>
            <?php foreach ($hocKyList as $hk): ?>
                <option value="<?= htmlspecialchars($hk['value']) ?>" <?= ($maHK && $maHK === $hk['value']) ? 'selected' : '' ?>><?= htmlspecialchars($hk['label']) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<!-- Thông tin tổng quát -->
<div class="card mb-4">
    <div class="card-header"><h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Thông tin tổng quát</h5></div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
            <div class="info-box">
                <div class="info-box__label">TBC tích lũy thang điểm 4</div>
                <div class="info-box__value"><?= $tbcTichLuyThang4 !== null ? number_format($tbcTichLuyThang4, 2) : '—' ?></div>
            </div>
            <div class="info-box">
                <div class="info-box__label">Xếp hạng học lực</div>
                <div class="info-box__value"><?= htmlspecialchars($xepHangHocLuc) ?></div>
            </div>
            <div class="info-box">
                <div class="info-box__label">TBC học tập thang 4</div>
                <div class="info-box__value"><?= $tbcHocTapThang4 !== null ? number_format($tbcHocTapThang4, 2) : '—' ?></div>
            </div>
            <div class="info-box">
                <div class="info-box__label">Xếp loại học tập thang 4</div>
                <div class="info-box__value"><?= htmlspecialchars($xepLoaiThang4) ?></div>
            </div>
            <div class="info-box">
                <div class="info-box__label">Số tín chỉ đã tích lũy</div>
                <div class="info-box__value"><?= (int)$tongTinChiTichLuy ?></div>
            </div>
            <div class="info-box">
                <div class="info-box__label">TBC học tập thang 10</div>
                <div class="info-box__value"><?= $tbcHocTapThang10 !== null ? number_format($tbcHocTapThang10, 2) : '—' ?></div>
            </div>
            <div class="info-box">
                <div class="info-box__label">Xếp loại học tập thang 10</div>
                <div class="info-box__value"><?= htmlspecialchars($xepLoaiThang10) ?></div>
            </div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-calendar-alt"></i></div>
        <div class="stat-value"><?= $tbHK !== null ? number_format($tbHK, 2) : '—' ?></div>
        <div class="stat-label">Điểm trung bình học kỳ <?= $maHK ? '(đã lọc)' : '' ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon cyan"><i class="fas fa-graduation-cap"></i></div>
        <div class="stat-value"><?= $tbToanKhoa !== null ? number_format($tbToanKhoa, 2) : '—' ?></div>
        <div class="stat-label">Điểm trung bình toàn khóa</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-star me-2"></i>Bảng điểm cá nhân</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Học kỳ</th>
                        <th>Năm học</th>
                        <th>Mã học phần</th>
                        <th>Tên học phần</th>
                        <th>Số tín chỉ</th>
                        <th>Thang điểm 10</th>
                        <th>Thang điểm 4</th>
                        <th>Thang điểm chữ</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $diem10To4 = function($d) {
                        if ($d === null) return '-';
                        $d = (float)$d;
                        if ($d >= 9) return '4.0';
                        if ($d >= 8.5) return '3.7';
                        if ($d >= 8) return '3.3';
                        if ($d >= 7) return '3.0';
                        if ($d >= 6.5) return '2.7';
                        if ($d >= 6) return '2.3';
                        if ($d >= 5.5) return '2.0';
                        if ($d >= 5) return '1.5';
                        if ($d >= 4) return '1.0';
                        if ($d >= 3) return '0.7';
                        return '0';
                    };
                    ?>
                    <?php if (!empty($dangKyList)): ?>
                        <?php foreach ($dangKyList as $dk):
                            $chiTiet = $chiTietByDangKy[$dk['MaDangKy']] ?? [];
                            $diemByLoai = [];
                            $tenByLoai = [];
                            foreach ($chiTiet as $ct) {
                                $maLoai = $ct['MaLoaiDiem'] ?? '';
                                if ($maLoai && !isset($diemByLoai[$maLoai])) {
                                    $diemByLoai[$maLoai] = (float)($ct['SoDiem'] ?? 0);
                                    $tenByLoai[$maLoai] = $ct['TenLoaiDiem'] ?? $maLoai;
                                }
                            }
                            $diemCC = $diemGK = $diemCK = null;
                            foreach ($diemByLoai as $maLoai => $soDiem) {
                                $loai = strtoupper(($tenByLoai[$maLoai] ?? '') . $maLoai);
                                if (strpos($loai, 'CC') !== false || strpos($loai, 'CHUYÊN CẦN') !== false) $diemCC = $soDiem;
                                elseif (strpos($loai, 'GK') !== false || strpos($loai, 'GIỮA KỲ') !== false) $diemGK = $soDiem;
                                elseif (strpos($loai, 'CK') !== false || strpos($loai, 'CUỐI KỲ') !== false) $diemCK = $soDiem;
                            }
                            $namHocVal = (int)($dk['NamHoc'] ?? 0);
                            $tenHocKyFull = $namHocVal ? ($namHocVal . '-' . ($namHocVal + 1)) : ($dk['TenHocKy'] ?? '-');
                            $detailJson = htmlspecialchars(json_encode([
                                'tenHocKy' => $tenHocKyFull,
                                'diemCC' => $diemCC !== null ? number_format($diemCC, 2) : '-',
                                'diemGK' => $diemGK !== null ? number_format($diemGK, 2) : '-',
                                'diemCK' => $diemCK !== null ? number_format($diemCK, 2) : '-',
                                'diemTK' => $dk['DiemTongKet'] !== null ? number_format((float)$dk['DiemTongKet'], 2) : '-',
                                'diemChu' => $dk['DiemChu'] ?? '-',
                                'tenMon' => $dk['TenMonHoc'] ?? '',
                            ]), ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($dk['TenHocKy'] ?? $dk['MaHocKy']) ?></td>
                            <td><?= (int)($dk['NamHoc'] ?? '') ?: '-' ?></td>
                            <td><?= htmlspecialchars($dk['MaLopHocPhan'] ?? $dk['MaMonHoc'] ?? '-') ?></td>
                            <td><strong><?= htmlspecialchars($dk['TenMonHoc'] ?? '') ?></strong></td>
                            <td><?= (int)($dk['SoTinChi'] ?? 0) ?></td>
                            <td><strong><?= $dk['DiemTongKet'] !== null ? number_format((float)$dk['DiemTongKet'], 1) : '-' ?></strong></td>
                            <td><?= $diem10To4($dk['DiemTongKet'] ?? null) ?></td>
                            <td><?= htmlspecialchars($dk['DiemChu'] ?? '-') ?></td>
                            <td>
                                <button type="button" class="btn btn-outline-primary btn-xem-chitiet" style="padding: 6px 12px; font-size: 13px;" data-detail="<?= $detailJson ?>" title="Xem chi tiết điểm">
                                    <i class="fas fa-eye"></i> Xem chi tiết
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">Chưa có dữ liệu đăng ký môn học.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal chi tiết điểm -->
<div id="modalChiTietDiem" class="modal-overlay" style="display: none;">
    <div class="modal-box">
        <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-list-alt me-2"></i>Chi tiết điểm: <span id="modalTenMon"></span></h5>
            <button type="button" class="modal-close" onclick="closeModalChiTiet()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tên học kỳ</th>
                            <th>CC</th>
                            <th>GK</th>
                            <th>CK</th>
                            <th>TK</th>
                            <th>Điểm chữ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="modalTenHocKy"></td>
                            <td id="modalDiemCC"></td>
                            <td id="modalDiemGK"></td>
                            <td id="modalDiemCK"></td>
                            <td id="modalDiemTK"></td>
                            <td id="modalDiemChu"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px; }
.modal-box { background: #fff; border-radius: 10px; max-width: 700px; width: 100%; box-shadow: 0 10px 40px rgba(0,0,0,0.2); max-height: 90vh; overflow: hidden; display: flex; flex-direction: column; }
.modal-header { padding: 16px 20px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; }
.modal-title { font-size: 16px; font-weight: 600; margin: 0; }
.modal-close { background: none; border: none; font-size: 24px; cursor: pointer; color: #64748b; line-height: 1; padding: 0 4px; }
.modal-close:hover { color: #1e293b; }
.modal-body { padding: 20px; overflow-y: auto; }
</style>

<script>
function openModalChiTiet(detail) {
    var d = typeof detail === 'string' ? JSON.parse(detail) : detail;
    document.getElementById('modalTenMon').textContent = d.tenMon || '-';
    document.getElementById('modalTenHocKy').textContent = d.tenHocKy || '-';
    document.getElementById('modalDiemCC').textContent = d.diemCC || '-';
    document.getElementById('modalDiemGK').textContent = d.diemGK || '-';
    document.getElementById('modalDiemCK').textContent = d.diemCK || '-';
    document.getElementById('modalDiemTK').textContent = d.diemTK || '-';
    document.getElementById('modalDiemChu').textContent = d.diemChu || '-';
    document.getElementById('modalChiTietDiem').style.display = 'flex';
}
function closeModalChiTiet() {
    document.getElementById('modalChiTietDiem').style.display = 'none';
}
document.querySelectorAll('.btn-xem-chitiet').forEach(function(btn) {
    btn.addEventListener('click', function() {
        openModalChiTiet(this.getAttribute('data-detail'));
    });
});
document.getElementById('modalChiTietDiem').addEventListener('click', function(e) {
    if (e.target === this) closeModalChiTiet();
});
</script>

<?php require_once __DIR__ . '/_layout_sv_footer.php'; ?>
