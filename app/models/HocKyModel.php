<?php
/**
 * HocKyModel - Quản lý dữ liệu Học kỳ
 */
require_once __DIR__ . '/../core/Model.php';

class HocKyModel extends Model {
    protected $table_name = "HOC_KY";
    protected $primaryKey = "MaHocKy";

    public $MaHocKy;
    public $TenHocKy;
    public $NamHoc;
    public $NgayBatDau;
    public $NgayKetThuc;

    /**
     * Lấy thông tin một học kỳ theo mã
     */
    public function getById($maHocKy) {
        try {
            $query = "SELECT * FROM {$this->table_name} WHERE MaHocKy = :MaHocKy";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaHocKy", $maHocKy);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo mới học kỳ
     */
    public function create() {
        try {
            $query = "INSERT INTO {$this->table_name} 
                      SET MaHocKy=:MaHocKy, TenHocKy=:TenHocKy, NamHoc=:NamHoc, 
                          NgayBatDau=:NgayBatDau, NgayKetThuc=:NgayKetThuc";
            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(":MaHocKy", $this->sanitize($this->MaHocKy));
            $stmt->bindValue(":TenHocKy", $this->sanitize($this->TenHocKy));
            $stmt->bindValue(":NamHoc", $this->sanitize($this->NamHoc));
            $stmt->bindValue(":NgayBatDau", $this->sanitize($this->NgayBatDau) ?: null);
            $stmt->bindValue(":NgayKetThuc", $this->sanitize($this->NgayKetThuc) ?: null);

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể thêm học kỳ. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'HocKyModel::create');
        }
    }

    /**
     * Cập nhật học kỳ
     */
    public function update() {
        try {
            $query = "UPDATE {$this->table_name} 
                      SET TenHocKy=:TenHocKy, NamHoc=:NamHoc, 
                          NgayBatDau=:NgayBatDau, NgayKetThuc=:NgayKetThuc 
                      WHERE MaHocKy=:MaHocKy";
            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(":MaHocKy", $this->sanitize($this->MaHocKy));
            $stmt->bindValue(":TenHocKy", $this->sanitize($this->TenHocKy));
            $stmt->bindValue(":NamHoc", $this->sanitize($this->NamHoc));
            $stmt->bindValue(":NgayBatDau", $this->sanitize($this->NgayBatDau) ?: null);
            $stmt->bindValue(":NgayKetThuc", $this->sanitize($this->NgayKetThuc) ?: null);

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể cập nhật học kỳ. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'HocKyModel::update');
        }
    }

    /**
     * Xóa học kỳ
     */
    public function delete() {
        try {
            $query = "DELETE FROM {$this->table_name} WHERE MaHocKy = :MaHocKy";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":MaHocKy", $this->sanitize($this->MaHocKy));

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể xóa học kỳ. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'HocKyModel::delete');
        }
    }

    /**
     * Lấy học kỳ hiện tại
     */
    public function getCurrentHocKy() {
        try {
            $query = "SELECT * FROM {$this->table_name} 
                      WHERE NgayBatDau <= CURDATE() AND NgayKetThuc >= CURDATE()
                      LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getCurrentHocKy: " . $e->getMessage());
            return null;
        }
    }
}