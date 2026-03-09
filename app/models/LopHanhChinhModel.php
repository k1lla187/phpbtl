<?php
/**
 * LopHanhChinhModel - Quản lý dữ liệu Lớp hành chính
 */
require_once __DIR__ . '/../core/Model.php';

class LopHanhChinhModel extends Model {
    protected $table_name = "LOP_HANH_CHINH";
    protected $primaryKey = "MaLop";

    public $MaLop;
    public $TenLop;
    public $MaNganh;
    public $KhoaHoc;
    public $MaCoVan;

    /**
     * Lấy tất cả lớp kèm thông tin ngành và cố vấn
     */
    public function readAllWithDetails() {
        try {
            $query = "SELECT lhc.*, n.TenNganh, gv.HoTen as TenCoVan,
                      (SELECT COUNT(*) FROM SINH_VIEN sv WHERE sv.MaLop = lhc.MaLop) as SiSo
                      FROM {$this->table_name} lhc
                      LEFT JOIN NGANH n ON lhc.MaNganh = n.MaNganh
                      LEFT JOIN GIANG_VIEN gv ON lhc.MaCoVan = gv.MaGiangVien
                      ORDER BY lhc.TenLop";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in readAllWithDetails: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy thông tin một lớp theo mã (kèm tên ngành)
     */
    public function getById($maLop) {
        try {
            $query = "SELECT lhc.*, n.TenNganh FROM {$this->table_name} lhc
                      LEFT JOIN NGANH n ON lhc.MaNganh = n.MaNganh
                      WHERE lhc.MaLop = :MaLop";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaLop", $maLop);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo mới lớp hành chính
     */
    public function create() {
        try {
            $query = "INSERT INTO {$this->table_name} 
                      SET MaLop=:MaLop, TenLop=:TenLop, MaNganh=:MaNganh, KhoaHoc=:KhoaHoc, MaCoVan=:MaCoVan";
            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(":MaLop", $this->sanitize($this->MaLop));
            $stmt->bindValue(":TenLop", $this->sanitize($this->TenLop));
            $stmt->bindValue(":MaNganh", $this->sanitize($this->MaNganh) ?: null);
            $stmt->bindValue(":KhoaHoc", $this->sanitize($this->KhoaHoc) ?: null);
            $stmt->bindValue(":MaCoVan", $this->sanitize($this->MaCoVan) ?: null);

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể thêm lớp. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'LopHanhChinhModel::create');
        }
    }

    /**
     * Cập nhật lớp hành chính
     */
    public function update() {
        try {
            $query = "UPDATE {$this->table_name} 
                      SET TenLop=:TenLop, MaNganh=:MaNganh, KhoaHoc=:KhoaHoc, MaCoVan=:MaCoVan 
                      WHERE MaLop=:MaLop";
            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(":MaLop", $this->sanitize($this->MaLop));
            $stmt->bindValue(":TenLop", $this->sanitize($this->TenLop));
            $stmt->bindValue(":MaNganh", $this->sanitize($this->MaNganh) ?: null);
            $stmt->bindValue(":KhoaHoc", $this->sanitize($this->KhoaHoc) ?: null);
            $stmt->bindValue(":MaCoVan", $this->sanitize($this->MaCoVan) ?: null);

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể cập nhật lớp. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'LopHanhChinhModel::update');
        }
    }

    /**
     * Xóa lớp hành chính
     */
    public function delete() {
        try {
            $query = "DELETE FROM {$this->table_name} WHERE MaLop = :MaLop";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":MaLop", $this->sanitize($this->MaLop));

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể xóa lớp. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'LopHanhChinhModel::delete');
        }
    }
}