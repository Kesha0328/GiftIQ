<?php
// toggle_bestseller.php
// POST endpoint: expects 'id' (product id). Returns JSON: { success:1, status:0/1 }

require 'admin_header.php'; // ensures DB connection and admin check

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => 0, 'error' => 'Invalid request']);
    exit;
}

// read input safely
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => 0, 'error' => 'Invalid id']);
    exit;
}

// fetch current value using prepared statement
$stmt = $conn->prepare("SELECT bestseller FROM products WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) {
    header('Content-Type: application/json');
    echo json_encode(['success' => 0, 'error' => 'Product not found']);
    exit;
}

$current = intval($row['bestseller']);
$new = $current ? 0 : 1;

// update using prepared statement
$u = $conn->prepare("UPDATE products SET bestseller = ? WHERE id = ?");
$u->bind_param("ii", $new, $id);
$ok = $u->execute();
$u->close();

header('Content-Type: application/json');
if ($ok) {
    // return the definitive status
    echo json_encode(['success' => 1, 'status' => $new]);
    exit;
} else {
    echo json_encode(['success' => 0, 'error' => 'DB update failed']);
    exit;
}
