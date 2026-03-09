-- Migration: Thêm cột Avatar vào bảng USER để lưu trữ đường dẫn ảnh đại diện
-- Chạy file này nếu database đã tồn tại và chưa có cột Avatar
-- Nếu cột đã tồn tại, MySQL sẽ báo lỗi "Duplicate column name" - bỏ qua

USE qldiem;

ALTER TABLE `USER` ADD COLUMN Avatar VARCHAR(255) NULL COMMENT 'Đường dẫn file ảnh đại diện (vd: uploads/avatars/avt_1_xxx.jpg)';
