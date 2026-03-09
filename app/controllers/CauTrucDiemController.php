<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/CauTrucDiemModel.php';

class CauTrucDiemController {
    private $db;
    private $ctdModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->ctdModel = new CauTrucDiemModel($this->db);
    }

    public function index() {
        $cautrucs = $this->ctdModel->readAll();
        $data = [
            'cautrucs' => $cautrucs,
            'pageTitle' => 'Cấu trúc điểm',
            'breadcrumb' => 'Cấu trúc điểm'
        ];
        require_once "../app/views/cautrucdiem/index.php";
    }

    public function create() {
        require_once "../views/admin/cautrucdiem/create.php";
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->ctdModel->MaMonHoc = $_POST['MaMonHoc'] ?? null;
            $this->ctdModel->MaLoaiDiem = $_POST['MaLoaiDiem'] ?? null;
            $this->ctdModel->HeSo = $_POST['HeSo'] ?? null;
            $this->ctdModel->MoTa = $_POST['MoTa'] ?? null;

            if ($this->ctdModel->create()) {
                header("Location: index.php?url=CauTrucDiem/index");
            } else {
                echo "Lỗi thêm cấu trúc điểm.";
            }
        }
    }

    public function edit($id) {
        // Trong model bạn dùng getById theo MaCauTruc
        $ctd = $this->ctdModel->getById($id);
        require_once "../views/admin/cautrucdiem/edit.php";
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Trong model bạn dùng ID để update, nên biến $id ở đây phải là ID (Primary Key)
            $this->ctdModel->ID = $id;
            $this->ctdModel->MaMonHoc = $_POST['MaMonHoc'] ?? null;
            $this->ctdModel->MaLoaiDiem = $_POST['MaLoaiDiem'] ?? null;
            $this->ctdModel->HeSo = $_POST['HeSo'] ?? null;
            $this->ctdModel->MoTa = $_POST['MoTa'] ?? null;

            if ($this->ctdModel->update()) {
                header("Location: index.php?url=CauTrucDiem/index");
            } else {
                echo "Lỗi cập nhật cấu trúc điểm.";
            }
        }
    }

    public function delete($id) {
        // Trong model bạn dùng MaCauTruc để xóa
        $this->ctdModel->MaCauTruc = $id;
        if ($this->ctdModel->delete()) {
            header("Location: index.php?url=CauTrucDiem/index");
        } else {
            echo "Lỗi xóa cấu trúc điểm.";
        }
    }
}
?>