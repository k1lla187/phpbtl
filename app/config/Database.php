<?php
class Database {
    private $host = 'localhost';
    private $db   = 'qldiem';    // Tên database của bạn
    private $user = 'root';      // User database
    private $pass = '';          // Mật khẩu
    private $charset = 'utf8mb4';
    
    public $conn;

    public function getConnection() {
        $this->conn = null;

        $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db . ";charset=" . $this->charset;
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->conn = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            die('Kết nối database thất bại: ' . $e->getMessage());
        }

        return $this->conn;
    }
}
?>