<?php
class App {
    // Default: Chuyển đến trang đăng nhập
    protected $controller = 'AuthController';
    protected $method = 'index';
    protected $params = [];
    
    // Controllers không cần đăng nhập
    protected $publicControllers = ['Auth'];
    // Controllers chỉ dành cho Admin (không cho Sinh viên/Giảng viên)
    protected $adminOnlyControllers = ['Home', 'Khoa', 'Nganh', 'HocKy', 'LopHanhChinh', 'MonHoc', 'LopHocPhan', 'Diem', 'DangKyHoc', 'User', 'ThongKe', 'CauTrucDiem', 'ChiTietDiem', 'LoaiDiem'];

    public function __construct() {
        $url = $this->parseUrl();
        $requestedController = $url[0] ?? 'Auth';
        
        // Kiểm tra đăng nhập
        $isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
        $isPublicController = in_array($requestedController, $this->publicControllers);
        
        // Nếu chưa đăng nhập và không phải trang public -> redirect về login
        if (!$isLoggedIn && !$isPublicController) {
            header('Location: index.php?url=Auth/index');
            exit;
        }
        
        // Nếu đã đăng nhập và đang ở trang Auth -> redirect về dashboard đúng theo role đã chọn
        if ($isLoggedIn && $requestedController === 'Auth' && ($url[1] ?? 'index') === 'index') {
            $this->redirectToRoleDashboard();
        }

        // Đồng bộ avatar từ DB mỗi request (tránh mất ảnh khi đăng xuất rồi đăng nhập lại)
        if ($isLoggedIn && !empty($_SESSION['user_id'])) {
            try {
                require_once __DIR__ . '/../config/Database.php';
                require_once __DIR__ . '/../models/UserModel.php';
                $db = new Database();
                $userModel = new UserModel($db->getConnection());
                $user = $userModel->getById($_SESSION['user_id']);
                if ($user) {
                    $_SESSION['user_name'] = $user['HoTen'] ?? $_SESSION['user_name'];
                    if (array_key_exists('Avatar', $user)) {
                        $_SESSION['user_avatar'] = !empty($user['Avatar']) ? $user['Avatar'] : null;
                    }
                    
                    // Kiểm tra trạng thái tài khoản - nếu bị vô hiệu hóa thì logout
                    $trangThai = $user['TrangThai'] ?? 1;
                    if ($trangThai == 0 || $trangThai === '0') {
                        // Xóa session và chuyển về login với thông báo
                        $_SESSION = [];
                        if (ini_get('session.use_cookies')) {
                            $params = session_get_cookie_params();
                            setcookie(session_name(), '', time() - 42000,
                                $params['path'], $params['domain'],
                                $params['secure'], $params['httponly']
                            );
                        }
                        session_destroy();
                        header('Location: index.php?url=Auth/index&error=account_disabled');
                        exit;
                    }
                }
            } catch (Throwable $e) {
                // Bỏ qua lỗi (vd: cột Avatar chưa tồn tại trong DB)
            }
        }

        // Phân quyền: Sinh viên/Giảng viên không được truy cập khu vực Admin
        if ($isLoggedIn) {
            $loginType = $_SESSION['login_type'] ?? 'admin';
            $isAdminOnly = in_array($requestedController, $this->adminOnlyControllers);
            $isAdminMethod = ($requestedController === 'SinhVien' || $requestedController === 'GiangVien') 
                && in_array($url[1] ?? 'dashboard', ['index', 'store', 'edit', 'delete', 'create']);
            if (($loginType === 'student' || $loginType === 'teacher') && ($isAdminOnly || $isAdminMethod)) {
                $this->redirectToRoleDashboard();
            }
        }

        // Kiểm tra file controller
        if ($url != null && file_exists('../app/controllers/' . ucfirst($url[0]) . 'Controller.php')) {
            $this->controller = ucfirst($url[0]) . 'Controller';
            unset($url[0]);
        }

        require_once '../app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // Kiểm tra method
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        $this->params = $url ? array_values($url) : [];

        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseUrl() {
        if (isset($_GET['url']) && trim($_GET['url']) !== '') {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        // Mặc định: trang đăng nhập khi truy cập public/index.php hoặc public/
        return ['Auth'];
    }

    /**
     * Redirect về dashboard đúng theo login_type (role đã chọn khi đăng nhập)
     */
    private function redirectToRoleDashboard() {
        $baseUrl = defined('URLROOT') ? rtrim(URLROOT, '/') : '';
        $loginType = $_SESSION['login_type'] ?? 'admin';
        switch ($loginType) {
            case 'student':
                header('Location: ' . $baseUrl . '/SinhVien/dashboard');
                exit;
            case 'teacher':
                header('Location: ' . $baseUrl . '/GiangVien/dashboard');
                exit;
            case 'admin':
            default:
                header('Location: ' . ($baseUrl ? $baseUrl . '/Home/index' : 'index.php?url=Home/index'));
                exit;
        }
    }
}
?>