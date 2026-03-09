<?php
/**
 * Base Controller Class
 * Cung cấp các phương thức chung cho tất cả Controllers
 */
class Controller {
    protected $db;

    public function __construct() {
        require_once __DIR__ . '/../config/Database.php';
        $database = new Database();
        $this->db = $database->getConnection();
    } // Đóng hàm __construct tại đây

    /**
     * Thiết lập thông báo nhanh (Flash Message)
     */
    protected function setFlash($type, $msg) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['flash_type'] = $type;
        $_SESSION['flash_message'] = $msg;
    }

    /**
     * Lấy và xóa thông báo nhanh sau khi hiển thị
     */
    protected function getFlash() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $type = $_SESSION['flash_type'] ?? null;
        $msg = $_SESSION['flash_message'] ?? null;
        unset($_SESSION['flash_type'], $_SESSION['flash_message']);
        return $type && $msg ? ['type' => $type, 'message' => $msg] : null;
    }

    /**
     * Load model
     * @param string $model Tên model cần load
     * @return object Model instance
     */
    public function model(string $model): object {
        $modelFile = __DIR__ . '/../models/' . $model . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model($this->db);
        }
        throw new \RuntimeException("Model file $modelFile not found.");
    }

    /**
     * Load view
     */
    public function view($view, $data = []) {
        $viewFile = __DIR__ . '/../views/admin/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View file $viewFile not found.");
        }
    }

    /**
     * Redirect to another URL
     */
    protected function redirect($url) {
        header("Location: index.php?url=$url");
        exit;
    }

    /**
     * Check if user is logged in
     */
    protected function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Check if user has required role
     */
    protected function hasRole($role) {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
    }

    /**
     * Require authentication
     */
    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->redirect('Auth/index');
        }
    }

    /**
     * Require specific role
     */
    protected function requireRole($role) {
        $this->requireLogin();
        if (!$this->hasRole($role)) {
            $this->redirect('Home/index');
        }
    }

    /**
     * Kiểm tra tài khoản có đang hoạt động không
     * Nếu tài khoản bị vô hiệu hóa thì tự động logout
     */
    protected function checkAccountStatus() {
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            return true; // Chưa đăng nhập, không cần kiểm tra
        }
        
        if (!isset($_SESSION['user_id'])) {
            return true;
        }
        
        // Lấy thông tin user từ database để kiểm tra trạng thái
        $userModel = $this->model('UserModel');
        $user = $userModel->getById($_SESSION['user_id']);
        
        if (!$user) {
            // User không tồn tại, logout
            $this->forceLogout();
            return false;
        }
        
        $trangThai = $user['TrangThai'] ?? 1;
        
        // Nếu tài khoản bị vô hiệu hóa (TrangThai = 0)
        if ($trangThai == 0 || $trangThai === '0') {
            $this->forceLogout();
            return false;
        }
        
        return true;
    }

    /**
     * Buộc logout và chuyển về trang đăng nhập
     */
    protected function forceLogout() {
        // Xóa tất cả session
        $_SESSION = [];
        
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        
        session_destroy();
        
        // Chuyển về trang đăng nhập với thông báo
        header("Location: index.php?url=Auth/index&error=account_disabled");
        exit;
    }

    /**
     * Validate input data
     */
    protected function validate($data, $rules) {
        $errors = [];
        foreach ($rules as $field => $ruleSet) {
            $value = $data[$field] ?? '';
            $fieldRules = explode('|', $ruleSet);
            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && empty(trim($value))) {
                    $errors[$field] = "Trường này là bắt buộc!";
                    break;
                }
                if (strpos($rule, 'min:') === 0 && !empty($value)) {
                    $min = (int) substr($rule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field] = "Phải có ít nhất $min ký tự!";
                        break;
                    }
                }
                if (strpos($rule, 'max:') === 0 && !empty($value)) {
                    $max = (int) substr($rule, 4);
                    if (strlen($value) > $max) {
                        $errors[$field] = "Không được vượt quá $max ký tự!";
                        break;
                    }
                }
                if ($rule === 'email' && !empty($value)) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[$field] = "Email không đúng định dạng!";
                        break;
                    }
                }
                if ($rule === 'phone' && !empty($value)) {
                    if (!preg_match('/^[0-9]{9,11}$/', $value)) {
                        $errors[$field] = "Số điện thoại không hợp lệ!";
                        break;
                    }
                }
                if ($rule === 'numeric' && !empty($value)) {
                    if (!is_numeric($value)) {
                        $errors[$field] = "Phải là số!";
                        break;
                    }
                }
            }
        }
        return $errors;
    }

    /**
     * Validate ngày sinh - kiểm tra tuổi tối thiểu
     * @param string $dob Ngày sinh (Y-m-d)
     * @param int $minAge Tuổi tối thiểu
     * @return string|null Thông báo lỗi hoặc null nếu hợp lệ
     */
    protected function validateDob($dob, $minAge = 16) {
        if (empty($dob)) return null;
        $birthDate = \DateTime::createFromFormat('Y-m-d', $dob);
        if (!$birthDate) {
            return 'Ngày sinh không đúng định dạng (dd/mm/yyyy hoặc yyyy-mm-dd)!';
        }
        $today = new \DateTime();
        $age = $today->diff($birthDate)->y;
        if ($age < $minAge) {
            return "Phải đủ {$minAge} tuổi trở lên!";
        }
        if ($birthDate > $today) {
            return 'Ngày sinh không được ở tương lai!';
        }
        return null;
    }

    protected function sanitize($value) {
        if ($value === null || $value === '') {
            return null;
        }
        return htmlspecialchars(strip_tags(trim($value)));
    }

    protected function getPost($key, $default = '') {
        return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
    }

    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function getDb() {
        return $this->db;
    }
}