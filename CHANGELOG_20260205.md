# UNISCORE - Log thay đổi hệ thống

## Ngày: 05/02/2026

### 0. Rebranding - UNISCORE

**Thay đổi:**
- Đổi tên hệ thống từ "Hệ Thống Quản Lý Đào Tạo" thành **UNISCORE**
- Cập nhật favicon mới với logo UNISCORE (hình khiên vàng với mũ tốt nghiệp)
- Thống nhất màu sắc với tone vàng chủ đạo (#d4af37)
- Cập nhật logo sidebar cho Admin, Giảng viên và Sinh viên
- Cập nhật footer với branding UNISCORE

**File thay đổi:**
- `public/favicon.svg` - Logo mới
- `public/css/admin.css` - Cập nhật màu chủ đạo
- `public/css/sinhvien.css` - Cập nhật màu chủ đạo
- `public/css/giangvien.css` - Cập nhật màu chủ đạo
- `app/views/admin/layouts/header.php` - Logo và branding mới
- `app/views/admin/layouts/footer.php` - Footer mới
- `app/views/auth/login.php` - Logo và branding mới
- `app/views/auth/forgotpassword.php` - Logo và branding mới
- `app/views/sinhvien/_layout_sv.php` - Logo mới
- `app/views/sinhvien/_layout_sv_footer.php` - Footer mới
- `app/views/giangvien/*.php` - Logo mới cho tất cả các trang
- `app/views/profile/giangvien.php` - Logo mới

---

### 1. Sửa lỗi và cải tiến phần Điểm danh

**Vấn đề:** Phần điểm danh trước đây cho phép nhập tay số buổi theo thứ tự, dễ gây lỗi và không kiểm soát được giới hạn.

**Giải pháp:** 
- Đổi từ input number sang dropdown select với số buổi giới hạn
- **Công thức mới**: Số buổi tối đa = `SoTinChi × 5 + 3` (thêm 3 buổi học bù)
  - Ví dụ: 1 tín chỉ = 8 buổi (5 + 3 buổi học bù)
  - 2 tín chỉ = 13 buổi (10 + 3)
  - 3 tín chỉ = 18 buổi (15 + 3)

**File thay đổi:**
- `app/views/giangvien/diemdanh.php` - Thay input thành select dropdown, hiển thị giới hạn buổi
- `app/controllers/GiangVienController.php` - Thêm validation kiểm tra số buổi không vượt quá giới hạn
- `app/models/LopHocPhanModel.php` - Cập nhật getById() để lấy thêm SoTinChi từ MON_HOC
- `migration_diem_danh.sql` - Cập nhật comment trong migration

---

### 2. Thêm tính năng Quên mật khẩu

**Mô tả:** Cho phép Sinh viên/Giảng viên yêu cầu đổi mật khẩu khi quên, Admin phải xác nhận mới đổi được.

**Quy trình:**
1. User vào trang "Quên mật khẩu" từ trang login
2. Nhập: Tên đăng nhập + Mã Sinh Viên/Giảng Viên + Vai trò
3. Hệ thống tạo yêu cầu, lưu vào bảng YEU_CAU_DOI_MAT_KHAU
4. Admin vào mục "Yêu cầu đổi MK" trong sidebar
5. Admin duyệt hoặc từ chối yêu cầu
6. Nếu duyệt: Hệ thống tự động tạo mật khẩu mới (8 ký tự ngẫu nhiên), hiển thị cho admin để thông báo cho user

**File mới:**
- `migration_forgot_password.sql` - Migration tạo bảng YEU_CAU_DOI_MAT_KHAU
- `app/models/YeuCauDoiMatKhauModel.php` - Model quản lý yêu cầu đổi mật khẩu
- `app/views/auth/forgotpassword.php` - Trang quên mật khẩu cho user
- `app/views/admin/home/quenmatkhau.php` - Trang quản lý yêu cầu cho admin

**File thay đổi:**
- `app/views/auth/login.php` - Thêm link "Quên mật khẩu?"
- `app/controllers/AuthController.php` - Thêm forgotPassword() và submitForgotPassword()
- `app/controllers/HomeController.php` - Thêm quenMatKhau() và duyetYeuCauMK()
- `app/views/admin/layouts/header.php` - Thêm menu "Yêu cầu đổi MK" trong sidebar

**Cấu trúc bảng YEU_CAU_DOI_MAT_KHAU:**
```sql
- ID (PK)
- TenDangNhap
- MaNguoiDung (Mã SV hoặc Mã GV)
- VaiTro (SinhVien/GiangVien)
- NgayYeuCau
- TrangThai (ChoXuLy/DaDuyet/TuChoi)
- MatKhauMoi (password mới sau khi admin duyệt)
- NguoiXuLy (Admin xử lý)
- NgayXuLy
- GhiChu
```

---

### 3. Thêm Favicon cho trang web

**Mô tả:** Thêm icon "QL" (Quản Lý) hiển thị trên tab trình duyệt

**File mới:**
- `public/favicon.svg` - File icon SVG với gradient màu xanh dương, chữ "QL" trắng

**File thay đổi (thêm link favicon):**
- `app/views/auth/login.php`
- `app/views/auth/forgotpassword.php`
- `app/views/admin/layouts/header.php`
- `app/views/giangvien/diemdanh.php`
- `app/views/giangvien/dashboardgiangvien.php`
- `app/views/giangvien/tracuudiem.php`
- `app/views/giangvien/nhapdiem.php`
- `app/views/giangvien/lichday.php`
- `app/views/giangvien/guithongbao.php`
- `app/views/sinhvien/_layout_sv.php`

**Cú pháp thêm:**
```html
<link rel="icon" type="image/svg+xml" href="<?= rtrim($baseUrl ?? '', '/') ?>/favicon.svg">
```

---

## Hướng dẫn cập nhật

### Bước 1: Chạy migration
```sql
-- Chạy file migration để tạo bảng mới
SOURCE migration_forgot_password.sql;
```

### Bước 2: Test chức năng

#### Test Điểm danh:
1. Đăng nhập với tài khoản Giảng viên
2. Vào "Điểm danh"
3. Chọn lớp học phần
4. Kiểm tra dropdown "Buổi thứ" có đúng số buổi = SoTinChi × 5 + 3 không
5. Thử điểm danh một buổi và lưu

#### Test Quên mật khẩu:
1. Vào trang login, click "Quên mật khẩu?"
2. Chọn vai trò (Giảng viên hoặc Sinh viên)
3. Nhập tên đăng nhập và mã tương ứng
4. Gửi yêu cầu
5. Đăng nhập Admin, vào "Yêu cầu đổi MK"
6. Duyệt yêu cầu
7. Kiểm tra mật khẩu mới hiển thị, copy và thông báo cho user
8. Đăng nhập lại với mật khẩu mới

#### Test Favicon:
1. Mở bất kỳ trang nào của hệ thống
2. Kiểm tra icon "QL" màu xanh hiển thị trên tab trình duyệt

---

## Lưu ý quan trọng

1. **Bảo mật mật khẩu:** Mật khẩu mới được tạo ngẫu nhiên và hash bằng `password_hash()` trước khi lưu vào database
2. **Validation:** Hệ thống kiểm tra tên đăng nhập và mã sinh viên/giảng viên có khớp với database trước khi tạo yêu cầu
3. **Không cho phép spam:** Nếu đã có yêu cầu đang chờ xử lý, không cho tạo yêu cầu mới
4. **Admin notification:** Mật khẩu mới sẽ hiển thị trên giao diện admin sau khi duyệt, admin cần thông báo cho user

---

## Cải tiến tương lai

1. **Email notification:** Gửi email tự động cho user khi admin duyệt yêu cầu
2. **SMS notification:** Gửi mật khẩu mới qua SMS
3. **Captcha:** Thêm captcha vào form quên mật khẩu để chống spam
4. **Activity log:** Ghi lại log mỗi khi có thay đổi mật khẩu
