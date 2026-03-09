<?php
/**
 * ChiTietDiemModel - Quản lý Chi tiết điểm
 */
require_once __DIR__ . '/../core/Model.php';

class ChiTietDiemModel extends Model {
    protected $table_name = "CHI_TIET_DIEM";
    protected $primaryKey = "MaChiTiet";

    public $MaChiTiet;
    public $MaDangKy;
    public $MaLoaiDiem;
    public $SoDiem;
    public $NgayNhap;
    public $NguoiNhap;

    public function __construct($db) {
        parent::__construct($db);
    }

    /**
     * Lấy điểm theo đăng ký học
     */
    public function getByDangKy($maDangKy) {
        $query = "SELECT ctd.*, ld.TenLoaiDiem 
                  FROM {$this->table_name} ctd
                  LEFT JOIN LOAI_DIEM ld ON ctd.MaLoaiDiem = ld.MaLoaiDiem
                  WHERE ctd.MaDangKy = :maDK
                  ORDER BY ctd.MaLoaiDiem";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maDK', $maDangKy);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Tạo mới chi tiết điểm
     */
    public function create() {
        $query = "INSERT INTO {$this->table_name} 
                  (MaDangKy, MaLoaiDiem, SoDiem, NgayNhap, NguoiNhap) 
                  VALUES (:MaDangKy, :MaLoaiDiem, :SoDiem, :NgayNhap, :NguoiNhap)";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaDangKy", $this->MaDangKy);
            $stmt->bindParam(":MaLoaiDiem", $this->MaLoaiDiem);
            $stmt->bindParam(":SoDiem", $this->SoDiem);
            $stmt->bindParam(":NgayNhap", $this->NgayNhap);
            $stmt->bindParam(":NguoiNhap", $this->NguoiNhap);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return $this->handlePdoException($e);
        }
    }

    /**
     * Cập nhật chi tiết điểm
     */
    public function update() {
        $query = "UPDATE {$this->table_name} 
                  SET SoDiem=:SoDiem, NgayNhap=:NgayNhap, NguoiNhap=:NguoiNhap 
                  WHERE MaChiTiet=:MaChiTiet";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaChiTiet", $this->MaChiTiet);
            $stmt->bindParam(":SoDiem", $this->SoDiem);
            $stmt->bindParam(":NgayNhap", $this->NgayNhap);
            $stmt->bindParam(":NguoiNhap", $this->NguoiNhap);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return $this->handlePdoException($e);
        }
    }

    /**
     * Cập nhật hoặc tạo mới điểm
     */
    public function upsert() {
        // Kiểm tra đã có điểm chưa
        $query = "SELECT MaChiTiet FROM {$this->table_name} 
                  WHERE MaDangKy = :maDK AND MaLoaiDiem = :maLD";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maDK', $this->MaDangKy);
            $stmt->bindParam(':maLD', $this->MaLoaiDiem);
            $stmt->execute();
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing) {
                $this->MaChiTiet = $existing['MaChiTiet'];
                return $this->update();
            } else {
                return $this->create();
            }
        } catch (PDOException $e) {
            return $this->handlePdoException($e);
        }
    }

    /**
     * Xóa chi tiết điểm
     */
    public function delete() {
        $query = "DELETE FROM {$this->table_name} WHERE MaChiTiet = :MaChiTiet";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaChiTiet", $this->MaChiTiet);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return $this->handlePdoException($e);
        }
    }
}