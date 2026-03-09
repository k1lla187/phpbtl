-- Migration: Thêm trạng thái điểm cho bảng DANG_KY_HOC
-- Trạng thái: 0 = Mới lưu, 1 = Đã khóa, 2 = Đã phê duyệt

ALTER TABLE DANG_KY_HOC 
ADD COLUMN TrangThaiDiem TINYINT DEFAULT 0 COMMENT '0=Moi luu, 1=Da khoa, 2=Phe duyet' AFTER KetQua;

ALTER TABLE DANG_KY_HOC 
ADD COLUMN NgayKhoaDiem DATETIME DEFAULT NULL AFTER TrangThaiDiem;

ALTER TABLE DANG_KY_HOC 
ADD COLUMN NguoiKhoaDiem VARCHAR(20) DEFAULT NULL AFTER NgayKhoaDiem;

ALTER TABLE DANG_KY_HOC 
ADD COLUMN NgayPheDuyet DATETIME DEFAULT NULL AFTER NguoiKhoaDiem;

ALTER TABLE DANG_KY_HOC 
ADD COLUMN NguoiPheDuyet VARCHAR(20) DEFAULT NULL AFTER NgayPheDuyet;

-- Cập nhật trạng thái mặc định cho các record hiện có
UPDATE DANG_KY_HOC SET TrangThaiDiem = 0 WHERE TrangThaiDiem IS NULL;
