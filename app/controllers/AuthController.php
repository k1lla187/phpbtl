<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/RememberTokenModel.php';

class AuthController extends Controller {
    private $userModel;
    private $rememberTokenModel;
    
    public function __construct() {
        $db = new Database();
        $this->userModel = new UserModel($db->getConnection());
        $this->rememberTokenModel = new RememberTokenModel($db->getConnection());
    }
    
    /**
     * Hiển thị trang đăng nhập (view bên ngoài admin)
     */
    public function index() {
        // Hiển thị thông báo lỗi nếu có
        $error = '';
        if (isset($_GET['error'])) {
            if ($_GET['error'] === 'account_disabled') {
                $error = 'Tài khoản không hoạt động. Vui lòng liên hệ admin hoặc cố vấn học tập để được hỗ trợ!';
            }
        }
        
        // Không tự động đăng nhập nữa
        // Chỉ hiển thị form login bình thường
        require_once __DIR__ . '/../views/auth/login.php';
    }
    
    /**
     * Xử lý đăng nhập
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $selectedRole = $_POST['role'] ?? 'admin';
            $remember = isset($_POST['remember']);
            
            // Validate input
            if (empty($username) || empty($password)) {
                $this->showLoginError('Vui lòng nhập đầy đủ thông tin!');
                return;
            }
            
            // Kiểm tra có token "ghi nhớ" cho vai trò này không
            $autoLoginAttempted = false;
            if ($remember && isset($_COOKIE['remember_token'])) {
                $token = $_COOKIE['remember_token'];
                $tokenData = $this->rememberTokenModel->findByToken($token);
                
                // Chỉ tự động đăng nhập nếu vai trò khớp
                if ($tokenData && $this->mapLoginTypeToRole($selectedRole) === $tokenData['VaiTro']) {
                    // Token hợp lệ và vai trò khớp
                    $savedUsername = $tokenData['TenDangNhap'];
                    
                    // Tìm user trong database bằng phương thức trực tiếp
                    $user = $this->userModel->getByUsernameDirect($savedUsername);
                    if ($user) {
                        // Kiểm tra trạng thái tài khoản
                        $trangThai = $user['TrangThai'] ?? 1;
                        if ($trangThai == 0 || $trangThai === '0') {
                            $this->showLoginError('Tài khoản không hoạt động. Vui lòng liên hệ admin hoặc cố vấn học tập để được hỗ trợ!');
                            return;
                        }
                        
                        // Lưu session
                        $_SESSION['user_id'] = $user['MaUser'];
                        $_SESSION['user_name'] = $user['HoTen'];
                        $_SESSION['user_role'] = $user['VaiTro'];
                        $_SESSION['user_avatar'] = $user['Avatar'] ?? null;
                        $_SESSION['logged_in'] = true;
                        $_SESSION['login_type'] = $selectedRole;
                        
                        // Redirect theo role
                        $this->redirectByRole($selectedRole, $user['VaiTro']);
                        return;
                    }
                }
            }
            
            // Tìm user theo username (đăng nhập bình thường)
            $userData = $this->userModel->getByUsernameDirect($username);
            $loggedIn = false;
            
            if ($userData) {
                // Kiểm tra trạng thái tài khoản - nếu bị vô hiệu hóa thì không cho đăng nhập
                $trangThai = $userData['TrangThai'] ?? 1;
                if ($trangThai == 0 || $trangThai === '0') {
                    $this->showLoginError('Tài khoản không hoạt động. Vui lòng liên hệ admin hoặc cố vấn học tập để được hỗ trợ!');
                    return;
                }
                
                $stored = $userData['MatKhau'] ?? '';
                $verify = (strpos($stored, '$2y$') === 0 || strpos($stored, '$2a$') === 0)
                    ? password_verify($password, $stored)
                    : ($stored === $password);
                    
                if ($verify) {
                    $loggedIn = true;
                }
            }
            
            if ($loggedIn && $userData) {
                // Lưu session
                $_SESSION['user_id'] = $userData['MaUser'];
                $_SESSION['user_name'] = $userData['HoTen'];
                $_SESSION['user_role'] = $userData['VaiTro'];
                $_SESSION['user_avatar'] = $userData['Avatar'] ?? null;
                $_SESSION['logged_in'] = true;
                $_SESSION['login_type'] = $selectedRole;
                
                // Xử lý ghi nhớ đăng nhập
                if ($remember) {
                    // Tạo token ngẫu nhiên an toàn
                    $token = bin2hex(random_bytes(32)); // 64 ký tự hex
                    $hashedToken = hash('sha256', $token); // Hash token trước khi lưu DB
                    
                    // Xóa token cũ của user này với vai trò này
                    $roleEnum = $this->mapLoginTypeToRole($selectedRole);
                    $this->rememberTokenModel->deleteByUsernameAndRole($username, $roleEnum);
                    
                    // Lưu token vào database
                    $this->rememberTokenModel->TenDangNhap = $username;
                    $this->rememberTokenModel->Token = $hashedToken;
                    $this->rememberTokenModel->VaiTro = $roleEnum;
                    $this->rememberTokenModel->NgayHetHan = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60)); // 30 ngày
                    $this->rememberTokenModel->UserAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
                    $this->rememberTokenModel->IPAddress = $_SERVER['REMOTE_ADDR'] ?? '';
                    $this->rememberTokenModel->create();
                    
                    // Lưu token vào cookie
                    $cookieExpire = time() + (30 * 24 * 60 * 60);
                    setcookie('remember_token', $hashedToken, $cookieExpire, '/', '', false, true);
                } else {
                    // Xóa cookie và token nếu không tick
                    if (isset($_COOKIE['remember_token'])) {
                        $this->rememberTokenModel->deleteByToken($_COOKIE['remember_token']);
                        setcookie('remember_token', '', time() - 3600, '/');
                    }
                }
                
                // Kiểm tra xem user có cần đổi mật khẩu không
                if ($this->userModel->requiresPasswordChange($userData['MaUser'])) {
                    $_SESSION['require_password_change'] = true;
                    $_SESSION['temp_login'] = true;
                    header('Location: index.php?url=Auth/changePassword');
                    exit;
                }
                
                // Redirect theo role
                $this->redirectByRole($selectedRole, $userData['VaiTro']);
            } else {
                $this->showLoginError('Sai tên đăng nhập hoặc mật khẩu!');
            }
        } else {
            $this->index();
        }
    }
    
    /**
     * Map login type sang role enum
     */
    private function mapLoginTypeToRole($loginType) {
        $roleMap = [
            'admin' => 'Admin',
            'teacher' => 'GiangVien',
            'student' => 'SinhVien'
        ];
        return $roleMap[$loginType] ?? 'Admin';
    }
    
    /**
     * Redirect theo role đã chọn và phân quyền trong DB
     */
    private function redirectByRole($selectedRole, $userRole) {
        $baseUrl = defined('URLROOT') ? rtrim(URLROOT, '/') : '';
        $userRoleNorm = trim($userRole ?? '');
        $isGiangVien = (strcasecmp($userRoleNorm, 'GiangVien') === 0);
        $isAdmin = (strcasecmp($userRoleNorm, 'Admin') === 0 || strcasecmp($userRoleNorm, 'QuanLy') === 0);
        $isSinhVien = (strcasecmp($userRoleNorm, 'SinhVien') === 0);

        switch ($selectedRole) {
            case 'teacher':
                if ($isGiangVien) {
                    header('Location: ' . $baseUrl . '/GiangVien/dashboard');
                    exit;
                }
                $this->showLoginError('Bạn không có quyền đăng nhập với vai trò Giảng viên. Tài khoản của bạn không thuộc vai trò giảng viên.');
                return;
            case 'student':
                if ($isSinhVien) {
                    header('Location: ' . $baseUrl . '/SinhVien/dashboard');
                    exit;
                }
                $this->showLoginError('Bạn không có quyền đăng nhập với vai trò Sinh viên.');
                return;
            case 'admin':
            default:
                if ($isAdmin) {
                    header('Location: ' . $baseUrl . '/Home/index');
                    exit;
                }
                $this->showLoginError('Bạn không có quyền đăng nhập với vai trò Quản trị.');
                return;
        }
    }
    
    /**
     * Hiển thị lỗi đăng nhập
     */
    private function showLoginError($message) {
        $data = ['error' => $message];
        require_once __DIR__ . '/../views/auth/login.php';
    }
    
    /**
     * Kết thúc phiên khi đóng tab (gọi từ JavaScript sendBeacon)
     * Không redirect - chỉ hủy session để lần mở sau phải đăng nhập lại
     */
    public function sessionEnd() {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
        header('Content-Type: text/plain');
        echo 'OK';
        exit;
    }

    /**
     * Đăng xuất
     */
    public function logout() {
        // Xóa token "ghi nhớ đăng nhập" khỏi database
        if (isset($_COOKIE['remember_token'])) {
            $this->rememberTokenModel->deleteByToken($_COOKIE['remember_token']);
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        // Xóa tất cả session data
        $_SESSION = [];
        
        // Xóa session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        
        session_destroy();
        
        header('Location: index.php?url=Auth/index');
        exit;
    }
    
    /**
     * Hiển thị trang đổi mật khẩu bắt buộc
     */
    public function changePassword() {
        // Kiểm tra đã đăng nhập chưa
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            header('Location: index.php?url=Auth/index');
            exit;
        }
        
        // Kiểm tra có yêu cầu đổi mật khẩu không
        if (!isset($_SESSION['require_password_change']) || !$_SESSION['require_password_change']) {
            // Nếu không cần đổi mật khẩu, redirect về trang chính
            $this->redirectByRole($_SESSION['login_type'] ?? 'admin', $_SESSION['user_role']);
            return;
        }
        
        require_once __DIR__ . '/../views/auth/changepassword.php';
    }
    
    /**
     * Xử lý đổi mật khẩu bắt buộc
     */
    public function submitChangePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=Auth/changePassword');
            exit;
        }
        
        // Kiểm tra session
        if (!isset($_SESSION['logged_in']) || !isset($_SESSION['require_password_change'])) {
            header('Location: index.php?url=Auth/index');
            exit;
        }
        
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate
        if (empty($newPassword) || empty($confirmPassword)) {
            $error = 'Vui lòng nhập đầy đủ thông tin!';
            require_once __DIR__ . '/../views/auth/changepassword.php';
            return;
        }
        
        if (strlen($newPassword) < 6) {
            $error = 'Mật khẩu mới phải có ít nhất 6 ký tự!';
            require_once __DIR__ . '/../views/auth/changepassword.php';
            return;
        }
        
        if ($newPassword !== $confirmPassword) {
            $error = 'Mật khẩu xác nhận không khớp!';
            require_once __DIR__ . '/../views/auth/changepassword.php';
            return;
        }
        
        // Cập nhật mật khẩu và xóa flag
        $userId = $_SESSION['user_id'];
        $result = $this->userModel->updatePasswordAndClearFlag($userId, $newPassword);
        
        if ($result) {
            // Xóa session flag
            unset($_SESSION['require_password_change']);
            unset($_SESSION['temp_login']);
            
            // Redirect về trang chính theo role
            $_SESSION['flash_success'] = 'Đổi mật khẩu thành công! Bạn có thể sử dụng mật khẩu mới để đăng nhập.';
            $this->redirectByRole($_SESSION['login_type'] ?? 'admin', $_SESSION['user_role']);
        } else {
            $error = 'Có lỗi xảy ra. Vui lòng thử lại!';
            require_once __DIR__ . '/../views/auth/changepassword.php';
        }
    }
    
    /**
     * Bỏ qua đổi mật khẩu (giữ mật khẩu tạm)
     */
    public function skipChangePassword() {
        if (!isset($_SESSION['logged_in'])) {
            header('Location: index.php?url=Auth/index');
            exit;
        }
        
        // Xóa flag và cho phép tiếp tục
        unset($_SESSION['require_password_change']);
        unset($_SESSION['temp_login']);
        
        // Nhưng vẫn giữ cờ trong DB để nhắc lại lần sau
        $this->redirectByRole($_SESSION['login_type'] ?? 'admin', $_SESSION['user_role']);
    }
    
    /**
     * Hiển thị trang quên mật khẩu
     */
    public function forgotPassword() {
        require_once __DIR__ . '/../views/auth/forgotpassword.php';
    }
    
    /**
     * Xử lý yêu cầu quên mật khẩu - Gửi yêu cầu đến Admin
     */
    public function submitForgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?url=Auth/forgotPassword');
            exit;
        }
        
        // Load YeuCauDoiMatKhauModel
        require_once __DIR__ . '/../models/YeuCauDoiMatKhauModel.php';
        $db = new Database();
        $yeuCauModel = new YeuCauDoiMatKhauModel($db->getConnection());
        
        $email = trim($_POST['email'] ?? '');
        $lyDo = trim($_POST['lydo'] ?? '');
        
        if (empty($email)) {
            $error = 'Vui lòng nhập địa chỉ email!';
            require_once __DIR__ . '/../views/auth/forgotpassword.php';
            return;
        }
        
        // Kiểm tra định dạng email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Địa chỉ email không hợp lệ!';
            require_once __DIR__ . '/../views/auth/forgotpassword.php';
            return;
        }
        
        // Tìm user theo email
        $user = $this->userModel->getByEmail($email);
        
        if (!$user) {
            // Không tiết lộ email có tồn tại hay không (bảo mật)
            $success = 'Yêu cầu của bạn đã được gửi đến Quản trị viên. Nếu email này tồn tại trong hệ thống, bạn sẽ nhận được mật khẩu mới qua email sau khi Admin duyệt.';
            require_once __DIR__ . '/../views/auth/forgotpassword.php';
            return;
        }
        
        // Kiểm tra xem user đã có yêu cầu đang chờ xử lý chưa
        if ($yeuCauModel->hasRequestPending($user['MaUser'])) {
            $error = 'Bạn đã có một yêu cầu đang chờ xử lý. Vui lòng đợi Admin duyệt hoặc liên hệ trực tiếp với Quản trị viên.';
            require_once __DIR__ . '/../views/auth/forgotpassword.php';
            return;
        }
        
        // Tạo yêu cầu mới
        $requestData = [
            'MaUser' => $user['MaUser'],
            'TenDangNhap' => $user['TenDangNhap'],
            'Email' => $user['Email'],
            'HoTen' => $user['HoTen'],
            'VaiTro' => $user['VaiTro'],
            'LyDo' => $lyDo ?: 'Không cung cấp lý do'
        ];
        
        $result = $yeuCauModel->create($requestData);
        
        if ($result) {
            $success = 'Yêu cầu khôi phục mật khẩu đã được gửi thành công! Vui lòng chờ Admin xem xét và duyệt. Mật khẩu mới sẽ được gửi đến email của bạn.';
        } else {
            $error = 'Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại sau!';
        }
        
        require_once __DIR__ . '/../views/auth/forgotpassword.php';
    }
    
    /**
     * Che dấu email để bảo mật
     */
    private function maskEmail($email) {
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1];
        
        $maskedName = substr($name, 0, 2) . str_repeat('*', max(strlen($name) - 4, 2)) . substr($name, -2);
        return $maskedName . '@' . $domain;
    }
    
    /**
     * Tạo mật khẩu ngẫu nhiên
     */
    private function generateRandomPassword($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $password;
    }
    
    /**
     * Gửi email chứa mật khẩu mới qua SMTP
     */
    private function sendPasswordResetEmail($emailService, $toEmail, $fullName, $username, $newPassword) {
        $subject = '[UNISCORE] Mật khẩu mới của bạn';
        
        $htmlBody = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
        .header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; }
        .header p { margin: 10px 0 0; opacity: 0.9; }
        .content { padding: 30px; background: #ffffff; }
        .content h2 { color: #1e293b; margin-top: 0; }
        .info-row { background: #f8fafc; padding: 12px 15px; border-radius: 8px; margin: 15px 0; }
        .info-row strong { color: #475569; }
        .password-box { background: linear-gradient(135deg, #eff6ff, #dbeafe); border: 2px solid #3b82f6; border-radius: 12px; padding: 25px; text-align: center; margin: 25px 0; }
        .password-label { color: #64748b; font-size: 14px; margin: 0 0 10px; }
        .password { font-size: 32px; font-weight: bold; color: #1d4ed8; letter-spacing: 3px; margin: 0; font-family: monospace; }
        .warning { background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 0 8px 8px 0; padding: 15px 20px; margin: 25px 0; }
        .warning-title { color: #92400e; font-weight: bold; margin: 0 0 10px; }
        .warning ul { margin: 0; padding-left: 20px; color: #78350f; }
        .warning li { margin: 5px 0; }
        .footer { background: #f1f5f9; padding: 20px; text-align: center; font-size: 12px; color: #64748b; }
        .footer p { margin: 5px 0; }
        .btn { display: inline-block; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 UNISCORE</h1>
            <p>Hệ thống Quản lý Điểm Sinh viên</p>
        </div>
        <div class="content">
            <h2>Xin chào ' . htmlspecialchars($fullName) . ',</h2>
            <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn trên hệ thống UNISCORE.</p>
            
            <div class="info-row">
                <strong>👤 Tên đăng nhập:</strong> ' . htmlspecialchars($username) . '
            </div>
            
            <div class="password-box">
                <p class="password-label">🔐 Mật khẩu mới của bạn là:</p>
                <p class="password">' . htmlspecialchars($newPassword) . '</p>
            </div>
            
            <div class="warning">
                <p class="warning-title">⚠️ Lưu ý quan trọng:</p>
                <ul>
                    <li>Vui lòng <strong>đăng nhập và đổi mật khẩu ngay</strong> sau khi nhận được email này</li>
                    <li>Không chia sẻ mật khẩu này với bất kỳ ai</li>
                    <li>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng liên hệ Quản trị viên ngay</li>
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
        
        // Gửi email qua SMTP
        return $emailService->send($toEmail, $subject, base64_encode($htmlBody), $fullName);
    }
}
