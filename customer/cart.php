<?php
session_start();
include '../config.php';

if (empty($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $pid = intval($_POST['product_id']);
    $qty = max(1, intval($_POST['quantity'] ?? 1));

    $res = $conn->query("SELECT * FROM products WHERE id=$pid");
    if ($res && $res->num_rows > 0) {
        $p = $res->fetch_assoc();
        if (isset($_SESSION['cart'][$pid])) {
            $_SESSION['cart'][$pid]['quantity'] += $qty;
        } else {
            $_SESSION['cart'][$pid] = [
                'product_id' => $pid,
                'name' => $p['name'],
                'price' => $p['price'],
                'quantity' => $qty,
                'image' => $p['image'] ?? '',
                'custom' => false
            ];
        }
        header("Location: cart.php?added=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Cart - GiftIQ</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/png" href="../uploads/favicon.png" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
:root {
  --accent-pink: #f7d4d1;
  --accent-gold: #ffe6b3;
  --accent-text: #d47474;
  --white: #fff;
  --shadow: 0 6px 20px rgba(0,0,0,0.06);
}
body {
  font-family: 'Poppins', sans-serif;
  margin: 0;
  background: linear-gradient(135deg, #fff8f6, #ffeecb);
  min-height: 100vh;
  color: #333;
}

.collection-header {
  text-align: center;
  margin: 2rem 0 1rem;
}
.collection-header h1 {
  background: linear-gradient(90deg, #f4b8b4, #ffd9a0);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-size: 2rem;
  font-weight: 700;
}

.cart-container {
  max-width: 900px;
  margin: 20px auto;
  background: var(--white);
  padding: 24px;
  border-radius: 16px;
  box-shadow: var(--shadow);
}

table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  padding: 12px;
  border-bottom: 1px solid #f0e0e0;
  vertical-align: middle;
  text-align: center;
}
th {
  background: #fff4f2;
  color: var(--accent-text);
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.9rem;
}

td img {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 10px;
  margin-right: 8px;
  vertical-align: middle;
}
td strong {
  display: block;
  font-size: 1rem;
  color: #333;
}

.btn-primary {
  background: linear-gradient(135deg, var(--accent-gold), var(--accent-pink));
  color: #fff;
  font-weight: 700;
  padding: 12px 20px;
  border-radius: 10px;
  text-decoration: none;
  display: inline-block;
  transition: all 0.25s ease;
  border: none;
  cursor: pointer;
}
.btn-primary:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(212,116,116,0.2);
}
.btn-update, .btn-remove {
  border: none;
  border-radius: 6px;
  padding: 8px 12px;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.9rem;
  transition: 0.2s;
}
.btn-update {
  background: #ffd6b3;
  color: #333;
}
.btn-update:hover { background: #ffc599; }
.btn-remove {
  background: #fcd3d3;
  color: #222;
}
.btn-remove:hover { background: #f9bebe; }

.total-box {
  text-align: right;
  font-size: 1.1rem;
  font-weight: 600;
  margin-top: 1rem;
}

.notice.success {
  background: #eafbea;
  color: #1b5e20;
  padding: 10px;
  border-radius: 8px;
  text-align: center;
  margin: 10px auto;
  max-width: 400px;
  font-weight: 500;
}

@media (max-width: 768px) {
  table, thead, tbody, th, td, tr {
    display: block;
  }
  thead { display: none; }
  tr {
    background: #fff;
    margin-bottom: 1rem;
    border-radius: 12px;
    box-shadow: var(--shadow);
    padding: 1rem;
  }
  td {
    border: none;
    text-align: left;
    padding: 8px 0;
  }
  td img {
    width: 160px;
    height: auto;
    border-radius: 12px;
    display: block;
    margin: 0 auto 10px;
  }
  td strong {
    text-align: center;
    margin-bottom: 6px;
  }
  td form {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  input[type='number'] {
    width: 60px;
  }
  .btn-update, .btn-remove {
    flex: 1;
  }
  .total-box {
    text-align: center;
    font-size: 1.2rem;
    margin-top: 16px;
  }
  .btn-primary {
    display: block;
    width: 100%;
    text-align: center;
    padding: 14px;
    font-size: 1rem;
  }
}
</style>
</head>
<body>

<?php include 'header.php'; ?>

<main>
  <div class="collection-header"><h1>🛒 My Cart</h1></div>
  <?php if (isset($_GET['added'])): ?>
    <div class="notice success">✅ Item added to your cart!</div>
  <?php endif; ?>

  <div class="cart-container">
  <?php
  if (empty($_SESSION['cart'])) {
      echo "<p style='text-align:center;font-weight:600;'>Your cart is empty.</p>";
  } else {
      $total = 0;
      echo "<table>";
      echo "<thead><tr><th>Item</th><th>Price</th><th>Qty</th><th>Subtotal</th><th>Action</th></tr></thead><tbody>";
      foreach($_SESSION['cart'] as $key => $it) {
          $qty = intval($it['quantity']);
          $price = floatval($it['price']);
          $name = htmlspecialchars($it['name']);
          $subtotal = $price * $qty;
          $total += $subtotal;
          echo "<tr>";
          echo "<td>";
          if (!empty($it['image']))
              echo "<img src='../uploads/".htmlspecialchars($it['image'])."'>";
          echo "<strong>{$name}</strong>";
          echo "</td>";
          echo "<td>₹".number_format($price,2)."</td>";
          echo "<td>
                  <form method='post' action='cart_update.php'>
                    <input type='hidden' name='key' value='".htmlspecialchars($key)."'>
                    <input type='number' name='quantity' value='{$qty}' min='1' style='padding:5px;border-radius:6px;border:1px solid #ddd;'>
                    <button name='action' value='update' class='btn-update'>Update</button>
                  </form>
                </td>";
          echo "<td>₹".number_format($subtotal,2)."</td>";
          echo "<td>
                  <form method='post' action='cart_update.php'>
                    <input type='hidden' name='key' value='".htmlspecialchars($key)."'>
                    <button name='action' value='remove' class='btn-remove'>Remove</button>
                  </form>
                </td>";
          echo "</tr>";
      }
      echo "</tbody></table>";
      echo "<div class='total-box'>Total: ₹".number_format($total,2)."</div>";

      if (isset($_SESSION['user_id'])) {
          echo "<div style='text-align:center;margin-top:18px;'>
                  <a href='checkout.php' class='btn-primary'>Proceed to Checkout</a>
                </div>";
      } else {
          echo "<div style='text-align:center;margin-top:18px;'>
                  <a href='login.php?redirect=checkout.php' class='btn-primary'>Login to Checkout</a>
                </div>";
      }
  }
  ?>
  </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
