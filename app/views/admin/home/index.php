<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <h4><i class="fas fa-tachometer-alt me-2"></i>Tổng quan Hệ thống</h4>
    <div>
        <span class="badge bg-primary"><i class="fas fa-calendar me-1"></i><?= date('d/m/Y') ?></span>
        <span class="badge bg-success ms-2"><i class="fas fa-clock me-1"></i><?= date('H:i') ?></span>
    </div>
</div>

<!-- Main Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-user-graduate"></i></div>
            <div class="stat-value"><?= number_format($data['totalSV'] ?? 0) ?></div>
            <div class="stat-label">Sinh viên</div>
            <div class="stat-change text-success"><i class="fas fa-arrow-up"></i> Hoạt động</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="stat-value"><?= number_format($data['totalGV'] ?? 0) ?></div>
            <div class="stat-label">Giảng viên</div>
            <div class="stat-change text-success"><i class="fas fa-check"></i> Đang giảng dạy</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3 mb-md-0">
        <div class="stat-card">
            <div class="stat-icon yellow"><i class="fas fa-book"></i></div>
            <div class="stat-value"><?= number_format($data['totalMH'] ?? 0) ?></div>
            <div class="stat-label">Môn học</div>
            <div class="stat-change text-info"><i class="fas fa-database"></i> Đã thiết lập</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-layer-group"></i></div>
            <div class="stat-value"><?= number_format($data['totalLHP'] ?? 0) ?></div>
            <div class="stat-label">Lớp học phần</div>
            <div class="stat-change text-primary"><i class="fas fa-play"></i> Đang mở</div>
        </div>
    </div>
</div>

<!-- Secondary Stats Row -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="stat-icon-sm bg-primary-subtle text-primary"><i class="fas fa-building"></i></div>
                </div>
                <div>
                    <h3 class="mb-0"><?= $data['totalKhoa'] ?? 0 ?></h3>
                    <small class="text-muted">Khoa</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="stat-icon-sm bg-success-subtle text-success"><i class="fas fa-graduation-cap"></i></div>
                </div>
                <div>
                    <h3 class="mb-0"><?= $data['totalNganh'] ?? 0 ?></h3>
                    <small class="text-muted">Ngành đào tạo</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3 mb-md-0">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="stat-icon-sm bg-info-subtle text-info"><i class="fas fa-file-signature"></i></div>
                </div>
                <div>
                    <h3 class="mb-0"><?= $data['totalDangKy'] ?? 0 ?></h3>
                    <small class="text-muted">Lượt đăng ký HP</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="stat-icon-sm bg-warning-subtle text-warning"><i class="fas fa-star"></i></div>
                </div>
                <div>
                    <h3 class="mb-0"><?= $data['totalGraded'] ?? 0 ?></h3>
                    <small class="text-muted">Đã chấm điểm</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts & Stats Row -->
<div class="row mb-4">
    <!-- Grade Statistics -->
    <div class="col-xl-4 col-md-6 mb-4 mb-xl-0">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pb-0">
                <h5 class="card-title mb-0"><i class="fas fa-chart-pie text-primary me-2"></i>Thống kê Kết quả</h5>
            </div>
            <div class="card-body">
                <div class="chart-container mb-4" style="position: relative; height: 200px;">
                    <canvas id="gradeChart"></canvas>
                </div>
                <div class="row text-center">
                    <div class="col-4">
                        <div class="p-2 rounded bg-success-subtle">
                            <div class="fs-4 fw-bold text-success"><?= $data['passCount'] ?? 0 ?></div>
                            <small class="text-muted">Đạt</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded bg-danger-subtle">
                            <div class="fs-4 fw-bold text-danger"><?= $data['failCount'] ?? 0 ?></div>
                            <small class="text-muted">Không đạt</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded bg-warning-subtle">
                            <div class="fs-4 fw-bold text-warning"><?= $data['pending'] ?? 0 ?></div>
                            <small class="text-muted">Chờ điểm</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Score Statistics -->
    <div class="col-xl-4 col-md-6 mb-4 mb-xl-0">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pb-0">
                <h5 class="card-title mb-0"><i class="fas fa-chart-line text-success me-2"></i>Chỉ số học tập</h5>
            </div>
            <div class="card-body">
                <div class="text-center py-4">
                    <div class="score-circle mb-3">
                        <svg viewBox="0 0 36 36" class="circular-chart">
                            <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            <path class="circle" stroke-dasharray="<?= $data['passRate'] ?? 0 ?>, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            <text x="18" y="20.35" class="percentage"><?= $data['passRate'] ?? 0 ?>%</text>
                        </svg>
                    </div>
                    <h6 class="text-muted mb-4">Tỷ lệ đạt</h6>
                </div>
                <div class="row border-top pt-3">
                    <div class="col-6 text-center border-end">
                        <div class="fs-3 fw-bold text-primary"><?= $data['avgScore'] ?? 0 ?></div>
                        <small class="text-muted">Điểm TB</small>
                    </div>
                    <div class="col-6 text-center">
                        <div class="fs-3 fw-bold text-info"><?= $data['totalDangKy'] ?? 0 ?></div>
                        <small class="text-muted">Tổng đăng ký</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Current Semester -->
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pb-0">
                <h5 class="card-title mb-0"><i class="fas fa-calendar-alt text-info me-2"></i>Học kỳ hiện tại</h5>
            </div>
            <div class="card-body">
                <div class="text-center py-3">
                    <div class="semester-badge mb-3"><i class="fas fa-calendar-check"></i></div>
                    <h4 class="text-primary mb-1"><?= $data['currentHocKy'] ?? 'Học kỳ 2' ?></h4>
                    <p class="text-muted mb-4"><?= $data['currentNamHoc'] ?? 'Năm học 2024-2025' ?></p>
                </div>
                <div class="row text-center border-top pt-3">
                    <div class="col-6 border-end">
                        <div class="fs-4 fw-bold text-success"><?= $data['activeLHP'] ?? 0 ?></div>
                        <small class="text-muted">Lớp HP mở</small>
                    </div>
                    <div class="col-6">
                        <div class="fs-4 fw-bold text-warning"><?= $data['pending'] ?? 0 ?></div>
                        <small class="text-muted">Chờ nhập điểm</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <h5 class="card-title mb-0"><i class="fas fa-bolt text-warning me-2"></i>Truy cập nhanh</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <a href="index.php?url=SinhVien" class="quick-action-btn">
                            <div class="quick-action-icon bg-primary"><i class="fas fa-user-graduate"></i></div>
                            <span>Sinh viên</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="index.php?url=GiangVien" class="quick-action-btn">
                            <div class="quick-action-icon bg-success"><i class="fas fa-chalkboard-teacher"></i></div>
                            <span>Giảng viên</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="index.php?url=Diem" class="quick-action-btn">
                            <div class="quick-action-icon bg-warning"><i class="fas fa-star"></i></div>
                            <span>Quản lý điểm</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="index.php?url=LopHocPhan" class="quick-action-btn">
                            <div class="quick-action-icon bg-info"><i class="fas fa-layer-group"></i></div>
                            <span>Lớp học phần</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="index.php?url=MonHoc" class="quick-action-btn">
                            <div class="quick-action-icon bg-secondary"><i class="fas fa-book"></i></div>
                            <span>Môn học</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="index.php?url=Khoa" class="quick-action-btn">
                            <div class="quick-action-icon bg-danger"><i class="fas fa-building"></i></div>
                            <span>Khoa</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="index.php?url=DangKyHoc" class="quick-action-btn">
                            <div class="quick-action-icon bg-dark"><i class="fas fa-file-signature"></i></div>
                            <span>Đăng ký HP</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="index.php?url=ThongKe" class="quick-action-btn">
                            <div class="quick-action-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);"><i class="fas fa-chart-bar"></i></div>
                            <span>Thống kê</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-icon-sm { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
.stat-change { font-size: 12px; margin-top: 8px; }
.score-circle { width: 150px; height: 150px; margin: 0 auto; }
.circular-chart { display: block; margin: 10px auto; max-width: 100%; max-height: 150px; }
.circle-bg { fill: none; stroke: #e9ecef; stroke-width: 3; }
.circle { fill: none; stroke: #28a745; stroke-width: 3; stroke-linecap: round; animation: progress 1s ease-out forwards; }
@keyframes progress { 0% { stroke-dasharray: 0 100; } }
.percentage { fill: #28a745; font-family: sans-serif; font-size: 0.5em; text-anchor: middle; font-weight: bold; }
.semester-badge { width: 80px; height: 80px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: white; font-size: 32px; box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3); }
.quick-action-btn { display: flex; flex-direction: column; align-items: center; padding: 20px 15px; border-radius: 12px; text-decoration: none; color: #475569; transition: all 0.3s ease; background: #f8fafc; border: 1px solid #e2e8f0; }
.quick-action-btn:hover { background: white; transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); color: #2563eb; }
.quick-action-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; margin-bottom: 10px; }
.quick-action-btn span { font-size: 13px; font-weight: 500; text-align: center; }
.activity-list { list-style: none; padding: 0; margin: 0; }
.activity-item { display: flex; align-items: center; padding: 15px 20px; border-bottom: 1px solid #f1f5f9; transition: background 0.2s; }
.activity-item:hover { background: #f8fafc; }
.activity-item:last-child { border-bottom: none; }
.activity-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px; flex-shrink: 0; }
.activity-content p { font-size: 14px; color: #374151; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('gradeChart');
if (ctx) {
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Đạt', 'Không đạt', 'Chờ điểm'],
            datasets: [{
                data: [<?= $data['passCount'] ?? 0 ?>, <?= $data['failCount'] ?? 0 ?>, <?= $data['pending'] ?? 0 ?>],
                backgroundColor: ['#28a745', '#dc3545', '#ffc107'],
                borderWidth: 0,
                cutout: '70%'
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>