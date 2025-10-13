<?php
// src/config/config.php
$host = "localhost";
$db   = "gift_db";
$user = "root";   // default in XAMPP
$pass = "";       // default is empty

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
