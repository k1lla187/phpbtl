-- Migration: Sửa cột TrangThai từ BIT sang TINYINT để dễ quản lý

-- Thêm cột tạm thời
ALTER TABLE USER ADD COLUMN TrangThai_new TINYINT DEFAULT 1;

-- Chuyển dữ liệu từ BIT sang TINYINT
UPDATE USER SET TrangThai_new = CAST(TrangThai AS UNSIGNED);

-- Xóa cột cũ và đổi tên cột mới
ALTER TABLE USER DROP COLUMN TrangThai;
ALTER TABLE USER CHANGE COLUMN TrangThai_new TrangThai TINYINT DEFAULT 1;
