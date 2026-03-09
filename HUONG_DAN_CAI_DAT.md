# HƯỚNG DẪN CÀI ĐẶT HỆ THỐNG UNISCORE

## 📋 Yêu cầu hệ thống
- XAMPP (Apache, MySQL, PHP 8.x)
- Trình duyệt web hiện đại (Chrome, Firefox, Edge)
- phpMyAdmin

## 🚀 Các bước cài đặt

### Bước 1: Chuẩn bị
1. Cài đặt XAMPP từ https://www.apachefriends.org
2. Khởi động Apache và MySQL từ XAMPP Control Panel

### Bước 2: Cấu hình project
1. Copy thư mục project vào `C:\xampp\htdocs\BTL-PHP-29`
2. Mở file `app/config/config.php` và kiểm tra thông tin database:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'qldiem');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Mật khẩu MySQL (mặc định XAMPP là rỗng)
   ```

### Bước 3: Tạo database
1. Mở phpMyAdmin: `http://localhost/phpmyadmin`
2. Click "New" (Mới) để tạo database
3. Tên database: `qldiem`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"

### Bước 4: Import dữ liệu chính
1. Chọn database `qldiem` vừa tạo
2. Click tab "Import"
3. Click "Choose File" và chọn file `qldiem.sql`
4. Click "Go" để import
5. Đợi đến khi thấy "Import has been successfully finished"

### Bước 5: Chạy migration
1. Vẫn ở database `qldiem`
2. Click tab "SQL"
3. Mở file `migration_all.sql` trong project
4. Copy toàn bộ nội dung và paste vào ô SQL query
5. Click "Go"
6. Thấy message "Migration completed successfully!" → Thành công ✅

### Bước 6: Kiểm tra cài đặt
1. Click tab "Structure" trong phpMyAdmin
2. Kiểm tra các bảng sau đã tồn tại:
   - ✅ `USER` (có cột `Avatar`)
   - ✅ `DIEM_DANH`
   - ✅ `THOI_KHOA_BIEU`
   - ✅ `YEU_CAU_DOI_MAT_KHAU`
   - ✅ Các bảng khác: `SINH_VIEN`, `GIANG_VIEN`, `MON_HOC`, v.v.

### Bước 7: Truy cập hệ thống
Mở trình duyệt và truy cập:
```
http://localhost/BTL-PHP-29/public/
```

### Bước 8: Đăng nhập
File `qldiem.sql` đã có tài khoản mẫu:

**Admin:**
- Tên đăng nhập: `admin`
- Mật khẩu: `admin123`

**Giảng viên:**
- Tên đăng nhập: `gv001`
- Mật khẩu: `123456`

**Sinh viên:**
- Tên đăng nhập: `sv001`
- Mật khẩu: `123456`

> 💡 **Lưu ý**: Nên đổi mật khẩu ngay sau khi đăng nhập lần đầu!

## 📁 Cấu trúc file SQL

- **qldiem.sql**: Database chính với cấu trúc bảng và dữ liệu mẫu
- **migration_all.sql**: File gộp tất cả migration (chạy sau qldiem.sql)
- ~~migration_*.sql~~: Các file migration riêng lẻ (đã gộp vào migration_all.sql)

## 🔧 Khắc phục sự cố

### Lỗi "Table doesn't exist"
→ Chạy lại file `migration_all.sql` (xem Bước 5)

### Lỗi kết nối database
→ Kiểm tra file `app/config/config.php` và thông tin MySQL

### Lỗi "Access denied"
→ Kiểm tra username/password MySQL trong `config.php`

### Trang trắng/lỗi 404
→ Kiểm tra Apache đã chạy chưa, URL có đúng không

### Lỗi favicon không hiển thị
→ Kiểm tra file `public/favicon.svg` đã tồn tại

## 📞 Hỗ trợ
Chi tiết về các lỗi và cách khắc phục:
- [GIAI_THICH_LOI_APACHE.md](GIAI_THICH_LOI_APACHE.md)
- [KHAC_PHUC_SU_CO.md](KHAC_PHUC_SU_CO.md)

---
*Ngày cập nhật: 05/02/2026*
