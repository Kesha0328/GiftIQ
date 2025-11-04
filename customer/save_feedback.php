<?php
session_start();
include "../config.php";  // ✅ your existing DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $conn->real_escape_string($_POST['name']);
    $message = $conn->real_escape_string($_POST['message']);
    $smile = intval($_POST['smile']);

    $query = "INSERT INTO feedbacks (name, message, smile_rating) VALUES ('$name', '$message', '$smile')";

    if ($conn->query($query)) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
