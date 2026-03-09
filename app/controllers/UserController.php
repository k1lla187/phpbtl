<?php
/**
 * UserController - Quản lý Tài khoản
 */
require_once __DIR__ . '/../core/Controller.php';

class UserController extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = $this->model('UserModel');
    }

    /**
     * Map vai trò sang định dạng chuẩn
     */
    private function mapRole($r) {
        $r = strtolower($r ?? '');
        if ($r === 'admin') return 'Admin';
        if ($r === 'teacher' || $r === 'gv' || $r === 'giangvien') return 'GiangVien';
        if ($r === 'student' || $r === 'sv' || $r === 'sinhvien') return 'SinhVien';
        if ($r === 'quanly') return 'QuanLy';
        return $r ?: 'SinhVien';
    }

/**
     * Thiết lập thông báo nhanh (Flash Message)
     */
    protected function setFlash($type, $msg) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['flash_type'] = $type;
        $_SESSION['flash_message'] = $msg;
    }

    /**
     * Lấy thông báo nhanh
     */
    protected function getFlash() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $type = $_SESSION['flash_type'] ?? null;
        $msg = $_SESSION['flash_message'] ?? null;
        unset($_SESSION['flash_type'], $_SESSION['flash_message']);
        return $type && $msg ? ['type' => $type, 'message' => $msg] : null;
    }

    /**
     * Xây dựng dữ liệu index
     */
    private function buildIndexData() {
        $users = $this->userModel->readAll();
        $users = is_array($users) ? $users : [];
        
        $totalAdmin = $totalTeacher = $totalStudent = 0;
        foreach ($users as $u) {
            $v = strtolower($u['VaiTro'] ?? '');
            if ($v === 'admin') $totalAdmin++;
            elseif ($v === 'giangvien') $totalTeacher++;
            else $totalStudent++;
        }
        
        return [
            'users' => $users,
            'totalUsers' => count($users),
            'totalAdmin' => $totalAdmin,
            'totalTeacher' => $totalTeacher,
            'totalStudent' => $totalStudent,
            'pageTitle' => 'Quản lý Tài khoản',
            'breadcrumb' => 'Tài khoản',
            'flash' => $this->getFlash(),
        ];
    }

    public function index() {
        $data = $this->buildIndexData();
        require_once __DIR__ . '/../views/admin/user/index.php';
    }

    public function store() {
        if (!$this->isPost()) {
            $this->redirect('User/index');
        }

        $username = $this->getPost('TenDangNhap') ?: $this->getPost('Username');
        $pw = $this->getPost('MatKhau') ?: $this->getPost('Password');
        $email = $this->getPost('Email');
        $vaiTro = $this->mapRole($this->getPost('VaiTro') ?: $this->getPost('Role') ?: 'SinhVien');

        // Validation
        if (empty($username)) {
            $this->setFlash('danger', 'Tên đăng nhập không được để trống.');
            $this->redirect('User/index');
        }
        if (empty($pw)) {
            $this->setFlash('danger', 'Mật khẩu không được để trống.');
            $this->redirect('User/index');
        }
        if (strlen($pw) < 6) {
            $this->setFlash('danger', 'Mật khẩu phải có ít nhất 6 ký tự.');
            $this->redirect('User/index');
        }
        if ($this->userModel->existsByTenDangNhap($username)) {
            $this->setFlash('danger', 'Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.');
            $this->redirect('User/index');
        }
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('danger', 'Email không đúng định dạng.');
            $this->redirect('User/index');
        }
        if (!empty($email) && $this->userModel->existsByEmail($email)) {
            $this->setFlash('danger', 'Email đã được sử dụng bởi tài khoản khác.');
            $this->redirect('User/index');
        }

        $this->userModel->TenDangNhap = $username;
        $this->userModel->MatKhau = password_hash($pw, PASSWORD_DEFAULT);
        $this->userModel->HoTen = $this->getPost('HoTen');
        $this->userModel->Email = $email ?: null;
        $this->userModel->SoDienThoai = $this->getPost('SoDienThoai');
        $this->userModel->VaiTro = $vaiTro;
        $this->userModel->TrangThai = $this->getPost('TrangThai') ? 1 : 0;
        $this->userModel->NgayTao = date('Y-m-d H:i:s');
        $this->userModel->NgayCapNhat = date('Y-m-d H:i:s');

        $result = $this->userModel->create();
        if ($result === true) {
            $this->setFlash('success', 'Thêm tài khoản thành công.');
        } else {
            $this->setFlash('danger', is_string($result) ? $result : 'Không thể thêm tài khoản. Vui lòng thử lại.');
        }
        
        $this->redirect('User/index');
    }

    public function edit($id) {
        $id = trim($id);
        if ($id === '') {
            $this->setFlash('danger', 'Mã tài khoản không hợp lệ.');
            $this->redirect('User/index');
        }
        
        $user = $this->userModel->getById($id);
        if (!$user) {
            $this->setFlash('danger', 'Không tìm thấy tài khoản.');
            $this->redirect('User/index');
        }
        
        $data = [
            'user' => $user,
            'pageTitle' => 'Sửa tài khoản',
            'breadcrumb' => 'Sửa tài khoản',
            'flash' => $this->getFlash(),
            'error' => ''
        ];
        require_once __DIR__ . '/../views/admin/user/edit.php';
    }

    public function update($id) {
        if (!$this->isPost()) {
            $this->redirect('User/index');
        }
        
        $id = trim($id);
        if ($id === '') {
            $this->setFlash('danger', 'Mã tài khoản không hợp lệ.');
            $this->redirect('User/index');
        }
        
        $user = $this->userModel->getById($id);
        if (!$user) {
            $this->setFlash('danger', 'Không tìm thấy tài khoản cần cập nhật.');
            $this->redirect('User/index');
        }

        $username = $this->getPost('TenDangNhap');
        $pw = $this->getPost('MatKhau');
        $email = $this->getPost('Email');
        $vaiTro = $this->mapRole($this->getPost('VaiTro') ?: 'SinhVien');

        // Validation
        if (empty($username)) {
            $this->setFlash('danger', 'Tên đăng nhập không được để trống.');
            $this->redirect('User/edit/' . $id);
        }
        if ($this->userModel->existsByTenDangNhap($username, $id)) {
            $this->setFlash('danger', 'Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.');
            $this->redirect('User/edit/' . $id);
        }
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('danger', 'Email không đúng định dạng.');
            $this->redirect('User/edit/' . $id);
        }
        if (!empty($email) && $this->userModel->existsByEmail($email, $id)) {
            $this->setFlash('danger', 'Email đã được sử dụng bởi tài khoản khác.');
            $this->redirect('User/edit/' . $id);
        }
        if (!empty($pw) && strlen($pw) < 6) {
            $this->setFlash('danger', 'Mật khẩu mới phải có ít nhất 6 ký tự.');
            $this->redirect('User/edit/' . $id);
        }

        $this->userModel->MaUser = $id;
        $this->userModel->TenDangNhap = $username;
        $this->userModel->HoTen = $this->getPost('HoTen');
        $this->userModel->Email = $email ?: null;
        $this->userModel->SoDienThoai = $this->getPost('SoDienThoai');
        $this->userModel->VaiTro = $vaiTro;
        $this->userModel->TrangThai = $this->getPost('TrangThai') ? 1 : 0;

        if (!empty($pw)) {
            $this->userModel->MatKhau = password_hash($pw, PASSWORD_DEFAULT);
            $result = $this->userModel->update();
        } else {
            $result = $this->userModel->updateWithoutPassword();
        }

        if ($result === true) {
            $this->setFlash('success', 'Cập nhật tài khoản thành công.');
        } else {
            $this->setFlash('danger', is_string($result) ? $result : 'Không thể cập nhật. Vui lòng thử lại.');
        }
        
        $this->redirect('User/index');
    }

    public function delete($id) {
        $id = trim($id);
        if ($id === '') {
            $this->setFlash('danger', 'Mã tài khoản không hợp lệ.');
            $this->redirect('User/index');
        }
        
        $user = $this->userModel->getById($id);
        if (!$user) {
            $this->setFlash('danger', 'Không tìm thấy tài khoản cần xóa.');
            $this->redirect('User/index');
        }
        
        if (strtolower($user['VaiTro'] ?? '') === 'admin') {
            $this->setFlash('danger', 'Không được xóa tài khoản Admin.');
            $this->redirect('User/index');
        }

        $this->userModel->MaUser = $id;
        $result = $this->userModel->delete();
        
        if ($result === true) {
            $this->setFlash('success', 'Đã xóa tài khoản.');
        } else {
            $this->setFlash('danger', is_string($result) ? $result : 'Không thể xóa tài khoản. Vui lòng thử lại.');
        }
        
        $this->redirect('User/index');
    }
}