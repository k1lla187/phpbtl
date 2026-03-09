<?php
/**
 * MonHocController - Quản lý Môn học
 */
require_once __DIR__ . '/../core/Controller.php';

class MonHocController extends Controller {
    private $monHocModel;
    private $nganhModel;

    public function __construct() {
        parent::__construct();
        $this->monHocModel = $this->model('MonHocModel');
        $this->nganhModel = $this->model('NganhModel');
    }

    private function buildIndexData($error = '', $success = '') {
        return [
            'monhocs' => $this->monHocModel->readAll(),
            'nganhs' => $this->nganhModel->readAll(),
            'pageTitle' => 'Quản lý Môn học',
            'breadcrumb' => 'Môn học',
            'error' => $error,
            'success' => $success
        ];
    }

    public function index() {
        $data = $this->buildIndexData();
        require_once __DIR__ . '/../views/admin/monhoc/index.php';
    }

    public function store() {
        if (!$this->isPost()) {
            $this->redirect('MonHoc/index');
        }

        $input = [
            'MaMonHoc' => $this->getPost('MaMonHoc'),
            'TenMonHoc' => $this->getPost('TenMonHoc'),
            'SoTinChi' => $this->getPost('SoTinChi'),
            'SoTietLyThuyet' => $this->getPost('SoTietLyThuyet'),
            'SoTietThucHanh' => $this->getPost('SoTietThucHanh'),
            'MaNganh' => $this->getPost('MaNganh'),
        ];

        $errors = $this->validate($input, [
            'MaMonHoc' => 'required|max:20',
            'TenMonHoc' => 'required|max:100',
            'SoTinChi' => 'required|numeric'
        ]);

        if (!empty($errors)) {
            $data = $this->buildIndexData(implode(' ', $errors));
            require_once __DIR__ . '/../views/admin/monhoc/index.php';
            return;
        }

        $this->monHocModel->MaMonHoc = $input['MaMonHoc'];
        $this->monHocModel->TenMonHoc = $input['TenMonHoc'];
        $this->monHocModel->SoTinChi = (int) $input['SoTinChi'];
        $this->monHocModel->SoTietLyThuyet = (int) ($input['SoTietLyThuyet'] ?: 0);
        $this->monHocModel->SoTietThucHanh = (int) ($input['SoTietThucHanh'] ?: 0);
        $this->monHocModel->MaNganh = $input['MaNganh'] ?: null;

        $result = $this->monHocModel->create();
        if ($result === true) {
            $this->redirect('MonHoc/index');
        }

        $data = $this->buildIndexData($result);
        require_once __DIR__ . '/../views/admin/monhoc/index.php';
    }

    public function edit($id) {
        $monHoc = $this->monHocModel->getById($id);
        if (!$monHoc) {
            $this->redirect('MonHoc/index');
        }

        $data = [
            'monhoc' => $monHoc,
            'nganhs' => $this->nganhModel->readAll(),
            'pageTitle' => 'Sửa môn học',
            'breadcrumb' => 'Sửa môn học',
            'error' => ''
        ];
        require_once __DIR__ . '/../views/admin/monhoc/edit.php';
    }

    public function update($id) {
        if (!$this->isPost()) {
            $this->redirect('MonHoc/index');
        }

        $input = [
            'TenMonHoc' => $this->getPost('TenMonHoc'),
            'SoTinChi' => $this->getPost('SoTinChi'),
            'SoTietLyThuyet' => $this->getPost('SoTietLyThuyet'),
            'SoTietThucHanh' => $this->getPost('SoTietThucHanh'),
            'MaNganh' => $this->getPost('MaNganh'),
        ];

        $errors = $this->validate($input, [
            'TenMonHoc' => 'required|max:100',
            'SoTinChi' => 'required|numeric'
        ]);

        if (!empty($errors)) {
            $data = [
                'monhoc' => array_merge(['MaMonHoc' => $id], $input),
                'nganhs' => $this->nganhModel->readAll(),
                'pageTitle' => 'Sửa môn học',
                'breadcrumb' => 'Sửa môn học',
                'error' => implode(' ', $errors)
            ];
            require_once __DIR__ . '/../views/admin/monhoc/edit.php';
            return;
        }

        $this->monHocModel->MaMonHoc = $id;
        $this->monHocModel->TenMonHoc = $input['TenMonHoc'];
        $this->monHocModel->SoTinChi = (int) $input['SoTinChi'];
        $this->monHocModel->SoTietLyThuyet = (int) ($input['SoTietLyThuyet'] ?: 0);
        $this->monHocModel->SoTietThucHanh = (int) ($input['SoTietThucHanh'] ?: 0);
        $this->monHocModel->MaNganh = $input['MaNganh'] ?: null;

        $result = $this->monHocModel->update();
        if ($result === true) {
            $this->redirect('MonHoc/index');
        }

        $data = [
            'monhoc' => array_merge(['MaMonHoc' => $id], $input),
            'nganhs' => $this->nganhModel->readAll(),
            'pageTitle' => 'Sửa môn học',
            'breadcrumb' => 'Sửa môn học',
            'error' => $result
        ];
        require_once __DIR__ . '/../views/admin/monhoc/edit.php';
    }

    public function delete($id) {
        $this->monHocModel->MaMonHoc = $id;
        $result = $this->monHocModel->delete();
        
        if ($result !== true) {
            $data = $this->buildIndexData($result);
            require_once __DIR__ . '/../views/admin/monhoc/index.php';
            return;
        }
        
        $this->redirect('MonHoc/index');
    }

    /**
     * API: Lấy mã môn học tiếp theo (AJAX)
     */
    public function getNextId() {
        header('Content-Type: application/json');
        $nextId = $this->monHocModel->generateNextId('MH');
        echo json_encode(['success' => true, 'nextId' => $nextId]);
        exit;
    }
}