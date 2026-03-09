-- Tạo database với bảng mã hỗ trợ tiếng Việt đầy đủ
CREATE DATABASE IF NOT EXISTS qldiem CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE qldiem;

-- 1. Bảng Khoa
CREATE TABLE KHOA (
    MaKhoa VARCHAR(10) PRIMARY KEY,
    TenKhoa VARCHAR(100) NOT NULL,
    NgayThanhLap DATE,
    TruongKhoa VARCHAR(100)
);

-- 2. Bảng Ngành
CREATE TABLE NGANH (
    MaNganh VARCHAR(10) PRIMARY KEY,
    TenNganh VARCHAR(100) NOT NULL,
    MaKhoa VARCHAR(10) NOT NULL,
    FOREIGN KEY (MaKhoa) REFERENCES KHOA(MaKhoa)
);

-- 3. Bảng Giảng Viên
CREATE TABLE GIANG_VIEN (
    MaGiangVien VARCHAR(20) PRIMARY KEY,
    HoTen VARCHAR(100) NOT NULL,
    NgaySinh DATE,
    GioiTinh VARCHAR(10),
    Email VARCHAR(100),
    SoDienThoai VARCHAR(15),
    HocVi VARCHAR(50),
    MaKhoa VARCHAR(10) NOT NULL,
    TrangThai BIT DEFAULT 1,
    FOREIGN KEY (MaKhoa) REFERENCES KHOA(MaKhoa)
);

-- 4. Bảng Lớp Hành Chính
CREATE TABLE LOP_HANH_CHINH (
    MaLop VARCHAR(20) PRIMARY KEY,
    TenLop VARCHAR(100) NOT NULL,
    MaNganh VARCHAR(10) NOT NULL,
    KhoaHoc INT,
    MaCoVan VARCHAR(20),
    FOREIGN KEY (MaNganh) REFERENCES NGANH(MaNganh),
    FOREIGN KEY (MaCoVan) REFERENCES GIANG_VIEN(MaGiangVien)
);

-- 5. Bảng Sinh Viên
CREATE TABLE SINH_VIEN (
    MaSinhVien VARCHAR(20) PRIMARY KEY,
    HoTen VARCHAR(100) NOT NULL,
    NgaySinh DATE NOT NULL,
    GioiTinh VARCHAR(10),
    DiaChi VARCHAR(255),
    Email VARCHAR(100),
    SoDienThoai VARCHAR(15),
    MaLop VARCHAR(20) NOT NULL,
    TrangThaiHocTap VARCHAR(50) DEFAULT 'Đang học',
    FOREIGN KEY (MaLop) REFERENCES LOP_HANH_CHINH(MaLop)
);

-- 6. Bảng Môn Học
CREATE TABLE MON_HOC (
    MaMonHoc VARCHAR(20) PRIMARY KEY,
    TenMonHoc VARCHAR(100) NOT NULL,
    SoTinChi INT NOT NULL CHECK (SoTinChi > 0),
    SoTietLyThuyet INT DEFAULT 0,
    SoTietThucHanh INT DEFAULT 0,
    MaNganh VARCHAR(10),
    FOREIGN KEY (MaNganh) REFERENCES NGANH(MaNganh)
);

-- 7. Bảng Học Kỳ
CREATE TABLE HOC_KY (
    MaHocKy VARCHAR(10) PRIMARY KEY,
    TenHocKy VARCHAR(50),
    NamHoc INT,
    NgayBatDau DATE,
    NgayKetThuc DATE
);

-- 8. Bảng Lớp Học Phần
CREATE TABLE LOP_HOC_PHAN (
    MaLopHocPhan VARCHAR(20) PRIMARY KEY,
    MaMonHoc VARCHAR(20) NOT NULL,
    MaHocKy VARCHAR(10) NOT NULL,
    MaGiangVien VARCHAR(20) NOT NULL,
    PhongHoc VARCHAR(50),
    SoLuongToiDa INT DEFAULT 60,
    TrangThai INT DEFAULT 1,
    FOREIGN KEY (MaMonHoc) REFERENCES MON_HOC(MaMonHoc),
    FOREIGN KEY (MaHocKy) REFERENCES HOC_KY(MaHocKy),
    FOREIGN KEY (MaGiangVien) REFERENCES GIANG_VIEN(MaGiangVien)
);

-- 9. Bảng Loại Điểm
CREATE TABLE LOAI_DIEM (
    MaLoaiDiem VARCHAR(10) PRIMARY KEY,
    TenLoaiDiem VARCHAR(50) NOT NULL
);

-- 10. Bảng Cấu Trúc Điểm
-- Thay IDENTITY bằng AUTO_INCREMENT
CREATE TABLE CAU_TRUC_DIEM (
    ID INT AUTO_INCREMENT PRIMARY KEY, 
    MaMonHoc VARCHAR(20) NOT NULL,
    MaLoaiDiem VARCHAR(10) NOT NULL,
    HeSo FLOAT NOT NULL CHECK (HeSo > 0 AND HeSo <= 1),
    MoTa VARCHAR(100),
    FOREIGN KEY (MaMonHoc) REFERENCES MON_HOC(MaMonHoc),
    FOREIGN KEY (MaLoaiDiem) REFERENCES LOAI_DIEM(MaLoaiDiem)
);

-- 11. Bảng Đăng Ký Học
-- Thay IDENTITY bằng AUTO_INCREMENT và GETDATE() bằng CURRENT_TIMESTAMP
CREATE TABLE DANG_KY_HOC (
    MaDangKy INT AUTO_INCREMENT PRIMARY KEY,
    MaSinhVien VARCHAR(20) NOT NULL,
    MaLopHocPhan VARCHAR(20) NOT NULL,
    NgayDangKy DATETIME DEFAULT CURRENT_TIMESTAMP,
    DiemTongKet FLOAT,
    DiemChu VARCHAR(2),
    DiemSo FLOAT,
    KetQua BIT,
    TrangThaiDiem TINYINT DEFAULT 0 COMMENT '0=Mới lưu, 1=Đã khóa, 2=Đã phê duyệt',
    NgayKhoaDiem DATETIME DEFAULT NULL COMMENT 'Ngày khóa điểm',
    NguoiKhoaDiem VARCHAR(20) DEFAULT NULL COMMENT 'Người khóa điểm',
    NgayPheDuyet DATETIME DEFAULT NULL COMMENT 'Ngày phê duyệt điểm',
    NguoiPheDuyet VARCHAR(20) DEFAULT NULL COMMENT 'Người phê duyệt điểm',
    FOREIGN KEY (MaSinhVien) REFERENCES SINH_VIEN(MaSinhVien),
    FOREIGN KEY (MaLopHocPhan) REFERENCES LOP_HOC_PHAN(MaLopHocPhan),
    CONSTRAINT UQ_SinhVien_Lop UNIQUE (MaSinhVien, MaLopHocPhan)
);

-- 12. Bảng Thời Khóa Biểu (Lịch dạy/học)
CREATE TABLE THOI_KHOA_BIEU (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    MaLopHocPhan VARCHAR(20) NOT NULL,
    Thu TINYINT NOT NULL COMMENT '2=Thứ 2, 3=Thứ 3, ..., 7=Thứ 7',
    TietBatDau TINYINT NOT NULL COMMENT 'Tiết bắt đầu (1-10)',
    TietKetThuc TINYINT NOT NULL COMMENT 'Tiết kết thúc (1-10)',
    PhongHoc VARCHAR(50),
    FOREIGN KEY (MaLopHocPhan) REFERENCES LOP_HOC_PHAN(MaLopHocPhan) ON DELETE CASCADE
);

-- 13. Bảng Chi Tiết Điểm
CREATE TABLE CHI_TIET_DIEM (
    MaChiTiet INT AUTO_INCREMENT PRIMARY KEY,
    MaDangKy INT NOT NULL,
    MaLoaiDiem VARCHAR(10) NOT NULL,
    SoDiem DECIMAL(4, 2) CHECK (SoDiem >= 0 AND SoDiem <= 10),
    NgayNhap DATETIME DEFAULT CURRENT_TIMESTAMP,
    NguoiNhap VARCHAR(20),
    FOREIGN KEY (MaDangKy) REFERENCES DANG_KY_HOC(MaDangKy),
    FOREIGN KEY (MaLoaiDiem) REFERENCES LOAI_DIEM(MaLoaiDiem)
);

-- 14. Bảng User
-- Tên bảng USER phải đặt trong dấu ` ` vì trùng từ khóa hệ thống của MySQL
CREATE TABLE `USER` (
    MaUser INT AUTO_INCREMENT PRIMARY KEY,
    TenDangNhap VARCHAR(50) NOT NULL UNIQUE,
    MatKhau VARCHAR(255) NOT NULL,
    HoTen VARCHAR(100) NOT NULL,
    Email VARCHAR(100) UNIQUE,
    SoDienThoai VARCHAR(15),
    VaiTro VARCHAR(50) NOT NULL,
    TrangThai BIT DEFAULT 1,
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    NgayCapNhat DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    Avatar VARCHAR(255) NULL COMMENT 'Đường dẫn ảnh đại diện (vd: uploads/avatars/avt_1_xxx.jpg)'
);
USE qldiem;

-- 1. Thêm dữ liệu Bảng KHOA
INSERT INTO KHOA (MaKhoa, TenKhoa, NgayThanhLap, TruongKhoa) VALUES
('CNTT', 'Công nghệ Thông tin', '2005-08-15', 'TS. Nguyễn Văn A'),
('KT', 'Kinh tế', '2005-08-15', 'TS. Trần Thị B'),
('NN', 'Ngoại ngữ', '2006-09-05', 'ThS. Lê Văn C'),
('XD', 'Xây dựng', '2007-01-10', 'TS. Phạm Văn D'),
('DL', 'Du lịch', '2008-05-20', 'ThS. Hoàng Thị E');

-- 2. Thêm dữ liệu Bảng NGANH
INSERT INTO NGANH (MaNganh, TenNganh, MaKhoa) VALUES
('CNPM', 'Kỹ thuật Phần mềm', 'CNTT'),
('HTTT', 'Hệ thống Thông tin', 'CNTT'),
('QTKD', 'Quản trị Kinh doanh', 'KT'),
('NNA', 'Ngôn ngữ Anh', 'NN'),
('XDDD', 'Xây dựng Dân dụng', 'XD');

-- 3. Thêm dữ liệu Bảng GIANG_VIEN
INSERT INTO GIANG_VIEN (MaGiangVien, HoTen, NgaySinh, GioiTinh, Email, SoDienThoai, HocVi, MaKhoa, TrangThai) VALUES
('GV001', 'Nguyễn Văn Hùng', '1980-05-10', 'Nam', 'hungnv@uni.edu.vn', '0912345678', 'Tiến sĩ', 'CNTT', 1),
('GV002', 'Trần Thị Mai', '1985-08-20', 'Nữ', 'maitt@uni.edu.vn', '0987654321', 'Thạc sĩ', 'KT', 1),
('GV003', 'Lê Thanh Sơn', '1978-12-15', 'Nam', 'sonlt@uni.edu.vn', '0909090909', 'Tiến sĩ', 'XD', 1),
('GV004', 'Phạm Lan Anh', '1990-03-25', 'Nữ', 'anhpl@uni.edu.vn', '0911223344', 'Thạc sĩ', 'NN', 1),
('GV005', 'Hoàng Văn Nam', '1982-07-30', 'Nam', 'namhv@uni.edu.vn', '0998877665', 'Thạc sĩ', 'CNTT', 1);

-- 4. Thêm dữ liệu Bảng LOP_HANH_CHINH
INSERT INTO LOP_HANH_CHINH (MaLop, TenLop, MaNganh, KhoaHoc, MaCoVan) VALUES
('D21CNPM01', 'Đại học CNPM K21 Lớp 1', 'CNPM', 21, 'GV001'),
('D21HTTT01', 'Đại học HTTT K21 Lớp 1', 'HTTT', 21, 'GV005'),
('D22QTKD01', 'Đại học QTKD K22 Lớp 1', 'QTKD', 22, 'GV002'),
('D22NNA01', 'Đại học NNA K22 Lớp 1', 'NNA', 22, 'GV004'),
('D23XDDD01', 'Đại học XDDD K23 Lớp 1', 'XDDD', 23, 'GV003');

-- 5. Thêm dữ liệu Bảng SINH_VIEN
INSERT INTO SINH_VIEN (MaSinhVien, HoTen, NgaySinh, GioiTinh, DiaChi, Email, SoDienThoai, MaLop, TrangThaiHocTap) VALUES
('SV001', 'Nguyễn Thị Hương', '2003-01-15', 'Nữ', 'Hà Nội', 'huongnt@st.uni.edu.vn', '0345678901', 'D21CNPM01', 'Đang học'),
('SV002', 'Trần Văn Bình', '2003-05-20', 'Nam', 'Nam Định', 'binhtv@st.uni.edu.vn', '0345678902', 'D21CNPM01', 'Đang học'),
('SV003', 'Lê Thị Thu', '2004-09-10', 'Nữ', 'Hải Phòng', 'thult@st.uni.edu.vn', '0345678903', 'D22QTKD01', 'Đang học'),
('SV004', 'Phạm Minh Tuấn', '2004-12-05', 'Nam', 'Đà Nẵng', 'tuanpm@st.uni.edu.vn', '0345678904', 'D22NNA01', 'Bảo lưu'),
('SV005', 'Hoàng Văn Đức', '2005-03-30', 'Nam', 'TP.HCM', 'duchv@st.uni.edu.vn', '0345678905', 'D23XDDD01', 'Đang học');

-- 6. Thêm dữ liệu Bảng MON_HOC
INSERT INTO MON_HOC (MaMonHoc, TenMonHoc, SoTinChi, SoTietLyThuyet, SoTietThucHanh, MaNganh) VALUES
('MH001', 'Lập trình Java', 3, 30, 15, 'CNPM'),
('MH002', 'Cơ sở dữ liệu', 3, 30, 15, 'HTTT'),
('MH003', 'Kế toán đại cương', 2, 30, 0, 'QTKD'),
('MH004', 'Tiếng Anh chuyên ngành 1', 2, 30, 0, 'NNA'),
('MH005', 'Sức bền vật liệu', 3, 45, 0, 'XDDD');

-- 7. Thêm dữ liệu Bảng HOC_KY
INSERT INTO HOC_KY (MaHocKy, TenHocKy, NamHoc, NgayBatDau, NgayKetThuc) VALUES
('HK1_2324', 'Học kỳ 1 Năm 2023-2024', 2023, '2023-09-05', '2024-01-15'),
('HK2_2324', 'Học kỳ 2 Năm 2023-2024', 2023, '2024-02-15', '2024-06-30'),
('HKH_2324', 'Học kỳ Hè Năm 2023-2024', 2023, '2024-07-01', '2024-08-15'),
('HK1_2425', 'Học kỳ 1 Năm 2024-2025', 2024, '2024-09-05', '2025-01-15'),
('HK2_2425', 'Học kỳ 2 Năm 2024-2025', 2024, '2025-02-15', '2025-06-30');

-- 8. Thêm dữ liệu Bảng LOP_HOC_PHAN
INSERT INTO LOP_HOC_PHAN (MaLopHocPhan, MaMonHoc, MaHocKy, MaGiangVien, PhongHoc, SoLuongToiDa, TrangThai) VALUES
('LHP001', 'MH001', 'HK1_2324', 'GV001', 'A101', 60, 1),
('LHP002', 'MH002', 'HK1_2324', 'GV005', 'PM02', 40, 1),
('LHP003', 'MH003', 'HK1_2324', 'GV002', 'B202', 80, 1),
('LHP004', 'MH004', 'HK2_2324', 'GV004', 'C303', 30, 1),
('LHP005', 'MH005', 'HK2_2324', 'GV003', 'D404', 50, 1);

-- 9. Thêm dữ liệu Bảng LOAI_DIEM (đã xóa Thực hành - TH)
INSERT INTO LOAI_DIEM (MaLoaiDiem, TenLoaiDiem) VALUES
('CC', 'Chuyên cần'),
('GK', 'Giữa kỳ'),
('CK', 'Cuối kỳ');

-- 10. Thêm dữ liệu Bảng CAU_TRUC_DIEM (đã xóa Thực hành - TH)
-- Tất cả môn đều có cấu trúc điểm CC, GK, CK
INSERT INTO CAU_TRUC_DIEM (MaMonHoc, MaLoaiDiem, HeSo, MoTa) VALUES
('MH001', 'CC', 0.1, 'Điểm danh'),
('MH001', 'GK', 0.3, 'Thi viết giữa kỳ'),
('MH001', 'CK', 0.6, 'Thi máy cuối kỳ'),
('MH002', 'CC', 0.1, 'Điểm danh'),
('MH002', 'CK', 0.9, 'Thi viết cuối kỳ'),
('MH003', 'CC', 0.1, 'Điểm danh'),
('MH003', 'GK', 0.3, 'Thi giữa kỳ'),
('MH003', 'CK', 0.6, 'Thi cuối kỳ'),
('MH004', 'CC', 0.2, 'Chuyên cần'),
('MH004', 'CK', 0.8, 'Thi cuối kỳ'),
('MH005', 'CC', 0.1, 'Điểm danh'),
('MH005', 'GK', 0.3, 'Thi giữa kỳ'),
('MH005', 'CK', 0.6, 'Thi cuối kỳ');

-- 11. Thêm dữ liệu Bảng DANG_KY_HOC
-- Tất cả môn đều có sinh viên đăng ký; tất cả giảng viên đều có môn dạy
INSERT INTO DANG_KY_HOC (MaSinhVien, MaLopHocPhan, DiemTongKet, DiemChu, DiemSo, KetQua) VALUES
-- LHP001 (Java - GV001): SV001, SV002
('SV001', 'LHP001', 8.5, 'A', 3.7, 1),
('SV002', 'LHP001', 6.0, 'C', 2.0, 1),
-- LHP002 (CSDL - GV005): SV001, SV002
('SV001', 'LHP002', 4.0, 'D', 1.0, 1),
('SV002', 'LHP002', 7.5, 'B', 3.0, 1),
-- LHP003 (Kế toán - GV002): SV003, SV001
('SV003', 'LHP003', 9.0, 'A+', 4.0, 1),
('SV001', 'LHP003', 8.0, 'B+', 3.3, 1),
-- LHP004 (TA chuyên ngành - GV004): SV002, SV003, SV004
('SV002', 'LHP004', 7.0, 'B', 3.0, 1),
('SV003', 'LHP004', 8.5, 'A', 3.7, 1),
('SV004', 'LHP004', 6.5, 'C+', 2.3, 1),
-- LHP005 (Sức bền - GV003): SV005, SV001, SV002
('SV005', 'LHP005', NULL, NULL, NULL, NULL),
('SV001', 'LHP005', 7.5, 'B', 3.0, 1),
('SV002', 'LHP005', 5.0, 'C', 2.0, 1);

-- 12. Thêm dữ liệu Bảng THOI_KHOA_BIEU (lịch dạy/học)
INSERT INTO THOI_KHOA_BIEU (MaLopHocPhan, Thu, TietBatDau, TietKetThuc, PhongHoc) VALUES
('LHP001', 2, 1, 3, 'A101'),
('LHP002', 3, 4, 6, 'PM02'),
('LHP003', 4, 1, 3, 'B202'),
('LHP004', 5, 7, 9, 'C303'),
('LHP005', 6, 1, 4, 'D404');

-- 13. Thêm dữ liệu Bảng CHI_TIET_DIEM (không dùng TH - đã xóa)
INSERT INTO CHI_TIET_DIEM (MaDangKy, MaLoaiDiem, SoDiem, NguoiNhap) VALUES
-- MaDangKy 1: SV001-LHP001 (Java)
(1, 'CC', 10.0, 'GV001'),
(1, 'GK', 8.0, 'GV001'),
(1, 'CK', 8.5, 'GV001'),
-- MaDangKy 2: SV002-LHP001
(2, 'CC', 8.0, 'GV001'),
(2, 'GK', 5.0, 'GV001'),
(2, 'CK', 6.0, 'GV001'),
-- MaDangKy 3: SV001-LHP002 (CSDL)
(3, 'CC', 7.0, 'GV005'),
(3, 'CK', 3.5, 'GV005'),
-- MaDangKy 4: SV002-LHP002
(4, 'CC', 9.0, 'GV005'),
(4, 'CK', 7.2, 'GV005'),
-- MaDangKy 5: SV003-LHP003 (Kế toán)
(5, 'CC', 10.0, 'GV002'),
(5, 'GK', 9.0, 'GV002'),
(5, 'CK', 9.0, 'GV002'),
-- MaDangKy 6: SV001-LHP003
(6, 'CC', 9.0, 'GV002'),
(6, 'GK', 8.0, 'GV002'),
(6, 'CK', 7.5, 'GV002'),
-- MaDangKy 7,8,9: LHP004 (TA)
(7, 'CC', 8.0, 'GV004'),
(7, 'CK', 6.5, 'GV004'),
(8, 'CC', 10.0, 'GV004'),
(8, 'CK', 8.0, 'GV004'),
(9, 'CC', 7.0, 'GV004'),
(9, 'CK', 6.2, 'GV004'),
-- MaDangKy 10,11: LHP005 (Sức bền) - SV001, SV002 có điểm
(10, 'CC', 8.0, 'GV003'),
(10, 'GK', 7.0, 'GV003'),
(10, 'CK', 7.5, 'GV003'),
(11, 'CC', 9.0, 'GV003'),
(11, 'GK', 6.0, 'GV003'),
(11, 'CK', 5.0, 'GV003');

-- 14. Thêm dữ liệu Bảng USER
-- Mật khẩu demo: 123456 (plaintext cho demo)
INSERT INTO `USER` (TenDangNhap, MatKhau, HoTen, Email, SoDienThoai, VaiTro, TrangThai) VALUES
('admin', '123456', 'Quản Trị Viên', 'admin@uni.edu.vn', '0901010101', 'Admin', 1),
('gv001', '123456', 'Nguyễn Văn Hùng', 'hungnv@uni.edu.vn', '0912345678', 'GiangVien', 1),
('gv002', '123456', 'Trần Thị Mai', 'maitt@uni.edu.vn', '0987654321', 'GiangVien', 1),
('gv003', '123456', 'Lê Thanh Sơn', 'sonlt@uni.edu.vn', '0909090909', 'GiangVien', 1),
('gv004', '123456', 'Phạm Lan Anh', 'anhpl@uni.edu.vn', '0911223344', 'GiangVien', 1),
('gv005', '123456', 'Hoàng Văn Nam', 'namhv@uni.edu.vn', '0998877665', 'GiangVien', 1),
('sv001', '123456', 'Nguyễn Thị Hương', 'huongnt@st.uni.edu.vn', '0345678901', 'SinhVien', 1),
('sv002', '123456', 'Trần Văn Bình', 'binhtv@st.uni.edu.vn', '0345678902', 'SinhVien', 1),
('sv003', '123456', 'Lê Thị Thu', 'thult@st.uni.edu.vn', '0345678903', 'SinhVien', 1),
('sv004', '123456', 'Phạm Minh Tuấn', 'tuanpm@st.uni.edu.vn', '0345678904', 'SinhVien', 1),
('sv005', '123456', 'Hoàng Văn Đức', 'duchv@st.uni.edu.vn', '0345678905', 'SinhVien', 1),
('daotao', '123456', 'Phòng Đào Tạo', 'pdt@uni.edu.vn', '0902020202', 'QuanLy', 1);

-- 15. Bảng Điểm danh (1 tín = 5 ca = 15 tiết, điểm CC = % tham gia * 10)
CREATE TABLE IF NOT EXISTS DIEM_DANH (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    MaDangKy INT NOT NULL,
    MaLopHocPhan VARCHAR(20) NOT NULL,
    BuoiThu TINYINT NOT NULL COMMENT 'Buổi thứ mấy (1-5 cho 1 tín, 1-15 cho 3 tín)',
    NgayDiemDanh DATE NOT NULL,
    CoMat BIT DEFAULT 1 COMMENT '1=Có mặt, 0=Vắng',
    GhiChu VARCHAR(255) NULL,
    NguoiDiemDanh VARCHAR(20) NULL,
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (MaDangKy) REFERENCES DANG_KY_HOC(MaDangKy) ON DELETE CASCADE,
    FOREIGN KEY (MaLopHocPhan) REFERENCES LOP_HOC_PHAN(MaLopHocPhan) ON DELETE CASCADE,
    UNIQUE KEY UQ_DangKy_Buoi (MaDangKy, BuoiThu)
);

-- ========== MIGRATION: Chạy nếu database đã tồn tại ==========
-- Xóa cột điểm Thực hành (TH) và thêm bảng Thời khóa biểu:
-- 1. Xóa dữ liệu TH trước (do foreign key)
-- DELETE FROM CHI_TIET_DIEM WHERE MaLoaiDiem = 'TH';
-- DELETE FROM CAU_TRUC_DIEM WHERE MaLoaiDiem = 'TH';
-- DELETE FROM LOAI_DIEM WHERE MaLoaiDiem = 'TH';
-- 2. Tạo bảng THOI_KHOA_BIEU (chạy nếu chưa có):
-- CREATE TABLE IF NOT EXISTS THOI_KHOA_BIEU (
--     ID INT AUTO_INCREMENT PRIMARY KEY,
--     MaLopHocPhan VARCHAR(20) NOT NULL,
--     Thu TINYINT NOT NULL,
--     TietBatDau TINYINT NOT NULL,
--     TietKetThuc TINYINT NOT NULL,
--     PhongHoc VARCHAR(50),
--     FOREIGN KEY (MaLopHocPhan) REFERENCES LOP_HOC_PHAN(MaLopHocPhan) ON DELETE CASCADE
-- );

-- 16. Bảng YEU_CAU_DOI_MAT_KHAU (Quên mật khẩu)
CREATE TABLE IF NOT EXISTS YEU_CAU_DOI_MAT_KHAU (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    MaUser INT NOT NULL COMMENT 'Mã người dùng trong bảng USER',
    TenDangNhap VARCHAR(50) NOT NULL COMMENT 'Tên đăng nhập của người yêu cầu',
    Email VARCHAR(100) NOT NULL COMMENT 'Email để gửi mật khẩu mới',
    HoTen VARCHAR(100) NOT NULL COMMENT 'Họ tên người yêu cầu',
    VaiTro VARCHAR(20) NOT NULL COMMENT 'Vai trò: Admin, GiangVien, SinhVien',
    LyDo TEXT NULL COMMENT 'Lý do quên mật khẩu (tùy chọn)',
    NgayYeuCau DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian gửi yêu cầu',
    TrangThai ENUM('ChoXuLy', 'DaDuyet', 'TuChoi') DEFAULT 'ChoXuLy' COMMENT 'Trạng thái xử lý',
    NguoiXuLy INT NULL COMMENT 'MaUser của admin xử lý',
    NgayXuLy DATETIME NULL COMMENT 'Thời gian admin xử lý',
    GhiChuAdmin TEXT NULL COMMENT 'Ghi chú từ admin (lý do từ chối, ...)',
    INDEX idx_trang_thai (TrangThai),
    INDEX idx_ma_user (MaUser),
    INDEX idx_ngay_yeu_cau (NgayYeuCau),
    CONSTRAINT fk_yc_user FOREIGN KEY (MaUser) REFERENCES `USER`(MaUser) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bảng quản lý yêu cầu đổi mật khẩu';

-- 17. Bảng REMEMBER_TOKENS (Ghi nhớ đăng nhập)
CREATE TABLE IF NOT EXISTS REMEMBER_TOKENS (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    TenDangNhap VARCHAR(50) NOT NULL COMMENT 'Tên đăng nhập của user',
    Token VARCHAR(255) NOT NULL COMMENT 'Token ngẫu nhiên (hash)',
    VaiTro ENUM('Admin', 'GiangVien', 'SinhVien') NOT NULL COMMENT 'Vai trò đăng nhập',
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo token',
    NgayHetHan DATETIME NOT NULL COMMENT 'Thời gian hết hạn (30 ngày)',
    UserAgent VARCHAR(255) NULL COMMENT 'Thông tin trình duyệt (bảo mật)',
    IPAddress VARCHAR(45) NULL COMMENT 'Địa chỉ IP (bảo mật)',
    UNIQUE KEY unique_token (Token),
    INDEX idx_username (TenDangNhap),
    INDEX idx_expiry (NgayHetHan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bảng lưu token Ghi nhớ đăng nhập';

-- Thêm cột YeuCauDoiMatKhau vào bảng USER
ALTER TABLE `USER`
ADD COLUMN IF NOT EXISTS YeuCauDoiMatKhau TINYINT(1) DEFAULT 0
COMMENT 'Đánh dấu người dùng cần đổi mật khẩu khi đăng nhập';