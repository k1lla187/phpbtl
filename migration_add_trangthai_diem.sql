-- ============================================================================
-- FILE MIGRATION ĐỂ THÊM CÁC CỘT TRẠNG THÁI ĐIỂM
-- ============================================================================
-- Chạy file này trong phpMyAdmin để thêm các cột cần thiết
-- Chạy trước khi sử dụng hệ thống quản lý điểm
-- ============================================================================

USE qldiem;

-- ============================================================================
-- THÊM CÁC CỘT TRẠNG THÁI ĐIỂM VÀO BẢNG DANG_KY_HOC
-- ============================================================================

-- 1. Thêm cột TrangThaiDiem (0=Mới lưu, 1=Đã khóa, 2=Đã phê duyệt)
ALTER TABLE `DANG_KY_HOC` 
ADD COLUMN `TrangThaiDiem` TINYINT DEFAULT 0 
COMMENT '0=Mới lưu, 1=Đã khóa, 2=Đã phê duyệt';

-- 2. Thêm cột NgayKhoaDiem
ALTER TABLE `DANG_KY_HOC` 
ADD COLUMN `NgayKhoaDiem` DATETIME DEFAULT NULL 
COMMENT 'Ngày khóa điểm';

-- 3. Thêm cột NguoiKhoaDiem
ALTER TABLE `DANG_KY_HOC` 
ADD COLUMN `NguoiKhoaDiem` VARCHAR(20) DEFAULT NULL 
COMMENT 'Người khóa điểm';

-- 4. Thêm cột NgayPheDuyet
ALTER TABLE `DANG_KY_HOC` 
ADD COLUMN `NgayPheDuyet` DATETIME DEFAULT NULL 
COMMENT 'Ngày phê duyệt điểm';

-- 5. Thêm cột NguoiPheDuyet
ALTER TABLE `DANG_KY_HOC` 
ADD COLUMN `NguoiPheDuyet` VARCHAR(20) DEFAULT NULL 
COMMENT 'Người phê duyệt điểm';


-- ============================================================================
-- THÊM CỘT AVATAR VÀO USER (nếu chưa có)
-- ============================================================================

ALTER TABLE `USER` 
ADD COLUMN `Avatar` VARCHAR(255) DEFAULT NULL 
COMMENT 'Đường dẫn ảnh đại diện';

ALTER TABLE `USER` 
ADD COLUMN `YeuCauDoiMatKhau` TINYINT(1) DEFAULT 0 
COMMENT 'Đánh dấu cần đổi mật khẩu';


-- ============================================================================
-- KIỂM TRA KẾT QUẢ
-- ============================================================================

-- Xem cấu trúc bảng DANG_KY_HOC
-- DESC DANG_KY_HOC;

-- Xem cấu trúc bảng USER
-- DESC USER;

SELECT 'Migration columns added successfully!' AS Status;
