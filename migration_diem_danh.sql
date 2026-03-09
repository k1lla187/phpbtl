-- Migration: Bảng điểm danh và điểm chuyên cần
-- 1 tín chỉ = 5 ca = 15 tiết. Điểm chuyên cần = % tham gia buổi học * 10
-- Giới hạn buổi = SoTinChi * 5 + 3 (thêm 3 buổi học bù)

USE qldiem;

-- Bảng DIEM_DANH: Lưu chi tiết điểm danh từng buổi
-- Mỗi buổi học = 1 ca = 3 tiết. Buổi thứ N trong môn (1 tín = 5 buổi + 3 buổi học bù)
-- Ví dụ: 1 tín chỉ = 8 buổi (5+3), 2 tín = 13 buổi (10+3), 3 tín = 18 buổi (15+3)
CREATE TABLE IF NOT EXISTS DIEM_DANH (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    MaDangKy INT NOT NULL,
    MaLopHocPhan VARCHAR(20) NOT NULL,
    BuoiThu TINYINT NOT NULL COMMENT 'Buổi thứ mấy (SoTinChi * 5 + 3)',
    NgayDiemDanh DATE NOT NULL,
    CoMat BIT DEFAULT 1 COMMENT '1=Có mặt, 0=Vắng',
    GhiChu VARCHAR(255) NULL,
    NguoiDiemDanh VARCHAR(20) NULL,
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (MaDangKy) REFERENCES DANG_KY_HOC(MaDangKy) ON DELETE CASCADE,
    FOREIGN KEY (MaLopHocPhan) REFERENCES LOP_HOC_PHAN(MaLopHocPhan) ON DELETE CASCADE,
    UNIQUE KEY UQ_DangKy_Buoi (MaDangKy, BuoiThu)
);
