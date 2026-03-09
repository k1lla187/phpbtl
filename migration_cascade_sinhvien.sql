-- Migration: Thêm ON DELETE CASCADE cho các bảng có foreign key đến SINH_VIEN
-- Điều này cho phép xóa sinh viên mà không bị lỗi ràng buộc

-- 1. Xóa và tạo lại foreign key trong bảng DANG_KY_HOC
ALTER TABLE DANG_KY_HOC 
DROP FOREIGN KEY dang_ky_hoc_ibfk_1;

ALTER TABLE DANG_KY_HOC 
ADD CONSTRAINT dang_ky_hoc_ibfk_1 
FOREIGN KEY (MaSinhVien) REFERENCES SINH_VIEN(MaSinhVien) ON DELETE CASCADE;

-- 2. Kiểm tra và cập nhật bảng DIEM_DANH (nếu có)
-- (Bảng DIEM_DANH liên kết qua MaDangKy, không trực tiếp qua MaSinhVien)

-- 3. Xóa các đăng ký học phần của sinh viên trước (backup)
-- DELETE FROM DANG_KY_HOC WHERE MaSinhVien = 'SV001';
