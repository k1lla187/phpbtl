<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/LoaiDiemModel.php';

class LoaiDiemController {
    private $db;
    private $loaiDiemModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->loaiDiemModel = new LoaiDiemModel($this->db);
    }

    public function index() {
        $loaidiems = $this->loaiDiemModel->readAll();
        $data = [
            'loaidiems' => $loaidiems,
            'pageTitle' => 'Quản lý Loại điểm',
            'breadcrumb' => 'Loại điểm'
        ];
        require_once "../app/views/loaidiem/index.php";
    }

    public function create() {
        require_once "../views/admin/loaidiem/create.php";
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->loaiDiemModel->MaLoaiDiem = $_POST['MaLoaiDiem'] ?? null;
            $this->loaiDiemModel->TenLoaiDiem = $_POST['TenLoaiDiem'] ?? null;

            if ($this->loaiDiemModel->create()) {
                header("Location: index.php?url=LoaiDiem/index");
            } else {
                echo "Lỗi thêm loại điểm.";
            }
        }
    }

    public function edit($id) {
        $loaiDiem = $this->loaiDiemModel->getById($id);
        require_once "../views/admin/loaidiem/edit.php";
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->loaiDiemModel->MaLoaiDiem = $id;
            $this->loaiDiemModel->TenLoaiDiem = $_POST['TenLoaiDiem'] ?? null;

            if ($this->loaiDiemModel->update()) {
                header("Location: index.php?url=LoaiDiem/index");
            } else {
                echo "Lỗi cập nhật loại điểm.";
            }
        }
    }

    public function delete($id) {
        $this->loaiDiemModel->MaLoaiDiem = $id;
        if ($this->loaiDiemModel->delete()) {
            header("Location: index.php?url=LoaiDiem/index");
        } else {
            echo "Lỗi xóa loại điểm.";
        }
    }
}
?>