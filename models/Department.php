<?php
class Department {
    private $conn;
    private $table_name = "departments";

    public function __construct($db) { $this->conn = $db; }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY dept_type ASC, amphoe ASC, name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isNameExists($name, $exclude_id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE name = :name";
        if ($exclude_id) { $query .= " AND id != :exclude_id"; }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        if ($exclude_id) { $stmt->bindParam(':exclude_id', $exclude_id); }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (name, dept_type, amphoe, dept_code, short_name, phone, email, address, latitude, longitude, status) 
                  VALUES (:name, :dept_type, :amphoe, :dept_code, :short_name, :phone, :email, :address, :latitude, :longitude, :status)";
        
        $stmt = $this->conn->prepare($query);
        
        $name = htmlspecialchars(strip_tags($data['name']));
        $dept_type = htmlspecialchars(strip_tags($data['dept_type']));
        $amphoe = (in_array($dept_type, ['health', 'school']) && !empty($data['amphoe'])) ? htmlspecialchars(strip_tags($data['amphoe'])) : null;
        
        $dept_code = !empty($data['dept_code']) ? htmlspecialchars(strip_tags($data['dept_code'])) : null;
        $short_name = !empty($data['short_name']) ? htmlspecialchars(strip_tags($data['short_name'])) : null;
        $phone = !empty($data['phone']) ? htmlspecialchars(strip_tags($data['phone'])) : null;
        $email = !empty($data['email']) ? htmlspecialchars(strip_tags($data['email'])) : null;
        $address = !empty($data['address']) ? htmlspecialchars(strip_tags($data['address'])) : null;
        
        // รับค่าพิกัด
        $latitude = !empty($data['latitude']) ? htmlspecialchars(strip_tags($data['latitude'])) : null;
        $longitude = !empty($data['longitude']) ? htmlspecialchars(strip_tags($data['longitude'])) : null;
        
        $status = htmlspecialchars(strip_tags($data['status']));
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':dept_type', $dept_type);
        $stmt->bindParam(':amphoe', $amphoe);
        $stmt->bindParam(':dept_code', $dept_code);
        $stmt->bindParam(':short_name', $short_name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':latitude', $latitude);
        $stmt->bindParam(':longitude', $longitude);
        $stmt->bindParam(':status', $status);
        
        if($stmt->execute()) { return true; }
        return false;
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, dept_type = :dept_type, amphoe = :amphoe, 
                      dept_code = :dept_code, short_name = :short_name, 
                      phone = :phone, email = :email, address = :address,
                      latitude = :latitude, longitude = :longitude, status = :status 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $name = htmlspecialchars(strip_tags($data['name']));
        $dept_type = htmlspecialchars(strip_tags($data['dept_type']));
        $amphoe = (in_array($dept_type, ['health', 'school']) && !empty($data['amphoe'])) ? htmlspecialchars(strip_tags($data['amphoe'])) : null;
        
        $dept_code = !empty($data['dept_code']) ? htmlspecialchars(strip_tags($data['dept_code'])) : null;
        $short_name = !empty($data['short_name']) ? htmlspecialchars(strip_tags($data['short_name'])) : null;
        $phone = !empty($data['phone']) ? htmlspecialchars(strip_tags($data['phone'])) : null;
        $email = !empty($data['email']) ? htmlspecialchars(strip_tags($data['email'])) : null;
        $address = !empty($data['address']) ? htmlspecialchars(strip_tags($data['address'])) : null;
        
        $latitude = !empty($data['latitude']) ? htmlspecialchars(strip_tags($data['latitude'])) : null;
        $longitude = !empty($data['longitude']) ? htmlspecialchars(strip_tags($data['longitude'])) : null;
        
        $status = htmlspecialchars(strip_tags($data['status']));
        $id = htmlspecialchars(strip_tags($id));
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':dept_type', $dept_type);
        $stmt->bindParam(':amphoe', $amphoe);
        $stmt->bindParam(':dept_code', $dept_code);
        $stmt->bindParam(':short_name', $short_name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':latitude', $latitude);
        $stmt->bindParam(':longitude', $longitude);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        
        if($stmt->execute()) { return true; }
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        
        if($stmt->execute()) { return true; }
        return false;
    }
}
?>