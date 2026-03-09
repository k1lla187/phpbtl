<?php require_once '../app/views/layouts/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <h4><i class="fas fa-chart-bar me-2"></i>Thống kê Báo cáo</h4>
    <div>
        <button class="btn btn-outline-primary me-2" onclick="window.print()">
            <i class="fas fa-print me-2"></i>In báo cáo
        </button>
        <a href="index.php?url=ThongKe/exportExcel" class="btn btn-success">
            <i class="fas fa-file-excel me-2"></i>Xuất Excel
        </a>
    </div>
</div>

<!-- Main Statistics -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-user-graduate"></i></div>
            <div class="stat-value"><?= $data['totalSV'] ?? 0 ?></div>
            <div class="stat-label">Sinh viên</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="stat-value"><?= $data['totalGV'] ?? 0 ?></div>
            <div class="stat-label">Giảng viên</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-icon yellow"><i class="fas fa-book"></i></div>
            <div class="stat-value"><?= $data['totalMH'] ?? 0 ?></div>
            <div class="stat-label">Môn học</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-layer-group"></i></div>
            <div class="stat-value"><?= $data['totalLHP'] ?? 0 ?></div>
            <div class="stat-label">Lớp HP</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-clipboard-list"></i></div>
            <div class="stat-value"><?= $data['totalDK'] ?? 0 ?></div>
            <div class="stat-label">Đăng ký</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-percentage"></i></div>
            <div class="stat-value"><?= $data['passRate'] ?? 0 ?>%</div>
            <div class="stat-label">Tỷ lệ đậu</div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-chart-pie me-2"></i>Thống kê Kết quả học tập</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="p-3 border rounded bg-success bg-opacity-10">
                            <div class="fs-2 fw-bold text-success"><?= $data['passed'] ?? 0 ?></div>
                            <div class="text-muted">Đậu</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 border rounded bg-danger bg-opacity-10">
                            <div class="fs-2 fw-bold text-danger"><?= $data['failed'] ?? 0 ?></div>
                            <div class="text-muted">Rớt</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 border rounded bg-warning bg-opacity-10">
                            <div class="fs-2 fw-bold text-warning"><?= ($data['totalDK'] ?? 0) - ($data['passed'] ?? 0) - ($data['failed'] ?? 0) ?></div>
                            <div class="text-muted">Chờ duyệt</div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <div class="progress" style="height: 25px;">
                        <?php 
                        $total = $data['totalDK'] ?? 1;
                        $passPercent = $total > 0 ? ($data['passed'] / $total * 100) : 0;
                        $failPercent = $total > 0 ? ($data['failed'] / $total * 100) : 0;
                        ?>
                        <div class="progress-bar bg-success" style="width: <?= $passPercent ?>%"><?= round($passPercent, 1) ?>% Đậu</div>
                        <div class="progress-bar bg-danger" style="width: <?= $failPercent ?>%"><?= round($failPercent, 1) ?>% Rớt</div>
                        <div class="progress-bar bg-warning" style="width: <?= 100 - $passPercent - $failPercent ?>%">Đang xử lý</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-venus-mars me-2"></i>Thống kê Giới tính Sinh viên</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="p-4 border rounded bg-primary bg-opacity-10">
                            <i class="fas fa-mars text-primary fs-1 mb-2"></i>
                            <div class="fs-2 fw-bold text-primary"><?= $data['maleCount'] ?? 0 ?></div>
                            <div class="text-muted">Nam</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 border rounded bg-danger bg-opacity-10">
                            <i class="fas fa-venus text-danger fs-1 mb-2"></i>
                            <div class="fs-2 fw-bold text-danger"><?= $data['femaleCount'] ?? 0 ?></div>
                            <div class="text-muted">Nữ</div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <?php 
                    $totalGender = ($data['maleCount'] ?? 0) + ($data['femaleCount'] ?? 0);
                    $malePercent = $totalGender > 0 ? ($data['maleCount'] / $totalGender * 100) : 50;
                    ?>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-primary" style="width: <?= $malePercent ?>%"><?= round($malePercent, 1) ?>% Nam</div>
                        <div class="progress-bar bg-danger" style="width: <?= 100 - $malePercent ?>%"><?= round(100 - $malePercent, 1) ?>% Nữ</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Data -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="fas fa-user-graduate me-2"></i>Sinh viên mới nhất</h5>
                <a href="index.php?url=SinhVien" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>MSSV</th>
                            <th>Họ tên</th>
                            <th>Lớp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($data['sinhviens'])): ?>
                            <?php foreach(array_slice($data['sinhviens'], 0, 5) as $sv): ?>
                            <tr>
                                <td><strong><?= $sv['MaSinhVien'] ?></strong></td>
                                <td><?= $sv['HoTen'] ?></td>
                                <td><span class="badge bg-info"><?= $sv['MaLop'] ?? '' ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center text-muted py-3">Chưa có dữ liệu</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Giảng viên</h5>
                <a href="index.php?url=GiangVien" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Mã GV</th>
                            <th>Họ tên</th>
                            <th>Khoa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($data['giangviens'])): ?>
                            <?php foreach(array_slice($data['giangviens'], 0, 5) as $gv): ?>
                            <tr>
                                <td><strong><?= $gv['MaGiangVien'] ?></strong></td>
                                <td><?= $gv['HoTen'] ?></td>
                                <td><span class="badge bg-success"><?= $gv['MaKhoa'] ?? '' ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center text-muted py-3">Chưa có dữ liệu</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
