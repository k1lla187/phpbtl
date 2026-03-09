<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/ChiTietDiemModel.php';

class ChiTietDiemController {
    private $db;
    private $ctDiemModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->ctDiemModel = new ChiTietDiemModel($this->db);
    }

    public function index() {
        $chitietdiems = $this->ctDiemModel->readAll();
        $data = [
            'chitietdiems' => $chitietdiems,
            'pageTitle' => 'Chi tiết điểm',
            'breadcrumb' => 'Chi tiết điểm'
        ];
        require_once "../app/views/chitietdiem/index.php";
    }

    public function create() {
        require_once "../app/views/chitietdiem/create.php";
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->ctDiemModel->MaDangKy = $_POST['MaDangKy'] ?? null;
            $this->ctDiemModel->MaLoaiDiem = $_POST['MaLoaiDiem'] ?? null;
            $this->ctDiemModel->SoDiem = $_POST['SoDiem'] ?? null;
            $this->ctDiemModel->NgayNhap = $_POST['NgayNhap'] ?? date('Y-m-d');
            $this->ctDiemModel->NguoiNhap = $_POST['NguoiNhap'] ?? 'System';

            if ($this->ctDiemModel->create()) {
                header("Location: index.php?url=ChiTietDiem/index");
            } else {
                echo "Lỗi nhập điểm.";
            }
        }
    }

    public function edit($id) {
        $diem = $this->ctDiemModel->getById($id);
        require_once "../views/admin/chitietdiem/edit.php";
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->ctDiemModel->MaChiTiet = $id;
            $this->ctDiemModel->MaDangKy = $_POST['MaDangKy'] ?? null;
            $this->ctDiemModel->MaLoaiDiem = $_POST['MaLoaiDiem'] ?? null;
            $this->ctDiemModel->SoDiem = $_POST['SoDiem'] ?? null;
            $this->ctDiemModel->NgayNhap = $_POST['NgayNhap'] ?? date('Y-m-d');
            $this->ctDiemModel->NguoiNhap = $_POST['NguoiNhap'] ?? 'System';

            if ($this->ctDiemModel->update()) {
                header("Location: index.php?url=ChiTietDiem/index");
            } else {
                echo "Lỗi cập nhật điểm.";
            }
        }
    }

    public function delete($id) {
        $this->ctDiemModel->MaChiTiet = $id;
        if ($this->ctDiemModel->delete()) {
            header("Location: index.php?url=ChiTietDiem/index");
        } else {
            echo "Lỗi xóa điểm.";
        }
    }
}
?>