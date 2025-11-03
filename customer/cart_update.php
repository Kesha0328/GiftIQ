<?php
session_start();

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $key = $_POST['key'] ?? '';
    $action = $_POST['action'] ?? '';

    if ($action === 'remove' && isset($_SESSION['cart'][$key])) {
        unset($_SESSION['cart'][$key]);
    }
    if ($action === 'update' && isset($_SESSION['cart'][$key])) {
        $qty = max(1, intval($_POST['quantity']));
        $_SESSION['cart'][$key]['quantity'] = $qty;
    }
}

header("Location: cart.php");
exit;
?>
