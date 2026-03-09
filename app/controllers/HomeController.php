<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../config/Database.php';

class HomeController extends Controller {
    public function index() {
        // Load các model để lấy số liệu thống kê
        $svModel = $this->model("SinhVienModel");
        $gvModel = $this->model("GiangVienModel");
        $lhpModel = $this->model("LopHocPhanModel");
        $mhModel = $this->model("MonHocModel");
        $khoaModel = $this->model("KhoaModel");
        $nganhModel = $this->model("NganhModel");
        $hocKyModel = $this->model("HocKyModel");
        $dangKyModel = $this->model("DangKyHocModel");
        $diemModel = $this->model("ChiTietDiemModel");
        
        // Lấy dữ liệu từ model
        $sinhviens = $svModel->readAll();
        $giangviens = $gvModel->readAll();
        $lophocphans = $lhpModel->readAll();
        $monhocs = $mhModel->readAll();
        $khoas = $khoaModel->readAll();
        $nganhs = $nganhModel->readAll();
        $hockys = $hocKyModel->readAll();
        $dangkys = $dangKyModel->readAll();
        
        // Tính thống kê cơ bản
        $totalSV = count($sinhviens);
        $totalGV = count($giangviens);
        $totalLHP = count($lophocphans);
        $totalMH = count($monhocs);
        $totalKhoa = count($khoas);
        $totalNganh = count($nganhs);
        $totalDangKy = count($dangkys);
        
        // Tính học kỳ hiện tại
        $currentHocKy = 'Chưa xác định';
        $currentNamHoc = '';
        $today = date('Y-m-d');
        foreach ($hockys as $hk) {
            if (isset($hk['NgayBatDau'], $hk['NgayKetThuc'])) {
                if ($today >= $hk['NgayBatDau'] && $today <= $hk['NgayKetThuc']) {
                    $currentHocKy = $hk['TenHocKy'] ?? 'Học kỳ ' . ($hk['MaHocKy'] ?? '');
                    $currentNamHoc = $hk['NamHoc'] ?? '';
                    break;
                }
            }
        }
        
        // Tính thống kê điểm
        $passCount = 0;
        $failCount = 0;
        $pendingCount = 0;
        $totalScore = 0;
        $scoreCount = 0;
        
        // Lấy chi tiết điểm cho từng đăng ký học
        foreach ($dangkys as $dk) {
            $diems = $diemModel->getByDangKy($dk['MaDangKy'] ?? 0);
            
            if (empty($diems)) {
                $pendingCount++;
                continue;
            }
            
            // Tính điểm TB (CC*10% + GK*30% + CK*60%)
            $diemCC = null;
            $diemGK = null;
            $diemCK = null;
            
            foreach ($diems as $d) {
                $loai = strtoupper($d['MaLoaiDiem'] ?? '');
                if (strpos($loai, 'CC') !== false) $diemCC = $d['SoDiem'] ?? null;
                elseif (strpos($loai, 'GK') !== false) $diemGK = $d['SoDiem'] ?? null;
                elseif (strpos($loai, 'CK') !== false) $diemCK = $d['SoDiem'] ?? null;
            }
            
            if ($diemCC !== null && $diemGK !== null && $diemCK !== null) {
                $diemTB = $diemCC * 0.1 + $diemGK * 0.3 + $diemCK * 0.6;
                $totalScore += $diemTB;
                $scoreCount++;
                
                if ($diemTB >= 4.0) {
                    $passCount++;
                } else {
                    $failCount++;
                }
            } else {
                $pendingCount++;
            }
        }
        
        // Tính tỷ lệ đậu và điểm TB
        $totalGraded = $passCount + $failCount;
        $passRate = $totalGraded > 0 ? round(($passCount / $totalGraded) * 100, 1) : 0;
        $avgScore = $scoreCount > 0 ? round($totalScore / $scoreCount, 2) : 0;
        
        // Thống kê sinh viên theo khoa
        $svByKhoa = [];
        foreach ($khoas as $khoa) {
            $count = 0;
            foreach ($sinhviens as $sv) {
                // Đếm SV theo khoa (thông qua ngành hoặc lớp)
                // Đây là logic đơn giản, có thể mở rộng sau
                $count++;
            }
            $svByKhoa[] = [
                'name' => $khoa['TenKhoa'] ?? '',
                'count' => rand(10, 50) // Demo data
            ];
        }
        
        // Thống kê top môn học được đăng ký nhiều nhất
        $topMonHoc = [];
        foreach (array_slice($monhocs, 0, 5) as $mh) {
            $topMonHoc[] = [
                'name' => $mh['TenMonHoc'] ?? '',
                'count' => rand(20, 100) // Demo data
            ];
        }
        
        // Hoạt động gần đây (demo)
        $recentActivities = [
            ['description' => 'Sinh viên mới đăng ký học phần', 'time' => '5 phút trước', 'icon' => 'user-plus', 'color' => 'primary'],
            ['description' => 'Cập nhật điểm lớp Lập trình Web', 'time' => '15 phút trước', 'icon' => 'edit', 'color' => 'success'],
            ['description' => 'Mở lớp học phần mới', 'time' => '1 giờ trước', 'icon' => 'chalkboard', 'color' => 'info'],
            ['description' => 'Thêm giảng viên mới', 'time' => '2 giờ trước', 'icon' => 'user-tie', 'color' => 'warning'],
            ['description' => 'Cập nhật thông tin sinh viên', 'time' => '3 giờ trước', 'icon' => 'user-edit', 'color' => 'secondary'],
        ];

        // Truyền data sang view home/index.php
        $this->view("home/index", [
            // Thống kê chính
            'totalSV' => $totalSV,
            'totalGV' => $totalGV,
            'totalLHP' => $totalLHP,
            'totalMH' => $totalMH,
            'totalKhoa' => $totalKhoa,
            'totalNganh' => $totalNganh,
            'totalDangKy' => $totalDangKy,
            
            // Thống kê điểm
            'passRate' => $passRate,
            'avgScore' => $avgScore,
            'passCount' => $passCount,
            'failCount' => $failCount,
            'pending' => $pendingCount,
            'totalGraded' => $totalGraded,
            
            // Học kỳ hiện tại
            'currentHocKy' => $currentHocKy,
            'currentNamHoc' => $currentNamHoc,
            'activeLHP' => $totalLHP,
            
            // Biểu đồ data
            'svByKhoa' => $svByKhoa,
            'topMonHoc' => $topMonHoc,
            
            // Hoạt động gần đây
            'recentActivities' => $recentActivities,
            
            // Page info
            'pageTitle' => 'Dashboard',
            'breadcrumb' => 'Trang chủ'
        ]);
    }
    
    /**
     * Quản lý yêu cầu đổi mật khẩu
     */
    public function quenMatKhau() {
        require_once __DIR__ . '/../models/YeuCauDoiMatKhauModel.php';
        $db = new Database();
        $yeuCauModel = new YeuCauDoiMatKhauModel($db->getConnection());
        
        $yeuCaus = $yeuCauModel->readAll();
        
        $this->view("home/quenmatkhau", [
            'yeuCaus' => $yeuCaus,
            'pageTitle' => 'Quản lý yêu cầu đổi mật khẩu',
            'breadcrumb' => 'Yêu cầu đổi mật khẩu'
        ]);
    }
    
    /**
     * Duyệt yêu cầu đổi mật khẩu
     */
    public function duyetYeuCauMK() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=Home/quenMatKhau');
            exit;
        }
        
        require_once __DIR__ . '/../models/YeuCauDoiMatKhauModel.php';
        require_once __DIR__ . '/../models/UserModel.php';
        
        $db = new Database();
        $yeuCauModel = new YeuCauDoiMatKhauModel($db->getConnection());
        $userModel = new UserModel($db->getConnection());
        
        $id = (int)($_POST['id'] ?? 0);
        $trangThai = $_POST['trangThai'] ?? 'TuChoi';
        $ghiChu = $_POST['ghiChu'] ?? '';
        $nguoiXuLy = $_SESSION['user_id'] ?? 'admin';
        
        if ($id <= 0) {
            header('Location: index.php?url=Home/quenMatKhau&error=invalid');
            exit;
        }
        
        $yeuCau = $yeuCauModel->getById($id);
        if (!$yeuCau) {
            header('Location: index.php?url=Home/quenMatKhau&error=notfound');
            exit;
        }
        
        $matKhauMoi = null;
        
        // Nếu duyệt, tạo mật khẩu mới và cập nhật vào bảng USER
        if ($trangThai === 'DaDuyet') {
            // Tạo mật khẩu mới ngẫu nhiên (8 ký tự)
            $matKhauMoi = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
            $matKhauHashed = password_hash($matKhauMoi, PASSWORD_DEFAULT);
            
            // Cập nhật mật khẩu cho user
            $users = $userModel->readAll();
            foreach ($users as $user) {
                if ($user['TenDangNhap'] === $yeuCau['TenDangNhap']) {
                    $userModel->MaUser = $user['MaUser'];
                    $userModel->TenDangNhap = $user['TenDangNhap'];
                    $userModel->MatKhau = $matKhauHashed;
                    $userModel->HoTen = $user['HoTen'];
                    $userModel->VaiTro = $user['VaiTro'];
                    $userModel->Email = $user['Email'] ?? null;
                    $userModel->update();
                    break;
                }
            }
        }
        
        // Cập nhật trạng thái yêu cầu
        $yeuCauModel->updateStatus($id, $trangThai, $nguoiXuLy, $matKhauMoi, $ghiChu);
        
        header('Location: index.php?url=Home/quenMatKhau&success=1');
        exit;
    }
}
?>