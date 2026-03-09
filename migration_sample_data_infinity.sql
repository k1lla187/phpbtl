-- ============================================================================
-- FILE THÊM DỮ LIỆU MẪU - KHOA, NGÀNH, MÔN HỌC
-- ============================================================================
-- Chạy file này trong phpMyAdmin của Infinity Free
-- LƯU Ý: Không tạo database mới - sử dụng database có sẵn
-- ============================================================================

-- ============================================================================
-- 0. THÊM CỘT CHO PHÉP ĐĂNG KÝ KHÁC KHOA
-- ============================================================================
ALTER TABLE `LOP_HOC_PHAN` 
ADD COLUMN IF NOT EXISTS `ChoPhepDangKyKhacKhoa` TINYINT(1) DEFAULT 0 
COMMENT 'Cho phép SV khác khoa đăng ký (1=Có, 0=Không)';


-- ============================================================================
-- 1. CẬP NHẬT KHOA (thêm khoa đại cương nếu chưa có)
-- ============================================================================
INSERT IGNORE INTO KHOA (MaKhoa, TenKhoa, NgayThanhLap, TruongKhoa) VALUES
('DG', 'Đại cương', '2005-01-01', 'TS. Lê Minh Đức');


-- ============================================================================
-- 2. CẬP NHẬT NGÀNH (thêm ngành cho các khoa)
-- ============================================================================
-- Ngành Công nghệ Thông tin (3 ngành)
INSERT IGNORE INTO NGANH (MaNganh, TenNganh, MaKhoa) VALUES
('CNPM', 'Kỹ thuật Phần mềm', 'CNTT'),
('HTTT', 'Hệ thống Thông tin', 'CNTT'),
('ATTT', 'An toàn Thông tin', 'CNTT');

-- Ngành Kinh tế (3 ngành)
INSERT IGNORE INTO NGANH (MaNganh, TenNganh, MaKhoa) VALUES
('QTKD', 'Quản trị Kinh doanh', 'KT'),
('KTPM', 'Kế toán Doanh nghiệp', 'KT'),
('TCNH', 'Tài chính Ngân hàng', 'KT');

-- Ngành Ngoại ngữ (3 ngành)
INSERT IGNORE INTO NGANH (MaNganh, TenNganh, MaKhoa) VALUES
('NNA', 'Ngôn ngữ Anh', 'NN'),
('NNP', 'Ngôn ngữ Pháp', 'NN'),
('NNT', 'Ngôn ngữ Trung Quốc', 'NN');

-- Ngành Xây dựng (2 ngành)
INSERT IGNORE INTO NGANH (MaNganh, TenNganh, MaKhoa) VALUES
('XDDD', 'Xây dựng Dân dụng', 'XD'),
('XDCN', 'Xây dựng Công nghiệp', 'XD');

-- Ngành Du lịch (2 ngành)
INSERT IGNORE INTO NGANH (MaNganh, TenNganh, MaKhoa) VALUES
('DLKS', 'Du lịch Khách sạn', 'DL'),
('QLDL', 'Quản lý Du lịch', 'DL');


-- ============================================================================
-- 3. THÊM MÔN HỌC THEO NGÀNH (5 môn mỗi ngành)
-- ============================================================================

-- === Môn học ngành CNTT - Kỹ thuật Phần mềm ===
INSERT IGNORE INTO MON_HOC (MaMonHoc, TenMonHoc, SoTinChi, SoTietLyThuyet, SoTietThucHanh, MaNganh) VALUES
('CNPM01', 'Lập trình Python cơ bản', 3, 30, 15, 'CNPM'),
('CNPM02', 'Lập trình Java', 3, 30, 15, 'CNPM'),
('CNPM03', 'Cơ sở dữ liệu', 3, 30, 15, 'CNPM'),
('CNPM04', 'Phân tích thiết kế hệ thống', 3, 45, 0, 'CNPM'),
('CNPM05', 'Kiểm thử phần mềm', 2, 20, 10, 'CNPM');

-- === Môn học ngành HTTT - Hệ thống Thông tin ===
INSERT IGNORE INTO MON_HOC (MaMonHoc, TenMonHoc, SoTinChi, SoTietLyThuyet, SoTietThucHanh, MaNganh) VALUES
('HTTT01', 'Phân tích thiết kế hệ thống thông tin', 3, 45, 0, 'HTTT'),
('HTTT02', 'Quản trị CSDL', 3, 30, 15, 'HTTT'),
('HTTT03', 'Kho dữ liệu và Business Intelligence', 3, 30, 15, 'HTTT'),
('HTTT04', 'An ninh mạng', 3, 30, 15, 'HTTT'),
('HTTT05', 'Thương mại điện tử', 2, 20, 10, 'HTTT');

-- === Môn học ngành ATTT - An toàn Thông tin ===
INSERT IGNORE INTO MON_HOC (MaMonHoc, TenMonHoc, SoTinChi, SoTietLyThuyet, SoTietThucHanh, MaNganh) VALUES
('ATTT01', 'An toàn mạng máy tính', 3, 30, 15, 'ATTT'),
('ATTT02', 'Mật mã học ứng dụng', 3, 30, 15, 'ATTT'),
('ATTT03', 'Phòng chống tấn công mạng', 3, 30, 15, 'ATTT'),
('ATTT04', 'An toàn ứng dụng Web', 2, 20, 10, 'ATTT'),
('ATTT05', 'Quản trị an ninh thông tin', 2, 20, 10, 'ATTT');

-- === Môn học ngành QTKD - Quản trị Kinh doanh ===
INSERT IGNORE INTO MON_HOC (MaMonHoc, TenMonHoc, SoTinChi, SoTietLyThuyet, SoTietThucHanh, MaNganh) VALUES
('QTKD01', 'Nguyên lý quản trị', 3, 45, 0, 'QTKD'),
('QTKD02', 'Marketing căn bản', 3, 30, 15, 'QTKD'),
('QTKD03', 'Quản trị nhân sự', 3, 45, 0, 'QTKD'),
('QTKD04', 'Tài chính doanh nghiệp', 3, 45, 0, 'QTKD'),
('QTKD05', 'Chiến lược kinh doanh', 3, 45, 0, 'QTKD');

-- === Môn học ngành Kế toán ===
INSERT IGNORE INTO MON_HOC (MaMonHoc, TenMonHoc, SoTinChi, SoTietLyThuyet, SoTietThucHanh, MaNganh) VALUES
('KTPM01', 'Kế toán tài chính', 3, 45, 0, 'KTPM'),
('KTPM02', 'Kế toán quản trị', 3, 45, 0, 'KTPM'),
('KTPM03', 'Kiểm toán', 3, 45, 0, 'KTPM'),
('KTPM04', 'Thuế', 2, 30, 0, 'KTPM'),
('KTPM05', 'Phân tích báo cáo tài chính', 3, 45, 0, 'KTPM');

-- === Môn học ngành Tài chính Ngân hàng ===
INSERT IGNORE INTO MON_HOC (MaMonHoc, TenMonHoc, SoTinChi, SoTietLyThuyet, SoTietThucHanh, MaNganh) VALUES
('TCNH01', 'Tài chính tiền tệ', 3, 45, 0, 'TCNH'),
('TCNH02', 'Nghiệp vụ ngân hàng thương mại', 3, 30, 15, 'TCNH'),
('TCNH03', 'Đầu tư tài chính', 3, 30, 15, 'TCNH'),
('TCNH04', 'Bảo hiểm', 2, 20, 10, 'TCNH'),
('TCNH05', 'Tài chính quốc tế', 3, 45, 0, 'TCNH');

-- === Môn học ngành Ngoại ngữ Anh ===
INSERT IGNORE INTO MON_HOC (MaMonHoc, TenMonHoc, SoTinChi, SoTietLyThuyet, SoTietThucHanh, MaNganh) VALUES
('NNA01', 'Tiếng Anh 1', 3, 30, 15, 'NNA'),
('NNA02', 'Tiếng Anh 2', 3, 30, 15, 'NNA'),
('NNA03', 'Tiếng Anh 3', 3, 30, 15, 'NNA'),
('NNA04', 'Tiếng Anh chuyên ngành', 3, 30, 15, 'NNA'),
('NNA05', 'Biên phiên dịch', 3, 20, 20, 'NNA');

-- === Môn học ngành Xây dựng ===
INSERT IGNORE INTO MON_HOC (MaMonHoc, TenMonHoc, SoTinChi, SoTietLyThuyet, SoTietThucHanh, MaNganh) VALUES
('XDDD01', 'Sức bền vật liệu', 3, 45, 0, 'XDDD'),
('XDDD02', 'Kết cấu bê tông cốt thép', 3, 45, 0, 'XDDD'),
('XDDD03', 'Kết cấu thép', 3, 45, 0, 'XDDD'),
('XDDD04', 'Nền móng công trình', 3, 45, 0, 'XDDD'),
('XDDD05', 'Thiết kế kiến trúc', 3, 30, 15, 'XDDD');

-- === Môn học ngành Du lịch ===
INSERT IGNORE INTO MON_HOC (MaMonHoc, TenMonHoc, SoTinChi, SoTietLyThuyet, SoTietThucHanh, MaNganh) VALUES
('DLKS01', 'Nghiệp vụ khách sạn', 3, 30, 15, 'DLKS'),
('DLKS02', 'Nghiệp vụ lữ hành', 3, 30, 15, 'DLKS'),
('DLKS03', 'Marketing du lịch', 3, 30, 15, 'DLKS'),
('DLKS04', 'Quản lý nhà hàng', 3, 30, 15, 'DLKS'),
('DLKS05', 'Tâm lý khách du lịch', 2, 20, 10, 'DLKS');


-- ============================================================================
-- 4. THÊM MÔN ĐẠI CƯƠNG (chung cho tất cả sinh viên)
-- ============================================================================
INSERT IGNORE INTO MON_HOC (MaMonHoc, TenMonHoc, SoTinChi, SoTietLyThuyet, SoTietThucHanh, MaNganh) VALUES
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
-- 5. THÊM CẤU TRÚC ĐIỂM CHO CÁC MÔN MỚI
-- ============================================================================
INSERT IGNORE INTO CAU_TRUC_DIEM (MaMonHoc, MaLoaiDiem, HeSo, MoTa) VALUES
-- CNTT
('CNPM01', 'CC', 0.1, 'Chuyên cần 10%'),
('CNPM01', 'GK', 0.3, 'Giữa kỳ 30%'),
('CNPM01', 'CK', 0.6, 'Cuối kỳ 60%'),
('CNPM02', 'CC', 0.1, 'Chuyên cần 10%'),
('CNPM02', 'GK', 0.3, 'Giữa kỳ 30%'),
('CNPM02', 'CK', 0.6, 'Cuối kỳ 60%'),
-- Kinh tế
('QTKD01', 'CC', 0.1, 'Chuyên cần 10%'),
('QTKD01', 'GK', 0.3, 'Giữa kỳ 30%'),
('QTKD01', 'CK', 0.6, 'Cuối kỳ 60%'),
-- Đại cương
('DG001', 'CC', 0.1, 'Chuyên cần 10%'),
('DG001', 'GK', 0.3, 'Giữa kỳ 30%'),
('DG001', 'CK', 0.6, 'Cuối kỳ 60%'),
('DG004', 'CC', 0.1, 'Chuyên cần 10%'),
('DG004', 'GK', 0.3, 'Giữa kỳ 30%'),
('DG004', 'CK', 0.6, 'Cuối kỳ 60%'),
('DG008', 'CC', 0.1, 'Chuyên cần 10%'),
('DG008', 'GK', 0.3, 'Giữa kỳ 30%'),
('DG008', 'CK', 0.6, 'Cuối kỳ 60%');


-- ============================================================================
-- 6. THÊM LỚP HỌC PHẦN MẪU (Học kỳ hiện tại)
-- Chú ý: Môn đại cương (DG) mặc định cho phép tất cả SV đăng ký
-- ============================================================================
INSERT IGNORE INTO LOP_HOC_PHAN (MaLopHocPhan, MaMonHoc, MaHocKy, MaGiangVien, PhongHoc, SoLuongToiDa, TrangThai, ChoPhepDangKyKhacKhoa) VALUES
-- CNTT (không cho phép đăng ký khác khoa)
('LHP_CNPM01', 'CNPM01', 'HK1_2425', 'GV001', 'A101', 60, 1, 0),
('LHP_CNPM02', 'CNPM02', 'HK1_2425', 'GV001', 'A102', 60, 1, 0),
('LHP_CNPM03', 'CNPM03', 'HK1_2425', 'GV005', 'PM01', 40, 1, 0),
-- Kinh tế (không cho phép đăng ký khác khoa)
('LHP_QTKD01', 'QTKD01', 'HK1_2425', 'GV002', 'B201', 80, 1, 0),
('LHP_QTKD02', 'QTKD02', 'HK1_2425', 'GV002', 'B202', 80, 1, 0),
-- Đại cương (CHO PHÉP tất cả SV đăng ký - dù khác khoa)
('LHP_DG001', 'DG001', 'HK1_2425', 'GV003', 'C301', 120, 1, 1),
('LHP_DG004', 'DG004', 'HK1_2425', 'GV003', 'C302', 120, 1, 1),
('LHP_DG008', 'DG008', 'HK1_2425', 'GV004', 'C303', 100, 1, 1);


-- ============================================================================
-- 7. THÊM THÔNG TIN KHOA VÀO BẢNG MON_HOC (để xác định môn đại cương)
-- ============================================================================
UPDATE MON_HOC SET MaNganh = 'DG' WHERE MaMonHoc LIKE 'DG%';


SELECT 'Sample data added successfully!' AS Status;
