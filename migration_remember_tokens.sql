-- Migration: Tạo bảng REMEMBER_TOKENS cho tính năng "Ghi nhớ đăng nhập"
-- Giải pháp an toàn: Không lưu mật khẩu vào cookie, chỉ lưu token

USE qldiem;

CREATE TABLE IF NOT EXISTS `REMEMBER_TOKENS` (
  `ID` INT AUTO_INCREMENT PRIMARY KEY,
  `TenDangNhap` VARCHAR(50) NOT NULL COMMENT 'Tên đăng nhập của user',
  `Token` VARCHAR(255) NOT NULL COMMENT 'Token ngẫu nhiên (hash)',
  `VaiTro` ENUM('Admin', 'GiangVien', 'SinhVien') NOT NULL COMMENT 'Vai trò đăng nhập',
  `NgayTao` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo token',
  `NgayHetHan` DATETIME NOT NULL COMMENT 'Thời gian hết hạn (30 ngày)',
  `UserAgent` VARCHAR(255) NULL COMMENT 'Thông tin trình duyệt (bảo mật)',
  `IPAddress` VARCHAR(45) NULL COMMENT 'Địa chỉ IP (bảo mật)',
  
  UNIQUE KEY `unique_token` (`Token`),
  INDEX `idx_username` (`TenDangNhap`),
  INDEX `idx_expiry` (`NgayHetHan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bảng lưu token "Ghi nhớ đăng nhập" - An toàn, không lưu mật khẩu';
