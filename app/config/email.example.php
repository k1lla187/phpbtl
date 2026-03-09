<?php
/**
 * Cấu hình Email SMTP - UNISCORE System
 * =====================================
 * 
 * HƯỚNG DẪN:
 * 1. Copy file này thành email.php
 * 2. Điền thông tin SMTP thật vào email.php
 * 3. KHÔNG commit file email.php lên Git
 * 
 * HƯỚNG DẪN CẤU HÌNH GMAIL:
 * -------------------------
 * 1. Đăng nhập vào tài khoản Gmail
 * 2. Bật "Xác minh 2 bước" tại: https://myaccount.google.com/security
 * 3. Tạo "Mật khẩu ứng dụng" tại: https://myaccount.google.com/apppasswords
 * 4. Điền thông tin vào cấu hình bên dưới
 */

define('EMAIL_CONFIG', [
    // SMTP Server Settings
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls',
    
    // Thông tin đăng nhập SMTP - THAY ĐỔI THÔNG TIN NÀY
    'smtp_username' => 'your-email@gmail.com',      // Email của bạn
    'smtp_password' => 'your-app-password',         // Mật khẩu ứng dụng 16 ký tự
    
    // Thông tin người gửi
    'from_email' => 'your-email@gmail.com',
    'from_name' => 'UNISCORE System',
    
    // Cài đặt khác
    'charset' => 'UTF-8',
    'debug' => false,
]);
