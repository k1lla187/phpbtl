-- Migration: Xóa cột điểm Thực hành (TH) và thêm bảng Thời khóa biểu
-- Chạy file này nếu database đã tồn tại trước khi cập nhật

USE qldiem;

-- 1. Xóa dữ liệu Thực hành (TH) - thứ tự quan trọng do foreign key
DELETE FROM CHI_TIET_DIEM WHERE MaLoaiDiem = 'TH';
DELETE FROM CAU_TRUC_DIEM WHERE MaLoaiDiem = 'TH';
DELETE FROM LOAI_DIEM WHERE MaLoaiDiem = 'TH';

-- 2. Cập nhật cấu trúc điểm MH001 (nếu có) - thêm CC nếu chưa có, điều chỉnh hệ số
-- MH001: CC 10%, GK 30%, CK 60% (đã bỏ TH 20%)
UPDATE CAU_TRUC_DIEM SET HeSo = 0.3 WHERE MaMonHoc = 'MH001' AND MaLoaiDiem = 'GK';
UPDATE CAU_TRUC_DIEM SET HeSo = 0.6 WHERE MaMonHoc = 'MH001' AND MaLoaiDiem = 'CK';
-- Xóa TH nếu còn
DELETE FROM CAU_TRUC_DIEM WHERE MaMonHoc = 'MH001' AND MaLoaiDiem = 'TH';

-- 3. Tạo bảng THOI_KHOA_BIEU
CREATE TABLE IF NOT EXISTS THOI_KHOA_BIEU (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    MaLopHocPhan VARCHAR(20) NOT NULL,
    Thu TINYINT NOT NULL COMMENT '2=Thứ 2, 3=Thứ 3, ..., 7=Thứ 7',
    TietBatDau TINYINT NOT NULL COMMENT 'Tiết bắt đầu (1-10)',
    TietKetThuc TINYINT NOT NULL COMMENT 'Tiết kết thúc (1-10)',
    PhongHoc VARCHAR(50),
    FOREIGN KEY (MaLopHocPhan) REFERENCES LOP_HOC_PHAN(MaLopHocPhan) ON DELETE CASCADE
);

-- 4. Thêm dữ liệu mẫu thời khóa biểu (chạy nếu cần)
-- INSERT INTO THOI_KHOA_BIEU (MaLopHocPhan, Thu, TietBatDau, TietKetThuc, PhongHoc) VALUES
-- ('LHP001', 2, 1, 2, 'A101'), ('LHP001', 4, 3, 4, 'A101'),
-- ('LHP002', 3, 1, 3, 'PM02'), ('LHP003', 2, 5, 6, 'B202'),
-- ('LHP004', 5, 1, 2, 'C303'), ('LHP005', 4, 7, 9, 'D404');
