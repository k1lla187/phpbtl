<?php
/**
 * ProfileController - Hồ sơ cá nhân, đổi mật khẩu (cho cả 3 roles)
 */
require_once __DIR__ . '/../core/Controller.php';

class ProfileController extends Controller {
    public function index() {
        $baseUrl = defined('URLROOT') ? rtrim(URLROOT, '/') : '';
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: ' . $baseUrl . '/Auth/index');
            exit;
        }
        $userModel = $this->model('UserModel');
        $user = $userModel->getById($_SESSION['user_id']);
        if (!$user) {
            session_destroy();
            header('Location: ' . $baseUrl . '/Auth/index');
            exit;
        }
        $_SESSION['user_avatar'] = $user['Avatar'] ?? null;
        $data = [
            'user' => $user,
            'error' => '',
            'success' => '',
            'pageTitle' => 'Hồ sơ cá nhân',
            'breadcrumb' => 'Hồ sơ'
        ];
        $loginType = $_SESSION['login_type'] ?? 'admin';
        if ($loginType === 'teacher') {
            require_once __DIR__ . '/../views/profile/giangvien.php';
        } elseif ($loginType === 'student') {
            require_once __DIR__ . '/../views/profile/sinhvien.php';
        } else {
            $data['pageTitle'] = 'Hồ sơ cá nhân';
            require_once __DIR__ . '/../views/admin/layouts/header.php';
            require_once __DIR__ . '/../views/profile/form.php';
            require_once __DIR__ . '/../views/admin/layouts/footer.php';
        }
    }

    public function update() {
        $baseUrl = defined('URLROOT') ? rtrim(URLROOT, '/') : '';
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: ' . $baseUrl . '/Auth/index');
            exit;
        }
        if (!$this->isPost()) {
            header('Location: ' . $baseUrl . '/Profile/index');
            exit;
        }
        $userModel = $this->model('UserModel');
        $user = $userModel->getById($_SESSION['user_id']);
        if (!$user) {
            header('Location: ' . $baseUrl . '/Auth/index');
            exit;
        }
        $hoTen = trim($this->getPost('HoTen'));
        $email = trim($this->getPost('Email'));
        $soDienThoai = trim($this->getPost('SoDienThoai'));
        if (empty($hoTen)) {
            $this->showProfileError('Họ tên không được để trống.');
            return;
        }
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->showProfileError('Email không đúng định dạng.');
            return;
        }
        if ($userModel->existsByEmail($email, $_SESSION['user_id'])) {
            $this->showProfileError('Email đã được sử dụng bởi tài khoản khác.');
            return;
        }
        $avatarPath = $user['Avatar'] ?? null;
        if (!empty($_FILES['avatar']['name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['avatar']['tmp_name']);
            finfo_close($finfo);
            if (in_array($mime, $allowed) && $_FILES['avatar']['size'] <= 2 * 1024 * 1024) {
                $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION) ?: 'jpg';
                $filename = 'avt_' . $_SESSION['user_id'] . '_' . time() . '.' . strtolower($ext);
                $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/avatars/';
                if (is_dir($uploadDir) && move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $filename)) {
                    if ($avatarPath && file_exists($uploadDir . basename($avatarPath))) {
                        @unlink($uploadDir . basename($avatarPath));
                    }
                    $avatarPath = 'uploads/avatars/' . $filename;
                }
            }
        }
        $userModel->MaUser = $_SESSION['user_id'];
        $userModel->TenDangNhap = $user['TenDangNhap'];
        $userModel->HoTen = $hoTen;
        $userModel->Email = $email ?: null;
        $userModel->SoDienThoai = $soDienThoai ?: null;
        $userModel->VaiTro = $user['VaiTro'];
        $userModel->TrangThai = $user['TrangThai'] ?? 1;
        $userModel->Avatar = $avatarPath;
        $userModel->MatKhau = '';
        if ($userModel->update()) {
            $_SESSION['user_name'] = $hoTen;
            $_SESSION['user_avatar'] = $avatarPath;
            $this->showProfileSuccess('Cập nhật hồ sơ thành công!');
        } else {
            $this->showProfileError('Không thể cập nhật. Vui lòng thử lại.');
        }
    }

    public function changePassword() {
        $baseUrl = defined('URLROOT') ? rtrim(URLROOT, '/') : '';
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: ' . $baseUrl . '/Auth/index');
            exit;
        }
        if (!$this->isPost()) {
            header('Location: ' . $baseUrl . '/Profile/index');
            exit;
        }
        $matKhauCu = $this->getPost('matKhauCu');
        $matKhauMoi = $this->getPost('matKhauMoi');
        $matKhauXacNhan = $this->getPost('matKhauXacNhan');
        if (empty($matKhauCu) || empty($matKhauMoi) || empty($matKhauXacNhan)) {
            $this->showProfileError('Vui lòng nhập đầy đủ mật khẩu.');
            return;
        }
        if (strlen($matKhauMoi) < 6) {
            $this->showProfileError('Mật khẩu mới phải có ít nhất 6 ký tự.');
            return;
        }
        if ($matKhauMoi !== $matKhauXacNhan) {
            $this->showProfileError('Mật khẩu xác nhận không khớp.');
            return;
        }
        $userModel = $this->model('UserModel');
        $user = $userModel->getById($_SESSION['user_id']);
        if (!$user) {
            header('Location: ' . $baseUrl . '/Auth/index');
            exit;
        }
        $stored = $user['MatKhau'] ?? '';
        $verify = (strpos($stored, '$2y$') === 0 || strpos($stored, '$2a$') === 0)
            ? password_verify($matKhauCu, $stored)
            : ($stored === $matKhauCu);
        if (!$verify) {
            $this->showProfileError('Mật khẩu hiện tại không đúng.');
            return;
        }
        $userModel->MaUser = $_SESSION['user_id'];
        $userModel->TenDangNhap = $user['TenDangNhap'];
        $userModel->HoTen = $user['HoTen'];
        $userModel->Email = $user['Email'] ?? null;
        $userModel->SoDienThoai = $user['SoDienThoai'] ?? null;
        $userModel->VaiTro = $user['VaiTro'];
        $userModel->TrangThai = $user['TrangThai'] ?? 1;
        $userModel->MatKhau = $matKhauMoi;
        if ($userModel->update()) {
            $this->showProfileSuccess('Đổi mật khẩu thành công!');
        } else {
            $this->showProfileError('Không thể đổi mật khẩu. Vui lòng thử lại.');
        }
    }

    private function showProfileError($msg) {
        $userModel = $this->model('UserModel');
        $user = $userModel->getById($_SESSION['user_id']);
        $data = ['user' => $user ?: [], 'error' => $msg, 'success' => '', 'pageTitle' => 'Hồ sơ', 'breadcrumb' => 'Hồ sơ'];
        $loginType = $_SESSION['login_type'] ?? 'admin';
        if ($loginType === 'teacher') {
            require_once __DIR__ . '/../views/profile/giangvien.php';
        } elseif ($loginType === 'student') {
            require_once __DIR__ . '/../views/profile/sinhvien.php';
        } else {
            require_once __DIR__ . '/../views/admin/layouts/header.php';
            require_once __DIR__ . '/../views/profile/form.php';
            require_once __DIR__ . '/../views/admin/layouts/footer.php';
        }
    }

    public function settings() {
        $baseUrl = defined('URLROOT') ? rtrim(URLROOT, '/') : '';
        if (empty($_SESSION['logged_in'])) {
            header('Location: ' . $baseUrl . '/Auth/index');
            exit;
        }
        $loginType = $_SESSION['login_type'] ?? 'admin';
        require_once __DIR__ . '/../views/profile/settings.php';
    }

    private function showProfileSuccess($msg) {
        $userModel = $this->model('UserModel');
        $user = $userModel->getById($_SESSION['user_id']);
        $data = ['user' => $user ?: [], 'error' => '', 'success' => $msg, 'pageTitle' => 'Hồ sơ', 'breadcrumb' => 'Hồ sơ'];
        $loginType = $_SESSION['login_type'] ?? 'admin';
        if ($loginType === 'teacher') {
            require_once __DIR__ . '/../views/profile/giangvien.php';
        } elseif ($loginType === 'student') {
            require_once __DIR__ . '/../views/profile/sinhvien.php';
        } else {
            require_once __DIR__ . '/../views/admin/layouts/header.php';
            require_once __DIR__ . '/../views/profile/form.php';
            require_once __DIR__ . '/../views/admin/layouts/footer.php';
        }
    }
}
