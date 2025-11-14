<?php
include "../config.php";

$result = $conn->query("SELECT * FROM feedback ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Feedback List</title>
<?php require 'admin_header.php'; ?>

<style>
    body {
        background-color: #0d0d0d;
        font-family: Arial, sans-serif;
        color: #ffffff;
        margin: 0;
        padding: 0;
    }

    .container {
        margin: 50px auto;
        width: 90%;
        max-width: 1100px;
        background: #111;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 0 20px rgba(255, 165, 0, 0.1);
    }

    h2 {
        color: #ffb56b;
        margin-bottom: 20px;
        font-size: 24px;
        font-weight: bold;
        text-align: center;
        border-bottom: 2px solid #ffb56b;
        padding-bottom: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        border-radius: 8px;
        overflow: hidden;
    }

    th {
        background-color: #1c1c1c;
        color: #ffb56b;
        padding: 14px;
        font-size: 14px;
        text-transform: uppercase;
        border-bottom: 2px solid #ffb56b;
    }

    td {
        padding: 12px;
        background-color: #161616;
        border-bottom: 1px solid #333;
    }

    tr:hover td {
        background-color: #1f1f1f;
        transition: 0.3s;
    }

    .rating {
        color: #ffb56b;
        font-size: 18px;
        font-weight: bold;
    }
</style>

</head>
<body>

<div class="container">
    <h2>Customer Feedback</h2>

    <table>
        <tr>
            <th>Name</th>
            <th>Message</th>
            <th>Rating</th>
            <th>Date</th>
        </tr>

        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['name'] ?></td>
            <td><?= $row['message'] ?></td>
            <td class="rating"><?= $row['smile_rating'] ?> ⭐</td>
            <td><?= $row['created_at'] ?></td>
        </tr>
        <?php endwhile; ?>

    </table>
</div>
<?php require 'admin_footer.php'; ?>

</body>
</html>
