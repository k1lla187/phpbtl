-- Migration: Thêm ON DELETE CASCADE cho tất cả các bảng liên quan
-- Để cho phép xóa sinh viên mà không bị lỗi ràng buộc

-- 1. Xóa và tạo lại foreign key trong bảng DANG_KY_HOC -> SINH_VIEN
ALTER TABLE DANG_KY_HOC 
DROP FOREIGN KEY IF EXISTS dang_ky_hoc_ibfk_1;

ALTER TABLE DANG_KY_HOC 
ADD CONSTRAINT dang_ky_hoc_ibfk_1 
FOREIGN KEY (MaSinhVien) REFERENCES SINH_VIEN(MaSinhVien) ON DELETE CASCADE;

-- 2. Xóa và tạo lại foreign key trong bảng CHI_TIET_DIEM -> DANG_KY_HOC
ALTER TABLE CHI_TIET_DIEM 
DROP FOREIGN KEY IF EXISTS chi_tiet_diem_ibfk_1;

ALTER TABLE CHI_TIET_DIEM 
ADD CONSTRAINT chi_tiet_diem_ibfk_1 
FOREIGN KEY (MaDangKy) REFERENCES DANG_KY_HOC(MaDangKy) ON DELETE CASCADE;

-- 3. Xóa và tạo lại foreign key trong bảng DIEM_DANH -> DANG_KY_HOC  
ALTER TABLE DIEM_DANH 
DROP FOREIGN KEY IF EXISTS diem_danh_ibfk_1;

ALTER TABLE DIEM_DANH 
ADD CONSTRAINT diem_danh_ibfk_1 
FOREIGN KEY (MaDangKy) REFERENCES DANG_KY_HOC(MaDangKy) ON DELETE CASCADE;

-- 4. Thêm CASCADE cho SINH_VIEN -> LOP_HANH_CHINH (nếu muốn xóa SV khi xóa lớp)
-- Lưu ý: Không nên thêm vì sẽ xóa hết SV khi xóa 1 lớp
