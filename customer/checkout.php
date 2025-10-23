<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Save shipping details and go to payment
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['shipping'] = [
        'name' => $_POST['name'],
        'phone' => $_POST['phone'],
        'address' => $_POST['address'],
        'city' => $_POST['city'],
        'postal_code' => $_POST['postal_code'],
        'country' => $_POST['country']
    ];
    header("Location: payment.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="customerpanel.css">
</head>
<body>
    <div class="collection-header">
        <h1>📦 Shipping Details</h1>
    </div>

    <div class="customize-section">
        <form method="post">
            <label>Name:</label>
            <input class="filter-select" type="text" name="name" required><br><br>

            <label>Phone:</label>
            <input class="filter-select" type="text" name="phone" required><br><br>

            <label>Address:</label>
            <input class="filter-select" type="text" name="address" required><br><br>

            <label>City:</label>
            <input class="filter-select" type="text" name="city" required><br><br>

            <label>Postal Code:</label>
            <input class="filter-select" type="text" name="postal_code" required><br><br>

            <label>Country:</label>
            <input class="filter-select" type="text" name="country" required><br><br>
            <div class = "card">
            <button type="submit" class="btn-primary">Continue to Payment</button>
            </div>
        </form>
    </div>
    <?php include 'footer.php'; ?>

</body>
</html>
