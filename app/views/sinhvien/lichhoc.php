<?php
$pageActive = 'lichhoc';
$pageTitle = 'Thời khóa biểu';
$breadcrumb = 'Cổng Sinh viên / Thời khóa biểu';
$lichHoc = $lichHoc ?? [];
$hocKys = $hocKys ?? [];
$maHocKy = $_GET['maHocKy'] ?? null;
$baseUrl = defined('URLROOT') ? URLROOT : '';
$thuLabels = [2 => 'Thứ 2', 3 => 'Thứ 3', 4 => 'Thứ 4', 5 => 'Thứ 5', 6 => 'Thứ 6', 7 => 'Thứ 7'];
require_once __DIR__ . '/_layout_sv.php';
?>
<div class="content-header">
    <div class="content-header__title">Thời khóa biểu học tập</div>
    <form method="get" action="" style="display: flex; gap: 8px;">
        <select name="maHocKy" class="form-select" style="min-width: 220px;" onchange="this.form.submit()">
            <option value="">Tất cả học kỳ</option>
            <?php foreach ($hocKys as $hk): ?>
                <option value="<?= htmlspecialchars($hk['MaHocKy']) ?>" <?= ($maHocKy === $hk['MaHocKy']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($hk['TenHocKy'] ?? '') ?> - <?= $hk['NamHoc'] ?? '' ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">Lịch học theo tuần</h5>
    </div>
    <div class="card-body">
        <?php if (empty($lichHoc)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h5>Chưa có lịch học</h5>
                <p>Bạn chưa đăng ký môn học nào hoặc chưa có thời khóa biểu cho học kỳ này.</p>
            </div>
        <?php else:
            $tietMax = 15;
            $slots = [];
            $rowspan = [];
            for ($t = 1; $t <= $tietMax; $t++) {
                for ($thu = 2; $thu <= 7; $thu++) {
                    $slots[$t][$thu] = null;
                    $rowspan[$t][$thu] = 0;
                }
            }
            foreach ($lichHoc as $ca) {
                $tietBD = (int)($ca['TietBatDau'] ?? 1);
                $tietKT = (int)($ca['TietKetThuc'] ?? 1);
                $thu = (int)($ca['Thu'] ?? 2);
                for ($t = $tietBD; $t <= $tietKT; $t++) {
                    $slots[$t][$thu] = $ca;
                }
                $rowspan[$tietBD][$thu] = max(1, $tietKT - $tietBD + 1);
            }
            $covered = [];
            for ($thu = 2; $thu <= 7; $thu++) $covered[$thu] = 0;
        ?>
        <div class="table-wrapper" style="overflow-x: auto;">
            <table class="table" style="min-width: 700px;">
                <thead>
                    <tr>
                        <th style="width: 70px;">Tiết</th>
                        <?php for ($thu = 2; $thu <= 7; $thu++): ?>
                            <th><?= $thuLabels[$thu] ?? 'T' . $thu ?></th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($tiet = 1; $tiet <= $tietMax; $tiet++): ?>
                        <tr>
                            <td><strong>Tiết <?= $tiet ?></strong></td>
                            <?php for ($thu = 2; $thu <= 7; $thu++): ?>
                                <?php
                                if (isset($covered[$thu]) && $covered[$thu] > 0) { $covered[$thu]--; continue; }
                                $ca = $slots[$tiet][$thu] ?? null;
                                $rs = (int)($rowspan[$tiet][$thu] ?? 0);
                                if ($rs > 0) $covered[$thu] = $rs - 1;
                                ?>
                                <td <?= $rs > 0 ? 'rowspan="' . $rs . '"' : '' ?> style="vertical-align: top; min-width: 120px;<?= $ca ? ' background: rgba(13, 148, 136, 0.08); border-left: 3px solid var(--primary-color, #0d9488);' : '' ?>">
                                    <?php if ($ca): ?>
                                        <div style="padding: 8px; border-radius: 8px;">
                                            <div style="font-weight: 600; color: var(--primary-dark, #0f766e);"><?= htmlspecialchars($ca['TenMonHoc'] ?? '') ?></div>
                                            <div class="text-muted" style="font-size: 11px;"><?= htmlspecialchars($ca['MaLopHocPhan'] ?? '') ?></div>
                                            <div class="text-muted" style="font-size: 11px;">GV: <?= htmlspecialchars($ca['TenGiangVien'] ?? '-') ?></div>
                                            <div class="text-muted" style="font-size: 11px;">Phòng: <?= htmlspecialchars($ca['PhongHoc'] ?? $ca['PhongMacDinh'] ?? '-') ?></div>
                                            <div class="text-muted" style="font-size: 10px;">Tiết <?= $ca['TietBatDau'] ?? '' ?>-<?= $ca['TietKetThuc'] ?? '' ?></div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php require_once __DIR__ . '/_layout_sv_footer.php'; ?>
