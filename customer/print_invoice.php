<?php
session_start();
include '../config.php';

if (!isset($_GET['order_id'])) die("No order selected");

$order_id = intval($_GET['order_id']);

$sql = "SELECT o.id, o.created_at, o.total, o.status,
              s.name, s.phone, s.address, s.city, s.postal_code, s.country,
              u.email
        FROM orders o
        LEFT JOIN shipping s ON o.id = s.order_id
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.id = $order_id";
$order = $conn->query($sql)->fetch_assoc();

$sql_items = "SELECT oi.quantity, oi.price, oi.custom_data, p.name, p.image
              FROM order_items oi
              LEFT JOIN products p ON oi.product_id = p.id
              WHERE oi.order_id = $order_id";
$items = $conn->query($sql_items);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/png" href="../uploads/favicon.png" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<title>Invoice #<?= $order['id']; ?> - GiftIQ</title>
<style>
@import url('https://i.ibb.co/Kxb6CC0Y/logo.png');
* { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
body {
  background: #f8f8f8;
  color: #222;
  padding: 30px;
}
.invoice-box {
  max-width: 900px;
  margin: auto;
  background: #fff;
  padding: 40px 50px;
  border-radius: 16px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 2px solid #e0e0e0;
  padding-bottom: 15px;
}
.header img {
  width: 120px;
}
.header h1 {
  color: #c58b6a;
  font-size: 1.8rem;
  margin: 0;
}
.invoice-details {
  margin-top: 20px;
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
}
.invoice-details div {
  width: 48%;
  margin-bottom: 10px;
}
h2.section-title {
  color: #c58b6a;
  font-size: 1.1rem;
  margin-top: 25px;
  border-bottom: 1px solid #eee;
  padding-bottom: 6px;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
}
th, td {
  border: 1px solid #ddd;
  padding: 10px;
  text-align: left;
  font-size: 0.95rem;
}
th {
  background: #f4ece6;
  color: #333;
}
td img {
  width: 50px;
  border-radius: 6px;
  margin-right: 8px;
  vertical-align: middle;
}
.total {
  text-align: right;
  font-weight: 600;
  padding-top: 10px;
}
.footer {
  text-align: center;
  margin-top: 30px;
  color: #777;
  font-size: 0.9rem;
  border-top: 1px solid #eee;
  padding-top: 10px;
}
.print-btn {
  text-align: center;
  margin-top: 20px;
}
.btn-primary {
  background: linear-gradient(90deg,#e9b89a,#c58b6a);
  border: none;
  color: #111;
  font-weight: 600;
  padding: 10px 18px;
  border-radius: 10px;
  cursor: pointer;
  margin: 0 6px;
  transition: all .2s ease;
}
.btn-primary:hover { transform: translateY(-3px); }
@media print {
  .print-btn { display: none; }
  body { background: #fff; }
  .invoice-box { box-shadow: none; border: none; }
}
</style>
</head>
<body>

<div class="invoice-box">
  <div class="header">
    <img src="https://i.ibb.co/Kxb6CC0Y/logo.png" alt="GiftIQ Logo">
    <div>
      <h1>INVOICE</h1>
      <p>Order #<?= $order['id']; ?><br><?= date('d M Y', strtotime($order['created_at'])); ?></p>
    </div>
  </div>

  <div class="invoice-details">
    <div>
      <h2 class="section-title">Billing To</h2>
      <p><strong><?= htmlspecialchars($order['name']); ?></strong><br>
      <?= htmlspecialchars($order['address']); ?><br>
      <?= htmlspecialchars($order['city']); ?> - <?= htmlspecialchars($order['postal_code']); ?><br>
      <?= htmlspecialchars($order['country']); ?><br>
      📞 <?= htmlspecialchars($order['phone']); ?><br>
      ✉ <?= htmlspecialchars($order['email']); ?></p>
    </div>
    <div>
      <h2 class="section-title">Order Info</h2>
      <p>Status: <strong><?= htmlspecialchars($order['status']); ?></strong><br>
      Payment Method: <strong>Cash on Delivery</strong><br>
      Generated On: <?= date('d M Y h:i A'); ?></p>
    </div>
  </div>

  <h2 class="section-title">Items Ordered</h2>
  <table>
    <thead>
      <tr>
        <th>Product</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $total = 0;
      while($item = $items->fetch_assoc()):
          $subtotal = $item['price'] * $item['quantity'];
          $total += $subtotal;
      ?>
      <tr>
        <td>
          <?php if(!empty($item['image'])): ?>
            <img src="../uploads/<?= htmlspecialchars($item['image']); ?>" alt="">
          <?php endif; ?>
          <?= htmlspecialchars($item['name']); ?>
          <?php if (!empty($item['custom_data'])):
              $details = json_decode($item['custom_data'], true);
              if ($details && is_array($details)) {
                  echo "<ul style='margin:5px 0 0 15px;font-size:0.85rem;color:#555;'>";
                  foreach ($details as $key => $value) {
                      if ($value && $value !== 'None') echo "<li>$key: ".htmlspecialchars($value)."</li>";
                  }
                  echo "</ul>";
              }
          endif; ?>
        </td>
        <td><?= (int)$item['quantity']; ?></td>
        <td>₹<?= number_format($item['price'], 2); ?></td>
        <td>₹<?= number_format($subtotal, 2); ?></td>
      </tr>
      <?php endwhile; ?>
      <tr>
        <td colspan="3" class="total">Total</td>
        <td><strong>₹<?= number_format($order['total'], 2); ?></strong></td>
      </tr>
    </tbody>
  </table>

  <div class="footer">
    <p>Thank you for shopping with <strong>GiftIQ</strong> 🎁<br>
    This is a computer-generated invoice — no signature required.</p>
    <p style="color:#c58b6a;font-weight:600;">Team GiftIQ</p>
  </div>

  <div class="print-btn">
    <button class="btn-primary" onclick="window.print()">🖨 Print Invoice</button>
    <a href="my_order.php" class="btn-primary">⬅ Back</a>
  </div>
</div>

</body>
</html>
