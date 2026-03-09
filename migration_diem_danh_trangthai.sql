-- Migration: Thêm cột TrangThai cho bảng điểm danh
-- 4 trạng thái: 1=Có mặt, 2=Muộn, 3=Nghỉ có lý do, 4=Nghỉ không lý do

-- Thêm cột TrangThai nếu chưa tồn tại
ALTER TABLE DIEM_DANH 
ADD COLUMN TrangThai TINYINT DEFAULT 1 COMMENT '1=Có mặt, 2=Muộn, 3=Nghỉ có lý do, 4=Nghỉ không lý do' AFTER CoMat;

-- Cập nhật dữ liệu cũ: những record CoMat = 0 chuyển thành TrangThai = 4 (Nghỉ không lý do)
UPDATE DIEM_DANH SET TrangThai = 4 WHERE CoMat = 0;

-- Cập nhật dữ liệu cũ: những record CoMat = 1 chuyển thành TrangThai = 1 (Có mặt)
UPDATE DIEM_DANH SET TrangThai = 1 WHERE CoMat = 1;

-- Cập nhật lại cột CoMat để luôn = 1 khi TrangThai = 1 (Có mặt)
-- CoMat sẽ được dùng để tính điểm: 1 = được tính điểm đầy đủ, 0 = bị trừ điểm
UPDATE DIEM_DANH SET CoMat = 1 WHERE TrangThai IN (1, 3);

-- Cập nhật lại cột CoMat = 0 cho các trạng thái bị trừ điểm
UPDATE DIEM_DANH SET CoMat = 0 WHERE TrangThai IN (2, 4);
