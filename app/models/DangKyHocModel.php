<?php
/**
 * DangKyHocModel - Quản lý đăng ký học
 */
require_once __DIR__ . '/../core/Model.php';

class DangKyHocModel extends Model {
    protected $table_name = "DANG_KY_HOC";
    protected $primaryKey = "MaDangKy";

    public $MaDangKy;
    public $MaSinhVien;
    public $MaLopHocPhan;
    public $NgayDangKy;
    public $DiemTongKet;
    public $DiemChu;
    public $DiemSo;
    public $KetQua;
    public $TrangThaiDiem;
    public $NgayKhoaDiem;
    public $NguoiKhoaDiem;
    public $NgayPheDuyet;
    public $NguoiPheDuyet;

    public function __construct($db) {
        parent::__construct($db);
    }

    /**
     * Lấy tất cả đăng ký học với chi tiết
     */
    public function readAllWithDetails() {
        $query = "SELECT dk.*, sv.HoTen as TenSinhVien, lhp.MaLopHocPhan, mh.TenMonHoc, hk.TenHocKy
                  FROM {$this->table_name} dk
                  LEFT JOIN SINH_VIEN sv ON dk.MaSinhVien = sv.MaSinhVien
                  LEFT JOIN LOP_HOC_PHAN lhp ON dk.MaLopHocPhan = lhp.MaLopHocPhan
                  LEFT JOIN MON_HOC mh ON lhp.MaMonHoc = mh.MaMonHoc
                  LEFT JOIN HOC_KY hk ON lhp.MaHocKy = hk.MaHocKy
                  ORDER BY dk.NgayDangKy DESC";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Lấy đăng ký học theo lớp học phần
     */
    public function getByLopHocPhan($maLopHocPhan) {
        $query = "SELECT DISTINCT dk.MaDangKy, dk.MaSinhVien, dk.MaLopHocPhan, dk.NgayDangKy,
                  dk.DiemTongKet, dk.DiemChu, dk.DiemSo, dk.KetQua,
                  sv.HoTen as TenSinhVien, sv.MaLop
                  FROM {$this->table_name} dk
                  JOIN SINH_VIEN sv ON dk.MaSinhVien = sv.MaSinhVien
                  WHERE dk.MaLopHocPhan = :maLop
                  ORDER BY dk.MaSinhVien";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maLop', $maLopHocPhan);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Lấy danh sách sinh viên trong lớp học phần (đủ thông tin hiển thị: mã, tên, lớp HC, email, SĐT, trạng thái)
     */
    public function getSinhVienByLopHocPhan($maLopHocPhan) {
        $query = "SELECT DISTINCT dk.MaSinhVien, sv.HoTen, sv.Email, sv.SoDienThoai, sv.MaLop, sv.TrangThaiHocTap,
                  lhc.TenLop as LopHanhChinh
                  FROM {$this->table_name} dk
                  JOIN SINH_VIEN sv ON dk.MaSinhVien = sv.MaSinhVien
                  LEFT JOIN LOP_HANH_CHINH lhc ON sv.MaLop = lhc.MaLop
                  WHERE dk.MaLopHocPhan = :maLop
                  ORDER BY sv.MaSinhVien";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maLop', $maLopHocPhan);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $list = [];
            foreach ($rows as $r) {
                $list[] = [
                    'MaSinhVien' => $r['MaSinhVien'] ?? '',
                    'HoTen' => $r['HoTen'] ?? '',
                    'LopHanhChinh' => $r['LopHanhChinh'] ?? ($r['MaLop'] ?? ''),
                    'Email' => $r['Email'] ?? '',
                    'SoDienThoai' => $r['SoDienThoai'] ?? '',
                    'TrangThai' => $r['TrangThaiHocTap'] ?? 'Đang học',
                ];
            }
            return $list;
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Lấy tất cả đăng ký học của sinh viên, kèm môn học, học kỳ, điểm
     */
    public function getByMaSinhVienWithDetails($maSinhVien) {
        $query = "SELECT dk.MaDangKy, dk.MaSinhVien, dk.MaLopHocPhan, dk.NgayDangKy,
                  dk.DiemTongKet, dk.DiemChu, dk.DiemSo, dk.KetQua,
                  lhp.MaMonHoc, lhp.MaHocKy,
                  mh.TenMonHoc, mh.SoTinChi,
                  gv.HoTen as TenGV, gv.MaGiangVien,
                  hk.TenHocKy, hk.NamHoc
                  FROM {$this->table_name} dk
                  JOIN LOP_HOC_PHAN lhp ON dk.MaLopHocPhan = lhp.MaLopHocPhan
                  LEFT JOIN MON_HOC mh ON lhp.MaMonHoc = mh.MaMonHoc
                  LEFT JOIN GIANG_VIEN gv ON lhp.MaGiangVien = gv.MaGiangVien
                  LEFT JOIN HOC_KY hk ON lhp.MaHocKy = hk.MaHocKy
                  WHERE dk.MaSinhVien = :maSV
                  ORDER BY hk.NamHoc DESC, hk.MaHocKy, mh.TenMonHoc";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maSV', $maSinhVien);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Kiểm tra sinh viên đã đăng ký lớp học phần chưa
     */
    public function isRegistered($maSinhVien, $maLopHocPhan) {
        $query = "SELECT COUNT(*) as count FROM {$this->table_name} 
                  WHERE MaSinhVien = :maSV AND MaLopHocPhan = :maLHP";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maSV', $maSinhVien);
            $stmt->bindParam(':maLHP', $maLopHocPhan);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Tạo mới đăng ký học
     */
    public function create() {
        // Kiểm tra đã đăng ký chưa
        if ($this->isRegistered($this->MaSinhVien, $this->MaLopHocPhan)) {
            return "Sinh viên đã đăng ký lớp học phần này rồi!";
        }

        $query = "INSERT INTO {$this->table_name} (MaSinhVien, MaLopHocPhan, NgayDangKy) 
                  VALUES (:MaSinhVien, :MaLopHocPhan, NOW())";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaSinhVien", $this->MaSinhVien);
            $stmt->bindParam(":MaLopHocPhan", $this->MaLopHocPhan);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return $this->handlePdoException($e);
        }
    }

    /**
     * Cập nhật điểm
     */
    public function updateDiem() {
        $query = "UPDATE {$this->table_name} 
                  SET DiemTongKet=:DiemTongKet, DiemChu=:DiemChu, DiemSo=:DiemSo, KetQua=:KetQua 
                  WHERE MaDangKy=:MaDangKy";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaDangKy", $this->MaDangKy);
            $stmt->bindParam(":DiemTongKet", $this->DiemTongKet);
            $stmt->bindParam(":DiemChu", $this->DiemChu);
            $stmt->bindParam(":DiemSo", $this->DiemSo);
            $stmt->bindParam(":KetQua", $this->KetQua);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return $this->handlePdoException($e);
        }
    }

    /**
     * Xóa đăng ký học
     */
    public function delete() {
        $query = "DELETE FROM {$this->table_name} WHERE MaDangKy = :MaDangKy";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaDangKy", $this->MaDangKy);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return $this->handlePdoException($e);
        }
    }

    /**
     * Lấy lịch học của sinh viên đã đăng ký (để kiểm tra trùng lịch)
     */
    public function getRegisteredSchedule($maSinhVien, $maHocKy = null) {
        try {
            $sql = "SELECT tkb.*, lhp.MaLopHocPhan, lhp.MaMonHoc, mh.TenMonHoc
                    FROM {$this->table_name} dk
                    INNER JOIN LOP_HOC_PHAN lhp ON dk.MaLopHocPhan = lhp.MaLopHocPhan
                    INNER JOIN THOI_KHOA_BIEU tkb ON lhp.MaLopHocPhan = tkb.MaLopHocPhan
                    LEFT JOIN MON_HOC mh ON lhp.MaMonHoc = mh.MaMonHoc
                    WHERE dk.MaSinhVien = :MaSinhVien";
            
            if ($maHocKy) {
                $sql .= " AND lhp.MaHocKy = :MaHocKy";
            }
            
            $sql .= " ORDER BY tkb.Thu, tkb.TietBatDau";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":MaSinhVien", $maSinhVien);
            if ($maHocKy) {
                $stmt->bindParam(":MaHocKy", $maHocKy);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getRegisteredSchedule: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Kiểm tra xem lớp học phần đã đủ sĩ số chưa
     */
    public function isClassFull($maLopHocPhan) {
        try {
            $query = "SELECT lhp.SoLuongToiDa, 
                      (SELECT COUNT(*) FROM DANG_KY_HOC dk WHERE dk.MaLopHocPhan = lhp.MaLopHocPhan) as SiSoDangKy
                      FROM LOP_HOC_PHAN lhp
                      WHERE lhp.MaLopHocPhan = :MaLopHocPhan";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaLopHocPhan", $maLopHocPhan);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $toiDa = (int)$result['SoLuongToiDa'];
                $dangKy = (int)$result['SiSoDangKy'];
                return $dangKy >= $toiDa;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error in isClassFull: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kiểm tra xem có trùng lịch với môn đã đăng ký không
     * Trùng lịch khi: cùng thứ AND (tiết bắt đầu mới <= tiết kết thúc cũ AND tiết kết thúc mới >= tiết bắt đầu cũ)
     */
    public function hasScheduleConflict($maSinhVien, $maLopHocPhanMoi, $maHocKy = null) {
        try {
            // Lấy lịch của lớp học phần mới
            $queryNew = "SELECT tkb.Thu, tkb.TietBatDau, tkb.TietKetThuc
                         FROM THOI_KHOA_BIEU tkb
                         WHERE tkb.MaLopHocPhan = :MaLopHocPhan";
            $stmtNew = $this->conn->prepare($queryNew);
            $stmtNew->bindParam(":MaLopHocPhan", $maLopHocPhanMoi);
            $stmtNew->execute();
            $newSchedule = $stmtNew->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($newSchedule)) {
                return false;
            }
            
            // Lấy lịch của các môn đã đăng ký
            $registeredSchedules = $this->getRegisteredSchedule($maSinhVien, $maHocKy);
            
            // Kiểm tra trùng lịch
            foreach ($newSchedule as $new) {
                foreach ($registeredSchedules as $registered) {
                    if ($new['Thu'] == $registered['Thu']) {
                        $newStart = (int)$new['TietBatDau'];
                        $newEnd = (int)$new['TietKetThuc'];
                        $regStart = (int)$registered['TietBatDau'];
                        $regEnd = (int)$registered['TietKetThuc'];
                        
                        // Check if time slots overlap
                        if ($newStart <= $regEnd && $newEnd >= $regStart) {
                            return true;
                        }
                    }
                }
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Error in hasScheduleConflict: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy trạng thái điểm của một đăng ký
     */
    public function getTrangThaiDiem($maDangKy) {
        try {
            $query = "SELECT TrangThaiDiem FROM {$this->table_name} WHERE MaDangKy = :MaDangKy";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaDangKy", $maDangKy);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (int)$result['TrangThaiDiem'] : 0;
        } catch (PDOException $e) {
            error_log("Error in getTrangThaiDiem: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Khóa điểm của một lớp học phần
     */
    public function khoaDiem($maLopHocPhan, $nguoiKhoa) {
        try {
            $query = "UPDATE {$this->table_name} 
                      SET TrangThaiDiem = 1, 
                          NgayKhoaDiem = NOW(), 
                          NguoiKhoaDiem = :NguoiKhoa 
                      WHERE MaLopHocPhan = :MaLopHocPhan 
                      AND TrangThaiDiem = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaLopHocPhan", $maLopHocPhan);
            $stmt->bindParam(":NguoiKhoa", $nguoiKhoa);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in khoaDiem: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mở khóa điểm (admin mới có quyền)
     */
    public function moKhoaDiem($maLopHocPhan) {
        try {
            $query = "UPDATE {$this->table_name} 
                      SET TrangThaiDiem = 0, 
                          NgayKhoaDiem = NULL, 
                          NguoiKhoaDiem = NULL 
                      WHERE MaLopHocPhan = :MaLopHocPhan 
                      AND TrangThaiDiem = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaLopHocPhan", $maLopHocPhan);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in moKhoaDiem: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Phê duyệt điểm của một lớp học phần
     */
    public function pheDuyetDiem($maLopHocPhan, $nguoiPheDuyet) {
        try {
            $query = "UPDATE {$this->table_name} 
                      SET TrangThaiDiem = 2, 
                          NgayPheDuyet = NOW(), 
                          NguoiPheDuyet = :NguoiPheDuyet 
                      WHERE MaLopHocPhan = :MaLopHocPhan 
                      AND TrangThaiDiem = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaLopHocPhan", $maLopHocPhan);
            $stmt->bindParam(":NguoiPheDuyet", $nguoiPheDuyet);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in pheDuyetDiem: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Hủy phê duyệt điểm (admin mới có quyền)
     */
    public function huyPheDuyetDiem($maLopHocPhan) {
        try {
            $query = "UPDATE {$this->table_name} 
                      SET TrangThaiDiem = 1, 
                          NgayPheDuyet = NULL, 
                          NguoiPheDuyet = NULL 
                      WHERE MaLopHocPhan = :MaLopHocPhan 
                      AND TrangThaiDiem = 2";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaLopHocPhan", $maLopHocPhan);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in huyPheDuyetDiem: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy danh sách lớp học phần với trạng thái điểm
     */
    public function getLopHocPhanWithTrangThaiDiem($maHocKy = null) {
        try {
            $query = "SELECT lhp.MaLopHocPhan, mh.TenMonHoc, hk.TenHocKy, hk.NamHoc,
                      (SELECT COUNT(*) FROM {$this->table_name} dk WHERE dk.MaLopHocPhan = lhp.MaLopHocPhan) as SiSo,
                      (SELECT COUNT(*) FROM {$this->table_name} dk WHERE dk.MaLopHocPhan = lhp.MaLopHocPhan AND dk.TrangThaiDiem = 0) as SoChuaKhoa,
                      (SELECT COUNT(*) FROM {$this->table_name} dk WHERE dk.MaLopHocPhan = lhp.MaLopHocPhan AND dk.TrangThaiDiem = 1) as SoDaKhoa,
                      (SELECT COUNT(*) FROM {$this->table_name} dk WHERE dk.MaLopHocPhan = lhp.MaLopHocPhan AND dk.TrangThaiDiem = 2) as SoDaPheDuyet,
                      (SELECT MAX(NgayKhoaDiem) FROM {$this->table_name} dk WHERE dk.MaLopHocPhan = lhp.MaLopHocPhan AND dk.TrangThaiDiem >= 1) as NgayKhoaCu,
                      (SELECT MAX(NguoiKhoaDiem) FROM {$this->table_name} dk WHERE dk.MaLopHocPhan = lhp.MaLopHocPhan AND dk.TrangThaiDiem >= 1) as NguoiKhoaCu,
                      (SELECT MAX(NgayPheDuyet) FROM {$this->table_name} dk WHERE dk.MaLopHocPhan = lhp.MaLopHocPhan AND dk.TrangThaiDiem = 2) as NgayPheDuyetCu,
                      (SELECT MAX(NguoiPheDuyet) FROM {$this->table_name} dk WHERE dk.MaLopHocPhan = lhp.MaLopHocPhan AND dk.TrangThaiDiem = 2) as NguoiPheDuyetCu
                      FROM LOP_HOC_PHAN lhp
                      LEFT JOIN MON_HOC mh ON lhp.MaMonHoc = mh.MaMonHoc
                      LEFT JOIN HOC_KY hk ON lhp.MaHocKy = hk.MaHocKy";
            
            if ($maHocKy) {
                $query .= " WHERE lhp.MaHocKy = :MaHocKy";
            }
            
            $query .= " ORDER BY hk.NamHoc DESC, hk.MaHocKy, mh.TenMonHoc";
            
            $stmt = $this->conn->prepare($query);
            if ($maHocKy) {
                $stmt->bindParam(":MaHocKy", $maHocKy);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getLopHocPhanWithTrangThaiDiem: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Kiểm tra xem điểm có được phép sửa không
     * @return int 0 = được sửa, 1 = đã khóa (chỉ admin), 2 = đã phê duyệt (chỉ admin)
     */
    public function isDiemLocked($maDangKy) {
        return $this->getTrangThaiDiem($maDangKy);
    }

    /**
     * Kiểm tra giảng viên có quyền sửa điểm không
     * @return bool
     */
    public function isEditableByGiangVien($maDangKy, $maGiangVien) {
        try {
            // Lấy trạng thái điểm
            $trangThai = $this->getTrangThaiDiem($maDangKy);
            
            // Nếu đã khóa (1) hoặc phê duyệt (2) thì giảng viên không được sửa
            if ($trangThai >= 1) {
                return false;
            }
            
            // Kiểm tra giảng viên có phải là người tạo điểm không
            $query = "SELECT ctd.NguoiNhap, lhp.MaGiangVien
                      FROM {$this->table_name} dk
                      JOIN LOP_HOC_PHAN lhp ON dk.MaLopHocPhan = lhp.MaLopHocPhan
                      LEFT JOIN CHI_TIET_DIEM ctd ON dk.MaDangKy = ctd.MaDangKy
                      WHERE dk.MaDangKy = :MaDangKy
                      LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaDangKy", $maDangKy);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result && $result['MaGiangVien'] == $maGiangVien;
        } catch (PDOException $e) {
            error_log("Error in isEditableByGiangVien: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy trạng thái điểm theo lớp học phần
     */
    public function getTrangThaiDiemByLop($maLopHocPhan) {
        try {
            // Kiểm tra xem cột TrangThaiDiem có tồn tại không
            if (!$this->columnExists('DANG_KY_HOC', 'TrangThaiDiem')) {
                return 0;
            }
            
            $query = "SELECT TrangThaiDiem FROM {$this->table_name} WHERE MaLopHocPhan = :MaLopHocPhan LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaLopHocPhan", $maLopHocPhan);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (int)$result['TrangThaiDiem'] : 0;
        } catch (PDOException $e) {
            error_log("Error in getTrangThaiDiemByLop: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Kiểm tra xem cột có tồn tại trong bảng không
     */
    private function columnExists($table, $column) {
        try {
            $query = "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS 
                      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':table', $table);
            $stmt->bindParam(':column', $column);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return isset($result['count']) && $result['count'] > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}