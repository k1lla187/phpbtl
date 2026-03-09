-- Migration: Bảng yêu cầu quên mật khẩu
-- Lưu các yêu cầu đổi mật khẩu từ sinh viên/giảng viên, admin xác nhận

USE qldiem;

-- Bảng YEU_CAU_DOI_MAT_KHAU: Lưu yêu cầu quên mật khẩu
CREATE TABLE IF NOT EXISTS YEU_CAU_DOI_MAT_KHAU (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    TenDangNhap VARCHAR(100) NOT NULL,
    MaNguoiDung VARCHAR(20) NOT NULL COMMENT 'Mã Sinh Viên hoặc Mã Giảng Viên',
    VaiTro ENUM('SinhVien', 'GiangVien') NOT NULL,
    NgayYeuCau DATETIME DEFAULT CURRENT_TIMESTAMP,
    TrangThai ENUM('ChoXuLy', 'DaDuyet', 'TuChoi') DEFAULT 'ChoXuLy',
    MatKhauMoi VARCHAR(255) NULL COMMENT 'Mật khẩu mới sau khi admin xác nhận',
    NguoiXuLy VARCHAR(20) NULL COMMENT 'Admin xử lý',
    NgayXuLy DATETIME NULL,
    GhiChu TEXT NULL,
    INDEX idx_tendangnhap (TenDangNhap),
    INDEX idx_trangthai (TrangThai)
);
