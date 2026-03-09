<?php
/**
 * KhoaController - Quản lý Khoa
 */
require_once __DIR__ . '/../core/Controller.php';

class KhoaController extends Controller {
    private $khoaModel;

    public function __construct() {
        parent::__construct();
        $this->khoaModel = $this->model('KhoaModel');
    }

    private function buildIndexData($error = '', $success = '') {
        return [
            'khoas' => $this->khoaModel->readAll(),
            'pageTitle' => 'Quản lý Khoa',
            'breadcrumb' => 'Khoa',
            'error' => $error,
            'success' => $success
        ];
    }

    public function index() {
        $data = $this->buildIndexData();
        require_once __DIR__ . '/../views/admin/khoa/index.php';
    }

    public function store() {
        if (!$this->isPost()) {
            $this->redirect('Khoa/index');
        }

        $input = [
            'MaKhoa' => $this->getPost('MaKhoa'),
            'TenKhoa' => $this->getPost('TenKhoa'),
            'NgayThanhLap' => $this->getPost('NgayThanhLap'),
            'TruongKhoa' => $this->getPost('TruongKhoa'),
        ];

        $errors = $this->validate($input, [
            'MaKhoa' => 'required|max:20',
            'TenKhoa' => 'required|max:100'
        ]);

        if (!empty($errors)) {
            $data = $this->buildIndexData(implode(' ', $errors));
            require_once __DIR__ . '/../views/admin/khoa/index.php';
            return;
        }

        $this->khoaModel->MaKhoa = $input['MaKhoa'];
        $this->khoaModel->TenKhoa = $input['TenKhoa'];
        $this->khoaModel->NgayThanhLap = $input['NgayThanhLap'] ?: null;
        $this->khoaModel->TruongKhoa = $input['TruongKhoa'] ?: null;

        $result = $this->khoaModel->create();
        if ($result === true) {
            $this->redirect('Khoa/index');
        }

        $data = $this->buildIndexData($result);
        require_once __DIR__ . '/../views/admin/khoa/index.php';
    }

    public function edit($id) {
        $khoa = $this->khoaModel->getById($id);
        if (!$khoa) {
            $this->redirect('Khoa/index');
        }

        $data = [
            'khoa' => $khoa,
            'pageTitle' => 'Sửa khoa',
            'breadcrumb' => 'Sửa khoa',
            'error' => ''
        ];
        require_once __DIR__ . '/../views/admin/khoa/edit.php';
    }

    public function update($id) {
        if (!$this->isPost()) {
            $this->redirect('Khoa/index');
        }

        $input = [
            'TenKhoa' => $this->getPost('TenKhoa'),
            'NgayThanhLap' => $this->getPost('NgayThanhLap'),
            'TruongKhoa' => $this->getPost('TruongKhoa'),
        ];

        $errors = $this->validate($input, [
            'TenKhoa' => 'required|max:100'
        ]);

        if (!empty($errors)) {
            $data = [
                'khoa' => array_merge(['MaKhoa' => $id], $input),
                'pageTitle' => 'Sửa khoa',
                'breadcrumb' => 'Sửa khoa',
                'error' => implode(' ', $errors)
            ];
            require_once __DIR__ . '/../views/admin/khoa/edit.php';
            return;
        }

        $this->khoaModel->MaKhoa = $id;
        $this->khoaModel->TenKhoa = $input['TenKhoa'];
        $this->khoaModel->NgayThanhLap = $input['NgayThanhLap'] ?: null;
        $this->khoaModel->TruongKhoa = $input['TruongKhoa'] ?: null;

        $result = $this->khoaModel->update();
        if ($result === true) {
            $this->redirect('Khoa/index');
        }

        $data = [
            'khoa' => array_merge(['MaKhoa' => $id], $input),
            'pageTitle' => 'Sửa khoa',
            'breadcrumb' => 'Sửa khoa',
            'error' => $result
        ];
        require_once __DIR__ . '/../views/admin/khoa/edit.php';
    }

    public function delete($id) {
        $this->khoaModel->MaKhoa = $id;
        $result = $this->khoaModel->delete();
        
        if ($result !== true) {
            $data = $this->buildIndexData($result);
            require_once __DIR__ . '/../views/admin/khoa/index.php';
            return;
        }
        
        $this->redirect('Khoa/index');
    }
}