<?php
session_start();
include '../config.php';

// Prices for each option
$chocolates = [
    "None" => 0,
    "Dairy Milk" => 100,
    "Ferrero Rocher" => 250,
    "Lindt" => 300
];

$accessories = [
    "None" => 0,
    "Teddy Bear" => 200,
    "Greeting Card" => 50,
    "Gift Wrap" => 80
];

$flowers = [
    "None" => 0,
    "Roses" => 150,
    "Lilies" => 200,
    "Tulips" => 250
];

$base_price = 500; // base gift box price

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $collection = $_POST['collection'];
    $chocolate  = $_POST['chocolate'];
    $accessory  = $_POST['accessory'];
    $flower     = $_POST['flower'];
    $message    = $_POST['message'];
    $quantity   = (int)$_POST['quantity'];

    // Calculate price
    $price = $base_price
            + $chocolates[$chocolate]
            + $accessories[$accessory]
            + $flowers[$flower];

    // Save into cart
    $_SESSION['cart'][] = [
    'product_id'   => 0, // special ID for custom gifts
    'name'         => "Custom Gift ($collection)",
    'price'        => $price,
    'quantity'     => $quantity,
    'custom_details' => [
        'Chocolates' => $chocolate,
        'Accessory'  => $accessory,
        'Flower'     => $flower,
        'Message'    => $message]
    ];

    header("Location: cart.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Build Your Custom Gift</title>
    <link rel="stylesheet" href="customerpanel.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="collection-header">
    <h1>✨ Build Your Custom Gift</h1>
    </div>

    <div class="customize-section">
    <form method="post">

    <p><label>🎁 Select Collection</label><br>
    <select name="collection" required>
        <option value="">-- Choose --</option>
        <option>Birthday</option>
        <option>Anniversary</option>
        <option>Valentine</option>
        <option>Congratulations</option>
        <option>Get Well Soon</option>
    </select></p>

    <p><label>🍫 Choose Chocolates</label><br>
    <select name="chocolate">
        <option>None</option>
        <option>Dairy Milk</option>
        <option>Ferrero Rocher</option>
        <option>Lindt</option>
    </select></p>

    <p><label>🎀 Choose Accessories</label><br>
    <select name="accessory">
        <option>None</option>
        <option>Teddy Bear</option>
        <option>Greeting Card</option>
        <option>Gift Wrap</option>
    </select></p>

    <p><label>🌸 Choose Flowers</label><br>
    <select name="flower">
        <option>None</option>
        <option>Roses</option>
        <option>Lilies</option>
        <option>Tulips</option>
    </select></p>

    <p><label>💌 Custom Message</label><br>
    <textarea name="message" placeholder="Write your personal note..."></textarea></p>

    <p><label>📦 Quantity</label><br>
    <input type="number" name="quantity" value="1" min="1"></p>

    <button type="submit" class="btn-primary">Add Custom Gift to Cart</button>
    </form>
    </div>
</body>
</html>
