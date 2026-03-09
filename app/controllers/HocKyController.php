<?php
/**
 * HocKyController - Quản lý Học kỳ
 */
require_once __DIR__ . '/../core/Controller.php';

class HocKyController extends Controller {
    private $hockyModel;

    public function __construct() {
        parent::__construct();
        $this->hockyModel = $this->model('HocKyModel');
    }

    private function buildIndexData($error = '', $success = '') {
        return [
            'hockys' => $this->hockyModel->readAll(),
            'pageTitle' => 'Quản lý Học kỳ',
            'breadcrumb' => 'Học kỳ',
            'error' => $error,
            'success' => $success
        ];
    }

    public function index() {
        $data = $this->buildIndexData();
        require_once __DIR__ . '/../views/admin/hocky/index.php';
    }

    public function store() {
        if (!$this->isPost()) {
            $this->redirect('HocKy/index');
        }

        $input = [
            'MaHocKy' => $this->getPost('MaHocKy'),
            'TenHocKy' => $this->getPost('TenHocKy'),
            'NamHoc' => $this->getPost('NamHoc'),
            'NgayBatDau' => $this->getPost('NgayBatDau'),
            'NgayKetThuc' => $this->getPost('NgayKetThuc'),
        ];

        $errors = $this->validate($input, [
            'MaHocKy' => 'required|max:20',
            'TenHocKy' => 'required|max:50',
            'NamHoc' => 'required|numeric'
        ]);

        // Validate ngày bắt đầu < ngày kết thúc
        if (!empty($input['NgayBatDau']) && !empty($input['NgayKetThuc'])) {
            if (strtotime($input['NgayBatDau']) >= strtotime($input['NgayKetThuc'])) {
                $errors['NgayKetThuc'] = 'Ngày kết thúc phải sau ngày bắt đầu.';
            }
        }

        if (!empty($errors)) {
            $data = $this->buildIndexData(implode(' ', $errors));
            require_once __DIR__ . '/../views/admin/hocky/index.php';
            return;
        }

        $this->hockyModel->MaHocKy = $input['MaHocKy'];
        $this->hockyModel->TenHocKy = $input['TenHocKy'];
        $this->hockyModel->NamHoc = (int) $input['NamHoc'];
        $this->hockyModel->NgayBatDau = $input['NgayBatDau'] ?: null;
        $this->hockyModel->NgayKetThuc = $input['NgayKetThuc'] ?: null;

        $result = $this->hockyModel->create();
        if ($result === true) {
            $this->redirect('HocKy/index');
        }

        $data = $this->buildIndexData($result);
        require_once __DIR__ . '/../views/admin/hocky/index.php';
    }

    public function edit($id) {
        $hocky = $this->hockyModel->getById($id);
        if (!$hocky) {
            $this->redirect('HocKy/index');
        }

        $data = [
            'hocky' => $hocky,
            'pageTitle' => 'Sửa học kỳ',
            'breadcrumb' => 'Sửa học kỳ',
            'error' => ''
        ];
        require_once __DIR__ . '/../views/admin/hocky/edit.php';
    }

    public function update($id) {
        if (!$this->isPost()) {
            $this->redirect('HocKy/index');
        }

        $input = [
            'TenHocKy' => $this->getPost('TenHocKy'),
            'NamHoc' => $this->getPost('NamHoc'),
            'NgayBatDau' => $this->getPost('NgayBatDau'),
            'NgayKetThuc' => $this->getPost('NgayKetThuc'),
        ];

        $errors = $this->validate($input, [
            'TenHocKy' => 'required|max:50',
            'NamHoc' => 'required|numeric'
        ]);

        if (!empty($input['NgayBatDau']) && !empty($input['NgayKetThuc'])) {
            if (strtotime($input['NgayBatDau']) >= strtotime($input['NgayKetThuc'])) {
                $errors['NgayKetThuc'] = 'Ngày kết thúc phải sau ngày bắt đầu.';
            }
        }

        if (!empty($errors)) {
            $data = [
                'hocky' => array_merge(['MaHocKy' => $id], $input),
                'pageTitle' => 'Sửa học kỳ',
                'breadcrumb' => 'Sửa học kỳ',
                'error' => implode(' ', $errors)
            ];
            require_once __DIR__ . '/../views/admin/hocky/edit.php';
            return;
        }

        $this->hockyModel->MaHocKy = $id;
        $this->hockyModel->TenHocKy = $input['TenHocKy'];
        $this->hockyModel->NamHoc = (int) $input['NamHoc'];
        $this->hockyModel->NgayBatDau = $input['NgayBatDau'] ?: null;
        $this->hockyModel->NgayKetThuc = $input['NgayKetThuc'] ?: null;

        $result = $this->hockyModel->update();
        if ($result === true) {
            $this->redirect('HocKy/index');
        }

        $data = [
            'hocky' => array_merge(['MaHocKy' => $id], $input),
            'pageTitle' => 'Sửa học kỳ',
            'breadcrumb' => 'Sửa học kỳ',
            'error' => $result
        ];
        require_once __DIR__ . '/../views/admin/hocky/edit.php';
    }

    public function delete($id) {
        $this->hockyModel->MaHocKy = $id;
        $result = $this->hockyModel->delete();
        
        if ($result !== true) {
            $data = $this->buildIndexData($result);
            require_once __DIR__ . '/../views/admin/hocky/index.php';
            return;
        }
        
        $this->redirect('HocKy/index');
    }
}