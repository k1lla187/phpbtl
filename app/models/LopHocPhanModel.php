<?php
/**
 * LopHocPhanModel - Quản lý dữ liệu Lớp học phần
 */
require_once __DIR__ . '/../core/Model.php';

class LopHocPhanModel extends Model {
    protected $table_name = "LOP_HOC_PHAN";
    protected $primaryKey = "MaLopHocPhan";

    public $MaLopHocPhan;
    public $MaMonHoc;
    public $MaHocKy;
    public $MaGiangVien;
    public $PhongHoc;
    public $SoLuongToiDa;
    public $TrangThai;
    public $ChoPhepDangKyKhacKhoa;

    /**
     * Lấy tất cả lớp học phần kèm thông tin chi tiết
     */
    public function readAllWithDetails() {
        try {
            $query = "SELECT lhp.*, mh.TenMonHoc, gv.HoTen as TenGV, hk.TenHocKy,
                      (SELECT COUNT(*) FROM DANG_KY_HOC dk WHERE dk.MaLopHocPhan = lhp.MaLopHocPhan) as SiSo
                      FROM {$this->table_name} lhp
                      LEFT JOIN MON_HOC mh ON lhp.MaMonHoc = mh.MaMonHoc
                      LEFT JOIN GIANG_VIEN gv ON lhp.MaGiangVien = gv.MaGiangVien
                      LEFT JOIN HOC_KY hk ON lhp.MaHocKy = hk.MaHocKy
                      ORDER BY lhp.MaLopHocPhan";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in readAllWithDetails: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh sách lớp học phần do một giảng viên phụ trách (có thông tin chi tiết)
     */
    public function getByMaGiangVien($maGiangVien) {
        if (empty($maGiangVien)) {
            return [];
        }
        try {
            $query = "SELECT DISTINCT lhp.MaLopHocPhan, lhp.MaMonHoc, lhp.MaHocKy, lhp.MaGiangVien, 
                      lhp.PhongHoc, lhp.SoLuongToiDa, lhp.TrangThai,
                      mh.TenMonHoc, mh.SoTinChi, gv.HoTen as TenGV, hk.TenHocKy, hk.NamHoc,
                      (SELECT COUNT(*) FROM DANG_KY_HOC dk WHERE dk.MaLopHocPhan = lhp.MaLopHocPhan) as SiSo,
                      (SELECT GROUP_CONCAT(CONCAT('T', tkb.Thu, ' Tiết ', tkb.TietBatDau, '-', tkb.TietKetThuc) SEPARATOR ', ')
                       FROM THOI_KHOA_BIEU tkb WHERE tkb.MaLopHocPhan = lhp.MaLopHocPhan) as TietHoc,
                      (SELECT CONCAT('Thứ ', tkb.Thu) FROM THOI_KHOA_BIEU tkb WHERE tkb.MaLopHocPhan = lhp.MaLopHocPhan ORDER BY tkb.Thu, tkb.TietBatDau LIMIT 1) as Thu
                      FROM {$this->table_name} lhp
                      LEFT JOIN MON_HOC mh ON lhp.MaMonHoc = mh.MaMonHoc
                      LEFT JOIN GIANG_VIEN gv ON lhp.MaGiangVien = gv.MaGiangVien
                      LEFT JOIN HOC_KY hk ON lhp.MaHocKy = hk.MaHocKy
                      WHERE lhp.MaGiangVien = :MaGiangVien
                      ORDER BY lhp.MaLopHocPhan";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaGiangVien", $maGiangVien);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getByMaGiangVien: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy thông tin một lớp học phần theo mã (kèm thông tin môn học)
     */
    public function getById($maLopHocPhan) {
        try {
            $query = "SELECT lhp.*, mh.TenMonHoc, mh.SoTinChi 
                      FROM {$this->table_name} lhp
                      LEFT JOIN MON_HOC mh ON lhp.MaMonHoc = mh.MaMonHoc
                      WHERE lhp.MaLopHocPhan = :MaLopHocPhan";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaLopHocPhan", $maLopHocPhan);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo mới lớp học phần
     */
    public function create() {
        try {
            $query = "INSERT INTO {$this->table_name} 
                      SET MaLopHocPhan=:MaLopHocPhan, MaMonHoc=:MaMonHoc, MaHocKy=:MaHocKy, 
                          MaGiangVien=:MaGiangVien, PhongHoc=:PhongHoc, SoLuongToiDa=:SoLuongToiDa, TrangThai=:TrangThai";
            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(":MaLopHocPhan", $this->sanitize($this->MaLopHocPhan));
            $stmt->bindValue(":MaMonHoc", $this->sanitize($this->MaMonHoc) ?: null);
            $stmt->bindValue(":MaHocKy", $this->sanitize($this->MaHocKy) ?: null);
            $stmt->bindValue(":MaGiangVien", $this->sanitize($this->MaGiangVien) ?: null);
            $stmt->bindValue(":PhongHoc", $this->sanitize($this->PhongHoc) ?: null);
            $stmt->bindValue(":SoLuongToiDa", (int)($this->SoLuongToiDa ?: 60));
            $stmt->bindValue(":TrangThai", (int)($this->TrangThai ?? 1));

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể thêm lớp học phần. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'LopHocPhanModel::create');
        }
    }

    /**
     * Cập nhật lớp học phần
     */
    public function update() {
        try {
            $query = "UPDATE {$this->table_name} 
                      SET MaMonHoc=:MaMonHoc, MaHocKy=:MaHocKy, MaGiangVien=:MaGiangVien, 
                          PhongHoc=:PhongHoc, SoLuongToiDa=:SoLuongToiDa, TrangThai=:TrangThai,
                          ChoPhepDangKyKhacKhoa=:ChoPhepDangKyKhacKhoa
                      WHERE MaLopHocPhan=:MaLopHocPhan";
            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(":MaLopHocPhan", $this->sanitize($this->MaLopHocPhan));
            $stmt->bindValue(":MaMonHoc", $this->sanitize($this->MaMonHoc) ?: null);
            $stmt->bindValue(":MaHocKy", $this->sanitize($this->MaHocKy) ?: null);
            $stmt->bindValue(":MaGiangVien", $this->sanitize($this->MaGiangVien) ?: null);
            $stmt->bindValue(":PhongHoc", $this->sanitize($this->PhongHoc) ?: null);
            $stmt->bindValue(":SoLuongToiDa", (int)($this->SoLuongToiDa ?: 60));
            $stmt->bindValue(":TrangThai", (int)($this->TrangThai ?? 1));
            $stmt->bindValue(":ChoPhepDangKyKhacKhoa", (int)($this->ChoPhepDangKyKhacKhoa ?? 0));

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể cập nhật lớp học phần. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'LopHocPhanModel::update');
        }
    }

    /**
     * Xóa lớp học phần
     */
    public function delete() {
        try {
            $query = "DELETE FROM {$this->table_name} WHERE MaLopHocPhan = :MaLopHocPhan";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":MaLopHocPhan", $this->sanitize($this->MaLopHocPhan));

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể xóa lớp học phần. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'LopHocPhanModel::delete');
        }
    }

    /**
     * Tạo mã lớp học phần tiếp theo tự động
     * Format: LHP001, LHP002, ...
     */
    public function generateNextId($prefix = 'LHP') {
        try {
            $query = "SELECT MaLopHocPhan FROM {$this->table_name} 
                      WHERE MaLopHocPhan LIKE :prefix 
                      ORDER BY MaLopHocPhan DESC LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':prefix', $prefix . '%');
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $currentId = $result['MaLopHocPhan'];
                $number = intval(preg_replace('/[^0-9]/', '', $currentId));
                $nextNumber = $number + 1;
            } else {
                $nextNumber = 1;
            }
            
            return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        } catch (PDOException $e) {
            error_log("Error in generateNextId: " . $e->getMessage());
            return $prefix . '001';
        }
    }

    /**
     * Lấy danh sách lớp học phần đang mở trong học kỳ hiện tại (cho sinh viên đăng ký)
     * Bao gồm: Mã LHP, Môn học, Giảng viên, Lịch học, Sĩ số còn lại/Tối đa
     * @param string|null $maHocKy Mã học kỳ
     * @param string|null $maKhoa Mã khoa (để lọc môn cùng khoa với sinh viên)
     */
    public function getAvailableForRegistration($maHocKy = null, $maKhoa = null) {
        try {
            $sql = "SELECT lhp.MaLopHocPhan, lhp.MaMonHoc, lhp.MaHocKy, lhp.PhongHoc, lhp.SoLuongToiDa, lhp.TrangThai, lhp.ChoPhepDangKyKhacKhoa,
                    mh.TenMonHoc, mh.SoTinChi,
                    gv.MaGiangVien, gv.HoTen as TenGiangVien,
                    hk.TenHocKy, hk.NamHoc,
                    n.MaKhoa,
                    (SELECT COUNT(*) FROM DANG_KY_HOC dk WHERE dk.MaLopHocPhan = lhp.MaLopHocPhan) as SiSoDangKy
                    FROM {$this->table_name} lhp
                    LEFT JOIN MON_HOC mh ON lhp.MaMonHoc = mh.MaMonHoc
                    LEFT JOIN GIANG_VIEN gv ON lhp.MaGiangVien = gv.MaGiangVien
                    LEFT JOIN HOC_KY hk ON lhp.MaHocKy = hk.MaHocKy
                    LEFT JOIN NGANH n ON mh.MaNganh = n.MaNganh
                    WHERE lhp.TrangThai = 1";
            
            if ($maHocKy) {
                $sql .= " AND lhp.MaHocKy = :MaHocKy";
            }
            
            // Lọc theo khoa (nếu có) - sinh viên chỉ đăng ký môn cùng khoa
            // TRỪ khi lớp học phần cho phép đăng ký khác khoa
            if ($maKhoa) {
                $sql .= " AND (n.MaKhoa = :MaKhoa OR lhp.ChoPhepDangKyKhacKhoa = 1 OR mh.MaNganh = 'DG')";
            }
            
            $sql .= " ORDER BY mh.TenMonHoc, lhp.MaLopHocPhan";
            
            $stmt = $this->conn->prepare($sql);
            if ($maHocKy) {
                $stmt->bindParam(":MaHocKy", $maHocKy);
            }
            if ($maKhoa) {
                $stmt->bindParam(":MaKhoa", $maKhoa);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getAvailableForRegistration: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy lịch học của một lớp học phần
     */
    public function getSchedule($maLopHocPhan) {
        try {
            $query = "SELECT tkb.*, lhp.PhongHoc as PhongMacDinh
                      FROM THOI_KHOA_BIEU tkb
                      LEFT JOIN LOP_HOC_PHAN lhp ON tkb.MaLopHocPhan = lhp.MaLopHocPhan
                      WHERE tkb.MaLopHocPhan = :MaLopHocPhan
                      ORDER BY tkb.Thu, tkb.TietBatDau";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaLopHocPhan", $maLopHocPhan);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getSchedule: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh sách các môn học từ lớp học phần đang mở (cho dropdown lọc)
     * @param string|null $maHocKy Mã học kỳ
     * @param string|null $maKhoa Mã khoa (để lọc môn cùng khoa)
     */
    public function getMonHocForFilter($maHocKy = null, $maKhoa = null) {
        try {
            $sql = "SELECT DISTINCT mh.MaMonHoc, mh.TenMonHoc
                    FROM {$this->table_name} lhp
                    LEFT JOIN MON_HOC mh ON lhp.MaMonHoc = mh.MaMonHoc
                    LEFT JOIN NGANH n ON mh.MaNganh = n.MaNganh
                    WHERE lhp.TrangThai = 1";
            
            if ($maHocKy) {
                $sql .= " AND lhp.MaHocKy = :MaHocKy";
            }
            
            // Lọc theo khoa (nếu có)
            if ($maKhoa) {
                $sql .= " AND n.MaKhoa = :MaKhoa";
            }
            
            $sql .= " ORDER BY mh.TenMonHoc";
            
            $stmt = $this->conn->prepare($sql);
            if ($maHocKy) {
                $stmt->bindParam(":MaHocKy", $maHocKy);
            }
            if ($maKhoa) {
                $stmt->bindParam(":MaKhoa", $maKhoa);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getMonHocForFilter: " . $e->getMessage());
            return [];
        }
    }
}
