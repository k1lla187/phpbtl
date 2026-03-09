<?php
/**
 * Base Model Class
 * Cung cấp các phương thức chung cho tất cả Models
 * Bao gồm xử lý lỗi PDO chuyên nghiệp
 */
abstract class Model {
    protected $conn;
    protected $table_name;
    protected $primaryKey;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Xử lý lỗi PDO và trả về thông báo tiếng Việt
     */
    protected function handlePdoException(PDOException $e, $context = '') {
        $errorCode = $e->errorInfo[1] ?? null;
        $message = $e->getMessage();

        switch ($errorCode) {
            case 1062: // Duplicate entry
                if (strpos($message, 'PRIMARY') !== false) {
                    return "Mã này đã tồn tại trong hệ thống!";
                }
                if (strpos($message, 'Email') !== false) {
                    return "Email này đã được sử dụng!";
                }
                if (strpos($message, 'SoDienThoai') !== false) {
                    return "Số điện thoại này đã được sử dụng!";
                }
                if (strpos($message, 'TenDangNhap') !== false) {
                    return "Tên đăng nhập này đã tồn tại!";
                }
                return "Dữ liệu bị trùng lặp!";

            case 1452: // Foreign key constraint fails
                return "Dữ liệu tham chiếu không tồn tại. Vui lòng kiểm tra lại!";

            case 1451: // Cannot delete - foreign key constraint
                return "Không thể xóa vì dữ liệu này đang được sử dụng ở nơi khác!";

            case 1406: // Data too long
                return "Dữ liệu nhập vào quá dài so với quy định!";

            case 1048: // Column cannot be null
                return "Vui lòng điền đầy đủ thông tin bắt buộc!";

            case 1264: // Out of range value
                return "Giá trị số vượt quá giới hạn cho phép!";

            case 1366: // Incorrect value
                return "Dữ liệu không đúng định dạng!";

            default:
                // Log lỗi chi tiết để debug
                error_log("PDO Error in $context: Code $errorCode - " . $message);
                return "Lỗi hệ thống" . ($errorCode ? " (Mã: $errorCode)" : "") . ". Vui lòng thử lại!";
        }
    }

    /**
     * Sanitize dữ liệu đầu vào
     */
    protected function sanitize($value) {
        if ($value === null || $value === '') {
            return null;
        }
        return htmlspecialchars(strip_tags(trim($value)));
    }

    /**
     * Lấy tất cả bản ghi
     */
    public function readAll() {
        try {
            $query = "SELECT * FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in readAll: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Đếm tổng số bản ghi
     */
    public function count() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Lấy bản ghi theo ID
     */
    public function getById($id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE " . $this->primaryKey . " = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getById: " . $e->getMessage());
            return null;
        }
    }
}
