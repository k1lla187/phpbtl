-- Migration: Thêm cột Avatar cho USER, Xóa loại điểm Thường xuyên (TX)
-- Chạy file này nếu database đã tồn tại (chạy 1 lần)

USE qldiem;

-- 1. Thêm cột Avatar vào bảng USER
-- Nếu cột đã tồn tại sẽ báo lỗi - bỏ qua và chạy tiếp bước 2
ALTER TABLE `USER` ADD COLUMN Avatar VARCHAR(255) NULL;

-- 2. Xóa dữ liệu Thường xuyên (TX) - thứ tự quan trọng do foreign key
DELETE FROM CHI_TIET_DIEM WHERE MaLoaiDiem = 'TX';
DELETE FROM CAU_TRUC_DIEM WHERE MaLoaiDiem = 'TX';
DELETE FROM LOAI_DIEM WHERE MaLoaiDiem = 'TX';
