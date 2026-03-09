<?php
/**
 * KhoaModel - Quản lý dữ liệu Khoa
 */
require_once __DIR__ . '/../core/Model.php';

class KhoaModel extends Model {
    protected $table_name = "KHOA";
    protected $primaryKey = "MaKhoa";

    public $MaKhoa;
    public $TenKhoa;
    public $NgayThanhLap;
    public $TruongKhoa;

    /**
     * Lấy thông tin một khoa theo mã
     */
    public function getById($maKhoa) {
        try {
            $query = "SELECT * FROM {$this->table_name} WHERE MaKhoa = :MaKhoa";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaKhoa", $maKhoa);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo mới một khoa
     */
    public function create() {
        try {
            $query = "INSERT INTO {$this->table_name} 
                      SET MaKhoa=:MaKhoa, TenKhoa=:TenKhoa, NgayThanhLap=:NgayThanhLap, TruongKhoa=:TruongKhoa";
            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(":MaKhoa", $this->sanitize($this->MaKhoa));
            $stmt->bindValue(":TenKhoa", $this->sanitize($this->TenKhoa));
            $stmt->bindValue(":NgayThanhLap", $this->sanitize($this->NgayThanhLap) ?: null);
            $stmt->bindValue(":TruongKhoa", $this->sanitize($this->TruongKhoa) ?: null);

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể thêm khoa. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'KhoaModel::create');
        }
    }

    /**
     * Cập nhật thông tin khoa
     */
    public function update() {
        try {
            $query = "UPDATE {$this->table_name} 
                      SET TenKhoa=:TenKhoa, NgayThanhLap=:NgayThanhLap, TruongKhoa=:TruongKhoa 
                      WHERE MaKhoa=:MaKhoa";
            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(":MaKhoa", $this->sanitize($this->MaKhoa));
            $stmt->bindValue(":TenKhoa", $this->sanitize($this->TenKhoa));
            $stmt->bindValue(":NgayThanhLap", $this->sanitize($this->NgayThanhLap) ?: null);
            $stmt->bindValue(":TruongKhoa", $this->sanitize($this->TruongKhoa) ?: null);

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể cập nhật khoa. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'KhoaModel::update');
        }
    }

    /**
     * Xóa khoa
     */
    public function delete() {
        try {
            $query = "DELETE FROM {$this->table_name} WHERE MaKhoa = :MaKhoa";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":MaKhoa", $this->sanitize($this->MaKhoa));

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể xóa khoa. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'KhoaModel::delete');
        }
    }
}