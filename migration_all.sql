-- ============================================================================
-- MIGRATION TỔNG HỢP - HỆ THỐNG QUẢN LÝ ĐIỂM UNISCORE
-- ============================================================================
-- File này chạy SAU KHI đã import file qldiem.sql
-- Thứ tự thực hiện: qldiem.sql → migration_all.sql
--
-- Nội dung:
--   1. Thêm cột trạng thái điểm vào DANG_KY_HOC
--   2. Xóa loại điểm TX và TH (không dùng nữa)
--   3. Cập nhật cấu trúc điểm (hệ số)
--   4. Thêm cột Avatar vào USER
--
-- Ngày cập nhật: 08/03/2026
-- ============================================================================

USE qldiem;

-- ============================================================================
-- PHẦN 1: THÊM CỘT TRẠNG THÁI ĐIỂM VÀO DANG_KY_HOC
-- ============================================================================

-- Thêm cột TrangThaiDiem (0=Mới lưu, 1=Đã khóa, 2=Đã phê duyệt)
ALTER TABLE `DANG_KY_HOC` 
ADD COLUMN IF NOT EXISTS `TrangThaiDiem` TINYINT DEFAULT 0 
COMMENT '0=Mới lưu, 1=Đã khóa, 2=Đã phê duyệt';

-- Thêm cột NgayKhoaDiem
ALTER TABLE `DANG_KY_HOC` 
ADD COLUMN IF NOT EXISTS `NgayKhoaDiem` DATETIME DEFAULT NULL 
COMMENT 'Ngày khóa điểm';

-- Thêm cột NguoiKhoaDiem
ALTER TABLE `DANG_KY_HOC` 
ADD COLUMN IF NOT EXISTS `NguoiKhoaDiem` VARCHAR(20) DEFAULT NULL 
COMMENT 'Người khóa điểm';

-- Thêm cột NgayPheDuyet
ALTER TABLE `DANG_KY_HOC` 
ADD COLUMN IF NOT EXISTS `NgayPheDuyet` DATETIME DEFAULT NULL 
COMMENT 'Ngày phê duyệt điểm';

-- Thêm cột NguoiPheDuyet
ALTER TABLE `DANG_KY_HOC` 
ADD COLUMN IF NOT EXISTS `NguoiPheDuyet` VARCHAR(20) DEFAULT NULL 
COMMENT 'Người phê duyệt điểm';


-- ============================================================================
-- PHẦN 2: XÓA LOẠI ĐIỂM KHÔNG SỬ DỤNG
-- ============================================================================

-- Xóa điểm Thường xuyên (TX) - theo thứ tự để tránh lỗi foreign key
DELETE FROM `CHI_TIET_DIEM` WHERE `MaLoaiDiem` = 'TX';
DELETE FROM `CAU_TRUC_DIEM` WHERE `MaLoaiDiem` = 'TX';
DELETE FROM `LOAI_DIEM` WHERE `MaLoaiDiem` = 'TX';

-- Xóa điểm Thực hành (TH)
DELETE FROM `CHI_TIET_DIEM` WHERE `MaLoaiDiem` = 'TH';
DELETE FROM `CAU_TRUC_DIEM` WHERE `MaLoaiDiem` = 'TH';
DELETE FROM `LOAI_DIEM` WHERE `MaLoaiDiem` = 'TH';


-- ============================================================================
-- PHẦN 3: CẬP NHẬT CẤU TRÚC ĐIỂM (HỆ SỐ)
-- ============================================================================
-- Cấu trúc: CC 10%, GK 30%, CK 60%

UPDATE `CAU_TRUC_DIEM` SET `HeSo` = 0.1 WHERE `MaMonHoc` = 'MH001' AND `MaLoaiDiem` = 'CC';
UPDATE `CAU_TRUC_DIEM` SET `HeSo` = 0.3 WHERE `MaMonHoc` = 'MH001' AND `MaLoaiDiem` = 'GK';
UPDATE `CAU_TRUC_DIEM` SET `HeSo` = 0.6 WHERE `MaMonHoc` = 'MH001' AND `MaLoaiDiem` = 'CK';


-- ============================================================================
-- PHẦN 4: THÊM CỘT AVATAR VÀO USER (nếu chưa có)
-- ============================================================================

ALTER TABLE `USER` 
ADD COLUMN IF NOT EXISTS `Avatar` VARCHAR(255) NULL 
COMMENT 'Đường dẫn ảnh đại diện';

ALTER TABLE `USER` 
ADD COLUMN IF NOT EXISTS `YeuCauDoiMatKhau` TINYINT(1) DEFAULT 0 
COMMENT 'Đánh dấu cần đổi mật khẩu';


-- ============================================================================
-- HOÀN TẤT MIGRATION
-- ============================================================================
-- Kiểm tra kết quả:
--   DESC DANG_KY_HOC;                  -- Kiểm tra các cột trạng thái điểm
--   SELECT * FROM LOAI_DIEM;           -- Kiểm tra TX và TH đã bị xóa
--
-- Nếu có lỗi "Duplicate column name" → Cột đã tồn tại, bỏ qua
-- ============================================================================

SELECT 'Migration completed successfully!' AS Status;
