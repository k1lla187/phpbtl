<?php
$pageActive = 'xemdiemdanh';
$pageTitle = 'Xem điểm danh';
$breadcrumb = 'Cổng Sinh viên / Xem điểm danh';
$diemDanhList = $diemDanhList ?? [];
$hocKys = $hocKys ?? [];
$baseUrl = defined('URLROOT') ? URLROOT : '';

$maHK = $_GET['maHocKy'] ?? null;

require_once __DIR__ . '/_layout_sv.php';
?>
<div class="content-header">
    <div class="content-header__title">Xem điểm danh</div>
    <form method="get" action="" style="display: flex; gap: 8px;">
        <select name="maHocKy" class="form-select" style="min-width: 220px;" onchange="this.form.submit()">
            <option value="">Tất cả học kỳ</option>
            <?php foreach ($hocKys as $hk): ?>
                <option value="<?= htmlspecialchars($hk['MaHocKy']) ?>" <?= ($maHK === $hk['MaHocKy']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($hk['TenHocKy'] ?? '') ?> - <?= $hk['NamHoc'] ?? '' ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">Bảng theo dõi điểm danh</h5>
    </div>
    <div class="card-body">
        <?php if (empty($diemDanhList)): ?>
            <div class="empty-state">
                <i class="fas fa-clipboard-check"></i>
                <h5>Chưa có dữ liệu điểm danh</h5>
                <p>Bạn chưa đăng ký môn học nào hoặc chưa có buổi điểm danh nào.</p>
                <a href="<?= $baseUrl ?>/SinhVien/dangKyHoc" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Đăng ký học phần
                </a>
            </div>
        <?php else: ?>
            <div class="info-box" style="background: #f0f9ff; border-left: 4px solid #0d9488; padding: 16px; margin-bottom: 20px; border-radius: 4px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-info-circle" style="color: #0d9488; font-size: 20px;"></i>
                    <div>
                        <strong>Quy định điểm danh:</strong>
                        <ul style="margin: 8px 0 0 20px; padding: 0; color: #475569; font-size: 14px;">
                            <li>1 tín chỉ = 5 buổi học = 15 tiết</li>
                            <li>Sinh viên cần tham gia tối thiểu <strong>80%</strong> số buổi học để đạt điểm chuyên cần</li>
                            <li>Điểm chuyên cần = (% tham gia × 10)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="table-wrapper" style="overflow-x: auto;">
                <table class="table" id="table-diemdanh">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Mã LHP</th>
                            <th>Môn học</th>
                            <th style="width: 80px;">Số TC</th>
                            <th style="width: 140px;">Học kỳ</th>
                            <th style="width: 100px;">Số buổi đã điểm danh</th>
                            <th style="width: 100px;">Số buổi có mặt</th>
                            <th style="width: 100px;">Tổng buổi</th>
                            <th style="width: 100px;">% Tham gia</th>
                            <th style="width: 100px;">Điểm CC</th>
                            <th style="width: 100px;">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($diemDanhList as $dd): ?>
                            <?php 
                                $phanTram = $dd['PhanTramThamGia'] ?? 0;
                                $diemCC = $dd['DiemChuyenCan'] ?? null;
                                $daDuBuoi = $dd['DaDuBuoi'] ?? false;
                                $soBuoiDaDD = $dd['SoBuoiDaDiemDanh'] ?? 0;
                                $soBuoiCoMat = $dd['SoBuoiCoMat'] ?? 0;
                                $tongBuoi = $dd['TongBuoi'] ?? 0;
                                
                                // Số buổi thiếu = số buổi chưa được điểm danh
                                // (dễ hiểu hơn cho sinh viên so với tính theo 80%)
                                $soBuoiThieu = $dd['SoBuoiConLai'] ?? max(0, $tongBuoi - $soBuoiDaDD);
                            ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($dd['MaLopHocPhan'] ?? '') ?></strong></td>
                                <td><?= htmlspecialchars($dd['TenMonHoc'] ?? '') ?></td>
                                <td><?= $dd['SoTinChi'] ?? '' ?></td>
                                <td>
                                    <?= htmlspecialchars($dd['TenHocKy'] ?? '') ?>
                                    <div style="font-size: 11px; color: #6b7280;"><?= $dd['NamHoc'] ?? '' ?></div>
                                </td>
                                <td><?= $soBuoiDaDD ?></td>
                                <td><?= $soBuoiCoMat ?></td>
                                <td><?= $tongBuoi ?></td>
                                <td>
                                    <div class="progress-bar" style="width: 80px; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                                        <div class="progress-fill" style="width: <?= min(100, $phanTram) ?>%; height: 100%; background: <?= $phanTram >= 80 ? '#22c55e' : ($phanTram >= 50 ? '#f59e0b' : '#ef4444') ?>; transition: width 0.3s;"></div>
                                    </div>
                                    <div style="font-size: 12px; margin-top: 4px;"><?= $phanTram ?>%</div>
                                </td>
                                <td>
                                    <?php if ($diemCC !== null): ?>
                                        <strong><?= number_format($diemCC, 1) ?></strong>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($soBuoiDaDD == 0): ?>
                                        <span class="badge badge-secondary">Chưa điểm danh</span>
                                    <?php elseif ($daDuBuoi): ?>
                                        <span class="badge badge-success"><i class="fas fa-check"></i> Đủ buổi</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> Thiếu <?= $soBuoiThieu ?> buổi</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Tổng kết -->
            <?php 
                $tongMon = count($diemDanhList);
                $monDuBuoi = count(array_filter($diemDanhList, function($dd) { return $dd['DaDuBuoi'] ?? false; }));
                $monThieuBuoi = $tongMon - $monDuBuoi;
            ?>
            <div style="display: flex; gap: 20px; margin-top: 24px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <div style="flex: 1; text-align: center; padding: 16px; background: #f8fafc; border-radius: 8px;">
                    <div style="font-size: 28px; font-weight: 700; color: #1e293b;"><?= $tongMon ?></div>
                    <div style="font-size: 13px; color: #64748b;">Tổng môn học</div>
                </div>
                <div style="flex: 1; text-align: center; padding: 16px; background: #f0fdf4; border-radius: 8px;">
                    <div style="font-size: 28px; font-weight: 700; color: #16a34a;"><?= $monDuBuoi ?></div>
                    <div style="font-size: 13px; color: #64748b;">Đủ buổi</div>
                </div>
                <div style="flex: 1; text-align: center; padding: 16px; background: #fef2f2; border-radius: 8px;">
                    <div style="font-size: 28px; font-weight: 700; color: #dc2626;"><?= $monThieuBuoi ?></div>
                    <div style="font-size: 13px; color: #64748b;">Thiếu buổi</div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
</style>
<?php require_once __DIR__ . '/_layout_sv_footer.php';
