<?php

require_once __DIR__ . '/../config/Database.php';

class CauTrucDiemModel {
    private $conn;
    private $table_name = "CAU_TRUC_DIEM";

    public $ID;
    public $MaMonHoc;
    public $MaLoaiDiem;
    public $HeSo;
    public $MoTa;
    public $MaCauTruc;
    public $TenCauTruc;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả cấu trúc điểm
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Lấy cấu trúc điểm theo môn học (để nhập điểm theo loại CC, GK, CK...)
     */
    public function getByMaMonHoc($maMonHoc) {
        if (empty($maMonHoc)) {
            return [];
        }
        // Lấy cấu trúc điểm duy nhất theo MaLoaiDiem để tránh dữ liệu bị lặp
        $query = "SELECT ctd.ID, ctd.MaMonHoc, ctd.MaLoaiDiem, ctd.HeSo, ctd.MoTa, ld.TenLoaiDiem 
                  FROM " . $this->table_name . " ctd
                  LEFT JOIN LOAI_DIEM ld ON ctd.MaLoaiDiem = ld.MaLoaiDiem
                  WHERE ctd.MaMonHoc = :MaMonHoc 
                  GROUP BY ctd.MaMonHoc, ctd.MaLoaiDiem
                  ORDER BY ctd.MaLoaiDiem";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":MaMonHoc", $maMonHoc);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows ?: [];
    }

    // Tạo mới cấu trúc điểm
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET MaMonHoc=:MaMonHoc, MaLoaiDiem=:MaLoaiDiem, HeSo=:HeSo, MoTa=:MoTa";
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->MaMonHoc = htmlspecialchars(strip_tags($this->MaMonHoc));
        $this->MaLoaiDiem = htmlspecialchars(strip_tags($this->MaLoaiDiem));
        $this->HeSo = htmlspecialchars(strip_tags($this->HeSo));
        $this->MoTa = htmlspecialchars(strip_tags($this->MoTa));

        // bind values
        $stmt->bindParam(":MaMonHoc", $this->MaMonHoc);
        $stmt->bindParam(":MaLoaiDiem", $this->MaLoaiDiem);
        $stmt->bindParam(":HeSo", $this->HeSo);
        $stmt->bindParam(":MoTa", $this->MoTa);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
        // Lấy một cấu trúc điểm theo ID
        public function getById($id) {
            $query = "SELECT * FROM " . $this->table_name . " WHERE ID = :ID";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":ID", $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Cập nhật cấu trúc điểm
        public function update() {
            $query = "UPDATE " . $this->table_name . " SET MaMonHoc=:MaMonHoc, MaLoaiDiem=:MaLoaiDiem, HeSo=:HeSo, MoTa=:MoTa WHERE ID=:ID";
            $stmt = $this->conn->prepare($query);
            $this->ID = htmlspecialchars(strip_tags($this->ID));
            $this->MaMonHoc = htmlspecialchars(strip_tags($this->MaMonHoc));
            $this->MaLoaiDiem = htmlspecialchars(strip_tags($this->MaLoaiDiem));
            $this->HeSo = htmlspecialchars(strip_tags($this->HeSo));
            $this->MoTa = htmlspecialchars(strip_tags($this->MoTa));
            $stmt->bindParam(":ID", $this->ID);
            $stmt->bindParam(":MaMonHoc", $this->MaMonHoc);
            $stmt->bindParam(":MaLoaiDiem", $this->MaLoaiDiem);
            $stmt->bindParam(":HeSo", $this->HeSo);
            $stmt->bindParam(":MoTa", $this->MoTa);
            return $stmt->execute();
        }

        // Xóa cấu trúc điểm
        public function delete() {
            $query = "DELETE FROM " . $this->table_name . " WHERE ID = :ID";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":ID", $this->ID);
            return $stmt->execute();
        }
}

?>