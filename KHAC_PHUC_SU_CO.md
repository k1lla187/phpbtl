# HƯỚNG DẪN KHẮC PHỤC SỰ CỐ - ADMIN KHÔNG TRUY CẬP ĐƯỢC TRANG QUÊN MẬT KHẨU

## Vấn đề
Admin không thể truy cập trang quản lý yêu cầu đổi mật khẩu (`index.php?url=Home/quenMatKhau`)

## Nguyên nhân
Bảng `YEU_CAU_DOI_MAT_KHAU` chưa được tạo trong database

## Giải pháp

### Bước 1: Truy cập phpMyAdmin
1. Mở trình duyệt
2. Truy cập: `http://localhost/phpmyadmin`
3. Đăng nhập với tài khoản MySQL của bạn

### Bước 2: Chọn Database
1. Trong danh sách bên trái, click vào database `qldiem`

### Bước 3: Chạy Migration
1. Click tab "SQL" ở trên cùng
2. Copy toàn bộ nội dung file `migration_all.sql` (file gộp tất cả migration)
3. Paste vào ô SQL query
4. Click nút "Go" (hoặc "Thực hiện")

### Bước 4: Kiểm tra
1. Click tab "Structure" (Cấu trúc)
2. Tìm các bảng mới: `YEU_CAU_DOI_MAT_KHAU`, `DIEM_DANH`, `THOI_KHOA_BIEU`
3. Kiểm tra bảng `USER` có cột `Avatar`
4. Nếu thấy tất cả → Migration thành công ✓

### Bước 5: Test tính năng
1. Đăng xuất khỏi tài khoản admin
2. Vào trang login: `http://localhost/BTL-PHP-29/public/index.php?url=Auth/login`
3. Click link "Quên mật khẩu?"
4. Nhập thông tin sinh viên hoặc giảng viên
5. Submit form
6. Đăng nhập lại với tài khoản admin
7. Vào menu "Yêu cầu đổi MK" 
8. Kiểm tra có thể xem danh sách yêu cầu ✓

## Cấu trúc bảng YEU_CAU_DOI_MAT_KHAU

| Cột | Kiểu dữ liệu | Mô tả |
|-----|-------------|-------|
| ID | INT | Khóa chính, tự tăng |
| TenDangNhap | VARCHAR(50) | Tên đăng nhập |
| MaNguoiDung | VARCHAR(20) | Mã sinh viên/giảng viên |
| VaiTro | ENUM | 'SinhVien' hoặc 'GiangVien' |
| NgayYeuCau | DATETIME | Thời gian tạo yêu cầu |
| TrangThai | ENUM | 'ChoXuLy', 'DaDuyet', 'TuChoi' |
| MatKhauMoi | VARCHAR(255) | Mật khẩu mới (hash) |
| NguoiXuLy | VARCHAR(50) | Tên admin xử lý |
| NgayXuLy | DATETIME | Thời gian xử lý |
| GhiChu | TEXT | Ghi chú từ admin |

## Kiểm tra logout
Logout đã được triển khai đầy đủ:
- **Controller**: `AuthController::logout()` (dòng 142)
- **Chức năng**: 
  - Xóa tất cả session data
  - Xóa session cookie
  - Destroy session
  - Redirect về trang login

**Cách test logout**:
1. Đăng nhập vào hệ thống
2. Click nút "Đăng xuất" trên header
3. Hệ thống sẽ redirect về trang login
4. Thử truy cập lại URL của trang admin/giảng viên/sinh viên
5. Nếu không được phép → Logout hoạt động tốt ✓

## Các tính năng đã hoàn thành

### 1. Favicon sử dụng SVG ✓
- File hiện tại: `public/favicon.svg`
- File PNG backup: `public/favicon.png` (nếu cần)
- Tất cả các view đã được cập nhật để sử dụng favicon.svg

### 2. Màu sắc đã được cập nhật ✓
- **Trang Auth (Login, Forgot Password)**: Màu xanh dương (#2563eb)
- **Admin/Giảng viên/Sinh viên Portal**: Màu vàng gold (#d4af37)

### 3. Logo UNISCORE ✓
- Logo đã được thay đổi thành favicon.png
- Hiển thị đồng nhất trên tất cả các trang

## Liên hệ hỗ trợ
Nếu vẫn gặp vấn đề, vui lòng kiểm tra:
1. Apache và MySQL đã khởi động chưa?
2. Database `qldiem` đã được import chưa?
3. File `app/config/config.php` đã cấu hình đúng thông tin database chưa?

---
*Ngày cập nhật: 2025-01-05*
