<?php
session_start();
include '../config.php';
include '../admin/mail_template.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
  exit;
}

if (empty($_SESSION['cart']) || empty($_SESSION['shipping'])) {
  header("Location: cart.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = intval($_SESSION['user_id']);
  $shipping = $_SESSION['shipping'];
  $payment_method = $_POST['payment'] ?? 'COD';

  $stmt = $conn->prepare("INSERT INTO orders (user_id, total, status, created_at) VALUES (?, 0, 'Pending', NOW())");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $order_id = $conn->insert_id;
  $stmt->close();

  $total = 0;

  foreach ($_SESSION['cart'] as $key => $item) {
    $qty = intval($item['quantity']);
    $price = floatval($item['price']);
    $subtotal = $price * $qty;
    $total += $subtotal;

    if (!empty($item['custom'])) {
      $custom_json = json_encode($item['details'], JSON_UNESCAPED_UNICODE);
      $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, custom_details) VALUES (?, ?, ?, ?, ?)");
      $pid = $item['product_id'] ?? 0;
      $stmt->bind_param("iiids", $order_id, $pid, $qty, $price, $custom_json);
    } else {
      $pid = intval($item['product_id']);
      $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("iiid", $order_id, $pid, $qty, $price);
    }
    $stmt->execute();
    $stmt->close();
  }

  $stmt = $conn->prepare("UPDATE orders SET total=? WHERE id=?");
  $stmt->bind_param("di", $total, $order_id);
  $stmt->execute();
  $stmt->close();

  $stmt = $conn->prepare("INSERT INTO shipping (order_id, name, phone, address, city, postal_code, country)
                          VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("issssss", 
    $order_id, $shipping['name'], $shipping['phone'],
    $shipping['address'], $shipping['city'],
    $shipping['postal_code'], $shipping['country']
  );
  $stmt->execute();
  $stmt->close();

  $user_result = $conn->query("SELECT name, email FROM users WHERE id=$user_id");
  $user = $user_result->fetch_assoc();
  $user_name = htmlspecialchars($user['name']);
  $user_email = htmlspecialchars($user['email']);

  $items_html = '';
  foreach ($_SESSION['cart'] as $item) {
    $items_html .= "
      <tr>
        <td>" . htmlspecialchars($item['name']) . "</td>
        <td>₹" . number_format($item['price'], 2) . "</td>
        <td>" . $item['quantity'] . "</td>
        <td>₹" . number_format($item['price'] * $item['quantity'], 2) . "</td>
      </tr>
    ";
  }

  $content = '
    <p>Dear <strong>' . $user_name . '</strong>,</p>
    <p>Thank you for placing your order with <strong>GiftIQ</strong>! We’re excited to prepare your perfect gift. Below are your order details:</p>

    <table width="100%" cellspacing="0" cellpadding="8" border="0" style="border-collapse:collapse;font-size:15px;">
      <thead>
        <tr style="background:#fff4eb;color:#a35c2f;text-align:left;">
          <th>Item</th>
          <th>Price</th>
          <th>Qty</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody>
        ' . $items_html . '
        <tr style="font-weight:bold;border-top:2px solid #eee;">
          <td colspan="3" align="right">Total:</td>
          <td>₹' . number_format($total, 2) . '</td>
        </tr>
      </tbody>
    </table>

    <h4>📦 Shipping Address:</h4>
    <p>' . nl2br(htmlspecialchars($shipping['address'])) . '<br>' .
    htmlspecialchars($shipping['city']) . ', ' . htmlspecialchars($shipping['postal_code']) . '<br>' .
    htmlspecialchars($shipping['country']) . '</p>

    <p>Your order status is currently <strong>Pending</strong>. We’ll notify you once it’s processed or shipped.</p>
    <p>Thank you for choosing <strong>GiftIQ</strong> — where every gift tells a story! 🎁</p>
  ';

  $mail = new PHPMailer(true);
  try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'denishsaliya@gmail.com';
    $mail->Password = 'byzr lpev fsbb fvvs';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('denishsaliya@gmail.com', 'GiftIQ');
    $mail->addAddress($user_email, $user_name);
    $mail->isHTML(true);
    $mail->Subject = "🎁 Order Confirmation - GiftIQ Order #$order_id";
    $mail->Body = giftIQMailTemplate("Order Confirmation - GiftIQ Order #$order_id", $content);
    $mail->send();
  } catch (Exception $e) {
    error_log("Mail error: " . $mail->ErrorInfo);
  }

  unset($_SESSION['cart'], $_SESSION['shipping']);

  header("Location: order_success.php?order_id=$order_id");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment - GiftIQ</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/png" href="../uploads/favicon.png" />
  <style>
    :root {
      --accent-pink: #f7d4d1;
      --accent-gold: #ffe6b3;
      --accent-text: #d47474;
      --white: #fff;
      --shadow: 0 8px 24px rgba(0,0,0,0.08);
    }

    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #fff8f6, #ffeecb);
      color: #333;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    main {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
    }

    .payment-card {
      width: 100%;
      max-width: 500px;
      background: var(--white);
      border-radius: 20px;
      box-shadow: var(--shadow);
      padding: 2.5rem;
      animation: fadeInUp 0.8s ease;
    }

    h1 {
      text-align: center;
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--accent-text);
      margin-bottom: 2rem;
    }

    input[type=radio] {
      position: absolute;
      opacity: 0;
      pointer-events: none;
    }

    label {
      display: block;
      padding: 1rem;
      background: #fffdfb;
      border: 2px solid #f3dede;
      border-radius: 12px;
      margin-bottom: 1rem;
      font-weight: 500;
      color: #444;
      cursor: pointer;
      transition: all 0.3s ease;
      text-align: left;
    }

    label:hover {
      background: #fff2ef;
      border-color: var(--accent-pink);
    }

    input[type=radio]:checked + label {
      border-color: var(--accent-text);
      background: linear-gradient(135deg, #ffe6b3, #f7d4d1);
      color: #fff;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }

    .btn-primary {
      width: 100%;
      background: linear-gradient(135deg, var(--accent-gold), var(--accent-pink));
      color: #fff;
      border: none;
      border-radius: 12px;
      padding: 1rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 8px 20px rgba(212, 116, 116, 0.15);
    }

    .btn-primary:hover {
      transform: translateY(-3px);
    }

    @media (max-width: 768px) {
      main {
        padding: 1.5rem;
      }

      .payment-card {
        padding: 1.8rem;
        border-radius: 16px;
      }

      h1 {
        font-size: 1.5rem;
      }

      label {
        font-size: 1rem;
      }

      .btn-primary {
        padding: 0.9rem;
        font-size: 1rem;
      }
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
<?php include 'header.php'; ?>

<main>
  <div class="payment-card fadeInUp">
    <h1>💳 Choose Payment Method</h1>
    <form method="post">
      <input type="radio" name="payment" id="cod" value="COD"  >
      <label for="cod">Cash on Delivery</label>

      <input type="radio" name="payment" id="upi" value="UPI" disabled>
      <label for="upi">UPI / QR Payment</label>

      <input type="radio" name="payment" id="card" value="Card" disabled>
      <label for="card">Credit / Debit Card</label>

      <button type="submit" class="btn-primary">Place Order</button>
    </form>
  </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
