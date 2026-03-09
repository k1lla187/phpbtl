<?php
/**
 * ThoiKhoaBieuModel - Quản lý thời khóa biểu (lịch dạy/học)
 */
require_once __DIR__ . '/../core/Model.php';

class ThoiKhoaBieuModel extends Model {
    protected $table_name = "THOI_KHOA_BIEU";
    protected $primaryKey = "ID";

    public $ID;
    public $MaLopHocPhan;
    public $Thu;
    public $TietBatDau;
    public $TietKetThuc;
    public $PhongHoc;

    /**
     * Lấy lịch theo mã lớp học phần
     */
    public function getByLopHocPhan($maLopHocPhan) {
        if (empty($maLopHocPhan)) return [];
        try {
            $query = "SELECT * FROM {$this->table_name} WHERE MaLopHocPhan = :MaLopHocPhan ORDER BY Thu, TietBatDau";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaLopHocPhan", $maLopHocPhan);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ThoiKhoaBieuModel::getByLopHocPhan: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy lịch giảng dạy của giảng viên (qua các lớp học phần)
     */
    public function getLichDayByGiangVien($maGiangVien, $maHocKy = null) {
        if (empty($maGiangVien)) return [];
        try {
            $sql = "SELECT tkb.*, lhp.MaMonHoc, lhp.MaHocKy, lhp.PhongHoc as PhongMacDinh,
                    mh.TenMonHoc, hk.TenHocKy, hk.NamHoc,
                    (SELECT COUNT(*) FROM DANG_KY_HOC dk WHERE dk.MaLopHocPhan = tkb.MaLopHocPhan) as SiSo
                    FROM {$this->table_name} tkb
                    INNER JOIN LOP_HOC_PHAN lhp ON tkb.MaLopHocPhan = lhp.MaLopHocPhan
                    LEFT JOIN MON_HOC mh ON lhp.MaMonHoc = mh.MaMonHoc
                    LEFT JOIN HOC_KY hk ON lhp.MaHocKy = hk.MaHocKy
                    WHERE lhp.MaGiangVien = :MaGiangVien";
            $params = [':MaGiangVien' => $maGiangVien];
            if ($maHocKy) {
                $sql .= " AND lhp.MaHocKy = :MaHocKy";
                $params[':MaHocKy'] = $maHocKy;
            }
            $sql .= " ORDER BY tkb.Thu, tkb.TietBatDau";
            $stmt = $this->conn->prepare($sql);
            foreach ($params as $k => $v) $stmt->bindValue($k, $v);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ThoiKhoaBieuModel::getLichDayByGiangVien: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy lịch học của sinh viên (qua đăng ký học)
     */
    public function getLichHocBySinhVien($maSinhVien, $maHocKy = null) {
        if (empty($maSinhVien)) return [];
        try {
            $sql = "SELECT tkb.*, lhp.MaMonHoc, lhp.MaHocKy, lhp.PhongHoc as PhongMacDinh,
                    mh.TenMonHoc, mh.SoTinChi, gv.HoTen as TenGiangVien, hk.TenHocKy, hk.NamHoc
                    FROM DANG_KY_HOC dk
                    INNER JOIN LOP_HOC_PHAN lhp ON dk.MaLopHocPhan = lhp.MaLopHocPhan
                    INNER JOIN {$this->table_name} tkb ON lhp.MaLopHocPhan = tkb.MaLopHocPhan
                    LEFT JOIN MON_HOC mh ON lhp.MaMonHoc = mh.MaMonHoc
                    LEFT JOIN GIANG_VIEN gv ON lhp.MaGiangVien = gv.MaGiangVien
                    LEFT JOIN HOC_KY hk ON lhp.MaHocKy = hk.MaHocKy
                    WHERE dk.MaSinhVien = :MaSinhVien";
            $params = [':MaSinhVien' => $maSinhVien];
            if ($maHocKy) {
                $sql .= " AND lhp.MaHocKy = :MaHocKy";
                $params[':MaHocKy'] = $maHocKy;
            }
            $sql .= " ORDER BY tkb.Thu, tkb.TietBatDau";
            $stmt = $this->conn->prepare($sql);
            foreach ($params as $k => $v) $stmt->bindValue($k, $v);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ThoiKhoaBieuModel::getLichHocBySinhVien: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Xóa tất cả lịch của một lớp học phần
     */
    public function deleteByLopHocPhan($maLopHocPhan) {
        try {
            $query = "DELETE FROM {$this->table_name} WHERE MaLopHocPhan = :MaLopHocPhan";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":MaLopHocPhan", $this->sanitize($maLopHocPhan));
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("ThoiKhoaBieuModel::deleteByLopHocPhan: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Thêm một ca học
     */
    public function create() {
        try {
            $query = "INSERT INTO {$this->table_name} (MaLopHocPhan, Thu, TietBatDau, TietKetThuc, PhongHoc)
                      VALUES (:MaLopHocPhan, :Thu, :TietBatDau, :TietKetThuc, :PhongHoc)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":MaLopHocPhan", $this->sanitize($this->MaLopHocPhan));
            $stmt->bindValue(":Thu", (int)$this->Thu);
            $stmt->bindValue(":TietBatDau", (int)$this->TietBatDau);
            $stmt->bindValue(":TietKetThuc", (int)$this->TietKetThuc);
            $stmt->bindValue(":PhongHoc", $this->sanitize($this->PhongHoc) ?: null);
            return $stmt->execute();
        } catch (PDOException $e) {
            return $this->handlePdoException($e, 'ThoiKhoaBieuModel::create');
        }
    }
}
