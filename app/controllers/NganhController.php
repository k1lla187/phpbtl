<?php
/**
 * NganhController - Quản lý Ngành
 */
require_once __DIR__ . '/../core/Controller.php';

class NganhController extends Controller {
    private $nganhModel;
    private $khoaModel;

    public function __construct() {
        parent::__construct();
        $this->nganhModel = $this->model('NganhModel');
        $this->khoaModel = $this->model('KhoaModel');
    }

    private function buildIndexData($error = '', $success = '') {
        return [
            'nganhs' => $this->nganhModel->readAll(),
            'khoas' => $this->khoaModel->readAll(),
            'pageTitle' => 'Quản lý Ngành',
            'breadcrumb' => 'Ngành',
            'error' => $error,
            'success' => $success
        ];
    }

    public function index() {
        $data = $this->buildIndexData();
        require_once __DIR__ . '/../views/admin/nganh/index.php';
    }

    public function store() {
        if (!$this->isPost()) {
            $this->redirect('Nganh/index');
        }

        $input = [
            'MaNganh' => $this->getPost('MaNganh'),
            'TenNganh' => $this->getPost('TenNganh'),
            'MaKhoa' => $this->getPost('MaKhoa'),
        ];

        $errors = $this->validate($input, [
            'MaNganh' => 'required|max:20',
            'TenNganh' => 'required|max:100'
        ]);

        if (!empty($errors)) {
            $data = $this->buildIndexData(implode(' ', $errors));
            require_once __DIR__ . '/../views/admin/nganh/index.php';
            return;
        }

        $this->nganhModel->MaNganh = $input['MaNganh'];
        $this->nganhModel->TenNganh = $input['TenNganh'];
        $this->nganhModel->MaKhoa = $input['MaKhoa'] ?: null;

        $result = $this->nganhModel->create();
        if ($result === true) {
            $this->redirect('Nganh/index');
        }

        $data = $this->buildIndexData($result);
        require_once __DIR__ . '/../views/admin/nganh/index.php';
    }

    public function edit($id) {
        $nganh = $this->nganhModel->getById($id);
        if (!$nganh) {
            $this->redirect('Nganh/index');
        }

        $data = [
            'nganh' => $nganh,
            'khoas' => $this->khoaModel->readAll(),
            'pageTitle' => 'Sửa ngành',
            'breadcrumb' => 'Sửa ngành',
            'error' => ''
        ];
        require_once __DIR__ . '/../views/admin/nganh/edit.php';
    }

    public function update($id) {
        if (!$this->isPost()) {
            $this->redirect('Nganh/index');
        }

        $input = [
            'TenNganh' => $this->getPost('TenNganh'),
            'MaKhoa' => $this->getPost('MaKhoa'),
        ];

        $errors = $this->validate($input, [
            'TenNganh' => 'required|max:100'
        ]);

        if (!empty($errors)) {
            $data = [
                'nganh' => array_merge(['MaNganh' => $id], $input),
                'khoas' => $this->khoaModel->readAll(),
                'pageTitle' => 'Sửa ngành',
                'breadcrumb' => 'Sửa ngành',
                'error' => implode(' ', $errors)
            ];
            require_once __DIR__ . '/../views/admin/nganh/edit.php';
            return;
        }

        $this->nganhModel->MaNganh = $id;
        $this->nganhModel->TenNganh = $input['TenNganh'];
        $this->nganhModel->MaKhoa = $input['MaKhoa'] ?: null;

        $result = $this->nganhModel->update();
        if ($result === true) {
            $this->redirect('Nganh/index');
        }

        $data = [
            'nganh' => array_merge(['MaNganh' => $id], $input),
            'khoas' => $this->khoaModel->readAll(),
            'pageTitle' => 'Sửa ngành',
            'breadcrumb' => 'Sửa ngành',
            'error' => $result
        ];
        require_once __DIR__ . '/../views/admin/nganh/edit.php';
    }

    public function delete($id) {
        $this->nganhModel->MaNganh = $id;
        $result = $this->nganhModel->delete();
        
        if ($result !== true) {
            $data = $this->buildIndexData($result);
            require_once __DIR__ . '/../views/admin/nganh/index.php';
            return;
        }
        
        $this->redirect('Nganh/index');
    }
}