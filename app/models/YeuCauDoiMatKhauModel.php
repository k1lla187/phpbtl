<?php
/**
 * YeuCauDoiMatKhauModel - Quản lý yêu cầu quên mật khẩu
 * Quy trình: Người dùng gửi yêu cầu → Admin duyệt → Gửi email mật khẩu mới
 */
require_once __DIR__ . '/../core/Model.php';

class YeuCauDoiMatKhauModel extends Model {
    protected $table_name = "YEU_CAU_DOI_MAT_KHAU";
    protected $primaryKey = "ID";

    public $ID;
    public $MaUser;
    public $TenDangNhap;
    public $Email;
    public $HoTen;
    public $VaiTro;
    public $LyDo;
    public $NgayYeuCau;
    public $TrangThai;
    public $NguoiXuLy;
    public $NgayXuLy;
    public $GhiChuAdmin;

    /**
     * Tạo yêu cầu đổi mật khẩu mới
     * @param array|null $data Dữ liệu yêu cầu (nếu không truyền sẽ sử dụng thuộc tính của model)
     */
    public function create($data = null) {
        try {
            // Nếu có tham số truyền vào, gán vào thuộc tính
            if ($data !== null && is_array($data)) {
                $this->MaUser = $data['MaUser'] ?? $this->MaUser;
                $this->TenDangNhap = $data['TenDangNhap'] ?? $this->TenDangNhap;
                $this->Email = $data['Email'] ?? $this->Email;
                $this->HoTen = $data['HoTen'] ?? $this->HoTen;
                $this->VaiTro = $data['VaiTro'] ?? $this->VaiTro;
                $this->LyDo = $data['LyDo'] ?? $this->LyDo;
            }
            
            $query = "INSERT INTO {$this->table_name} 
                      (MaUser, TenDangNhap, Email, HoTen, VaiTro, LyDo, TrangThai)
                      VALUES (:MaUser, :TenDangNhap, :Email, :HoTen, :VaiTro, :LyDo, 'ChoXuLy')";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':MaUser', (int)$this->MaUser);
            $stmt->bindValue(':TenDangNhap', $this->sanitize($this->TenDangNhap));
            $stmt->bindValue(':Email', $this->sanitize($this->Email));
            $stmt->bindValue(':HoTen', $this->sanitize($this->HoTen));
            $stmt->bindValue(':VaiTro', $this->sanitize($this->VaiTro));
            $stmt->bindValue(':LyDo', $this->sanitize($this->LyDo) ?: null);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("YeuCauDoiMatKhauModel::create: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy tất cả yêu cầu đang chờ xử lý
     */
    public function getChoXuLy() {
        try {
            $query = "SELECT y.*, u.Avatar 
                      FROM {$this->table_name} y
                      LEFT JOIN USER u ON y.MaUser = u.MaUser
                      WHERE y.TrangThai = 'ChoXuLy' 
                      ORDER BY y.NgayYeuCau DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("YeuCauDoiMatKhauModel::getChoXuLy: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy tất cả yêu cầu
     */
    public function readAll() {
        try {
            $query = "SELECT y.*, u.Avatar, admin.HoTen as TenNguoiXuLy
                      FROM {$this->table_name} y
                      LEFT JOIN USER u ON y.MaUser = u.MaUser
                      LEFT JOIN USER admin ON y.NguoiXuLy = admin.MaUser
                      ORDER BY y.NgayYeuCau DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("YeuCauDoiMatKhauModel::readAll: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy yêu cầu theo ID
     */
    public function getById($id) {
        try {
            $query = "SELECT y.*, u.Avatar
                      FROM {$this->table_name} y
                      LEFT JOIN USER u ON y.MaUser = u.MaUser
                      WHERE y.ID = :ID";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':ID', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("YeuCauDoiMatKhauModel::getById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Kiểm tra yêu cầu đã tồn tại chưa (đang chờ xử lý)
     */
    public function hasRequestPending($maUser) {
        try {
            $query = "SELECT COUNT(*) as count FROM {$this->table_name} 
                      WHERE MaUser = :MaUser 
                      AND TrangThai = 'ChoXuLy'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':MaUser', (int)$maUser);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("YeuCauDoiMatKhauModel::hasRequestPending: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Duyệt yêu cầu
     */
    public function approve($id, $nguoiXuLy, $ghiChu = null) {
        try {
            $query = "UPDATE {$this->table_name} 
                      SET TrangThai = 'DaDuyet', 
                          NguoiXuLy = :NguoiXuLy, 
                          NgayXuLy = NOW(),
                          GhiChuAdmin = :GhiChuAdmin
                      WHERE ID = :ID";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':ID', (int)$id);
            $stmt->bindValue(':NguoiXuLy', (int)$nguoiXuLy);
            $stmt->bindValue(':GhiChuAdmin', $this->sanitize($ghiChu));
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("YeuCauDoiMatKhauModel::approve: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Từ chối yêu cầu
     */
    public function reject($id, $nguoiXuLy, $ghiChu = null) {
        try {
            $query = "UPDATE {$this->table_name} 
                      SET TrangThai = 'TuChoi', 
                          NguoiXuLy = :NguoiXuLy, 
                          NgayXuLy = NOW(),
                          GhiChuAdmin = :GhiChuAdmin
                      WHERE ID = :ID";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':ID', (int)$id);
            $stmt->bindValue(':NguoiXuLy', (int)$nguoiXuLy);
            $stmt->bindValue(':GhiChuAdmin', $this->sanitize($ghiChu));
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("YeuCauDoiMatKhauModel::reject: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đếm số yêu cầu đang chờ xử lý
     */
    public function countPending() {
        try {
            $query = "SELECT COUNT(*) as count FROM {$this->table_name} WHERE TrangThai = 'ChoXuLy'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['count'];
        } catch (PDOException $e) {
            error_log("YeuCauDoiMatKhauModel::countPending: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Cập nhật trạng thái yêu cầu (dùng cho HomeController)
     * @param int $id ID yêu cầu
     * @param string $trangThai Trạng thái mới (DaDuyet, TuChoi)
     * @param int|string $nguoiXuLy Người xử lý
     * @param string|null $matKhauMoi Mật khẩu mới (không lưu vào DB, chỉ để tham khảo)
     * @param string|null $ghiChu Ghi chú
     */
    public function updateStatus($id, $trangThai, $nguoiXuLy = null, $matKhauMoi = null, $ghiChu = null) {
        try {
            $query = "UPDATE {$this->table_name} 
                      SET TrangThai = :TrangThai, 
                          NguoiXuLy = :NguoiXuLy, 
                          NgayXuLy = NOW(), 
                          GhiChuAdmin = :GhiChu 
                      WHERE ID = :ID";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':ID', (int)$id);
            $stmt->bindValue(':TrangThai', $this->sanitize($trangThai));
            $stmt->bindValue(':NguoiXuLy', $nguoiXuLy);
            $stmt->bindValue(':GhiChu', $this->sanitize($ghiChu));
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("YeuCauDoiMatKhauModel::updateStatus: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa yêu cầu
     */
    public function delete($id = null) {
        try {
            $deleteId = $id ?? $this->ID;
            $query = "DELETE FROM {$this->table_name} WHERE ID = :ID";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':ID', (int)$deleteId);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("YeuCauDoiMatKhauModel::delete: " . $e->getMessage());
            return false;
        }
    }
}
