<?php
/**
 * GiangVienController - Quản lý Giảng viên
 */
require_once __DIR__ . '/../core/Controller.php';

class GiangVienController extends Controller {
    private $gvModel;
    private $khoaModel;

    public function __construct() {
        parent::__construct();
        $this->gvModel = $this->model('GiangVienModel');
        $this->khoaModel = $this->model('KhoaModel');
    }

    /**
     * Dashboard cổng giảng viên (sau khi đăng nhập vai trò Giảng viên)
     * Hiển thị view app/views/giangvien/dashboard.php - trang tổng quan
     */
    public function dashboard() {
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            $baseUrl = defined('URLROOT') ? rtrim(URLROOT, '/') : '';
            header('Location: ' . $baseUrl . '/Auth/index');
            exit;
        }
        $userModel = $this->model('UserModel');
        $user = $userModel->getById($_SESSION['user_id']);
        if (!$user) {
            session_destroy();
            header('Location: ' . (defined('URLROOT') ? rtrim(URLROOT, '/') : '') . '/Auth/index');
            exit;
        }
        $tenDangNhap = trim($user['TenDangNhap'] ?? '');
        $giangVien = $this->gvModel->getById($tenDangNhap);
        if (!$giangVien) {
            $allGV = $this->gvModel->readAll();
            foreach ($allGV as $g) {
                if (strcasecmp($g['MaGiangVien'] ?? '', $tenDangNhap) === 0) {
                    $giangVien = $g;
                    break;
                }
            }
        }
        if (!$giangVien) {
            $giangVien = ['MaGiangVien' => $tenDangNhap, 'HoTen' => $user['HoTen'] ?? 'Giảng viên'];
        }

        $maGV = $giangVien['MaGiangVien'] ?? $tenDangNhap;
        $lhpModel = $this->model('LopHocPhanModel');
        $dangKyModel = $this->model('DangKyHocModel');
        $diemModel = $this->model('ChiTietDiemModel');
        $ddModel = $this->model('DiemDanhModel');

        $lopHocPhanList = $lhpModel->getByMaGiangVien($maGV);

        // Calculate statistics for dashboard
        $tongLop = count($lopHocPhanList);
        $tongSinhVien = 0;
        $tongMon = [];
        $diemDaNhap = 0;
        $buoiDaDiemDanh = 0;
        $tongDiem = 0;
        $soLopCoDiem = 0;

        foreach ($lopHocPhanList as $lop) {
            $maLopHP = $lop['MaLopHocPhan'] ?? '';
            $siSo = (int)($lop['SiSo'] ?? 0);
            $tongSinhVien += $siSo;

            // Count unique subjects
            $maMon = $lop['MaMonHoc'] ?? '';
            if ($maMon && !in_array($maMon, $tongMon)) {
                $tongMon[] = $maMon;
            }

            // Count grades entered
            if ($maLopHP) {
                $dangKys = $dangKyModel->getByLopHocPhan($maLopHP);
                foreach ($dangKys as $dk) {
                    $diems = $diemModel->getByDangKy($dk['MaDangKy'] ?? 0);
                    if (!empty($diems)) {
                        $diemDaNhap++;
                        // Calculate average
                        $diemTB = $this->tinhDiemTrungBinh($diems);
                        if ($diemTB !== null) {
                            $tongDiem += $diemTB;
                            $soLopCoDiem++;
                        }
                    }
                }

                // Count attendance sessions
                $bangDiemDanh = $ddModel->getBangDiemDanhByLop($maLopHP);
                $buoiDaDiemDanh = max($buoiDaDiemDanh, count($bangDiemDanh) > 0 ? count($bangDiemDanh[0]['Buoi'] ?? []) : 0);
            }
        }

        $thongKe = [
            'tongLop' => $tongLop,
            'tongSinhVien' => $tongSinhVien,
            'tongMon' => count($tongMon),
            'diemDaNhap' => $diemDaNhap,
            'buoiDaDiemDanh' => $buoiDaDiemDanh,
            'diemTrungBinh' => $soLopCoDiem > 0 ? $tongDiem / $soLopCoDiem : 0
        ];

        require_once __DIR__ . '/../views/giangvien/dashboard.php';
    }

    /**
     * Danh sách lớp học phần - trang Lớp & môn được dạy
     */
    public function indexLopHocPhan() {
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            $baseUrl = defined('URLROOT') ? rtrim(URLROOT, '/') : '';
            header('Location: ' . $baseUrl . '/Auth/index');
            exit;
        }
        $userModel = $this->model('UserModel');
        $user = $userModel->getById($_SESSION['user_id']);
        if (!$user) {
            session_destroy();
            header('Location: ' . (defined('URLROOT') ? rtrim(URLROOT, '/') : '') . '/Auth/index');
            exit;
        }
        $tenDangNhap = trim($user['TenDangNhap'] ?? '');
        $giangVien = $this->gvModel->getById($tenDangNhap);
        if (!$giangVien) {
            $allGV = $this->gvModel->readAll();
            foreach ($allGV as $g) {
                if (strcasecmp($g['MaGiangVien'] ?? '', $tenDangNhap) === 0) {
                    $giangVien = $g;
                    break;
                }
            }
        }
        if (!$giangVien) {
            $giangVien = ['MaGiangVien' => $tenDangNhap, 'HoTen' => $user['HoTen'] ?? 'Giảng viên'];
        }
        $hocKyModel = $this->model('HocKyModel');
        $lhpModel = $this->model('LopHocPhanModel');
        $maGV = $giangVien['MaGiangVien'] ?? $tenDangNhap;
        $hocKys = $hocKyModel->readAll();
        $hocKyList = [];
        foreach ($hocKys as $hk) {
            $hocKyList[] = [
                'value' => $hk['MaHocKy'] ?? '',
                'label' => $hk['TenHocKy'] ?? '',
            ];
        }
        $lopHocPhanList = $lhpModel->getByMaGiangVien($maGV);
        $sinhVienLopHocPhan = [];
        require_once __DIR__ . '/../views/giangvien/dashboardgiangvien.php';
    }

    /**
     * Lấy thông tin giảng viên đang đăng nhập (cổng GV). Redirect nếu chưa đăng nhập.
     * @return array [giangVien, maGV, lopHocPhanList]
     */
    private function getGiangVienPortalData() {
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            $baseUrl = defined('URLROOT') ? rtrim(URLROOT, '/') : '';
            header('Location: ' . $baseUrl . '/Auth/index');
            exit;
        }
        $userModel = $this->model('UserModel');
        $user = $userModel->getById($_SESSION['user_id']);
        if (!$user) {
            session_destroy();
            header('Location: ' . (defined('URLROOT') ? rtrim(URLROOT, '/') : '') . '/Auth/index');
            exit;
        }
        $tenDangNhap = trim($user['TenDangNhap'] ?? '');
        $giangVien = $this->gvModel->getById($tenDangNhap);
        if (!$giangVien) {
            $allGV = $this->gvModel->readAll();
            foreach ($allGV as $g) {
                if (strcasecmp($g['MaGiangVien'] ?? '', $tenDangNhap) === 0) {
                    $giangVien = $g;
                    break;
                }
            }
        }
        if (!$giangVien) {
            $giangVien = ['MaGiangVien' => $tenDangNhap, 'HoTen' => $user['HoTen'] ?? 'Giảng viên'];
        }
        $maGV = $giangVien['MaGiangVien'] ?? $tenDangNhap;
        $lhpModel = $this->model('LopHocPhanModel');
        $lopHocPhanList = $lhpModel->getByMaGiangVien($maGV);
        return [$giangVien, $maGV, $lopHocPhanList];
    }

    /**
     * Nhập điểm - dùng view app/views/giangvien/nhapdiem.php
     */
    public function nhapDiem() {
        list($giangVien, $maGV, $lopHocPhanList) = $this->getGiangVienPortalData();
        $maLopHocPhan = $_GET['maLopHocPhan'] ?? null;
        $lopHocPhanSelected = null;
        $cauTrucDiem = [];
        if ($maLopHocPhan) {
            $lhpModel = $this->model('LopHocPhanModel');
            $lopHocPhanSelected = $lhpModel->getById($maLopHocPhan);
                if ($lopHocPhanSelected && ($lopHocPhanSelected['MaGiangVien'] ?? '') === $maGV) {
                $maMonHoc = $lopHocPhanSelected['MaMonHoc'] ?? '';
                if ($maMonHoc) {
                    $cauTrucModel = $this->model('CauTrucDiemModel');
                    $cauTrucDiem = $cauTrucModel->getByMaMonHoc($maMonHoc);
                    // Loại bỏ TH (Thực hành) và TX (Thường xuyên) khỏi giao diện nhập điểm
                    $cauTrucDiem = array_values(array_filter($cauTrucDiem, function($ct) {
                        $m = strtoupper($ct['MaLoaiDiem'] ?? '');
                        return $m !== 'TH' && $m !== 'TX';
                    }));
                }
            } else {
                $lopHocPhanSelected = null;
            }
        }
        require_once __DIR__ . '/../views/giangvien/nhapdiem.php';
    }

    /**
     * Tra cứu điểm - dùng view app/views/giangvien/tracuudiem.php
     */
    public function traCuuDiem() {
        list($giangVien, $maGV, $lopHocPhanList) = $this->getGiangVienPortalData();
        $maLopHocPhan = $_GET['maLopHocPhan'] ?? null;
        $lopHocPhanSelected = null;
        $sinhVienDiem = [];
        $thongKe = ['tong' => 0, 'dau' => 0, 'rot' => 0, 'chuaCoDiem' => 0];
        $loaiDiemList = [];
        $loaiDiemModel = $this->model('LoaiDiemModel');
        $loaiDiemList = $loaiDiemModel->readAll();
        // Loại bỏ TH (Thực hành) và TX (Thường xuyên) khỏi giao diện tra cứu điểm
        $loaiDiemList = array_values(array_filter($loaiDiemList, function($ld) {
            $m = strtoupper($ld['MaLoaiDiem'] ?? '');
            return $m !== 'TH' && $m !== 'TX';
        }));
        if ($maLopHocPhan) {
            $lhpModel = $this->model('LopHocPhanModel');
            $lopHocPhanSelected = $lhpModel->getById($maLopHocPhan);
            if ($lopHocPhanSelected && (strcasecmp($lopHocPhanSelected['MaGiangVien'] ?? '', $maGV) === 0)) {
                $dangKyModel = $this->model('DangKyHocModel');
                $diemModel = $this->model('ChiTietDiemModel');
                $dangKys = $dangKyModel->getByLopHocPhan($maLopHocPhan);
                foreach ($dangKys as $dk) {
                    $diems = $diemModel->getByDangKy($dk['MaDangKy'] ?? 0);
                    $diemByLoai = [];
                    foreach ($diems as $d) {
                        $tenLoai = $d['TenLoaiDiem'] ?? $d['MaLoaiDiem'] ?? '';
                        if ($tenLoai) {
                            $diemByLoai[$tenLoai] = ['SoDiem' => (float)($d['SoDiem'] ?? 0)];
                        }
                    }
                    $diemTB = $this->tinhDiemTrungBinh($diems);
                    $diemChu = $this->diemSoSangChu($diemTB);
                    $row = [
                        'MaSinhVien' => $dk['MaSinhVien'] ?? '',
                        'HoTen' => $dk['TenSinhVien'] ?? '',
                        'MaLop' => $dk['MaLop'] ?? '',
                        'MaDangKy' => $dk['MaDangKy'] ?? '',
                        'diem' => $diemByLoai,
                        'DiemTongKet' => $diemTB,
                        'DiemChu' => $diemChu,
                    ];
                    $sinhVienDiem[] = $row;
                    $thongKe['tong']++;
                    if ($diemTB === null) {
                        $thongKe['chuaCoDiem']++;
                    } else {
                        if ($diemTB >= 4.0) $thongKe['dau']++; else $thongKe['rot']++;
                    }
                }
            } else {
                $lopHocPhanSelected = null;
            }
        }
        require_once __DIR__ . '/../views/giangvien/tracuudiem.php';
    }

    /**
     * Tính điểm trung bình từ danh sách chi tiết điểm (CC*0.1 + GK*0.3 + CK*0.6)
     */
    private function tinhDiemTrungBinh($diems) {
        $diemCC = $diemGK = $diemCK = null;
        foreach ($diems as $d) {
            $loai = strtoupper($d['MaLoaiDiem'] ?? $d['TenLoaiDiem'] ?? '');
            $soDiem = (float)($d['SoDiem'] ?? 0);
            if (strpos($loai, 'CC') !== false || $loai === 'CHUYÊN CẦN') $diemCC = $soDiem;
            elseif (strpos($loai, 'GK') !== false || $loai === 'GIỮA KỲ') $diemGK = $soDiem;
            elseif (strpos($loai, 'CK') !== false || $loai === 'CUỐI KỲ') $diemCK = $soDiem;
        }
        if ($diemCC !== null && $diemGK !== null && $diemCK !== null) {
            return round($diemCC * 0.1 + $diemGK * 0.3 + $diemCK * 0.6, 2);
        }
        return null;
    }

    /**
     * Quy đổi điểm số sang điểm chữ (thang 4)
     */
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
     * Gửi thông báo - dùng view app/views/giangvien/guithongbao.php
     */
    public function guiThongBao() {
        list($giangVien, $maGV, $lopHocPhanList) = $this->getGiangVienPortalData();
        $maLopHocPhan = $_GET['maLopHocPhan'] ?? null;
        $lopHocPhanSelected = null;
        $sinhVienList = [];
        if ($maLopHocPhan) {
            $lhpModel = $this->model('LopHocPhanModel');
            $lopHocPhanSelected = $lhpModel->getById($maLopHocPhan);
            if ($lopHocPhanSelected && (strcasecmp($lopHocPhanSelected['MaGiangVien'] ?? '', $maGV) === 0)) {
                $dangKyModel = $this->model('DangKyHocModel');
                $dangKys = $dangKyModel->getByLopHocPhan($maLopHocPhan);
                foreach ($dangKys as $dk) {
                    $sinhVienList[] = [
                        'MaSinhVien' => $dk['MaSinhVien'] ?? '',
                        'HoTen' => $dk['TenSinhVien'] ?? '',
                        'Email' => $dk['Email'] ?? '',
                    ];
                }
            } else {
                $lopHocPhanSelected = null;
            }
        }
        require_once __DIR__ . '/../views/giangvien/guithongbao.php';
    }

    /**
     * Điểm danh - Bảng điểm danh theo lớp học phần
     * Stt, Mã SV, Tên SV, Mã HP, % tham gia, Điểm chuyên cần
     */
    public function diemDanh() {
        list($giangVien, $maGV, $lopHocPhanList) = $this->getGiangVienPortalData();
        $maLopHocPhan = $_GET['maLopHocPhan'] ?? null;
        $buoiSelected = isset($_GET['buoi']) ? (int)$_GET['buoi'] : 1;
        $lopHocPhanSelected = null;
        $bangDiemDanh = [];
        if ($maLopHocPhan) {
            foreach ($lopHocPhanList as $l) {
                if (($l['MaLopHocPhan'] ?? '') === $maLopHocPhan) {
                    $lopHocPhanSelected = $l;
                    break;
                }
            }
            if (!$lopHocPhanSelected) {
                $lhpModel = $this->model('LopHocPhanModel');
                $lopHocPhanSelected = $lhpModel->getById($maLopHocPhan);
            }
            if ($lopHocPhanSelected && (strcasecmp($lopHocPhanSelected['MaGiangVien'] ?? '', $maGV) === 0)) {
                $ddModel = $this->model('DiemDanhModel');
                $bangDiemDanh = $ddModel->getBangDiemDanhByLop($maLopHocPhan);
            } else {
                $lopHocPhanSelected = null;
            }
        }
        require_once __DIR__ . '/../views/giangvien/diemdanh.php';
    }

    /**
     * Lưu điểm danh buổi (POST)
     */
    public function saveDiemDanh() {
        if (!$this->isPost()) {
            $this->redirect('GiangVien/diemDanh');
        }
        list($giangVien, $maGV, $lopHocPhanList) = $this->getGiangVienPortalData();
        $maLop = $this->getPost('MaLopHocPhan');
        $buoiThu = (int) $this->getPost('BuoiThu');
        // Lấy trạng thái điểm danh: 1=Có mặt, 2=Muộn, 3=Nghỉ có lý do, 4=Nghỉ không lý do
        $danhSach = $_POST['trangThai'] ?? [];
        $baseUrl = defined('URLROOT') ? rtrim(URLROOT, '/') : '';
        
        if (empty($maLop) || $buoiThu < 1) {
            header('Location: ' . $baseUrl . '/GiangVien/diemDanh?maLopHocPhan=' . urlencode($maLop ?? ''));
            exit;
        }
        $lhpModel = $this->model('LopHocPhanModel');
        $lop = $lhpModel->getById($maLop);
        if (!$lop || strcasecmp($lop['MaGiangVien'] ?? '', $maGV) !== 0) {
            header('Location: ' . $baseUrl . '/GiangVien/diemDanh');
            exit;
        }
        // Kiểm tra giới hạn buổi: SoTinChi * 5 + 3
        $soTinChi = (int)($lop['SoTinChi'] ?? 1);
        $soBuoiToiDa = $soTinChi * 5 + 3;
        if ($buoiThu > $soBuoiToiDa) {
            header('Location: ' . $baseUrl . '/GiangVien/diemDanh?maLopHocPhan=' . urlencode($maLop) . '&error=limit');
            exit;
        }
        $ddModel = $this->model('DiemDanhModel');
        $ddModel->saveDiemDanhBuoi($maLop, $buoiThu, $danhSach, $maGV);
        
        // Giữ nguyên buổi đang điểm danh sau khi lưu
        header('Location: ' . $baseUrl . '/GiangVien/diemDanh?maLopHocPhan=' . urlencode($maLop) . '&buoi=' . $buoiThu . '&success=1');
        exit;
    }

    /**
     * Đồng bộ điểm chuyên cần từ điểm danh vào CHI_TIET_DIEM
     */
    public function dongBoDiemCC() {
        $baseUrl = defined('URLROOT') ? rtrim(URLROOT, '/') : '';
        list($giangVien, $maGV, $lopHocPhanList) = $this->getGiangVienPortalData();
        $maLop = $_GET['maLopHocPhan'] ?? '';
        if (empty($maLop)) {
            header('Location: ' . $baseUrl . '/GiangVien/diemDanh');
            exit;
        }
        $lhpModel = $this->model('LopHocPhanModel');
        $lop = $lhpModel->getById($maLop);
        if (!$lop || strcasecmp($lop['MaGiangVien'] ?? '', $maGV) !== 0) {
            header('Location: ' . $baseUrl . '/GiangVien/diemDanh');
            exit;
        }
        $ddModel = $this->model('DiemDanhModel');
        $bangDiem = $ddModel->getBangDiemDanhByLop($maLop);
        $ctdModel = $this->model('ChiTietDiemModel');
        foreach ($bangDiem as $r) {
            if (($r['DiemChuyenCan'] ?? null) !== null) {
                $ctdModel->MaDangKy = $r['MaDangKy'];
                $ctdModel->MaLoaiDiem = 'CC';
                $ctdModel->SoDiem = $r['DiemChuyenCan'];
                $ctdModel->NgayNhap = date('Y-m-d H:i:s');
                $ctdModel->NguoiNhap = $maGV;
                $ctdModel->upsert();
            }
        }
        header('Location: ' . $baseUrl . '/GiangVien/diemDanh?maLopHocPhan=' . urlencode($maLop) . '&sync=1');
        exit;
    }

    /**
     * Lịch giảng dạy - Thời khóa biểu của giảng viên
     */
    public function lichDay() {
        list($giangVien, $maGV, $lopHocPhanList) = $this->getGiangVienPortalData();
        $hocKyModel = $this->model('HocKyModel');
        $tkbModel = $this->model('ThoiKhoaBieuModel');
        $maHocKy = $_GET['maHocKy'] ?? null;
        $lichDay = $tkbModel->getLichDayByGiangVien($maGV, $maHocKy);
        $hocKys = $hocKyModel->readAll();
        require_once __DIR__ . '/../views/giangvien/lichday.php';
    }

    /**
     * API: Lấy danh sách sinh viên trong lớp học phần (cho dashboard - AJAX)
     * GET ?maLopHocPhan=...
     */
    public function getSinhVienLopHocPhan() {
        header('Content-Type: application/json; charset=utf-8');
        list($giangVien, $maGV, $lopHocPhanList) = $this->getGiangVienPortalData();
        $maLopHocPhan = $_GET['maLopHocPhan'] ?? '';
        if (empty($maLopHocPhan)) {
            echo json_encode(['success' => false, 'message' => 'Thiếu mã lớp học phần']);
            exit;
        }
        $lhpModel = $this->model('LopHocPhanModel');
        $lop = $lhpModel->getById($maLopHocPhan);
        if (!$lop || strcasecmp($lop['MaGiangVien'] ?? '', $maGV) !== 0) {
            echo json_encode(['success' => false, 'message' => 'Bạn không được phép xem lớp học phần này']);
            exit;
        }
        $dangKyModel = $this->model('DangKyHocModel');
        $sinhVien = $dangKyModel->getSinhVienByLopHocPhan($maLopHocPhan);
        echo json_encode(['success' => true, 'sinhVien' => $sinhVien], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * API: Lấy danh sách sinh viên và điểm của lớp học phần (cho trang nhập điểm - AJAX)
     * GET ?maLopHocPhan=...
     */
    public function getSinhVienDiem() {
        header('Content-Type: application/json; charset=utf-8');
        list($giangVien, $maGV, $lopHocPhanList) = $this->getGiangVienPortalData();
        $maLopHocPhan = $_GET['maLopHocPhan'] ?? '';
        if (empty($maLopHocPhan)) {
            echo json_encode(['success' => false, 'message' => 'Thiếu mã lớp học phần']);
            return;
        }
        $lhpModel = $this->model('LopHocPhanModel');
        $lop = $lhpModel->getById($maLopHocPhan);
        if (!$lop || strcasecmp($lop['MaGiangVien'] ?? '', $maGV) !== 0) {
            echo json_encode(['success' => false, 'message' => 'Bạn không được phép xem lớp học phần này']);
            return;
        }
        $dangKyModel = $this->model('DangKyHocModel');
        $diemModel = $this->model('ChiTietDiemModel');
        $dangKys = $dangKyModel->getByLopHocPhan($maLopHocPhan);
        $sinhVien = [];
        foreach ($dangKys as $dk) {
            $diems = $diemModel->getByDangKy($dk['MaDangKy'] ?? 0);
            $diemByLoai = [];
            foreach ($diems as $d) {
                $maLoai = $d['MaLoaiDiem'] ?? '';
                if ($maLoai) {
                    $diemByLoai[$maLoai] = ['SoDiem' => (float)($d['SoDiem'] ?? 0)];
                }
            }
            $diemTB = $this->tinhDiemTrungBinh($diems);
            $diemChu = $this->diemSoSangChu($diemTB);
            $sinhVien[] = [
                'MaDangKy' => $dk['MaDangKy'] ?? '',
                'MaSinhVien' => $dk['MaSinhVien'] ?? '',
                'HoTen' => $dk['TenSinhVien'] ?? '',
                'diem' => $diemByLoai,
                'DiemTongKet' => $diemTB,
                'DiemChu' => $diemChu,
            ];
        }
        echo json_encode(['success' => true, 'sinhVien' => $sinhVien], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * API: Lưu điểm (cho trang nhập điểm - AJAX POST JSON)
     * Body: { maDangKy: number, diem: { MaLoaiDiem: value } }
     */
    public function saveDiem() {
        header('Content-Type: application/json; charset=utf-8');
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
            return;
        }
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (!$data || empty($data['maDangKy'])) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            return;
        }
        $maDangKy = (int) $data['maDangKy'];
        
        // Kiểm tra trạng thái điểm - nếu đã khóa hoặc phê duyệt thì giảng viên không được sửa
        $dangKyModel = $this->model('DangKyHocModel');
        $trangThaiDiem = $dangKyModel->getTrangThaiDiem($maDangKy);
        if ($trangThaiDiem >= 1) {
            echo json_encode(['success' => false, 'message' => 'Điểm đã bị khóa. Liên hệ admin để chỉnh sửa!']);
            return;
        }
        
        $diemData = $data['diem'] ?? [];
        if (!is_array($diemData)) {
            $diemData = [];
        }
        $nguoiNhap = $_SESSION['user_name'] ?? $_SESSION['user_id'] ?? 'GV';
        $ngayNhap = date('Y-m-d H:i:s');
        $diemModel = $this->model('ChiTietDiemModel');
        foreach ($diemData as $maLoaiDiem => $value) {
            $soDiem = is_numeric($value) ? (float) $value : 0;
            if ($soDiem < 0 || $soDiem > 10) {
                continue;
            }
            $diemModel->MaDangKy = $maDangKy;
            $diemModel->MaLoaiDiem = $maLoaiDiem;
            $diemModel->SoDiem = $soDiem;
            $diemModel->NgayNhap = $ngayNhap;
            $diemModel->NguoiNhap = $nguoiNhap;
            $result = $diemModel->upsert();
            if ($result !== true) {
                echo json_encode(['success' => false, 'message' => $result ?: 'Lỗi khi lưu điểm']);
                return;
            }
        }
        echo json_encode(['success' => true]);
        exit;
    }

    /**
     * Alias cho exportDiem - dùng bởi nút "Xuất Excel" trong tra cứu điểm
     */
    public function xuatExcel() {
        $this->exportDiem();
    }

    /**
     * Xuất Excel danh sách lớp học phần
     */
    public function exportLopHocPhan() {
        list($giangVien, $maGV, $lopHocPhanList) = $this->getGiangVienPortalData();
        
        $filename = 'DanhSach_LopHocPhan_' . $maGV . '_' . date('Ymd_His') . '.csv';
        
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // BOM for UTF-8 Excel
        echo "\xEF\xBB\xBF";
        
        $output = fopen('php://output', 'w');
        
        // Header row
        fputcsv($output, ['Mã Lớp HP', 'Tên Môn Học', 'Mã Môn Học', 'Tên Lớp', 'Số Tín Chỉ', 'Số SV Đăng Ký', 'Thứ', 'Tiết Học', 'Phòng Học']);
        
        // Data rows
        foreach ($lopHocPhanList as $lop) {
            fputcsv($output, [
                $lop['MaLopHocPhan'] ?? '',
                $lop['TenMonHoc'] ?? ($lop['MaMonHoc'] ?? ''),
                $lop['MaMonHoc'] ?? '',
                $lop['TenLop'] ?? '',
                $lop['SoTinChi'] ?? '',
                $lop['SoSinhVien'] ?? 0,
                $lop['Thu'] ?? '',
                $lop['TietHoc'] ?? '',
                $lop['PhongHoc'] ?? ''
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Xuất Excel danh sách sinh viên của lớp học phần
     * GET ?maLopHocPhan=...
     */
    public function exportSinhVien() {
        list($giangVien, $maGV, $lopHocPhanList) = $this->getGiangVienPortalData();
        
        $maLopHocPhan = $_GET['maLopHocPhan'] ?? '';
        if (empty($maLopHocPhan)) {
            header('HTTP/1.1 400 Bad Request');
            echo 'Thiếu mã lớp học phần';
            exit;
        }
        
        $lhpModel = $this->model('LopHocPhanModel');
        $lop = $lhpModel->getById($maLopHocPhan);
        if (!$lop || strcasecmp($lop['MaGiangVien'] ?? '', $maGV) !== 0) {
            header('HTTP/1.1 403 Forbidden');
            echo 'Bạn không có quyền xuất danh sách lớp này';
            exit;
        }
        
        $dangKyModel = $this->model('DangKyHocModel');
        $sinhVien = $dangKyModel->getSinhVienByLopHocPhan($maLopHocPhan);
        
        $filename = 'DanhSach_SinhVien_' . $maLopHocPhan . '_' . date('Ymd_His') . '.csv';
        
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo "\xEF\xBB\xBF";
        
        $output = fopen('php://output', 'w');
        
        // Header row
        fputcsv($output, ['STT', 'Mã Sinh Viên', 'Họ Tên', 'Lớp Hành Chính', 'Email', 'Số Điện Thoại', 'Trạng Thái']);
        
        // Data rows
        $stt = 1;
        foreach ($sinhVien as $sv) {
            fputcsv($output, [
                $stt++,
                $sv['MaSinhVien'] ?? '',
                $sv['HoTen'] ?? '',
                $sv['LopHanhChinh'] ?? '',
                $sv['Email'] ?? '',
                $sv['SoDienThoai'] ?? '',
                $sv['TrangThai'] ?? 'Đang học'
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Xuất Excel điểm của lớp học phần
     * GET ?maLopHocPhan=...
     */
    public function exportDiem() {
        list($giangVien, $maGV, $lopHocPhanList) = $this->getGiangVienPortalData();
        
        $maLopHocPhan = $_GET['maLopHocPhan'] ?? '';
        if (empty($maLopHocPhan)) {
            header('HTTP/1.1 400 Bad Request');
            echo 'Thiếu mã lớp học phần';
            exit;
        }
        
        $lhpModel = $this->model('LopHocPhanModel');
        $lop = $lhpModel->getById($maLopHocPhan);
        if (!$lop || strcasecmp($lop['MaGiangVien'] ?? '', $maGV) !== 0) {
            header('HTTP/1.1 403 Forbidden');
            echo 'Bạn không có quyền xuất điểm lớp này';
            exit;
        }
        
        $dangKyModel = $this->model('DangKyHocModel');
        $diemModel = $this->model('ChiTietDiemModel');
        $loaiDiemModel = $this->model('LoaiDiemModel');
        
        $dangKys = $dangKyModel->getByLopHocPhan($maLopHocPhan);
        $loaiDiemList = $loaiDiemModel->readAll();
        // Loại bỏ TH (Thực hành) và TX (Thường xuyên) khỏi file xuất Excel
        $loaiDiemList = array_values(array_filter($loaiDiemList, function($ld) {
            $m = strtoupper($ld['MaLoaiDiem'] ?? '');
            return $m !== 'TH' && $m !== 'TX';
        }));
        
        $filename = 'BangDiem_' . $maLopHocPhan . '_' . date('Ymd_His') . '.csv';
        
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo "\xEF\xBB\xBF";
        
        $output = fopen('php://output', 'w');
        
        // Build header with dynamic loai diem columns
        $header = ['STT', 'Mã Sinh Viên', 'Họ Tên', 'Lớp'];
        $loaiDiemCodes = [];
        foreach ($loaiDiemList as $ld) {
            $header[] = $ld['TenLoaiDiem'] ?? $ld['MaLoaiDiem'];
            $loaiDiemCodes[] = $ld['MaLoaiDiem'];
        }
        $header[] = 'Điểm Tổng Kết';
        $header[] = 'Điểm Chữ';
        fputcsv($output, $header);
        
        // Data rows
        $stt = 1;
        foreach ($dangKys as $dk) {
            $diems = $diemModel->getByDangKy($dk['MaDangKy'] ?? 0);
            $diemByLoai = [];
            foreach ($diems as $d) {
                $maLoai = $d['MaLoaiDiem'] ?? '';
                if ($maLoai) {
                    $diemByLoai[$maLoai] = $d['SoDiem'] ?? '';
                }
            }
            
            $diemTB = $this->tinhDiemTrungBinh($diems);
            $diemChu = $this->diemSoSangChu($diemTB);
            
            $row = [
                $stt++,
                $dk['MaSinhVien'] ?? '',
                $dk['TenSinhVien'] ?? '',
                $dk['MaLop'] ?? ''
            ];
            
            foreach ($loaiDiemCodes as $code) {
                $row[] = isset($diemByLoai[$code]) ? $diemByLoai[$code] : '';
            }
            
            $row[] = $diemTB !== null ? $diemTB : '';
            $row[] = $diemChu;
            
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Hiển thị danh sách giảng viên
     */
    public function index() {
        $data = $this->buildIndexData();
        require_once __DIR__ . '/../views/admin/giangvien/index.php';
    }

    /**
     * Xây dựng dữ liệu cho trang index
     */
    private function buildIndexData($error = '', $success = '') {
        return [
            'giangviens' => $this->gvModel->readAll(),
            'khoas' => $this->khoaModel->readAll(),
            'pageTitle' => 'Quản lý Giảng viên',
            'breadcrumb' => 'Giảng viên',
            'error' => $error,
            'success' => $success
        ];
    }

    /**
     * Thêm mới giảng viên
     */
    public function store() {
        if (!$this->isPost()) {
            $this->redirect('GiangVien/index');
        }

        $input = [
            'MaGiangVien' => $this->getPost('MaGiangVien'),
            'HoTen' => $this->getPost('HoTen'),
            'NgaySinh' => $this->getPost('NgaySinh'),
            'GioiTinh' => $this->getPost('GioiTinh'),
            'Email' => $this->getPost('Email'),
            'SoDienThoai' => $this->getPost('SoDienThoai'),
            'HocVi' => $this->getPost('HocVi'),
            'MaKhoa' => $this->getPost('MaKhoa'),
        ];

        // Validate
        $errors = $this->validate($input, [
            'MaGiangVien' => 'required|max:20',
            'HoTen' => 'required|max:100',
            'Email' => 'email',
            'SoDienThoai' => 'phone'
        ]);

        // Validate ngày sinh
        if (!empty($input['NgaySinh'])) {
            $dobError = $this->validateDob($input['NgaySinh'], 22);
            if ($dobError) $errors['NgaySinh'] = $dobError;
        }

        if (!empty($errors)) {
            $data = $this->buildIndexData(implode(' ', $errors));
            require_once __DIR__ . '/../views/admin/giangvien/index.php';
            return;
        }

        // Gán dữ liệu vào model
        $this->gvModel->MaGiangVien = $input['MaGiangVien'];
        $this->gvModel->HoTen = $input['HoTen'];
        $this->gvModel->NgaySinh = $input['NgaySinh'] ?: null;
        $this->gvModel->GioiTinh = $input['GioiTinh'] ?: null;
        $this->gvModel->Email = $input['Email'] ?: null;
        $this->gvModel->SoDienThoai = $input['SoDienThoai'] ?: null;
        $this->gvModel->HocVi = $input['HocVi'] ?: null;
        $this->gvModel->MaKhoa = $input['MaKhoa'] ?: null;
        $this->gvModel->TrangThai = 'Đang làm việc';

        $result = $this->gvModel->create();
        if ($result === true) {
            // Tự động tạo tài khoản đăng nhập: tên đăng nhập = mã GV, mật khẩu mặc định 123456
            $userModel = $this->model('UserModel');
            if (!$userModel->existsByTenDangNhap($input['MaGiangVien'])) {
                $userModel->TenDangNhap = $input['MaGiangVien'];
                $userModel->MatKhau = password_hash('123456', PASSWORD_DEFAULT);
                $userModel->HoTen = $input['HoTen'];
                $userModel->Email = (!empty($input['Email']) && !$userModel->existsByEmail($input['Email'])) ? $input['Email'] : null;
                $userModel->SoDienThoai = $input['SoDienThoai'] ?: null;
                $userModel->VaiTro = 'GiangVien';
                $userModel->TrangThai = 1;
                $userModel->create();
            }
            $this->redirect('GiangVien/index');
        }

        $data = $this->buildIndexData($result);
        require_once __DIR__ . '/../views/admin/giangvien/index.php';
    }

    /**
     * Hiển thị form sửa giảng viên
     */
    public function edit($id) {
        $gv = $this->gvModel->getById($id);
        if (!$gv) {
            $this->redirect('GiangVien/index');
        }

        $data = [
            'giangvien' => $gv,
            'khoas' => $this->khoaModel->readAll(),
            'pageTitle' => 'Sửa giảng viên',
            'breadcrumb' => 'Sửa giảng viên',
            'error' => ''
        ];
        require_once __DIR__ . '/../views/admin/giangvien/edit.php';
    }

    /**
     * Cập nhật giảng viên
     */
    public function update($id) {
        if (!$this->isPost()) {
            $this->redirect('GiangVien/index');
        }

        $input = [
            'HoTen' => $this->getPost('HoTen'),
            'NgaySinh' => $this->getPost('NgaySinh'),
            'GioiTinh' => $this->getPost('GioiTinh'),
            'Email' => $this->getPost('Email'),
            'SoDienThoai' => $this->getPost('SoDienThoai'),
            'HocVi' => $this->getPost('HocVi'),
            'MaKhoa' => $this->getPost('MaKhoa'),
            'TrangThai' => $this->getPost('TrangThai'),
        ];

        // Validate
        $errors = $this->validate($input, [
            'HoTen' => 'required|max:100',
            'Email' => 'email',
            'SoDienThoai' => 'phone'
        ]);

        if (!empty($input['NgaySinh'])) {
            $dobError = $this->validateDob($input['NgaySinh'], 22);
            if ($dobError) $errors['NgaySinh'] = $dobError;
        }

        if (!empty($errors)) {
            $data = [
                'giangvien' => array_merge(['MaGiangVien' => $id], $input),
                'khoas' => $this->khoaModel->readAll(),
                'pageTitle' => 'Sửa giảng viên',
                'breadcrumb' => 'Sửa giảng viên',
                'error' => implode(' ', $errors)
            ];
            require_once __DIR__ . '/../views/admin/giangvien/edit.php';
            return;
        }

        // Gán dữ liệu vào model
        $this->gvModel->MaGiangVien = $id;
        $this->gvModel->HoTen = $input['HoTen'];
        $this->gvModel->NgaySinh = $input['NgaySinh'] ?: null;
        $this->gvModel->GioiTinh = $input['GioiTinh'] ?: null;
        $this->gvModel->Email = $input['Email'] ?: null;
        $this->gvModel->SoDienThoai = $input['SoDienThoai'] ?: null;
        $this->gvModel->HocVi = $input['HocVi'] ?: null;
        $this->gvModel->MaKhoa = $input['MaKhoa'] ?: null;
        $this->gvModel->TrangThai = $input['TrangThai'] ?: 'Đang làm việc';

        $result = $this->gvModel->update();
        if ($result === true) {
            $this->redirect('GiangVien/index');
        }

        $data = [
            'giangvien' => array_merge(['MaGiangVien' => $id], $input),
            'khoas' => $this->khoaModel->readAll(),
            'pageTitle' => 'Sửa giảng viên',
            'breadcrumb' => 'Sửa giảng viên',
            'error' => $result
        ];
        require_once __DIR__ . '/../views/admin/giangvien/edit.php';
    }

    /**
     * Xóa giảng viên
     */
    public function delete($id) {
        $this->gvModel->MaGiangVien = $id;
        $result = $this->gvModel->delete();
        
        if ($result !== true) {
            // Có lỗi khi xóa, quay lại với thông báo lỗi
            $data = $this->buildIndexData($result);
            require_once __DIR__ . '/../views/admin/giangvien/index.php';
            return;
        }
        
        $this->redirect('GiangVien/index');
    }

    /**
     * API: Lấy mã giảng viên tiếp theo (AJAX)
     */
    public function getNextId() {
        header('Content-Type: application/json');
        $nextId = $this->gvModel->generateNextId('GV');
        echo json_encode(['success' => true, 'nextId' => $nextId]);
        exit;
    }
}