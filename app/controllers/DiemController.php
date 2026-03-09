<?php
/**
 * DiemController - Quản lý Điểm
 */
require_once __DIR__ . '/../core/Controller.php';

class DiemController extends Controller {
    private $diemModel;
    private $dangKyModel;
    private $lopHPModel;
    private $hocKyModel;

    public function __construct() {
        parent::__construct();
        $this->diemModel = $this->model('ChiTietDiemModel');
        $this->dangKyModel = $this->model('DangKyHocModel');
        $this->lopHPModel = $this->model('LopHocPhanModel');
        $this->hocKyModel = $this->model('HocKyModel');
    }

    public function index() {
        $lophocphans = $this->lopHPModel->readAllWithDetails();
        $hockys = $this->hocKyModel->readAll();
        
        $bangdiem = [];
        $filterLop = isset($_GET['lop']) ? $this->sanitize($_GET['lop']) : (isset($_GET['maLopHocPhan']) ? $this->sanitize($_GET['maLopHocPhan']) : null);
        
        if ($filterLop) {
            $bangdiem = $this->getBangDiemByLop($filterLop);
            foreach ($bangdiem as &$row) {
                $diemTB = $this->tinhDiemTB($row);
                $row['DiemTongKet'] = $diemTB !== null ? number_format($diemTB, 2) : null;
                $row['DiemChu'] = $this->diemSoSangChu($diemTB);
                $row['KetQua'] = $diemTB !== null ? ($diemTB >= 4.0 ? 'Đạt' : 'Không đạt') : null;
            }
            unset($row);
        }
        
        // Tính thống kê
        $passed = 0;
        $failed = 0;
        $pending = 0;
        
        foreach ($bangdiem as $sv) {
            $diemTB = $this->tinhDiemTB($sv);
            if ($diemTB === null) {
                $pending++;
            } elseif ($diemTB >= 4.0) {
                $passed++;
            } else {
                $failed++;
            }
        }
        
        $data = [
            'bangdiem' => $bangdiem,
            'lophocphans' => $lophocphans,
            'hockys' => $hockys,
            'filterLop' => $filterLop,
            'totalSV' => count($bangdiem),
            'passed' => $passed,
            'failed' => $failed,
            'pending' => $pending,
            'trangThaiDiem' => $filterLop ? $this->dangKyModel->getTrangThaiDiemByLop($filterLop) : 0,
            'pageTitle' => 'Quản lý Điểm',
            'breadcrumb' => 'Điểm',
            'error' => '',
            'success' => ''
        ];
        
        require_once __DIR__ . '/../views/admin/diem/index.php';
    }
    
    /**
     * Tính điểm trung bình theo công thức chuẩn
     */
    private function tinhDiemTB($sv) {
        $diemCC = isset($sv['DiemCC']) ? (float) $sv['DiemCC'] : null;
        $diemGK = isset($sv['DiemGK']) ? (float) $sv['DiemGK'] : null;
        $diemCK = isset($sv['DiemCK']) ? (float) $sv['DiemCK'] : null;
        
        if ($diemCC === null && $diemGK === null && $diemCK === null) {
            return null;
        }
        
        // Công thức: CC*10% + GK*30% + CK*60%
        $cc = $diemCC ?? 0;
        $gk = $diemGK ?? 0;
        $ck = $diemCK ?? 0;
        
        return round($cc * 0.1 + $gk * 0.3 + $ck * 0.6, 2);
    }
    
    private function diemSoSangChu($diemSo) {
        if ($diemSo === null) return '';
        if ($diemSo >= 9.0) return 'A+';
        if ($diemSo >= 8.5) return 'A';
        if ($diemSo >= 8.0) return 'B+';
        if ($diemSo >= 7.0) return 'B';
        if ($diemSo >= 6.5) return 'C+';
        if ($diemSo >= 5.5) return 'C';
        if ($diemSo >= 5.0) return 'D+';
        if ($diemSo >= 4.0) return 'D';
        return 'F';
    }
    
    /**
     * Lấy bảng điểm theo lớp học phần
     */
    private function getBangDiemByLop($maLop) {
        $db = $this->getDb();
        
        // Kiểm tra xem cột TrangThaiDiem có tồn tại không
        $hasTrangThaiDiem = $this->columnExists('DANG_KY_HOC', 'TrangThaiDiem');
        
        $trangThaiSelect = $hasTrangThaiDiem ? 'dk.TrangThaiDiem,' : '0 as TrangThaiDiem,';
        
        $query = "SELECT dk.MaDangKy as ID, sv.MaSinhVien as MSSV, sv.HoTen, 
                         lhp.MaLopHocPhan, {$trangThaiSelect}
                         MAX(CASE WHEN ld.TenLoaiDiem = 'Chuyên cần' OR ld.TenLoaiDiem = 'Quá trình' THEN ctd.SoDiem END) as DiemCC,
                         MAX(CASE WHEN ld.TenLoaiDiem = 'Giữa kỳ' THEN ctd.SoDiem END) as DiemGK,
                         MAX(CASE WHEN ld.TenLoaiDiem = 'Cuối kỳ' THEN ctd.SoDiem END) as DiemCK
                  FROM DANG_KY_HOC dk
                  JOIN SINH_VIEN sv ON dk.MaSinhVien = sv.MaSinhVien
                  JOIN LOP_HOC_PHAN lhp ON dk.MaLopHocPhan = lhp.MaLopHocPhan
                  LEFT JOIN CHI_TIET_DIEM ctd ON dk.MaDangKy = ctd.MaDangKy
                  LEFT JOIN LOAI_DIEM ld ON ctd.MaLoaiDiem = ld.MaLoaiDiem
                  WHERE dk.MaLopHocPhan = :maLop
                  GROUP BY dk.MaDangKy, sv.MaSinhVien, sv.HoTen, lhp.MaLopHocPhan
                  ORDER BY sv.MaSinhVien";
        
        try {
            $stmt = $db->prepare($query);
            $stmt->bindParam(':maLop', $maLop);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getBangDiemByLop: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Kiểm tra xem cột có tồn tại trong bảng không
     */
    private function columnExists($table, $column) {
        try {
            $db = $this->getDb();
            $query = "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS 
                      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':table', $table);
            $stmt->bindParam(':column', $column);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return isset($result['count']) && $result['count'] > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Cập nhật điểm hàng loạt
     */
    public function updateAll() {
        if (!$this->isPost()) {
            $this->redirect('Diem/index');
        }
        
        $diemData = $_POST['diem'] ?? [];
        $filterLop = $this->getPost('MaLopHocPhan');
        $errors = [];
        
        // Lấy trạng thái điểm hiện tại
        $currentTrangThai = $filterLop ? $this->dangKyModel->getTrangThaiDiemByLop($filterLop) : 0;
        
        // Nếu điểm đã khóa hoặc phê duyệt thì KHÔNG AI được sửa (kể cả admin)
        // Admin chỉ có thể mở khóa hoặc phê duyệt
        if ($currentTrangThai >= 1) {
            $this->redirect('Diem/index?lop=' . urlencode($filterLop) . '&error=locked');
            return;
        }
        
        foreach ($diemData as $maDangKy => $diem) {
            // Validate điểm
            foreach (['DiemCC', 'DiemGK', 'DiemCK'] as $loai) {
                if (isset($diem[$loai]) && $diem[$loai] !== '') {
                    $val = (float) $diem[$loai];
                    if ($val < 0 || $val > 10) {
                        $errors[] = "Điểm phải từ 0 đến 10.";
                        break 2;
                    }
                }
            }
        }
        
        if (!empty($errors)) {
            $data = [
                'bangdiem' => $filterLop ? $this->getBangDiemByLop($filterLop) : [],
                'lophocphans' => $this->lopHPModel->readAll(),
                'hockys' => $this->hocKyModel->readAll(),
                'filterLop' => $filterLop,
                'totalSV' => 0,
                'passed' => 0,
                'failed' => 0,
                'pending' => 0,
                'trangThaiDiem' => $currentTrangThai,
                'pageTitle' => 'Quản lý Điểm',
                'breadcrumb' => 'Điểm',
                'error' => implode(' ', $errors),
                'success' => ''
            ];
            require_once __DIR__ . '/../views/admin/diem/index.php';
            return;
        }
        
        // Xử lý cập nhật điểm
        foreach ($diemData as $maDangKy => $diem) {
            $this->updateDiemForDangKy($maDangKy, $diem);
        }
        
        $this->redirect('Diem/index' . ($filterLop ? '?lop=' . urlencode($filterLop) . '&success=save' : ''));
    }
    
    /**
     * Cập nhật điểm cho một đăng ký học
     */
    private function updateDiemForDangKy($maDangKy, $diem) {
        try {
            $db = $this->getDb();
            
            // Lấy MaLoaiDiem cho từng loại điểm
            $loaiDiem = [
                'qt' => $this->getMaLoaiDiemByTen('Chuyên cần'),
                'gk' => $this->getMaLoaiDiemByTen('Giữa kỳ'),
                'ck' => $this->getMaLoaiDiemByTen('Cuối kỳ')
            ];
            
            // Cập nhật từng loại điểm
            foreach (['qt', 'gk', 'ck'] as $loai) {
                $maLoai = $loaiDiem[$loai] ?? null;
                if ($maLoai && isset($diem[$loai]) && $diem[$loai] !== '') {
                    $soDiem = (float) $diem[$loai];
                    
                    // Kiểm tra xem đã có điểm này chưa
                    $checkQuery = "SELECT MaChiTiet FROM CHI_TIET_DIEM WHERE MaDangKy = :maDangKy AND MaLoaiDiem = :maLoai LIMIT 1";
                    $checkStmt = $db->prepare($checkQuery);
                    $checkStmt->bindParam(':maDangKy', $maDangKy);
                    $checkStmt->bindParam(':maLoai', $maLoai);
                    $checkStmt->execute();
                    $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($existing) {
                        // Cập nhật điểm hiện có
                        $updateQuery = "UPDATE CHI_TIET_DIEM SET SoDiem = :soDiem WHERE MaDangKy = :maDangKy AND MaLoaiDiem = :maLoai";
                        $stmt = $db->prepare($updateQuery);
                        $stmt->bindParam(':soDiem', $soDiem);
                        $stmt->bindParam(':maDangKy', $maDangKy);
                        $stmt->bindParam(':maLoai', $maLoai);
                        $stmt->execute();
                    } else {
                        // Thêm mới điểm
                        $insertQuery = "INSERT INTO CHI_TIET_DIEM (MaDangKy, MaLoaiDiem, SoDiem, NgayNhap) VALUES (:maDangKy, :maLoai, :soDiem, NOW())";
                        $stmt = $db->prepare($insertQuery);
                        $stmt->bindParam(':maDangKy', $maDangKy);
                        $stmt->bindParam(':maLoai', $maLoai);
                        $stmt->bindParam(':soDiem', $soDiem);
                        $stmt->execute();
                    }
                }
            }
            
            // Cập nhật điểm tổng kết vào DANG_KY_HOC
            $this->updateDiemTongKet($maDangKy);
            
        } catch (PDOException $e) {
            error_log("Error updating diem: " . $e->getMessage());
        }
    }
    
    /**
     * Lấy mã loại điểm theo tên
     */
    private function getMaLoaiDiemByTen($tenLoai) {
        try {
            $db = $this->getDb();
            $query = "SELECT MaLoaiDiem FROM LOAI_DIEM WHERE TenLoaiDiem = :tenLoai LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':tenLoai', $tenLoai);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['MaLoaiDiem'] : null;
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Cập nhật điểm tổng kết vào bảng DANG_KY_HOC
     */
    private function updateDiemTongKet($maDangKy) {
        try {
            $db = $this->getDb();
            
            // Lấy điểm chi tiết
            $query = "SELECT ctd.SoDiem, ld.TenLoaiDiem 
                      FROM CHI_TIET_DIEM ctd 
                      JOIN LOAI_DIEM ld ON ctd.MaLoaiDiem = ld.MaLoaiDiem 
                      WHERE ctd.MaDangKy = :maDangKy";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':maDangKy', $maDangKy);
            $stmt->execute();
            $diems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $diemCC = null;
            $diemGK = null;
            $diemCK = null;
            
            foreach ($diems as $d) {
                $tenLoai = $d['TenLoaiDiem'] ?? '';
                if ($tenLoai === 'Chuyên cần' || $tenLoai === 'Quá trình') {
                    $diemCC = (float) $d['SoDiem'];
                } elseif ($tenLoai === 'Giữa kỳ') {
                    $diemGK = (float) $d['SoDiem'];
                } elseif ($tenLoai === 'Cuối kỳ') {
                    $diemCK = (float) $d['SoDiem'];
                }
            }
            
            // Tính điểm TB
            $diemTB = null;
            if ($diemCC !== null || $diemGK !== null || $diemCK !== null) {
                $cc = $diemCC ?? 0;
                $gk = $diemGK ?? 0;
                $ck = $diemCK ?? 0;
                $diemTB = round($cc * 0.1 + $gk * 0.3 + $ck * 0.6, 2);
            }
            
            // Chuyển điểm số sang điểm chữ
            $diemChu = $this->diemSoSangChu($diemTB);
            
            // Xác định kết quả
            $ketQua = null;
            if ($diemTB !== null) {
                $ketQua = $diemTB >= 4.0 ? 'Đạt' : 'Không đạt';
            }
            
            // Cập nhật vào DANG_KY_HOC
            $updateQuery = "UPDATE DANG_KY_HOC SET DiemTongKet = :diemTB, DiemChu = :diemChu, DiemSo = :diemSo, KetQua = :ketQua WHERE MaDangKy = :maDangKy";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(':diemTB', $diemTB);
            $updateStmt->bindParam(':diemChu', $diemChu);
            $updateStmt->bindParam(':diemSo', $diemTB);
            $updateStmt->bindParam(':ketQua', $ketQua);
            $updateStmt->bindParam(':maDangKy', $maDangKy);
            $updateStmt->execute();
            
        } catch (PDOException $e) {
            error_log("Error updating diem tong ket: " . $e->getMessage());
        }
    }
    
    /**
     * Phê duyệt điểm
     */
    public function approve() {
        if (!$this->isPost()) {
            $this->redirect('Diem/index');
        }
        
        $maLop = $this->getPost('MaLopHocPhan');
        
        if (empty($maLop)) {
            $data = [
                'bangdiem' => [],
                'lophocphans' => $this->lopHPModel->readAllWithDetails(),
                'hockys' => $this->hocKyModel->readAll(),
                'filterLop' => null,
                'totalSV' => 0,
                'passed' => 0,
                'failed' => 0,
                'pending' => 0,
                'pageTitle' => 'Quản lý Điểm',
                'breadcrumb' => 'Điểm',
                'error' => 'Vui lòng chọn lớp học phần.',
                'success' => ''
            ];
            require_once __DIR__ . '/../views/admin/diem/index.php';
            return;
        }
        
        // Lấy thông tin người dùng hiện tại
        $nguoiDung = $_SESSION['user'] ?? null;
        $nguoiPheDuyet = $nguoiDung['TenDangNhap'] ?? 'admin';
        
        // Phê duyệt điểm
        $result = $this->dangKyModel->pheDuyetDiem($maLop, $nguoiPheDuyet);
        
        if ($result) {
            $this->redirect('Diem/index?lop=' . urlencode($maLop) . '&success=2');
        } else {
            $this->redirect('Diem/index?lop=' . urlencode($maLop) . '&error=2');
        }
    }

    /**
     * Khóa điểm
     */
    public function lock() {
        if (!$this->isPost()) {
            $this->redirect('Diem/index');
        }
        
        $maLop = $this->getPost('MaLopHocPhan');
        
        if (empty($maLop)) {
            $this->redirect('Diem/index');
            return;
        }
        
        // Lấy thông tin người dùng hiện tại
        $nguoiDung = $_SESSION['user'] ?? null;
        $nguoiKhoa = $nguoiDung['TenDangNhap'] ?? 'admin';
        
        // Khóa điểm
        $result = $this->dangKyModel->khoaDiem($maLop, $nguoiKhoa);
        
        if ($result) {
            $this->redirect('Diem/index?lop=' . urlencode($maLop) . '&success=1');
        } else {
            $this->redirect('Diem/index?lop=' . urlencode($maLop) . '&error=1');
        }
    }

    /**
     * Mở khóa điểm (Admin only)
     */
    public function unlock() {
        if (!$this->isPost()) {
            $this->redirect('Diem/index');
        }
        
        $maLop = $this->getPost('MaLopHocPhan');
        
        if (empty($maLop)) {
            $this->redirect('Diem/index');
            return;
        }
        
        // Chỉ admin mới có quyền mở khóa
        $isAdmin = isset($_SESSION['user_role']) && (strtolower($_SESSION['user_role']) === 'admin' || $_SESSION['user_role'] === 'Admin');
        if (!$isAdmin) {
            $this->redirect('Diem/index?error=unauthorized');
            return;
        }
        
        // Mở khóa điểm
        $result = $this->dangKyModel->moKhoaDiem($maLop);
        
        if ($result) {
            $this->redirect('Diem/index?lop=' . urlencode($maLop) . '&success=3');
        } else {
            $this->redirect('Diem/index?lop=' . urlencode($maLop) . '&error=3');
        }
    }

    /**
     * Hủy phê duyệt điểm (Admin only)
     */
    public function unapprove() {
        if (!$this->isPost()) {
            $this->redirect('Diem/index');
        }
        
        $maLop = $this->getPost('MaLopHocPhan');
        
        if (empty($maLop)) {
            $this->redirect('Diem/index');
            return;
        }
        
        // Chỉ admin mới có quyền hủy phê duyệt
        $isAdmin = isset($_SESSION['user_role']) && (strtolower($_SESSION['user_role']) === 'admin' || $_SESSION['user_role'] === 'Admin');
        if (!$isAdmin) {
            $this->redirect('Diem/index?error=unauthorized');
            return;
        }
        
        // Hủy phê duyệt điểm
        $result = $this->dangKyModel->huyPheDuyetDiem($maLop);
        
        if ($result) {
            $this->redirect('Diem/index?lop=' . urlencode($maLop) . '&success=4');
        } else {
            $this->redirect('Diem/index?lop=' . urlencode($maLop) . '&error=4');
        }
    }

    /**
     * API: Kiểm tra trạng thái điểm
     */
    public function getTrangThaiDiem() {
        header('Content-Type: application/json');
        
        $maDangKy = isset($_GET['maDangKy']) ? (int)$_GET['maDangKy'] : 0;
        
        if (!$maDangKy) {
            echo json_encode(['success' => false, 'message' => 'Thiếu mã đăng ký']);
            exit;
        }
        
        $trangThai = $this->dangKyModel->getTrangThaiDiem($maDangKy);
        
        echo json_encode([
            'success' => true,
            'trangThai' => $trangThai,
            'message' => $this->getTrangThaiDiemMessage($trangThai)
        ]);
        exit;
    }

    private function getTrangThaiDiemMessage($trangThai) {
        switch ($trangThai) {
            case 0: return 'Mới lưu';
            case 1: return 'Đã khóa';
            case 2: return 'Đã phê duyệt';
            default: return 'Không xác định';
        }
    }

    /**
     * Xuất bảng điểm sinh viên ra Excel (CSV)
     */
    public function exportExcel() {
        $filterLop = isset($_GET['lop']) ? $this->sanitize($_GET['lop']) : null;
        if (empty($filterLop)) {
            header('Location: index.php?url=Diem/index');
            exit;
        }
        $bangdiem = $this->getBangDiemByLop($filterLop);
        foreach ($bangdiem as &$row) {
            $diemTB = $this->tinhDiemTB($row);
            $row['DiemTongKet'] = $diemTB !== null ? round($diemTB, 2) : null;
            $row['DiemChu'] = $this->diemSoSangChu($diemTB);
            $row['KetQua'] = $diemTB !== null ? ($diemTB >= 4.0 ? 'Đạt' : 'Không đạt') : null;
        }
        unset($row);

        $filename = 'BangDiem_' . $filterLop . '_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        echo "\xEF\xBB\xBF";

        $output = fopen('php://output', 'w');
        fputcsv($output, ['STT', 'MSSV', 'Họ tên', 'Lớp HP', 'Điểm QT', 'Điểm GK', 'Điểm CK', 'Điểm TB', 'Điểm chữ', 'Kết quả']);
        $stt = 1;
        foreach ($bangdiem as $r) {
            fputcsv($output, [
                $stt++,
                $r['MSSV'] ?? '',
                $r['HoTen'] ?? '',
                $r['MaLopHocPhan'] ?? '',
                $r['DiemCC'] ?? '',
                $r['DiemGK'] ?? '',
                $r['DiemCK'] ?? '',
                $r['DiemTongKet'] ?? '',
                $r['DiemChu'] ?? '',
                $r['KetQua'] ?? ''
            ]);
        }
        fclose($output);
        exit;
    }
}
