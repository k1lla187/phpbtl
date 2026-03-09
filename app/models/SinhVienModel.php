<?php
/**
 * SinhVienModel - Quản lý dữ liệu Sinh viên
 */
require_once __DIR__ . '/../core/Model.php';

class SinhVienModel extends Model {
    protected $table_name = "SINH_VIEN";
    protected $primaryKey = "MaSinhVien";

    public $MaSinhVien;
    public $HoTen;
    public $NgaySinh;
    public $GioiTinh;
    public $DiaChi;
    public $Email;
    public $SoDienThoai;
    public $MaLop;
    public $TrangThaiHocTap;

    /**
     * Lấy tất cả sinh viên kèm thông tin lớp
     */
    public function readAllWithLop() {
        try {
            $query = "SELECT sv.*, lhc.TenLop 
                      FROM {$this->table_name} sv
                      LEFT JOIN LOP_HANH_CHINH lhc ON sv.MaLop = lhc.MaLop
                      ORDER BY sv.HoTen";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in readAllWithLop: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy thông tin một sinh viên theo mã
     */
    public function getById($maSinhVien) {
        try {
            $query = "SELECT * FROM {$this->table_name} WHERE MaSinhVien = :MaSinhVien";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaSinhVien", $maSinhVien);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy sinh viên theo mã kèm tên lớp hành chính
     */
    public function getByIdWithLop($maSinhVien) {
        try {
            $query = "SELECT sv.*, lhc.TenLop
                      FROM {$this->table_name} sv
                      LEFT JOIN LOP_HANH_CHINH lhc ON sv.MaLop = lhc.MaLop
                      WHERE sv.MaSinhVien = :MaSinhVien";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaSinhVien", $maSinhVien);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getByIdWithLop: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo mới sinh viên
     */
    public function create() {
        try {
            $query = "INSERT INTO {$this->table_name} 
                      SET MaSinhVien=:MaSinhVien, HoTen=:HoTen, NgaySinh=:NgaySinh, 
                          GioiTinh=:GioiTinh, DiaChi=:DiaChi, Email=:Email, 
                          SoDienThoai=:SoDienThoai, MaLop=:MaLop, TrangThaiHocTap=:TrangThaiHocTap";
            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(":MaSinhVien", $this->sanitize($this->MaSinhVien));
            $stmt->bindValue(":HoTen", $this->sanitize($this->HoTen));
            $stmt->bindValue(":NgaySinh", $this->sanitize($this->NgaySinh) ?: null);
            $stmt->bindValue(":GioiTinh", $this->sanitize($this->GioiTinh) ?: null);
            $stmt->bindValue(":DiaChi", $this->sanitize($this->DiaChi) ?: null);
            $stmt->bindValue(":Email", $this->sanitize($this->Email) ?: null);
            $stmt->bindValue(":SoDienThoai", $this->sanitize($this->SoDienThoai) ?: null);
            $stmt->bindValue(":MaLop", $this->sanitize($this->MaLop) ?: null);
            $stmt->bindValue(":TrangThaiHocTap", $this->sanitize($this->TrangThaiHocTap) ?: 'Đang học');

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể thêm sinh viên. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'SinhVienModel::create');
        }
    }

    /**
     * Cập nhật thông tin sinh viên
     */
    public function update() {
        try {
            $query = "UPDATE {$this->table_name} 
                      SET HoTen=:HoTen, NgaySinh=:NgaySinh, GioiTinh=:GioiTinh, 
                          DiaChi=:DiaChi, Email=:Email, SoDienThoai=:SoDienThoai, 
                          MaLop=:MaLop, TrangThaiHocTap=:TrangThaiHocTap 
                      WHERE MaSinhVien=:MaSinhVien";
            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(":MaSinhVien", $this->sanitize($this->MaSinhVien));
            $stmt->bindValue(":HoTen", $this->sanitize($this->HoTen));
            $stmt->bindValue(":NgaySinh", $this->sanitize($this->NgaySinh) ?: null);
            $stmt->bindValue(":GioiTinh", $this->sanitize($this->GioiTinh) ?: null);
            $stmt->bindValue(":DiaChi", $this->sanitize($this->DiaChi) ?: null);
            $stmt->bindValue(":Email", $this->sanitize($this->Email) ?: null);
            $stmt->bindValue(":SoDienThoai", $this->sanitize($this->SoDienThoai) ?: null);
            $stmt->bindValue(":MaLop", $this->sanitize($this->MaLop) ?: null);
            $stmt->bindValue(":TrangThaiHocTap", $this->sanitize($this->TrangThaiHocTap));

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể cập nhật sinh viên. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'SinhVienModel::update');
        }
    }

    /**
     * Xóa sinh viên
     */
    public function delete() {
        try {
            $query = "DELETE FROM {$this->table_name} WHERE MaSinhVien = :MaSinhVien";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":MaSinhVien", $this->sanitize($this->MaSinhVien));

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể xóa sinh viên. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'SinhVienModel::delete');
        }
    }

    /**
     * Tìm kiếm sinh viên
     */
    public function search($keyword) {
        try {
            $query = "SELECT sv.*, lhc.TenLop 
                      FROM {$this->table_name} sv
                      LEFT JOIN LOP_HANH_CHINH lhc ON sv.MaLop = lhc.MaLop
                      WHERE sv.HoTen LIKE :keyword 
                         OR sv.Email LIKE :keyword 
                         OR sv.MaSinhVien LIKE :keyword
                      ORDER BY sv.HoTen";
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
     * Tạo mã sinh viên tiếp theo tự động
     * Format: SV001, SV002, ...
     */
    public function generateNextId($prefix = 'SV') {
        try {
            $query = "SELECT MaSinhVien FROM {$this->table_name} 
                      WHERE MaSinhVien LIKE :prefix 
                      ORDER BY MaSinhVien DESC LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':prefix', $prefix . '%');
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                // Lấy số từ mã hiện tại (ví dụ: SV015 -> 15)
                $currentId = $result['MaSinhVien'];
                $number = intval(preg_replace('/[^0-9]/', '', $currentId));
                $nextNumber = $number + 1;
            } else {
                $nextNumber = 1;
            }
            
            // Format với padding 3 số (001, 002, ...)
            return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        } catch (PDOException $e) {
            error_log("Error in generateNextId: " . $e->getMessage());
            return $prefix . '001';
        }
    }
}