# Hướng dẫn Cấu hình Email SMTP

## Giới thiệu
Tính năng **Quên mật khẩu** của hệ thống UNISCORE sử dụng SMTP để gửi email chứa mật khẩu mới cho người dùng một cách an toàn và chuyên nghiệp.

## Cấu hình với Gmail (Khuyến nghị)

### Bước 1: Bật Xác minh 2 bước
1. Đăng nhập vào tài khoản Gmail: https://accounts.google.com
2. Vào mục **Bảo mật** (Security)
3. Bật **Xác minh 2 bước** (2-Step Verification)

### Bước 2: Tạo Mật khẩu ứng dụng
1. Truy cập: https://myaccount.google.com/apppasswords
2. Chọn **Ứng dụng**: Mail
3. Chọn **Thiết bị**: Windows Computer (hoặc thiết bị của bạn)
4. Nhấn **Tạo** (Generate)
5. **Sao chép mật khẩu 16 ký tự** được hiển thị (ví dụ: `abcd efgh ijkl mnop`)

### Bước 3: Cập nhật file cấu hình
Mở file `app/config/email.php` và cập nhật thông tin:

```php
define('EMAIL_CONFIG', [
    // SMTP Server Settings
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls',
    
    // Thông tin đăng nhập - THAY ĐỔI Ở ĐÂY
    'smtp_username' => 'your-email@gmail.com',       // Email Gmail của bạn
    'smtp_password' => 'abcdefghijklmnop',           // Mật khẩu ứng dụng (16 ký tự, không khoảng trắng)
    
    // Thông tin người gửi
    'from_email' => 'your-email@gmail.com',          // Email người gửi
    'from_name' => 'UNISCORE System',                // Tên hiển thị
    
    // Cài đặt khác
    'charset' => 'UTF-8',
    'debug' => false,                                // Đặt true để xem log debug
]);
```

## Cấu hình với các SMTP Server khác

### Microsoft Outlook/Office 365
```php
'smtp_host' => 'smtp.office365.com',
'smtp_port' => 587,
'smtp_secure' => 'tls',
'smtp_username' => 'your-email@outlook.com',
'smtp_password' => 'your-password',
```

### Yahoo Mail
```php
'smtp_host' => 'smtp.mail.yahoo.com',
'smtp_port' => 587,
'smtp_secure' => 'tls',
'smtp_username' => 'your-email@yahoo.com',
'smtp_password' => 'your-app-password',
```

### Mailgun (Dịch vụ chuyên nghiệp)
```php
'smtp_host' => 'smtp.mailgun.org',
'smtp_port' => 587,
'smtp_secure' => 'tls',
'smtp_username' => 'postmaster@your-domain.mailgun.org',
'smtp_password' => 'your-mailgun-password',
```

## Kiểm tra cấu hình

Sau khi cấu hình, bạn có thể kiểm tra bằng cách:
1. Truy cập trang đăng nhập
2. Nhấn "Quên mật khẩu?"
3. Nhập email đã đăng ký trong hệ thống
4. Kiểm tra hộp thư (và cả thư mục Spam)

## Xử lý lỗi thường gặp

### Lỗi "Không thể kết nối đến SMTP server"
- Kiểm tra firewall có chặn cổng 587 không
- Kiểm tra kết nối internet
- Thử đổi sang cổng 465 với `smtp_secure` = `ssl`

### Lỗi "SMTP Error: 535 Authentication failed"
- Kiểm tra lại username và password
- Đảm bảo đã sử dụng **Mật khẩu ứng dụng** thay vì mật khẩu thường (với Gmail)
- Đảm bảo đã bật "Xác minh 2 bước"

### Lỗi "SMTP Error: 550 Relay not permitted"
- Email người gửi phải khớp với tài khoản SMTP
- Kiểm tra domain đã được xác thực chưa

### Debug
Để xem chi tiết lỗi, đặt `'debug' => true` trong file cấu hình, sau đó kiểm tra log tại:
- **XAMPP**: `C:\xampp\apache\logs\error.log`
- **Linux**: `/var/log/apache2/error.log`

## Bảo mật

⚠️ **Quan trọng**:
- **KHÔNG** commit file `email.php` lên Git nếu chứa thông tin nhạy cảm
- Thêm `app/config/email.php` vào `.gitignore`
- Sử dụng **Mật khẩu ứng dụng** thay vì mật khẩu chính
- Thay đổi mật khẩu định kỳ

## Hỗ trợ

Nếu gặp vấn đề, vui lòng liên hệ:
- Email: admin@uniscore.edu.vn
- Hotline: 1900-xxxx
