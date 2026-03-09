<?php

require_once __DIR__ . '/../config/Database.php';

class LoaiDiemModel {
    private $conn;
    private $table_name = "LOAI_DIEM";

    public $MaLoaiDiem;
    public $TenLoaiDiem;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả các loại điểm
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy thông tin một loại điểm theo mã
    public function getById($maLoaiDiem) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE MaLoaiDiem = :MaLoaiDiem";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":MaLoaiDiem", $maLoaiDiem);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tạo mới loại điểm
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET MaLoaiDiem=:MaLoaiDiem, TenLoaiDiem=:TenLoaiDiem";
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->MaLoaiDiem = htmlspecialchars(strip_tags($this->MaLoaiDiem));
        $this->TenLoaiDiem = htmlspecialchars(strip_tags($this->TenLoaiDiem));

        // bind values
        $stmt->bindParam(":MaLoaiDiem", $this->MaLoaiDiem);
        $stmt->bindParam(":TenLoaiDiem", $this->TenLoaiDiem);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Cập nhật thông tin loại điểm
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET TenLoaiDiem=:TenLoaiDiem WHERE MaLoaiDiem=:MaLoaiDiem";
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->MaLoaiDiem = htmlspecialchars(strip_tags($this->MaLoaiDiem));
        $this->TenLoaiDiem = htmlspecialchars(strip_tags($this->TenLoaiDiem));

        // bind values
        $stmt->bindParam(":MaLoaiDiem", $this->MaLoaiDiem);
        $stmt->bindParam(":TenLoaiDiem", $this->TenLoaiDiem);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Xóa loại điểm
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE MaLoaiDiem = :MaLoaiDiem";
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->MaLoaiDiem = htmlspecialchars(strip_tags($this->MaLoaiDiem));

        // bind id
        $stmt->bindParam(":MaLoaiDiem", $this->MaLoaiDiem);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}