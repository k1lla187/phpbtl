-- ============================================================================
-- FILE SQL ĐẦY ĐỦ CHO INFINITY FREE
-- ============================================================================
-- Chạy file này TRỰC TIẾP trong phpMyAdmin của InfinityFree
-- File này sẽ: Tạo bảng + Thêm dữ liệu mẫu
-- ============================================================================

-- ============================================================================
-- 1. TẠO CÁC BẢNG (nếu chưa tồn tại)
-- ============================================================================

-- Bảng Khoa
CREATE TABLE IF NOT EXISTS KHOA (
    MaKhoa VARCHAR(10) PRIMARY KEY,
    TenKhoa VARCHAR(100) NOT NULL,
    NgayThanhLap DATE,
    TruongKhoa VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng Ngành
CREATE TABLE IF NOT EXISTS NGANH (
    MaNganh VARCHAR(10) PRIMARY KEY,
    TenNganh VARCHAR(100) NOT NULL,
    MaKhoa VARCHAR(10),
    FOREIGN KEY (MaKhoa) REFERENCES KHOA(MaKhoa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng Giảng viên
CREATE TABLE IF NOT EXISTS GIANG_VIEN (
    MaGiangVien VARCHAR(20) PRIMARY KEY,
    HoTen VARCHAR(100) NOT NULL,
    NgaySinh DATE,
    GioiTinh VARCHAR(10),
    Email VARCHAR(100),
    SoDienThoai VARCHAR(15),
    HocVi VARCHAR(50),
    MaKhoa VARCHAR(10),
    TrangThai TINYINT DEFAULT 1,
    FOREIGN KEY (MaKhoa) REFERENCES KHOA(MaKhoa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng Lớp hành chính
CREATE TABLE IF NOT EXISTS LOP_HANH_CHINH (
    MaLop VARCHAR(20) PRIMARY KEY,
    TenLop VARCHAR(100) NOT NULL,
    MaNganh VARCHAR(10) NOT NULL,
    KhoaHoc INT,
    MaCoVan VARCHAR(20),
    FOREIGN KEY (MaNganh) REFERENCES NGANH(MaNganh),
    FOREIGN KEY (MaCoVan) REFERENCES GIANG_VIEN(MaGiangVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng Sinh viên
CREATE TABLE IF NOT EXISTS SINH_VIEN (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng Môn học
CREATE TABLE IF NOT EXISTS MON_HOC (
    MaMonHoc VARCHAR(20) PRIMARY KEY,
    TenMonHoc VARCHAR(100) NOT NULL,
    SoTinChi INT NOT NULL CHECK (SoTinChi > 0),
    SoTietLyThuyet INT DEFAULT 0,
    SoTietThucHanh INT DEFAULT 0,
    MaNganh VARCHAR(10),
    FOREIGN KEY (MaNganh) REFERENCES NGANH(MaNganh)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng Học kỳ
CREATE TABLE IF NOT EXISTS HOC_KY (
    MaHocKy VARCHAR(20) PRIMARY KEY,
    TenHocKy VARCHAR(50) NOT NULL,
    NamHoc INT,
    NgayBatDau DATE,
    NgayKetThuc DATE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng Lớp học phần
CREATE TABLE IF NOT EXISTS LOP_HOC_PHAN (
    MaLopHocPhan VARCHAR(20) PRIMARY KEY,
    MaMonHoc VARCHAR(20),
    MaHocKy VARCHAR(20),
    MaGiangVien VARCHAR(20),
    PhongHoc VARCHAR(20),
    SoLuongToiDa INT DEFAULT 60,
    TrangThai VARCHAR(50) DEFAULT 'Đang mở',
    ChoPhepDangKyKhacKhoa TINYINT(1) DEFAULT 0,
    FOREIGN KEY (MaMonHoc) REFERENCES MON_HOC(MaMonHoc),
    FOREIGN KEY (MaHocKy) REFERENCES HOC_KY(MaHocKy),
    FOREIGN KEY (MaGiangVien) REFERENCES GIANG_VIEN(MaGiangVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng Loại điểm
CREATE TABLE IF NOT EXISTS LOAI_DIEM (
    MaLoaiDiem VARCHAR(10) PRIMARY KEY,
    TenLoaiDiem VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng Cấu trúc điểm
CREATE TABLE IF NOT EXISTS CAU_TRUC_DIEM (
    id INT AUTO_INCREMENT PRIMARY KEY,
    MaMonHoc VARCHAR(20),
    MaLoaiDiem VARCHAR(10),
    HeSo DECIMAL(3,2) DEFAULT 1,
    MoTa VARCHAR(100),
    FOREIGN KEY (MaMonHoc) REFERENCES MON_HOC(MaMonHoc),
    FOREIGN KEY (MaLoaiDiem) REFERENCES LOAI_DIEM(MaLoaiDiem)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng Đăng ký học
CREATE TABLE IF NOT EXISTS DANG_KY_HOC (
    MaDangKy INT AUTO_INCREMENT PRIMARY KEY,
    MaSinhVien VARCHAR(20),
    MaLopHocPhan VARCHAR(20),
    NgayDangKy DATETIME DEFAULT CURRENT_TIMESTAMP,
    TrangThaiDiem TINYINT DEFAULT 0,
    FOREIGN KEY (MaSinhVien) REFERENCES SINH_VIEN(MaSinhVien),
    FOREIGN KEY (MaLopHocPhan) REFERENCES LOP_HOC_PHAN(MaLopHocPhan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng Thời khóa biểu
CREATE TABLE IF NOT EXISTS THOI_KHOA_BIEU (
    id INT AUTO_INCREMENT PRIMARY KEY,
    MaLopHocPhan VARCHAR(20),
    Thu INT,
    TietBatDau INT,
    TietKetThuc INT,
    PhongHoc VARCHAR(20),
    FOREIGN KEY (MaLopHocPhan) REFERENCES LOP_HOC_PHAN(MaLopHocPhan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng Điểm danh
CREATE TABLE IF NOT EXISTS DIEM_DANH (
    id INT AUTO_INCREMENT PRIMARY KEY,
    MaDangKy INT,
    BuoiThu INT,
    NgayDiemDanh DATE,
    TrangThai INT DEFAULT 1,
    GhiChu VARCHAR(255),
    FOREIGN KEY (MaDangKy) REFERENCES DANG_KY_HOC(MaDangKy)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng Chi tiết điểm
CREATE TABLE IF NOT EXISTS CHI_TIET_DIEM (
    id INT AUTO_INCREMENT PRIMARY KEY,
    MaDangKy INT,
    MaLoaiDiem VARCHAR(10),
    SoDiem DECIMAL(5,2),
    NgayNhap DATETIME,
    NguoiNhap VARCHAR(50),
    FOREIGN KEY (MaDangKy) REFERENCES DANG_KY_HOC(MaDangKy),
    FOREIGN KEY (MaLoaiDiem) REFERENCES LOAI_DIEM(MaLoaiDiem)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng User
CREATE TABLE IF NOT EXISTS `USER` (
    TenDangNhap VARCHAR(50) PRIMARY KEY,
    MatKhau VARCHAR(255) NOT NULL,
    HoTen VARCHAR(100),
    Email VARCHAR(100),
    SoDienThoai VARCHAR(15),
    VaiTro VARCHAR(20) DEFAULT 'SinhVien',
    TrangThai INT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 2. THÊM DỮ LIỆU KHOA
-- ============================================================================
INSERT IGNORE INTO KHOA (MaKhoa, TenKhoa, NgayThanhLap, TruongKhoa) VALUES
('CNTT', 'Công nghệ Thông tin', '2005-08-15', 'TS. Nguyễn Văn A'),
('KT', 'Kinh tế', '2005-08-15', 'TS. Trần Thị B'),
('NN', 'Ngoại ngữ', '2006-09-05', 'ThS. Lê Văn C'),
('XD', 'Xây dựng', '2007-01-10', 'TS. Phạm Văn D'),
('DL', 'Du lịch', '2008-05-20', 'ThS. Hoàng Thị E'),
('DG', 'Đại cương', '2005-01-01', 'TS. Lê Minh Đức');


-- ============================================================================
-- 3. THÊM DỮ LIỆU NGÀNH
-- ============================================================================
INSERT IGNORE INTO NGANH (MaNganh, TenNganh, MaKhoa) VALUES
-- CNTT
('CNPM', 'Kỹ thuật Phần mềm', 'CNTT'),
('HTTT', 'Hệ thống Thông tin', 'CNTT'),
('ATTT', 'An toàn Thông tin', 'CNTT'),
-- Kinh tế
('QTKD', 'Quản trị Kinh doanh', 'KT'),
('KTPM', 'Kế toán Doanh nghiệp', 'KT'),
('TCNH', 'Tài chính Ngân hàng', 'KT'),
-- Ngoại ngữ
('NNA', 'Ngôn ngữ Anh', 'NN'),
('NNP', 'Ngôn ngữ Pháp', 'NN'),
('NNT', 'Ngôn ngữ Trung Quốc', 'NN'),
-- Xây dựng
('XDDD', 'Xây dựng Dân dụng', 'XD'),
('XDCN', 'Xây dựng Công nghiệp', 'XD'),
-- Du lịch
('DLKS', 'Du lịch Khách sạn', 'DL'),
('QLDL', 'Quản lý Du lịch', 'DL');


-- ============================================================================
-- 4. THÊM DỮ LIỆU GIẢNG VIÊN
-- ============================================================================
INSERT IGNORE INTO GIANG_VIEN (MaGiangVien, HoTen, NgaySinh, GioiTinh, Email, SoDienThoai, HocVi, MaKhoa, TrangThai) VALUES
('GV001', 'Nguyễn Văn Hùng', '1980-05-10', 'Nam', 'hungnv@uni.edu.vn', '0912345678', 'Tiến sĩ', 'CNTT', 1),
('GV002', 'Trần Thị Mai', '1985-08-20', 'Nữ', 'maitt@uni.edu.vn', '0987654321', 'Thạc sĩ', 'KT', 1),
('GV003', 'Lê Thanh Sơn', '1978-12-15', 'Nam', 'sonlt@uni.edu.vn', '0909090909', 'Tiến sĩ', 'XD', 1),
('GV004', 'Phạm Lan Anh', '1990-03-25', 'Nữ', 'anhpl@uni.edu.vn', '0911223344', 'Thạc sĩ', 'NN', 1),
('GV005', 'Hoàng Văn Nam', '1982-07-30', 'Nam', 'namhv@uni.edu.vn', '0998877665', 'Thạc sĩ', 'CNTT', 1);


-- ============================================================================
-- 5. THÊM DỮ LIỆU LỚP HÀNH CHÍNH
-- ============================================================================
INSERT IGNORE INTO LOP_HANH_CHINH (MaLop, TenLop, MaNganh, KhoaHoc, MaCoVan) VALUES
('D21CNPM01', 'Đại học CNPM K21 Lớp 1', 'CNPM', 21, 'GV001'),
('D21HTTT01', 'Đại học HTTT K21 Lớp 1', 'HTTT', 21, 'GV005'),
('D22QTKD01', 'Đại học QTKD K22 Lớp 1', 'QTKD', 22, 'GV002'),
('D22NNA01', 'Đại học NNA K22 Lớp 1', 'NNA', 22, 'GV004'),
('D23XDDD01', 'Đại học XDDD K23 Lớp 1', 'XDDD', 23, 'GV003');


-- ============================================================================
-- 6. THÊM DỮ LIỆU SINH VIÊN
-- ============================================================================
INSERT IGNORE INTO SINH_VIEN (MaSinhVien, HoTen, NgaySinh, GioiTinh, DiaChi, Email, SoDienThoai, MaLop, TrangThaiHocTap) VALUES
('SV001', 'Nguyễn Thị Hương', '2003-01-15', 'Nữ', 'Hà Nội', 'huongnt@st.uni.edu.vn', '0345678901', 'D21CNPM01', 'Đang học'),
('SV002', 'Trần Văn Bình', '2003-05-20', 'Nam', 'Nam Định', 'binhtv@st.uni.edu.vn', '0345678902', 'D21CNPM01', 'Đang học'),
('SV003', 'Lê Thị Thu', '2004-09-10', 'Nữ', 'Hải Phòng', 'thult@st.uni.edu.vn', '0345678903', 'D22QTKD01', 'Đang học'),
('SV004', 'Phạm Minh Tuấn', '2004-12-05', 'Nam', 'Đà Nẵng', 'tuanpm@st.uni.edu.vn', '0345678904', 'D22NNA01', 'Bảo lưu'),
('SV005', 'Hoàng Văn Đức', '2005-03-30', 'Nam', 'TP.HCM', 'duchv@st.uni.edu.vn', '0345678905', 'D23XDDD01', 'Đang học');


-- ============================================================================
-- 7. THÊM DỮ LIỆU MÔN HỌC (bao gồm môn đại cương)
-- ============================================================================
INSERT IGNORE INTO MON_HOC (MaMonHoc, TenMonHoc, SoTinChi, SoTietLyThuyet, SoTietThucHanh, MaNganh) VALUES
-- CNTT - CNPM
('CNPM01', 'Lập trình Python cơ bản', 3, 30, 15, 'CNPM'),
('CNPM02', 'Lập trình Java', 3, 30, 15, 'CNPM'),
('CNPM03', 'Cơ sở dữ liệu', 3, 30, 15, 'CNPM'),
('CNPM04', 'Phân tích thiết kế hệ thống', 3, 45, 0, 'CNPM'),
('CNPM05', 'Kiểm thử phần mềm', 2, 20, 10, 'CNPM'),
-- CNTT - HTTT
('HTTT01', 'Phân tích thiết kế hệ thống thông tin', 3, 45, 0, 'HTTT'),
('HTTT02', 'Quản trị CSDL', 3, 30, 15, 'HTTT'),
('HTTT03', 'Kho dữ liệu và Business Intelligence', 3, 30, 15, 'HTTT'),
('HTTT04', 'An ninh mạng', 3, 30, 15, 'HTTT'),
('HTTT05', 'Thương mại điện tử', 2, 20, 10, 'HTTT'),
-- Kinh tế - QTKD
('QTKD01', 'Nguyên lý quản trị', 3, 45, 0, 'QTKD'),
('QTKD02', 'Marketing căn bản', 3, 30, 15, 'QTKD'),
('QTKD03', 'Quản trị nhân sự', 3, 45, 0, 'QTKD'),
('QTKD04', 'Tài chính doanh nghiệp', 3, 45, 0, 'QTKD'),
('QTKD05', 'Chiến lược kinh doanh', 3, 45, 0, 'QTKD'),
-- Ngoại ngữ - NNA
('NNA01', 'Tiếng Anh 1', 3, 30, 15, 'NNA'),
('NNA02', 'Tiếng Anh 2', 3, 30, 15, 'NNA'),
('NNA03', 'Tiếng Anh 3', 3, 30, 15, 'NNA'),
('NNA04', 'Tiếng Anh chuyên ngành', 3, 30, 15, 'NNA'),
('NNA05', 'Biên phiên dịch', 3, 20, 20, 'NNA'),
-- Xây dựng - XDDD
('XDDD01', 'Sức bền vật liệu', 3, 45, 0, 'XDDD'),
('XDDD02', 'Kết cấu bê tông cốt thép', 3, 45, 0, 'XDDD'),
('XDDD03', 'Kết cấu thép', 3, 45, 0, 'XDDD'),
('XDDD04', 'Nền móng công trình', 3, 45, 0, 'XDDD'),
('XDDD05', 'Thiết kế kiến trúc', 3, 30, 15, 'XDDD'),
-- Môn ĐẠI CƯƠNG (DG)
('DG001', 'Toán cao cấp 1', 3, 45, 0, 'DG'),
('DG002', 'Toán cao cấp 2', 3, 45, 0, 'DG'),
('DG003', 'Giải tích', 3, 45, 0, 'DG'),
('DG004', 'Xác suất Thống kê', 3, 45, 0, 'DG'),
('DG005', 'Vật lý đại cương', 3, 45, 0, 'DG'),
('DG006', 'Hóa học đại cương', 2, 30, 0, 'DG'),
('DG007', 'Tin học đại cương', 3, 30, 15, 'DG'),
('DG008', 'Tiếng Anh 1', 3, 30, 15, 'DG'),
('DG009', 'Tiếng Anh 2', 3, 30, 15, 'DG'),
('DG010', 'Tiếng Anh 3', 3, 30, 15, 'DG'),
('DG011', 'Giáo dục thể chất 1', 1, 0, 30, 'DG'),
('DG012', 'Giáo dục thể chất 2', 1, 0, 30, 'DG'),
('DG013', 'Giáo dục quốc phòng', 3, 30, 15, 'DG'),
('DG014', 'Pháp luật đại cương', 2, 30, 0, 'DG'),
('DG015', 'Tư tưởng Hồ Chí Minh', 3, 45, 0, 'DG');


-- ============================================================================
-- 8. THÊM DỮ LIỆU HỌC KỲ
-- ============================================================================
INSERT IGNORE INTO HOC_KY (MaHocKy, TenHocKy, NamHoc, NgayBatDau, NgayKetThuc) VALUES
('HK1_2324', 'Học kỳ 1 Năm 2023-2024', 2023, '2023-09-05', '2024-01-15'),
('HK2_2324', 'Học kỳ 2 Năm 2023-2024', 2023, '2024-02-15', '2024-06-30'),
('HKH_2324', 'Học kỳ Hè Năm 2023-2024', 2023, '2024-07-01', '2024-08-15'),
('HK1_2425', 'Học kỳ 1 Năm 2024-2025', 2024, '2024-09-05', '2025-01-15'),
('HK2_2425', 'Học kỳ 2 Năm 2024-2025', 2024, '2025-02-15', '2025-06-30');


-- ============================================================================
-- 9. THÊM DỮ LIỆU LỚP HỌC PHẦN
-- ============================================================================
INSERT IGNORE INTO LOP_HOC_PHAN (MaLopHocPhan, MaMonHoc, MaHocKy, MaGiangVien, PhongHoc, SoLuongToiDa, TrangThai, ChoPhepDangKyKhacKhoa) VALUES
-- CNTT
('LHP001', 'CNPM01', 'HK1_2425', 'GV001', 'A101', 60, 'Đang mở', 0),
('LHP002', 'CNPM02', 'HK1_2425', 'GV001', 'A102', 60, 'Đang mở', 0),
('LHP003', 'CNPM03', 'HK1_2425', 'GV005', 'PM01', 40, 'Đang mở', 0),
-- Kinh tế
('LHP004', 'QTKD01', 'HK1_2425', 'GV002', 'B201', 80, 'Đang mở', 0),
('LHP005', 'QTKD02', 'HK1_2425', 'GV002', 'B202', 80, 'Đang mở', 0),
-- Đại cương (CHO PHÉP tất cả SV đăng ký)
('LHP006', 'DG001', 'HK1_2425', 'GV003', 'C301', 120, 'Đang mở', 1),
('LHP007', 'DG004', 'HK1_2425', 'GV003', 'C302', 120, 'Đang mở', 1),
('LHP008', 'DG008', 'HK1_2425', 'GV004', 'C303', 100, 'Đang mở', 1);


-- ============================================================================
-- 10. THÊM DỮ LIỆU LOẠI ĐIỂM
-- ============================================================================
INSERT IGNORE INTO LOAI_DIEM (MaLoaiDiem, TenLoaiDiem) VALUES
('CC', 'Chuyên cần'),
('GK', 'Giữa kỳ'),
('CK', 'Cuối kỳ');


-- ============================================================================
-- 11. THÊM DỮ LIỆU CẤU TRÚC ĐIỂM
-- ============================================================================
INSERT IGNORE INTO CAU_TRUC_DIEM (MaMonHoc, MaLoaiDiem, HeSo, MoTa) VALUES
('CNPM01', 'CC', 0.10, 'Chuyên cần 10%'),
('CNPM01', 'GK', 0.30, 'Giữa kỳ 30%'),
('CNPM01', 'CK', 0.60, 'Cuối kỳ 60%'),
('CNPM02', 'CC', 0.10, 'Chuyên cần 10%'),
('CNPM02', 'GK', 0.30, 'Giữa kỳ 30%'),
('CNPM02', 'CK', 0.60, 'Cuối kỳ 60%'),
('QTKD01', 'CC', 0.10, 'Chuyên cần 10%'),
('QTKD01', 'GK', 0.30, 'Giữa kỳ 30%'),
('QTKD01', 'CK', 0.60, 'Cuối kỳ 60%'),
('DG001', 'CC', 0.10, 'Chuyên cần 10%'),
('DG001', 'GK', 0.30, 'Giữa kỳ 30%'),
('DG001', 'CK', 0.60, 'Cuối kỳ 60%'),
('DG004', 'CC', 0.10, 'Chuyên cần 10%'),
('DG004', 'GK', 0.30, 'Giữa kỳ 30%'),
('DG004', 'CK', 0.60, 'Cuối kỳ 60%'),
('DG008', 'CC', 0.10, 'Chuyên cần 10%'),
('DG008', 'GK', 0.30, 'Giữa kỳ 30%'),
('DG008', 'CK', 0.60, 'Cuối kỳ 60%');


-- ============================================================================
-- 12. THÊM DỮ LIỆU USER
-- ============================================================================
INSERT IGNORE INTO `USER` (TenDangNhap, MatKhau, HoTen, Email, SoDienThoai, VaiTro, TrangThai) VALUES
-- Admin
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quản trị viên', 'admin@uni.edu.vn', '0911111111', 'Admin', 1),
-- Giảng viên
('GV001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn Hùng', 'hungnv@uni.edu.vn', '0912345678', 'GiangVien', 1),
-- Sinh viên
('SV001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Thị Hương', 'huongnt@st.uni.edu.vn', '0345678901', 'SinhVien', 1),
('SV002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Văn Bình', 'binhtv@st.uni.edu.vn', '0345678902', 'SinhVien', 1),
('SV003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Thị Thu', 'thult@st.uni.edu.vn', '0345678903', 'SinhVien', 1);


SELECT 'Database created and data imported successfully!' AS Status;
