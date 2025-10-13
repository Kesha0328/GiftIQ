<?php
// src/models/user.php
class User {
    private $conn;
    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($fullname, $email, $password) {
        // hash password
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $fullname, $email, $hash);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function checkEmailExists($email) {
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        return $stmt->num_rows > 0;
    }
}
