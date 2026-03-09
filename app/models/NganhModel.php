<?php
/**
 * NganhModel - Quản lý dữ liệu Ngành
 */
require_once __DIR__ . '/../core/Model.php';

class NganhModel extends Model {
    protected $table_name = "NGANH";
    protected $primaryKey = "MaNganh";

    public $MaNganh;
    public $TenNganh;
    public $MaKhoa;

    /**
     * Lấy tất cả ngành kèm thông tin khoa
     */
    public function readAllWithKhoa() {
        try {
            $query = "SELECT n.*, k.TenKhoa 
                      FROM {$this->table_name} n
                      LEFT JOIN KHOA k ON n.MaKhoa = k.MaKhoa
                      ORDER BY n.TenNganh";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in readAllWithKhoa: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy thông tin một ngành theo mã
     */
    public function getById($maNganh) {
        try {
            $query = "SELECT * FROM {$this->table_name} WHERE MaNganh = :MaNganh";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaNganh", $maNganh);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo mới ngành
     */
    public function create() {
        try {
            $query = "INSERT INTO {$this->table_name} 
                      SET MaNganh=:MaNganh, TenNganh=:TenNganh, MaKhoa=:MaKhoa";
            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(":MaNganh", $this->sanitize($this->MaNganh));
            $stmt->bindValue(":TenNganh", $this->sanitize($this->TenNganh));
            $stmt->bindValue(":MaKhoa", $this->sanitize($this->MaKhoa) ?: null);

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể thêm ngành. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'NganhModel::create');
        }
    }

    /**
     * Cập nhật ngành
     */
    public function update() {
        try {
            $query = "UPDATE {$this->table_name} 
                      SET TenNganh=:TenNganh, MaKhoa=:MaKhoa 
                      WHERE MaNganh=:MaNganh";
            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(":MaNganh", $this->sanitize($this->MaNganh));
            $stmt->bindValue(":TenNganh", $this->sanitize($this->TenNganh));
            $stmt->bindValue(":MaKhoa", $this->sanitize($this->MaKhoa) ?: null);

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể cập nhật ngành. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'NganhModel::update');
        }
    }

    /**
     * Xóa ngành
     */
    public function delete() {
        try {
            $query = "DELETE FROM {$this->table_name} WHERE MaNganh = :MaNganh";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":MaNganh", $this->sanitize($this->MaNganh));

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể xóa ngành. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'NganhModel::delete');
        }
    }
}