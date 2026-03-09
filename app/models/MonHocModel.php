<?php
/**
 * MonHocModel - Quản lý dữ liệu Môn học
 */
require_once __DIR__ . '/../core/Model.php';

class MonHocModel extends Model {
    protected $table_name = "MON_HOC";
    protected $primaryKey = "MaMonHoc";

    public $MaMonHoc;
    public $TenMonHoc;
    public $SoTinChi;
    public $SoTietLyThuyet;
    public $SoTietThucHanh;
    public $MaNganh;

    /**
     * Lấy tất cả môn học kèm thông tin ngành
     */
    public function readAllWithNganh() {
        try {
            $query = "SELECT mh.*, n.TenNganh 
                      FROM {$this->table_name} mh
                      LEFT JOIN NGANH n ON mh.MaNganh = n.MaNganh
                      ORDER BY mh.TenMonHoc";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in readAllWithNganh: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy môn học theo ngành
     */
    public function getByMaNganh($maNganh) {
        if (empty($maNganh)) return [];
        try {
            $query = "SELECT mh.*, n.TenNganh FROM {$this->table_name} mh
                      LEFT JOIN NGANH n ON mh.MaNganh = n.MaNganh
                      WHERE mh.MaNganh = :MaNganh ORDER BY mh.TenMonHoc";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaNganh", $maNganh);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("MonHocModel::getByMaNganh: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy thông tin một môn học theo mã
     */
    public function getById($maMonHoc) {
        try {
            $query = "SELECT * FROM {$this->table_name} WHERE MaMonHoc = :MaMonHoc";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaMonHoc", $maMonHoc);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo mới môn học
     */
    public function create() {
        try {
            $query = "INSERT INTO {$this->table_name} 
                      SET MaMonHoc=:MaMonHoc, TenMonHoc=:TenMonHoc, SoTinChi=:SoTinChi, 
                          SoTietLyThuyet=:SoTietLyThuyet, SoTietThucHanh=:SoTietThucHanh, MaNganh=:MaNganh";
            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(":MaMonHoc", $this->sanitize($this->MaMonHoc));
            $stmt->bindValue(":TenMonHoc", $this->sanitize($this->TenMonHoc));
            $stmt->bindValue(":SoTinChi", (int)$this->SoTinChi ?: null);
            $stmt->bindValue(":SoTietLyThuyet", (int)$this->SoTietLyThuyet ?: null);
            $stmt->bindValue(":SoTietThucHanh", (int)$this->SoTietThucHanh ?: null);
            $stmt->bindValue(":MaNganh", $this->sanitize($this->MaNganh) ?: null);

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể thêm môn học. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'MonHocModel::create');
        }
    }

    /**
     * Cập nhật môn học
     */
    public function update() {
        try {
            $query = "UPDATE {$this->table_name} 
                      SET TenMonHoc=:TenMonHoc, SoTinChi=:SoTinChi, 
                          SoTietLyThuyet=:SoTietLyThuyet, SoTietThucHanh=:SoTietThucHanh, MaNganh=:MaNganh 
                      WHERE MaMonHoc=:MaMonHoc";
            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(":MaMonHoc", $this->sanitize($this->MaMonHoc));
            $stmt->bindValue(":TenMonHoc", $this->sanitize($this->TenMonHoc));
            $stmt->bindValue(":SoTinChi", (int)$this->SoTinChi ?: null);
            $stmt->bindValue(":SoTietLyThuyet", (int)$this->SoTietLyThuyet ?: null);
            $stmt->bindValue(":SoTietThucHanh", (int)$this->SoTietThucHanh ?: null);
            $stmt->bindValue(":MaNganh", $this->sanitize($this->MaNganh) ?: null);

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể cập nhật môn học. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'MonHocModel::update');
        }
    }

    /**
     * Xóa môn học
     */
    public function delete() {
        try {
            $query = "DELETE FROM {$this->table_name} WHERE MaMonHoc = :MaMonHoc";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":MaMonHoc", $this->sanitize($this->MaMonHoc));

            if ($stmt->execute()) {
                return true;
            }
            return "Không thể xóa môn học. Vui lòng thử lại!";
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'MonHocModel::delete');
        }
    }

    /**
     * Tạo mã môn học tiếp theo tự động
     * Format: MH001, MH002, ...
     */
    public function generateNextId($prefix = 'MH') {
        try {
            $query = "SELECT MaMonHoc FROM {$this->table_name} 
                      WHERE MaMonHoc LIKE :prefix 
                      ORDER BY MaMonHoc DESC LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':prefix', $prefix . '%');
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $currentId = $result['MaMonHoc'];
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