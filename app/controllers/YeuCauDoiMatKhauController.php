<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/YeuCauDoiMatKhauModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class YeuCauDoiMatKhauController extends Controller {
    private $yeuCauModel;
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->yeuCauModel = new YeuCauDoiMatKhauModel($this->db);
        $this->userModel = new UserModel($this->db);
        
        // Kiểm tra đăng nhập và quyền Admin
        if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'Admin') {
            header('Location: index.php?url=Auth/index');
            exit;
        }
    }
    
    /**
     * Hiển thị danh sách yêu cầu đổi mật khẩu
     */
    public function index() {
        $filter = $_GET['filter'] ?? 'all';
        
        if ($filter === 'pending') {
            $requests = $this->yeuCauModel->getChoXuLy();
        } else {
            $requests = $this->yeuCauModel->readAll();
        }
        
        $pendingCount = $this->yeuCauModel->countPending();
        
        require_once __DIR__ . '/../views/admin/yeucaudoimatkhau/index.php';
    }
    
    /**
     * Duyệt yêu cầu - Gửi mật khẩu mới qua email
     */
    public function approve() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=YeuCauDoiMatKhau/index');
            exit;
        }
        
        $id = intval($_POST['id'] ?? 0);
        $ghiChu = trim($_POST['ghichu'] ?? '');
        
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Yêu cầu không hợp lệ!';
            header('Location: index.php?url=YeuCauDoiMatKhau/index');
            exit;
        }
        
        // Lấy thông tin yêu cầu
        $request = $this->yeuCauModel->getById($id);
        
        if (!$request) {
            $_SESSION['flash_error'] = 'Không tìm thấy yêu cầu!';
            header('Location: index.php?url=YeuCauDoiMatKhau/index');
            exit;
        }
        
        if ($request['TrangThai'] !== 'ChoXuLy') {
            $_SESSION['flash_error'] = 'Yêu cầu này đã được xử lý trước đó!';
            header('Location: index.php?url=YeuCauDoiMatKhau/index');
            exit;
        }
        
        // Tạo mật khẩu mới
        $newPassword = $this->generateRandomPassword(10);
        
        // Load EmailService và kiểm tra cấu hình
        require_once __DIR__ . '/../services/EmailService.php';
        $emailService = new EmailService();
        $emailConfigured = $emailService->isConfigured();
        $emailSent = false;
        
        if ($emailConfigured) {
            // Gửi email nếu đã cấu hình SMTP
            $emailSent = $this->sendPasswordResetEmail(
                $emailService, 
                $request['Email'], 
                $request['HoTen'], 
                $request['TenDangNhap'], 
                $newPassword
            );
        }
        
        // Cập nhật mật khẩu và đặt flag yêu cầu đổi mật khẩu
        $this->userModel->updatePassword($request['MaUser'], $newPassword);
        $this->userModel->setRequirePasswordChange($request['MaUser'], true);
        
        // Cập nhật trạng thái yêu cầu
        $ghiChuFinal = $ghiChu ?: ($emailSent ? 'Đã duyệt và gửi mật khẩu mới qua email' : 'Đã duyệt - Admin thông báo mật khẩu');
        $this->yeuCauModel->approve($id, $_SESSION['user_id'], $ghiChuFinal);
        
        // Hiển thị kết quả tùy thuộc vào việc email có được gửi hay không
        if ($emailSent) {
            $_SESSION['flash_success'] = 'Đã duyệt yêu cầu và gửi mật khẩu mới đến email ' . $request['Email'];
        } else {
            // Lưu thông tin mật khẩu để hiển thị cho admin
            $_SESSION['password_reset_info'] = [
                'hoTen' => $request['HoTen'],
                'tenDangNhap' => $request['TenDangNhap'],
                'email' => $request['Email'],
                'newPassword' => $newPassword,
                'vaiTro' => $request['VaiTro'],
                'emailConfigured' => $emailConfigured,
                'emailError' => $emailConfigured ? $emailService->getLastError() : 'Chưa cấu hình SMTP'
            ];
            header('Location: index.php?url=YeuCauDoiMatKhau/showPassword');
            exit;
        }
        
        header('Location: index.php?url=YeuCauDoiMatKhau/index');
        exit;
    }
    
    /**
     * Hiển thị mật khẩu mới khi không gửi được email
     */
    public function showPassword() {
        if (!isset($_SESSION['password_reset_info'])) {
            header('Location: index.php?url=YeuCauDoiMatKhau/index');
            exit;
        }
        
        $passwordInfo = $_SESSION['password_reset_info'];
        unset($_SESSION['password_reset_info']);
        
        require_once __DIR__ . '/../views/admin/yeucaudoimatkhau/show_password.php';
    }
    
    /**
     * Từ chối yêu cầu
     */
    public function reject() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=YeuCauDoiMatKhau/index');
            exit;
        }
        
        $id = intval($_POST['id'] ?? 0);
        $lyDoTuChoi = trim($_POST['lydotuchoi'] ?? '');
        
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Yêu cầu không hợp lệ!';
            header('Location: index.php?url=YeuCauDoiMatKhau/index');
            exit;
        }
        
        if (empty($lyDoTuChoi)) {
            $_SESSION['flash_error'] = 'Vui lòng nhập lý do từ chối!';
            header('Location: index.php?url=YeuCauDoiMatKhau/index');
            exit;
        }
        
        $request = $this->yeuCauModel->getById($id);
        
        if (!$request || $request['TrangThai'] !== 'ChoXuLy') {
            $_SESSION['flash_error'] = 'Yêu cầu không tồn tại hoặc đã được xử lý!';
            header('Location: index.php?url=YeuCauDoiMatKhau/index');
            exit;
        }
        
        $this->yeuCauModel->reject($id, $_SESSION['user_id'], $lyDoTuChoi);
        
        $_SESSION['flash_success'] = 'Đã từ chối yêu cầu của ' . $request['HoTen'];
        header('Location: index.php?url=YeuCauDoiMatKhau/index');
        exit;
    }
    
    /**
     * Xóa yêu cầu
     */
    public function delete() {
        $id = intval($_GET['id'] ?? 0);
        
        if ($id > 0) {
            $this->yeuCauModel->delete($id);
            $_SESSION['flash_success'] = 'Đã xóa yêu cầu!';
        }
        
        header('Location: index.php?url=YeuCauDoiMatKhau/index');
        exit;
    }
    
    /**
     * Tạo mật khẩu ngẫu nhiên
     */
    private function generateRandomPassword($length = 10) {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%';
        
        // Đảm bảo có ít nhất 1 ký tự mỗi loại
        $password = $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];
        
        // Thêm các ký tự còn lại
        $allChars = $uppercase . $lowercase . $numbers;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        // Xáo trộn mật khẩu
        return str_shuffle($password);
    }
    
    /**
     * Gửi email chứa mật khẩu mới
     */
    private function sendPasswordResetEmail($emailService, $toEmail, $fullName, $username, $newPassword) {
        $subject = '[UNISCORE] Mật khẩu mới - Yêu cầu đã được duyệt';
        
        $htmlBody = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
        .header { background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; }
        .header p { margin: 10px 0 0; opacity: 0.9; }
        .content { padding: 30px; background: #ffffff; }
        .content h2 { color: #1e293b; margin-top: 0; }
        .success-badge { background: #ecfdf5; border: 2px solid #10b981; color: #059669; padding: 10px 20px; border-radius: 30px; display: inline-block; font-weight: bold; margin-bottom: 20px; }
        .info-row { background: #f8fafc; padding: 12px 15px; border-radius: 8px; margin: 15px 0; }
        .info-row strong { color: #475569; }
        .password-box { background: linear-gradient(135deg, #eff6ff, #dbeafe); border: 2px solid #3b82f6; border-radius: 12px; padding: 25px; text-align: center; margin: 25px 0; }
        .password-label { color: #64748b; font-size: 14px; margin: 0 0 10px; }
        .password { font-size: 32px; font-weight: bold; color: #1d4ed8; letter-spacing: 3px; margin: 0; font-family: monospace; }
        .warning { background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 0 8px 8px 0; padding: 15px 20px; margin: 25px 0; }
        .warning-title { color: #92400e; font-weight: bold; margin: 0 0 10px; }
        .warning ul { margin: 0; padding-left: 20px; color: #78350f; }
        .warning li { margin: 5px 0; }
        .important { background: #fef2f2; border-left: 4px solid #ef4444; border-radius: 0 8px 8px 0; padding: 15px 20px; margin: 25px 0; }
        .important-title { color: #dc2626; font-weight: bold; margin: 0 0 10px; }
        .important p { color: #7f1d1d; margin: 0; }
        .footer { background: #f1f5f9; padding: 20px; text-align: center; font-size: 12px; color: #64748b; }
        .footer p { margin: 5px 0; }
        .btn { display: inline-block; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ YÊU CẦU ĐÃ ĐƯỢC DUYỆT</h1>
            <p>Hệ thống Quản lý Điểm Sinh viên UNISCORE</p>
        </div>
        <div class="content">
            <h2>Xin chào ' . htmlspecialchars($fullName) . ',</h2>
            
            <div class="success-badge">🎉 Yêu cầu khôi phục mật khẩu đã được duyệt!</div>
            
            <p>Quản trị viên đã xem xét và phê duyệt yêu cầu khôi phục mật khẩu của bạn.</p>
            
            <div class="info-row">
                <strong>👤 Tên đăng nhập:</strong> ' . htmlspecialchars($username) . '
            </div>
            
            <div class="password-box">
                <p class="password-label">🔐 Mật khẩu mới của bạn là:</p>
                <p class="password">' . htmlspecialchars($newPassword) . '</p>
            </div>
            
            <div class="important">
                <p class="important-title">⚠️ BẮT BUỘC ĐỔI MẬT KHẨU</p>
                <p>Khi đăng nhập lần đầu bằng mật khẩu này, hệ thống sẽ <strong>yêu cầu bạn tạo mật khẩu mới</strong> để đảm bảo an toàn tài khoản.</p>
            </div>
            
            <div class="warning">
                <p class="warning-title">📝 Lưu ý quan trọng:</p>
                <ul>
                    <li>Mật khẩu mới chỉ sử dụng được <strong>một lần</strong></li>
                    <li>Sau khi đăng nhập, hãy tạo mật khẩu dễ nhớ cho bạn</li>
                    <li>Không chia sẻ email này với bất kỳ ai</li>
                </ul>
            </div>
            
            <p style="text-align: center;">
                <a href="' . (defined('URLROOT') ? URLROOT : '') . '" class="btn">Đăng nhập ngay</a>
            </p>
        </div>
        <div class="footer">
            <p>Email này được gửi tự động từ hệ thống UNISCORE.</p>
            <p>Vui lòng không trả lời email này.</p>
            <p>© ' . date('Y') . ' UNISCORE - Quản lý điểm sinh viên</p>
        </div>
    </div>
</body>
</html>';
        
        return $emailService->send($toEmail, $subject, base64_encode($htmlBody), $fullName);
    }
}
