<?php
/**
 * RememberTokenModel - Quản lý token "Ghi nhớ đăng nhập"
 * Giải pháp an toàn: Không lưu mật khẩu, chỉ lưu token ngẫu nhiên
 */
require_once __DIR__ . '/../core/Model.php';

class RememberTokenModel extends Model {
    protected $table_name = "REMEMBER_TOKENS";
    protected $primaryKey = "ID";

    public $ID;
    public $TenDangNhap;
    public $Token;
    public $VaiTro;
    public $NgayTao;
    public $NgayHetHan;
    public $UserAgent;
    public $IPAddress;

    /**
     * Tạo token mới
     */
    public function create() {
        try {
            $query = "INSERT INTO {$this->table_name} 
                      (TenDangNhap, Token, VaiTro, NgayHetHan, UserAgent, IPAddress)
                      VALUES (:TenDangNhap, :Token, :VaiTro, :NgayHetHan, :UserAgent, :IPAddress)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':TenDangNhap', $this->sanitize($this->TenDangNhap));
            $stmt->bindValue(':Token', $this->sanitize($this->Token));
            $stmt->bindValue(':VaiTro', $this->sanitize($this->VaiTro));
            $stmt->bindValue(':NgayHetHan', $this->NgayHetHan);
            $stmt->bindValue(':UserAgent', $this->sanitize($this->UserAgent));
            $stmt->bindValue(':IPAddress', $this->sanitize($this->IPAddress));
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("RememberTokenModel::create: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tìm token theo token string
     */
    public function findByToken($token) {
        try {
            $query = "SELECT * FROM {$this->table_name} 
                      WHERE Token = :Token 
                      AND NgayHetHan > NOW()
                      LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':Token', $this->sanitize($token));
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("RememberTokenModel::findByToken: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Xóa token cũ của user
     */
    public function deleteByUsername($username) {
        try {
            $query = "DELETE FROM {$this->table_name} WHERE TenDangNhap = :TenDangNhap";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':TenDangNhap', $this->sanitize($username));
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("RememberTokenModel::deleteByUsername: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa token của user theo vai trò cụ thể
     */
    public function deleteByUsernameAndRole($username, $role) {
        try {
            $query = "DELETE FROM {$this->table_name} 
                      WHERE TenDangNhap = :TenDangNhap 
                      AND VaiTro = :VaiTro";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':TenDangNhap', $this->sanitize($username));
            $stmt->bindValue(':VaiTro', $this->sanitize($role));
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("RememberTokenModel::deleteByUsernameAndRole: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa token theo token string
     */
    public function deleteByToken($token) {
        try {
            $query = "DELETE FROM {$this->table_name} WHERE Token = :Token";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':Token', $this->sanitize($token));
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("RememberTokenModel::deleteByToken: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa token hết hạn
     */
    public function deleteExpired() {
        try {
            $query = "DELETE FROM {$this->table_name} WHERE NgayHetHan < NOW()";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("RememberTokenModel::deleteExpired: " . $e->getMessage());
            return false;
        }
    }
}
