<?php
/**
 * LopHocPhanController - Quản lý Lớp học phần
 */
require_once __DIR__ . '/../core/Controller.php';

class LopHocPhanController extends Controller {
    private $lhpModel;
    private $monHocModel;
    private $hocKyModel;
    private $gvModel;
    private $dangKyModel;

    public function __construct() {
        parent::__construct();
        $this->lhpModel = $this->model('LopHocPhanModel');
        $this->monHocModel = $this->model('MonHocModel');
        $this->hocKyModel = $this->model('HocKyModel');
        $this->gvModel = $this->model('GiangVienModel');
        $this->dangKyModel = $this->model('DangKyHocModel');
    }

    private function buildIndexData($error = '', $success = '') {
        return [
            'lophps' => $this->lhpModel->readAllWithDetails(),
            'monhocs' => $this->monHocModel->readAll(),
            'hockys' => $this->hocKyModel->readAll(),
            'giangviens' => $this->gvModel->readAll(),
            'pageTitle' => 'Quản lý Lớp học phần',
            'breadcrumb' => 'Lớp học phần',
            'error' => $error,
            'success' => $success
        ];
    }

    public function index() {
        $data = $this->buildIndexData();
        require_once __DIR__ . '/../views/admin/lophocphan/index.php';
    }

    public function store() {
        if (!$this->isPost()) {
            $this->redirect('LopHocPhan/index');
        }

        $input = [
            'MaLopHocPhan' => $this->getPost('MaLopHocPhan'),
            'MaMonHoc' => $this->getPost('MaMonHoc'),
            'MaHocKy' => $this->getPost('MaHocKy'),
            'MaGiangVien' => $this->getPost('MaGiangVien'),
            'PhongHoc' => $this->getPost('PhongHoc'),
            'SoLuongToiDa' => $this->getPost('SoLuongToiDa'),
        ];

        $errors = $this->validate($input, [
            'MaLopHocPhan' => 'required|max:20',
            'MaMonHoc' => 'required',
            'MaHocKy' => 'required',
            'SoLuongToiDa' => 'numeric'
        ]);

        if (!empty($errors)) {
            $data = $this->buildIndexData(implode(' ', $errors));
            require_once __DIR__ . '/../views/admin/lophocphan/index.php';
            return;
        }

        $this->lhpModel->MaLopHocPhan = $input['MaLopHocPhan'];
        $this->lhpModel->MaMonHoc = $input['MaMonHoc'];
        $this->lhpModel->MaHocKy = $input['MaHocKy'];
        $this->lhpModel->MaGiangVien = $input['MaGiangVien'] ?: null;
        $this->lhpModel->PhongHoc = $input['PhongHoc'] ?: null;
        $this->lhpModel->SoLuongToiDa = $input['SoLuongToiDa'] ? (int) $input['SoLuongToiDa'] : 60;
        $this->lhpModel->TrangThai = 'Đang mở';

        $result = $this->lhpModel->create();
        if ($result === true) {
            $this->redirect('LopHocPhan/index');
        }

        $data = $this->buildIndexData($result);
        require_once __DIR__ . '/../views/admin/lophocphan/index.php';
    }

    public function edit($id) {
        $lhp = $this->lhpModel->getById($id);
        if (!$lhp) {
            $this->redirect('LopHocPhan/index');
        }

        $data = [
            'lophocphan' => $lhp,
            'monhocs' => $this->monHocModel->readAll(),
            'hockys' => $this->hocKyModel->readAll(),
            'giangviens' => $this->gvModel->readAll(),
            'pageTitle' => 'Sửa lớp học phần',
            'breadcrumb' => 'Sửa lớp học phần',
            'error' => ''
        ];
        require_once __DIR__ . '/../views/admin/lophocphan/edit.php';
    }

    public function update($id) {
        if (!$this->isPost()) {
            $this->redirect('LopHocPhan/index');
        }

        $input = [
            'MaMonHoc' => $this->getPost('MaMonHoc'),
            'MaHocKy' => $this->getPost('MaHocKy'),
            'MaGiangVien' => $this->getPost('MaGiangVien'),
            'PhongHoc' => $this->getPost('PhongHoc'),
            'SoLuongToiDa' => $this->getPost('SoLuongToiDa'),
            'TrangThai' => $this->getPost('TrangThai'),
            'ChoPhepDangKyKhacKhoa' => $this->getPost('ChoPhepDangKyKhacKhoa'),
        ];

        $errors = $this->validate($input, [
            'MaMonHoc' => 'required',
            'MaHocKy' => 'required',
            'SoLuongToiDa' => 'numeric'
        ]);

        if (!empty($errors)) {
            $data = [
                'lophocphan' => array_merge(['MaLopHocPhan' => $id], $input),
                'monhocs' => $this->monHocModel->readAll(),
                'hockys' => $this->hocKyModel->readAll(),
                'giangviens' => $this->gvModel->readAll(),
                'pageTitle' => 'Sửa lớp học phần',
                'breadcrumb' => 'Sửa lớp học phần',
                'error' => implode(' ', $errors)
            ];
            require_once __DIR__ . '/../views/admin/lophocphan/edit.php';
            return;
        }

        $this->lhpModel->MaLopHocPhan = $id;
        $this->lhpModel->MaMonHoc = $input['MaMonHoc'];
        $this->lhpModel->MaHocKy = $input['MaHocKy'];
        $this->lhpModel->MaGiangVien = $input['MaGiangVien'] ?: null;
        $this->lhpModel->PhongHoc = $input['PhongHoc'] ?: null;
        $this->lhpModel->SoLuongToiDa = $input['SoLuongToiDa'] ? (int) $input['SoLuongToiDa'] : 60;
        $this->lhpModel->TrangThai = $input['TrangThai'] ?: 'Đang mở';
        $this->lhpModel->ChoPhepDangKyKhacKhoa = isset($input['ChoPhepDangKyKhacKhoa']) ? 1 : 0;

        $result = $this->lhpModel->update();
        if ($result === true) {
            $this->redirect('LopHocPhan/index');
        }

        $data = [
            'lophocphan' => array_merge(['MaLopHocPhan' => $id], $input),
            'monhocs' => $this->monHocModel->readAll(),
            'hockys' => $this->hocKyModel->readAll(),
            'giangviens' => $this->gvModel->readAll(),
            'pageTitle' => 'Sửa lớp học phần',
            'breadcrumb' => 'Sửa lớp học phần',
            'error' => $result
        ];
        require_once __DIR__ . '/../views/admin/lophocphan/edit.php';
    }

    public function delete($id) {
        $this->lhpModel->MaLopHocPhan = $id;
        $result = $this->lhpModel->delete();
        
        if ($result !== true) {
            $data = $this->buildIndexData($result);
            require_once __DIR__ . '/../views/admin/lophocphan/index.php';
            return;
        }
        
        $this->redirect('LopHocPhan/index');
    }

    /**
     * Gán sinh viên vào lớp học phần
     */
    public function assignStudent() {
        if (!$this->isPost()) {
            $this->redirect('LopHocPhan/index');
        }

        $maSinhVien = $this->getPost('MaSinhVien');
        $maLopHocPhan = $this->getPost('MaLopHocPhan');

        if (empty($maSinhVien) || empty($maLopHocPhan)) {
            $data = $this->buildIndexData('Vui lòng chọn sinh viên và lớp học phần.');
            require_once __DIR__ . '/../views/admin/lophocphan/index.php';
            return;
        }

        $this->dangKyModel->MaSinhVien = $maSinhVien;
        $this->dangKyModel->MaLopHocPhan = $maLopHocPhan;

        $result = $this->dangKyModel->create();
        if ($result === true) {
            $this->redirect('LopHocPhan/index');
        }

        $data = $this->buildIndexData($result);
        require_once __DIR__ . '/../views/admin/lophocphan/index.php';
    }

    /**
     * API: Lấy mã lớp học phần tiếp theo (AJAX)
     */
    public function getNextId() {
        header('Content-Type: application/json');
        $nextId = $this->lhpModel->generateNextId('LHP');
        echo json_encode(['success' => true, 'nextId' => $nextId]);
        exit;
    }
}