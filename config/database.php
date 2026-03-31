<?php
class Database {
    private $host = "localhost";
    private $db_name = "hrms_db";
    private $username = "root"; // ปกติ XAMPP ใช้ root
    private $password = "";     // ปกติ XAMPP ไม่มีรหัสผ่าน
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>