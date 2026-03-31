<?php
// ==========================================
// ชื่อไฟล์: PositionLevel.php
// ที่อยู่ไฟล์: models/PositionLevel.php
// ==========================================

class PositionLevel {
    private $conn;
    private $table_name = "position_levels";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        // เรียงลำดับตามประเภทสายงาน
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY type ASC, id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (name, type) VALUES (:name, :type)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":type", $data['type']);

        if($stmt->execute()) return true;
        return false;
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " SET name = :name, type = :type WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":type", $data['type']);
        $stmt->bindParam(":id", $id);

        if($stmt->execute()) return true;
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);

        if($stmt->execute()) return true;
        return false;
    }
}
?>