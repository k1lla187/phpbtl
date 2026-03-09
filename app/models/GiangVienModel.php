<?php
/**
 * GiangVienModel - Quản lý dữ liệu Giảng viên
 */
require_once __DIR__ . '/../core/Model.php';

class GiangVienModel extends Model {
    protected $table_name = "GIANG_VIEN";
    protected $primaryKey = "MaGiangVien";

    public $MaGiangVien;
    public $HoTen;
    public $NgaySinh;
    public $GioiTinh;
    public $Email;
    public $SoDienThoai;
    public $HocVi;
    public $MaKhoa;
    public $TrangThai;

    /**
     * Lấy tất cả giảng viên kèm thông tin khoa
     */
    public function readAllWithKhoa() {
        try {
            $query = "SELECT gv.*, k.TenKhoa 
                      FROM {$this->table_name} gv
                      LEFT JOIN KHOA k ON gv.MaKhoa = k.MaKhoa
                      ORDER BY gv.HoTen";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in readAllWithKhoa: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy thông tin một giảng viên theo mã
     */
    public function getById($maGiangVien) {
        try {
            $query = "SELECT * FROM {$this->table_name} WHERE MaGiangVien = :MaGiangVien";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaGiangVien", $maGiangVien);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo mới giảng viên
     */
    public function create() {
        try {
            $query = "INSERT INTO {$this->table_name} 
                      SET MaGiangVien=:MaGiangVien, HoTen=:HoTen, NgaySinh=:NgaySinh, 
                          GioiTinh=:GioiTinh, Email=:Email, SoDienThoai=:SoDienThoai, 
                          HocVi=:HocVi, MaKhoa=:MaKhoa, TrangThai=:TrangThai";
            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(":MaGiangVien", $this->sanitize($this->MaGiangVien));
            $stmt->bindValue(":HoTen", $this->sanitize($this->HoTen));
            $stmt->bindValue(":NgaySinh", $this->sanitize($this->NgaySinh));
            $stmt->bindValue(":GioiTinh", $this->sanitize($this->GioiTinh));
            $stmt->bindValue(":Email", $this->sanitize($this->Email));
            $stmt->bindValue(":SoDienThoai", $this->sanitize($this->SoDienThoai));
            $stmt->bindValue(":HocVi", $this->sanitize($this->HocVi));
            $stmt->bindValue(":MaKhoa", $this->sanitize($this->MaKhoa) ?: null);
            $stmt->bindValue(":TrangThai", $this->sanitize($this->TrangThai) ?: 'Đang làm việc');

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể thêm giảng viên. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'GiangVienModel::create');
        }
    }

    /**
     * Cập nhật thông tin giảng viên
     */
    public function update() {
        try {
            $query = "UPDATE {$this->table_name} 
                      SET HoTen=:HoTen, NgaySinh=:NgaySinh, GioiTinh=:GioiTinh, 
                          Email=:Email, SoDienThoai=:SoDienThoai, HocVi=:HocVi, 
                          MaKhoa=:MaKhoa, TrangThai=:TrangThai 
                      WHERE MaGiangVien=:MaGiangVien";
            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(":MaGiangVien", $this->sanitize($this->MaGiangVien));
            $stmt->bindValue(":HoTen", $this->sanitize($this->HoTen));
            $stmt->bindValue(":NgaySinh", $this->sanitize($this->NgaySinh));
            $stmt->bindValue(":GioiTinh", $this->sanitize($this->GioiTinh));
            $stmt->bindValue(":Email", $this->sanitize($this->Email));
            $stmt->bindValue(":SoDienThoai", $this->sanitize($this->SoDienThoai));
            $stmt->bindValue(":HocVi", $this->sanitize($this->HocVi));
            $stmt->bindValue(":MaKhoa", $this->sanitize($this->MaKhoa) ?: null);
            $stmt->bindValue(":TrangThai", $this->sanitize($this->TrangThai));

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể cập nhật giảng viên. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'GiangVienModel::update');
        }
    }

    /**
     * Xóa giảng viên
     */
    public function delete() {
        try {
            $query = "DELETE FROM {$this->table_name} WHERE MaGiangVien = :MaGiangVien";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":MaGiangVien", $this->sanitize($this->MaGiangVien));

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể xóa giảng viên. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'GiangVienModel::delete');
        }
    }

    /**
     * Tìm kiếm giảng viên
     */
    public function search($keyword) {
        try {
            $query = "SELECT gv.*, k.TenKhoa 
                      FROM {$this->table_name} gv
                      LEFT JOIN KHOA k ON gv.MaKhoa = k.MaKhoa
                      WHERE gv.HoTen LIKE :keyword 
                         OR gv.Email LIKE :keyword 
                         OR gv.MaGiangVien LIKE :keyword
                      ORDER BY gv.HoTen";
            $stmt = $this->conn->prepare($query);
            $keyword = "%" . $this->sanitize($keyword) . "%";
            $stmt->bindParam(":keyword", $keyword);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in search: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Tạo mã giảng viên tiếp theo tự động
     * Format: GV001, GV002, ...
     */
    public function generateNextId($prefix = 'GV') {
        try {
            $query = "SELECT MaGiangVien FROM {$this->table_name} 
                      WHERE MaGiangVien LIKE :prefix 
                      ORDER BY MaGiangVien DESC LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':prefix', $prefix . '%');
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $currentId = $result['MaGiangVien'];
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
}
