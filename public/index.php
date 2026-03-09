<?php
// Cấu hình session: cookie hết hạn khi đóng trình duyệt (không persist qua tab mới)
ini_set('session.cookie_lifetime', 0);
ini_set('session.use_strict_mode', 1);
// Bắt đầu session (rất quan trọng cho đăng nhập/đăng xuất sau này)
session_start();

// Gọi file khởi tạo (init) từ thư mục app
require_once '../app/init.php';

// Khởi chạy ứng dụng
$app = new App();
?>