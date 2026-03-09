<?php
/**
 * DangKyHocController - Quản lý Đăng ký học
 */
require_once __DIR__ . '/../core/Controller.php';

class DangKyHocController extends Controller {
    private $dkhModel;
    private $svModel;
    private $lhpModel;

    public function __construct() {
        parent::__construct();
        $this->dkhModel = $this->model('DangKyHocModel');
        $this->svModel = $this->model('SinhVienModel');
        $this->lhpModel = $this->model('LopHocPhanModel');
    }

    private function buildIndexData($error = '', $success = '') {
        return [
            'dangkyhocs' => $this->dkhModel->readAllWithDetails(),
            'sinhviens' => $this->svModel->readAll(),
            'lophocphans' => $this->lhpModel->readAll(),
            'pageTitle' => 'Quản lý Đăng ký học',
            'breadcrumb' => 'Đăng ký học',
            'error' => $error,
            'success' => $success
        ];
    }

    public function index() {
        $data = $this->buildIndexData();
        require_once __DIR__ . '/../views/admin/dangkyhoc/index.php';
    }

    public function store() {
        if (!$this->isPost()) {
            $this->redirect('DangKyHoc/index');
        }

        $input = [
            'MaSinhVien' => $this->getPost('MaSinhVien'),
            'MaLopHocPhan' => $this->getPost('MaLopHocPhan'),
        ];

        $errors = $this->validate($input, [
            'MaSinhVien' => 'required',
            'MaLopHocPhan' => 'required'
        ]);

        if (!empty($errors)) {
            $data = $this->buildIndexData(implode(' ', $errors));
            require_once __DIR__ . '/../views/admin/dangkyhoc/index.php';
            return;
        }

        $this->dkhModel->MaSinhVien = $input['MaSinhVien'];
        $this->dkhModel->MaLopHocPhan = $input['MaLopHocPhan'];

        $result = $this->dkhModel->create();
        if ($result === true) {
            $this->redirect('DangKyHoc/index');
        }

        $data = $this->buildIndexData($result);
        require_once __DIR__ . '/../views/admin/dangkyhoc/index.php';
    }

    public function delete($id) {
        $this->dkhModel->MaDangKy = $id;
        $result = $this->dkhModel->delete();
        
        if ($result !== true) {
            $data = $this->buildIndexData($result);
            require_once __DIR__ . '/../views/admin/dangkyhoc/index.php';
            return;
        }
        
        $this->redirect('DangKyHoc/index');
    }
}