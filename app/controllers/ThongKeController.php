<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/SinhVienModel.php';
require_once __DIR__ . '/../models/GiangVienModel.php';
require_once __DIR__ . '/../models/LopHocPhanModel.php';
require_once __DIR__ . '/../models/MonHocModel.php';
require_once __DIR__ . '/../models/DangKyHocModel.php';

class ThongKeController {
    private $db;
    private $svModel;
    private $gvModel;
    private $lhpModel;
    private $mhModel;
    private $dkhModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->svModel = new SinhVienModel($this->db);
        $this->gvModel = new GiangVienModel($this->db);
        $this->lhpModel = new LopHocPhanModel($this->db);
        $this->mhModel = new MonHocModel($this->db);
        $this->dkhModel = new DangKyHocModel($this->db);
    }

    public function index() {
        // Lấy số liệu thống kê
        $sinhviens = $this->svModel->readAll();
        $giangviens = $this->gvModel->readAll();
        $lophocphans = $this->lhpModel->readAll();
        $monhocs = $this->mhModel->readAll();
        $dangkyhocs = $this->dkhModel->readAll();
        
        // Tính toán thống kê
        $totalSV = count($sinhviens);
        $totalGV = count($giangviens);
        $totalLHP = count($lophocphans);
        $totalMH = count($monhocs);
        $totalDK = count($dangkyhocs);
        
        // Tính tỷ lệ đậu/rớt từ đăng ký học
        $passed = 0;
        $failed = 0;
        foreach ($dangkyhocs as $dk) {
            if (isset($dk['KetQua'])) {
                if ($dk['KetQua'] == 'Đạt') {
                    $passed++;
                } elseif ($dk['KetQua'] == 'Không đạt') {
                    $failed++;
                }
            }
        }
        
        $passRate = $totalDK > 0 ? round(($passed / $totalDK) * 100, 1) : 0;
        
        // Thống kê theo giới tính sinh viên
        $maleCount = 0;
        $femaleCount = 0;
        foreach ($sinhviens as $sv) {
            if (isset($sv['GioiTinh'])) {
                if ($sv['GioiTinh'] == 'Nam') {
                    $maleCount++;
                } else {
                    $femaleCount++;
                }
            }
        }
        
        $data = [
            'totalSV' => $totalSV,
            'totalGV' => $totalGV,
            'totalLHP' => $totalLHP,
            'totalMH' => $totalMH,
            'totalDK' => $totalDK,
            'passed' => $passed,
            'failed' => $failed,
            'passRate' => $passRate,
            'maleCount' => $maleCount,
            'femaleCount' => $femaleCount,
            'sinhviens' => $sinhviens,
            'giangviens' => $giangviens,
            'pageTitle' => 'Thống kê',
            'breadcrumb' => 'Thống kê'
        ];
        
        require_once "../app/views/admin/thongke/index.php";
    }

    /**
     * Xuất báo cáo thống kê ra Excel
     */
    public function exportExcel() {
        $sinhviens = $this->svModel->readAll();
        $giangviens = $this->gvModel->readAll();
        $lophocphans = $this->lhpModel->readAll();
        $monhocs = $this->mhModel->readAll();
        $dangkyhocs = $this->dkhModel->readAll();
        
        $totalSV = count($sinhviens);
        $totalGV = count($giangviens);
        $totalLHP = count($lophocphans);
        $totalMH = count($monhocs);
        $totalDK = count($dangkyhocs);
        
        $passed = 0;
        $failed = 0;
        foreach ($dangkyhocs as $dk) {
            if (isset($dk['KetQua'])) {
                if ($dk['KetQua'] == 'Đạt' || $dk['KetQua'] == 1) {
                    $passed++;
                } elseif ($dk['KetQua'] == 'Không đạt' || $dk['KetQua'] == 0) {
                    $failed++;
                }
            }
        }
        
        $maleCount = 0;
        $femaleCount = 0;
        foreach ($sinhviens as $sv) {
            if (isset($sv['GioiTinh'])) {
                if ($sv['GioiTinh'] == 'Nam') {
                    $maleCount++;
                } else {
                    $femaleCount++;
                }
            }
        }
        
        $filename = 'BaoCaoThongKe_' . date('Ymd_His') . '.csv';
        
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo "\xEF\xBB\xBF"; // BOM for UTF-8
        
        $output = fopen('php://output', 'w');
        
        // Tiêu đề báo cáo
        fputcsv($output, ['BÁO CÁO THỐNG KÊ HỆ THỐNG QUẢN LÝ ĐIỂM']);
        fputcsv($output, ['Ngày xuất: ' . date('d/m/Y H:i:s')]);
        fputcsv($output, []);
        
        // Thống kê tổng quan
        fputcsv($output, ['THỐNG KÊ TỔNG QUAN']);
        fputcsv($output, ['Chỉ tiêu', 'Số lượng']);
        fputcsv($output, ['Tổng số sinh viên', $totalSV]);
        fputcsv($output, ['Tổng số giảng viên', $totalGV]);
        fputcsv($output, ['Tổng số môn học', $totalMH]);
        fputcsv($output, ['Tổng số lớp học phần', $totalLHP]);
        fputcsv($output, ['Tổng số đăng ký học', $totalDK]);
        fputcsv($output, []);
        
        // Thống kê kết quả học tập
        fputcsv($output, ['THỐNG KÊ KẾT QUẢ HỌC TẬP']);
        fputcsv($output, ['Kết quả', 'Số lượng', 'Tỷ lệ (%)']);
        $passRate = $totalDK > 0 ? round(($passed / $totalDK) * 100, 1) : 0;
        $failRate = $totalDK > 0 ? round(($failed / $totalDK) * 100, 1) : 0;
        fputcsv($output, ['Đậu', $passed, $passRate . '%']);
        fputcsv($output, ['Rớt', $failed, $failRate . '%']);
        fputcsv($output, ['Chưa có kết quả', $totalDK - $passed - $failed, round(100 - $passRate - $failRate, 1) . '%']);
        fputcsv($output, []);
        
        // Thống kê giới tính
        fputcsv($output, ['THỐNG KÊ GIỚI TÍNH SINH VIÊN']);
        fputcsv($output, ['Giới tính', 'Số lượng', 'Tỷ lệ (%)']);
        $totalGender = $maleCount + $femaleCount;
        $malePercent = $totalGender > 0 ? round(($maleCount / $totalGender) * 100, 1) : 0;
        fputcsv($output, ['Nam', $maleCount, $malePercent . '%']);
        fputcsv($output, ['Nữ', $femaleCount, round(100 - $malePercent, 1) . '%']);
        fputcsv($output, []);
        
        // Danh sách sinh viên
        fputcsv($output, ['DANH SÁCH SINH VIÊN']);
        fputcsv($output, ['STT', 'MSSV', 'Họ tên', 'Ngày sinh', 'Giới tính', 'Email', 'SĐT', 'Lớp', 'Trạng thái']);
        $stt = 1;
        foreach ($sinhviens as $sv) {
            fputcsv($output, [
                $stt++,
                $sv['MaSinhVien'] ?? '',
                $sv['HoTen'] ?? '',
                isset($sv['NgaySinh']) ? date('d/m/Y', strtotime($sv['NgaySinh'])) : '',
                $sv['GioiTinh'] ?? '',
                $sv['Email'] ?? '',
                $sv['SoDienThoai'] ?? '',
                $sv['MaLop'] ?? '',
                $sv['TrangThaiHocTap'] ?? ''
            ]);
        }
        
        fclose($output);
        exit;
    }
}
?>
