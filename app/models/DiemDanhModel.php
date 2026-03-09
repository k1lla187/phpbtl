<?php
/**
 * DiemDanhModel - Quản lý điểm danh
 * 1 tín chỉ = 5 ca = 15 tiết. Điểm chuyên cần = % tham gia buổi học * 10
 * Trạng thái: 1=Có mặt, 2=Muộn, 3=Nghỉ có lý do, 4=Nghỉ không lý do
 */
require_once __DIR__ . '/../core/Model.php';

class DiemDanhModel extends Model {
    protected $table_name = "DIEM_DANH";
    protected $primaryKey = "ID";

    // Trạng thái điểm danh
    const CO_MAT = 1;
    const MUON = 2;
    const NGHI_CO_LY_DO = 3;
    const NGHI_KHONG_LY_DO = 4;

    public $ID;
    public $MaDangKy;
    public $MaLopHocPhan;
    public $BuoiThu;
    public $NgayDiemDanh;
    public $CoMat;
    public $TrangThai;
    public $GhiChu;
    public $NguoiDiemDanh;

    /**
     * Lấy bảng điểm danh theo lớp học phần với đầy đủ thông tin
     */
    public function getBangDiemDanhByLop($maLopHocPhan) {
        if (empty($maLopHocPhan)) return [];
        try {
            $query = "SELECT dk.MaDangKy, sv.MaSinhVien, sv.HoTen, lhp.MaLopHocPhan, lhp.MaMonHoc, mh.TenMonHoc, mh.SoTinChi,
                      (SELECT COUNT(*) FROM {$this->table_name} dd WHERE dd.MaDangKy = dk.MaDangKy AND dd.TrangThai = 1) as SoBuoiCoMat,
                      (SELECT COUNT(*) FROM {$this->table_name} dd WHERE dd.MaDangKy = dk.MaDangKy AND dd.TrangThai = 2) as SoBuoiMuon,
                      (SELECT COUNT(*) FROM {$this->table_name} dd WHERE dd.MaDangKy = dk.MaDangKy AND dd.TrangThai = 3) as SoBuoiNghiCoLyDo,
                      (SELECT COUNT(*) FROM {$this->table_name} dd WHERE dd.MaDangKy = dk.MaDangKy AND dd.TrangThai = 4) as SoBuoiNghiKhongLyDo,
                      (SELECT COUNT(*) FROM {$this->table_name} dd WHERE dd.MaDangKy = dk.MaDangKy) as SoBuoiDaDiemDanh
                      FROM DANG_KY_HOC dk
                      JOIN SINH_VIEN sv ON dk.MaSinhVien = sv.MaSinhVien
                      JOIN LOP_HOC_PHAN lhp ON dk.MaLopHocPhan = lhp.MaLopHocPhan
                      LEFT JOIN MON_HOC mh ON lhp.MaMonHoc = mh.MaMonHoc
                      WHERE dk.MaLopHocPhan = :maLop
                      ORDER BY sv.MaSinhVien";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maLop', $maLopHocPhan);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $result = [];
            foreach ($rows as $r) {
                $soTinChi = (int)($r['SoTinChi'] ?? 1);
                $tongBuoi = $soTinChi * 5;
                $soCoMat = (int)($r['SoBuoiCoMat'] ?? 0);
                $soMuon = (int)($r['SoBuoiMuon'] ?? 0);
                $soNghiCoLyDo = (int)($r['SoBuoiNghiCoLyDo'] ?? 0);
                $soNghiKhongLyDo = (int)($r['SoBuoiNghiKhongLyDo'] ?? 0);
                $soDaDD = (int)($r['SoBuoiDaDiemDanh'] ?? 0);

                // Tính điểm CC:
                // Có mặt: 100% điểm
                // Muộn: 50% điểm (trừ 50%)
                // Nghỉ có lý do: 100% điểm (không trừ)
                // Nghỉ không lý do: 0% điểm (trừ 100%)
                $diemToiDa = $tongBuoi * 1.0; // Mỗi buổi tối đa 1 điểm (thang 10 điểm / 10 buổi)
                $diemThucTe = $soCoMat * 1.0 + $soMuon * 0.5 + $soNghiCoLyDo * 1.0 + $soNghiKhongLyDo * 0.0;
                
                // Giới hạn % tham gia tối đa là 100%
                $phanTram = $tongBuoi > 0 ? min(100, round($diemThucTe / $diemToiDa * 100, 1)) : 0;
                // Giới hạn điểm CC tối đa là 10
                $diemCC = $tongBuoi > 0 ? min(10, round($diemThucTe / $diemToiDa * 10, 2)) : null;
                
                $result[] = array_merge($r, [
                    'TongBuoi' => $tongBuoi,
                    'PhanTramThamGia' => $phanTram,
                    'DiemChuyenCan' => $diemCC,
                ]);
            }
            return $result;
        } catch (PDOException $e) {
            error_log("DiemDanhModel::getBangDiemDanhByLop: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy chi tiết điểm danh theo MaDangKy (các buổi)
     */
    public function getByMaDangKy($maDangKy) {
        if (empty($maDangKy)) return [];
        try {
            $query = "SELECT * FROM {$this->table_name} WHERE MaDangKy = :maDangKy ORDER BY BuoiThu";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maDangKy', $maDangKy);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Cập nhật hoặc tạo điểm danh cho một buổi
     */
    /**
     * Lưu điểm danh một sinh viên cho một buổi
     * @param int $maDangKy Mã đăng ký
     * @param string $maLopHocPhan Mã lớp học phần
     * @param int $buoiThu Buổi thứ
     * @param int $trangThai Trạng thái: 1=Có mặt, 2=Muộn, 3=Nghỉ có lý do, 4=Nghỉ không lý do
     * @param string|null $ngayDiemDanh Ngày điểm danh
     * @param string|null $nguoiDD Người điểm danh
     */
    public function upsertBuoi($maDangKy, $maLopHocPhan, $buoiThu, $trangThai, $ngayDiemDanh = null, $nguoiDD = null) {
        $ngay = $ngayDiemDanh ?: date('Y-m-d');
        
        // Xác định CoMat dựa trên trạng thái
        // CoMat = 1: được tính điểm đầy đủ (Có mặt, Nghỉ có lý do)
        // CoMat = 0: bị trừ điểm (Muộn, Nghỉ không lý do)
        $coMat = in_array($trangThai, [self::CO_MAT, self::NGHI_CO_LY_DO]) ? 1 : 0;
        
        try {
            $query = "INSERT INTO {$this->table_name} (MaDangKy, MaLopHocPhan, BuoiThu, NgayDiemDanh, CoMat, TrangThai, NguoiDiemDanh)
                      VALUES (:maDangKy, :maLop, :buoiThu, :ngay, :coMat, :trangThai, :nguoi)
                      ON DUPLICATE KEY UPDATE CoMat = :coMat2, TrangThai = :trangThai2, NgayDiemDanh = :ngay2, NguoiDiemDanh = :nguoi2";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':maDangKy', $maDangKy);
            $stmt->bindValue(':maLop', $maLopHocPhan);
            $stmt->bindValue(':buoiThu', (int)$buoiThu);
            $stmt->bindValue(':ngay', $ngay);
            $stmt->bindValue(':coMat', $coMat);
            $stmt->bindValue(':trangThai', (int)$trangThai);
            $stmt->bindValue(':nguoi', $nguoiDD);
            $stmt->bindValue(':coMat2', $coMat);
            $stmt->bindValue(':trangThai2', (int)$trangThai);
            $stmt->bindValue(':ngay2', $ngay);
            $stmt->bindValue(':nguoi2', $nguoiDD);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("DiemDanhModel::upsertBuoi: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lưu điểm danh hàng loạt (buổi hiện tại)
     * @param string $maLopHocPhan Mã lớp học phần
     * @param int $buoiThu Buổi thứ
     * @param array $danhSachTrangThai Mảng [MaDangKy => TrangThai]
     * @param string|null $nguoiDD Người điểm danh
     */
    public function saveDiemDanhBuoi($maLopHocPhan, $buoiThu, $danhSachTrangThai, $nguoiDD = null) {
        require_once __DIR__ . '/DangKyHocModel.php';
        $dangKyModel = new DangKyHocModel($this->conn);
        $dangKys = $dangKyModel->getByLopHocPhan($maLopHocPhan);
        $ngay = date('Y-m-d');
        foreach ($dangKys as $dk) {
            $maDK = $dk['MaDangKy'] ?? 0;
            // Mặc định là nghỉ không lý do (4) nếu không có trong danh sách
            $trangThai = isset($danhSachTrangThai[$maDK]) ? (int)$danhSachTrangThai[$maDK] : self::NGHI_KHONG_LY_DO;
            $this->upsertBuoi($maDK, $maLopHocPhan, $buoiThu, $trangThai, $ngay, $nguoiDD);
        }
        return true;
    }

    /**
     * Lấy điểm danh của sinh viên (cho sinh viên xem)
     */
    public function getByMaSinhVien($maSinhVien, $maHocKy = null) {
        try {
            $query = "SELECT dd.*, dk.MaLopHocPhan, lhp.MaMonHoc, mh.TenMonHoc, mh.SoTinChi, 
                      hk.TenHocKy, hk.NamHoc
                      FROM {$this->table_name} dd
                      JOIN DANG_KY_HOC dk ON dd.MaDangKy = dk.MaDangKy
                      JOIN LOP_HOC_PHAN lhp ON dk.MaLopHocPhan = lhp.MaLopHocPhan
                      LEFT JOIN MON_HOC mh ON lhp.MaMonHoc = mh.MaMonHoc
                      LEFT JOIN HOC_KY hk ON lhp.MaHocKy = hk.MaHocKy
                      WHERE dk.MaSinhVien = :maSV";
            
            if ($maHocKy) {
                $query .= " AND lhp.MaHocKy = :maHocKy";
            }
            
            $query .= " ORDER BY hk.NamHoc DESC, hk.MaHocKy, mh.TenMonHoc, dd.BuoiThu";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maSV', $maSinhVien);
            if ($maHocKy) {
                $stmt->bindParam(':maHocKy', $maHocKy);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("DiemDanhModel::getByMaSinhVien: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy tổng hợp điểm danh của sinh viên theo môn học
     */
    public function getTongHopDiemDanhBySinhVien($maSinhVien, $maHocKy = null) {
        try {
            $query = "SELECT dk.MaDangKy, dk.MaLopHocPhan, lhp.MaMonHoc, mh.TenMonHoc, mh.SoTinChi,
                      hk.TenHocKy, hk.NamHoc,
                      (SELECT COUNT(*) FROM {$this->table_name} dd WHERE dd.MaDangKy = dk.MaDangKy AND dd.TrangThai = 1) as SoBuoiCoMat,
                      (SELECT COUNT(*) FROM {$this->table_name} dd WHERE dd.MaDangKy = dk.MaDangKy AND dd.TrangThai = 2) as SoBuoiMuon,
                      (SELECT COUNT(*) FROM {$this->table_name} dd WHERE dd.MaDangKy = dk.MaDangKy AND dd.TrangThai = 3) as SoBuoiNghiCoLyDo,
                      (SELECT COUNT(*) FROM {$this->table_name} dd WHERE dd.MaDangKy = dk.MaDangKy AND dd.TrangThai = 4) as SoBuoiNghiKhongLyDo,
                      (SELECT COUNT(*) FROM {$this->table_name} dd WHERE dd.MaDangKy = dk.MaDangKy) as SoBuoiDaDiemDanh
                      FROM DANG_KY_HOC dk
                      JOIN LOP_HOC_PHAN lhp ON dk.MaLopHocPhan = lhp.MaLopHocPhan
                      LEFT JOIN MON_HOC mh ON lhp.MaMonHoc = mh.MaMonHoc
                      LEFT JOIN HOC_KY hk ON lhp.MaHocKy = hk.MaHocKy
                      WHERE dk.MaSinhVien = :maSV";
            
            if ($maHocKy) {
                $query .= " AND lhp.MaHocKy = :maHocKy";
            }
            
            $query .= " ORDER BY hk.NamHoc DESC, hk.MaHocKy, mh.TenMonHoc";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maSV', $maSinhVien);
            if ($maHocKy) {
                $stmt->bindParam(':maHocKy', $maHocKy);
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $result = [];
            foreach ($rows as $r) {
                $soTinChi = (int)($r['SoTinChi'] ?? 1);
                $tongBuoi = $soTinChi * 5;
                $soCoMat = (int)($r['SoBuoiCoMat'] ?? 0);
                $soMuon = (int)($r['SoBuoiMuon'] ?? 0);
                $soNghiCoLyDo = (int)($r['SoBuoiNghiCoLyDo'] ?? 0);
                $soNghiKhongLyDo = (int)($r['SoBuoiNghiKhongLyDo'] ?? 0);
                $soDaDD = (int)($r['SoBuoiDaDiemDanh'] ?? 0);
                
                // Tính điểm CC theo trạng thái
                $diemToiDa = $tongBuoi * 1.0;
                $diemThucTe = $soCoMat * 1.0 + $soMuon * 0.5 + $soNghiCoLyDo * 1.0 + $soNghiKhongLyDo * 0.0;
                $phanTram = $tongBuoi > 0 ? min(100, round($diemThucTe / $diemToiDa * 100, 1)) : 0;
                $diemCC = $tongBuoi > 0 ? min(10, round($diemThucTe / $diemToiDa * 10, 2)) : null;
                
                // Kiểm tra đủ buổi hay chưa (tối thiểu 80%)
                $duBuoi = $phanTram >= 80 ? true : false;
                
                $result[] = array_merge($r, [
                    'TongBuoi' => $tongBuoi,
                    'SoBuoiConLai' => max(0, $tongBuoi - $soDaDD),
                    'PhanTramThamGia' => $phanTram,
                    'DiemChuyenCan' => $diemCC,
                    'DaDuBuoi' => $duBuoi
                ]);
            }
            return $result;
        } catch (PDOException $e) {
            error_log("DiemDanhModel::getTongHopDiemDanhBySinhVien: " . $e->getMessage());
            return [];
        }
    }
}
