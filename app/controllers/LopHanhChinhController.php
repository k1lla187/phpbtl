<?php
/**
 * LopHanhChinhController - Quản lý Lớp hành chính
 */
require_once __DIR__ . '/../core/Controller.php';

class LopHanhChinhController extends Controller {
    private $lhcModel;
    private $nganhModel;
    private $gvModel;

    public function __construct() {
        parent::__construct();
        $this->lhcModel = $this->model('LopHanhChinhModel');
        $this->nganhModel = $this->model('NganhModel');
        $this->gvModel = $this->model('GiangVienModel');
    }

    private function buildIndexData($error = '', $success = '') {
        return [
            'lophanhchinhs' => $this->lhcModel->readAllWithDetails(),
            'nganhs' => $this->nganhModel->readAll(),
            'giangviens' => $this->gvModel->readAll(),
            'pageTitle' => 'Quản lý Lớp hành chính',
            'breadcrumb' => 'Lớp hành chính',
            'error' => $error,
            'success' => $success
        ];
    }

    public function index() {
        $data = $this->buildIndexData();
        require_once __DIR__ . '/../views/admin/lophanhchinh/index.php';
    }

    public function store() {
        if (!$this->isPost()) {
            $this->redirect('LopHanhChinh/index');
        }

        $input = [
            'MaLop' => $this->getPost('MaLop'),
            'TenLop' => $this->getPost('TenLop'),
            'MaNganh' => $this->getPost('MaNganh'),
            'KhoaHoc' => $this->getPost('KhoaHoc'),
            'MaCoVan' => $this->getPost('MaCoVan'),
        ];

        $errors = $this->validate($input, [
            'MaLop' => 'required|max:20',
            'TenLop' => 'required|max:100',
            'KhoaHoc' => 'numeric'
        ]);

        if (!empty($errors)) {
            $data = $this->buildIndexData(implode(' ', $errors));
            require_once __DIR__ . '/../views/admin/lophanhchinh/index.php';
            return;
        }

        $this->lhcModel->MaLop = $input['MaLop'];
        $this->lhcModel->TenLop = $input['TenLop'];
        $this->lhcModel->MaNganh = $input['MaNganh'] ?: null;
        $this->lhcModel->KhoaHoc = $input['KhoaHoc'] ? (int) $input['KhoaHoc'] : null;
        $this->lhcModel->MaCoVan = $input['MaCoVan'] ?: null;

        $result = $this->lhcModel->create();
        if ($result === true) {
            $this->redirect('LopHanhChinh/index');
        }

        $data = $this->buildIndexData($result);
        require_once __DIR__ . '/../views/admin/lophanhchinh/index.php';
    }

    public function edit($id) {
        $lop = $this->lhcModel->getById($id);
        if (!$lop) {
            $this->redirect('LopHanhChinh/index');
        }

        $data = [
            'lop' => $lop,
            'nganhs' => $this->nganhModel->readAll(),
            'giangviens' => $this->gvModel->readAll(),
            'pageTitle' => 'Sửa lớp hành chính',
            'breadcrumb' => 'Sửa lớp hành chính',
            'error' => ''
        ];
        require_once __DIR__ . '/../views/admin/lophanhchinh/edit.php';
    }

    public function update($id) {
        if (!$this->isPost()) {
            $this->redirect('LopHanhChinh/index');
        }

        $input = [
            'TenLop' => $this->getPost('TenLop'),
            'MaNganh' => $this->getPost('MaNganh'),
            'KhoaHoc' => $this->getPost('KhoaHoc'),
            'MaCoVan' => $this->getPost('MaCoVan'),
        ];

        $errors = $this->validate($input, [
            'TenLop' => 'required|max:100',
            'KhoaHoc' => 'numeric'
        ]);

        if (!empty($errors)) {
            $data = [
                'lop' => array_merge(['MaLop' => $id], $input),
                'nganhs' => $this->nganhModel->readAll(),
                'giangviens' => $this->gvModel->readAll(),
                'pageTitle' => 'Sửa lớp hành chính',
                'breadcrumb' => 'Sửa lớp hành chính',
                'error' => implode(' ', $errors)
            ];
            require_once __DIR__ . '/../views/admin/lophanhchinh/edit.php';
            return;
        }

        $this->lhcModel->MaLop = $id;
        $this->lhcModel->TenLop = $input['TenLop'];
        $this->lhcModel->MaNganh = $input['MaNganh'] ?: null;
        $this->lhcModel->KhoaHoc = $input['KhoaHoc'] ? (int) $input['KhoaHoc'] : null;
        $this->lhcModel->MaCoVan = $input['MaCoVan'] ?: null;

        $result = $this->lhcModel->update();
        if ($result === true) {
            $this->redirect('LopHanhChinh/index');
        }

        $data = [
            'lop' => array_merge(['MaLop' => $id], $input),
            'nganhs' => $this->nganhModel->readAll(),
            'giangviens' => $this->gvModel->readAll(),
            'pageTitle' => 'Sửa lớp hành chính',
            'breadcrumb' => 'Sửa lớp hành chính',
            'error' => $result
        ];
        require_once __DIR__ . '/../views/admin/lophanhchinh/edit.php';
    }

    public function delete($id) {
        $this->lhcModel->MaLop = $id;
        $result = $this->lhcModel->delete();
        
        if ($result !== true) {
            $data = $this->buildIndexData($result);
            require_once __DIR__ . '/../views/admin/lophanhchinh/index.php';
            return;
        }
        
        $this->redirect('LopHanhChinh/index');
    }
}